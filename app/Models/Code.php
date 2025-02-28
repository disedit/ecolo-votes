<?php

namespace App\Models;

use App\Models\Scopes\EditionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Code extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Edition
     */
    public function edition(): BelongsTo {
        return $this->belongsTo(Edition::class);
    }

    /**
     * User
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if code is enabled
     */
    public function wasPickedUp(): bool {
        return !!$this->pickedup_at;
    }

    /**
     * Pick up and activate a code
     */
    public function pickUp() {
        $this->pickedup_at = now();
        $this->save();
    }

    /**
     * Deactivate a code
     */
    public function leaveDown() {
        $this->pickedup_at = null;
        $this->save();
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
