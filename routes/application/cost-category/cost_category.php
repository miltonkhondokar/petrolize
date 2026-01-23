<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceData\Cost\CostCategoryController;

/*
|--------------------------------------------------------------------------
| Cost Category Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('cost-category-status-update/{uuid}', [CostCategoryController::class, 'statusUpdate'])
        ->name('cost-category.status-update');

    Route::resource('cost-category', CostCategoryController::class);
});
