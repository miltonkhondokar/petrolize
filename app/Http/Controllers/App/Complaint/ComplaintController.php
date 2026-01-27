<?php

namespace App\Http\Controllers\App\Complaint;

use App\Http\Controllers\Controller;
use App\Models\FuelStationComplaint;
use App\Models\FuelStation;
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
        $filters = $request->only(['fuel_station_uuid', 'category', 'status', 'is_active']);

        $complaints = FuelStationComplaint::with('fuelStation')
            ->when($filters['fuel_station_uuid'] ?? null, fn ($q, $fuelStation) => $q->where('fuel_station_uuid', $fuelStation))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $fuelStations = FuelStation::where('is_active', true)->get();
        $categories = ['fuel_shortage', 'nozzle_issue', 'power_failure'];

        $breadcrumb = [
            "page_header" => "Fuel Station Complaints",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Complaints",
            "second_item_link" => "#",
            "second_item_icon" => "fa-exclamation-triangle",
        ];

        return view('application.pages.app.complaint.index', compact('complaints', 'filters', 'fuelStations', 'categories', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        $fuelStations = FuelStation::where('is_active', true)->get();
        $categories = ['fuel_shortage', 'nozzle_issue', 'power_failure'];

        $breadcrumb = [
            "page_header" => "Create Fuel Station Complaint",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Complaints",
            "second_item_link" => route('complaints.index'),
            "second_item_icon" => "fa-exclamation-triangle",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.app.complaint.create', compact('fuelStations', 'categories', 'breadcrumb'));
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
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
            $validated['user_uuid'] = Auth::user()->uuid;
            $complaint = FuelStationComplaint::create($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Fuel Station Complaint',
                'type' => 'create',
                'item_id' => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel station complaint created successfully.');
            return redirect()->route('complaints.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PumpComplaint creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            Alert::error('Error', 'Failed to create fuel station complaint.');
            return back()->withInput();
        }
    }

    /**
     * Show a specific complaint
     */
    public function show($uuid)
    {
        $complaint = FuelStationComplaint::with('fuelStation')->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Fuel Station Complaint Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Complaints",
            "second_item_link" => route('complaints.index'),
            "second_item_icon" => "fa-exclamation-triangle",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.app.complaint.show', compact('complaint', 'breadcrumb'));
    }

    /**
     * Show the form for editing a complaint
     */
    public function edit($uuid)
    {
        $complaint = FuelStationComplaint::where('uuid', $uuid)->firstOrFail();
        $fuelStations = FuelStation::where('is_active', true)->get();
        $categories = ['fuel_shortage', 'nozzle_issue', 'power_failure'];

        $breadcrumb = [
            "page_header" => "Edit Fuel Station Complaint",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Fuel Station Complaints",
            "second_item_link" => route('complaints.index'),
            "second_item_icon" => "fa-exclamation-triangle",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.app.complaint.edit', compact('complaint', 'fuelStations', 'categories', 'breadcrumb'));
    }

    /**
     * Update a complaint
     */
    public function update(Request $request, $uuid)
    {
        $complaint = FuelStationComplaint::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'fuel_station_uuid' => 'required|exists:fuel_stations,uuid',
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
            $validated['user_uuid'] = Auth::user()->uuid;
            $complaint->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Station Complaint',
                'type' => 'update',
                'item_id' => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Fuel station complaint updated successfully.');
            return redirect()->route('complaints.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PumpComplaint update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel station complaint.');
            return back()->withInput();
        }
    }

    /**
     * Delete a complaint
     */
    public function destroy($uuid)
    {
        $complaint = FuelStationComplaint::where('uuid', $uuid)->firstOrFail();

        try {
            DB::transaction(function () use ($complaint) {
                $complaint->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Fuel Station Complaint',
                    'type' => 'delete',
                    'item_id' => $complaint->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Fuel station complaint deleted successfully.');
            return redirect()->route('complaints.index');
        } catch (\Exception $e) {
            Log::error('PumpComplaint delete failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete fuel station complaint.');
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

        $complaint = FuelStationComplaint::where('uuid', $uuid)->firstOrFail();

        try {
            $complaint->update([
                'status' => $request->status,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Fuel Station Complaint Status',
                'type' => 'update',
                'item_id' => $complaint->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Fuel station complaint status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('PumpComplaint status update failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update fuel station complaint status.');
            return back();
        }
    }
}
