# Stripe Payment Integration Guide

## Overview

SMBGen now uses **Stripe** as the primary payment processor, replacing QuickBooks for direct payment collection. This provides a simpler, more flexible payment experience with instant processing and real-time payment tracking.

## Features

### 1. **Simple Payment Collection**
- Standalone payment page at `/pay`
- No login required - perfect for quick payments
- Collects: amount, customer name, email, optional description
- Instant card processing with Stripe Elements
- Mobile-responsive design

### 2. **Invoice Payments**
- Generate payment links for any invoice
- Checkout sessions with line-item details
- Automatic invoice status updates when paid
- Payment tracking and receipts

### 3. **Webhook Integration**
- Real-time payment status updates
- Automatic invoice marking as paid
- Payment intent tracking
- Failed payment handling

## Setup Instructions

### 1. Get Stripe API Keys

1. Sign up at [https://stripe.com](https://stripe.com)
2. Get your API keys from the Stripe Dashboard
   - **Test mode**: Use for development
   - **Live mode**: Use for production

### 2. Configure Environment Variables

Add to your `.env` file:

```env
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 3. Set Up Webhooks (Production)

1. Go to Stripe Dashboard → Developers → Webhooks
2. Add endpoint: `https://yourdomain.com/stripe/webhook`
3. Select events to listen for:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
4. Copy the webhook signing secret to `STRIPE_WEBHOOK_SECRET`

## Usage Examples

### Simple Payment Collection

Direct customers to: `https://yourdomain.com/pay`

This shows a beautiful payment form where they can:
- Enter the amount to pay
- Provide their name and email
- Add an optional description
- Complete payment with their card

Perfect for:
- Quick service payments
- Collecting deposits
- One-time charges
- Point-of-sale transactions

### Invoice Payments

```php
use App\Services\StripeService;

$stripeService = new StripeService();

// Generate a payment link for an invoice
$successUrl = route('payment.success', ['invoice' => $invoice->id]);
$cancelUrl = route('payment.cancel', ['invoice' => $invoice->id]);

$session = $stripeService->createCheckoutSession($invoice, $successUrl, $cancelUrl);

// Share the payment URL with your client
$paymentUrl = $session['url'];
```

### Create Payment Intent (Custom Integration)

```php
use App\Services\StripeService;

$stripeService = new StripeService();

// Create a payment intent for manual integration
$paymentIntent = $stripeService->createPaymentIntent($invoice);

// Use the client secret in your frontend
$clientSecret = $paymentIntent['client_secret'];
```

## API Endpoints

### Public Routes (No Auth Required)

- `GET /pay` - Simple payment collection page
- `POST /pay/process` - Process simple payment
- `POST /stripe/webhook` - Stripe webhook handler

### Authenticated Routes

- `POST /payment/checkout` - Create checkout session
- `GET /payment/success` - Payment success page
- `GET /payment/cancel` - Payment cancellation page

## Database Schema

### Clients Table
```php
stripe_customer_id  // Stripe customer ID
```

### Invoices Table
```php
stripe_payment_intent_id      // Payment Intent ID
stripe_checkout_session_id    // Checkout Session ID
stripe_client_secret          // Client secret for frontend
stripe_payment_url            // Payment link URL
```

## Stripe Service Methods

### `findOrCreateCustomer(Client $client)`
Finds or creates a Stripe customer for a client.

### `createPaymentIntent(Invoice $invoice)`
Creates a payment intent for an invoice.

### `createCheckoutSession(Invoice $invoice, $successUrl, $cancelUrl)`
Creates a hosted checkout session with line items.

### `createPaymentLink(Invoice $invoice)`
Creates a shareable payment link.

### `handleSuccessfulPayment($paymentIntentId)`
Processes successful payment webhook.

### `refundPayment($paymentIntentId, $amount = null)`
Refunds a payment (full or partial).

### `testConnection()`
Tests Stripe API connection.

## Migration from QuickBooks

### What Changed

**Before (QuickBooks):**
- Required OAuth connection
- Manual invoice syncing
- External payment processing
- Token refresh complexity

**After (Stripe):**
- Simple API keys
- Direct payment processing
- Real-time payment status
- Automatic receipts

### Migration Steps

1. ✅ Install Stripe PHP SDK: `composer require stripe/stripe-php`
2. ✅ Run migration: `php artisan migrate`
3. ✅ Configure `.env` with Stripe keys
4. ✅ Test payment collection: Visit `/pay`
5. ✅ Set up webhooks for production
6. (Optional) Migrate historical invoice data

### Keeping QuickBooks (Optional)

You can keep QuickBooks for accounting while using Stripe for payments:
- Use Stripe for payment collection
- Sync paid invoices to QuickBooks for bookkeeping
- Best of both worlds: Easy payments + Professional accounting

## Testing

### Test Cards (Stripe Test Mode)

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Authentication Required**: `4000 0025 0000 3155`

Use any future expiration date and any 3-digit CVC.

### Test Webhook Locally

```bash
# Install Stripe CLI
# https://stripe.com/docs/stripe-cli

# Forward webhooks to local server
stripe listen --forward-to http://localhost:8000/stripe/webhook

# Trigger test events
stripe trigger payment_intent.succeeded
```

## Security Best Practices

1. **Never expose secret key** - Keep in `.env` file
2. **Validate webhooks** - Always verify webhook signatures
3. **Use HTTPS** - Required for production
4. **Implement CSRF protection** - Laravel does this automatically
5. **Log payment attempts** - Monitor for fraud
6. **Set up Stripe Radar** - Automatic fraud detection

## Pricing

Stripe charges:
- **2.9% + $0.30** per successful US card charge
- **3.4% + $0.30** for international cards
- **No monthly fees**
- **No setup fees**

## Support & Resources

- **Stripe Documentation**: [https://stripe.com/docs](https://stripe.com/docs)
- **Dashboard**: [https://dashboard.stripe.com](https://dashboard.stripe.com)
- **API Reference**: [https://stripe.com/docs/api](https://stripe.com/docs/api)
- **Test Cards**: [https://stripe.com/docs/testing](https://stripe.com/docs/testing)

## Troubleshooting

### "No such customer" error
**Solution**: The client's `stripe_customer_id` is invalid. Set it to `null` to create a new customer.

### Webhook signature verification failed
**Solution**: Make sure `STRIPE_WEBHOOK_SECRET` matches the webhook secret from Stripe Dashboard.

### Payment intent already succeeded
**Solution**: This happens if a webhook fires multiple times. Your code already handles this gracefully.

### Card declined
**Solution**: This is a legitimate decline. Ask the customer to try a different card or payment method.

## Next Steps

1. **Enable Payment Methods**: Add Apple Pay, Google Pay, ACH, etc.
2. **Subscription Billing**: Use Stripe Billing for recurring payments
3. **Payment Links**: Create shareable links without writing code
4. **Connect**: Allow clients to connect their own Stripe accounts
5. **Radar**: Enable advanced fraud detection

---

**Built with ❤️ using Stripe API v19**
