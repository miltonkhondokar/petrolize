<?php

namespace App\Http\Controllers\ReferenceData\Complaint;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class ComplaintCategoryController extends Controller
{
    /**
     * List complaint categories
     */
    public function index()
    {
        $categories = ComplaintCategory::latest()->paginate(20);

        $breadcrumb = [
            "page_header" => "Complaint Categories",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Complaint Categories",
            "second_item_link" => "#",
            "second_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.complaint-category.index',
            compact('categories', 'breadcrumb')
        );
    }

    /**
     * Create form
     */
    public function create()
    {
        $breadcrumb = [
            "page_header" => "Create Complaint Category",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Complaint Categories",
            "second_item_link" => route('complaint-categories.index'),
            "second_item_icon" => "fa-list",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.complaint-category.create',
            compact('breadcrumb')
        );
    }

    /**
     * Store category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:fuel_station_complaint_categories,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {

                $category = ComplaintCategory::create($validated);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Created Complaint Category',
                    'type'       => 'create',
                    'item_id'    => $category->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Complaint category created successfully.');
            return redirect()->route('complaint-categories.index');
        } catch (\Exception $e) {

            Log::error('ComplaintCategory create failed', [
                'error' => $e->getMessage(),
                'data'  => $request->all(),
            ]);

            Alert::error('Error', 'Failed to create complaint category.');
            return back()->withInput();
        }
    }

    /**
     * Show category details
     */
    public function show(string $uuid)
    {
        $category = ComplaintCategory::where('uuid', $uuid)
            ->withCount('complaints')
            ->firstOrFail();

        $breadcrumb = [
            "page_header" => "Complaint Category Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => url('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Complaint Categories",
            "second_item_link" => route('complaint-category.index'),
            "second_item_icon" => "fa-list",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.complaint-category.show',
            compact('category', 'breadcrumb')
        );
    }

    /**
     * Edit form
     */
    public function edit(string $uuid)
    {
        $category = ComplaintCategory::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Edit Complaint Category",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Complaint Categories",
            "second_item_link" => route('complaint-categories.index'),
            "second_item_icon" => "fa-list",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.complaint-category.edit',
            compact('category', 'breadcrumb')
        );
    }

    /**
     * Update category
     */
    public function update(Request $request, string $uuid)
    {
        $category = ComplaintCategory::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($category, $validated, $request) {

                $category->update($validated);

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Updated Complaint Category',
                    'type'       => 'update',
                    'item_id'    => $category->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            Alert::success('Success', 'Complaint category updated successfully.');
            return redirect()->route('complaint-categories.index');
        } catch (\Exception $e) {

            Log::error('ComplaintCategory update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update complaint category.');
            return back()->withInput();
        }
    }

    /**
     * Delete category
     */
    public function destroy(string $uuid)
    {
        $category = ComplaintCategory::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($category) {

                $category->delete();

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Deleted Complaint Category',
                    'type'       => 'delete',
                    'item_id'    => $category->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Complaint category deleted successfully.');
            return back();
        } catch (\Exception $e) {

            Log::error('ComplaintCategory delete failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete complaint category.');
            return back();
        }
    }
}
