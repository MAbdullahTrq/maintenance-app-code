<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
     * Get the team invitations sent by this user.
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class, 'invited_by');
    }

    /**
     * Get the team invitation accepted by this user.
     */
    public function acceptedInvitation(): HasMany
    {
        return $this->hasMany(TeamInvitation::class, 'accepted_by');
    }

    /**
     * Get the team members (users invited by this user with team member roles).
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by')
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['team_member', 'viewer', 'editor']);
            });
    }

    /**
     * Get the technicians (users invited by this user with technician role).
     */
    public function technicians(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by')
            ->whereHas('role', function ($query) {
                $query->where('slug', 'technician');
            });
    }

    /**
     * Get the team owner (user who invited this user).
     */
    public function teamOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Check if this user is a team owner (has team members).
     */
    public function isTeamOwner(): bool
    {
        return $this->teamMembers()->exists();
    }

    /**
     * Check if this user is a team member (was invited by someone).
     */
    public function isTeamMember(): bool
    {
        return !is_null($this->invited_by);
    }

    /**
     * Get the workspace owner (either this user or the user who invited them).
     */
    public function getWorkspaceOwner(): User
    {
        return $this->isTeamMember() ? $this->teamOwner : $this;
    }

    /**
     * Get all users in the same workspace (team owner + team members).
     */
    public function getWorkspaceUsers()
    {
        $workspaceOwner = $this->getWorkspaceOwner();
        
        if ($workspaceOwner->id === $this->id) {
            // This user is the workspace owner
            return User::where('id', $workspaceOwner->id)
                ->orWhere('invited_by', $workspaceOwner->id)
                ->get();
        } else {
            // This user is a team member
            return User::where('id', $workspaceOwner->id)
                ->orWhere('invited_by', $workspaceOwner->id)
                ->get();
        }
    }

    /**
     * Get the properties managed by this user.
     */
    public function managedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    /**
     * Get the owners managed by this user.
     */
    public function managedOwners(): HasMany
    {
        return $this->hasMany(Owner::class, 'manager_id');
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
     * For team members, check their workspace owner's subscription.
     */
    public function hasActiveSubscription(): bool
    {
        // If this user is a team member, check their workspace owner's subscription
        if ($this->isTeamMember()) {
            $workspaceOwner = $this->getWorkspaceOwner();
            return $workspaceOwner->subscriptions()->where('status', 'active')
                ->where('ends_at', '>', now())
                ->exists();
        }
        
        // For property managers and others, check their own subscription
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
     * Check if the user is a team member (team_member, viewer, editor).
     *
     * @return bool
     */
    public function hasTeamMemberRole()
    {
        return $this->role && in_array($this->role->slug, ['team_member', 'viewer', 'editor']);
    }

    /**
     * Check if the user is a viewer (read-only access).
     *
     * @return bool
     */
    public function isViewer()
    {
        return $this->hasRole('viewer');
    }

    /**
     * Check if the user is an editor (can edit but not create).
     *
     * @return bool
     */
    public function isEditor()
    {
        return $this->hasRole('editor');
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

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
