<?php

namespace App\Models;

use App\Models\Attendee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'receipt' => 'object'
    ];

    /**
     * Attendee
     */
    public function attendee(): BelongsTo {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * Edition
     */
    public function edition(): BelongsTo {
        return $this->attendee->edition();
    }

    /**
     * User
     */
    public function user(): BelongsTo {
        return $this->attendee->user();
    }
}
