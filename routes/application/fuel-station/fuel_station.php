<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceData\Fuel\FuelStationController;

/*
|--------------------------------------------------------------------------
| Fuel Stations Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('fuel-station-status-update/{uuid}', [FuelStationController::class, 'fuelStationStatusUpdate'])
        ->name('fuel-station-status-update');

    Route::resource('fuel-station', FuelStationController::class);
});
