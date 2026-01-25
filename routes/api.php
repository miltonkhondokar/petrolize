<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\Api\FuelSalesController;
use App\Http\Controllers\Api\CostEntryController;
use App\Http\Controllers\Api\ComplaintController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth (Public + Protected)
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {

        // Public
        Route::post('/login', [CustomAuthController::class, 'apiLogin']);
        Route::post('/refresh', [CustomAuthController::class, 'apiRefreshToken']);
        Route::get('/check', [CustomAuthController::class, 'apiCheckAuth']);

        // Protected
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [CustomAuthController::class, 'apiLogout']);
            Route::get('/user', [CustomAuthController::class, 'apiUser']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Protected APIs (Bearer token required)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {

        // -------------------------
        // Fuel Sales Days (Mobile)
        // -------------------------
        Route::get('/fuel-sales-days', [FuelSalesController::class, 'index']);
        Route::post('/fuel-sales-days', [FuelSalesController::class, 'store']);
        Route::get('/fuel-sales-days/{uuid}', [FuelSalesController::class, 'show']);
        Route::match(['put', 'patch'], '/fuel-sales-days/{uuid}', [FuelSalesController::class, 'update']);
        Route::post('/fuel-sales-days/{uuid}/submit', [FuelSalesController::class, 'submit']);

        // Station fuel prices
        Route::get('/fuel-stations/{uuid}/fuel-prices', [FuelSalesController::class, 'stationFuelPrices']);

        // -------------------------
        // Cost Entries (Mobile)
        // -------------------------
        Route::get('/cost-entries', [CostEntryController::class, 'index']);
        Route::post('/cost-entries', [CostEntryController::class, 'store']);
        Route::get('/cost-entries/{uuid}', [CostEntryController::class, 'show']);
        Route::match(['put', 'patch'], '/cost-entries/{uuid}', [CostEntryController::class, 'update']);
        Route::delete('/cost-entries/{uuid}', [CostEntryController::class, 'destroy']);

        // -------------------------
        // Complaints (Mobile)
        // -------------------------
        Route::get('/complaints', [ComplaintController::class, 'index']);
        Route::post('/complaints', [ComplaintController::class, 'store']);
        Route::get('/complaints/{uuid}', [ComplaintController::class, 'show']);
        Route::match(['put', 'patch'], '/complaints/{uuid}', [ComplaintController::class, 'update']);
        Route::delete('/complaints/{uuid}', [ComplaintController::class, 'destroy']);
        Route::post('/complaints/{uuid}/status', [ComplaintController::class, 'statusUpdate']);
    });
});
