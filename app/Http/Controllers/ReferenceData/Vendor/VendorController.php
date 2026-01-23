<?php

namespace App\Http\Controllers\ReferenceData\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'email', 'phone', 'is_active']);

        $vendors = Vendor::query()
            ->when($filters['name'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($filters['email'] ?? null, fn($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->when($filters['phone'] ?? null, fn($q, $v) => $q->where('phone', 'like', "%{$v}%"))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $breadcrumb = [
            "page_header" => "Vendors",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Vendors",
            "second_item_link" => route('vendors.index'),
            "second_item_icon" => "fa-truck",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.vendors.index', compact('breadcrumb', 'vendors', 'filters'));
    }

    public function create()
    {
        $breadcrumb = [
            "page_header" => "Create Vendor",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Vendors",
            "second_item_link" => route('vendors.index'),
            "second_item_icon" => "fa-truck",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.vendors.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:vendors,name',
            'email'      => 'nullable|email|unique:vendors,email',
            'phone'      => 'required|string|max:20|unique:vendors,phone',
            'address'    => 'nullable|string|max:255',
            'is_active'  => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $vendor = Vendor::create([
                'uuid'       => Str::uuid(),
                'name'       => $validated['name'],
                'email'      => $validated['email'] ?? null,
                'phone'      => $validated['phone'],
                'address'    => $validated['address'] ?? null,
                'is_active'  => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Created Vendor',
                'type'       => 'create',
                'item_id'    => $vendor->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Vendor created successfully.');
            return redirect()->route('vendors.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Vendor create failed', [
                'error' => $e->getMessage(),
                'data'  => $request->all()
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Failed to create Vendor',
                'type'       => 'error',
                'item_id'    => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to create vendor.');
            return back()->withInput();
        }
    }

    public function show($uuid)
    {
        $vendor = Vendor::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Vendor Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Vendors",
            "second_item_link" => route('vendors.index'),
            "second_item_icon" => "fa-truck",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.vendors.show', compact('vendor', 'breadcrumb'));
    }

    public function edit($uuid)
    {
        $vendor = Vendor::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Edit Vendor",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Vendors",
            "second_item_link" => route('vendors.index'),
            "second_item_icon" => "fa-truck",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.vendors.edit', compact('vendor', 'breadcrumb'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:vendors,name,' . $vendor->id,
            'email'      => 'nullable|email|unique:vendors,email,' . $vendor->id,
            'phone'      => 'required|string|max:20|unique:vendors,phone,' . $vendor->id,
            'address'    => 'nullable|string|max:255',
            'is_active'  => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $vendor->update($validated);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Vendor',
                'type'       => 'update',
                'item_id'    => $vendor->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Vendor updated successfully.');
            return redirect()->route('vendors.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Vendor update failed', [
                'vendor_id' => $vendor->id,
                'error'     => $e->getMessage()
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Failed to update Vendor',
                'type'       => 'error',
                'item_id'    => $vendor->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to update vendor.');
            return back()->withInput();
        }
    }

    public function destroy(Vendor $vendor)
    {
        try {
            if ($vendor->pumpFuelStocks()->exists()) {
                Alert::error('Error', 'Vendor is in use and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($vendor) {
                $vendor->delete();

                AuditLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => 'Deleted Vendor',
                    'type'       => 'delete',
                    'item_id'    => $vendor->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Vendor deleted successfully.');
            return redirect()->route('vendors.index');
        } catch (\Exception $e) {
            Log::error('Vendor delete failed', [
                'vendor_id' => $vendor->id,
                'error'     => $e->getMessage()
            ]);

            Alert::error('Error', 'Failed to delete vendor.');
            return back();
        }
    }

    public function vendorStatusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $vendor = Vendor::where('uuid', $uuid)->firstOrFail();

            $vendor->update([
                'is_active' => $request->status === 'active',
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Vendor Status',
                'type'       => 'update',
                'item_id'    => $vendor->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Vendor status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('Vendor status update failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to update vendor status.');
            return back();
        }
    }
}
