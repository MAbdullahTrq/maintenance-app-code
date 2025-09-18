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
        'trial_started_at',
        'trial_expires_at',
        'trial_extended',
        'account_locked_at',
        'data_deletion_at',
        'reminder_emails_sent',
        'last_reminder_sent_at',
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
            'trial_started_at' => 'datetime',
            'trial_expires_at' => 'datetime',
            'trial_extended' => 'boolean',
            'account_locked_at' => 'datetime',
            'data_deletion_at' => 'datetime',
            'last_reminder_sent_at' => 'datetime',
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
     * Get team members (excluding technicians).
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by')
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['editor', 'viewer']);
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
     * Get the properties assigned to this user.
     */
    public function assignedProperties(): HasMany
    {
        return $this->hasMany(PropertyAssignment::class);
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
     * Get the owners assigned to this user for management.
     */
    public function assignedOwners(): HasMany
    {
        return $this->hasMany(Owner::class, 'managed_by');
    }

    /**
     * Get the owner assignments for this user.
     */
    public function ownerAssignments(): HasMany
    {
        return $this->hasMany(OwnerAssignment::class);
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
     * Get the checklists created by the user.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class, 'manager_id');
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
     * Check if the user is a team member (editor, viewer).
     *
     * @return bool
     */
    public function hasTeamMemberRole()
    {
        return $this->role && in_array($this->role->slug, ['editor', 'viewer']);
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
     * Check if the user can assign tasks to technicians.
     * This includes property managers and team members with editor role.
     *
     * @return bool
     */
    public function canAssignTasks()
    {
        return $this->isPropertyManager() || $this->isEditor();
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

    // Trial-related methods
    public function startTrial(): void
    {
        $this->update([
            'trial_started_at' => now(),
            'trial_expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);
    }

    public function isOnTrial(): bool
    {
        return $this->trial_started_at && 
               $this->trial_expires_at && 
               now()->lt($this->trial_expires_at);
    }

    public function isInGracePeriod(): bool
    {
        if (!$this->trial_expires_at) {
            return false;
        }

        $gracePeriodEnd = $this->trial_expires_at->addDays(7);
        return now()->gte($this->trial_expires_at) && now()->lt($gracePeriodEnd);
    }

    public function isAccountLocked(): bool
    {
        return $this->account_locked_at !== null;
    }

    public function lockAccount(): void
    {
        $this->update([
            'account_locked_at' => now(),
            'data_deletion_at' => now()->addDays(90),
        ]);
    }

    public function unlockAccount(): void
    {
        $this->update([
            'account_locked_at' => null,
            'data_deletion_at' => null,
        ]);
    }

    public function shouldDeleteData(): bool
    {
        return $this->data_deletion_at && now()->gte($this->data_deletion_at);
    }

    public function canAccessSystem(): bool
    {
        // Admin users always have access
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user is active (email verified)
        if (!$this->is_active) {
            return false;
        }

        // If user has active subscription, they can access
        if ($this->hasActiveSubscription()) {
            return true;
        }

        // If on trial, they can access
        if ($this->isOnTrial()) {
            return true;
        }

        // If in grace period, they can access but should be prompted to subscribe
        if ($this->isInGracePeriod()) {
            return true;
        }

        // Account is locked
        return false;
    }

    public function getTrialStatus(): string
    {
        if ($this->isOnTrial()) {
            $daysLeft = now()->diffInDays($this->trial_expires_at, false);
            return $daysLeft > 0 ? "trial_active" : "trial_expired";
        }

        if ($this->isInGracePeriod()) {
            return "grace_period";
        }

        if ($this->isAccountLocked()) {
            return "account_locked";
        }

        return "no_trial";
    }

    public function getTrialDaysLeft(): int
    {
        if (!$this->trial_expires_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->trial_expires_at, false));
    }

    public function getGracePeriodDaysLeft(): int
    {
        if (!$this->trial_expires_at) {
            return 0;
        }

        $gracePeriodEnd = $this->trial_expires_at->addDays(7);
        return max(0, now()->diffInDays($gracePeriodEnd, false));
    }

    public function extendTrial(): void
    {
        if ($this->trial_extended) {
            throw new \Exception('Trial can only be extended once.');
        }

        $this->update([
            'trial_expires_at' => $this->trial_expires_at->addDays(7),
            'trial_extended' => true,
        ]);
    }

    public function incrementReminderEmails(): void
    {
        $this->increment('reminder_emails_sent');
        $this->update(['last_reminder_sent_at' => now()]);
    }

    public function canSendReminderEmail(): bool
    {
        if ($this->reminder_emails_sent >= 3) {
            return false;
        }

        if (!$this->last_reminder_sent_at) {
            return true;
        }

        // Send reminder emails at specific intervals
        $daysSinceLastReminder = now()->diffInDays($this->last_reminder_sent_at);
        
        switch ($this->reminder_emails_sent) {
            case 0: // First reminder - Day 37
                return $daysSinceLastReminder >= 7;
            case 1: // Second reminder - Day 60
                return $daysSinceLastReminder >= 23;
            case 2: // Third reminder - Day 85
                return $daysSinceLastReminder >= 25;
            default:
                return false;
        }
    }

    /**
     * Get the current property count for the user.
     */
    public function getCurrentPropertyCount(): int
    {
        // For team members, get the workspace owner's property count
        if ($this->isTeamMember()) {
            $workspaceOwner = $this->getWorkspaceOwner();
            return $workspaceOwner->managedProperties()->count();
        }
        
        return $this->managedProperties()->count();
    }

    /**
     * Get the current technician count for the user.
     */
    public function getCurrentTechnicianCount(): int
    {
        // For team members, get the workspace owner's technician count
        if ($this->isTeamMember()) {
            $workspaceOwner = $this->getWorkspaceOwner();
            return $workspaceOwner->technicians()->count();
        }
        
        return $this->technicians()->count();
    }

    /**
     * Get the subscription limits for the user.
     */
    public function getSubscriptionLimits(): array
    {
        // For team members, get the workspace owner's subscription limits
        if ($this->isTeamMember()) {
            $workspaceOwner = $this->getWorkspaceOwner();
            return $workspaceOwner->getSubscriptionLimits();
        }
        
        // Trial users have Premium package limits
        if ($this->isOnTrial()) {
            return [
                'property_limit' => 100,
                'technician_limit' => 30,
            ];
        }
        
        $subscription = $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->with('plan')
            ->first();
        
        return [
            'property_limit' => $subscription?->plan?->property_limit ?? 0,
            'technician_limit' => $subscription?->plan?->technician_limit ?? 0,
        ];
    }

    /**
     * Check if the user can create a new property.
     */
    public function canCreateProperty(): bool
    {
        // Admin users can always create properties
        if ($this->isAdmin()) {
            return true;
        }
        
        // Trial users have Premium package limits (100 properties)
        if ($this->isOnTrial()) {
            return $this->getCurrentPropertyCount() < 100;
        }
        
        // Users with active subscriptions are limited by their plan
        if ($this->hasActiveSubscription()) {
            $limits = $this->getSubscriptionLimits();
            return $this->getCurrentPropertyCount() < $limits['property_limit'];
        }
        
        // Users without active subscriptions cannot create properties
        return false;
    }

    /**
     * Check if the user can create a new technician.
     */
    public function canCreateTechnician(): bool
    {
        // Admin users can always create technicians
        if ($this->isAdmin()) {
            return true;
        }
        
        // Trial users have Premium package limits (30 technicians)
        if ($this->isOnTrial()) {
            return $this->getCurrentTechnicianCount() < 30;
        }
        
        // Users with active subscriptions are limited by their plan
        if ($this->hasActiveSubscription()) {
            $limits = $this->getSubscriptionLimits();
            return $this->getCurrentTechnicianCount() < $limits['technician_limit'];
        }
        
        // Users without active subscriptions cannot create technicians
        return false;
    }

    /**
     * Get the remaining property slots available.
     */
    public function getRemainingPropertySlots(): int
    {
        if ($this->isAdmin()) {
            return -1; // Unlimited
        }
        
        if ($this->isOnTrial()) {
            return max(0, 100 - $this->getCurrentPropertyCount());
        }
        
        if ($this->hasActiveSubscription()) {
            $limits = $this->getSubscriptionLimits();
            return max(0, $limits['property_limit'] - $this->getCurrentPropertyCount());
        }
        
        return 0;
    }

    /**
     * Get the remaining technician slots available.
     */
    public function getRemainingTechnicianSlots(): int
    {
        if ($this->isAdmin()) {
            return -1; // Unlimited
        }
        
        if ($this->isOnTrial()) {
            return max(0, 30 - $this->getCurrentTechnicianCount());
        }
        
        if ($this->hasActiveSubscription()) {
            $limits = $this->getSubscriptionLimits();
            return max(0, $limits['technician_limit'] - $this->getCurrentTechnicianCount());
        }
        
        return 0;
    }
}
