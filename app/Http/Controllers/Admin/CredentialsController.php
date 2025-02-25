<?php

namespace App\Http\Controllers\Admin;

use App\Models\Fee;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Payment;
use App\Models\Attendee;
use Illuminate\Http\Request;
use App\Notifications\CheckedIn;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Notifications\PaidTicketIssued;
use Illuminate\Support\Facades\Artisan;

class CredentialsController extends Controller
{
    /**
     * Credentials dashboard
     */
    public function credentials(): Response
    {
        $attendees = Attendee::with([
            'user' => function ($query) { $query->with('group'); },
            'type'
        ])->get();

        return Inertia::render('Admin/Credentials', [
            'attendees' => $attendees
        ]);
    }

    /**
     * Scanner only
     */
    public function scanner(): Response
    {
        return Inertia::render('Admin/Scanner');
    }

    /**
     * Check in
     */
    public function checkIn(Attendee $attendee, Request $request): RedirectResponse
    {
        $firstCheckIn = !$attendee->first_checked_in;
        $attendee->checkIn('LOOKUP');

        if (!$request->input('silent') && $attendee->isVoter() && $firstCheckIn) {
            $attendee->user->notify(new CheckedIn);
        }

        return to_route('admin_credentials');
    }

    /**
     * Check in
     */
    public function checkOut(Attendee $attendee): RedirectResponse
    {
        $attendee->checkOut('LOOKUP');
        return to_route('admin_credentials');
    }

    /**
     * Scan and update check in status
     */
    public function scan(Request $request): JsonResponse
    {
        $qrCodeInput = $request->input('qr_code');
        $qrCodeParts = explode(' ', $qrCodeInput);
        $qrCode = $qrCodeParts[0];
        $client = $qrCodeParts[1] ?? 'APP';
        $mode = $request->input('mode');
        $response = [
            'type' => '',
            'message' => '',
            'attendee' => null
        ];

        $attendee = Attendee::where('qr_code', strval($qrCode))->first();
        $firstCheckIn = !$attendee->first_checked_in;

        if (!$attendee) {
            $response['type'] = 'FAIL';
            $response['message'] = 'Code not found';
            return response()->json($response, 422);
        }

        $response['attendee'] = [
            'id' => $attendee->id,
            'first_name' => $attendee->user->first_name,
            'last_name' => $attendee->user->last_name,
            'group' => $attendee->user->group->name,
            'type' => $attendee->type->name,
            'color' => $attendee->type->color
        ];

        // If undoing check in
        if ($mode === 'OUT') {
            $attendee->checkOut($client);
            $response['type'] = 'OK';
            $response['message'] = 'Checked out';
            return response()->json($response);
        }

        // If ticket has date contraints
        if (
            ($attendee->type->valid_from && $attendee->type->valid_from->greaterThan(now()))
            || ($attendee->type->valid_until && $attendee->type->valid_until->lessThan(now()))
        ) {
            $response['type'] = 'WARNING';
            $response['message'] = 'Ticket is not valid on this date';
            return response()->json($response, 422);
        }

        // If attendee had checked in
        if ($attendee->checked_in !== null) {
            $response['type'] = 'DOUBLE';
            $response['message'] = 'Already checked in';
            return response()->json($response, 422);
        }

        // If attendee has not paid
        if (!$attendee->paid) {
            $response['type'] = 'WARNING';
            $response['message'] = 'Payment pending';
            return response()->json($response, 422);
        }

        // If attendee is not confirmed
        if (!$attendee->paid || !$attendee->confirmed) {
            $response['type'] = 'WARNING';
            $response['message'] = 'Not confirmed or not paid';
            return response()->json($response, 422);
        }

        // Check attendee in
        $attendee->checkIn($client);
        $response['type'] = 'OK';

        if (in_array($client, ['APPLEPASS', 'GOOGLEPASS']) && $attendee->isVoter() && $firstCheckIn) {
            $attendee->user->notify(new CheckedIn);
            $response['message'] = 'Checked in and notified';
        } else {
            $response['message'] = 'Checked in';
        }

        return response()->json($response);
    }

    /**
     * Get full attendee details and log
     */
    public function credential(Attendee $attendee): JsonResponse
    {
        $attendee->load(['details', 'accessLog', 'user']);
        $attendee->loginToken = $attendee->user->currentLoginToken();
        $attendee->fees = $attendee->fees();

        return response()->json($attendee);
    }

    /**
     * Mark attendee as paid
     */
    public function pay(Attendee $attendee, Request $request): JsonResponse
    {
        $fee = $request->input('fee');
        $pending = $request->input('pending');
        $attendee->paid = 1;

        $payment = new Payment;
        $payment->attendee_id = $attendee->id;
        $payment->receipt = [
            'cart' => [[
                'id' => $fee['id'],
                'name' => $fee['name'],
                'amount' => $fee['amount'],
                'description' => $attendee->user->first_name . ' ' . $attendee->user->last_name
            ]],
            'invoice' => [
                'vat' => '',
                'name' => $attendee->user->first_name . ' ' . $attendee->user->last_name,
                'type' => 'myself',
                'address' => ''
            ]
        ];
        $payment->amount = $fee['amount'];
        $payment->status = ($pending) ? 'pending' : 'paid';
        $payment->completed_checkout = 1;
        $payment->save();

        if($request->input('notify')) {
            $attendee->ticket_notified = 1;
            $attendee->user->notify(new PaidTicketIssued($payment));
        }

        $attendee->save();

        return response()->json($payment);
    }

    public function sync()
    {
        Artisan::call('attendees:sync');
        return response()->json(['synced' => true]);
    }
}
