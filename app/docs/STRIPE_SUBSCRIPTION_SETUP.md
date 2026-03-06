# Stripe Subscription Configuration Guide

## Environment Variables

Add these to your `.env` file:

```env
# Stripe API Keys
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx

# Stripe Price IDs (from your Stripe Dashboard)
STRIPE_PRICE_STARTER=price_xxxxxxxxxxxxx
STRIPE_PRICE_PROFESSIONAL=price_xxxxxxxxxxxxx
STRIPE_PRICE_ENTERPRISE=price_xxxxxxxxxxxxx

# Server IP for DNS configuration (for domain setup)
APP_SERVER_IP=203.0.113.10
```

## Stripe Dashboard Setup

### 1. Create Products and Prices

1. Log in to your [Stripe Dashboard](https://dashboard.stripe.com/)
2. Go to **Products** → **Add product**
3. Create three products:

#### Starter Plan
- **Name**: smbgen Starter
- **Description**: Basic features for small businesses
- **Pricing**: $29/month recurring
- **Copy the Price ID** to `STRIPE_PRICE_STARTER`

#### Professional Plan
- **Name**: smbgen Professional
- **Description**: Advanced features with custom domains
- **Pricing**: $79/month recurring
- **Copy the Price ID** to `STRIPE_PRICE_PROFESSIONAL`

#### Enterprise Plan
- **Name**: smbgen Enterprise
- **Description**: Full-featured plan with priority support
- **Pricing**: $199/month recurring
- **Copy the Price ID** to `STRIPE_PRICE_ENTERPRISE`

### 2. Configure Webhook

1. Go to **Developers** → **Webhooks**
2. Click **Add endpoint**
3. Set endpoint URL: `https://yourdomain.com/webhooks/stripe`
4. Select events to listen to:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.paid`
   - `invoice.payment_failed`
5. Copy the **Signing secret** to `STRIPE_WEBHOOK_SECRET`

### 3. Enable Customer Portal

1. Go to **Settings** → **Billing** → **Customer portal**
2. Enable the portal
3. Configure settings:
   - ✅ Allow customers to update payment methods
   - ✅ Allow customers to view invoice history
   - ✅ Allow customers to cancel subscriptions
   - Set cancellation behavior to "Cancel at period end"

## Testing

### Test Mode

Use Stripe test mode during development:

```env
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
```

### Test Cards

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Requires authentication**: `4000 0027 6000 3184`

Use any future expiry date, any 3-digit CVC, and any ZIP code.

### Webhook Testing

Install Stripe CLI for local webhook testing:

```bash
stripe listen --forward-to localhost:8000/webhooks/stripe
```

## Database Requirements

Ensure your `tenants` table has these columns:

```php
$table->string('stripe_id')->nullable();
$table->string('stripe_subscription_id')->nullable();
$table->string('plan')->nullable();
$table->timestamp('trial_ends_at')->nullable();
$table->string('primary_domain')->nullable();
```

If missing, create a migration:

```bash
php artisan make:migration add_stripe_fields_to_tenants_table
```

## Features

### Subscription Management
- ✅ Multi-tier pricing (Starter/Professional/Enterprise)
- ✅ Stripe Checkout integration
- ✅ Customer Portal for self-service
- ✅ Plan upgrades and downgrades
- ✅ Cancel at period end
- ✅ Automatic subscription renewal

### Trial System
- ✅ Dashboard banner with countdown
- ✅ Color-coded urgency (green/yellow/red)
- ✅ Dismissible notification
- ✅ Automatic trial expiration

### Domain Management
- ✅ Add custom domains
- ✅ Set primary domain
- ✅ DNS verification
- ✅ Setup guide included

## Usage

### For Tenants

**View Plans:**
```
/admin/subscription/plans
```

**Manage Subscription:**
```
/admin/subscription/manage
```

**Manage Domains:**
```
/admin/domains
```

### For Super Admins

**Extend Trial:**
```php
POST /super-admin/tenants/{tenant}/extend-trial
Body: { days: 30 }
```

**Upgrade Plan:**
```php
POST /super-admin/tenants/{tenant}/upgrade
Body: { plan: 'professional' }
```

## Security

- Webhook requests are verified using Stripe signature
- Subscription changes logged to application logs
- Failed payments trigger email notifications (configure in controller)

## Troubleshooting

### Webhook not firing
- Check webhook URL is publicly accessible
- Verify `STRIPE_WEBHOOK_SECRET` is correct
- Check Laravel logs for errors

### Subscription not updating
- Verify metadata includes `tenant_id`
- Check webhook events are being received
- Review Stripe Dashboard → Developers → Events

### Trial banner not showing
- Check `trial_ends_at` is set on tenant
- Verify date is in the future
- Clear browser cache

## Production Checklist

- [ ] Switch to live Stripe keys
- [ ] Update webhook endpoint to production URL
- [ ] Create live products and prices
- [ ] Update Price IDs in `.env`
- [ ] Test full subscription flow
- [ ] Configure Customer Portal settings
- [ ] Set up payment failure email notifications
- [ ] Monitor Stripe Dashboard for issues

## Support

For Stripe-specific questions, consult:
- [Stripe Documentation](https://stripe.com/docs)
- [Stripe PHP Library](https://github.com/stripe/stripe-php)
- [Stripe Checkout](https://stripe.com/docs/checkout)
- [Customer Portal](https://stripe.com/docs/billing/subscriptions/customer-portal)
