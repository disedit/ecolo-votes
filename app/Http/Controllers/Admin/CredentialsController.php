<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class CredentialsController extends Controller
{
    /**
     * Credentials dashboard
     */
    public function credentials(): Response
    {
        $attendees = Attendee::with(['group', 'type'])->get();

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
    public function checkIn(Attendee $attendee): RedirectResponse
    {
        $attendee->checkIn('LOOKUP');

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
            'first_name' => $attendee->first_name,
            'last_name' => $attendee->last_name,
            'group' => $attendee->group->name,
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
            $response['type'] = 'FAIL';
            $response['message'] = 'Already picked up code';
            return response()->json($response, 422);
        }

        // Check attendee in
        $attendee->checkIn($client);
        $response['type'] = 'OK';
        $response['message'] = 'Checked in';

        return response()->json($response);
    }

    /**
     * Get full attendee details and log
     */
    public function credential(Attendee $attendee): JsonResponse
    {
        $attendee->load(['details', 'accessLog']);
        return response()->json($attendee);
    }
}
