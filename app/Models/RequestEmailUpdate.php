<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestEmailUpdate extends Model
{
    protected $fillable = [
        'maintenance_request_id',
        'user_id',
    ];

    /**
     * Get the maintenance request that owns the email update.
     */
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    /**
     * Get the user that owns the email update.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
