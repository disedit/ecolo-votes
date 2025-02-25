<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Attendee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class TicketsController extends Controller
{
    /**
     * Invoicing dashboard
     */
    public function tickets(): Response
    {
        $tickets = Attendee::with([
            'type',
            'payments' => fn ($query) => $query->where('completed_checkout', 1),
            'user' => fn ($query) => $query->with('group')
        ])->get()->append('fees')->map(function ($row) {
            if ($row->subdelegates) {
                $subdelegates = collect($row->subdelegates)->map(function ($subdelegate) use ($row) {
                    $subdelegate->linked = Attendee::where('registered_by_user_id', $row->user->id)->exists();
                    return $subdelegate;
                });
            } else {
                $subdelegates = null;
            }

            return [
                'id' => $row->id,
                'attendee' => $row->user->fullName(),
                'role' => $row->type->only('name', 'color'),
                'organisation' => $row->user->group_other ?? $row->group->name,
                'fees' => $row->fees,
                'payments' => $row->payments,
                'paid' => $row->paid,
                'confirmed' => $row->confirmed,
                'notified' => $row->notified,
                'notifiable' => $row->notifiable,
                'ticket_issued' => $row->ticket_notified,
                'token' => $row->user->currentLoginToken()->token,
                'subdelegates' => $subdelegates,
                'registered_by' => ($row->registered_by_user_id) ? User::select('first_name', 'last_name')->find($row->registered_by_user_id) : null
            ];
        });

        return Inertia::render('Admin/Tickets', [
            'tickets' => $tickets
        ]);
    }
}
