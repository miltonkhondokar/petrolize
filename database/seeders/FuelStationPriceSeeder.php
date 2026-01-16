<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\FuelStation;
use App\Models\FuelType;

class FuelStationPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //fetch pum and Fuel Station uuids
        $fuelStation = FuelStation::first();
        $fuelType = FuelType::first();


        DB::table('fuel_station_prices')->insert([
            [
                'uuid' => Str::uuid(),
                'fuel_station_uuid' => $fuelStation->uuid,
                'fuel_type_uuid' => $fuelType->uuid,
                'price_per_unit' => 110.50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
