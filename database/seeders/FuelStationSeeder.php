<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FuelStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fuel_stations')->insert([
            [
                'uuid' => Str::uuid(),
                'user_uuid' => null,                // manager, optional
                'name' => 'Fuel Flow Station – Dhaka',
                'region_uuid' => null,              // fill if you have region UUIDs
                'governorate_uuid' => null,         // fill if available
                'center_uuid' => null,              // fill if available
                'city_uuid' => null,                // fill if available
                'location' => 'Dhaka',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'user_uuid' => null,
                'name' => 'Fuel Flow Station – Chattogram',
                'region_uuid' => null,
                'governorate_uuid' => null,
                'center_uuid' => null,
                'city_uuid' => null,
                'location' => 'Chattogram',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
