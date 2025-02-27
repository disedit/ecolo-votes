<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Group;
use App\Models\Attendee;
use App\Models\LoginToken;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Attendees
     */
    public function attendees(): HasMany {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Attendee
     */
    public function attendee(): ?Attendee {
        return Attendee::where('user_id', $this->id)->first();
    }

    /**
     * Code
     */
    public function code(): ?Code {
        return Code::where('user_id', $this->id)->first();
    }

    /**
     * Current login Token
     */
    public function currentLoginToken(): LoginToken {
        $loginToken = LoginToken::where('user_id', $this->id)->where('expires_at', '>', now())->orderBy('id','desc')->first();
        if (!$loginToken) $loginToken = LoginToken::create(user: $this);

        return $loginToken;
    }

    /**
     * Login Tokens
     */
    public function tokens(): HasMany {
        return $this->hasMany(LoginToken::class);
    }

    /**
     * Group
     */
    public function group(): BelongsTo {
        return $this->belongsTo(Group::class);
    }

    /**
     * Is QR code
     */
    public function isCode(): bool {
        return !!$this->code();
    }

    /**
     * Can vote
     */
    public function canVote(): bool {
        $code = $this->code();
        return $code && $code->wasPickedUp();
    }

    /**
     * Is checked in
     */
    public function isCheckedIn(): bool {
        $attendee = $this->attendee();
        return $attendee && $attendee->hasCheckedIn();
    }

    /**
     * Check role
     */
    public function hasRole(string $role): bool {
        if ($this->role === 'admin') return true;
        return $this->role === $role;
    }

    /**
     * Has any admin role
     */
    public function hasAdminRole(): bool {
        return $this->role !== 'user';
    }

    /**
     * Full name
     */
    public function fullName(): string {
        return $this->first_name . ' ' . $this->last_name;
    }
}
