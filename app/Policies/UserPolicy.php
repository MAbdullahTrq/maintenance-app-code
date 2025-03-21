<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperManager() || $user->isPropertyManager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isSuperManager() || 
               $user->id === $model->id || 
               $user->id === $model->invited_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperManager() || $user->isPropertyManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super managers can update any user
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can update technicians they invited
        if ($user->isPropertyManager() && $model->isTechnician() && $model->invited_by === $user->id) {
            return true;
        }

        // Users can update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Super managers can delete any user except themselves
        if ($user->isSuperManager() && $user->id !== $model->id) {
            return true;
        }

        // Property managers can delete technicians they invited
        if ($user->isPropertyManager() && $model->isTechnician() && $model->invited_by === $user->id) {
            return true;
        }

        return false;
    }
} 