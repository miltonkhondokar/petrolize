<?php

namespace App\Http\Controllers\GeoLocation;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Region;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class GovernorateController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'region_uuid', 'is_active']);

        $governorates = Governorate::query()
            ->with('region')
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->when($filters['region_uuid'] ?? null, fn($q, $region) => $q->where('region_uuid', $region))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $regions = Region::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Governorates",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Governorates",
            "second_item_link" => route('governorate.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.geo-location.governorate.index', compact('breadcrumb', 'governorates', 'regions', 'filters'));
    }

    public function create()
    {
        $regions = Region::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Create Governorate",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Governorates",
            "second_item_link" => route('governorate.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.geo-location.governorate.create', compact('regions', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:governorates,name',
            'region_uuid' => 'required|exists:regions,uuid',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $gov = Governorate::create([
                'uuid' => Str::uuid(),
                'name' => $validated['name'],
                'region_uuid' => $validated['region_uuid'],
                'is_active' => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Governorate',
                'type' => 'create',
                'item_id' => $gov->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'Governorate created successfully.');
            return redirect()->route('governorate.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Governorate create failed', ['error' => $e->getMessage(), 'data' => $request->all()]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to create Governorate',
                'type' => 'error',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to create governorate.');
            return back()->withInput();
        }
    }

    public function edit($uuid)
    {
        $gov = Governorate::with('region')->where('uuid', $uuid)->firstOrFail();
        $regions = Region::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Edit Governorate",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Governorates",
            "second_item_link" => route('governorate.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.geo-location.governorate.edit', compact('gov', 'regions', 'breadcrumb'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:governorates,name,' . $governorate->id,
            'region_uuid' => 'required|exists:regions,uuid',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $governorate->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Governorate',
                'type' => 'update',
                'item_id' => $governorate->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            Alert::success('Success', 'Governorate updated successfully.');
            return redirect()->route('governorate.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Governorate update failed', ['error' => $e->getMessage(), 'governorate' => $governorate->id]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Failed to update Governorate',
                'type' => 'error',
                'item_id' => $governorate->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::error('Error', 'Failed to update governorate.');
            return back()->withInput();
        }
    }

    public function destroy(Governorate $governorate)
    {
        try {
            // Check relationships before delete
            if ($governorate->centers()->exists()) {
                Alert::error('Error', 'Governorate is in use and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($governorate) {
                $governorate->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Governorate',
                    'type' => 'delete',
                    'item_id' => $governorate->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            Alert::success('Success', 'Governorate deleted successfully.');
            return redirect()->route('governorate.index');
        } catch (\Exception $e) {
            Log::error('Governorate delete failed', ['error' => $e->getMessage(), 'id' => $governorate->id]);
            Alert::error('Error', 'Failed to delete governorate.');
            return back();
        }
    }
}
