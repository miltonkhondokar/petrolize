<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceData\Complaint\ComplaintCategoryController;

/*
|--------------------------------------------------------------------------
| Fuel Station Complaint Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('complaint-category-status-update/{uuid}', [ComplaintCategoryController::class, 'statusUpdate'])
        ->name('complaint-category-status-update');

    Route::resource('complaint-category', ComplaintCategoryController::class);
});
