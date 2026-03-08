<?php

namespace App\Modules\CleanSlate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CleanSlate\Enums\SubscriptionTier;
use App\Modules\CleanSlate\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function entry(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $this->subscriptionService->isSubscribed($user)) {
            return redirect()->route('cleanslate.billing.plans');
        }

        if (! $user->profile?->onboarding_complete) {
            return redirect()->route('cleanslate.onboarding.profile');
        }

        return redirect()->route('cleanslate.dashboard');
    }

    public function plans(): View
    {
        return view('cleanslate::billing.plans', [
            'tiers' => SubscriptionTier::cases(),
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $request->validate([
            'tier' => ['required', 'in:basic,professional,executive'],
        ]);

        $tier = SubscriptionTier::from($request->tier);
        $user = $request->user();

        $url = $this->subscriptionService->getCheckoutUrl(
            $user,
            $tier,
            route('cleanslate.billing.success'),
            route('cleanslate.billing.plans'),
        );

        return redirect($url);
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('cleanslate.onboarding.profile')
            ->with('success', 'Subscription activated! Let\'s set up your profile.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $this->subscriptionService->cancel($request->user());

        return redirect()->route('cleanslate.billing.plans')
            ->with('success', 'Your subscription has been cancelled.');
    }
}
