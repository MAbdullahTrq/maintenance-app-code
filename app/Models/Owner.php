<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company',
        'notes',
        'manager_id',
        'qr_code',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent deletion if owner has properties
        static::deleting(function ($owner) {
            if ($owner->properties()->count() > 0) {
                throw new \Exception('Cannot delete owner with associated properties. Please remove or reassign the properties first.');
            }
        });
    }

    /**
     * Get the manager that owns the owner.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the properties owned by this owner.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Generate QR code for the owner.
     */
    public function generateQrCode(): string
    {
        try {
            $qrContent = $this->getOwnerUrl();
            $qrCodeData = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(300)
                ->margin(10)
                ->generate($qrContent);
            
            if ($qrCodeData instanceof \Illuminate\Support\HtmlString) {
                $qrCodeData = $qrCodeData->toHtml();
            }
            
            $filename = 'qr_codes/owner_' . $this->id . '_' . time() . '.svg';
            \Storage::disk('public')->put($filename, $qrCodeData);
            $this->update(['qr_code' => $filename]);
            
            return $filename;
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed for owner ' . $this->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the URL for the owner's QR code.
     */
    public function getOwnerUrl(): string
    {
        return config('app.url') . '/owner/' . $this->id . '/info';
    }

    /**
     * Get the display name with company in parentheses if available.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->company && !empty(trim($this->company))) {
            return $this->name . ' (' . $this->company . ')';
        }
        return $this->name;
    }
}
