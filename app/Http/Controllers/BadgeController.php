<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Extra;
use Inertia\Response;
use App\Models\ExtraList;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Badge
     */
    public function badge(Request $request): Response {
        $extras = Extra::all()->map(function ($item) use ($request) {
            $item->user = [
                'signed_up' => ExtraList::where('extra_id', $item->id)->where('user_id', $request->user()->id)->exists()
            ];
            $item->full = $item->full();
            return $item;
        });
        
        return Inertia::render('Badge', [
            'user' => $request->user()->load('group'),
            'attendee' => $request->user()->attendee()->load('type'),
            'extras' => $extras
        ]);
    }
}
