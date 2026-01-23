<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceData\Fuel\FuelStationFuelTypeController;

/*
|--------------------------------------------------------------------------
| Fuel Stations Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::resource('fuel-station-fuel-type', FuelStationFuelTypeController::class);
    Route::patch('fuel-station-fuel-type/{uuid}/status', [FuelStationFuelTypeController::class, 'statusUpdate'])
        ->name('fuel-station-fuel-type.status-update');
});
