<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FuelUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Liter', 'abbreviation' => 'L', 'description' => 'Standard liter unit (metric)', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            DB::table('fuel_units')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $unit['name'],
                'abbreviation' => $unit['abbreviation'],
                'description' => $unit['description'],
                'is_active' => $unit['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
