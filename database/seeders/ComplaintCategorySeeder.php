<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('complaint_categories')->insert([
            [
                'uuid' => Str::uuid(),
                'code' => 'fuel_shortage',
                'name' => 'Fuel Shortage',
                'description' => 'Fuel is unavailable or insufficient at the station',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'code' => 'nozzle_issue',
                'name' => 'Nozzle Issue',
                'description' => 'Problems related to fuel nozzle operation',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'code' => 'power_failure',
                'name' => 'Power Failure',
                'description' => 'Electricity or power-related issues',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
