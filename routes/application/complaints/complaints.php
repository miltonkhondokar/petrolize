<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\Complaint\ComplaintController;

/*
|--------------------------------------------------------------------------
| Fuel Station Complaint Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('complaints-status-update/{uuid}', [ComplaintController::class, 'statusUpdate'])
        ->name('complaints.status-update');

    Route::resource('complaints', ComplaintController::class);
});
