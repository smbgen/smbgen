# ClientBridge Pricing Model & Technical Specifications

**SaaS Subscription Plans with Booking-First Multi-Tenant Offering**

**Last Updated:** December 28, 2025  
**Status:** Planning Phase

---

## Executive Summary

Define a booking-first pricing model that emphasizes a simple, multi-tenant offering for booking + landing pages + client area, while reserving messaging and complex features for dedicated instances. Each plan targets a specific market segment with pricing aligned to value delivered.

**Pricing Strategy:**
- **Free (Gmail):** Forever-free multi-tenant tier for gmail.com users focused on booking + landing pages + client area
- **Booking (Multi-Tenant):** Entry paid tier for booking-first offering without messaging or complex features
- **Dedicated (Professional/Enterprise):** Dedicated-instance tiers that enable messaging, advanced features, and customizations

---

## Pricing Tiers Overview

| Plan | Monthly | Yearly | Target Customer | Expected MRR/Tenant |
|------|---------|--------|-----------------|---------------------|
| **Free (Gmail)** | $0 | N/A | Individuals using gmail.com | $0 |
| **Booking (Multi-Tenant)** | $29 | $290 (17% off) | Solopreneurs, micro-businesses | $29 |
| **Dedicated Professional** | $99 | $990 (17% off) | Small-medium businesses | $99 |
| **Dedicated Enterprise** | $299 | $2,990 (17% off) | Large organizations | $299 |

**Trial:** 14-day free trial on Dedicated Professional; Free (Gmail) tier available with no credit card

---

## Plan 1: Free (Gmail) — Booking-Only (Forever Free)

**Eligibility & Target:**
- Sign up with Google OAuth and an email ending in `gmail.com`
- Individuals and very small teams testing ClientBridge

**Value Proposition:**
"Publish a booking page with a simple client area — free for Gmail users"

### Features Included

#### ✅ Core Features
- ✅ **Booking Management**
  - Public booking page (landing page)
  - Booking calendar view
  - Email notifications for new bookings
  - Basic booking customization (fields, form text)
  
- ✅ **Client Area (Read-Only Basics)**
  - View bookings and basic details
  - View invoices (if issued)
  
- ✅ **Landing Pages**
  - Simple landing page templates for booking

#### ❌ Features NOT Included
- ❌ Messaging (reserved for dedicated instances)
- ❌ Advanced Billing (recurring, payment plans)
- ❌ CMS (full site builder)
- ❌ Inspection Reports
- ❌ Phone System integration
- ❌ API Access
- ❌ Custom Branding (uses default ClientBridge branding)
- ❌ Priority Support
- ❌ Advanced Automations

### Technical Limits

```json
{
  "limits": {
    "max_users": 1,
    "max_clients": 50,
    "max_bookings_per_month": 25,
    "storage_gb": 1,
    "email_limit_monthly": 200,
    "api_calls_per_hour": 0,
    "custom_domains": 0,
    "file_size_limit_mb": 5
  }
}
```

### Resource Cost Analysis (Per Tenant)

**Infrastructure Costs:**
- Database storage: ~100MB average = $0.01/month
- File storage: 5GB = $0.50/month
- Bandwidth: ~10GB = $0.50/month
- Email sending: 500 emails = $0.50/month
- Server resources: $2/month (shared)
- **Total Cost:** ~$3.51/month

**Gross Margin:** N/A (free tier); cost capped via strict limits

---

## Plan 2: Booking (Multi-Tenant) — $29/month ⭐ Recommended Entry

**Target Market:**
- Solopreneurs and small teams focused on booking + landing pages + client area
- Those who do not need messaging or complex capabilities

**Value Proposition:**
"Affordable booking with landing pages and a simple client area — no messaging"

### Features Included

#### ✅ Features Included
- ✅ Booking Management (enhanced)
- ✅ Simple Landing Pages (more templates)
- ✅ Client Area (basic file viewing, invoices viewing)
- ✅ Google Calendar Sync (one-way)
- ✅ Basic Branding (logo + primary color)

#### ❌ Features NOT Included
- ❌ Messaging (reserved for dedicated instances)
- ❌ Advanced Billing (recurring, payment plans)
- ❌ Phone System integration
- ❌ API Access
- ❌ Advanced Analytics
- ❌ Full CMS builder
- ❌ White-label (ClientBridge branding removed entirely)

### Technical Limits

```json
{
  "limits": {
    "max_users": 3,
    "max_clients": 250,
    "max_bookings_per_month": 250,
    "storage_gb": 10,
    "email_limit_monthly": 2000,
    "api_calls_per_hour": 0,
    "custom_domains": 0,
    "file_size_limit_mb": 25,
    "landing_pages": 5
  }
}
```

### Resource Cost Analysis (Per Tenant)

**Infrastructure Costs:**
- Database storage: ~500MB average = $0.05/month
- File storage: 50GB = $5/month
- Bandwidth: ~50GB = $2.50/month
- Email sending: 5,000 emails = $5/month
- Server resources: $5/month (shared)
- Google API calls: $1/month
- **Total Cost:** ~$18.55/month

**Gross Margin:** $99 - $18.55 = **$80.45 (81%)**

---

## Dedicated Instances: Professional ($99) & Enterprise ($299)

**Target Market:**
- Businesses needing messaging, complex features, custom domains, and higher limits

**Value Proposition:**
"Unlock messaging and advanced features on dedicated infrastructure"

### Features Included

#### ✅ Dedicated Instance Features

- ✅ **Inspection Reports**
  - Create custom inspection templates
  - Digital forms with photos
  - PDF report generation
  - Client signatures
  
- ✅ **Messaging** (staff ↔ client conversations, email notifications)
- ✅ **Phone System Integration**
  - VoIP integration
  - Call logging
  - Click-to-call from dashboard
  
- ✅ **API Access**
  - RESTful API
  - Webhooks for events
  - API documentation
  - Dedicated API keys
  
- ✅ **Custom Domain**
  - Use your own domain (app.yourcompany.com)
  - SSL certificate management
  
- ✅ **Advanced Analytics**
  - Custom reports
  - Export data to CSV/PDF
  - Revenue analytics
  - Client lifetime value tracking
  
- ✅ **White-Label**
  - Remove all ClientBridge branding
  - Custom email templates
  - Fully branded experience
  
- ✅ **Priority Support + Onboarding**
  - < 4 hour response time
  - Dedicated account manager
  - Onboarding call
  - Training sessions
  - Phone support
  
- ✅ **SLA Guarantee**
  - 99.9% uptime guarantee
  - Priority incident response
  
- ✅ **Advanced Automations**
  - Workflow automation
  - Custom triggers
  - Zapier integration

### Technical Limits

```json
{
  "limits": {
    "max_users": 15 (Professional) / 100 (Enterprise),
    "max_clients": 1000 (Professional) / 10000 (Enterprise),
    "max_bookings_per_month": 500 (Professional) / 5000 (Enterprise),
    "storage_gb": 50 (Professional) / 250 (Enterprise),
    "email_limit_monthly": 5000 (Professional) / 25000 (Enterprise),
    "api_calls_per_hour": 0 (Professional) / 10000 (Enterprise),
    "custom_domains": 0 (Professional) / 1 (Enterprise),
    "file_size_limit_mb": 50 (Professional) / 250 (Enterprise),
    "cms_pages": 25 (Professional) / 100 (Enterprise),
    "inspection_templates": 0 (Professional) / 50 (Enterprise)
  }
}
```

### Resource Cost Analysis (Per Tenant)

**Infrastructure Costs:**
- Database storage: ~2GB average = $0.20/month
- File storage: 250GB = $25/month
- Bandwidth: ~200GB = $10/month
- Email sending: 25,000 emails = $25/month
- Server resources: $20/month (dedicated resources)
- Google API calls: $5/month
- Phone system API: $10/month
- Custom domain SSL: $2/month
- **Total Cost:** ~$97.20/month

**Gross Margin:** $299 - $97.20 = **$201.80 (67%)**

---

## Feature Comparison Matrix (Booking-First)

| Feature | Free (Gmail) | Booking (Multi-Tenant) | Dedicated Pro | Dedicated Enterprise |
|---------|---------|--------------|------------|
| **Core Features** |
| Booking Management | ✅ | ✅ | ✅ | ✅ |
| Client Area | ✅ | ✅ | ✅ | ✅ |
| Messaging | ❌ | ❌ | ✅ | ✅ |
| File Management | ✅ | ✅ | ✅ | ✅ |
| Basic Billing | ❌ | ✅ | ✅ | ✅ |
| **Advanced Features** |
| Landing Pages (Simple) | ✅ | ✅ | ✅ | ✅ |
| Full CMS / Site Builder | ❌ | ❌ | ✅ | ✅ |
| Custom Branding | ❌ | ✅ | ✅ | ✅ |
| Google Calendar Sync | ❌ | ✅ (one-way) | ✅ (two-way) | ✅ (two-way) |
| Google Drive Integration | ❌ | ❌ | ✅ | ✅ |
| Email Tracking | ❌ | ❌ | ✅ | ✅ |
| Advanced Billing | ❌ | ❌ | ✅ | ✅ |
| Priority Support | ❌ | ❌ | ✅ | ✅ |
| **Enterprise Features** |
| Inspection Reports | ❌ | ❌ | ✅ | ✅ |
| Phone System | ❌ | ❌ | ✅ | ✅ |
| API Access | ❌ | ❌ | ✅ | ✅ |
| Custom Domain | ❌ | ❌ | ❌ | ✅ |
| Advanced Analytics | ❌ | ❌ | ✅ | ✅ |
| White-Label | ❌ | ❌ | ✅ | ✅ |
| SLA Guarantee | ❌ | ❌ | ✅ | ✅ |
| Dedicated Support | ❌ | ❌ | ✅ | ✅ |
| **Limits** |
| Users | 1 | 3 | 15 | 100 |
| Clients | 50 | 250 | 1,000 | 10,000 |
| Storage | 1 GB | 10 GB | 50 GB | 250 GB |
| Bookings/Month | 25 | 250 | 500 | 5,000 |
| Emails/Month | 200 | 2,000 | 5,000 | 25,000 |

---

## Technical Implementation

### Database Schema for Plans

```php
// database/seeders/PlanSeeder.php

use App\Models\Plan;

Plan::create([
    'name' => 'Starter',
    'slug' => 'starter',
    'description' => 'Essential client management and booking tools for solopreneurs',
    'price_cents' => 2900, // $29.00
    'billing_period' => 'monthly',
    'stripe_price_id' => 'price_starter_monthly',
    'is_active' => true,
    'is_public' => true,
    'sort_order' => 1,
    'features' => [
        'booking' => true,
        'billing' => true,
        'messages' => true,
        'file_management' => true,
        'cms' => false,
        'inspection_reports' => false,
        'phone_system' => false,
        'api_access' => false,
        'custom_branding' => false,
        'priority_support' => false,
        'google_calendar' => false,
        'google_drive' => false,
        'email_tracking' => false,
        'advanced_analytics' => false,
        'white_label' => false,
        'custom_domain' => false,
    ],
    'limits' => [
        'max_users' => 3,
        'max_clients' => 100,
        'max_bookings_per_month' => 50,
        'storage_gb' => 5,
        'email_limit_monthly' => 500,
        'api_calls_per_hour' => 0,
        'custom_domains' => 0,
        'file_size_limit_mb' => 10,
        'cms_pages' => 0,
    ],
]);

Plan::create([
    'name' => 'Professional',
    'slug' => 'professional',
    'description' => 'Everything you need to run a professional service business',
    'price_cents' => 9900, // $99.00
    'billing_period' => 'monthly',
    'stripe_price_id' => 'price_professional_monthly',
    'is_active' => true,
    'is_public' => true,
    'sort_order' => 2,
    'features' => [
        'booking' => true,
        'billing' => true,
        'messages' => true,
        'file_management' => true,
        'cms' => true,
        'inspection_reports' => false,
        'phone_system' => false,
        'api_access' => false,
        'custom_branding' => true,
        'priority_support' => true,
        'google_calendar' => true,
        'google_drive' => true,
        'email_tracking' => true,
        'advanced_analytics' => false,
        'white_label' => false,
        'custom_domain' => false,
    ],
    'limits' => [
        'max_users' => 15,
        'max_clients' => 1000,
        'max_bookings_per_month' => 500,
        'storage_gb' => 50,
        'email_limit_monthly' => 5000,
        'api_calls_per_hour' => 0,
        'custom_domains' => 0,
        'file_size_limit_mb' => 50,
        'cms_pages' => 25,
    ],
]);

Plan::create([
    'name' => 'Enterprise',
    'slug' => 'enterprise',
    'description' => 'Full-featured platform with enterprise-grade support',
    'price_cents' => 29900, // $299.00
    'billing_period' => 'monthly',
    'stripe_price_id' => 'price_enterprise_monthly',
    'is_active' => true,
    'is_public' => true,
    'sort_order' => 3,
    'features' => [
        'booking' => true,
        'billing' => true,
        'messages' => true,
        'file_management' => true,
        'cms' => true,
        'inspection_reports' => true,
        'phone_system' => true,
        'api_access' => true,
        'custom_branding' => true,
        'priority_support' => true,
        'google_calendar' => true,
        'google_drive' => true,
        'email_tracking' => true,
        'advanced_analytics' => true,
        'white_label' => true,
        'custom_domain' => true,
    ],
    'limits' => [
        'max_users' => 100,
        'max_clients' => 10000,
        'max_bookings_per_month' => 5000,
        'storage_gb' => 250,
        'email_limit_monthly' => 25000,
        'api_calls_per_hour' => 10000,
        'custom_domains' => 1,
        'file_size_limit_mb' => 250,
        'cms_pages' => 100,
        'inspection_templates' => 50,
    ],
]);
```

### Feature Gate Implementation

```php
// app/Models/Tenant.php

public function hasFeature(string $feature): bool
{
    // Suspended/cancelled tenants have no features
    if (in_array($this->status, ['suspended', 'cancelled'])) {
        return false;
    }
    
    // Trial tenants get Professional features
    if ($this->status === 'trial') {
        $trialPlan = Plan::where('slug', 'professional')->first();
        return $trialPlan->features[$feature] ?? false;
    }
    
    return $this->plan->features[$feature] ?? false;
}

public function isWithinLimit(string $limitKey): bool
{
    $limit = $this->plan->limits[$limitKey] ?? null;
    
    if ($limit === null || $limit === 0) {
        return false; // Feature not available
    }
    
    $currentUsage = match($limitKey) {
        'max_users' => $this->users()->count(),
        'max_clients' => $this->clients()->count(),
        'max_bookings_per_month' => $this->bookings()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count(),
        'storage_gb' => $this->calculateStorageUsageGB(),
        'email_limit_monthly' => $this->emails()
            ->whereBetween('sent_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count(),
        'cms_pages' => $this->cmsPages()->count(),
        default => 0,
    };
    
    return $currentUsage < $limit;
}

public function getRemainingCapacity(string $limitKey): int
{
    $limit = $this->plan->limits[$limitKey] ?? 0;
    $usage = match($limitKey) {
        'max_users' => $this->users()->count(),
        'max_clients' => $this->clients()->count(),
        'storage_gb' => $this->calculateStorageUsageGB(),
        default => 0,
    };
    
    return max(0, $limit - $usage);
}
```

### Controller Feature Checks

```php
// app/Http/Controllers/Admin/CmsPageController.php

public function create()
{
    // Check feature availability
    if (!tenant_has_feature('cms')) {
        return redirect()
            ->route('admin.dashboard')
            ->with('error', 'CMS is not available on your plan. Upgrade to Professional or higher.');
    }
    
    // Check limit
    if (!tenant()->isWithinLimit('cms_pages')) {
        $limit = tenant()->plan->limits['cms_pages'];
        return redirect()
            ->route('admin.cms.index')
            ->with('error', "You've reached your CMS page limit ({$limit} pages). Please upgrade your plan.");
    }
    
    return view('admin.cms.create');
}
```

### View Feature Checks

```blade
{{-- resources/views/admin/partials/navigation.blade.php --}}

@if(tenant_has_feature('cms'))
    <a href="{{ route('admin.cms.index') }}">
        CMS Pages
    </a>
@endif

@if(tenant_has_feature('inspection_reports'))
    <a href="{{ route('admin.inspections.index') }}">
        Inspection Reports
    </a>
@endif

{{-- Show upgrade prompt if feature not available --}}
@if(!tenant_has_feature('cms'))
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-yellow-800">
            Want to create custom landing pages? 
            <a href="{{ route('admin.billing.upgrade') }}" class="font-semibold underline">
                Upgrade to Professional
            </a>
        </p>
    </div>
@endif
```

---

## Revenue Projections

### Scenario 1: Conservative Growth

**Assumptions:**
- 10 new signups/month
- 60% trial conversion rate
- 5% monthly churn
- Plan distribution: 40% Starter, 50% Pro, 10% Enterprise

| Month | New Tenants | Active Paid | MRR | ARR |
|-------|-------------|-------------|-----|-----|
| 1 | 6 | 6 | $408 | $4,896 |
| 3 | 16 | 16 | $1,088 | $13,056 |
| 6 | 29 | 29 | $1,972 | $23,664 |
| 12 | 52 | 52 | $3,536 | $42,432 |

### Scenario 2: Moderate Growth

**Assumptions:**
- 25 new signups/month
- 70% trial conversion rate
- 3% monthly churn
- Plan distribution: 30% Starter, 60% Pro, 10% Enterprise

| Month | New Tenants | Active Paid | MRR | ARR |
|-------|-------------|-------------|-----|-----|
| 1 | 18 | 18 | $1,458 | $17,496 |
| 3 | 49 | 49 | $3,969 | $47,628 |
| 6 | 92 | 92 | $7,452 | $89,424 |
| 12 | 170 | 170 | $13,770 | $165,240 |

### Scenario 3: Aggressive Growth

**Assumptions:**
- 50 new signups/month
- 75% trial conversion rate
- 2% monthly churn
- Plan distribution: 25% Starter, 65% Pro, 10% Enterprise

| Month | New Tenants | Active Paid | MRR | ARR |
|-------|-------------|-------------|-----|-----|
| 1 | 38 | 38 | $3,230 | $38,760 |
| 3 | 107 | 107 | $9,095 | $109,140 |
| 6 | 203 | 203 | $17,255 | $207,060 |
| 12 | 385 | 385 | $32,725 | $392,700 |

---

## Pricing Strategy Considerations

### 1. Competitor Analysis

| Competitor | Starter | Mid-Tier | Enterprise |
|------------|---------|----------|------------|
| **ClientBridge** | $29 | $99 | $299 |
| Jobber | $49 | $169 | $349 |
| Housecall Pro | $49 | $129 | $249 |
| ServiceTitan | N/A | $200+ | Custom |

**Positioning:** Undercut competitors on entry tier, competitive on mid-tier, aggressive on enterprise.

### 2. Psychological Pricing

- **$29** vs $30 - Classic under-30 threshold
- **$99** vs $100 - Psychologically under $100
- **$299** vs $300 - Just under $300 benchmark

### 3. Annual Discount Strategy

**17% discount for annual:**
- Starter: $290/year (save $58)
- Professional: $990/year (save $198)
- Enterprise: $2,990/year (save $598)

**Benefits:**
- Locks in customers for 12 months (reduces churn)
- Improves cash flow
- Higher customer lifetime value

### 4. Trial Strategy

**14-day free trial on Professional plan:**

**Pros:**
- Users experience best features
- Higher conversion to paid (see full value)
- Creates upgrade inertia ("we're already using CMS")

**Cons:**
- Could cannibalize Starter sales
- Higher support costs during trial

**Mitigation:**
- Clearly show plan comparison after trial
- Email sequence highlighting Pro-only features
- Make downgrade easy but friction-ful

### 5. Feature Packaging Philosophy

**Core Principle:** Each tier should feel complete, not handicapped

**Starter:**
- ✅ Core CRM functionality works fully
- ✅ Not a "demo" - real value delivered
- ❌ Limited by scale (3 users, 100 clients), not features crippled

**Professional:**
- ✅ "Sweet spot" - most customers land here
- ✅ All features most businesses need
- ✅ Clear differentiation from Starter (CMS, branding)

**Enterprise:**
- ✅ Premium features for large orgs
- ✅ Justifies price with support + SLA
- ✅ White-label and API = different customer profile

---

## Upgrade/Downgrade Policies

### Upgrade Flow

**Immediate Access:**
```
User clicks "Upgrade to Professional"
  → Stripe creates proration charge
  → Plan changes immediately
  → Features unlock immediately
  → Email confirmation sent
```

**No Friction:**
- One-click upgrade
- Prorated billing (fair)
- Instant feature access
- Celebrate upgrade with email

### Downgrade Flow

**End of Billing Period:**
```
User clicks "Downgrade to Starter"
  → Confirm: "Downgrade at end of billing period?"
  → User confirms
  → Plan change scheduled
  → Warning email sent 3 days before
  → On renewal date: plan downgrades
  → Features disabled gracefully
```

**Graceful Degradation:**
- User keeps access to current plan until renewal
- Warning about what will be lost (CMS pages, extra users, etc.)
- Option to export data before downgrade
- If over new limits (e.g., 10 users → 3 user limit), require reduction first

### Cancellation Policy

**Keep Data for 90 Days:**
```
User cancels subscription
  → Immediate: Status = 'cancelled', access disabled
  → Email: "Sorry to see you go" + feedback survey
  → Days 1-90: Data retained, can reactivate
  → Day 90: Email warning "Data will be deleted in 7 days"
  → Day 97: Permanent deletion
```

**Reactivation:**
- Easy one-click reactivation within 90 days
- All data restored
- Same tenant slug reserved

---

## Add-Ons (Future Consideration)

Beyond base plans, consider add-ons for:

| Add-On | Price | Description |
|--------|-------|-------------|
| Extra Users | $5/user/month | Beyond plan limits |
| Extra Storage | $10/50GB/month | Beyond plan limits |
| SMS Notifications | $0.05/SMS | Text alerts to clients |
| Advanced Reporting | $29/month | Business intelligence dashboard |
| Zapier Integration | $19/month | Connect to 3,000+ apps |
| Dedicated IP | $10/month | Email deliverability |

---

## Pricing Page Design

### Key Elements

1. **Hero:**
   - "Simple, transparent pricing"
   - "14-day free trial • No credit card required • Cancel anytime"

2. **Plan Cards (Side-by-Side):**
   - Starter | Professional ⭐ | Enterprise
   - Price prominent
   - "Best for [customer type]"
   - Top 5 features listed
   - "See all features" accordion

3. **Feature Comparison Table:**
   - Expandable sections
   - Checkmarks for clarity
   - Highlight Professional as recommended

4. **FAQs:**
   - Can I change plans later? Yes
   - What happens to my data if I cancel? Retained 90 days
   - Do you offer refunds? Pro-rated refunds within 30 days
   - Can I get a discount for annual billing? Yes, 17% off

5. **Social Proof:**
   - Testimonials from each plan tier
   - "Trusted by 500+ businesses"

---

## Stripe Setup Required

### Create Products & Prices (Stripe)

```bash
# Stripe CLI

# Free (Gmail) — No Stripe product required

# Booking (Multi-Tenant) Plan
stripe products create \
  --name "ClientBridge Booking" \
  --description "Booking, landing pages, and basic client area"

stripe prices create \
  --product prod_booking_xxxxx \
  --unit-amount 2900 \
  --currency usd \
  --recurring[interval]=month

stripe prices create \
  --product prod_booking_xxxxx \
  --unit-amount 29000 \
  --currency usd \
  --recurring[interval]=year

# Dedicated Professional Plan
stripe products create \
  --name "ClientBridge Professional" \
  --description "Everything you need to run a professional service business"

stripe prices create \
  --product prod_pro_xxxxx \
  --unit-amount 9900 \
  --currency usd \
  --recurring[interval]=month

stripe prices create \
  --product prod_pro_xxxxx \
  --unit-amount 99000 \
  --currency usd \
  --recurring[interval]=year

# Dedicated Enterprise Plan
stripe products create \
  --name "ClientBridge Enterprise" \
  --description "Full-featured platform with enterprise-grade support"

stripe prices create \
  --product prod_enterprise_xxxxx \
  --unit-amount 29900 \
  --currency usd \
  --recurring[interval]=month

stripe prices create \
  --product prod_enterprise_xxxxx \
  --unit-amount 299000 \
  --currency usd \
  --recurring[interval]=year
```

---

## Success Metrics

### Key Performance Indicators (KPIs)

**Acquisition:**
- Trial signups/month
- Trial-to-paid conversion rate (target: 70%+)
- Cost per acquisition (CPA)

**Revenue:**
- Monthly Recurring Revenue (MRR)
- Annual Recurring Revenue (ARR)
- Average Revenue Per User (ARPU)
- Customer Lifetime Value (LTV)

**Retention:**
- Monthly churn rate (target: < 3%)
- Plan upgrade rate (target: 10%/month)
- Plan downgrade rate (target: < 2%/month)

**Product:**
- Feature adoption rates per plan
- Limit breach rate (how often users hit limits)
- Support ticket volume by plan tier

---

## Next Steps

1. ✅ Finalize pricing strategy (review with stakeholders)
2. ✅ Create Stripe products and prices
3. ✅ Implement plan seeder with these specs
4. ✅ Build pricing page UI
5. ✅ Implement feature gates in code
6. ✅ Test upgrade/downgrade flows
7. ✅ Launch beta with limited customers

---

**Last Updated:** December 28, 2025  
**Status:** Planning Phase - Ready for Review  
**Owner:** Product & Engineering Teams
