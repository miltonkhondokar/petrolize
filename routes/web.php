<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\DemoRegistrationController;

/*
|--------------------------------------------------------------------------
| Admin Module Routes
|--------------------------------------------------------------------------
*/

// Access Management Routes
require __DIR__.'/application/admin-module/permission/permission.php';
require __DIR__.'/application/admin-module/role/role.php';
// User Management Routes
require __DIR__.'/application/admin-module/user/user.php';
// Login and Dashboard Management Routes
require __DIR__.'/application/admin-module/application.php';

// Audit Management Routes
require __DIR__ . '/application/audit/audit.php';

// Fuel Management Routes
require __DIR__.'/application/fuel/fuel.php';

// Fuel Station Management Routes
require __DIR__.'/application/fuel-station/fuel_station.php';

//fuel Unit Management Routes
require __DIR__.'/application/fuel-unit/fuel_unit.php';

//fuel Unit Price Management Routes
require __DIR__.'/application/fuel-unit-price/fuel_unit_price.php';

//cost category Management Routes
require __DIR__.'/application/cost-category/cost_category.php';

//complaint Management Routes
require __DIR__.'/application/complaint-category/complaint_category.php';
