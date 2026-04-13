# SaaS Product Module — Module Documentation

> Data broker opt-out and digital reputation suppression service.
> Last updated: March 2026

---

## Team Briefing

### What It Is

SaaS Product Module is a **data broker opt-out / digital reputation suppression service** embedded as a module within the Portal 7 platform. Customers subscribe, provide their personal info, and the system tracks opt-out requests submitted to data brokers on their behalf. Each subscriber gets a personal opt-out specialist and ongoing monitoring for new listings that appear after initial removal.

### What Is Built

**Public-Facing**
- Marketing landing page with hero, features, how-it-works, and a 3-tier pricing section
- Interactive plan selection page routing directly into Stripe Checkout
- Intake/contact fallback form for prospects not ready to subscribe — lands as a lead in the admin dashboard

**Subscription and Billing**
- Stripe Checkout integration via Laravel Cashier
- Checkout session ID passed back on success so the subscription activates without needing a webhook forwarded locally
- Webhooks sync in production as a secondary path

**Onboarding (post-subscription, 4 steps)**
1. Profile — name, date of birth
2. Contact — email addresses, phone numbers
3. Addresses — past/current addresses for broker searches
4. Confirm and Launch — reviews data, kicks off initial scan job

**Customer Dashboard**
- Scan job status per data broker
- Removal request tracking (pending / submitted / confirmed / failed)
- Exposure score

**Admin Panel**
- `/admin/saas-product-module` — view all customers, profiles, scan status
- Broker management — toggle brokers active/inactive, update metadata
- Debug view for dev inspection

**Dev and Seeding**
- `DemoCustomerSeeder` seeds 4 demo users with realistic scan/removal data for UI development
- Three exposure scenarios: low, high, very high
- Fake Cashier subscriptions — no real Stripe calls needed for local dev

### Architecture Notes
- Self-contained module at `app/Modules/SaasProductModule/` — own routes, controllers, middleware, models, views, jobs, migrations, and seeders
- Same `User` model as the rest of the platform — a SaasProductModule customer is just a user with a `saasproductmodule` Cashier subscription and a `Profile` record. No separate auth or user table.
- Two middleware guards: `EnsureSubscribed` (active Cashier subscription) and `EnsureOnboardingComplete` (profile data filled in)
- Jobs: `DispatchInitialScanJob` fires on onboarding completion; `ProcessScanJob` and `SubmitRemovalJob` handle broker interactions (currently stubbed)

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
1. `/saas-product-module` — marketing landing page with hero, features, how-it-works, 3-tier pricing
2. `/saas-product-module/plans` — interactive plan selector, posts to Stripe Checkout
3. Intake/contact form — fallback for prospects not ready to subscribe; creates a lead in the admin dashboard

### Post-Subscription Onboarding (4 steps)
1. **Profile** — first name, last name, date of birth
2. **Contact** — email addresses, phone numbers (empties filtered automatically)
3. **Addresses** — current and past addresses for broker searches
4. **Confirm and Launch** — review data, dispatches `DispatchInitialScanJob`

### Customer Dashboard
- `/saas-product-module/dashboard` — exposure score, scan status per broker, removal request tracking
- Scan statuses: `pending` > `running` > `completed` / `failed`
- Removal statuses: `pending` > `submitted` > `confirmed` / `failed`

---

## Architecture

### Module Location
```
app/Modules/SaasProductModule/
├── SaasProductModuleServiceProvider.php
├── Config/saasproductmodule.php
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
│       ├── EnsureSubscribed.php           — checks active 'saasproductmodule' Cashier subscription
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
- **Same `User` model** as the rest of the platform. A SaasProductModule customer is a regular user with a `saasproductmodule` Cashier subscription row and a `Profile` record.
- **Subscription type key**: `'saasproductmodule'` (Cashier's `type` column). All checks must use `subscribed('saasproductmodule')` or `subscription('saasproductmodule')` — not the Cashier default `'default'`.
- **No webhook dependency for local dev**: The checkout success URL includes `{CHECKOUT_SESSION_ID}`; `BillingController::success()` syncs the subscription directly from the Stripe API before redirecting, so it works without `stripe listen`.
- **Tailwind scanning**: `app/Modules/**/*.blade.php` must remain in `tailwind.config.js` content paths (already configured).

### Route Groups
```
/saas-product-module                   — public landing page
/saas-product-module/plans             — public plan selector (no auth required)
/saas-product-module/start             — smart entry redirect (auth required)
/saas-product-module/checkout          — POST, initiates Stripe Checkout (auth required)
/saas-product-module/billing/success   — Stripe return URL, syncs subscription
/saas-product-module/onboarding/*      — requires auth + active subscription
/saas-product-module/dashboard         — requires auth + subscription + completed onboarding
/admin/saas-product-module/*           — requires company_administrator role
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

Admin: `admin@smbgen.com` — admin panel at `/admin/saas-product-module`

To reseed demo data without wiping the database:
```bash
php artisan db:seed --class="App\Modules\SaasProductModule\Database\Seeders\DataBrokerSeeder"
php artisan db:seed --class="App\Modules\SaasProductModule\Database\Seeders\DemoCustomerSeeder"
```

---

## Open Questions and Pre-Launch Considerations

### 1. Broker Automation — What Is Actually Implemented?
The scan/removal jobs (`ProcessScanJob`, `SubmitRemovalJob`) exist as Laravel jobs but the actual broker interaction logic is stubbed. The team needs to define the submission mechanism per broker: web scraping, email template, certified mail, API (where available), or a mix. This is the largest open implementation gap.

### 2. Human vs. Automated Workflow
The Executive tier advertises "manual removals" and a "dedicated specialist." Is there a staff-facing workflow (task queue, assignment, internal notes) that needs to be built, or is that tier label aspirational at launch?

### 3. Tier Enforcement at the Broker Level
Basic covers 18 brokers, Professional 24, Executive 25 — but all active brokers are currently scanned for all tiers. Broker-to-tier mapping must be implemented before launch.

### 4. Recurring Scan Scheduler
`DispatchInitialScanJob` fires once at onboarding completion. Ongoing scans (monthly / weekly / continuous per tier) need a scheduled job that respects each tier's cadence. Nothing is scheduled yet.

### 5. Exposure Score Calculation
Currently a static integer seeded onto the `Profile` model. Define whether this is computed from scan results (and when it recalculates), or if it is operator-assigned.

### 6. Customer Notification Emails
No outbound email notifications exist yet for SaasProductModule events (new listing found, removal confirmed, scan complete). Define which events should trigger customer communication.

### 7. PII Handling and Compliance
The module stores names, DOBs, email addresses, phone numbers, and physical addresses. CCPA, GDPR, and individual broker terms apply. Define: data retention policy, who has read access, audit logging requirements, and whether submitted opt-out content is stored.

### 8. Domain and Branding Strategy
SaaS Product Module currently lives under the same domain and auth as the main portal. Decide whether it stays co-located long-term or eventually spins off as a standalone product. The module architecture supports either path without major refactoring.
