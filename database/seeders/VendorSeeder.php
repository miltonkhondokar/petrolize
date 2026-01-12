<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['name' => 'Fuel Supplier A', 'phone' => '01710000001', 'email' => 'a@example.com', 'address' => 'Dhaka'],
            ['name' => 'Fuel Supplier B', 'phone' => '01710000002', 'email' => 'b@example.com', 'address' => 'Chittagong'],
            ['name' => 'Fuel Supplier C', 'phone' => '01710000003', 'email' => 'c@example.com', 'address' => 'Khulna'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
