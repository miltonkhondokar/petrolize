<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\DemoRegistrationController;
use App\Http\Controllers\Web\FuelPurchaseWebController;
use App\Http\Controllers\Web\VendorPaymentWebController;
use App\Http\Controllers\Web\FuelSalesDayWebController;

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

// Vendor Management Routes
require __DIR__.'/application/vendor/vendor.php';

// Geo Location Management Routes
require __DIR__.'/application/geo-locations/geo_location.php';

//Fuel Station Fuel Type Management Routes
require __DIR__.'/application/fuel-station-fuel-type/fuel_station_fuel_type.php';




Route::middleware(['auth'])->group(function () {

    // Purchases (Web)
    Route::get('/fuel-purchases', [FuelPurchaseWebController::class, 'index'])->name('fuel_purchases.index');
    Route::get('/fuel-purchases/create', [FuelPurchaseWebController::class, 'create'])->name('fuel_purchases.create');
    Route::post('/fuel-purchases', [FuelPurchaseWebController::class, 'store'])->name('fuel_purchases.store');
    Route::get('/fuel-purchases/{uuid}', [FuelPurchaseWebController::class, 'show'])->name('fuel_purchases.show');
    Route::post('/fuel-purchases/{uuid}/receive', [FuelPurchaseWebController::class, 'receive'])->name('fuel_purchases.receive');


    Route::get('/fuel-purchases/{uuid}/edit', [FuelPurchaseWebController::class, 'edit'])->name('fuel_purchases.edit');
    Route::put('/fuel-purchases/{uuid}', [FuelPurchaseWebController::class, 'update'])->name('fuel_purchases.update');

    Route::get('/vendor-payments/{uuid}/edit', [VendorPaymentWebController::class, 'edit'])->name('vendor_payments.edit');
    Route::put('/vendor-payments/{uuid}', [VendorPaymentWebController::class, 'update'])->name('vendor_payments.update');


    // Vendor Payments (Web)
    Route::get('/vendor-payments', [VendorPaymentWebController::class, 'index'])->name('vendor_payments.index');
    Route::get('/vendor-payments/create', [VendorPaymentWebController::class, 'create'])->name('vendor_payments.create');
    Route::post('/vendor-payments', [VendorPaymentWebController::class, 'store'])->name('vendor_payments.store');
    Route::get('/vendor-payments/{uuid}', [VendorPaymentWebController::class, 'show'])->name('vendor_payments.show');
    Route::post('/vendor-payments/{uuid}/allocate', [VendorPaymentWebController::class, 'allocate'])->name('vendor_payments.allocate');

});



Route::middleware(['auth'])->group(function () {

    Route::get('/fuel-sales-days', [FuelSalesDayWebController::class, 'index'])->name('fuel_sales_days.index');
    Route::get('/fuel-sales-days/create', [FuelSalesDayWebController::class, 'create'])->name('fuel_sales_days.create');
    Route::post('/fuel-sales-days', [FuelSalesDayWebController::class, 'store'])->name('fuel_sales_days.store');

    Route::get('/fuel-sales-days/{uuid}', [FuelSalesDayWebController::class, 'show'])->name('fuel_sales_days.show');

    Route::get('/fuel-sales-days/{uuid}/edit', [FuelSalesDayWebController::class, 'edit'])->name('fuel_sales_days.edit');
    Route::put('/fuel-sales-days/{uuid}', [FuelSalesDayWebController::class, 'update'])->name('fuel_sales_days.update');

    Route::post('/fuel-sales-days/{uuid}/submit', [FuelSalesDayWebController::class, 'submit'])->name('fuel_sales_days.submit');
});