<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Support\Str;

class GovernorateSeeder extends Seeder
{
    public function run()
    {
        // Only governorates that are used by CenterSeeder
        $data = [
            'RYD' => ['Riyadh'],
            'MAK' => ['Jeddah'],
            'MDN' => ['Al-Madinah'], // IMPORTANT: matches your old list + CitySeeder center name "Al-Madinah Center"
        ];

        foreach ($data as $regionCode => $governorates) {
            $region = Region::where('code', $regionCode)->first();
            if (!$region) {
                continue;
            }

            foreach ($governorates as $name) {
                Governorate::updateOrCreate(
                    [
                        'region_uuid' => $region->uuid,
                        'name' => $name,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'code' => null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
