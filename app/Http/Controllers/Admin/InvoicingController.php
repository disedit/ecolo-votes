<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class InvoicingController extends Controller
{
    /**
     * Invoicing dashboard
     */
    public function invoicing(): Response
    {
        $payments = Payment::with([
            'attendee' => fn ($query) => $query->withTrashed()
                ->with(['user' => fn ($query) => $query->withTrashed()])])
            ->where('completed_checkout', 1)->get();
        return Inertia::render('Admin/Invoicing', [
            'payments' => $payments
        ]);
    }

    /**
     * Get a payment
     */
    public function payment(Payment $payment): JsonResponse
    {
        return response()->json($payment);
    }

    /**
     * Update a payment
     */
    public function update(Payment $payment, Request $request): JsonResponse
    {
        $payment->status = $request->input('status');
        $payment->receipt = $request->input('receipt');
        $payment->save();

        $attendee = $payment->attendee;
        $attendee->paid = ($payment->status === 'paid') ? 1 : 0;
        $attendee->save();

        return response()->json($payment);
    }

    /**
     * Delete a payment
     */
    public function delete(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json(['deleted' => true]);
    }
}
