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

    // Keep resource but EXCLUDE edit/update (we will custom them)
    Route::resource('fuel-unit-price', FuelUnitPriceController::class)->except(['edit', 'update']);

    // Custom edit/update by STATION UUID (bulk)
    Route::get('fuel-unit-price/{stationUuid}/edit', [FuelUnitPriceController::class, 'edit'])
        ->name('fuel-unit-price.edit');

    Route::put('fuel-unit-price/{stationUuid}', [FuelUnitPriceController::class, 'update'])
        ->name('fuel-unit-price.update');


    Route::get('fuel-unit-price/{stationUuid}/show', [FuelUnitPriceController::class, 'stationShow'])
        ->name('fuel-unit-price.station-show');


    // ajax route
    Route::get('fuel-unit-price/station/{stationUuid}/fuel-types', [FuelUnitPriceController::class, 'stationFuelTypes'])
        ->name('fuel-unit-price.station-fuel-types');
});
