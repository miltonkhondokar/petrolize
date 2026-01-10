<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fuel\FuelCategoryController;

/*
|--------------------------------------------------------------------------
| Fuel Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {

    Route::patch('/fuel-status-update/{uuid}', [FuelCategoryController::class, 'fuelStatusUpdate'])->name('fuel-status-update');

    Route::resource('fuel', FuelCategoryController::class);
});
