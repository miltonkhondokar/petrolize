<?php

namespace App\Http\Controllers\App\Cost;

use App\Http\Controllers\Controller;
use App\Models\CostEntry;
use App\Models\CostCategory;
use App\Models\FuelStation;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class CostEntryController extends Controller
{
    /**
     * Display a listing of the cost entries.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['fuel_station_uuid', 'cost_category_uuid', 'is_active', 'expense_date']);

        $costEntries = CostEntry::with(['fuelStation', 'category'])
            ->when($filters['fuel_station_uuid'] ?? null, fn($q, $fs) => $q->where('fuel_station_uuid', $fs))
            ->when($filters['cost_category_uuid'] ?? null, fn($q, $cat) => $q->where('cost_category_uuid', $cat))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn($q) => $q->where('is_active', $filters['is_active']))
            ->when($filters['expense_date'] ?? null, fn($q, $date) => $q->whereDate('expense_date', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fuelStations = FuelStation::where('is_active', true)->get();
        $categories = CostCategory::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Cost Entries",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Entries",
            "second_item_link" => "#",
            "second_item_icon" => "fa-money-bill",
        ];

        return view('application.pages.app.cost.cost-entries.index', compact('costEntries', 'filters', 'fuelStations', 'categories', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new cost entry.
     */
    public function create()
    {
        $fuelStations = FuelStation::where('is_active', true)->get();
        $categories = CostCategory::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Create Cost Entry",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Entries",
            "second_item_link" => route('cost-entries.index'),
            "second_item_icon" => "fa-money-bill",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.app.cost.cost-entries.create', compact('fuelStations', 'categories', 'breadcrumb'));
    }

    /**
     * Store a newly created cost entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'cost_category_uuid' => 'required|exists:cost_categories,uuid',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $costEntry = CostEntry::create($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Cost Entry',
                'type' => 'create',
                'item_id' => $costEntry->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Cost entry created successfully.');
            return redirect()->route('cost-entries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CostEntry creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            Alert::error('Error', 'Failed to create cost entry.');
            return back()->withInput();
        }
    }

    /**
     * Show a specific cost entry.
     */
    public function show($uuid)
    {
        $costEntry = CostEntry::with(['fuelStation', 'category'])->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Cost Entry Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Entries",
            "second_item_link" => route('cost-entries.index'),
            "second_item_icon" => "fa-money-bill",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.app.cost.cost-entries.show', compact('costEntry', 'breadcrumb'));
    }

    /**
     * Show the form for editing a cost entry.
     */
    public function edit($uuid)
    {
        $costEntry = CostEntry::where('uuid', $uuid)->firstOrFail();
        $fuelStations = FuelStation::where('is_active', true)->get();
        $categories = CostCategory::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Edit Cost Entry",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Entries",
            "second_item_link" => route('cost-entries.index'),
            "second_item_icon" => "fa-money-bill",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.app.cost.cost-entries.edit', compact('costEntry', 'fuelStations', 'categories', 'breadcrumb'));
    }

    /**
     * Update a cost entry.
     */
    public function update(Request $request, $uuid)
    {
        $costEntry = CostEntry::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
            'cost_category_uuid' => 'required|exists:cost_categories,uuid',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $costEntry->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Cost Entry',
                'type' => 'update',
                'item_id' => $costEntry->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Cost entry updated successfully.');
            return redirect()->route('cost-entries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CostEntry update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update cost entry.');
            return back()->withInput();
        }
    }

    /**
     * Delete a cost entry.
     */
    public function destroy($uuid)
    {
        $costEntry = CostEntry::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($costEntry) {
                $costEntry->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Cost Entry',
                    'type' => 'delete',
                    'item_id' => $costEntry->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Cost entry deleted successfully.');
            return redirect()->route('cost-entries.index');
        } catch (\Exception $e) {
            Log::error('CostEntry delete failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete cost entry.');
            return back();
        }
    }
}
