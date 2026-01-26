<?php

namespace App\Http\Controllers\ReferenceData\Fuel;

use App\Constants\UserType;
use App\Http\Controllers\Controller;
use App\Models\FuelStation;
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
        $filters = $request->only([
            'name',
            'location',
            'is_active',
            'region_uuid',
            'governorate_uuid',
            'center_uuid',
            'city_uuid',
            'user_uuid',
        ]);

        $fuelStations = FuelStation::with(['manager', 'region', 'governorate', 'center', 'city'])
            ->when($filters['name'] ?? null, fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->when($filters['location'] ?? null, fn ($q, $location) => $q->where('location', 'like', "%{$location}%"))
            ->when($filters['region_uuid'] ?? null, fn ($q, $v) => $q->where('region_uuid', $v))
            ->when($filters['governorate_uuid'] ?? null, fn ($q, $v) => $q->where('governorate_uuid', $v))
            ->when($filters['center_uuid'] ?? null, fn ($q, $v) => $q->where('center_uuid', $v))
            ->when($filters['city_uuid'] ?? null, fn ($q, $v) => $q->where('city_uuid', $v))
            ->when($filters['user_uuid'] ?? null, fn ($q, $v) => $q->where('user_uuid', $v))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active'] == '1'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $managers = User::active()
            ->whereHas('roles', fn ($q) => $q->where('name', UserType::FUEL_STATION_MANAGER))
            ->get();

        // Geo dropdown data (active only)
        $regions      = DB::table('regions')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name']);
        $governorates = DB::table('governorates')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'region_uuid']);
        $centers      = DB::table('centers')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'governorate_uuid']);
        $cities       = DB::table('cities')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'center_uuid']);

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

        return view('application.pages.reference-data.fuel.stations.index', compact(
            'breadcrumb',
            'fuelStations',
            'managers',
            'filters',
            'regions',
            'governorates',
            'centers',
            'cities',
        ));
    }

    /**
     * Show the create fuel station form.
     */
    public function create()
    {
        $managers = User::active()
            ->whereHas('roles', fn ($q) => $q->where('name', UserType::FUEL_STATION_MANAGER))
            ->get();

        $regions      = DB::table('regions')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name']);
        $governorates = DB::table('governorates')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'region_uuid']);
        $centers      = DB::table('centers')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'governorate_uuid']);
        $cities       = DB::table('cities')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'center_uuid']);

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

        return view('application.pages.reference-data.fuel.stations.create', compact(
            'managers',
            'breadcrumb',
            'regions',
            'governorates',
            'centers',
            'cities',
        ));
    }

    /**
     * Store a new fuel station.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // FIX: your table is fuel_stations, not pumps
            'name'      => 'required|string|max:100|unique:fuel_stations,name',
            'location'  => 'nullable|string|max:255',
            'user_uuid' => 'nullable|exists:users,uuid',
            'is_active' => 'required|boolean',

            // ✅ geo (nullable)
            'region_uuid'      => 'nullable|exists:regions,uuid',
            'governorate_uuid' => 'nullable|exists:governorates,uuid',
            'center_uuid'      => 'nullable|exists:centers,uuid',
            'city_uuid'        => 'nullable|exists:cities,uuid',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, &$fuelStation) {
                $fuelStation = FuelStation::create([
                    'uuid'      => Str::uuid(),
                    'name'      => $validated['name'],
                    'location'  => $validated['location'] ?? null,
                    'user_uuid' => $validated['user_uuid'] ?? null,
                    'is_active' => $validated['is_active'],

                    'region_uuid'      => $validated['region_uuid'] ?? null,
                    'governorate_uuid' => $validated['governorate_uuid'] ?? null,
                    'center_uuid'      => $validated['center_uuid'] ?? null,
                    'city_uuid'        => $validated['city_uuid'] ?? null,
                ]);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Created Fuel Station',
                    'type'       => 'create',
                    'item_id'    => $fuelStation->id,
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
        $fuelStation = FuelStation::with(['manager', 'region', 'governorate', 'center', 'city'])
            ->where('uuid', $uuid)
            ->firstOrFail();

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

        return view('application.pages.reference-data.fuel.stations.show', compact('fuelStation', 'breadcrumb'));
    }

    /**
     * Show edit fuel station form.
     */
    public function edit($uuid)
    {
        $fuelStation = FuelStation::with(['manager', 'region', 'governorate', 'center', 'city'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $managers = User::active()
            ->whereHas('roles', fn ($q) => $q->where('name', UserType::FUEL_STATION_MANAGER))
            ->get();

        $regions      = DB::table('regions')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name']);
        $governorates = DB::table('governorates')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'region_uuid']);
        $centers      = DB::table('centers')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'governorate_uuid']);
        $cities       = DB::table('cities')->where('is_active', 1)->orderBy('name')->get(['uuid', 'name', 'center_uuid']);

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

        return view('application.pages.reference-data.fuel.stations.edit', compact(
            'fuelStation',
            'managers',
            'breadcrumb',
            'regions',
            'governorates',
            'centers',
            'cities'
        ));
    }

    /**
     * Update an existing fuel station.
     */
    public function update(Request $request, $uuid)
    {
        $fuel_station = FuelStation::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:fuel_stations,name,' . $fuel_station->id,
            'location'  => 'nullable|string|max:255',
            'user_uuid' => 'nullable|exists:users,uuid',
            'is_active' => 'required|boolean',

            'region_uuid'      => 'nullable|exists:regions,uuid',
            'governorate_uuid' => 'nullable|exists:governorates,uuid',
            'center_uuid'      => 'nullable|exists:centers,uuid',
            'city_uuid'        => 'nullable|exists:cities,uuid',
        ]);

        try {
            DB::transaction(function () use ($fuel_station, $validated, $request) {
                $fuel_station->update([
                    'name'      => $validated['name'],
                    'location'  => $validated['location'] ?? null,
                    'user_uuid' => $validated['user_uuid'] ?? null,
                    'is_active' => $validated['is_active'],

                    'region_uuid'      => $validated['region_uuid'] ?? null,
                    'governorate_uuid' => $validated['governorate_uuid'] ?? null,
                    'center_uuid'      => $validated['center_uuid'] ?? null,
                    'city_uuid'        => $validated['city_uuid'] ?? null,
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
                'fuel_station_id' => $fuel_station->id,
                'error'   => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to update fuel station.');
            return back()->withInput();
        }
    }

    /**
     * Delete a fuel station safely.
     * (kept your logic as-is)
     */
    public function destroy(FuelStation $fuel_station)
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
                'fuel_station_id' => $fuel_station->id,
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
            $fuelStation = FuelStation::where('uuid', $uuid)->firstOrFail();

            $fuelStation->update([
                'is_active' => $validated['status'] === 'active',
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Station Status',
                'type'       => 'update',
                'item_id'    => $fuelStation->id,
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
