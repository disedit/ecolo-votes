<?php

namespace App\Models;

use App\Models\Edition;
use App\Models\ExtraList;
use App\Models\Scopes\EditionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Extra extends Model
{
    use HasFactory;

    /**
     * Edition
     */
    public function edition(): BelongsTo {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Extra list
     */
    public function list(): HasMany {
        return $this->hasMany(ExtraList::class);
    }

    /**
     * Row
     */
    public function row($userId): ExtraList {
        return ExtraList::where('extra_id', $this->id)->where('user_id', $userId)->first();
    }

    /**
     * Is the extra full?
     */
    public function full(): bool {
        return $this->list->count() >= $this->capacity;
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
