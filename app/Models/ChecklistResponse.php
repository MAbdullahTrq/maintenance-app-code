<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'checklist_item_id',
        'is_completed',
        'text_response',
        'response_attachment_path',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    /**
     * Get the maintenance request that owns the response.
     */
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    /**
     * Get the checklist item that owns the response.
     */
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Get the response attachment URL if it exists.
     */
    public function getResponseAttachmentUrlAttribute(): ?string
    {
        if (!$this->response_attachment_path) {
            return null;
        }
        
        return asset('storage/' . $this->response_attachment_path);
    }
} 