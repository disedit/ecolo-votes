<?php

namespace App\Http\Controllers\Admin;

use App\Models\Code;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Group;
use Inertia\Response;
use App\Models\Edition;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class CodeController extends Controller
{
    /**
     * Codes dashboard
     */
    public function codes(): Response
    {
        $codes = Code::all();

        return Inertia::render('Admin/Codes', [
            'codes' => $codes
        ]);
    }

    /**
     * Pickup and activate a code
     */
    public function pickUp(Code $code): RedirectResponse
    {
        $code->pickup();

        return to_route('admin_codes');
    }

    /**
     * Check in
     */
    public function leaveDown(Code $code): RedirectResponse
    {
        $code->leaveDown();

        return to_route('admin_codes');
    }

    /**
     * Scan and update check in status
     */
    public function scan(Request $request): JsonResponse
    {
        $qrCode = $request->input('qr_code');
        $mode = $request->input('mode');
        $response = [
            'type' => '',
            'message' => '',
            'attendee' => null
        ];

        $code = Code::where('qr_code', strval($qrCode))->first();

        if (!$code) {
            $response['type'] = 'FAIL';
            $response['message'] = 'Code not found';
            return response()->json($response, 422);
        }

        $response['code'] = [
            'id' => $code->id,
            'code' => $code->code
        ];

        // If deactivating
        if ($mode === 'OUT') {
            $code->leaveDown();
            $response['type'] = 'OK';
            $response['message'] = 'Code deactivated';
            return response()->json($response);
        }

        // If attendee had checked in
        if ($code->pickedup_dat !== null) {
            $response['type'] = 'FAIL';
            $response['message'] = 'Code previously activated at ' . $code->pickedup_at;
            return response()->json($response, 422);
        }

        // Pickup code
        $code->pickup();
        $response['type'] = 'OK';
        $response['message'] = 'Code activated';

        return response()->json($response);
    }

    /**
     * Generate codes
     */
    public function create(Request $request): JsonResponse {
        $request->validate([
            'amount' => 'required',
        ]);

        $amount = $request->input('amount');
        $group = Group::where('is_codes', 1)->firstOrFail();
        $password = Hash::make(Str::random(40));

        for($i = 0; $i <= $amount; $i++) {
            $codeString = Str::random(12);

            // Generate a user for the code
            $user = new User;
            $user->group_id = $group->id;
            $user->first_name = $codeString;
            $user->last_name = 'Code';
            $user->email = $codeString . '@codes.ecolo.be';
            $user->password = $password;
            $user->role = 'code';
            $user->save();

            // Generate code entries
            $code = new Code;
            $code->edition_id = Edition::current()->first()->id;
            $code->user_id = $user->id;
            $code->code = $codeString;
            $code->save();
        }

        return response()->json(['message' => 'Codes generated']);
    }
}
