<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('business.integrations.stripe.secret_key'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:100', // Minimum $1.00
            'description' => 'required|string|max:255',
            'payment_type' => 'required|string|in:invoice,product,subscription',
            'success_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
        ]);

        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'currency' => 'usd',
                'description' => $request->description,
                'payment_type' => $request->payment_type,
                'status' => 'pending',
                'metadata' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            // Create Stripe checkout session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $request->description,
                            ],
                            'unit_amount' => $request->amount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $request->success_url ?? route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->cancel_url ?? route('payment.cancel'),
                'customer_email' => auth()->user()->email ?? $request->email,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'payment_type' => $request->payment_type,
                ],
            ]);

            // Update payment with session ID
            $payment->update(['stripe_session_id' => $session->id]);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment checkout error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('dashboard')->with('error', 'Payment verification failed');
        }

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $payment = Payment::where('stripe_session_id', $sessionId)->first();

            if (! $payment) {
                return redirect()->route('dashboard')->with('error', 'Payment not found');
            }

            if ($session->payment_status === 'paid') {
                $payment->update([
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'status' => 'completed',
                ]);

                // If linked to an invoice, mark it paid
                if ($payment->invoice_id) {
                    $invoice = \App\Models\Invoice::find($payment->invoice_id);
                    if ($invoice) {
                        $invoice->status = \App\Models\Invoice::STATUS_PAID;
                        $invoice->paid_at = now();
                        $invoice->save();
                    }
                }

                return redirect()->route('dashboard')->with('success', 'Payment successful! Thank you for your purchase.');
            }

            return redirect()->route('dashboard')->with('error', 'Payment was not completed');

        } catch (\Exception $e) {
            Log::error('Payment success verification error: '.$e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Payment verification failed');
        }
    }

    public function cancel()
    {
        return redirect()->route('dashboard')->with('info', 'Payment was cancelled');
    }

    /**
     * Show simple payment collection page
     */
    public function collect()
    {
        return view('payment.collect');
    }

    /**
     * Handle successful payment confirmation from frontend
     */
    public function confirmPayment(Request $request)
    {
        $validated = $request->validate([
            'invoiceId' => 'required|exists:invoices,id',
        ]);

        try {
            $invoice = \App\Models\Invoice::with('user')->find($validated['invoiceId']);

            if (! $invoice || $invoice->status === \App\Models\Invoice::STATUS_PAID) {
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            // Update invoice status
            $invoice->update([
                'status' => \App\Models\Invoice::STATUS_PAID,
                'paid_at' => now(),
                'stripe_payment_url' => 'direct',
            ]);

            // Send invoice email (listeners automatically handle tracking)
            \Mail::to($invoice->user->email)->send(new \App\Mail\InvoiceMailable($invoice));

            Log::info('Payment success processed', [
                'invoice_id' => $invoice->id,
                'email_sent' => true,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Payment success handling failed', [
                'invoice_id' => $validated['invoiceId'],
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process simple payment
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.50',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            // Find or create user for this email
            $user = \App\Models\User::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'role' => \App\Models\User::ROLE_CLIENT,
                    'password' => bcrypt(str()->random(32)),
                ]
            );

            // Create invoice
            $invoice = \App\Models\Invoice::create([
                'user_id' => $user->id,
                'status' => \App\Models\Invoice::STATUS_SENT,
                'currency' => 'usd',
                'memo' => $validated['description'] ?? 'Quick Payment Collection',
                'total_amount' => (int) ($validated['amount'] * 100),
            ]);

            // Create invoice item
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $validated['description'] ?? 'Payment',
                'quantity' => 1,
                'unit_amount' => (int) ($validated['amount'] * 100),
                'total_amount' => (int) ($validated['amount'] * 100),
            ]);

            // Create or find Stripe customer
            $stripeService = app(\App\Services\StripeService::class);
            $customerId = $stripeService->findOrCreateCustomer($user);

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int) ($validated['amount'] * 100), // Convert to cents
                'currency' => 'usd',
                'customer' => $customerId,
                'receipt_email' => $validated['email'],
                'description' => $validated['description'] ?? 'Payment via CLIENTBRIDGE',
                'metadata' => [
                    'customer_name' => $validated['name'],
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Save payment intent to invoice
            $invoice->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_client_secret' => $paymentIntent->client_secret,
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'invoiceId' => $invoice->id,
                'email' => $validated['email'],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment processing error: '.$e->getMessage());

            return response()->json([
                'error' => 'Payment failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('business.integrations.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);

            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;
                default:
                    Log::info('Unhandled webhook event type: '.$event->type);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    private function handleCheckoutSessionCompleted($session)
    {
        Log::info('Checkout session completed', ['session_id' => $session->id]);

        $payment = Payment::where('stripe_session_id', $session->id)->first();
        if ($payment) {
            $payment->update([
                'stripe_payment_intent_id' => $session->payment_intent,
                'status' => 'completed',
            ]);

            if ($payment->invoice_id) {
                $invoice = \App\Models\Invoice::find($payment->invoice_id);
                if ($invoice) {
                    $invoice->status = \App\Models\Invoice::STATUS_PAID;
                    $invoice->paid_at = now();
                    $invoice->save();
                }
            }
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', ['payment_intent_id' => $paymentIntent->id]);

        // Update invoice if linked
        if (isset($paymentIntent->metadata->invoice_id)) {
            $invoice = \App\Models\Invoice::find($paymentIntent->metadata->invoice_id);
            if ($invoice && $invoice->status !== \App\Models\Invoice::STATUS_PAID) {
                $invoice->update([
                    'status' => \App\Models\Invoice::STATUS_PAID,
                    'paid_at' => now(),
                    'stripe_payment_url' => 'direct', // Mark as paid via direct Stripe payment
                ]);

                // Send invoice email (listeners automatically handle tracking)
                try {
                    $invoice->load('user');
                    \Mail::to($invoice->user->email)->send(new \App\Mail\InvoiceMailable($invoice));

                    Log::info('Invoice email sent', [
                        'invoice_id' => $invoice->id,
                        'email' => $invoice->user->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send invoice email', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        Log::info('Payment intent failed', ['payment_intent_id' => $paymentIntent->id]);

        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if ($payment) {
            $payment->update(['status' => 'failed']);
        }
    }
}
