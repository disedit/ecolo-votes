<?php

namespace App\Models;

use App\Models\Fee;
use App\Models\User;
use App\Models\Edition;
use App\Models\Payment;
use App\Models\AccessLog;
use App\Models\AttendeeDetail;
use App\Models\Scopes\EditionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subdelegates' => 'object'
    ];

    protected $fillable = [
        'paid'
    ];

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
     * Type
     */
    public function type(): BelongsTo {
        return $this->belongsTo(Type::class);
    }

    /**
     * Group
     */
    public function group(): BelongsTo {
        return $this->user->group();
    }

    /**
     * Fee
     */
    public function fee(): HasOne {
        return $this->hasOne(Fee::class);
    }

    /**
     * Payments
     */
    public function payments(): HasMany {
        return $this->hasMany(Payment::class);
    }

    /**
     * Details
     */
    public function details(): HasMany {
        return $this->hasMany(AttendeeDetail::class);
    }

    /**
     * Access log
     */
    public function accessLog(): HasMany {
        return $this->hasMany(AccessLog::class);
    }

    /**
     * Fees
     */
    public function fees($prepend = null): array {
        if ($this->custom_fee === '0.00') return [];

        $fees = $this->type->fees->filter(function ($fee) {
            return (!$fee->member_parties) ? true : in_array($this->user->group->id, $fee->member_parties);
        })->sortBy('amount')->values()->all();

        if ($this->custom_fee || $prepend) {
            array_unshift($fees, ['id' => 'custom', 'name' => 'Participant fee', 'amount' => $this->custom_fee ?? $prepend]);
        }

        return $fees;
    }

    /**
     * Check if attendee has subdelegates
     */
    public function hasSubdelegates(): bool {
        if (!$this->subdelegates) return false;
        return count($this->subdelegates) > 0;
    }

    /**
     * Has fees
     */
    public function hasFees(): bool {
        return count($this->fees()) > 0;
    }

    /**
     * Check in
     */
    public function checkIn(string $client = 'APP') {
        $this->checked_in = now();
        if (!$this->first_checked_in) $this->first_checked_in = now();
        $this->save();
        AccessLog::log(attendee: $this, type: 'IN', client: $client);
    }

    /**
     * Check out
     */
    public function checkOut(string $client = 'APP') {
        $this->checked_in = null;
        $this->save();
        AccessLog::log(attendee: $this, type: 'OUT', client: $client);
    }

    /**
     * Determine if attendee is a voter
     */
    public function isVoter(): bool {
        return $this->votes > 0;
    }

    /**
     * Determine if attendee has checked in
     */
    public function hasCheckedIn(): bool {
        return !!$this->checked_in;
    }

    /**
     * Fees attribute
     */
    public function getFeesAttribute(): array {
        return $this->fees();
    }

    /**
     * Filter voters
     */
    public function scopeVoters($query)
    {
        return $query->where('votes', '>=', 1);
    }

    /**
     * Filter voters
     */
    public function scopeDelegates($query)
    {
        return $query->withoutGlobalScopes()
            ->select('attendees.*')
            ->join('types', 'types.id', '=', 'attendees.type_id')
            ->where('is_delegates', 1)
            ->where('attendees.edition_id', config('edition_id'))
            ->whereNull('deleted_at');
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
