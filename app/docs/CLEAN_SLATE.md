# Clean Slate — Module Documentation

> Data broker opt-out and digital reputation suppression service.
> Last updated: March 2026

---

## Overview

Clean Slate is a self-contained Laravel module that allows customers to subscribe to an ongoing data broker monitoring and opt-out service. Customers provide their personal information (name, DOB, emails, phone numbers, addresses), and the system tracks opt-out submission attempts across a curated list of data brokers.

It is embedded within the Portal 7 platform but architecturally isolated — own routes, controllers, middleware, models, views, jobs, migrations, and seeders — under `app/Modules/CleanSlate/`.

---

## Subscription Tiers

| Tier         | Price     | Brokers | Scan Frequency | Notes                                     |
|--------------|-----------|---------|----------------|-------------------------------------------|
| Basic        | $300/mo   | 18      | Monthly        | Web form opt-outs, email support          |
| Professional | $750/mo   | 24      | Weekly         | Email opt-outs included, priority support |
| Executive    | $1,500/mo | 25      | Continuous     | Manual removals, dedicated specialist     |

Pricing is managed via Stripe. Each tier maps to a Stripe Price ID configured in `.env`:

```
CLEANSLATE_STRIPE_PRICE_BASIC=price_...
CLEANSLATE_STRIPE_PRICE_PROFESSIONAL=price_...
CLEANSLATE_STRIPE_PRICE_EXECUTIVE=price_...
```

---

## User Journey

### Public
1. `/clean-slate` — marketing landing page with hero, features, how-it-works, 3-tier pricing
2. `/clean-slate/plans` — interactive plan selector, posts to Stripe Checkout
3. Intake/contact form — fallback for prospects not ready to subscribe; creates a lead in the admin dashboard

### Post-Subscription Onboarding (4 steps)
1. **Profile** — first name, last name, date of birth
2. **Contact** — email addresses, phone numbers (empties filtered automatically)
3. **Addresses** — current and past addresses for broker searches
4. **Confirm and Launch** — review data, dispatches `DispatchInitialScanJob`

### Customer Dashboard
- `/clean-slate/dashboard` — exposure score, scan status per broker, removal request tracking
- Scan statuses: `pending` > `running` > `completed` / `failed`
- Removal statuses: `pending` > `submitted` > `confirmed` / `failed`

---

## Architecture

### Module Location
```
app/Modules/CleanSlate/
├── CleanSlateServiceProvider.php
├── Config/cleanslate.php
├── Database/
│   ├── Migrations/
│   └── Seeders/
│       ├── DataBrokerSeeder.php
│       └── DemoCustomerSeeder.php
├── Enums/
│   ├── RemovalStatus.php
│   ├── ScanStatus.php
│   └── SubscriptionTier.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── BillingController.php
│   │   ├── DashboardController.php
│   │   └── OnboardingController.php
│   └── Middleware/
│       ├── EnsureSubscribed.php           — checks active 'cleanslate' Cashier subscription
│       └── EnsureOnboardingComplete.php
├── Jobs/
│   ├── DispatchInitialScanJob.php
│   ├── ProcessScanJob.php
│   └── SubmitRemovalJob.php
├── Models/
│   ├── DataBroker.php
│   ├── Profile.php
│   ├── RemovalRequest.php
│   └── ScanJob.php
├── Resources/Views/
│   ├── admin/
│   ├── billing/plans.blade.php
│   ├── dashboard/index.blade.php
│   └── onboarding/
│       ├── profile.blade.php
│       ├── contact.blade.php
│       ├── addresses.blade.php
│       └── confirm.blade.php
├── Routes/web.php
└── Services/SubscriptionService.php
```

### Key Design Decisions
- **Same `User` model** as the rest of the platform. A CleanSlate customer is a regular user with a `cleanslate` Cashier subscription row and a `Profile` record.
- **Subscription type key**: `'cleanslate'` (Cashier's `type` column). All checks must use `subscribed('cleanslate')` or `subscription('cleanslate')` — not the Cashier default `'default'`.
- **No webhook dependency for local dev**: The checkout success URL includes `{CHECKOUT_SESSION_ID}`; `BillingController::success()` syncs the subscription directly from the Stripe API before redirecting, so it works without `stripe listen`.
- **Tailwind scanning**: `app/Modules/**/*.blade.php` must remain in `tailwind.config.js` content paths (already configured).

### Route Groups
```
/clean-slate                   — public landing page
/clean-slate/plans             — public plan selector (no auth required)
/clean-slate/start             — smart entry redirect (auth required)
/clean-slate/checkout          — POST, initiates Stripe Checkout (auth required)
/clean-slate/billing/success   — Stripe return URL, syncs subscription
/clean-slate/onboarding/*      — requires auth + active subscription
/clean-slate/dashboard         — requires auth + subscription + completed onboarding
/admin/clean-slate/*           — requires company_administrator role
```

---

## Stripe Setup

1. Create 3 products in Stripe with monthly recurring prices
2. Copy the **Price IDs** (not product IDs — `price_...` not `prod_...`) into `.env`
3. For local dev, subscription syncs on checkout success without needing a webhook listener
4. For production, configure the Stripe webhook endpoint and set `STRIPE_WEBHOOK_SECRET`

Webhook endpoint: `POST /stripe/webhook`

To forward webhooks locally during testing:
```bash
stripe listen --forward-to prtl7-app.test/stripe/webhook
```

---

## Local Development

### Demo Users

Seeded by `DemoCustomerSeeder`. All have fake Cashier subscriptions (no real Stripe calls) and completed onboarding profiles with realistic scan/removal data.

| Email                       | Password                   | Tier         | Scenario           |
|-----------------------------|----------------------------|--------------|--------------------|
| demo@smbgen.com             | demo-password-local-only   | Professional | High exposure      |
| sarah.mitchell@demo.test    | password                   | Professional | High exposure      |
| marcus.delgado@demo.test    | password                   | Basic        | Low exposure       |
| jennifer.okafor@demo.test   | password                   | Executive    | Very high exposure |

Admin: `admin@smbgen.com` — admin panel at `/admin/clean-slate`

To reseed demo data without wiping the database:
```bash
php artisan db:seed --class="App\Modules\CleanSlate\Database\Seeders\DataBrokerSeeder"
php artisan db:seed --class="App\Modules\CleanSlate\Database\Seeders\DemoCustomerSeeder"
```

---

## Open Questions and Pre-Launch Considerations

### 1. Broker Automation — What Is Actually Implemented?
The scan/removal jobs exist as Laravel jobs but the actual broker interaction logic is stubbed. The team needs to define the submission mechanism per broker: web scraping, email template, certified mail, API (where available), or a mix. This is the largest open implementation gap.

### 2. Human vs. Automated Workflow
The Executive tier advertises "manual removals" and a "dedicated specialist." Is there a staff-facing workflow (task queue, assignment, internal notes) that needs to be built, or is that tier label aspirational at launch?

### 3. Tier Enforcement at the Broker Level
Basic covers 18 brokers, Professional 24, Executive 25 — but all active brokers are currently scanned for all tiers. Broker-to-tier mapping must be implemented before launch.

### 4. Recurring Scan Scheduler
`DispatchInitialScanJob` fires once at onboarding completion. Ongoing scans (monthly / weekly / continuous per tier) need a scheduled job that respects each tier's cadence. Nothing is scheduled yet.

### 5. Exposure Score Calculation
Currently a static integer seeded onto the `Profile` model. Define whether this is computed from scan results (and when it recalculates), or if it is operator-assigned.

### 6. Customer Notification Emails
No outbound email notifications exist yet for CleanSlate events (new listing found, removal confirmed, scan complete). Define which events should trigger customer communication.

### 7. PII Handling and Compliance
The module stores names, DOBs, email addresses, phone numbers, and physical addresses. CCPA, GDPR, and individual broker terms apply. Define: data retention policy, who has read access, audit logging requirements, and whether submitted opt-out content is stored.

### 8. Domain and Branding Strategy
Clean Slate currently lives under the same domain and auth as the main portal. Decide whether it stays co-located long-term or eventually spins off as a standalone product. The module architecture supports either path without major refactoring.
