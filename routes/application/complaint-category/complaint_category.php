<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complaint\ComplaintController;

/*
|--------------------------------------------------------------------------
| Pump Complaint Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('complaint-category-status-update/{uuid}', [ComplaintController::class, 'statusUpdate'])
        ->name('complaint-category.status-update');

    Route::resource('complaint-category', ComplaintController::class);
});
