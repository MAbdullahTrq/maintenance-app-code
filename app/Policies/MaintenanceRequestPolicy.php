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
        // Admin can view all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can view requests for their properties
        if ($user->isPropertyManager()) {
            return true;
        }

        // Technicians can view requests assigned to them
        return $user->isTechnician();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin can view all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can view requests for their properties
        if ($user->isPropertyManager()) {
            return true;
        }

        // Technicians can view requests assigned to them
        if ($user->isTechnician()) {
            return $maintenanceRequest->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin can create requests
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can create requests
        return $user->isPropertyManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin can update any request
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can update requests for their properties
        if ($user->isPropertyManager()) {
            return $maintenanceRequest->property->manager_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin can delete any request
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can delete requests for their properties
        if ($user->isPropertyManager()) {
            return $maintenanceRequest->property->manager_id === $user->id;
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

        // Admin can approve all requests
        if ($user->isAdmin()) {
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

        // Admin can assign all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can assign requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can accept the maintenance request.
     */
    public function accept(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only technicians can accept requests
        if (!$user->isTechnician()) {
            return false;
        }

        // Only assigned technicians can accept requests
        return $maintenanceRequest->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can reject the maintenance request.
     */
    public function reject(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Only technicians can reject requests
        if (!$user->isTechnician()) {
            return false;
        }

        // Only assigned technicians can reject requests
        return $maintenanceRequest->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can update the status of the maintenance request.
     */
    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin can update status of all requests
        if ($user->isAdmin()) {
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
        // Admin can delete images from all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can delete images from requests for their properties
        if ($user->isPropertyManager() && $maintenanceRequest->property->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    public function restore(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        // Admin can restore any request
        if ($user->isAdmin()) {
            return true;
        }

        // Property managers can restore requests for their properties
        if ($user->isPropertyManager()) {
            return $maintenanceRequest->property->manager_id === $user->id;
        }

        return false;
    }
} 