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
        // Append session ID placeholder so the success handler can sync without relying on webhooks
        $successUrlWithSession = $successUrl.(str_contains($successUrl, '?') ? '&' : '?').'session_id={CHECKOUT_SESSION_ID}';

        return $user->newSubscription('cleanslate', $tier->stripePriceId())
            ->checkout([
                'success_url' => $successUrlWithSession,
                'cancel_url'  => $cancelUrl,
            ])
            ->url;
    }

    public function syncFromCheckoutSession(User $user, string $sessionId): void
    {
        $stripe = $user->stripe();

        $session = $stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['subscription.items'],
        ]);

        if (! $session->subscription || $session->subscription->status !== 'active') {
            return;
        }

        $sub   = $session->subscription;
        $item  = $sub->items->data[0] ?? null;
        $price = $item?->price?->id;

        $user->subscriptions()->updateOrCreate(
            ['stripe_id' => $sub->id],
            [
                'user_id'       => $user->id,
                'type'          => 'cleanslate',
                'stripe_status' => $sub->status,
                'stripe_price'  => $price,
                'quantity'      => $item?->quantity ?? 1,
                'trial_ends_at' => null,
                'ends_at'       => null,
            ]
        );
    }

    public function cancel(User $user): void
    {
        $user->subscription('cleanslate')?->cancel();
    }
}
