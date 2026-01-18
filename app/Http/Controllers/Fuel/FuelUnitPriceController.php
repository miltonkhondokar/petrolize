<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelStationPrice;
use App\Models\FuelStation;
use App\Models\FuelType;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class FuelUnitPriceController extends Controller
{
    /**
     * Display a listing of fuel unit prices
     */
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid', 'fuel_type_uuid', 'is_active']);

        $prices = FuelStationPrice::with(['fuelStation', 'fuelType'])
            ->when($filters['fuel_station_uuid'] ?? null, fn ($q, $fuelStation) => $q->where('fuel_station_uuid', $fuelStation))
            ->when($filters['fuel_type_uuid'] ?? null, fn ($q, $fuelType) => $q->where('fuel_type_uuid', $fuelType))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fuelStations = FuelStation::where('is_active', true)->get();
        $fuelTypes = FuelType::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Fuel Unit Prices",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Unit Prices",
            "second_item_link" => "#",
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.fuel.fuel-unit-price.index', compact('prices', 'filters', 'fuelStations', 'fuelTypes', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new fuel unit price
     */
    public function create()
    {
        $fuelStations = FuelStation::where('is_active', true)->get();
        $fuelTypes = FuelType::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Create Fuel Unit Price",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Unit Prices",
            "second_item_link" => route('fuel-unit-price.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.fuel.fuel-unit-price.create', compact('fuelStations', 'fuelTypes', 'breadcrumb'));
    }

    /**
     * Store a newly created fuel unit price
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'prices' => 'required|array|min:1',
            'prices.*.price_per_unit' => 'required|numeric|min:0',
            'prices.*.is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['prices'] as $fuelTypeUuid => $row) {
                FuelStationPrice::updateOrCreate(
                    [
                        'fuel_station_uuid' => $validated['fuel_station_uuid'],
                        'fuel_type_uuid' => $fuelTypeUuid,
                    ],
                    [
                        'price_per_unit' => $row['price_per_unit'],
                        'is_active' => isset($row['is_active']) ? (bool) $row['is_active'] : false,
                    ]
                );
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created/Updated Fuel Unit Prices (Bulk)',
                'type' => 'create',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel unit prices saved successfully.');
            return redirect()->route('fuel-unit-price.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FuelStationPrice bulk store failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to save Fuel Unit Prices (Bulk)',
                'type' => 'error',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to save fuel unit prices.');
            return back()->withInput();
        }
    }

    /**
     * Display the specified fuel unit price
     */
    public function show($uuid)
    {
        $price = FuelStationPrice::with(['fuelStation', 'fuelType'])->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Unit Price Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Unit Prices",
            "second_item_link" => route('fuel-unit-price.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.fuel.fuel-unit-price.show', compact('price', 'breadcrumb'));
    }

    /**
     * Show the form for editing the specified fuel unit price
     */
    public function edit($stationUuid)
    {
        $fuelStations = FuelStation::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Edit Fuel Unit Prices",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Unit Prices",
            "second_item_link" => route('fuel-unit-price.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view(
            'application.pages.fuel.fuel-unit-price.edit',
            compact('fuelStations', 'breadcrumb', 'stationUuid')
        );
    }

    /**
     * Update the specified fuel unit price
     */
    public function update(Request $request, $stationUuid)
    {
        $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'prices' => 'required|array|min:1',
            'prices.*.price_per_unit' => 'required|numeric|min:0',
            'prices.*.is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $fuelStationUuid = $request->fuel_station_uuid;

            foreach ($request->prices as $fuelTypeUuid => $row) {
                FuelStationPrice::updateOrCreate(
                    [
                        'fuel_station_uuid' => $fuelStationUuid,
                        'fuel_type_uuid' => $fuelTypeUuid,
                    ],
                    [
                        'price_per_unit' => $row['price_per_unit'],
                        'is_active' => isset($row['is_active']) ? (bool) $row['is_active'] : false,
                    ]
                );
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Unit Prices (Bulk)',
                'type' => 'update',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel unit prices updated successfully.');
            return redirect()->route('fuel-unit-price.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FuelStationPrice bulk update failed', [
                'stationUuid' => $stationUuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel unit prices.');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified fuel unit price
     */
    public function destroy($uuid)
    {
        $price = FuelStationPrice::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($price) {
                $price->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Fuel Unit Price',
                    'type' => 'delete',
                    'item_id' => $price->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel unit price deleted successfully.');
            return redirect()->route('fuel-unit-price.index');
        } catch (\Exception $e) {
            Log::error('PumpFuelPrice delete failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete fuel unit price.');
            return back();
        }
    }

    /**
     * Update fuel unit price status (active/inactive)
     */
    public function fuelUnitStatusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $price = FuelStationPrice::where('uuid', $uuid)->firstOrFail();

        try {
            $price->update([
                'is_active' => $request->status === 'active',
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Unit Price Status',
                'type' => 'update',
                'item_id' => $price->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Fuel unit price status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('PumpFuelPrice status update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel unit price status.');
            return back();
        }
    }

    public function stationFuelTypes($stationUuid)
    {
        $station = FuelStation::where('uuid', $stationUuid)->firstOrFail();

        // If you want ONLY station assigned fuel types (pivot), use:
        // $fuelTypes = $station->fuelTypes()->where('fuel_types.is_active', true)->get();

        // If you want ALL active fuel types (your requirement):
        $fuelTypes = FuelType::where('is_active', true)->get();

        $prices = FuelStationPrice::where('fuel_station_uuid', $stationUuid)->get()
            ->keyBy('fuel_type_uuid');

        return response()->json([
            'fuelTypes' => $fuelTypes->map(fn ($t) => [
                'uuid' => $t->uuid,
                'name' => $t->name,
                'code' => $t->code,
                'rating_value' => $t->rating_value,
            ])->values(),
            'prices' => $prices->map(fn ($p) => [
                'fuel_type_uuid' => $p->fuel_type_uuid,
                'price_per_unit' => (string) $p->price_per_unit,
                'is_active' => (bool) $p->is_active,
            ])->values(),
        ]);
    }

    public function stationShow($stationUuid)
    {
        // keep stationUuid even if station is inactive? (your choice)
        $station = FuelStation::where('uuid', $stationUuid)->firstOrFail();

        $fuelStations = FuelStation::where('is_active', true)->get();
        $fuelTypes = FuelType::where('is_active', true)->get(); // not mandatory, but good for consistency

        $breadcrumb = [
            "page_header" => "Fuel Unit Prices Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Unit Prices",
            "second_item_link" => route('fuel-unit-price.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.fuel.fuel-unit-price.show', compact(
            'station',
            'stationUuid',
            'fuelStations',
            'fuelTypes',
            'breadcrumb'
        ));
    }

}
