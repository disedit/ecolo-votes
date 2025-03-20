<?php

namespace App\Http\Controllers\Admin;

use App\Models\Type;
use Inertia\Inertia;
use App\Models\Group;
use Inertia\Response;
use App\Models\Edition;
use App\Models\Attendee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        if (!$attendee) {
            $response['type'] = 'FAIL';
            $response['message'] = 'Code not found: ' . $qrCode;
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

        if ($attendee->email) {
            $attendee->details->push([
                'field_key' => 'email',
                'field_label' => 'Email',
                'field_value' => $attendee->email,
            ]);
        }

        if ($attendee->phone) {
            $attendee->details->push([
                'field_key' => 'phone',
                'field_label' => 'Phone',
                'field_value' => $attendee->phone,
            ]);
        }

        return response()->json($attendee);
    }

    /**
     * Import credentials
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['file', 'mimes:txt,csv,xls,xlsx,ods', 'required', 'max:2048'],
            'wipe' => ['boolean']
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathName());
        $lines = $spreadsheet->getSheet(0)->toArray();

        // Validate rows
        $errors = $this->errors($lines);
        if (count($errors) > 0) {
            return response()->json(['errors' => ['lines' => $errors]], 422);
        }

        // Empty table if replacing data
        if ($request->input('wipe')) {
            Attendee::query()->delete();
        }

        // Create attendees
        $i = 0;
        foreach($lines as $line) {
            $i++;
            if($i === 1) continue;

            $attendee = new Attendee;
            $attendee->edition_id = Edition::current()->first()->id;
            $attendee->group_id = $this->group()->id;
            $attendee->type_id = $this->type($line[0], $line[5] ?? '')->id;
            $attendee->first_name = trim($line[1]);
            $attendee->last_name = trim($line[1]);
            $attendee->email = trim($line[3]);
            $attendee->phone = $this->clean($line[4]);
            $attendee->qr_code = Str::random(16);
            $attendee->token = Str::random(60);
            $attendee->save();
        }

        return response()->json(['imported' => true]);
    }

    /**
     * Validate import file
     */
    private function errors($lines): array
    {
        $errors = [];
        $i = 0;
        if (count($lines) < 2) $errors[] = 'File is empty';

        foreach($lines as $line) {
            $i++;
            if($i === 1) continue;
            if (!$line[0]) $errors[] = 'Row ' . $i . ' is missing a type';
            if (!$line[1]) $errors[] = 'Row ' . $i . ' is missing a first name';
            if (!$line[2]) $errors[] = 'Row ' . $i . ' is missing a last name';
            if ($line[3] && !filter_var($line[3], FILTER_VALIDATE_EMAIL)) $errors[] = 'Row ' . $i . ' has an invalid email (' . $line[3] . ')';
        }

        return $errors;
    }

    /**
     * Clean phone input
     */
    private function clean(string $input): string
    {
        $input = str_replace('+', '', $input);
        return trim($input);
    }

    /**
     * Get or create type
     */
    private function type(string $name, string $color): Type
    {
        $type = Type::where('name', $name)->first();

        if (!$type) {
            $type = new Type;
            $type->edition_id = Edition::current()->first()->id;
            $type->name = $name;
            $type->color = $this->color($color);
            $type->save();
        }

        return $type;
    }

    /**
     * Assign color
     */
    private function color(string $color): string
    {
        if ($color) return $color;

        $colors = collect(['green', 'purple', 'pink', 'yellow', 'orange', 'red', 'blue']);
        $usedColors = Type::all()->map(fn ($type) => $type->color);
        $unusedColors = $colors->filter(fn ($color) => !$usedColors->contains(fn ($colorName) => $colorName === $color));
        return $unusedColors->first();
    }

    /**
     * Get or create the attendees group
     */
    private function group(): Group
    {
        $group = Group::where('name', 'Attendees')->first();

        if (!$group) {
            $group = new Group;
            $group->name = 'Attendees';
            $group->is_codes = 0;
            $group->save();
        }

        return $group;
    }
}
