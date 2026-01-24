<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Seed the permissions table.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // User management
            'user_management',
            'user_list',
            'user_create',
            'user_store',
            'user_edit',
            'user_update',
            'user_delete',
            'user_view',

            // Role management
            'role_management',
            'role_list',
            'role_create',
            'role_store',
            'role_edit',
            'role_update',
            'role_delete',
            'role_view',

            // Permission management
            'permission_management',
            'permission_list',
            'permission_create',
            'permission_store',
            'permission_edit',
            'permission_update',
            'permission_delete',
            'permission_view',
            'permission_assign',

            //user role management
            'user_role_management',
            'user_role_list',
            'user_role_create',
            'user_role_store',
            'user_role_edit',
            'user_role_update',
            'user_role_delete',
            'user_role_view',          
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
