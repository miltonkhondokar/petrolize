<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\Fuel\FuelPurchaseController;
use App\Http\Controllers\App\Vendor\VendorPaymentController;
use App\Http\Controllers\App\Fuel\FuelSalesDayController;

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
require __DIR__.'/application/complaints/complaints.php';

// Complaint Category Management Routes
require __DIR__.'/application/complaint-category/complaint_category.php';

// Vendor Management Routes
require __DIR__.'/application/vendor/vendor.php';

// Geo Location Management Routes
require __DIR__.'/application/geo-locations/geo_location.php';

//Fuel Station Fuel Type Management Routes
require __DIR__.'/application/fuel-station-fuel-type/fuel_station_fuel_type.php';

//Cost Entry Management Routes
require __DIR__. '/application/cost-entry/cost_entry.php';



Route::middleware(['auth'])->group(function () {

    // Purchases (Web)
    Route::get('/fuel-purchases', [FuelPurchaseController::class, 'index'])->name('fuel_purchases.index');
    Route::get('/fuel-purchases/create', [FuelPurchaseController::class, 'create'])->name('fuel_purchases.create');
    Route::post('/fuel-purchases', [FuelPurchaseController::class, 'store'])->name('fuel_purchases.store');
    Route::get('/fuel-purchases/{uuid}', [FuelPurchaseController::class, 'show'])->name('fuel_purchases.show');
    Route::post('/fuel-purchases/{uuid}/receive', [FuelPurchaseController::class, 'receive'])->name('fuel_purchases.receive');


    Route::get('/fuel-purchases/{uuid}/edit', [FuelPurchaseController::class, 'edit'])->name('fuel_purchases.edit');
    Route::put('/fuel-purchases/{uuid}', [FuelPurchaseController::class, 'update'])->name('fuel_purchases.update');

    Route::get('/vendor-payments/{uuid}/edit', [VendorPaymentController::class, 'edit'])->name('vendor_payments.edit');
    Route::put('/vendor-payments/{uuid}', [VendorPaymentController::class, 'update'])->name('vendor_payments.update');


    // Vendor Payments (Web)
    Route::get('/vendor-payments', [VendorPaymentController::class, 'index'])->name('vendor_payments.index');
    Route::get('/vendor-payments/create', [VendorPaymentController::class, 'create'])->name('vendor_payments.create');
    Route::post('/vendor-payments', [VendorPaymentController::class, 'store'])->name('vendor_payments.store');
    Route::get('/vendor-payments/{uuid}', [VendorPaymentController::class, 'show'])->name('vendor_payments.show');
    Route::post('/vendor-payments/{uuid}/allocate', [VendorPaymentController::class, 'allocate'])->name('vendor_payments.allocate');

    Route::get('/vendor-payments/unpaid/{vendor_uuid}', [VendorPaymentController::class, 'unpaidPurchases'])
        ->name('vendor_payments.unpaid');
});



Route::middleware(['auth'])->group(function () {

    Route::get('/fuel-sales-days', [FuelSalesDayController::class, 'index'])->name('fuel_sales_days.index');
    Route::get('/fuel-sales-days/create', [FuelSalesDayController::class, 'create'])->name('fuel_sales_days.create');
    Route::post('/fuel-sales-days', [FuelSalesDayController::class, 'store'])->name('fuel_sales_days.store');

    Route::get('/fuel-sales-days/{uuid}', [FuelSalesDayController::class, 'show'])->name('fuel_sales_days.show');

    Route::get('/fuel-sales-days/{uuid}/edit', [FuelSalesDayController::class, 'edit'])->name('fuel_sales_days.edit');
    Route::put('/fuel-sales-days/{uuid}', [FuelSalesDayController::class, 'update'])->name('fuel_sales_days.update');

    Route::post('/fuel-sales-days/{uuid}/submit', [FuelSalesDayController::class, 'submit'])->name('fuel_sales_days.submit');

    //ajax
    Route::get('/fuel-station/{station}/prices', [FuelSalesDayController::class, 'getFuelPrices']);
});