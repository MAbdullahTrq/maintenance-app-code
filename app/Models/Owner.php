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
        'unique_identifier',
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
        return config('app.url') . '/' . $this->unique_identifier;
    }

    /**
     * Generate a unique identifier for the owner.
     */
    public function generateUniqueIdentifier(): string
    {
        $identifier = strtolower(str_replace(' ', '-', $this->name)) . '-' . substr(md5($this->id . time()), 0, 8);
        
        // Ensure uniqueness
        $counter = 1;
        $originalIdentifier = $identifier;
        while (static::where('unique_identifier', $identifier)->where('id', '!=', $this->id)->exists()) {
            $identifier = $originalIdentifier . '-' . $counter;
            $counter++;
        }
        
        return $identifier;
    }

    /**
     * Set the unique identifier if not already set.
     */
    public function ensureUniqueIdentifier(): void
    {
        if (empty($this->unique_identifier)) {
            $this->update(['unique_identifier' => $this->generateUniqueIdentifier()]);
        }
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
