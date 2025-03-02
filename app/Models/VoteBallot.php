<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoteBallot extends Model
{
    use HasFactory;

    protected $fillable = ['vote_id', 'code_id', 'votes', 'checked_in'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vote_option_ids' => 'object'
    ];

    /**
     * Vote
     */
    public function vote(): BelongsTo {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Code
     */
    public function code(): BelongsTo {
        return $this->belongsTo(Code::class);
    }

    /**
     * Option
     */
    public function option(): BelongsTo {
        return $this->belongsTo(VoteOption::class);
    }
}
