<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginToken extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * User
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new login token
     */
    public static function create(User $user): Self {
        $loginToken = new Self;
        $loginToken->user_id = $user->id;
        $loginToken->token = Str::random(60);
        $loginToken->expires_at = now()->addDays(30);
        $loginToken->save();

        return $loginToken;
    }
}
