<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_in_days',
        'property_limit',
        'technician_limit',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the subscriptions for the plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '€' . number_format($this->price, 2);
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $days = $this->duration_in_days;
        
        if ($days % 365 === 0) {
            $years = $days / 365;
            return $years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        
        if ($days % 30 === 0) {
            $months = $days / 30;
            return $months . ' ' . ($months === 1 ? 'month' : 'months');
        }
        
        return $days . ' ' . ($days === 1 ? 'day' : 'days');
    }
} 