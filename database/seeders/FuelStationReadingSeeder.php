<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\FuelStation;
use App\Models\FuelType;

class FuelStationReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch first active Fuel Station
        $fuelStation = FuelStation::where('is_active', true)->first();

        // Fetch first active fuel type
        $fuelType = FuelType::where('is_active', true)->first();

        // Safety check (very important)
        if (!$fuelStation || !$fuelType) {
            $this->command->warn('Fuel Station or FuelType not found. Skipping PumpFuelReadingSeeder.');
            return;
        }

        DB::table('fuel_station_readings')->insert([
            [
                'uuid' => Str::uuid(),
                'fuel_station_uuid' => $fuelStation->uuid,
                'fuel_type_uuid' => $fuelType->uuid,
                'nozzle_number' => 1,
                'reading' => 5000.000,
                'reading_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'fuel_station_uuid' => $fuelStation->uuid,
                'fuel_type_uuid' => $fuelType->uuid,
                'nozzle_number' => 2,
                'reading' => 5000.000,
                'reading_date' => now(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
