<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceData\Vendor\VendorController;

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {

    Route::patch('vendor-status-update/{uuid}', [VendorController::class, 'vendorStatusUpdate'])->name('vendor-status-update');

    Route::resource('vendors', VendorController::class);
});
