<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelStationFuelType;
use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class FuelStationFuelTypeController extends Controller
{
    /**
     * Display a listing of station fuel assignments
     */
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid', 'fuel_type_uuid', 'is_active']);

        $assignments = FuelStationFuelType::with(['fuelStation', 'fuelType'])
            ->when($filters['fuel_station_uuid'] ?? null, fn($q, $v) => $q->where('fuel_station_uuid', $v))
            ->when($filters['fuel_type_uuid'] ?? null, fn($q, $v) => $q->where('fuel_type_uuid', $v))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fuelStations = FuelStation::where('is_active', true)->get();
        $fuelTypes = FuelType::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Fuel Station Fuel Types",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Fuel Types",
            "second_item_link" => "#",
            "second_item_icon" => "fa-gas-pump",
        ];

        return view('application.pages.fuel.station-fuel-type.index', compact('assignments', 'filters', 'fuelStations', 'fuelTypes', 'breadcrumb'));
    }

    /**
     * Show assignment form
     */
    public function create()
    {
        $fuelStations = FuelStation::where('is_active', true)->get();
        $fuelTypes = FuelType::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Assign Fuel Types to Station",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Fuel Types",
            "second_item_link" => route('fuel-station-fuel-type.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Assign",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view(
            'application.pages.fuel.station-fuel-type.create',
            compact('fuelStations', 'fuelTypes', 'breadcrumb')
        );
    }

    /**
     * Store / Update assignments (Bulk)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'fuel_types' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['fuel_types'] as $fuelTypeUuid => $row) {
                FuelStationFuelType::updateOrCreate(
                    [
                        'fuel_station_uuid' => $validated['fuel_station_uuid'],
                        'fuel_type_uuid' => $fuelTypeUuid,
                    ],
                    [
                        'is_active' => isset($row['is_active']) ? true : false,
                    ]
                );
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Assigned Fuel Types to Fuel Station',
                'type' => 'create',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel types assigned successfully.');
            return redirect()->route('fuel-station-fuel-type.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FuelStationFuelType store failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            Alert::error('Error', 'Failed to assign fuel types.');
            return back()->withInput();
        }
    }

    public function show($uuid)
    {
        $assignment = FuelStationFuelType::with(['fuelStation', 'fuelType'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Station Fuel Type Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Fuel Types",
            "second_item_link" => route('fuel-station-fuel-type.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.fuel.station-fuel-type.show', compact('assignment', 'breadcrumb'));
    }

    /**
     * Edit assignment for a station
     */
    public function edit($stationUuid)
    {
        $station = FuelStation::where('uuid', $stationUuid)->firstOrFail();
        $fuelTypes = FuelType::where('is_active', true)->get();

        $assignedFuelTypes = FuelStationFuelType::where('fuel_station_uuid', $stationUuid)
            ->get()
            ->keyBy('fuel_type_uuid');

        $breadcrumb = [
            "page_header" => "Edit Station Fuel Types",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Fuel Types",
            "second_item_link" => route('fuel-station-fuel-type.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view(
            'application.pages.fuel.station-fuel-type.edit',
            compact('station', 'fuelTypes', 'assignedFuelTypes', 'breadcrumb')
        );
    }

    /**
     * Update assignments (Bulk)
     */
    public function update(Request $request, $stationUuid)
    {
        $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'fuel_types' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->fuel_types as $fuelTypeUuid => $row) {
                FuelStationFuelType::updateOrCreate(
                    [
                        'fuel_station_uuid' => $request->fuel_station_uuid,
                        'fuel_type_uuid' => $fuelTypeUuid,
                    ],
                    [
                        'is_active' => isset($row['is_active']) ? true : false,
                    ]
                );
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Station Fuel Types',
                'type' => 'update',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel station fuel types updated successfully.');
            return redirect()->route('fuel-station-fuel-type.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FuelStationFuelType update failed', [
                'stationUuid' => $stationUuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel station fuel types.');
            return back()->withInput();
        }
    }

    /**
     * Toggle active/inactive status
     */
    public function statusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $assignment = FuelStationFuelType::where('uuid', $uuid)->firstOrFail();

        try {
            $assignment->update([
                'is_active' => $request->status === 'active',
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Station Fuel Type Status',
                'type' => 'update',
                'item_id' => $assignment->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('FuelStationFuelType status update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update status.');
            return back();
        }
    }

    /**
     * Delete assignment
     */
    public function destroy($uuid)
    {
        $assignment = FuelStationFuelType::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($assignment) {
                $assignment->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Fuel Station Fuel Type',
                    'type' => 'delete',
                    'item_id' => $assignment->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel station fuel type deleted successfully.');
            return redirect()->route('fuel-station-fuel-type.index');
        } catch (\Exception $e) {
            Log::error('FuelStationFuelType delete failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete fuel station fuel type.');
            return back();
        }
    }
}
