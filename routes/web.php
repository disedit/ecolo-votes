<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PassController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ExtrasController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\TicketsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoicingController;
use App\Http\Controllers\Admin\CredentialsController;
use App\Http\Controllers\Admin\VoteController as AdminVoteController;
use App\Http\Controllers\Admin\ExtrasController as AdminExtrasController;

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

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
    Route::get('/awaiting', [HomeController::class, 'awaiting'])->name('awaiting');
    Route::get('/cart/return', [CartController::class, 'return'])->name('return');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::get('/invoice/{payment}', [InvoiceController::class, 'view'])->name('invoice');

    Route::middleware('paid')->group(function() {
        Route::get('/badge', [BadgeController::class, 'badge'])->name('badge');
        Route::get('/pass/{attendee}/apple', [PassController::class, 'apple']);
        Route::get('/pass/{attendee}/google', [PassController::class, 'google']);
        Route::post('/extras/{extra}/add', [ExtrasController::class, 'add']);
        Route::post('/extras/{extra}/remove', [ExtrasController::class, 'remove']);
        Route::get('/votes', [VoteController::class, 'votes'])->name('votes');
    });

    Route::middleware('paying')->group(function() {
        Route::get('/cart', [CartController::class, 'cart'])->name('cart');
        Route::get('/cart/payment/{payment}', [CartController::class, 'payment'])->name('payment');
        Route::post('/cart/payment', [CartController::class, 'createPayment'])->name('create_payment');
        Route::post('/cart/link-delegate', [CartController::class, 'linkDelegate'])->name('link_delegate');
        Route::post('/cart/remove-delegate', [CartController::class, 'removeDelegate'])->name('remove_delegate');
    });

    Route::middleware(['paid', 'voter', 'checkedin'])->group(function() {
        Route::get('/vote', [VoteController::class, 'vote'])->name('vote');
    });

    Route::middleware(['paid', 'checkedin'])->group(function() {
        Route::get('/info', [HomeController::class, 'info'])->name('info');
    });

    Route::middleware('admin')->group(function () {
        
    });

    Route::middleware('admin')->group(function () {
        Route::get('/admin', [DashboardController::class, 'dashboard'])->name('admin_dashboard');
        Route::get('/admin/credentials', [CredentialsController::class, 'credentials'])->name('admin_credentials');
        Route::post('/admin/credentials/{attendee}/check_in', [CredentialsController::class, 'checkIn'])->name('admin_check_in');
        Route::post('/admin/credentials/{attendee}/check_out', [CredentialsController::class, 'checkOut'])->name('admin_check_out');
        Route::get('/admin/scanner', [CredentialsController::class, 'scanner'])->name('admin_scanner');
        Route::get('/admin/tickets', [TicketsController::class, 'tickets'])->name('admin_tickets');
        Route::get('/admin/invoicing', [InvoicingController::class, 'invoicing'])->name('admin_invoicing');
        Route::get('/admin/extras', [AdminExtrasController::class, 'extras'])->name('admin_extras');
        Route::get('/admin/votes', [AdminVoteController::class, 'votes'])->name('admin_votes');
        Route::post('/admin/votes/{vote}/reorder', [AdminVoteController::class, 'reorder'])->name('admin_votes_reorder');
        Route::get('/admin/voters', [AdminVoteController::class, 'voters'])->name('admin_voters');
        Route::get('/admin/screen', [ScreenController::class, 'screen'])->name('screen');
    });
});

require __DIR__.'/auth.php';
