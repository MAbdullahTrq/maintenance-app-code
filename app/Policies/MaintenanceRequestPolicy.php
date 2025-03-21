<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view maintenance requests
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Super managers can view all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can view requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        // Technicians can view requests assigned to them
        if ($user->isTechnician() && $maintenanceRequest->assigned_to === $user->id) {
            return true;
        }

        return false;
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
    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Super managers can update all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can update requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Super managers can delete all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can delete requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can approve the maintenance request.
     */
    public function approve(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only pending requests can be approved
        if (!$maintenanceRequest->isPending()) {
            return false;
        }

        // Super managers can approve all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can approve requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can assign the maintenance request.
     */
    public function assign(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only approved requests can be assigned
        if (!$maintenanceRequest->isApproved() && !$maintenanceRequest->isPending()) {
            return false;
        }

        // Super managers can assign all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can assign requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the status of the maintenance request.
     */
    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Super managers can update status of all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can update status of requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        // Technicians can update status of requests assigned to them
        if ($user->isTechnician() && $maintenanceRequest->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete images from the maintenance request.
     */
    public function deleteImage(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Super managers can delete images from all requests
        if ($user->isSuperManager()) {
            return true;
        }

        // Property managers can delete images from requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }
} 