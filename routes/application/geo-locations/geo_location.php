<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceData\GeoLocation\RegionController;
use App\Http\Controllers\ReferenceData\GeoLocation\GovernorateController;
use App\Http\Controllers\ReferenceData\GeoLocation\CityController;
use App\Http\Controllers\ReferenceData\GeoLocation\CenterController;

/*
|--------------------------------------------------------------------------
| Region Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware(['auth', 'auth.check'])->group(function () {

    Route::patch('region-status-update/{uuid}', [RegionController::class, 'regionStatusUpdate'])->name('region-status-update');

    Route::resource('regions', RegionController::class);
});


/*
|--------------------------------------------------------------------------
| Governorate Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware(['auth', 'auth.check'])->group(function () {

    Route::patch('governorate-status-update/{uuid}', [GovernorateController::class, 'governorateStatusUpdate'])->name('governorate-status-update');

    Route::resource('governorates', GovernorateController::class);
});


/*
|--------------------------------------------------------------------------
| City Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware(['auth', 'auth.check'])->group(function () {

    Route::patch('city-status-update/{uuid}', [CityController::class, 'cityStatusUpdate'])->name('city-status-update');

    Route::resource('cities', CityController::class);
});


/*
|--------------------------------------------------------------------------
| Center Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware(['auth', 'auth.check'])->group(function () {

    Route::patch('center-status-update/{uuid}', [CenterController::class, 'centerStatusUpdate'])->name('center-status-update');

    Route::resource('centers', CenterController::class);
});
