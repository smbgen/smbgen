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

    public $timestamps = true;

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'name',
        'email',
        'subdomain',
        'custom_domain',
        'plan',
        'deployment_mode',
        'subscription_tier_id',
        'trial_ends_at',
        'is_active',
        'stripe_customer_id',
        'stripe_subscription_id',
        'data',
    ];

    /**
     * Attributes stored as regular columns, not in the JSON data column.
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
            'deployment_mode',
            'subscription_tier_id',
            'trial_ends_at',
            'is_active',
            'stripe_customer_id',
            'stripe_subscription_id',
        ];
    }

    public function subscriptionTier(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTier::class, 'subscription_tier_id');
    }

    public function isOnTier(string $tierSlug): bool
    {
        return $this->subscriptionTier?->slug === $tierSlug;
    }

    public function hasFeature(string $feature): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->subscriptionTier?->hasFeature($feature) ?? false;
    }

    public function getLimit(string $limitKey): ?int
    {
        return $this->subscriptionTier?->getLimit($limitKey);
    }

    public function isWithinLimit(string $limitKey, int $usage): bool
    {
        $limit = $this->getLimit($limitKey);

        if ($limit === null) {
            return true;
        }

        return $usage < $limit;
    }

    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isPast();
    }

    public function getUpdatedAtColumn(): ?string
    {
        return 'updated_at';
    }

    public function getCreatedAtColumn(): ?string
    {
        return 'created_at';
    }
}
