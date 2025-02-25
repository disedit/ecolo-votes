<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Welcome page
     */
    public function welcome(Request $request) {
        if (!$request->user()->hasPaid() && !$request->user()->isCheckedIn()) {
            return redirect()->route('cart');
        }

        if ($request->user()->canVote()) {
            return redirect()->route('vote');
        }

        if ($request->user()->isCheckedIn()) {
            return redirect()->route('info');
        }

        return redirect()->route('badge');
    }

    /**
     * Awaiting page
     */
    public function awaiting(Request $request): Response | RedirectResponse {
        if ($request->user()->isConfirmed()) {
            return redirect()->route('welcome');
        }

        return Inertia::render('Awaiting', [
            'user' => $request->user()
        ]);
    }

    /**
     * Info page
     */
    public function info(Request $request): Response {
        return Inertia::render('Info', [
            'attendee' => $request->user()->attendee()->only('votes'),
        ]);
    }
}
