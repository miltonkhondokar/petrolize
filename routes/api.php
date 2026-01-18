<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/passport-test', function (Request $request) {
    return response()->json([
        'id' => $request->user()->id,
        'email' => $request->user()->email,
    ]);
});
