<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $invoices = Invoice::with('items')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('billing.index', compact('invoices', 'payments'));
    }

    public function pay(Invoice $invoice, Request $request)
    {
        $this->authorize('view', $invoice);

        if ($invoice->status === \App\Models\Invoice::STATUS_PAID) {
            return back()->with('info', 'Invoice already paid.');
        }

        try {
            \Stripe\Stripe::setApiKey(config('business.integrations.stripe.secret_key'));

            $payment = Payment::create([
                'user_id' => auth()->id(),
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
                'description' => 'Invoice #'.$invoice->id,
                'payment_type' => 'invoice',
                'status' => 'pending',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                ],
            ]);

            $lineItems = [];
            foreach ($invoice->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => $invoice->currency,
                        'product_data' => ['name' => $item->description],
                        'unit_amount' => $item->unit_amount,
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            if (empty($lineItems)) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => $invoice->currency,
                        'product_data' => ['name' => $invoice->memo ?: ('Invoice #'.$invoice->id)],
                        'unit_amount' => $invoice->total_amount,
                    ],
                    'quantity' => 1,
                ];
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.index'),
                'customer_email' => auth()->user()->email,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'payment_type' => 'invoice',
                ],
            ]);

            $payment->update(['stripe_session_id' => $session->id]);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            Log::error('Invoice pay error: '.$e->getMessage());

            return back()->with('error', 'Unable to initiate payment.');
        }
    }

    public function payPublic(Invoice $invoice, Request $request)
    {
        if ($invoice->status === \App\Models\Invoice::STATUS_PAID) {
            return redirect()->route('login')->with('info', 'Invoice already paid.');
        }

        try {
            \Stripe\Stripe::setApiKey(config('business.integrations.stripe.secret_key'));

            $payment = Payment::create([
                'user_id' => $invoice->user_id,
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
                'description' => 'Invoice #'.$invoice->id,
                'payment_type' => 'invoice',
                'status' => 'pending',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'public_payment' => true,
                ],
            ]);

            $lineItems = [];
            foreach ($invoice->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => $invoice->currency,
                        'product_data' => ['name' => $item->description],
                        'unit_amount' => $item->unit_amount,
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            if (empty($lineItems)) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => $invoice->currency,
                        'product_data' => ['name' => $invoice->memo ?: ('Invoice #'.$invoice->id)],
                        'unit_amount' => $invoice->total_amount,
                    ],
                    'quantity' => 1,
                ];
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('login'),
                'customer_email' => $invoice->user->email,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'payment_type' => 'invoice',
                    'public_payment' => 'true',
                ],
            ]);

            $payment->update(['stripe_session_id' => $session->id]);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            Log::error('Public invoice pay error: '.$e->getMessage());

            return redirect()->route('login')->with('error', 'Unable to initiate payment.');
        }
    }
}
