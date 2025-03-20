<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\Admin\CodeController;
use App\Http\Controllers\Admin\CredentialsController;
use App\Http\Controllers\Admin\VoteController as AdminVoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/votes/{vote}/results', [VoteController::class, 'results']);

    Route::middleware('admin')->group(function () {
        Route::get('/credential/{attendee}', [CredentialsController::class, 'credential']);
        Route::post('/credentials/scan', [CredentialsController::class, 'scan']);
        Route::post('/codes/scan', [CodeController::class, 'scan']);
        Route::post('/admin/credentials/import', [CredentialsController::class, 'import']);
        Route::post('/admin/codes/create', [CodeController::class, 'create']);
        Route::post('/admin/votes/create', [AdminVoteController::class, 'create']);
        Route::get('/admin/votes/{vote}', [AdminVoteController::class, 'vote']);
        Route::get('/admin/votes_to_import', [AdminVoteController::class, 'votesToImport']);
        Route::post('/admin/votes/{vote}/open', [AdminVoteController::class, 'open']);
        Route::post('/admin/votes/{vote}/debate', [AdminVoteController::class, 'openDebate']);
        Route::post('/admin/votes/{vote}/debate/close', [AdminVoteController::class, 'closeDebate']);
        Route::post('/admin/votes/{vote}/close', [AdminVoteController::class, 'close']);
        Route::post('/admin/votes/{vote}/delete', [AdminVoteController::class, 'delete']);
    });

    Route::middleware(['voter'])->group(function() {
        Route::post('/vote/cast', [VoteController::class, 'cast'])->name('vote_cast');
    });
});
