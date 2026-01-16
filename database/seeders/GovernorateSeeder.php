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
        // Map of Region codes to Governorates
        $data = [
            'RYD' => ['Riyadh', 'Al-Kharj', 'Al-Majma', 'Al-Muzahimiyah', 'Diriyah', 'Al-Ghat', 'Riyadh East'],
            'MAK' => ['Jeddah', 'Makkah', 'Taif', 'Al-Qunfudhah', 'Ranyah'],
            'MDN' => ['Al-Madinah', 'Yanbu', 'Badr', 'Ula', 'Al-Hijrah'],
            'EPR' => ['Dammam', 'Dhahran', 'Al-Khobar', 'Hofuf', 'Qatif'],
            'QSM' => ['Buraidah', 'Unaizah', 'Al-Rass'],
            'HIL' => ['Ha’il', 'Sakaka', 'Baish', 'Shinan'],
            'TBK' => ['Tabuk', 'Duba', 'Haql', 'Al-Wajh'],
            'NBR' => ['Arar', 'Rafha', 'Turaif'],
            'JZN' => ['Jizan', 'Sabya', 'Abu Arish'],
            'NJR' => ['Najran', 'Yadamah', 'Badr Al-Janoub'],
            'BHH' => ['Al-Bahah', 'Baljurashi', 'Al-Mikhwah'],
            'ASR' => ['Abha', 'Khamis Mushait', 'Bisha'],
            'ALJ' => ['Sakaka', 'Dumat Al-Jandal', 'Al-Qurayyat'],
        ];

        foreach ($data as $regionCode => $governorates) {
            $region = Region::where('code', $regionCode)->first();
            if (!$region) continue;

            foreach ($governorates as $name) {
                Governorate::create([
                    'uuid' => (string) Str::uuid(),
                    'region_uuid' => $region->uuid,
                    'name' => $name,
                    'code' => null,
                    'is_active' => true,
                ]);
            }
        }
    }
}
