<?php

namespace App\Models;

use App\Models\Attendee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'access_log';
    
    /**
     * Log an entry
     */
    public static function log(Attendee $attendee, string $type, string $client): bool
    {
        $entry = new Self;
        $entry->attendee_id = $attendee->id;
        $entry->type = $type;
        $entry->client = $client;
        $entry->logged_by_user_id = (Auth::user()) ? Auth::user()->id : null;
        return $entry->save();
    }
}
