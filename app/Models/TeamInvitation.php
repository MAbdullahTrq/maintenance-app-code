<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamInvitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'role_id',
        'invited_by',
        'token',
        'expires_at',
        'accepted_at',
        'accepted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            $invitation->token = Str::random(64);
            $invitation->expires_at = now()->addDays(7); // 7 days expiry
        });
    }

    /**
     * Get the role for this invitation.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who sent this invitation.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the user who accepted this invitation.
     */
    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * Check if the invitation is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the invitation has been accepted.
     */
    public function isAccepted(): bool
    {
        return !is_null($this->accepted_at);
    }

    /**
     * Check if the invitation is still valid (not expired and not accepted).
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isAccepted();
    }

    /**
     * Accept the invitation.
     */
    public function accept(User $user): void
    {
        $this->update([
            'accepted_at' => now(),
            'accepted_by' => $user->id,
        ]);
    }
}
