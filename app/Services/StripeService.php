<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripeService
{
    protected ?StripeClient $stripe = null;

    protected bool $isConfigured = false;

    public function __construct()
    {
        $secretKey = config('services.stripe.secret');

        // Check if Stripe is properly configured
        if (empty($secretKey) || $secretKey === '' || str_starts_with($secretKey, 'sk_') === false) {
            $this->isConfigured = false;

            return;
        }

        try {
            Stripe::setApiKey($secretKey);
            $this->stripe = new StripeClient($secretKey);
            $this->isConfigured = true;
        } catch (\Exception $e) {
            Log::error('Stripe initialization error: '.$e->getMessage());
            $this->isConfigured = false;
        }
    }

    /**
     * Check if Stripe is properly configured
     */
    public function isConfigured(): bool
    {
        return $this->isConfigured;
    }

    /**
     * Create or retrieve a Stripe customer
     */
    public function findOrCreateCustomer(User $user): ?string
    {
        if (! $this->isConfigured) {
            return null;
        }

        try {
            // Check if user already has a Stripe customer ID
            if ($user->stripe_customer_id) {
                return $user->stripe_customer_id;
            }

            // Search for existing customer by email
            $customers = $this->stripe->customers->search([
                'query' => "email:'{$user->email}'",
                'limit' => 1,
            ]);

            if (count($customers->data) > 0) {
                $customerId = $customers->data[0]->id;
                $user->update(['stripe_customer_id' => $customerId]);

                return $customerId;
            }

            // Create new customer
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone ?? null,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);

            return $customer->id;
        } catch (Exception $e) {
            Log::error('Stripe Create Customer Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Create a payment intent for an invoice
     */
    public function createPaymentIntent(Invoice $invoice): ?array
    {
        if (! $this->isConfigured) {
            return null;
        }

        try {
            $user = $invoice->user;
            $customerId = $this->findOrCreateCustomer($user);

            if (! $customerId) {
                return null;
            }

            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $invoice->total_amount, // Amount in cents
                'currency' => strtolower($invoice->currency ?? 'usd'),
                'customer' => $customerId,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                ],
                'description' => $invoice->memo ?? "Invoice #{$invoice->id}",
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Save payment intent ID to invoice
            $invoice->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_client_secret' => $paymentIntent->client_secret,
            ]);

            return [
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Create Payment Intent Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Create a Checkout Session for simple payment collection
     */
    public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl): ?array
    {
        if (! $this->isConfigured) {
            return null;
        }

        try {
            $user = $invoice->user;
            $customerId = $this->findOrCreateCustomer($user);

            // Build line items from invoice items
            $lineItems = [];
            foreach ($invoice->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => strtolower($invoice->currency ?? 'usd'),
                        'unit_amount' => $item->unit_amount, // Amount in cents
                        'product_data' => [
                            'name' => $item->description,
                        ],
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            $session = $this->stripe->checkout->sessions->create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                ],
            ]);

            // Save session ID to invoice
            $invoice->update([
                'stripe_checkout_session_id' => $session->id,
                'stripe_payment_url' => $session->url,
            ]);

            return [
                'session_id' => $session->id,
                'url' => $session->url,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Create Checkout Session Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Retrieve payment intent status
     */
    public function getPaymentIntentStatus(string $paymentIntentId): ?array
    {
        if (! $this->isConfigured) {
            return null;
        }

        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            return [
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'amount_received' => $paymentIntent->amount_received,
                'currency' => $paymentIntent->currency,
                'created' => $paymentIntent->created,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Get Payment Intent Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Handle successful payment webhook
     */
    public function handleSuccessfulPayment(string $paymentIntentId): bool
    {
        try {
            $invoice = Invoice::where('stripe_payment_intent_id', $paymentIntentId)->first();

            if (! $invoice) {
                Log::warning("Invoice not found for payment intent: {$paymentIntentId}");

                return false;
            }

            $invoice->update([
                'status' => Invoice::STATUS_PAID,
                'paid_at' => now(),
            ]);

            // Create payment record
            $invoice->payments()->create([
                'amount' => $invoice->total_amount,
                'payment_method' => 'stripe',
                'transaction_id' => $paymentIntentId,
                'paid_at' => now(),
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Stripe Handle Payment Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Create a payment link for quick payment collection
     */
    public function createPaymentLink(Invoice $invoice): ?string
    {
        if (! $this->isConfigured) {
            return null;
        }

        try {
            // Build line items
            $lineItems = [];
            foreach ($invoice->items as $item) {
                // Create a price for each item
                $price = $this->stripe->prices->create([
                    'currency' => strtolower($invoice->currency ?? 'usd'),
                    'unit_amount' => $item->unit_amount,
                    'product_data' => [
                        'name' => $item->description,
                    ],
                ]);

                $lineItems[] = [
                    'price' => $price->id,
                    'quantity' => $item->quantity,
                ];
            }

            $paymentLink = $this->stripe->paymentLinks->create([
                'line_items' => $lineItems,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                ],
            ]);

            $invoice->update([
                'stripe_payment_url' => $paymentLink->url,
            ]);

            return $paymentLink->url;
        } catch (Exception $e) {
            Log::error('Stripe Create Payment Link Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentIntentId, ?int $amount = null): ?array
    {
        if (! $this->isConfigured) {
            return null;
        }

        try {
            $refund = $this->stripe->refunds->create([
                'payment_intent' => $paymentIntentId,
                'amount' => $amount, // If null, full refund
            ]);

            return [
                'id' => $refund->id,
                'amount' => $refund->amount,
                'status' => $refund->status,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Refund Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get customer payment methods
     */
    public function getCustomerPaymentMethods(string $customerId): array
    {
        if (! $this->isConfigured) {
            return [];
        }

        try {
            $paymentMethods = $this->stripe->paymentMethods->all([
                'customer' => $customerId,
                'type' => 'card',
            ]);

            return array_map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'brand' => $pm->card->brand,
                    'last4' => $pm->card->last4,
                    'exp_month' => $pm->card->exp_month,
                    'exp_year' => $pm->card->exp_year,
                ];
            }, $paymentMethods->data);
        } catch (Exception $e) {
            Log::error('Stripe Get Payment Methods Error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Test Stripe connection
     */
    public function testConnection(): array
    {
        if (! $this->isConfigured) {
            return [
                'success' => false,
                'message' => 'Stripe is not configured. Please add your API keys to the .env file.',
            ];
        }

        try {
            $balance = $this->stripe->balance->retrieve();

            return [
                'success' => true,
                'message' => 'Successfully connected to Stripe',
                'data' => [
                    'available' => $balance->available,
                    'pending' => $balance->pending,
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to connect: '.$e->getMessage(),
            ];
        }
    }
}
