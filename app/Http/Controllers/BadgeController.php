<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Attendee;

class BadgeController extends Controller
{
    /**
     * Badge
     */
    public function badge(string $token): Response {

        $attendee = Attendee::where('token', $token)->first();

        if (!$attendee) {
            return abort(403, 'Invalid badge token');
        }

        return Inertia::render('Badge', [
            'attendee' => $attendee->load(['type', 'group']),
        ]);
    }
}
