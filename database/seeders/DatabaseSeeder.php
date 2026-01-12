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

            //fuel units
            FuelUnitSeeder::class,

            //Fuel types and Pumps
            FuelTypeSeeder::class,
            PumpSeeder::class,

            //Pump fuel prices
            PumpFuelPriceSeeder::class,

            // Vendors
            VendorSeeder::class,

            //Pump fuel stocks
            PumpFuelStockSeeder::class,

            //Cost categories
            CostCategorySeeder::class,

            //Cost entries
            CostEntrySeeder::class,

            //Pump complaints
            PumpComplaintSeeder::class,

            //Pump fuel readings
            PumpFuelReadingSeeder::class,
        ]);
    }
}
