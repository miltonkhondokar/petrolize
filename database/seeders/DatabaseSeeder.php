<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            //Permissions & Roles
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            RoleHasPermissionsTableSeeder::class,

            //Users
            UsersTableSeeder::class,

            //Geo-Location (Regions → Governorates → Centers → Cities)
            RegionSeeder::class,
            GovernorateSeeder::class,
            CenterSeeder::class,
            CitySeeder::class,

            //fuel units
            FuelUnitSeeder::class,

            //Fuel types and Pumps
            FuelTypeSeeder::class,
            FuelStationSeeder::class,

            //Fuel Station fuel prices
            FuelStationPriceSeeder::class,

            // Vendors
            VendorSeeder::class,

            //Fuel Station fuel stocks
            FuelStationStockSeeder::class,

            //Cost categories
            CostCategorySeeder::class,

            //Cost entries
            CostEntrySeeder::class,

            //Fuel Station complaints
            FuelStationComplaintSeeder::class,

            //Fuel Station fuel readings
            FuelStationReadingSeeder::class,
        ]);
    }
}
