<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/auto/{token}', [AuthenticatedSessionController::class, 'autologin']);

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('code', [AuthenticatedSessionController::class, 'create_code'])->name('code_login');
    Route::post('code', [AuthenticatedSessionController::class, 'store_code']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
