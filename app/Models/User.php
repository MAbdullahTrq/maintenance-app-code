<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'role_id',
        'invited_by',
        'is_active',
        'verification_token',
        'verification_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($user) {
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'verification_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who invited this user.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the users invited by this user.
     */
    public function invitedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    /**
     * Get the properties managed by this user.
     */
    public function managedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    /**
     * Get the maintenance requests assigned to this user.
     */
    public function assignedRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_to');
    }

    /**
     * Get the maintenance requests approved by this user.
     */
    public function approvedRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'approved_by');
    }

    /**
     * Get the subscriptions for the user.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if the user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role && $this->role->slug === $role;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a property manager.
     *
     * @return bool
     */
    public function isPropertyManager()
    {
        return $this->hasRole('property_manager');
    }

    /**
     * Check if the user is a technician.
     *
     * @return bool
     */
    public function isTechnician()
    {
        return $this->hasRole('technician');
    }

    /**
     * Generate a verification token for the user.
     *
     * @return string
     */
    public function generateVerificationToken(): string
    {
        $token = \Illuminate\Support\Str::random(60);
        
        $this->update([
            'verification_token' => $token,
            'verification_token_expires_at' => now()->addHours(24),
        ]);

        return $token;
    }

    /**
     * Check if the verification token is valid.
     *
     * @param string $token
     * @return bool
     */
    public function isValidVerificationToken(string $token): bool
    {
        return $this->verification_token === $token 
            && $this->verification_token_expires_at 
            && $this->verification_token_expires_at->isFuture();
    }

    /**
     * Clear the verification token.
     *
     * @return void
     */
    public function clearVerificationToken(): void
    {
        $this->update([
            'verification_token' => null,
            'verification_token_expires_at' => null,
        ]);
    }
}
