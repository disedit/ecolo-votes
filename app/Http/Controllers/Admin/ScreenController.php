<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Vote;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Edition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScreenController extends Controller
{
    /**
     * Screen
     */
    public function screen(): Response
    {
        /* Time-based password */
        $secret = config('google2fa.secret');
        $interval = config('google2fa.regenerate_every');
        $google2fa = app('pragmarx.google2fa');
        $google2fa->setOneTimePasswordLength(config('google2fa.password_length'));
        $google2fa->setKeyRegeneration($interval);
        $code = $google2fa->getCurrentOtp($secret);
        $timestamp = $google2fa->getTimestamp();
        $now = Carbon::now();
        $nextCode = Carbon::createFromTimestamp(($timestamp * $interval) + $interval);
        $nextAlert = (abs($nextCode->diffInSeconds($now)) <= 5);
        $resultsOnScreen = 60;
        $willHideIn = null;

        /* Vote */
        $vote = Vote::currentOrRecentlyClosed();

        if ($vote) {
            $vote->append('abridgedResults', 'recentlyClosed');

            if ($vote->recentlyClosed) {
                $willHideIn = $resultsOnScreen * 1000;
            }
        }

        return Inertia::render('Screen/Screen', [
            'edition' => Edition::current()->first()->only('title', 'dates', 'location'),
            'vote' => $vote,
            'code' => [
                'password' => $code,
                'nextAlert' => $nextAlert
            ],
            'willHideIn' => $willHideIn
        ]);
    }
}
