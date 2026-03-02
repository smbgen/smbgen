<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        'billing_period',
        'stripe_price_id',
        'is_active',
        'features',
        'limits',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'features' => 'array',
            'limits' => 'array',
            'sort_order' => 'integer',
            'price_cents' => 'integer',
        ];
    }

    /**
     * Get the tenants on this subscription tier.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'subscription_tier_id');
    }

    /**
     * Get the price in dollars.
     */
    public function getPriceAttribute(): float
    {
        return $this->price_cents / 100;
    }

    /**
     * Check if a feature is included in this tier.
     */
    public function hasFeature(string $feature): bool
    {
        return ($this->features[$feature] ?? false) === true;
    }

    /**
     * Get a limit for this tier.
     */
    public function getLimit(string $limitKey): ?int
    {
        return $this->limits[$limitKey] ?? null;
    }

    /**
     * Format price for display.
     */
    public function formattedPrice(): string
    {
        return '$'.number_format($this->price, 2);
    }

    /**
     * Get scope to only active tiers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get tiers ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
