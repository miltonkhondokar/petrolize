<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\FuelStation;
use App\Models\ComplaintCategory;
use App\Models\User;
use App\Constants\UserType;

class FuelStationComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $fuelStation = FuelStation::first();
        $category = ComplaintCategory::where('code', 'nozzle_issue')->first();

        // Pick a valid user (manager preferred)
        $user = User::role(UserType::FUEL_STATION_MANAGER)->first()
              ?? User::first();

        if (! $fuelStation || ! $category || ! $user) {
            return; // safety guard
        }

        DB::table('fuel_station_complaints')->insert([
            [
                'uuid' => (string) Str::uuid(),
                'fuel_station_uuid' => $fuelStation->uuid,
                'complaint_category_uuid' => $category->uuid,
                'user_uuid' => $user->uuid, // ✅ FIX
                'title' => 'Nozzle not dispensing fuel',
                'description' => 'Nozzle #3 is stuck and not dispensing fuel properly',
                'status' => 'open',
                'complaint_date' => now(),
                'resolved_date' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
