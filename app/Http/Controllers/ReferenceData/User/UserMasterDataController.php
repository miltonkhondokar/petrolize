<?php

namespace App\Http\Controllers\ReferenceData\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Constants\UserType;
use App\Constants\UserStatus;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class UserMasterDataController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'email', 'phone', 'gender']);

        $users = User::query()
            ->with('roles')
            ->when($filters['name'] ?? null, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($filters['email'] ?? null, function ($query, $email) {
                $query->where('email', 'like', "%{$email}%");
            })
            ->when($filters['phone'] ?? null, function ($query, $phone) {
                $query->where('phone', 'like', "%{$phone}%");
            })
            ->when(isset($filters['gender']) && $filters['gender'] !== '', function ($query) use ($filters) {
                $query->where('gender', $filters['gender']);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $breadcrumb = [
            "page_header" => "User Management",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Users",
            "second_item_link" => route('user-master-data'),
            "second_item_icon" => "fa-users",
            "third_item_name" => "List",
            "third_item_link" => "#",
            "third_item_icon" => "fa-list",
        ];

        return view('application.pages.reference-data.users.index', compact('users', 'filters', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all(); // Spatie roles
        $userTypes = UserType::all(); // <-- get all user types

        $breadcrumb = [
            "page_header" => "Create New User",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Users",
            "second_item_link" => route('user-master-data'),
            "second_item_icon" => "fa-users",
            "third_item_name" => "Create",
            "third_item_link" => "#",
            "third_item_icon" => "fa-plus",
        ];

        return view('application.pages.reference-data.users.create', compact('breadcrumb', 'roles', 'userTypes'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Full validation including regex for password
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|unique:users,phone|regex:/^[0-9]{11}$/',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'gender' => 'required|in:1,2',
            'user_status' => 'required|in:0,1',
            'user_type' => 'required|string|in:' . implode(',', \App\Constants\UserType::all()),
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ], [
            'password.regex' => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 special character',
            'phone.regex' => 'Phone must be exactly 11 digits',
            'user_type.in' => 'Invalid user type selected'
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'user_type' => $request->user_type,
                'user_status' => $request->user_status,
                'email_verification_status' => 1,
                'email_verified_at' => now(),
            ]);


            if ($request->has('roles')) {
                $roles = Role::whereIn('id', $request->roles)->get();
                $user->syncRoles($roles);
            }


            // Audit log for success
            AuditLog::create([
                'user_id' => Auth::id() ?? null,
                'action' => 'Created User',
                'type' => 'create',
                'item_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();


            Alert::success('Success', 'User Created Successfully')->persistent('OK');
            return redirect()->route('user-master-data');

        } catch (\Exception $e) {
            DB::rollBack();

            // Audit log for failure
            AuditLog::create([
                'user_id' => Auth::id() ?? null,
                'action' => 'Failed to create User: ' . $request->email,
                'type' => 'error',
                'item_id' => null, // no user created
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::error('Error creating user: ' . $e->getMessage());

            Alert::error('Error', 'Failed to create user. Please try again.')->persistent('OK');

            return redirect()->back()->withInput();

        }
    }

    /**
     * Display the specified user.
     */
    public function show($uuid)
    {
        abort(403, 'Unauthorized action.');

        $user = User::where('uuid', $uuid)->with('roles')->firstOrFail();

        $breadcrumb = [
            "page_header" => "User Details",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Users",
            "second_item_link" => route('user-master-data'),
            "second_item_icon" => "fa-users",
            "third_item_name" => "Details",
            "third_item_link" => "#",
            "third_item_icon" => "fa-eye",
        ];

        return view('application.pages.reference-data.users.show', compact('user', 'breadcrumb'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($uuid)
    {
        $user = User::where('uuid', $uuid)->with('roles')->firstOrFail();
        $roles = Role::all();

        $breadcrumb = [
            "page_header" => "Edit User",
            "first_item_name" => "Dashboard",
            "first_item_link" => route('/'),
            "first_item_icon" => "fa-home",
            "second_item_name" => "Users",
            "second_item_link" => route('user-master-data'),
            "second_item_icon" => "fa-users",
            "third_item_name" => "Edit",
            "third_item_link" => "#",
            "third_item_icon" => "fa-edit",
        ];

        return view('application.pages.reference-data.users.edit', compact('user', 'roles', 'breadcrumb'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Validation including regex for password if provided
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id . '|regex:/^[0-9]{11}$/',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'gender' => 'required|in:1,2',
            'user_status' => 'required|in:0,1',
            'user_type' => 'required|string|in:' . implode(',', \App\Constants\UserType::all()),
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ], [
            'password.regex' => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 special character',
            'phone.regex' => 'Phone must be exactly 11 digits',
            'user_type.in' => 'Invalid user type selected'
        ]);

        DB::beginTransaction();

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->user_type = $request->user_type;
            $user->user_status = $request->user_status;

            // Update password only if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Sync roles if provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            } else {
                $user->roles()->detach();
            }

            // Audit log for success
            AuditLog::create([
                'user_id' => Auth::id() ?? null,
                'action' => 'Updated User',
                'type' => 'update',
                'item_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Alert::success('Success', 'User Updated Successfully')->persistent('OK');
            return redirect()->route('user-master-data');

        } catch (\Exception $e) {
            DB::rollBack();

            // Audit log for failure
            AuditLog::create([
                'user_id' => Auth::id() ?? null,
                'action' => 'Failed to update User: ' . $user->email,
                'type' => 'error',
                'item_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::error('Error updating user: ' . $e->getMessage());

            Alert::error('Error', 'Failed to update user. Please try again.')->persistent('OK');

            return redirect()->back()->withInput();

        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($uuid)
    {
        abort(403, 'Unauthorized action.');
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Check if user has any associated data
        $hasPumps = \App\Models\FuelStation::where('user_uuid', $uuid)->exists();

        if ($hasPumps) {
            return redirect()->route('user-master-data')
                ->with('error', 'Cannot delete user. User is assigned as fuel station manager.');
        }

        $user->delete();

        return redirect()->route('user-master-data')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Update user status.
     */
    public function status(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $user->update([
            'user_status' => $request->status === 'active' ? UserStatus::ACTIVE : UserStatus::INACTIVE,
        ]);

        return redirect()->back()
            ->with('success', 'User status updated successfully.');
    }
}
