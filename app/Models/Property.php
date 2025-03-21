<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'qr_code',
        'access_link',
        'manager_id',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->access_link)) {
                $property->access_link = Str::random(32);
            }
        });
    }

    /**
     * Get the manager that owns the property.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the maintenance requests for the property.
     */
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /**
     * Generate a QR code for the property.
     */
    public function generateQrCode(): string
    {
        // This would typically use a QR code generation library
        // For now, we'll just return a placeholder
        return 'qr_code_' . $this->id . '.png';
    }

    /**
     * Get the request URL for this property.
     */
    public function getRequestUrl(): string
    {
        return url('/request/' . $this->access_link);
    }
} 