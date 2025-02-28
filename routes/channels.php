<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('Attendee.Status.{id}', fn () => true);

Broadcast::channel('Attendees.List', function (User $user) {
    return $user->hasRole('credentials');
});

Broadcast::channel('Codes.List', function (User $user) {
    return $user->hasRole('credentials');
});

Broadcast::channel('Votes', function (User $user) {
    return $user->canVote() || $user->hasRole('vote_manager');
});
