<?php

namespace App\Http\Controllers\ReferenceData\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelType;
use App\Models\FuelUnit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class FuelCategoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'code', 'rating_value', 'is_active']);

        $fuelTypes = FuelType::query()
            ->with('defaultUnit')
            ->when($filters['name'] ?? null, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($filters['code'] ?? null, function ($query, $code) {
                $query->where('code', 'like', "%{$code}%");
            })
            ->when($filters['rating_value'] ?? null, function ($query, $rating_value) {
                $query->where('rating_value', 'like', "%{$rating_value}%");
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fuelUnits = FuelUnit::where('is_active', true)->get();


        $breadcrumb = [
            "page_header" => "Fuel Types",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Types",
            "second_item_link" => route('fuel.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];


        return view('application.pages.reference-data.fuel.types.index', compact('breadcrumb', 'fuelTypes', 'fuelUnits', 'filters'));
    }

    public function create()
    {
        $fuelUnits = FuelUnit::where('is_active', true)->get();


        $breadcrumb = [
            "page_header" => "Create Fuel Type",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Types",
            "second_item_link" => route('fuel.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];


        return view('application.pages.reference-data.fuel.types.create', compact('fuelUnits', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:50|unique:fuel_types,name',
            'code'           => 'required|string|max:10|unique:fuel_types,code',
            'rating_value'   => 'required|integer|min:1|unique:fuel_types,rating_value',
            'fuel_unit_uuid' => 'required|exists:fuel_units,uuid',
            'is_active'      => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $fuelType = FuelType::create([
                'uuid'           => Str::uuid(),
                'name'           => $validated['name'],
                'code'           => strtoupper($validated['code']),
                'rating_value'   => $validated['rating_value'],
                'fuel_unit_uuid' => $validated['fuel_unit_uuid'],
                'is_active'      => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Created Fuel Type',
                'type'       => 'create',
                'item_id'    => $fuelType->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Fuel type created successfully.');
            return redirect()->route('fuel.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FuelType create failed', [
                'error' => $e->getMessage(),
                'data'  => $request->all()
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Failed to create Fuel Type',
                'type'       => 'error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to create fuel type.');
            return back()->withInput();
        }
    }

    public function show($uuid)
    {
        $fuel = FuelType::with('defaultUnit')->where('uuid', $uuid)->first();

        $breadcrumb = [
            "page_header" => "Fuel Type Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Types",
            "second_item_link" => route('fuel.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];


        return view('application.pages.reference-data.fuel.types.show', compact('fuel', 'breadcrumb'));
    }

    public function edit($uuid)
    {

        $fuel = FuelType::with('defaultUnit')->where('uuid', $uuid)->first();

        $fuelUnits = FuelUnit::where('is_active', true)->get();


        $breadcrumb = [
            "page_header" => "Edit Fuel Type",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Types",
            "second_item_link" => route('fuel.index'),
            "second_item_icon" => "fa-gas-pump",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];


        return view('application.pages.reference-data.fuel.types.edit', compact('fuel', 'fuelUnits', 'breadcrumb'));
    }

    public function update(Request $request, FuelType $fuel)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:50|unique:fuel_types,name,' . $fuel->id,
            'code'           => 'required|string|max:10|unique:fuel_types,code,' . $fuel->id,
            'rating_value'   => 'required|integer|min:1|unique:fuel_types,rating_value,' . $fuel->id,
            'fuel_unit_uuid' => 'required|exists:fuel_units,uuid',
            'is_active'      => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $fuel->update([
                'name'           => $validated['name'],
                'code'           => strtoupper($validated['code']),
                'rating_value'   => $validated['rating_value'],
                'fuel_unit_uuid' => $validated['fuel_unit_uuid'],
                'is_active'      => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Type',
                'type'       => 'update',
                'item_id'    => $fuel->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Fuel type updated successfully.');
            return redirect()->route('fuel.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FuelType update failed', [
                'fuel_id' => $fuel->id,
                'error'   => $e->getMessage()
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Failed to update Fuel Type',
                'type'       => 'error',
                'item_id'    => $fuel->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to update fuel type.');
            return back()->withInput();
        }
    }

    public function destroy(FuelType $fuel)
    {
        try {
            if (
                $fuel->pumpFuelPrices()->exists() ||
                $fuel->pumpFuelStocks()->exists() ||
                $fuel->pumpFuelReadings()->exists()
            ) {
                Alert::error('Error', 'Fuel type is in use and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($fuel) {
                $fuel->delete();

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Deleted Fuel Type',
                    'type'       => 'delete',
                    'item_id'    => $fuel->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel type deleted successfully.');
            return redirect()->route('fuel.index');

        } catch (\Exception $e) {
            Log::error('FuelType delete failed', [
                'fuel_id' => $fuel->id,
                'error'   => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to delete fuel type.');
            return back();
        }
    }

    public function fuelStatusUpdate(Request $request, $uuid)
    {
        // dd($request->all());
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $fuel = FuelType::where('uuid', $uuid)->first();

            if (!$fuel) {
                Alert::error('Error', 'Fuel type not found.');
                return back();
            }

            $fuel->update([
                'is_active' => $request->status === 'active',
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Fuel Type Status',
                'type'       => 'update',
                'item_id'    => $fuel->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Fuel type status updated successfully.');
            return back();

        } catch (\Exception $e) {
            Log::error('Fuel status update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel type status.');
            return back();
        }
    }

}
