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
        'attachment_path_old',
        'attachments',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'attachments' => 'array',
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
        if (!$this->attachment_path_old) {
            return null;
        }
        
        return asset('storage/' . $this->attachment_path_old);
    }

    /**
     * Get all attachment URLs.
     */
    public function getAttachmentUrlsAttribute(): array
    {
        if (!$this->attachments) {
            return [];
        }
        
        return array_map(function($path) {
            return asset('storage/' . $path);
        }, $this->attachments);
    }

    /**
     * Check if item has any attachments.
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments) || !empty($this->attachment_path_old);
    }

    /**
     * Get all attachment paths (including legacy single attachment).
     */
    public function getAllAttachmentPaths(): array
    {
        $paths = [];
        
        // Add legacy single attachment if exists
        if ($this->attachment_path_old) {
            $paths[] = $this->attachment_path_old;
        }
        
        // Add multiple attachments if exist
        if ($this->attachments) {
            $paths = array_merge($paths, $this->attachments);
        }
        
        return $paths;
    }
}
