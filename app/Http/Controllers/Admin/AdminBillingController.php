<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMailable;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminBillingController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['user', 'items'])->orderBy('created_at', 'desc')->paginate(20);

        // Get Stripe connection status
        $stripeService = app(StripeService::class);
        $stripeStatus = $stripeService->testConnection();

        return view('admin.billing.index', compact('invoices', 'stripeStatus'));
    }

    public function show(User $user)
    {
        $invoices = Invoice::with('items')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('admin.billing.show', compact('user', 'invoices'));
    }

    public function create(?User $user = null, ?Request $request = null)
    {
        if ($user) {
            // User pre-selected, show create form with user
            return view('admin.billing.create', compact('user'));
        }

        // No user pre-selected, show client selection
        $clients = User::whereIn('role', [User::ROLE_CLIENT, User::ROLE_USER])
            ->orderBy('name')
            ->get();

        return view('admin.billing.create', compact('clients'));
    }

    public function store(Request $request, ?User $user = null)
    {
        $data = $request->validate([
            'user_id' => $user ? 'nullable' : 'required|exists:users,id',
            'memo' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:draft,sent',
            'generate_payment_link' => 'nullable|boolean',
            'send_email' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_amount' => 'required|numeric|min:0',
        ]);

        $userId = $user ? $user->id : $data['user_id'];
        $selectedUser = $user ?: User::findOrFail($userId);

        $invoice = Invoice::create([
            'user_id' => $userId,
            'status' => $data['status'] ?? Invoice::STATUS_DRAFT,
            'currency' => 'usd',
            'memo' => $data['memo'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'total_amount' => 0,
        ]);

        foreach ($data['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_amount' => (int) ($item['unit_amount'] * 100), // Convert to cents
                'total_amount' => (int) ($item['quantity'] * $item['unit_amount'] * 100),
            ]);
        }

        $invoice->recalculateTotals();

        // Generate payment link if requested
        if (! empty($data['generate_payment_link'])) {
            $stripeService = app(\App\Services\StripeService::class);
            if ($stripeService->isConfigured()) {
                $successUrl = url('/payment/success?invoice_id='.$invoice->id);
                $cancelUrl = url('/payment/cancel?invoice_id='.$invoice->id);
                $stripeService->createCheckoutSession($invoice, $successUrl, $cancelUrl);
                $invoice->refresh();
            }
        }

        // Send email if requested
        if (! empty($data['send_email']) && $invoice->hasStripePaymentUrl()) {
            try {
                Mail::to($selectedUser->email)->send(new InvoiceMailable($invoice->fresh(['user', 'items'])));
                $invoice->update(['sent_at' => now(), 'status' => Invoice::STATUS_SENT]);
            } catch (\Exception $e) {
                \Log::error('Failed to send invoice email', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.billing.show', $selectedUser)
            ->with('success', 'Invoice created successfully!'.($invoice->hasStripePaymentUrl() ? ' Payment link generated.' : ''));
    }

    public function sendInvoice(Invoice $invoice)
    {
        $invoice->load('user', 'items');

        // Ensure recipient exists
        if (empty($invoice->user) || empty($invoice->user->email)) {
            \Illuminate\Support\Facades\Log::warning('Attempted to send invoice with missing recipient', ['invoice_id' => $invoice->id]);

            return back()->with('error', 'Invoice has no recipient email configured.');
        }

        try {
            Mail::to($invoice->user->email)->send(new InvoiceMailable($invoice));

            $invoice->update(['sent_at' => now(), 'status' => $invoice->status === Invoice::STATUS_DRAFT ? Invoice::STATUS_SENT : $invoice->status]);

            return back()->with('success', 'Invoice emailed to client.');
        } catch (\Exception $e) {
            // Log detailed error for diagnostics and return friendly message
            \Illuminate\Support\Facades\Log::error('Failed to send invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to send invoice email. Please check mail configuration and try again.');
        } catch (\Throwable $t) {
            \Illuminate\Support\Facades\Log::error('Unexpected error sending invoice', ['invoice_id' => $invoice->id, 'error' => $t->getMessage()]);

            return back()->with('error', 'An unexpected error occurred while sending the invoice.');
        }
    }

    /**
     * Generate Stripe payment link for invoice
     */
    public function generateStripePaymentLink(Invoice $invoice, StripeService $stripeService)
    {
        $invoice->load('user', 'items');

        // Check if Stripe is configured
        if (! $stripeService->isConfigured()) {
            return back()->with('error', 'Stripe is not configured. Please add STRIPE_PUBLIC_KEY and STRIPE_SECRET_KEY to your .env file.');
        }

        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('info', 'Invoice is already paid.');
        }

        if (empty($invoice->items) || $invoice->items->count() === 0) {
            return back()->with('error', 'Cannot generate payment link for invoice with no items.');
        }

        $successUrl = url('/payment/success?invoice_id='.$invoice->id);
        $cancelUrl = url('/payment/cancel?invoice_id='.$invoice->id);

        $session = $stripeService->createCheckoutSession($invoice, $successUrl, $cancelUrl);

        if (! $session) {
            return back()->with('error', 'Failed to generate Stripe payment link. Please check your Stripe configuration.');
        }

        return back()->with('success', 'Stripe payment link generated successfully!')->with('payment_url', $session['url']);
    }

    /**
     * Send invoice with Stripe payment link
     */
    public function sendStripeInvoice(Invoice $invoice, StripeService $stripeService)
    {
        $invoice->load('user', 'items');

        // Check if Stripe is configured
        if (! $stripeService->isConfigured()) {
            return back()->with('error', 'Stripe is not configured. Please add STRIPE_PUBLIC_KEY and STRIPE_SECRET_KEY to your .env file.');
        }

        if (empty($invoice->user) || empty($invoice->user->email)) {
            return back()->with('error', 'Invoice has no recipient email configured.');
        }

        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('info', 'Invoice is already paid.');
        }

        // Generate Stripe payment link if not exists
        if (! $invoice->stripe_payment_url) {
            $successUrl = url('/payment/success?invoice_id='.$invoice->id);
            $cancelUrl = url('/payment/cancel?invoice_id='.$invoice->id);

            $session = $stripeService->createCheckoutSession($invoice, $successUrl, $cancelUrl);

            if (! $session) {
                return back()->with('error', 'Failed to generate Stripe payment link. Please check your Stripe configuration.');
            }

            $invoice->refresh();
        }

        // Send email
        try {
            Mail::to($invoice->user->email)->send(new InvoiceMailable($invoice));

            $invoice->update([
                'sent_at' => now(),
                'status' => $invoice->status === Invoice::STATUS_DRAFT ? Invoice::STATUS_SENT : $invoice->status,
            ]);

            return back()->with('success', 'Invoice with Stripe payment link sent to client!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send Stripe invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send invoice email: '.$e->getMessage());
        }
    }

    /**
     * Refund a Stripe payment
     */
    public function refundPayment(Invoice $invoice, Request $request, StripeService $stripeService)
    {
        if (! $invoice->stripe_payment_intent_id) {
            return back()->with('error', 'No Stripe payment found for this invoice.');
        }

        if ($invoice->status !== Invoice::STATUS_PAID) {
            return back()->with('error', 'Only paid invoices can be refunded.');
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $refundAmount = $validated['amount'] ?? null;
            if ($refundAmount) {
                $refundAmount = (int) ($refundAmount * 100); // Convert to cents
            }

            $refund = $stripeService->refundPayment($invoice->stripe_payment_intent_id, $refundAmount);

            if (! $refund) {
                return back()->with('error', 'Failed to process refund.');
            }

            // Update invoice status
            $invoice->update([
                'status' => Invoice::STATUS_DRAFT, // Or create a new REFUNDED status
                'paid_at' => null,
            ]);

            \Illuminate\Support\Facades\Log::info('Invoice refunded', [
                'invoice_id' => $invoice->id,
                'refund_id' => $refund['id'],
                'amount' => $refund['amount'],
            ]);

            $amountFormatted = number_format($refund['amount'] / 100, 2);

            return back()->with('success', "Refund of \${$amountFormatted} processed successfully!");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to refund payment', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to process refund: '.$e->getMessage());
        }
    }

    /**
     * Delete an invoice
     */
    public function destroy(Invoice $invoice)
    {
        try {
            // Prevent deletion of paid invoices with Stripe payments
            if ($invoice->status === Invoice::STATUS_PAID && $invoice->stripe_payment_intent_id) {
                return back()->with('error', 'Cannot delete paid invoices. Please refund the payment first.');
            }

            $invoiceId = $invoice->id;
            $userName = $invoice->user->name ?? 'Unknown';

            // Delete related items first (cascade should handle this, but being explicit)
            $invoice->items()->delete();

            // Delete the invoice
            $invoice->delete();

            \Illuminate\Support\Facades\Log::info('Invoice deleted', [
                'invoice_id' => $invoiceId,
                'user_name' => $userName,
                'deleted_by' => auth()->id(),
            ]);

            return back()->with('success', "Invoice #{$invoiceId} deleted successfully.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete invoice: '.$e->getMessage());
        }
    }
}
