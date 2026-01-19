<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\FuelStation;

// All routes inside this group require a Bearer Token
Route::middleware('auth:api')->group(function () {

    // 1. Test Route
    Route::get('/passport-test', function (Request $request) {
        return response()->json([
            'user_id' => $request->user()->id,
            'email'   => $request->user()->email,
        ]);
    });

    // 2. Fuel Station Route
    Route::get('/fuel-stations/{uuid}', function ($uuid) {
        $station = FuelStation::where('uuid', $uuid)->first();

        if (!$station) {
            return response()->json(['message' => 'Fuel Station not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $station
        ]);
    });
});
