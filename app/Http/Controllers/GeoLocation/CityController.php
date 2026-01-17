<?php

namespace App\Http\Controllers\GeoLocation;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Center;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'center_uuid', 'is_active']);

        $cities = City::with('center.governorate')
            ->when($filters['name'] ?? null, fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->when($filters['center_uuid'] ?? null, fn ($q, $center) => $q->where('center_uuid', $center))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()->paginate(20)->withQueryString();

        $centers = Center::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Cities",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cities",
            "second_item_link" => route('cities.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.geo-location.cities.index', compact('breadcrumb', 'cities', 'centers', 'filters'));
    }

    public function create()
    {
        $centers = Center::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Create City",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cities",
            "second_item_link" => route('cities.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.geo-location.cities.create', compact('centers', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name',
            'center_uuid' => 'required|exists:centers,uuid',
            'is_active' => 'required|boolean'
        ]);

        DB::beginTransaction();
        try {
            $city = City::create([
                'uuid' => Str::uuid(),
                'name' => $validated['name'],
                'center_uuid' => $validated['center_uuid'],
                'is_active' => $validated['is_active']
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created City',
                'type' => 'create',
                'item_id' => $city->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();
            Alert::success('Success', 'City created successfully.');
            return redirect()->route('cities.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('City create failed', ['error' => $e->getMessage(), 'data' => $request->all()]);
            Alert::error('Error', 'Failed to create city.');
            return back()->withInput();
        }
    }


    public function show($uuid)
    {
        $city = City::with(['center.governorate.region'])->where('uuid', $uuid)->firstOrFail();

        $breadcrumb = [
            "page_header" => "City Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cities",
            "second_item_link" => route('cities.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.geo-location.cities.show', compact('city', 'breadcrumb'));
    }


    public function edit($uuid)
    {
        $city = City::with('center')->where('uuid', $uuid)->firstOrFail();
        $centers = Center::where('is_active', true)->get();

        $breadcrumb = [
            "page_header" => "Edit City",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Cities",
            "second_item_link" => route('cities.index'),
            "second_item_icon" => "fa-map",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.geo-location.cities.edit', compact('city', 'centers', 'breadcrumb'));
    }

    public function update(Request $request, $uuid)
    {
        $city = City::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name,' . $city->id,
            'center_uuid' => 'required|exists:centers,uuid',
            'is_active' => 'required|boolean'
        ]);

        DB::beginTransaction();
        try {
            $city->update($validated);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated City',
                'type' => 'update',
                'item_id' => $city->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();
            Alert::success('Success', 'City updated successfully.');
            return redirect()->route('cities.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('City update failed', ['error' => $e->getMessage(), 'city' => $city->id]);
            Alert::error('Error', 'Failed to update city.');
            return back()->withInput();
        }
    }

    public function destroy(City $city)
    {
        try {
            DB::transaction(function () use ($city) {
                $city->delete();

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Deleted City',
                    'type' => 'delete',
                    'item_id' => $city->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            });

            Alert::success('Success', 'City deleted successfully.');
            return redirect()->route('cities.index');
        } catch (\Exception $e) {
            Log::error('City delete failed', ['error' => $e->getMessage(), 'id' => $city->id]);
            Alert::error('Error', 'Failed to delete city.');
            return back();
        }
    }

    public function cityStatusUpdate(Request $request, $uuid)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $city = City::where('uuid', $uuid)->firstOrFail();
            $city->is_active = $request->status === 'active' ? 1 : 0;
            $city->save();

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Updated City Status',
                'type'       => 'update',
                'item_id'    => $city->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Alert::success('Success', 'City status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('City status update failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            Alert::error('Error', 'Failed to update city status.');
            return back();
        }
    }


}
