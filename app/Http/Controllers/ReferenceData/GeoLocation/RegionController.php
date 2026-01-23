<?php

namespace App\Http\Controllers\ReferenceData\GeoLocation;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'code', 'is_active']);

        $regions = Region::query()
            ->when($filters['name'] ?? null, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($filters['code'] ?? null, fn ($q, $v) => $q->where('code', 'like', "%{$v}%"))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $breadcrumb = [
            "page_header" => "Regions",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Regions",
            "second_item_link" => route('regions.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.geo-location.regions.index', compact('breadcrumb', 'regions', 'filters'));
    }

    public function create()
    {
        $breadcrumb = [
            "page_header" => "Create Region",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Regions",
            "second_item_link" => route('regions.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.geo-location.regions.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name',
            'code' => 'nullable|string|max:10|unique:regions,code',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $region = Region::create([
                'uuid' => Str::uuid(),
                'name' => $validated['name'],
                'code' => strtoupper($validated['code'] ?? null),
                'is_active' => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Region',
                'type' => 'create',
                'item_id' => $region->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Region created successfully.');
            return redirect()->route('regions.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Region create failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to create Region',
                'type' => 'error',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to create region.');
            return back()->withInput();
        }
    }

    public function show($uuid)
    {
        $region = Region::with('governorates')->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Region Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Regions",
            "second_item_link" => route('regions.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.geo-location.regions.show', compact('region', 'breadcrumb'));
    }

    public function edit($uuid)
    {
        $region = Region::where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "Edit Region",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Regions",
            "second_item_link" => route('regions.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.geo-location.regions.edit', compact('region', 'breadcrumb'));
    }

    public function update(Request $request, $uuid)
    {
        $region = Region::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name,' . $region->id,
            'code' => 'nullable|string|max:10|unique:regions,code,' . $region->id,
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $region->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code'] ?? null),
                'is_active' => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Region',
                'type' => 'update',
                'item_id' => $region->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Region updated successfully.');
            return redirect()->route('regions.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Region update failed', [
                'region_id' => $region->id,
                'error' => $e->getMessage(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to update Region',
                'type' => 'error',
                'item_id' => $region->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to update region.');
            return back()->withInput();
        }
    }

    public function destroy(Region $region)
    {
        try {
            if ($region->governorates()->exists()) {
                Alert::error('Error', 'Region is in use and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($region) {
                $region->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Region',
                    'type' => 'delete',
                    'item_id' => $region->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Region deleted successfully.');
            return redirect()->route('regions.index');
        } catch (\Exception $e) {
            Log::error('Region delete failed', [
                'region_id' => $region->id,
                'error' => $e->getMessage(),
            ]);

            Alert::error('Error', 'Failed to delete region.');
            return back();
        }
    }

    public function regionStatusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $region = Region::where('uuid', $uuid)->firstOrFail();
            $region->is_active = $request->status === 'active' ? 1 : 0;
            $region->save();

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated Region Status',
                'type'       => 'update',
                'item_id'    => $region->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'Region status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('Region status update failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            Alert::error('Error', 'Failed to update region status.');
            return back();
        }
    }

}
