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
        $base = url('/code');
        $qrCode = str_replace($base . '/', "", $request->input('qr_code'));
        $mode = $request->input('mode');
        $response = [
            'type' => '',
            'message' => '',
            'attendee' => null
        ];

        $code = Code::where('code', strval($qrCode))->first();

        if (!$code) {
            $response['type'] = 'FAIL';
            $response['message'] = 'Code not found: ' . $qrCode;
            return response()->json($response, 422);
        }

        $response['code'] = [
            'id' => $code->id,
            'code' => $code->code
        ];

        // If deactivating
        if ($mode === 'OUT') {
            $code->leaveDown();
            $response['type'] = 'WARNING';
            $response['message'] = 'Code deactivated: ' . $qrCode;
            return response()->json($response);
        }

        // If attendee had checked in
        if ($code->pickedup_at !== null) {
            $response['type'] = 'DOUBLE';
            $response['message'] = 'Code previously activated at ' . $code->pickedup_at;
            return response()->json($response, 422);
        }

        // Pickup code
        $code->pickup();
        $response['type'] = 'OK';
        $response['message'] = 'Code activated: ' . $qrCode;

        return response()->json($response);
    }

    /**
     * Generate codes
     */
    public function create(Request $request): JsonResponse {
        $request->validate([
            'amount' => 'required',
            'preactivate' => 'boolean',
            'visually_impaired' => 'boolean'
        ]);

        $amount = $request->input('amount');
        $preactivate = $request->input('preactivate');
        $visuallyImpaired = $request->input('visually_impaired');
        $group = Group::where('is_codes', 1)->firstOrFail();
        $password = Hash::make(Str::random(40));

        for($i = 0; $i < $amount; $i++) {
            $codeString = Str::random(12);
            if ($visuallyImpaired) $codeString = 'E-' . $codeString;

            // Check if code already exists
            $code = Code::withoutGlobalScopes()->where('code', $codeString)->first();
            if ($code) {
                $i--;
                continue;
            }

            // Generate a user for the code
            $user = new User;
            $user->group_id = $group->id;
            $user->first_name = $codeString;
            $user->last_name = 'Code';
            $user->email = $codeString . '@codes.ecolo.be';
            $user->password = $password;
            $user->role = 'code';
            $user->code_exception = ($visuallyImpaired) ? 1 : 0;
            $user->save();

            // Generate code entries
            $code = new Code;
            $code->edition_id = Edition::current()->first()->id;
            $code->user_id = $user->id;
            $code->code = $codeString;
            $code->pickedup_at = ($preactivate) ? now() : null;
            $code->save();
        }

        return response()->json(['message' => 'Codes generated']);
    }

    /**
     * Print codes
     */
    public function print(): Response {
        $codes = Code::all();

        return Inertia::render('Admin/CodesPrint', [
            'codes' => $codes
        ]);
    }
}
