<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'subdomain',
        'custom_domain',
        'plan',
        'subscription_tier_id',
        'trial_ends_at',
        'is_active',
        'stripe_customer_id',
        'stripe_subscription_id',
        'data',
    ];

    /**
     * Attributes stored as regular columns, not in JSON data column.
     * This overrides the base Tenant model's behavior.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'subdomain',
            'custom_domain',
            'plan',
            'subscription_tier_id',
            'trial_ends_at',
            'is_active',
            'stripe_customer_id',
            'stripe_subscription_id',
        ];
    }

    /**
     * Get the subscription tier for this tenant.
     */
    public function subscriptionTier(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTier::class, 'subscription_tier_id');
    }

    /**
     * Check if tenant is on a specific tier.
     */
    public function isOnTier(string $tierSlug): bool
    {
        return $this->subscriptionTier?->slug === $tierSlug;
    }

    /**
     * Check if tenant has a feature based on their tier.
     */
    public function hasFeature(string $feature): bool
    {
        // Inactive tenants have no features
        if (! $this->is_active) {
            return false;
        }

        // Check if tier exists and has feature
        if (! $this->subscriptionTier) {
            return false;
        }

        return $this->subscriptionTier->hasFeature($feature);
    }

    /**
     * Get a limit for this tenant's tier.
     */
    public function getLimit(string $limitKey): ?int
    {
        if (! $this->subscriptionTier) {
            return null;
        }

        return $this->subscriptionTier->getLimit($limitKey);
    }

    /**
     * Check if tenant is within a limit.
     */
    public function isWithinLimit(string $limitKey, int $currentUsage): bool
    {
        $limit = $this->getLimit($limitKey);

        if ($limit === null || $limit === 0) {
            return false; // Feature/limit not available
        }

        return $currentUsage < $limit;
    }

    /**
     * Get the name of the "updated at" column.
     */
    public function getUpdatedAtColumn(): ?string
    {
        return 'updated_at';
    }

    /**
     * Get the name of the "created at" column.
     */
    public function getCreatedAtColumn(): ?string
    {
        return 'created_at';
    }
}
