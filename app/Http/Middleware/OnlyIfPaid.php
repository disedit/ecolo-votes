<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyIfPaid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $user->syncPaymentStatus();

        if (!$user->isConfirmed()) {
            return redirect()->route('awaiting');
        }

        if (!$user->hasPaid() && !$user->isCheckedIn()) {
            return redirect()->route('cart')->with('message', 'You need to purchase a ticket first.');
        }

        return $next($request);
    }
}
