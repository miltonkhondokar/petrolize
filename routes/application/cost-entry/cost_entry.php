<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\Cost\CostEntryController;

/*
|--------------------------------------------------------------------------
| Cost Entry Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'auth.check'])->group(function () {
    Route::patch('cost-entries-status-update/{uuid}', [CostEntryController::class, 'statusUpdate'])
        ->name('cost-entries.status-update');

    Route::resource('cost-entries', CostEntryController::class);
});
