<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\Controller;
use App\Models\PumpComplaint;
use App\Models\Pump;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $filters = $request->only(['pump_uuid', 'category', 'status', 'is_active']);

        $complaints = PumpComplaint::with('pump')
            ->when($filters['pump_uuid'] ?? null, fn ($q, $pump) => $q->where('pump_uuid', $pump))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pumps = Pump::where('is_active', true)->get();
        $categories = ['fuel_shortage', 'nozzle_issue', 'power_failure'];

        $breadcrumb = [
            "page_header" => "Pump Complaints",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Pump Complaints",
            "second_item_link" => "#",
            "second_item_icon" => "fa-exclamation-triangle",
        ];

        return view('application.pages.complaint-category.index', compact('complaints', 'filters', 'pumps', 'categories', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        $pumps = Pump::where('is_active', true)->get();
        $categories = ['fuel_shortage', 'nozzle_issue', 'power_failure'];

        $breadcrumb = [
            "page_header" => "Create Pump Complaint",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Pump Complaints",
            "second_item_link" => route('complaint-category.index'),
            "second_item_icon" => "fa-exclamation-triangle",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.complaint-category.create', compact('pumps', 'categories', 'breadcrumb'));
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pump_uuid' => 'required|exists:pumps,uuid',
            'category' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,resolved',
            'complaint_date' => 'required|date',
            'resolved_date' => 'nullable|date|after_or_equal:complaint_date',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $complaint = PumpComplaint::create($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Pump Complaint',
                'type' => 'create',
                'item_id' => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Pump complaint created successfully.');
            return redirect()->route('complaint-category.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PumpComplaint creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            Alert::error('Error', 'Failed to create pump complaint.');
            return back()->withInput();
        }
    }

    /**
     * Show a specific complaint
     */
    public function show($uuid)
    {
        $complaint = PumpComplaint::with('pump')->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Pump Complaint Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Pump Complaints",
            "second_item_link" => route('complaint-category.index'),
            "second_item_icon" => "fa-exclamation-triangle",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.complaint-category.show', compact('complaint', 'breadcrumb'));
    }

    /**
     * Show the form for editing a complaint
     */
    public function edit($uuid)
    {
        $complaint = PumpComplaint::where('uuid', $uuid)->firstOrFail();
        $pumps = Pump::where('is_active', true)->get();
        $categories = ['fuel_shortage', 'nozzle_issue', 'power_failure'];

        $breadcrumb = [
            "page_header" => "Edit Pump Complaint",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Pump Complaints",
            "second_item_link" => route('complaint-category.index'),
            "second_item_icon" => "fa-exclamation-triangle",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.complaint-category.edit', compact('complaint', 'pumps', 'categories', 'breadcrumb'));
    }

    /**
     * Update a complaint
     */
    public function update(Request $request, $uuid)
    {
        $complaint = PumpComplaint::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'pump_uuid' => 'required|exists:pumps,uuid',
            'category' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,resolved',
            'complaint_date' => 'required|date',
            'resolved_date' => 'nullable|date|after_or_equal:complaint_date',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $complaint->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Pump Complaint',
                'type' => 'update',
                'item_id' => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Pump complaint updated successfully.');
            return redirect()->route('complaint-category.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PumpComplaint update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update pump complaint.');
            return back()->withInput();
        }
    }

    /**
     * Delete a complaint
     */
    public function destroy($uuid)
    {
        $complaint = PumpComplaint::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($complaint) {
                $complaint->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Pump Complaint',
                    'type' => 'delete',
                    'item_id' => $complaint->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Pump complaint deleted successfully.');
            return redirect()->route('complaint-category.index');
        } catch (\Exception $e) {
            Log::error('PumpComplaint delete failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete pump complaint.');
            return back();
        }
    }

    /**
     * Update complaint status (open, in_progress, resolved) or active/inactive
     */
    public function statusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|string|in:open,in_progress,resolved',
        ]);

        $complaint = PumpComplaint::where('uuid', $uuid)->firstOrFail();

        try {
            $complaint->update([
                'status' => $request->status,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Pump Complaint Status',
                'type' => 'update',
                'item_id' => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Pump complaint status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('PumpComplaint status update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update pump complaint status.');
            return back();
        }
    }
}
