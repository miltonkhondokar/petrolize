<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Center;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Riyadh City' => ['Olaya', 'Al-Malaz', 'Al-Rawdhah'],
            'Jeddah Center' => ['Corniche', 'Al-Balad', 'Ash-Shati'],
            'Al-Madinah Center' => ['Al-Hijrah', 'Quba', 'Al-Masjid'],
            // add more center => cities mapping here
        ];

        foreach ($data as $centerName => $cities) {
            $center = Center::where('name', $centerName)->first();
            if (!$center) continue;

            foreach ($cities as $name) {
                City::create([
                    'uuid' => Str::uuid(),
                    'center_uuid' => $center->uuid,
                    'name' => $name,
                    'is_active' => true,
                ]);
            }
        }
    }
}
