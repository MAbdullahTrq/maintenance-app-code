<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'type',
        'description',
        'task_description',
        'is_required',
        'attachment_path',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the checklist that owns the item.
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    /**
     * Get the responses for this checklist item.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(ChecklistResponse::class);
    }

    /**
     * Check if this item is a checkbox type.
     */
    public function isCheckbox(): bool
    {
        return $this->type === 'checkbox';
    }

    /**
     * Check if this item is a text type.
     */
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Get the attachment URL if it exists.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }
        
        return asset('storage/' . $this->attachment_path);
    }
}
