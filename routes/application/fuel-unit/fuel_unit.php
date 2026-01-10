<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fuel\FuelUnitController;

/*
|--------------------------------------------------------------------------
| Fuel Unit Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('fuel-unit-status-update/{uuid}', [FuelUnitController::class, 'fuelUnitStatusUpdate'])
        ->name('fuel-unit-status-update');

    Route::resource('fuel-unit', FuelUnitController::class);
});
