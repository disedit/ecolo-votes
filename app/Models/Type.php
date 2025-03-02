<?php

namespace App\Models;

use App\Models\Fee;
use App\Models\Attendee;
use App\Models\Scopes\EditionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * Fees
     */
    public function fees(): HasMany {
        return $this->hasMany(Fee::class);
    }

    /**
     * Attendees
     */
    public function attendees(): HasMany {
        return $this->hasMany(Attendee::class);
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new EditionScope);
    }
}
