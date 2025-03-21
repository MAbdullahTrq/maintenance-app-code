<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'comment_id',
        'image_path',
        'type',
    ];

    /**
     * Get the maintenance request that owns the image.
     */
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    /**
     * Get the comment that owns the image.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(RequestComment::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getUrl(): string
    {
        return asset('storage/' . $this->image_path);
    }
} 