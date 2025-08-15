<?php

namespace App\Policies;

use App\Models\Checklist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecklistPolicy
{
    use HandlesAuthorization;

    /**
     * Get all user IDs in the workspace (manager + team members).
     */
    private function getWorkspaceUserIds(User $user): array
    {
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        $workspaceUserIds = [$workspaceOwner->id];
        $teamMemberIds = $workspaceOwner->teamMembers()->pluck('users.id')->toArray();
        return array_merge($workspaceUserIds, $teamMemberIds);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->isPropertyManager() || $user->hasTeamMemberRole()) && $user->canAccessSystem();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Checklist $checklist): bool
    {
        if (!$user->canAccessSystem()) {
            return false;
        }
        
        if ($user->isPropertyManager() || $user->hasTeamMemberRole()) {
            $workspaceUserIds = $this->getWorkspaceUserIds($user);
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->isPropertyManager() || $user->hasTeamMemberRole()) && $user->canAccessSystem();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Checklist $checklist): bool
    {
        if (!$user->canAccessSystem()) {
            return false;
        }
        
        if ($user->isPropertyManager() || $user->hasTeamMemberRole()) {
            $workspaceUserIds = $this->getWorkspaceUserIds($user);
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Checklist $checklist): bool
    {
        if (!$user->canAccessSystem()) {
            return false;
        }
        
        if ($user->isPropertyManager() || $user->hasTeamMemberRole()) {
            $workspaceUserIds = $this->getWorkspaceUserIds($user);
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Checklist $checklist): bool
    {
        if (!$user->canAccessSystem()) {
            return false;
        }
        
        if ($user->isPropertyManager() || $user->hasTeamMemberRole()) {
            $workspaceUserIds = $this->getWorkspaceUserIds($user);
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Checklist $checklist): bool
    {
        if (!$user->canAccessSystem()) {
            return false;
        }
        
        if ($user->isPropertyManager() || $user->hasTeamMemberRole()) {
            $workspaceUserIds = $this->getWorkspaceUserIds($user);
            return in_array($checklist->manager_id, $workspaceUserIds);
        }
        
        return false;
    }
}
