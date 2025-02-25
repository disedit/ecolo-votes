<?php

namespace App\Http\Middleware;

use App\Models\Edition;
use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $edition = Edition::select('title', 'short_title', 'date_start', 'date_end', 'location', 'dates', 'cover', 'venue_name', 'venue_address', 'venue_link', 'links')->current()->first();

        return [
            ...parent::share($request),
            'edition' => $edition,
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'message' => fn () => $request->session()->get('message')
            ],
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
