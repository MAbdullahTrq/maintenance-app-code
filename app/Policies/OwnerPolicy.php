<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OwnerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPropertyManager() || $user->isTeamMember();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Owner $owner): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        // For team members, check if the owner belongs to their workspace owner
        if ($user->isTeamMember()) {
            $workspaceOwner = $user->getWorkspaceOwner();
            return $owner->manager_id === $workspaceOwner->id;
        }
        
        // For property managers, check if they manage this owner
        return $owner->manager_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isPropertyManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Owner $owner): bool
    {
        return $user->isAdmin() || $owner->manager_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Owner $owner): bool
    {
        return $user->isAdmin() || $owner->manager_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Owner $owner): bool
    {
        return $user->isAdmin() || $owner->manager_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Owner $owner): bool
    {
        return $user->isAdmin() || $owner->manager_id === $user->id;
    }
}
