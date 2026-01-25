<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Constants\UserType;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@fuel-flow.test',
                'phone' => '8801000000001',
                'role' => UserType::ADMIN,
            ],
            [
                'name' => 'Executive User',
                'email' => 'executive@fuel-flow.test',
                'phone' => '8801000000002',
                'role' => UserType::EXECUTIVE,
            ],
            [
                'name' => 'Fuel Station Manager User',
                'email' => 'manager@fuel-flow.test',
                'phone' => '8801000000003',
                'role' => UserType::FUEL_STATION_MANAGER,
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('Master@1234567890!.'),
                    'email_verification_status' => 1,
                    'user_status' => 1,
                ]
            );

            // Assign role (Spatie)
            if (! $user->hasRole($data['role'])) {
                $user->syncRoles([$data['role']]);
            }
        }
    }
}
