<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Center;
use App\Models\Governorate;
use Illuminate\Support\Str;

class CenterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Riyadh'     => ['Riyadh City', 'Al-Kharj Center', 'Diriyah Center'],
            'Jeddah'     => ['Jeddah Center'],
            'Al-Madinah' => ['Al-Madinah Center', 'Yanbu Center'], // ✅ FIXED
        ];

        foreach ($data as $govName => $centers) {
            $gov = Governorate::where('name', $govName)->first();
            if (!$gov) {
                continue;
            }

            foreach ($centers as $name) {
                Center::create([
                    'uuid' => (string) Str::uuid(), // ✅ make string
                    'governorate_uuid' => $gov->uuid,
                    'name' => $name,
                    'is_active' => true,
                ]);
            }
        }
    }
}
