<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display subscription plans
     */
    public function plans()
    {
        $subscription = $this->getSubscriptionData();

        $plans = [
            'starter' => [
                'name' => 'Starter',
                'price' => '$29',
                'price_id' => config('services.stripe.plans.starter'),
                'interval' => 'month',
                'features' => [
                    'Up to 50 clients',
                    'Basic CMS features',
                    'Email support',
                    '5 GB storage',
                ],
            ],
            'professional' => [
                'name' => 'Professional',
                'price' => '$79',
                'price_id' => config('services.stripe.plans.professional'),
                'interval' => 'month',
                'features' => [
                    'Up to 500 clients',
                    'Advanced CMS features',
                    'Priority email support',
                    '50 GB storage',
                    'AI content generation',
                    'Booking system',
                ],
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'price' => '$199',
                'price_id' => config('services.stripe.plans.enterprise'),
                'interval' => 'month',
                'features' => [
                    'Unlimited clients',
                    'Full CMS features',
                    'Priority phone & email support',
                    '500 GB storage',
                    'Advanced AI features',
                    'White-label options',
                    'Dedicated account manager',
                ],
            ],
        ];

        return view('admin.subscription.plans', compact('plans', 'subscription'));
    }

    /**
     * Create a new subscription via Stripe Checkout
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
        ]);

        $user = auth()->user();

        try {
            // Get or create Stripe customer
            $stripeCustomerId = BusinessSetting::get('stripe_customer_id');

            if (! $stripeCustomerId) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => BusinessSetting::get('business_name', $user->name),
                ]);

                $stripeCustomerId = $customer->id;
                BusinessSetting::set('stripe_customer_id', $stripeCustomerId);
            }

            // Get price ID for the plan
            $priceId = config("services.stripe.plans.{$request->plan}");

            if (! $priceId) {
                return back()->with('error', 'Invalid plan selected.');
            }

            // Create Checkout Session
            $session = Session::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('admin.subscription.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('admin.subscription.plans'),
                'metadata' => [
                    'plan' => $request->plan,
                ],
            ]);

            return redirect($session->url);

        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription creation failed', [
                'plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to create subscription: '.$e->getMessage());
        }
    }

    /**
     * Handle successful subscription
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('admin.subscription.plans')
                ->with('error', 'Invalid session.');
        }

        try {
            $session = Session::retrieve($sessionId);

            BusinessSetting::set('stripe_subscription_id', $session->subscription);
            BusinessSetting::set('subscription_plan', $session->metadata->plan ?? 'professional');
            BusinessSetting::set('trial_ends_at', null);

            return redirect()->route('admin.subscription.manage')
                ->with('success', 'Subscription activated successfully!');

        } catch (ApiErrorException $e) {
            Log::error('Failed to retrieve checkout session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.subscription.plans')
                ->with('error', 'Failed to verify subscription.');
        }
    }

    /**
     * Display current subscription management page
     */
    public function manage()
    {
        $subscription = $this->getSubscriptionData();
        $stripeSubscription = null;
        $stripeSubscriptionId = BusinessSetting::get('stripe_subscription_id');

        if ($stripeSubscriptionId) {
            try {
                $stripeSubscription = Subscription::retrieve($stripeSubscriptionId);
            } catch (ApiErrorException $e) {
                Log::error('Failed to retrieve subscription', [
                    'subscription_id' => $stripeSubscriptionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('admin.subscription.manage', compact('subscription', 'stripeSubscription'));
    }

    /**
     * Upgrade or downgrade subscription plan
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
        ]);

        $stripeSubscriptionId = BusinessSetting::get('stripe_subscription_id');

        if (! $stripeSubscriptionId) {
            return back()->with('error', 'No active subscription found.');
        }

        try {
            $stripeSubscription = Subscription::retrieve($stripeSubscriptionId);

            $newPriceId = config("services.stripe.plans.{$request->plan}");

            if (! $newPriceId) {
                return back()->with('error', 'Invalid plan selected.');
            }

            Subscription::update($stripeSubscriptionId, [
                'items' => [
                    [
                        'id' => $stripeSubscription->items->data[0]->id,
                        'price' => $newPriceId,
                    ],
                ],
                'proration_behavior' => 'always_invoice',
            ]);

            BusinessSetting::set('subscription_plan', $request->plan);

            return back()->with('success', 'Plan updated successfully!');

        } catch (ApiErrorException $e) {
            Log::error('Failed to update subscription', [
                'new_plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update plan: '.$e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $stripeSubscriptionId = BusinessSetting::get('stripe_subscription_id');

        if (! $stripeSubscriptionId) {
            return back()->with('error', 'No active subscription found.');
        }

        try {
            $stripeSubscription = Subscription::update($stripeSubscriptionId, [
                'cancel_at_period_end' => true,
            ]);

            Log::info('Subscription cancelled', [
                'subscription_id' => $stripeSubscriptionId,
                'ends_at' => $stripeSubscription->current_period_end,
            ]);

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');

        } catch (ApiErrorException $e) {
            Log::error('Failed to cancel subscription', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to cancel subscription: '.$e->getMessage());
        }
    }

    /**
     * Reactivate cancelled subscription
     */
    public function reactivate(Request $request)
    {
        $stripeSubscriptionId = BusinessSetting::get('stripe_subscription_id');

        if (! $stripeSubscriptionId) {
            return back()->with('error', 'No subscription found.');
        }

        try {
            Subscription::update($stripeSubscriptionId, [
                'cancel_at_period_end' => false,
            ]);

            return back()->with('success', 'Subscription reactivated successfully!');

        } catch (ApiErrorException $e) {
            Log::error('Failed to reactivate subscription', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reactivate subscription: '.$e->getMessage());
        }
    }

    /**
     * Redirect to Stripe Customer Portal
     */
    public function portal()
    {
        $stripeCustomerId = BusinessSetting::get('stripe_customer_id');

        if (! $stripeCustomerId) {
            return back()->with('error', 'No Stripe customer found.');
        }

        try {
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $stripeCustomerId,
                'return_url' => route('admin.subscription.manage'),
            ]);

            return redirect($session->url);

        } catch (ApiErrorException $e) {
            Log::error('Failed to create portal session', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to access billing portal.');
        }
    }

    /**
     * Handle Stripe webhooks for subscription events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);

            switch ($event->type) {
                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                case 'invoice.paid':
                    $this->handleInvoicePaid($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    /**
     * Get a plain object representing the current subscription state from BusinessSetting.
     */
    private function getSubscriptionData(): object
    {
        return (object) [
            'plan' => BusinessSetting::get('subscription_plan'),
            'stripe_customer_id' => BusinessSetting::get('stripe_customer_id'),
            'stripe_subscription_id' => BusinessSetting::get('stripe_subscription_id'),
            'trial_ends_at' => BusinessSetting::get('trial_ends_at'),
        ];
    }

    private function handleSubscriptionUpdated($subscription)
    {
        BusinessSetting::set('stripe_subscription_id', $subscription->id);
        BusinessSetting::set('subscription_plan', $subscription->metadata->plan ?? 'professional');

        Log::info('Subscription updated via webhook', [
            'subscription_id' => $subscription->id,
        ]);
    }

    private function handleSubscriptionDeleted($subscription)
    {
        BusinessSetting::set('stripe_subscription_id', null);
        BusinessSetting::set('subscription_plan', null);

        Log::info('Subscription deleted via webhook', [
            'subscription_id' => $subscription->id,
        ]);
    }

    private function handleInvoicePaid($invoice)
    {
        Log::info('Invoice paid', [
            'customer' => $invoice->customer,
            'amount' => $invoice->amount_paid / 100,
            'currency' => $invoice->currency,
        ]);
    }

    private function handlePaymentFailed($invoice)
    {
        Log::error('Payment failed', [
            'customer' => $invoice->customer,
            'amount' => $invoice->amount_due / 100,
            'currency' => $invoice->currency,
        ]);
    }
}
