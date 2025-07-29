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
        'image',
        'qr_code',
        'access_link',
        'manager_id',
        'owner_id',
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
     * Get the owner that owns the property.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
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
        // Generate QR code content (property request URL)
        $qrContent = $this->getRequestUrl();
        
        // Create QR code using SimpleSoftwareIO/simple-qrcode package
        $qrCode = (new \SimpleSoftwareIO\QrCode\QrCode())
            ->format('png')
            ->size(300)
            ->margin(10)
            ->generate($qrContent);
        
        // Generate unique filename
        $filename = 'qr_codes/property_' . $this->id . '_' . time() . '.png';
        
        // Store the QR code file
        \Storage::disk('public')->put($filename, $qrCode);
        
        // Update the property with the QR code filename
        $this->update(['qr_code' => $filename]);
        
        return $filename;
    }

    /**
     * Get the request URL for this property.
     */
    public function getRequestUrl(): string
    {
        return url('/request/' . $this->access_link);
    }
} 