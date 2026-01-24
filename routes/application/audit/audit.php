<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\System\Audit\UserAuditController;

/*
|--------------------------------------------------------------------------
| Audit Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your audit log panel. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'auth.check'])->prefix('audit')->name('audit.')->group(function () {
    Route::resource('user', UserAuditController::class);
});
