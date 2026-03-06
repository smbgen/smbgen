<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripePaymentController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show Stripe connection status
     */
    public function index()
    {
        $testConnection = $this->stripeService->testConnection();

        return view('admin.stripe.index', [
            'connected' => $testConnection['success'],
            'connectionData' => $testConnection['data'] ?? null,
        ]);
    }

    /**
     * Generate payment link for an invoice
     */
    public function generatePaymentLink(Invoice $invoice)
    {
        $successUrl = route('payment.success', ['invoice' => $invoice->id]);
        $cancelUrl = route('payment.cancel', ['invoice' => $invoice->id]);

        $session = $this->stripeService->createCheckoutSession($invoice, $successUrl, $cancelUrl);

        if (! $session) {
            return back()->with('error', 'Failed to generate payment link');
        }

        return back()->with('success', 'Payment link generated successfully!')->with('payment_url', $session['url']);
    }

    /**
     * Create payment intent for invoice
     */
    public function createPaymentIntent(Invoice $invoice)
    {
        $paymentIntent = $this->stripeService->createPaymentIntent($invoice);

        if (! $paymentIntent) {
            return response()->json(['error' => 'Failed to create payment intent'], 500);
        }

        return response()->json($paymentIntent);
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->stripeService->handleSuccessfulPayment($paymentIntent->id);
                break;

            case 'checkout.session.completed':
                $session = $event->data->object;
                $invoiceId = $session->metadata->invoice_id ?? null;

                if ($invoiceId) {
                    $invoice = Invoice::find($invoiceId);
                    if ($invoice) {
                        $invoice->update([
                            'status' => Invoice::STATUS_PAID,
                            'paid_at' => now(),
                        ]);

                        // Create payment record
                        $invoice->payments()->create([
                            'amount' => $invoice->total_amount,
                            'payment_method' => 'stripe',
                            'transaction_id' => $session->payment_intent,
                            'paid_at' => now(),
                        ]);
                    }
                }
                break;

            default:
                \Log::info('Unhandled Stripe webhook event: '.$event->type);
        }

        return response()->json(['status' => 'success']);
    }
}
