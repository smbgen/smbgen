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
        $tenant = tenant();
        
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
                    'Default subdomain',
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
                    'Custom domain support',
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
                    'Multiple custom domains',
                    'Advanced AI features',
                    'White-label options',
                    'Dedicated account manager',
                ],
            ],
        ];

        return view('admin.subscription.plans', compact('plans', 'tenant'));
    }

    /**
     * Create a new subscription via Stripe Checkout
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
        ]);

        $tenant = tenant();
        $user = auth()->user();

        try {
            // Get or create Stripe customer
            if (!$tenant->stripe_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => BusinessSetting::get('business_name', $user->name),
                    'metadata' => [
                        'tenant_id' => $tenant->id,
                        'user_id' => $user->id,
                    ],
                ]);
                
                $tenant->stripe_id = $customer->id;
                $tenant->save();
            }

            // Get price ID for the plan
            $priceId = config("services.stripe.plans.{$request->plan}");

            if (!$priceId) {
                return back()->with('error', 'Invalid plan selected.');
            }

            // Create Checkout Session
            $session = Session::create([
                'customer' => $tenant->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('admin.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('admin.subscription.plans'),
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan' => $request->plan,
                ],
            ]);

            return redirect($session->url);

        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription creation failed', [
                'tenant_id' => $tenant->id,
                'plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful subscription
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('admin.subscription.plans')
                ->with('error', 'Invalid session.');
        }

        try {
            $session = Session::retrieve($sessionId);
            $tenant = tenant();

            // Update tenant with subscription details
            $tenant->stripe_subscription_id = $session->subscription;
            $tenant->plan = $session->metadata->plan ?? 'professional';
            $tenant->trial_ends_at = null; // Clear trial
            $tenant->save();

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
        $tenant = tenant();
        $subscription = null;

        if ($tenant->stripe_subscription_id) {
            try {
                $subscription = Subscription::retrieve($tenant->stripe_subscription_id);
            } catch (ApiErrorException $e) {
                Log::error('Failed to retrieve subscription', [
                    'tenant_id' => $tenant->id,
                    'subscription_id' => $tenant->stripe_subscription_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('admin.subscription.manage', compact('tenant', 'subscription'));
    }

    /**
     * Upgrade or downgrade subscription plan
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
        ]);

        $tenant = tenant();

        if (!$tenant->stripe_subscription_id) {
            return back()->with('error', 'No active subscription found.');
        }

        try {
            $subscription = Subscription::retrieve($tenant->stripe_subscription_id);
            
            // Get new price ID
            $newPriceId = config("services.stripe.plans.{$request->plan}");

            if (!$newPriceId) {
                return back()->with('error', 'Invalid plan selected.');
            }

            // Update subscription
            Subscription::update($tenant->stripe_subscription_id, [
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'price' => $newPriceId,
                    ],
                ],
                'proration_behavior' => 'always_invoice',
            ]);

            // Update tenant plan
            $tenant->plan = $request->plan;
            $tenant->save();

            return back()->with('success', 'Plan updated successfully!');

        } catch (ApiErrorException $e) {
            Log::error('Failed to update subscription', [
                'tenant_id' => $tenant->id,
                'new_plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update plan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $tenant = tenant();

        if (!$tenant->stripe_subscription_id) {
            return back()->with('error', 'No active subscription found.');
        }

        try {
            // Cancel at period end (don't cancel immediately)
            $subscription = Subscription::update($tenant->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            Log::info('Subscription cancelled', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $tenant->stripe_subscription_id,
                'ends_at' => $subscription->current_period_end,
            ]);

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');

        } catch (ApiErrorException $e) {
            Log::error('Failed to cancel subscription', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate cancelled subscription
     */
    public function reactivate(Request $request)
    {
        $tenant = tenant();

        if (!$tenant->stripe_subscription_id) {
            return back()->with('error', 'No subscription found.');
        }

        try {
            Subscription::update($tenant->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            return back()->with('success', 'Subscription reactivated successfully!');

        } catch (ApiErrorException $e) {
            Log::error('Failed to reactivate subscription', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reactivate subscription: ' . $e->getMessage());
        }
    }

    /**
     * Redirect to Stripe Customer Portal
     */
    public function portal()
    {
        $tenant = tenant();

        if (!$tenant->stripe_id) {
            return back()->with('error', 'No Stripe customer found.');
        }

        try {
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $tenant->stripe_id,
                'return_url' => route('admin.subscription.manage'),
            ]);

            return redirect($session->url);

        } catch (ApiErrorException $e) {
            Log::error('Failed to create portal session', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to access billing portal.');
        }
    }

    /**
     * Dismiss trial banner
     */
    public function dismissTrialBanner(Request $request)
    {
        BusinessSetting::set('trial_banner_dismissed', true);

        return response()->json(['success' => true]);
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

            // Handle different event types
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
                'payload' => $payload,
            ]);

            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    /**
     * Handle subscription updated event
     */
    private function handleSubscriptionUpdated($subscription)
    {
        $tenantId = $subscription->metadata->tenant_id ?? null;

        if (!$tenantId) {
            return;
        }

        $tenant = \Stancl\Tenancy\Database\Models\Tenant::find($tenantId);

        if ($tenant) {
            $tenant->stripe_subscription_id = $subscription->id;
            $tenant->plan = $subscription->metadata->plan ?? 'professional';
            $tenant->save();

            Log::info('Subscription updated via webhook', [
                'tenant_id' => $tenantId,
                'subscription_id' => $subscription->id,
            ]);
        }
    }

    /**
     * Handle subscription deleted event
     */
    private function handleSubscriptionDeleted($subscription)
    {
        $tenantId = $subscription->metadata->tenant_id ?? null;

        if (!$tenantId) {
            return;
        }

        $tenant = \Stancl\Tenancy\Database\Models\Tenant::find($tenantId);

        if ($tenant) {
            $tenant->stripe_subscription_id = null;
            $tenant->plan = null;
            $tenant->save();

            Log::info('Subscription deleted via webhook', [
                'tenant_id' => $tenantId,
            ]);
        }
    }

    /**
     * Handle invoice paid event
     */
    private function handleInvoicePaid($invoice)
    {
        Log::info('Invoice paid', [
            'customer' => $invoice->customer,
            'amount' => $invoice->amount_paid / 100,
            'currency' => $invoice->currency,
        ]);
    }

    /**
     * Handle payment failed event
     */
    private function handlePaymentFailed($invoice)
    {
        Log::error('Payment failed', [
            'customer' => $invoice->customer,
            'amount' => $invoice->amount_due / 100,
            'currency' => $invoice->currency,
        ]);

        // TODO: Send email notification to tenant about failed payment
    }
}
