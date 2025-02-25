<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Attendee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fee extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'member_parties' => 'object'
    ];

    /**
     * Type
     */
    public function type(): BelongsTo {
        return $this->belongsTo(Type::class);
    }

    /**
     * Attendees
     */
    public function attendees(): HasMany {
        return $this->hasMany(Attendee::class);
    }
}
