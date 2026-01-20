<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Models\FuelStation;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication APIs
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {

        // Public (no token required)
        Route::post('/login', [CustomAuthController::class, 'apiLogin']);
        Route::get('/check', [CustomAuthController::class, 'apiCheckAuth']);

        // Protected (Bearer token required)
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [CustomAuthController::class, 'apiLogout']);
            Route::get('/user', [CustomAuthController::class, 'apiUser']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Resources
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {

        Route::get('/fuel-stations/{uuid}', function ($uuid) {
            $station = FuelStation::where('uuid', $uuid)->first();

            if (!$station) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuel Station not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $station
            ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Passport OAuth Routes
|--------------------------------------------------------------------------
| Used ONLY for refresh tokens and password grant
*/
Route::prefix('oauth')->group(function () {
    Route::post('/token', [
        \Laravel\Passport\Http\Controllers\AccessTokenController::class,
        'issueToken'
    ]);
});
