<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\Admin\CodeController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CredentialsController;
use App\Http\Controllers\Admin\VoteController as AdminVoteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/badge/{token}', [BadgeController::class, 'badge'])->name('badge');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

    Route::middleware(['voter'])->group(function() {
        Route::get('/vote', [VoteController::class, 'vote'])->name('vote');
        Route::get('/votes', [VoteController::class, 'votes'])->name('votes');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/admin', [DashboardController::class, 'dashboard'])->name('admin_dashboard');
        Route::get('/admin/credentials', [CredentialsController::class, 'credentials'])->name('admin_credentials');
        Route::post('/admin/credentials/{attendee}/check_in', [CredentialsController::class, 'checkIn'])->name('admin_check_in');
        Route::post('/admin/credentials/{attendee}/check_out', [CredentialsController::class, 'checkOut'])->name('admin_check_out');
        Route::get('/admin/scanner', [CredentialsController::class, 'scanner'])->name('admin_scanner');
        Route::get('/admin/codes', [CodeController::class, 'codes'])->name('admin_codes');
        Route::post('/admin/codes/{code}/pickup', [CodeController::class, 'pickUp'])->name('admin_code_pickup');
        Route::post('/admin/codes/{code}/leavedown', [CodeController::class, 'leaveDown'])->name('admin_code_leavedown');
        Route::get('/admin/votes', [AdminVoteController::class, 'votes'])->name('admin_votes');
        Route::post('/admin/votes/{vote}/reorder', [AdminVoteController::class, 'reorder'])->name('admin_votes_reorder');
        Route::get('/admin/screen', [ScreenController::class, 'screen'])->name('screen');
    });
});

require __DIR__.'/auth.php';
