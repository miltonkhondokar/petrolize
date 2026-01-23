<?php

namespace App\Http\Controllers\ReferenceData\Cost;

use App\Http\Controllers\Controller;
use App\Models\CostCategory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class CostCategoryController extends Controller
{
    /**
     * Display a listing of cost categories
     */
    public function index(Request $request)
    {
        $filters = $request->only(['is_active', 'name']);

        $categories = CostCategory::when($filters['name'] ?? null, fn ($q, $name) => $q->where('name', 'ilike', "%$name%"))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $breadcrumb = [
            "page_header" => "Cost Categories",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Categories",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.cost-category.index', compact('categories', 'filters', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new cost category
     */
    public function create()
    {
        $breadcrumb = [
            "page_header" => "Create Cost Category",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Categories",
            "second_item_link" => route('cost-category.index'),
            "second_item_icon" => "fa-list",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.cost-category.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created cost category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cost_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $category = CostCategory::create($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Cost Category',
                'type' => 'create',
                'item_id' => $category->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Cost category created successfully.');
            return redirect()->route('cost-category.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CostCategory creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            Alert::error('Error', 'Failed to create cost category.');
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing a cost category
     */
    public function edit($uuid)
    {
        $category = CostCategory::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Edit Cost Category",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Categories",
            "second_item_link" => route('cost-category.index'),
            "second_item_icon" => "fa-list",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.cost-category.edit', compact('category', 'breadcrumb'));
    }

    /**
     * Update the specified cost category
     */
    public function update(Request $request, $uuid)
    {
        $category = CostCategory::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cost_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $category->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Cost Category',
                'type' => 'update',
                'item_id' => $category->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Cost category updated successfully.');
            return redirect()->route('cost-category.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CostCategory update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update cost category.');
            return back()->withInput();
        }
    }

    /**
     * Show details of a cost category
     */
    public function show($uuid)
    {
        $category = CostCategory::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Cost Category Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cost Categories",
            "second_item_link" => route('cost-category.index'),
            "second_item_icon" => "fa-list",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.cost-category.show', compact('category', 'breadcrumb'));
    }

    /**
     * Delete a cost category
     */
    public function destroy($uuid)
    {
        $category = CostCategory::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($category) {
                $category->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Cost Category',
                    'type' => 'delete',
                    'item_id' => $category->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Cost category deleted successfully.');
            return redirect()->route('cost-category.index');
        } catch (\Exception $e) {
            Log::error('CostCategory delete failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete cost category.');
            return back();
        }
    }

    /**
     * Update status (active/inactive) of a cost category
     */
    public function statusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $category = CostCategory::where('uuid', $uuid)->firstOrFail();

        try {
            $category->update([
                'is_active' => $request->status === 'active',
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Cost Category Status',
                'type' => 'update',
                'item_id' => $category->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Cost category status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('CostCategory status update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update cost category status.');
            return back();
        }
    }
}
