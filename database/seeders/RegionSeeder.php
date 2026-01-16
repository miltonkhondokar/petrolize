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
            ['name' => 'Eastern Province', 'code' => 'EPR', 'is_active' => true],
            ['name' => 'Qassim', 'code' => 'QSM', 'is_active' => true],
            ['name' => 'Ha’il', 'code' => 'HIL', 'is_active' => true],
            ['name' => 'Tabuk', 'code' => 'TBK', 'is_active' => true],
            ['name' => 'Northern Borders', 'code' => 'NBR', 'is_active' => true],
            ['name' => 'Jizan', 'code' => 'JZN', 'is_active' => true],
            ['name' => 'Najran', 'code' => 'NJR', 'is_active' => true],
            ['name' => 'Bahah', 'code' => 'BHH', 'is_active' => true],
            ['name' => 'Asir', 'code' => 'ASR', 'is_active' => true],
            ['name' => 'Al-Jawf', 'code' => 'ALJ', 'is_active' => true],
        ];

        foreach ($regions as $region) {
            Region::create([
                'uuid' => (string) Str::uuid(),
                'name' => $region['name'],
                'code' => $region['code'],
                'is_active' => $region['is_active'],
            ]);
        }
    }
}
