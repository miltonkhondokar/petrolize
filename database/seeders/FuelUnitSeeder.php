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
            ['name' => 'Milliliter', 'abbreviation' => 'mL', 'description' => '1/1000 of a liter', 'is_active' => false],
            ['name' => 'US Gallon', 'abbreviation' => 'gal', 'description' => 'US customary gallon (~3.785 L)', 'is_active' => false],
            ['name' => 'Imperial Gallon', 'abbreviation' => 'imp gal', 'description' => 'UK gallon (~4.546 L)', 'is_active' => false],
            ['name' => 'Barrel', 'abbreviation' => 'bbl', 'description' => 'Standard oil barrel (~159 L)', 'is_active' => false],
            ['name' => 'Cubic Meter', 'abbreviation' => 'm³', 'description' => 'Metric cubic meter (1000 L)', 'is_active' => false],
            ['name' => 'US Pint', 'abbreviation' => 'pt', 'description' => 'US pint (~0.473 L)', 'is_active' => false],
            ['name' => 'US Quart', 'abbreviation' => 'qt', 'description' => 'US quart (~0.946 L)', 'is_active' => false],
            ['name' => 'UK Pint', 'abbreviation' => 'imp pt', 'description' => 'UK pint (~0.568 L)', 'is_active' => false],
            ['name' => 'UK Quart', 'abbreviation' => 'imp qt', 'description' => 'UK quart (~1.136 L)', 'is_active' => false],
            ['name' => 'US Fluid Ounce', 'abbreviation' => 'fl oz', 'description' => 'US fluid ounce (~29.573 mL)', 'is_active' => false],
            ['name' => 'UK Fluid Ounce', 'abbreviation' => 'fl oz (imp)', 'description' => 'UK fluid ounce (~28.41 mL)', 'is_active' => false],
            ['name' => 'Deciliter', 'abbreviation' => 'dL', 'description' => '1/10 of a liter', 'is_active' => false],
            ['name' => 'Cubic Foot', 'abbreviation' => 'ft³', 'description' => 'Imperial cubic foot (~28.316 L)', 'is_active' => false],
            ['name' => 'Cubic Inch', 'abbreviation' => 'in³', 'description' => 'Small volume (~16.387 mL)', 'is_active' => false],
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
