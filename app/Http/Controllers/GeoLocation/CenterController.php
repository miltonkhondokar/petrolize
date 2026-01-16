<?php

namespace App\Http\Controllers\GeoLocation;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Governorate;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class CenterController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'governorate_uuid', 'is_active']);

        $centers = Center::with('governorate')
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->when($filters['governorate_uuid'] ?? null, fn($q, $gov) => $q->where('governorate_uuid', $gov))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn($q) => $q->where('is_active', $filters['is_active']))
            ->latest()->paginate(20)->withQueryString();

        $governorates = Governorate::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Centers",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Centers",
            "second_item_link" => route('center.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.geo-location.centers.index', compact('breadcrumb', 'centers', 'governorates', 'filters'));
    }

    public function create()
    {
        $governorates = Governorate::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Create Center",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Centers",
            "second_item_link" => route('center.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.geo-location.centers.create', compact('breadcrumb', 'governorates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:centers,name',
            'governorate_uuid' => 'required|exists:governorates,uuid',
            'is_active' => 'required|boolean'
        ]);

        DB::beginTransaction();
        try {
            $center = Center::create([
                'uuid' => Str::uuid(),
                'name' => $validated['name'],
                'governorate_uuid' => $validated['governorate_uuid'],
                'is_active' => $validated['is_active'],
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Center',
                'type' => 'create',
                'item_id' => $center->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();
            Alert::success('Success', 'Center created successfully.');
            return redirect()->route('center.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Center create failed', ['error' => $e->getMessage(), 'data' => $request->all()]);
            Alert::error('Error', 'Failed to create center.');
            return back()->withInput();
        }
    }

    public function edit($uuid)
    {
        $center = Center::with('governorate')->where('uuid', $uuid)->firstOrFail();
        $governorates = Governorate::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Edit Center",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Centers",
            "second_item_link" => route('center.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.geo-location.centers.edit', compact('center', 'governorates', 'breadcrumb'));
    }

    public function update(Request $request, Center $center)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:centers,name,' . $center->id,
            'governorate_uuid' => 'required|exists:governorates,uuid',
            'is_active' => 'required|boolean'
        ]);

        DB::beginTransaction();
        try {
            $center->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated Center',
                'type' => 'update',
                'item_id' => $center->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();
            Alert::success('Success', 'Center updated successfully.');
            return redirect()->route('center.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Center update failed', ['error' => $e->getMessage(), 'center' => $center->id]);
            Alert::error('Error', 'Failed to update center.');
            return back()->withInput();
        }
    }

    public function destroy(Center $center)
    {
        try {
            if ($center->cities()->exists()) {
                Alert::error('Error', 'Center has cities and cannot be deleted.');
                return back();
            }

            DB::transaction(function () use ($center) {
                $center->delete();
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted Center',
                    'type' => 'delete',
                    'item_id' => $center->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            });

            Alert::success('Success', 'Center deleted successfully.');
            return redirect()->route('center.index');
        } catch (\Exception $e) {
            Log::error('Center delete failed', ['error' => $e->getMessage(), 'id' => $center->id]);
            Alert::error('Error', 'Failed to delete center.');
            return back();
        }
    }
}
