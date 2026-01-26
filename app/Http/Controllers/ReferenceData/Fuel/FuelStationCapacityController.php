<?php

namespace App\Http\Controllers\ReferenceData\Fuel;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FuelStation;
use App\Models\FuelStationCapacityLog;
use App\Models\FuelType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FuelStationCapacityController extends Controller
{
    /**
     * List capacity logs (history) with filters.
     * View: application.pages.reference-data.fuel.fuel-station-capacity.index
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'fuel_station_uuid',
            'fuel_type_uuid',
            'is_active',
            'from',
            'to',
        ]);

        $rows = FuelStationCapacityLog::with(['station', 'fuelType'])
            ->when($filters['fuel_station_uuid'] ?? null, fn ($q, $v) => $q->where('fuel_station_uuid', $v))
            ->when($filters['fuel_type_uuid'] ?? null, fn ($q, $v) => $q->where('fuel_type_uuid', $v))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active'] == '1'))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->where('effective_from', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->where('effective_from', '<=', $v))
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $stations = FuelStation::orderBy('name')->get(['uuid', 'name', 'location']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid', 'name', 'code']);

        $breadcrumb = [
            "page_header" => "Fuel Station Capacity",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Capacity",
            "second_item_link" => route('fuel-capacity.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.fuel.fuel-station-capacity.index', compact(
            'rows',
            'stations',
            'fuelTypes',
            'filters',
            'breadcrumb'
        ));
    }

    /**
     * Show create form.
     * View: application.pages.reference-data.fuel.fuel-station-capacity.create
     */
    public function create()
    {
        $fuelStations = FuelStation::orderBy('name')->get(['uuid', 'name', 'location']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid', 'name', 'code']);

        $breadcrumb = [
            "page_header" => "Fuel Station Capacity",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Capacity",
            "second_item_link" => route('fuel-capacity.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.fuel.fuel-station-capacity.create', compact(
            'fuelStations',
            'fuelTypes',
            'breadcrumb'
        ));
    }

    /**
     * Store new capacity snapshots (batch creation).
     * Handles multiple fuel types for a station at once.
     * Enforces unique constraint (station, fuel, effective_from).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_station_uuid' => 'required|uuid|exists:fuel_stations,uuid',
            'effective_from'    => 'required|date',
            'note'              => 'nullable|string|max:2000',
            'capacities'        => 'required|array|min:1',
            'capacities.*.capacity_liters' => 'required|numeric|min:0',
            'capacities.*.is_active' => 'nullable|in:0,1',
        ]);

        try {
            $createdCount = 0;
            $skippedCount = 0;
            $errors = [];

            DB::transaction(function () use ($validated, $request, &$createdCount, &$skippedCount, &$errors) {
                foreach ($validated['capacities'] as $fuelTypeUuid => $data) {
                    // Skip if capacity is empty or zero
                    if (empty($data['capacity_liters']) || $data['capacity_liters'] <= 0) {
                        $skippedCount++;
                        continue;
                    }

                    try {
                        // Check if record already exists
                        $exists = FuelStationCapacityLog::where('fuel_station_uuid', $validated['fuel_station_uuid'])
                            ->where('fuel_type_uuid', $fuelTypeUuid)
                            ->where('effective_from', $validated['effective_from'])
                            ->exists();

                        if ($exists) {
                            $skippedCount++;
                            $fuelType = FuelType::where('uuid', $fuelTypeUuid)->first();
                            $errors[] = "Skipped: " . ($fuelType->name ?? 'Unknown') . " (already exists)";
                            continue;
                        }

                        FuelStationCapacityLog::create([
                            'uuid'              => (string) Str::uuid(),
                            'fuel_station_uuid' => $validated['fuel_station_uuid'],
                            'fuel_type_uuid'    => $fuelTypeUuid,
                            'capacity_liters'   => $data['capacity_liters'],
                            'effective_from'    => $validated['effective_from'],
                            'note'              => $validated['note'] ?? null,
                            'is_active'         => isset($data['is_active']) ? ($data['is_active'] == '1') : true,
                        ]);

                        $createdCount++;
                    } catch (\Exception $e) {
                        $skippedCount++;
                        $fuelType = FuelType::where('uuid', $fuelTypeUuid)->first();
                        $errors[] = "Error: " . ($fuelType->name ?? 'Unknown') . " - " . $e->getMessage();
                        Log::error('Individual fuel capacity create failed', [
                            'fuel_type_uuid' => $fuelTypeUuid,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Create audit log for the batch operation
                if ($createdCount > 0) {
                    AuditLog::create([
                        'user_id'    => Auth::id(),
                        'action'     => "Created {$createdCount} Fuel Station Capacity Snapshot(s)",
                        'type'       => 'create',
                        'item_id'    => null,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            });

            // Build success/warning message
            if ($createdCount > 0 && $skippedCount === 0) {
                Alert::success('Success', "Successfully created {$createdCount} capacity record(s).");
            } elseif ($createdCount > 0 && $skippedCount > 0) {
                $message = "Created {$createdCount} record(s), skipped {$skippedCount}.";
                if (!empty($errors)) {
                    $message .= " Details: " . implode('; ', array_slice($errors, 0, 3));
                }
                Alert::warning('Partial Success', $message);
            } elseif ($createdCount === 0 && $skippedCount > 0) {
                $message = "No records created. All {$skippedCount} record(s) were skipped.";
                if (!empty($errors)) {
                    $message .= " Details: " . implode('; ', array_slice($errors, 0, 3));
                }
                Alert::warning('Warning', $message);
            } else {
                Alert::error('Error', 'No valid capacity data provided.');
            }

            return redirect()->route('fuel-capacity.index');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for duplicate key violation (cross-database compatible)
            if (str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'UNIQUE constraint') ||
                (int)($e->errorInfo[1] ?? 0) === 1062) {
                Alert::error('Duplicate', 'A capacity record already exists for this Station + Fuel Type + Effective Time.');
                return back()->withInput();
            }

            Log::error('Fuel capacity create failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            Alert::error('Error', 'Database error occurred while saving fuel capacity.');
            return back()->withInput();
        } catch (\Exception $e) {
            Log::error('Fuel capacity create failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            Alert::error('Error', 'Failed to save fuel capacity. Please try again.');
            return back()->withInput();
        }
    }

    /**
     * Show details.
     * View: application.pages.reference-data.fuel.fuel-station-capacity.show
     */
    public function show(string $uuid)
    {
        $row = FuelStationCapacityLog::with(['station', 'fuelType'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Station Capacity",
            "first_item_name" => "Fuel Station Capacity",
            "first_item_link" => route('fuel-capacity.index'),
            "first_item_icon" => "fa-gas-pump",
            "second_item_name" => "Details",
            "second_item_link" => "#",
            "second_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.fuel.fuel-station-capacity.show', compact('row', 'breadcrumb'));
    }

    /**
     * Show edit form.
     * View: application.pages.reference-data.fuel.fuel-station-capacity.edit
     */
    public function edit(string $uuid)
    {
        $row = FuelStationCapacityLog::with(['station', 'fuelType'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $stations = FuelStation::orderBy('name')->get(['uuid', 'name', 'location']);
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get(['uuid', 'name', 'code']);

        $breadcrumb = [
            "page_header" => "Fuel Station Capacity",
            "first_item_name" => "Fuel Station Capacity",
            "first_item_link" => route('fuel-capacity.index'),
            "first_item_icon" => "fa-gas-pump",
            "second_item_name" => "Edit",
            "second_item_link" => "#",
            "second_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.fuel.fuel-station-capacity.edit', compact(
            'row',
            'stations',
            'fuelTypes',
            'breadcrumb'
        ));
    }

    /**
     * Update an existing record.
     * NOTE: Station and fuel type cannot be changed (locked fields).
     * Changing effective_from may trigger unique constraint.
     */
    public function update(Request $request, string $uuid)
    {
        $row = FuelStationCapacityLog::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'capacity_liters' => 'required|numeric|min:0',
            'effective_from'  => 'required|date',
            'note'            => 'nullable|string|max:2000',
            'is_active'       => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($row, $validated, $request) {
                $row->update([
                    'capacity_liters' => $validated['capacity_liters'],
                    'effective_from'  => $validated['effective_from'],
                    'note'            => $validated['note'] ?? null,
                    'is_active'       => $validated['is_active'],
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Updated Fuel Station Capacity Snapshot',
                    'type'       => 'update',
                    'item_id'    => $row->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel capacity updated successfully.');
            return redirect()->route('fuel-capacity.index');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for duplicate key violation (cross-database compatible)
            if (str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'UNIQUE constraint') ||
                (int)($e->errorInfo[1] ?? 0) === 1062) {
                Alert::error('Duplicate', 'Another capacity record already exists for this Station + Fuel Type + Effective Time.');
                return back()->withInput();
            }

            Log::error('Fuel capacity update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
            Alert::error('Error', 'Database error occurred while updating fuel capacity.');
            return back()->withInput();
        } catch (\Exception $e) {
            Log::error('Fuel capacity update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
            Alert::error('Error', 'Failed to update fuel capacity. Please try again.');
            return back()->withInput();
        }
    }

    /**
     * Resource destroy (soft delete logic).
     * Keeps the record but marks is_active = false.
     */
    public function destroy(string $uuid)
    {
        try {
            $row = FuelStationCapacityLog::where('uuid', $uuid)->firstOrFail();

            DB::transaction(function () use ($row) {
                $row->update(['is_active' => false]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Deactivated Fuel Station Capacity Snapshot',
                    'type'       => 'delete',
                    'item_id'    => $row->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Capacity record deactivated successfully.');
            return back();
        } catch (ModelNotFoundException $e) {
            Log::error('Fuel capacity not found for deactivation', [
                'uuid' => $uuid,
                'error' => $e->getMessage()
            ]);
            Alert::error('Error', 'Capacity record not found.');
            return back();
        } catch (\Exception $e) {
            Log::error('Fuel capacity deactivate failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage()
            ]);
            Alert::error('Error', 'Failed to deactivate capacity record. Please try again.');
            return back();
        }
    }

    /**
     * Custom PATCH route for status toggle.
     * Route: PATCH /fuel-capacity/{uuid}/status
     * Expects: is_active = 0 or 1
     */
    public function fuelCapacityStatusUpdate(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'is_active' => 'required|in:0,1',
        ]);

        try {
            $row = FuelStationCapacityLog::where('uuid', $uuid)->firstOrFail();

            DB::transaction(function () use ($row, $validated, $request) {
                $row->update([
                    'is_active' => $validated['is_active'] == '1',
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Updated Fuel Capacity Status to ' . ($validated['is_active'] == '1' ? 'Active' : 'Inactive'),
                    'type'       => 'update',
                    'item_id'    => $row->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            $statusText = $validated['is_active'] == '1' ? 'activated' : 'deactivated';
            Alert::success('Success', "Capacity status {$statusText} successfully.");
            return back();
        } catch (ModelNotFoundException $e) {
            Log::error('Fuel capacity not found for status update', [
                'uuid' => $uuid,
                'error' => $e->getMessage()
            ]);
            Alert::error('Error', 'Capacity record not found.');
            return back();
        } catch (\Exception $e) {
            Log::error('Fuel capacity status update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
            Alert::error('Error', 'Failed to update capacity status. Please try again.');
            return back();
        }
    }

    /**
     * AJAX endpoint: Get fuel types and existing capacities for a station.
     * Used by the create form to dynamically load fuel types.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stationFuelTypes(Request $request)
    {
        $validated = $request->validate([
            'station_uuid' => 'required|uuid|exists:fuel_stations,uuid',
            'effective_from' => 'required|date',
        ]);

        try {
            $station = FuelStation::where('uuid', $validated['station_uuid'])
                ->firstOrFail(['uuid', 'name', 'location']);

            // Get all active fuel types
            $fuelTypes = FuelType::where('is_active', true)
                ->orderBy('name')
                ->get(['uuid', 'name', 'code', 'rating_value']);

            // Get existing capacities for this station and date
            $capacities = FuelStationCapacityLog::where('fuel_station_uuid', $validated['station_uuid'])
                ->where('effective_from', $validated['effective_from'])
                ->get(['uuid', 'fuel_type_uuid', 'capacity_liters', 'is_active'])
                ->keyBy('fuel_type_uuid');

            return response()->json([
                'success' => true,
                'station' => $station,
                'fuelTypes' => $fuelTypes,
                'capacities' => $capacities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fuel station not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to load station fuel types', [
                'station_uuid' => $validated['station_uuid'] ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load fuel types. Please try again.',
            ], 500);
        }
    }
}
