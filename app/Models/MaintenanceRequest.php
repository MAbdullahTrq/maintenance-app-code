<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'requester_name',
        'requester_email',
        'requester_phone',
        'property_id',
        'status',
        'due_date',
        'assigned_to',
        'approved_by',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the property that owns the maintenance request.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the technician assigned to the maintenance request.
     */
    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the manager who approved the maintenance request.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the images for the maintenance request.
     */
    public function images(): HasMany
    {
        return $this->hasMany(RequestImage::class);
    }

    /**
     * Get the comments for the maintenance request.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(RequestComment::class);
    }

    /**
     * Get the request images by type.
     */
    public function getImagesByType(string $type): HasMany
    {
        return $this->images()->where('type', $type);
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is accepted (approved in DB).
     */
    public function isAccepted(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is assigned (also uses approved in DB).
     */
    public function isAssigned(): bool
    {
        return $this->status === 'approved' && $this->assigned_to !== null;
    }

    /**
     * Check if the request is started (in_progress in DB).
     */
    public function isStarted(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the request is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the request is declined.
     */
    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    /**
     * Mark the request as accepted.
     */
    public function markAsAccepted(User $user, ?string $dueDate = null): self
    {
        $this->update([
            'status' => 'accepted',
            'approved_by' => $user->id,
            'due_date' => $dueDate,
        ]);

        return $this;
    }

    /**
     * Mark the request as assigned.
     */
    public function markAsAssigned(): self
    {
        // We keep the status as 'approved' but assign a technician
        $this->update([
            'status' => 'approved',
        ]);

        return $this;
    }

    /**
     * Mark the request as started.
     */
    public function markAsStarted(): self
    {
        $this->update([
            'status' => 'in_progress',
        ]);

        return $this;
    }

    /**
     * Mark the request as completed.
     */
    public function markAsCompleted(): self
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark the request as declined.
     */
    public function markAsDeclined(): self
    {
        $this->update([
            'status' => 'declined',
        ]);

        return $this;
    }

    /**
     * Assign the request to a technician.
     */
    public function assignTo(User $technician): self
    {
        $this->update([
            'assigned_to' => $technician->id,
        ]);

        return $this;
    }
} 