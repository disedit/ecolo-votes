<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyIfPaying
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

        if ($user->hasPaid()) {
            return redirect()->route('badge');
        }

        $openPayments = $user->openPayments();
        if (count($openPayments) > 0) {
            return redirect()->route('return', ['session_id' => $openPayments[0]->checkout_session_id]);
        }

        if (!$user->hasFees()) {
            return redirect()->route('awaiting');
        }

        return $next($request);
    }
}
