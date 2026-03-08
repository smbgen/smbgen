<?php

namespace App\Modules\CleanSlate\Services;

use App\Models\User;
use App\Modules\CleanSlate\Enums\SubscriptionTier;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionService
{
    public function getActiveTier(User $user): ?SubscriptionTier
    {
        $subscription = $user->subscription('cleanslate');

        if (! $subscription || ! $subscription->active()) {
            return null;
        }

        foreach (SubscriptionTier::cases() as $tier) {
            if ($subscription->hasPrice($tier->stripePriceId())) {
                return $tier;
            }
        }

        return null;
    }

    public function isSubscribed(User $user): bool
    {
        return $this->getActiveTier($user) !== null;
    }

    public function getCheckoutUrl(User $user, SubscriptionTier $tier, string $successUrl, string $cancelUrl): string
    {
        return $user->newSubscription('cleanslate', $tier->stripePriceId())
            ->checkout([
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
            ])
            ->url;
    }

    public function cancel(User $user): void
    {
        $user->subscription('cleanslate')?->cancel();
    }
}
