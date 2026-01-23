<?php

namespace App\Http\Controllers\ReferenceData\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelUnit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class FuelUnitController extends Controller
{
    /**
     * Display a listing of fuel units
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'is_active']);

        $fuelUnits = FuelUnit::query()
            ->when($filters['name'] ?? null, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $breadcrumb = [
            "page_header" => "Fuel Units",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Units",
            "second_item_link" => "#",
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.fuel.units.index', compact('fuelUnits', 'filters', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new fuel unit
     */
    public function create()
    {
        $breadcrumb = [
            "page_header" => "Create Fuel Unit",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Units",
            "second_item_link" => route('fuel-unit.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.fuel.units.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created fuel unit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:fuel_units,name',
            'abbreviation' => 'nullable|string|max:10',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $fuelUnit = FuelUnit::create($validated);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Created Fuel Unit',
                'type'       => 'create',
                'item_id'    => $fuelUnit->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel unit created successfully.');
            return redirect()->route('fuel-unit.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FuelUnit creation failed', [
                'error' => $e->getMessage(),
                'data'  => $request->all()
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Failed to create Fuel Unit',
                'type'       => 'error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to create fuel unit.');
            return back()->withInput();
        }
    }

    /**
     * Display the specified fuel unit
     */
    public function show($uuid)
    {
        $fuelUnit = FuelUnit::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Unit Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Units",
            "second_item_link" => route('fuel-unit.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.fuel.units.show', compact('fuelUnit', 'breadcrumb'));
    }

    /**
     * Show the form for editing the specified fuel unit
     */
    public function edit($uuid)
    {
        $fuelUnit = FuelUnit::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Edit Fuel Unit",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Units",
            "second_item_link" => route('fuel-unit.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.fuel.units.edit', compact('fuelUnit', 'breadcrumb'));
    }

    /**
     * Update the specified fuel unit
     */
    public function update(Request $request, $uuid)
    {
        $fuelUnit = FuelUnit::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:fuel_units,name,' . $fuelUnit->id,
            'abbreviation' => 'nullable|string|max:10',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $fuelUnit->update($validated);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Unit',
                'type'       => 'update',
                'item_id'    => $fuelUnit->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel unit updated successfully.');
            return redirect()->route('fuel-unit.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FuelUnit update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage()
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Failed to update Fuel Unit',
                'type'       => 'error',
                'item_id'    => $fuelUnit->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to update fuel unit.');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified fuel unit
     */
    public function destroy($uuid)
    {
        $fuelUnit = FuelUnit::where('uuid', $uuid)->firstOrFail();

        try {
            if ($fuelUnit->fuelTypes()->exists() || $fuelUnit->pumpFuelStocks()->exists()) {
                Alert::error('Error', 'Fuel unit is in use and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($fuelUnit) {
                $fuelUnit->delete();

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Deleted Fuel Unit',
                    'type'       => 'delete',
                    'item_id'    => $fuelUnit->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel unit deleted successfully.');
            return redirect()->route('fuel-unit.index');

        } catch (\Exception $e) {
            Log::error('FuelUnit delete failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to delete fuel unit.');
            return back();
        }
    }

    /**
     * Update fuel unit status (active/inactive)
     */
    public function fuelUnitStatusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $fuelUnit = FuelUnit::where('uuid', $uuid)->firstOrFail();

        try {
            $fuelUnit->update([
                'is_active' => $request->status === 'active',
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Unit Status',
                'type'       => 'update',
                'item_id'    => $fuelUnit->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Fuel unit status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('FuelUnit status update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to update fuel unit status.');
            return back();
        }
    }
}
