<?php

namespace App\Models;

use App\Models\User;
use App\Models\Extra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtraList extends Model
{
    use HasFactory;

    /**
     * Extra
     */
    public function extra(): BelongsTo {
        return $this->belongsTo(Extra::class);
    }

    /**
     * Extra
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
