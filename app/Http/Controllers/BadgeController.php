<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Badge
     */
    public function badge(Request $request): Response {
        return Inertia::render('Badge', [
            'user' => $request->user()->load('group'),
            'attendee' => $request->user()->attendee()->load('type'),
        ]);
    }
}
