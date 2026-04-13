<?php

namespace App\Modules\SaasProductModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SaasProductModule\Enums\SubscriptionTier;
use App\Modules\SaasProductModule\Services\SubscriptionService;
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
            return redirect()->route('saasproductmodule.billing.plans');
        }

        if (! $user->profile?->onboarding_complete) {
            return redirect()->route('saasproductmodule.onboarding.profile');
        }

        return redirect()->route('saasproductmodule.dashboard');
    }

    public function plans(): View
    {
        return view('saasproductmodule::billing.plans', [
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
            route('saasproductmodule.billing.success'),
            route('saasproductmodule.billing.plans'),
        );

        return redirect($url);
    }

    public function success(Request $request): RedirectResponse
    {
        if ($sessionId = $request->query('session_id')) {
            $this->subscriptionService->syncFromCheckoutSession($request->user(), $sessionId);
        }

        return redirect()->route('saasproductmodule.onboarding.profile')
            ->with('success', 'Subscription activated! Let\'s set up your profile.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $this->subscriptionService->cancel($request->user());

        return redirect()->route('saasproductmodule.billing.plans')
            ->with('success', 'Your subscription has been cancelled.');
    }
}
