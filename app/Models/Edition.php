<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Attendee;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Edition extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'links' => 'object'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mappings',
    ];

    /**
     * Attendees
     */
    public function attendees(): HasMany {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Types
     */
    public function types(): HasMany {
        return $this->hasMany(Type::class);
    }

    /**
     * Scope a query to only include the current edition
     */
    public function scopeCurrent($query)
    {
        return $query->where('current', '=', 1);
    }

    /**
     * Sets an edition as current
     */
    public function setCurrent()
    {
        DB::table('editions')->where('current', 1)->update(['current' => 0]);

        $this->current = 1;
        return $this->save();
    }
}
