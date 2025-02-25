<?php

namespace App\Models;

use App\Models\Attendee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendeeDetail extends Model
{
    use HasFactory;

    /**
     * Attendee
     */
    public function attendee(): BelongsTo {
        return $this->belongsTo(Attendee::class);
    }
}
