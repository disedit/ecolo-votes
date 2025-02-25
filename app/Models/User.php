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
    public function attendee(): Attendee {
        return Attendee::where('user_id', $this->id)->first();
    }

    /**
     * Code
     */
    public function code(): Attendee {
        return Code::where('user_id', $this->id)->first();
    }

    /**
     * Payments
     */
    public function payments(): HasManyThrough {
        return $this->hasManyThrough(Payment::class, Attendee::class);
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
     * Return true if user has paid for current edition
     */
    public function hasPaid(): bool {
        return $this->attendee() && !!$this->attendee()->paid;
    }

    /**
     * Check if a user has been confirmed for current edition
     */
    public function isConfirmed(): bool {
        return !!$this->attendee()->confirmed;
    }

    /**
     * Can vote
     */
    public function canVote(): bool {
        $attendee = $this->attendee();
        return $attendee->isVoter() && $attendee->hasCheckedIn();
    }

    /**
     * Is checked in
     */
    public function isCheckedIn(): bool {
        $attendee = $this->attendee();
        return $attendee->hasCheckedIn();
    }

    /**
     * Check if user has a payment beings processed
     */
    public function openPayments(): Collection {
        $payments = $this->attendee()->payments()->where('completed_checkout', 1)->whereNot('status', 'paid')->whereNot('status', 'declined')->whereNot('status', 'refunded')->get();
        return $payments;
    }

    public function syncPaymentStatus() {
        $user = ($this->attendee()->registered_by_user_id) ? User::find($this->attendee()->registered_by_user_id) : $this;
        $hasValidPayment = $user->attendee()->payments()
            ->where(function ($query) {
                $query->where('status', 'paid')
                    ->orWhere('status', 'pending');
            })
            ->where('completed_checkout', 1)
            ->exists();

        if (($hasValidPayment && !$this->hasPaid()) || (!$this->hasFees() && !$this->hasPaid())) {
          $this->attendee()->update([
            'paid' => 1
          ]);
        }
  
        if (!$hasValidPayment && $this->hasPaid() && $this->hasFees()) {
          $this->attendee()->update([
            'paid' => 0
          ]);
        }
    }

    /**
     * Fees
     */
    public function fees($prepend = null): array {
        return $this->attendee()->fees($prepend);
    }

    /**
     * Has fees
     */
    public function hasFees(): bool {
        return count($this->fees()) > 0;
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
