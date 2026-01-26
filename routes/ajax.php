<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ajax\GeoController;

/*
|--------------------------------------------------------------------------
| Ajax Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth'])->group(function () {
    Route::prefix('ajax/geo')->group(function () {
        Route::get('/governorates/{region}', [GeoController::class, 'governorates']);
        Route::get('/centers/{governorate}', [GeoController::class, 'centers']);
        Route::get('/cities/{center}', [GeoController::class, 'cities']);
    });
});
