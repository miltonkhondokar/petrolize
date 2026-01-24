<?php

namespace App\Http\Controllers\AccessManagement\Permission\Role;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;

class UserRoleController extends Controller
{
    public function index()
    {
        // $this->authorize('user_role_list');

        try {
            $users = User::paginate(10);
            return view('access-management.permission.user-permission.index', ['users' => $users]);
        } catch (\Exception $e) {
            Log::error('Failed to load user list: ' . $e->getMessage());
            Alert::error('Error', 'Failed to load user list. Please try again.')->persistent('OK');
            return redirect()->back();
        }
    }

    public function create()
    {
        abort(403, 'Unauthorized action.');
    }

    public function store(Request $request)
    {
        abort(403, 'Unauthorized action.');
    }

    public function show($id)
    {
        abort(403, 'Unauthorized action.');
    }

    public function edit(User $user)
    {
        // $this->authorize('user_role_edit');
        try {
            $roles = Role::pluck('name', 'name')->all();
            $userRoles = $user->roles->pluck('name', 'name')->all();
    
            return view('access-management.permission.user-permission.edit',  [
                'user' => $user,
                'roles' => $roles,
                'userRoles' => $userRoles
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load user edit page: ' . $e->getMessage());
            Alert::error('Error', 'Failed to load user edit page. Please try again.')->persistent('OK');
            return redirect()->back();
        }
    }
    

    public function update(Request $request, User $user)
    {
        // $this->authorize('user_role_update');
        
        try {
            $request->validate([
                'roles' => 'required'
            ]);
    
            // Update the user's roles
            $user->syncRoles($request->roles);
    
            Alert::success('Success', 'User Updated Successfully with roles')->persistent('OK');
            return redirect()->route('user-role.index');
        } catch (\Exception $e) {
            Log::error('Failed to update user roles: ' . $e->getMessage());
            Alert::error('Error', 'Failed to update user roles. Please try again.')->persistent('OK');
            return redirect()->back();
        }
    }
    

    public function destroy($userId)
    {
        abort(403, 'Unauthorized action.');
    }
}
