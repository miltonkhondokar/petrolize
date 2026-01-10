<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\Pump;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class FuelStationController extends Controller
{
    /**
     * List all fuel stations with filters.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'location', 'is_active']);

        $pumps = Pump::with('manager')
            ->when($filters['name'] ?? null, fn ($query, $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->where('location', 'like', "%{$location}%"))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($query) => $query->where('is_active', $filters['is_active'] == '1'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $managers = User::active()
            ->whereHas('roles', fn ($query) => $query->where('name', 'manager'))
            ->get();

        $breadcrumb = [
            "page_header" => "Fuel Stations",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Stations",
            "second_item_link" => route('fuel-station.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.fuel.stations.index', compact('breadcrumb', 'pumps', 'managers', 'filters'));
    }

    /**
     * Show the create fuel station form.
     */
    public function create()
    {
        $managers = User::active()
            ->whereHas('roles', fn ($query) => $query->where('name', 'manager'))
            ->get();

        $breadcrumb = [
            "page_header" => "Create Fuel Station",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Stations",
            "second_item_link" => route('fuel-station.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.fuel.stations.create', compact('managers', 'breadcrumb'));
    }

    /**
     * Store a new fuel station.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:pumps,name',
            'location'  => 'nullable|string|max:255',
            'user_uuid' => 'nullable|exists:users,uuid',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, &$pump) {
                $pump = Pump::create([
                    'uuid'      => Str::uuid(),
                    'name'      => $validated['name'],
                    'location'  => $validated['location'],
                    'user_uuid' => $validated['user_uuid'],
                    'is_active' => $validated['is_active'],
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Created Fuel Station',
                    'type'       => 'create',
                    'item_id'    => $pump->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel station created successfully.');
            return redirect()->route('fuel-station.index');

        } catch (\Exception $e) {
            Log::error('FuelStation create failed', [
                'error' => $e->getMessage(),
                'data'  => $request->all()
            ]);

            Alert::error('Error', 'Failed to create fuel station.');
            return back()->withInput();
        }
    }

    /**
     * Show fuel station details.
     */
    public function show($uuid)
    {
        $pump = Pump::with('manager')->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Station Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Stations",
            "second_item_link" => route('fuel-station.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.fuel.stations.show', compact('pump', 'breadcrumb'));
    }

    /**
     * Show edit fuel station form.
     */
    public function edit($uuid)
    {
        $pump = Pump::with('manager')->where('uuid', $uuid)->firstOrFail();

        $managers = User::active()
            ->whereHas('roles', fn ($query) => $query->where('name', 'manager'))
            ->get();

        $breadcrumb = [
            "page_header" => "Edit Fuel Station",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Stations",
            "second_item_link" => route('fuel-station.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.fuel.stations.edit', compact('pump', 'managers', 'breadcrumb'));
    }

    /**
     * Update an existing fuel station.
     */
    public function update(Request $request, $uuid)
    {

        $fuel_station = Pump::where('uuid', $uuid)->firstOrFail();


        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:pumps,name,' . $fuel_station->id,
            'location'  => 'nullable|string|max:255',
            'user_uuid' => 'nullable|exists:users,uuid',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($fuel_station, $validated, $request) {
                $fuel_station->update([
                    'name'      => $validated['name'],
                    'location'  => $validated['location'],
                    'user_uuid' => $validated['user_uuid'],
                    'is_active' => $validated['is_active'],
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Updated Fuel Station',
                    'type'       => 'update',
                    'item_id'    => $fuel_station->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel station updated successfully.');
            return redirect()->route('fuel-station.index');

        } catch (\Exception $e) {
            Log::error('FuelStation update failed', [
                'pump_id' => $fuel_station->id,
                'error'   => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to update fuel station.');
            return back()->withInput();
        }
    }

    /**
     * Delete a fuel station safely.
     */
    public function destroy(Pump $fuel_station)
    {
        try {
            if (
                $fuel_station->fuelPrices()->exists() ||
                $fuel_station->fuelStocks()->exists() ||
                $fuel_station->fuelReadings()->exists() ||
                $fuel_station->costs()->exists() ||
                $fuel_station->complaints()->exists()
            ) {
                Alert::error('Error', 'Fuel station is in use and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($fuel_station) {
                $fuel_station->delete();

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Deleted Fuel Station',
                    'type'       => 'delete',
                    'item_id'    => $fuel_station->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel station deleted successfully.');
            return redirect()->route('fuel-station.index');

        } catch (\Exception $e) {
            Log::error('FuelStation delete failed', [
                'pump_id' => $fuel_station->id,
                'error'   => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to delete fuel station.');
            return back();
        }
    }

    /**
     * Update fuel station status (active/inactive).
     */
    public function fuelStationStatusUpdate(Request $request, $uuid)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $pump = Pump::where('uuid', $uuid)->firstOrFail();

            $pump->update([
                'is_active' => $validated['status'] === 'active',
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Station Status',
                'type'       => 'update',
                'item_id'    => $pump->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Fuel station status updated successfully.');
            return back();

        } catch (\Exception $e) {
            Log::error('Fuel station status update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel station status.');
            return back();
        }
    }
}
