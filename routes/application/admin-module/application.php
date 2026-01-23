<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\Command\OptimizeController;
use App\Http\Controllers\ReferenceData\User\UserMasterDataController;
use App\Http\Controllers\App\Dashboard\DashboardController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->to(route('dashboard'))
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [CustomAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CustomAuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'auth.check'])->group(function () {

    //dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('/');


    //fetch all users
    Route::get('user-master-data', [UserMasterDataController::class, 'index'])->name('user-master-data');

    //create user
    Route::get('user-master-data-create', [UserMasterDataController::class, 'create'])->name('user-master-data-create');
    Route::post('user-master-data-store', [UserMasterDataController::class, 'store'])->name('user-master-data-store');


    // Your existing routes
    Route::post('logout', [CustomAuthController::class, 'logout'])->name('logout');

    Route::get('optimize-clear', [OptimizeController::class, 'clear'])->name('optimize-clear');
});
