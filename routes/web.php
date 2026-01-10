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
