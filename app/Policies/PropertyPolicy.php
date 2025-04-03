<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPropertyManager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->manager_id === $user->id;
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
    public function update(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->manager_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->manager_id === $user->id;
    }

    /**
     * Determine whether the user can create a maintenance request for the property.
     */
    public function createRequest(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->manager_id === $user->id;
    }

    public function restore(User $user, Property $property): bool
    {
        return $user->isAdmin() || $property->manager_id === $user->id;
    }
} 