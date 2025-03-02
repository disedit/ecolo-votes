<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoteOption extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Vote
     */
    public function vote(): BelongsTo {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Region
     */
    public function region(): BelongsTo {
        return $this->belongsTo(Region::class);
    }

    /**
     * Ballots
     */
    public function ballots(): HasMany {
        return $this->hasMany(VoteBallot::class);
    }
}
