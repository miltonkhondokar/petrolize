<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use Illuminate\Support\Str;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            ['name' => 'Riyadh', 'code' => 'RYD', 'is_active' => true],
            ['name' => 'Makkah', 'code' => 'MAK', 'is_active' => true],
            ['name' => 'Madinah', 'code' => 'MDN', 'is_active' => true],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['code' => $region['code']], // avoid duplicates
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $region['name'],
                    'is_active' => $region['is_active'],
                ]
            );
        }
    }
}
