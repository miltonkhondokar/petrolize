<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fuel\FuelUnitPriceController;

/*
|--------------------------------------------------------------------------
| Fuel Unit Price Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('fuel-unit-price-status-update/{uuid}', [FuelUnitPriceController::class, 'fuelUnitStatusUpdate'])
        ->name('fuel-unit-price-status-update');

    Route::resource('fuel-unit-price', FuelUnitPriceController::class);
});
