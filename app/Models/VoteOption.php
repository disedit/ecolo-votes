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
     * Vote
     */
    public function vote(): BelongsTo {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Ballots
     */
    public function ballots(): HasMany {
        return $this->hasMany(VoteBallot::class);
    }
}
