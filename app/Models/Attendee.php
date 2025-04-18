<?php

namespace App\Models;

use App\Models\Edition;
use App\Models\AccessLog;
use App\Models\AttendeeDetail;
use App\Models\Scopes\EditionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Events\AttendeeCheckinStatusChanged;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendee extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    /**
     * Edition
     */
    public function edition(): BelongsTo {
        return $this->belongsTo(Edition::class);
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
        return $this->belongsTo(Group::class);
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
     * Check in
     */
    public function checkIn(string $client = 'APP') {
        $this->checked_in = now();
        if (!$this->first_checked_in) $this->first_checked_in = now();
        $this->save();
        AccessLog::log(attendee: $this, type: 'IN', client: $client);
        AttendeeCheckinStatusChanged::dispatch($this, 'in');
    }

    /**
     * Check out
     */
    public function checkOut(string $client = 'APP') {
        $this->checked_in = null;
        $this->save();
        AccessLog::log(attendee: $this, type: 'OUT', client: $client);
        AttendeeCheckinStatusChanged::dispatch($this, 'out');
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
     * Filter voters
     */
    public function scopeVoters($query)
    {
        return $query->where('votes', '>=', 1);
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
