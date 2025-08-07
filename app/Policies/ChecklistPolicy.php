<?php

namespace App\Policies;

use App\Models\Checklist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecklistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->isPropertyManager() || $user->hasTeamMemberRole()) && $user->hasActiveSubscription();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Checklist $checklist): bool
    {
        if (!$user->hasActiveSubscription()) {
            return false;
        }
        
        if ($user->isPropertyManager()) {
            return $checklist->manager_id === $user->id;
        }
        
        if ($user->hasTeamMemberRole()) {
            $workspaceOwner = $user->getWorkspaceOwner();
            $workspaceUserIds = [$workspaceOwner->id];
            $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->isPropertyManager() || $user->hasTeamMemberRole()) && $user->hasActiveSubscription();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Checklist $checklist): bool
    {
        if (!$user->hasActiveSubscription()) {
            return false;
        }
        
        if ($user->isPropertyManager()) {
            return $checklist->manager_id === $user->id;
        }
        
        if ($user->hasTeamMemberRole()) {
            $workspaceOwner = $user->getWorkspaceOwner();
            $workspaceUserIds = [$workspaceOwner->id];
            $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Checklist $checklist): bool
    {
        if (!$user->hasActiveSubscription()) {
            return false;
        }
        
        if ($user->isPropertyManager()) {
            return $checklist->manager_id === $user->id;
        }
        
        if ($user->hasTeamMemberRole()) {
            $workspaceOwner = $user->getWorkspaceOwner();
            $workspaceUserIds = [$workspaceOwner->id];
            $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Checklist $checklist): bool
    {
        if (!$user->hasActiveSubscription()) {
            return false;
        }
        
        if ($user->isPropertyManager()) {
            return $checklist->manager_id === $user->id;
        }
        
        if ($user->hasTeamMemberRole()) {
            $workspaceOwner = $user->getWorkspaceOwner();
            $workspaceUserIds = [$workspaceOwner->id];
            $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Checklist $checklist): bool
    {
        if (!$user->hasActiveSubscription()) {
            return false;
        }
        
        if ($user->isPropertyManager()) {
            return $checklist->manager_id === $user->id;
        }
        
        if ($user->hasTeamMemberRole()) {
            $workspaceOwner = $user->getWorkspaceOwner();
            $workspaceUserIds = [$workspaceOwner->id];
            $workspaceUserIds = array_merge($workspaceUserIds, $workspaceOwner->teamMembers()->pluck('users.id')->toArray());
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }
}
