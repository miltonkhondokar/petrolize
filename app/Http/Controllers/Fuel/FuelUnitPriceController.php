<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\PumpFuelPrice;
use App\Models\Pump;
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
        $filters = $request->only(['pump_uuid', 'fuel_type_uuid', 'is_active']);

        $prices = PumpFuelPrice::with(['pump', 'fuelType'])
            ->when($filters['pump_uuid'] ?? null, fn ($q, $pump) => $q->where('pump_uuid', $pump))
            ->when($filters['fuel_type_uuid'] ?? null, fn ($q, $fuelType) => $q->where('fuel_type_uuid', $fuelType))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pumps = Pump::where('is_active', true)->get();
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

        return view('application.pages.fuel.fuel-unit-price.index', compact('prices', 'filters', 'pumps', 'fuelTypes', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new fuel unit price
     */
    public function create()
    {
        $pumps = Pump::where('is_active', true)->get();
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

        return view('application.pages.fuel.fuel-unit-price.create', compact('pumps', 'fuelTypes', 'breadcrumb'));
    }

    /**
     * Store a newly created fuel unit price
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pump_uuid'       => 'required|exists:pumps,uuid',
            'fuel_type_uuid'  => 'required|exists:fuel_types,uuid',
            'price_per_unit'  => 'required|numeric|min:0',
            'is_active'       => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $price = PumpFuelPrice::create($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Fuel Unit Price',
                'type' => 'create',
                'item_id' => $price->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel unit price created successfully.');
            return redirect()->route('fuel-unit-price.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PumpFuelPrice creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to create Fuel Unit Price',
                'type' => 'error',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to create fuel unit price.');
            return back()->withInput();
        }
    }

    /**
     * Display the specified fuel unit price
     */
    public function show($uuid)
    {
        $price = PumpFuelPrice::with(['pump', 'fuelType'])->where('uuid', $uuid)->firstOrFail();

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
    public function edit($uuid)
    {
        $price = PumpFuelPrice::where('uuid', $uuid)->firstOrFail();
        $pumps = Pump::where('is_active', true)->get();
        $fuelTypes = FuelType::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Edit Fuel Unit Price",
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

        return view('application.pages.fuel.fuel-unit-price.edit', compact('price', 'pumps', 'fuelTypes', 'breadcrumb'));
    }

    /**
     * Update the specified fuel unit price
     */
    public function update(Request $request, $uuid)
    {
        $price = PumpFuelPrice::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'pump_uuid'       => 'required|exists:pumps,uuid',
            'fuel_type_uuid'  => 'required|exists:fuel_types,uuid',
            'price_per_unit'  => 'required|numeric|min:0',
            'is_active'       => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $price->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Unit Price',
                'type' => 'update',
                'item_id' => $price->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel unit price updated successfully.');
            return redirect()->route('fuel-unit-price.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PumpFuelPrice update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to update Fuel Unit Price',
                'type' => 'error',
                'item_id' => $price->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to update fuel unit price.');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified fuel unit price
     */
    public function destroy($uuid)
    {
        $price = PumpFuelPrice::where('uuid', $uuid)->firstOrFail();

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

        $price = PumpFuelPrice::where('uuid', $uuid)->firstOrFail();

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
}
