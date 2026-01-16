<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\FuelStation;
use App\Models\FuelType;

class FuelStationStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //fetch a Fuel Station and fuel type uuid for seeding
        $fuelStation = FuelStation::first();
        $fuelType = FuelType::first();


        DB::table('fuel_station_stocks')->insert([
            [
                'uuid' => Str::uuid(),
                'fuel_station_uuid' => $fuelStation->uuid,
                'fuel_type_uuid' => $fuelType->uuid,
                'quantity' => 5000.000,
                'purchase_price' => 95.50,
                'total_cost' => 477500.00,
                'reference_no' => 'INIT-STOCK-001',
                'stock_date' => now(),
                'is_initial_stock' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
