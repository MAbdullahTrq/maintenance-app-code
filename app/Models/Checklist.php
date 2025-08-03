<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
    ];

    /**
     * Get the manager that owns the checklist.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the items for the checklist.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('order');
    }

    /**
     * Get the maintenance requests that use this checklist.
     */
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Get the checkbox items for the checklist.
     */
    public function checkboxItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->where('type', 'checkbox')->orderBy('order');
    }

    /**
     * Get the text items for the checklist.
     */
    public function textItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->where('type', 'text')->orderBy('order');
    }

    /**
     * Get the required checkbox items for the checklist.
     */
    public function requiredCheckboxItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)
            ->where('type', 'checkbox')
            ->where('is_required', true)
            ->orderBy('order');
    }

    /**
     * Generate the formatted description for maintenance requests.
     */
    public function generateFormattedDescription(): string
    {
        $description = $this->description ?: '';
        
        if ($this->items->count() > 0) {
            $description .= "\n\n**Checklist Items:**\n";
            
            foreach ($this->items as $item) {
                $required = $item->is_required ? ' (Required)' : '';
                $description .= "- {$item->description}{$required}\n";
            }
        }
        
        return trim($description);
    }
}
