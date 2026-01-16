<?php

namespace App\Http\Controllers\AccessManagement\Permission;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize('permission_list');
    
        $permissions = Permission::paginate(10);
    
        // Define breadcrumb for the page
        $breadcrumb = [
            "page_header" => "Access Management",
            "first_item_name" => "Permissions",
            "first_item_link" => route('permissions.index'),
            "second_item_name" => "Permission List",
            "second_item_link" => route('permissions.index'),
        ];
    
        return view('access-management.permission.index', compact('permissions', 'breadcrumb'));
    }
    

    public function create()
    {
        // $this->authorize('permission_create');
        
        // Define breadcrumb for the page
        $breadcrumb = [
            "page_header" => "Access Management",
            "first_item_name" => "Permissions",
            "first_item_link" => route('permissions.index'),
            "second_item_name" => "Create Permission",
            "second_item_link" => route('permissions.create'),
        ];
    
        return view('access-management.permission.create', compact('breadcrumb'));
    }
    

    public function store(Request $request)
    {
        // $this->authorize('permission_store');

        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name'
            ]
        ]);

        try {
            Permission::create([
                'name' => $request->name
            ]);

            Alert::success('Success', 'Permission Created Successfully')->persistent('OK');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Error creating permission: ' . $e->getMessage());

            Alert::error('Error', 'Failed to create permission. Please try again.')->persistent('OK');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        abort(403, 'Unauthorized action.');
    }

    public function edit(Permission $permission)
    {
        $this->authorize('permission_edit');
        // Define breadcrumb for the page
        $breadcrumb = [
            "page_header" => "Access Management",
            "first_item_name" => "Permissions",
            "first_item_link" => route('permissions.index'),
            "second_item_name" => "Permission List",
            "second_item_link" => route('permissions.index'),
        ];


        return view('access-management.permission.edit', compact('permission', 'breadcrumb'));
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('permission_update');

        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,' . $permission->id
            ]
        ]);

        try {

            $permission->update([
                'name' => $request->name
            ]);

            Alert::success('Success', 'Permission Updated Successfully')->persistent('OK');
            return redirect()->route('permissions.index');
        } catch (\Exception $e) {
            Log::error('Error updating permission: ' . $e->getMessage());
            Alert::error('Error', 'Failed to update permission. Please try again.')->persistent('OK');
            return redirect()->back();
        }
    }

    public function destroy($permissionId)
    {
        $this->authorize('permission_delete');
        
        try {

            $permission = Permission::findOrFail($permissionId);

            $permission->delete();

            Alert::success('Success', 'Permission Deleted Successfully')->persistent('OK');
            return redirect()->route('permissions.index');
        } catch (\Exception $e) {
            Log::error('Error deleting permission: ' . $e->getMessage());

            Alert::error('Error', 'Failed to delete permission. Please try again.')->persistent('OK');
            return redirect()->back();
        }
    }
}
