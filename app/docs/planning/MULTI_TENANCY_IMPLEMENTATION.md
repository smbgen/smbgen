# Multi-Tenancy Implementation Plan
**ClientBridge SaaS Transformation**

**Date:** December 28, 2025  
**Current State:** Single-tenant Laravel app with dormant Stancl/tenancy v3.9  
**Target State:** Multi-tenant SaaS with super admin, plan-based features, subscription billing

---

## Executive Summary

Transform ClientBridge from single-tenant to multi-tenant SaaS platform, enabling multiple customer organizations (tenants) to run on shared infrastructure with isolated data, customizable features per pricing tier, and centralized super admin management.

**Key Benefits:**
- Enable SaaS business model with recurring revenue
- Reduce hosting costs through shared infrastructure
- Scale to 100+ customers on single deployment
- Self-service tenant provisioning with trial/demo capabilities
- Feature gating by subscription tier

**Estimated Effort:** 110-160 hours (3-4 weeks full-time)  
**Risk Level:** Medium (package already configured, but requires careful data migration)

---

## Current Architecture Analysis

### Existing Setup
Transform ClientBridge from single-tenant to a booking-first multi-tenant SaaS offering, where shared infrastructure provides booking + landing pages + client areas. Messaging and more complex features (API, phone, full CMS, advanced billing) are reserved for dedicated instances. Centralized super admin manages tenants and dedicated deployments.
- **Status:** `TENANCY_ENABLED=false` - completely dormant
**Key Benefits:**
- Rapid onboarding via booking-first flow
- Clear separation: multi-tenant for simple bookings; dedicated for complex features
- Lower support and infra costs for entry tiers
- Self-service signup for Free (Gmail) tier; upgrade path to paid booking tier and dedicated tiers
- **Bootstrappers:** Database, Cache, Filesystem, Queue isolation ready
- **Identification:** Subdomain-based (`{tenant}.clientbridge.test`)
- **Scope Decision:** Multi-tenant MVP limited to booking + landing pages + client area

    /pricing             → Pricing plans (Free Gmail, Booking, Dedicated)
    /register            → Tenant signup (Booking & Dedicated)
    /signup-gmail        → Free Gmail signup (Google OAuth)
    /dashboard           → Client area (booking-centric)
ROLE_USER = 'user'
ROLE_CLIENT = 'client' (default)
    $table->json('features');                        // {"booking": true, "landing_pages": true}
    $table->json('limits');                          // {"max_users": 3, "max_clients": 250}

// Add to: users, clients, bookings, invoices, client_files,
```php
    // Messaging and complex features only enabled for dedicated instances
    if ($feature === 'messages' && !($this->settings['deployment'] ?? 'multi') === 'dedicated') {
        return false;
    }
    
    return $this->plan->features[$feature] ?? false;
'features' => [
    "billing": false,
    "messages": false,
    "cms": false,
    "client_area": true,
    "max_users": 3,
    "max_clients": 250,
    "max_bookings_per_month": 250,
    "storage_gb": 10,
    "email_limit_monthly": 2000

**Goal:** Enable subscription payments via Stripe for paid Booking (multi-tenant) and Dedicated tiers; Free (Gmail) tier requires no Stripe setup.
- ✅ Stancl/tenancy package configured
7. Create first super admin user via seeder
8. Add Free (Gmail) eligibility guard (Google OAuth + email domain check)
- ✅ Tenant isolation bootstrappers ready
3. Configure local DNS for testing (*.clientbridge.test)
4. Add `/signup-gmail` route and controller for Free tier onboarding
- ✅ Google OAuth (needs multi-tenant adaptation)
1. Define plan features and limits in seeder (booking-first presets)
- ❌ No subscription billing
1. Create Stripe products and prices for Booking and Dedicated plans (no Stripe for Free Gmail)
- ❌ No tenant/plan management
- ❌ No tenant-scoped data (no `tenant_id` columns)

---

## Target Architecture

### Multi-Tenant Model

```
┌─────────────────────────────────────────────────────────┐
│                    Platform Layer                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │         Super Admin Panel (super-admin.*)        │  │
│  │  - Manage all tenants                            │  │
│  │  - Manage plans & pricing                        │  │
│  │  - System analytics                              │  │
│  │  - Tenant impersonation                          │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        ▼                   ▼                   ▼
┌───────────────┐   ┌───────────────┐   ┌───────────────┐
│  Tenant A     │   │  Tenant B     │   │  Tenant C     │
│  (acme.*)     │   │  (globex.*)   │   │  (initech.*)  │
├───────────────┤   ├───────────────┤   ├───────────────┤
│ Plan: Pro     │   │ Plan: Starter │   │ Plan: Trial   │
│ Users: 12     │   │ Users: 3      │   │ Users: 1      │
│ Status: Active│   │ Status: Active│   │ Status: Trial │
│               │   │               │   │               │
│ Database:     │   │ Database:     │   │ Database:     │
│ tenant_abc123 │   │ tenant_def456 │   │ tenant_ghi789 │
│               │   │               │   │               │
│ Features:     │   │ Features:     │   │ Features:     │
│ ✓ Booking     │   │ ✓ Booking     │   │ ✓ Booking     │
│ ✓ Billing     │   │ ✗ Billing     │   │ ✗ Billing     │
│ ✓ CMS         │   │ ✗ CMS         │   │ ✗ CMS         │
│ ✓ Phone       │   │ ✗ Phone       │   │ ✗ Phone       │
└───────────────┘   └───────────────┘   └───────────────┘
```

### Routing Strategy

```
Central Domain (no tenant context):
  https://clientbridge.app
    /                    → Landing page
    /pricing             → Pricing plans
    /register            → Tenant signup
    /super-admin/*       → Super admin panel

Tenant Subdomains:
  https://acme.clientbridge.app
    /login               → Tenant-specific auth
    /dashboard           → Client portal
    /admin/*             → Tenant admin panel
    
  https://globex.clientbridge.app
    /login               → Separate auth
    /dashboard           → Separate data
    /admin/*             → Separate admin panel
```

---

## Database Schema Design

### New Tables

#### 1. Tenants Table
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->string('name');                          // "Acme Corp"
    $table->string('slug')->unique();                // "acme" → acme.clientbridge.app
    $table->string('domain')->nullable()->unique();  // Custom domain: "app.acme.com"
    $table->foreignId('plan_id')->constrained();
    
    // Status & Lifecycle
    $table->enum('status', ['trial', 'active', 'suspended', 'cancelled'])->default('trial');
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('subscription_started_at')->nullable();
    
    // Billing
    $table->string('stripe_customer_id')->nullable();
    $table->string('stripe_subscription_id')->nullable();
    $table->string('billing_email')->nullable();
    
    // Settings
    $table->json('settings')->nullable();            // Tenant-specific config
    $table->string('logo_url')->nullable();
    $table->string('primary_color')->nullable();     // Brand customization
    
    // Audit
    $table->timestamp('last_activity_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('slug');
    $table->index('status');
    $table->index('stripe_customer_id');
});
```

#### 2. Plans Table
```php
Schema::create('plans', function (Blueprint $table) {
    $table->id();
    $table->string('name');                          // "Professional"
    $table->string('slug')->unique();                // "professional"
    $table->text('description')->nullable();
    
    // Pricing
    $table->integer('price_cents');                  // 9900 = $99.00
    $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
    $table->string('stripe_price_id')->nullable();
    
    // Features & Limits (JSON)
    $table->json('features');                        // {"booking": true, "cms": true}
    $table->json('limits');                          // {"max_users": 10, "max_clients": 500}
    
    // Status
    $table->boolean('is_active')->default(true);
    $table->boolean('is_public')->default(true);     // Show on pricing page?
    $table->integer('sort_order')->default(0);
    
    $table->timestamps();
    
    $table->index('slug');
    $table->index('is_active');
});
```

#### 3. Tenant Invitations Table
```php
Schema::create('tenant_invitations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->string('email');
    $table->string('role')->default('tenant_staff');
    $table->string('token')->unique();
    $table->foreignId('invited_by')->constrained('users');
    $table->timestamp('expires_at');
    $table->timestamp('accepted_at')->nullable();
    $table->timestamps();
    
    $table->index(['tenant_id', 'email']);
    $table->index('token');
});
```

### Modified Tables (Add tenant_id)

```php
// Add to: users, clients, bookings, invoices, messages, client_files,
// availabilities, cms_pages, cms_images, activity_logs, email_logs

Schema::table('users', function (Blueprint $table) {
    $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
    $table->index('tenant_id');
});

Schema::table('clients', function (Blueprint $table) {
    $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
    $table->index('tenant_id');
});

// Repeat for all tenant-scoped tables...

Schema::table('business_settings', function (Blueprint $table) {
    // NULL tenant_id = global setting, non-null = tenant-specific override
    $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
    $table->index('tenant_id');
    $table->unique(['tenant_id', 'key']); // One setting per tenant
});
```

---

## User Roles & Permissions

### Role Hierarchy

```
Platform Level (tenant_id = NULL):
  ├─ super_admin          Full platform access, manage all tenants
  └─ support              Read-only access to all tenants for support

Tenant Level (tenant_id = {X}):
  ├─ tenant_owner         Primary account holder, billing, full control
  ├─ tenant_admin         Manage settings, users, clients (no billing)
  ├─ tenant_manager       Manage clients, bookings, files
  ├─ tenant_staff         Limited access (bookings, messages)
  └─ client               External customer, portal-only access
```

### User Model Updates

```php
// app/Models/User.php

// New role constants
const ROLE_SUPER_ADMIN = 'super_admin';
const ROLE_SUPPORT = 'support';
const ROLE_TENANT_OWNER = 'tenant_owner';
const ROLE_TENANT_ADMIN = 'tenant_admin';
const ROLE_TENANT_MANAGER = 'tenant_manager';
const ROLE_TENANT_STAFF = 'tenant_staff';
const ROLE_CLIENT = 'client';

// Add relationship
public function tenant()
{
    return $this->belongsTo(Tenant::class);
}

// Permission helpers
public function isSuperAdmin(): bool
{
    return $this->role === self::ROLE_SUPER_ADMIN && $this->tenant_id === null;
}

public function isSupport(): bool
{
    return $this->role === self::ROLE_SUPPORT && $this->tenant_id === null;
}

public function isTenantOwner(): bool
{
    return $this->role === self::ROLE_TENANT_OWNER;
}

public function canManageTenant(): bool
{
    return in_array($this->role, [
        self::ROLE_TENANT_OWNER,
        self::ROLE_TENANT_ADMIN,
    ]);
}

public function canManageUsers(): bool
{
    return in_array($this->role, [
        self::ROLE_TENANT_OWNER,
        self::ROLE_TENANT_ADMIN,
    ]);
}

public function canManageClients(): bool
{
    return in_array($this->role, [
        self::ROLE_TENANT_OWNER,
        self::ROLE_TENANT_ADMIN,
        self::ROLE_TENANT_MANAGER,
    ]);
}

// Scope queries to tenant automatically
protected static function booted()
{
    // Only apply tenant scope to non-platform users
    static::addGlobalScope('tenant', function (Builder $builder) {
        if (tenancy()->initialized && !auth()->user()?->isSuperAdmin()) {
            $builder->where('tenant_id', tenant('id'));
        }
    });
}
```

### Middleware

```php
// app/Http/Middleware/SuperAdmin.php
if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
    abort(403, 'Unauthorized: Super admin access required');
}

// app/Http/Middleware/InitializeTenancy.php
$host = request()->getHost();
$subdomain = explode('.', $host)[0];

if ($subdomain && !in_array($subdomain, ['www', 'app', 'clientbridge'])) {
    $tenant = Tenant::where('slug', $subdomain)->firstOrFail();
    tenancy()->initialize($tenant);
}

// Update app/Http/Middleware/CompanyAdministrator.php
if (!auth()->user()->canManageTenant()) {
    abort(403);
}

// Ensure user belongs to current tenant
if (auth()->user()->tenant_id !== tenant('id')) {
    abort(403, 'Unauthorized: Invalid tenant access');
}
```

---

## Feature Control System

### Current State (Config-based)
```php
// config/business.php - Global for all users
if (config('business.features.cms')) {
    // Show CMS menu
}
```

### Target State (Plan-based)
```php
// Tenant model
public function hasFeature(string $feature): bool
{
    if ($this->status === 'suspended' || $this->status === 'cancelled') {
        return false; // No features for suspended tenants
    }
    
    return $this->plan->features[$feature] ?? false;
}

public function isWithinLimit(string $limitKey): bool
{
    $limit = $this->plan->limits[$limitKey] ?? null;
    
    switch ($limitKey) {
        case 'max_users':
            return $this->users()->count() < $limit;
        case 'max_clients':
            return $this->clients()->count() < $limit;
        case 'storage_gb':
            return $this->calculateStorageUsageGB() < $limit;
        default:
            return true;
    }
}

// Helper function (app/helpers.php)
function tenant_has_feature(string $feature): bool
{
    if (!tenancy()->initialized) {
        return config("business.features.{$feature}", false);
    }
    
    return tenant()->hasFeature($feature);
}

// Usage in controllers/views
if (tenant_has_feature('cms')) {
    // Show CMS features
}

// Check limits before creating resources
if (!tenant()->isWithinLimit('max_users')) {
    throw new \Exception('User limit reached. Please upgrade your plan.');
}
```

### Plan Features Schema

**Example Plan JSON:**
```json
{
  "features": {
    "booking": true,
    "billing": true,
    "messages": true,
    "cms": true,
    "file_management": true,
    "inspection_reports": false,
    "phone_system": false,
    "api_access": false,
    "custom_branding": true,
    "priority_support": true
  },
  "limits": {
    "max_users": 10,
    "max_clients": 500,
    "max_bookings_per_month": 1000,
    "storage_gb": 50,
    "email_limit_monthly": 5000,
    "api_calls_per_hour": 1000
  }
}
```

---

## Subscription Billing Integration

### Extend StripeService

```php
// app/Services/StripeService.php

public function createSubscription(Tenant $tenant, Plan $plan, ?string $paymentMethodId = null): array
{
    $owner = $tenant->users()->where('role', 'tenant_owner')->first();
    
    // Create or get Stripe customer
    if (!$tenant->stripe_customer_id) {
        $customer = $this->stripe->customers->create([
            'email' => $tenant->billing_email ?? $owner->email,
            'name' => $tenant->name,
            'metadata' => [
                'tenant_id' => $tenant->id,
                'tenant_slug' => $tenant->slug,
            ],
        ]);
        $tenant->update(['stripe_customer_id' => $customer->id]);
    } else {
        $customer = $this->stripe->customers->retrieve($tenant->stripe_customer_id);
    }
    
    // Attach payment method if provided
    if ($paymentMethodId) {
        $this->stripe->paymentMethods->attach($paymentMethodId, [
            'customer' => $customer->id,
        ]);
        
        $this->stripe->customers->update($customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId,
            ],
        ]);
    }
    
    // Create subscription
    $subscription = $this->stripe->subscriptions->create([
        'customer' => $customer->id,
        'items' => [
            ['price' => $plan->stripe_price_id],
        ],
        'metadata' => [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
        ],
        'trial_period_days' => $tenant->status === 'trial' ? 14 : null,
    ]);
    
    // Update tenant
    $tenant->update([
        'stripe_subscription_id' => $subscription->id,
        'status' => $subscription->status === 'active' ? 'active' : 'trial',
        'subscription_started_at' => now(),
        'plan_id' => $plan->id,
    ]);
    
    return [
        'subscription' => $subscription,
        'customer' => $customer,
    ];
}

public function updateSubscription(Tenant $tenant, Plan $newPlan): bool
{
    if (!$tenant->stripe_subscription_id) {
        return false;
    }
    
    $subscription = $this->stripe->subscriptions->retrieve($tenant->stripe_subscription_id);
    
    $this->stripe->subscriptions->update($tenant->stripe_subscription_id, [
        'items' => [
            [
                'id' => $subscription->items->data[0]->id,
                'price' => $newPlan->stripe_price_id,
            ],
        ],
        'proration_behavior' => 'create_prorations',
    ]);
    
    $tenant->update(['plan_id' => $newPlan->id]);
    
    return true;
}

public function cancelSubscription(Tenant $tenant, bool $immediately = false): bool
{
    if (!$tenant->stripe_subscription_id) {
        return false;
    }
    
    if ($immediately) {
        $this->stripe->subscriptions->cancel($tenant->stripe_subscription_id);
        $tenant->update(['status' => 'cancelled']);
    } else {
        // Cancel at period end
        $this->stripe->subscriptions->update($tenant->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);
    }
    
    return true;
}
```

### Webhook Handling

```php
// Update app/Http/Controllers/PaymentController.php

public function webhook(Request $request)
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid signature'], 400);
    }
    
    // Handle subscription events
    if (str_starts_with($event->type, 'customer.subscription.')) {
        $this->handleSubscriptionEvent($event);
    }
    
    // ... existing invoice/payment handling ...
    
    return response()->json(['received' => true]);
}

private function handleSubscriptionEvent($event)
{
    $subscription = $event->data->object;
    $tenant = Tenant::where('stripe_subscription_id', $subscription->id)->first();
    
    if (!$tenant) {
        \Log::warning('Subscription webhook for unknown tenant', [
            'subscription_id' => $subscription->id,
        ]);
        return;
    }
    
    switch ($event->type) {
        case 'customer.subscription.created':
            $tenant->update([
                'status' => $subscription->status,
                'subscription_started_at' => now(),
            ]);
            break;
            
        case 'customer.subscription.updated':
            $status = match($subscription->status) {
                'active' => 'active',
                'past_due', 'unpaid' => 'suspended',
                'canceled' => 'cancelled',
                default => $tenant->status,
            };
            
            $tenant->update(['status' => $status]);
            break;
            
        case 'customer.subscription.deleted':
            $tenant->update(['status' => 'cancelled']);
            break;
            
        case 'customer.subscription.trial_will_end':
            // Send notification 3 days before trial ends
            $tenant->owner->notify(new TrialEndingSoonNotification($tenant));
            break;
    }
}
```

---

## Super Admin Panel

### Routes
```php
// routes/web.php

Route::middleware(['auth', 'superAdmin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Tenant Management
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])->name('tenants.impersonate');
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    
    // Plan Management
    Route::resource('plans', PlanController::class);
    
    // Analytics
    Route::get('analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
    Route::get('analytics/mrr', [SuperAdminController::class, 'mrr'])->name('analytics.mrr');
    Route::get('analytics/churn', [SuperAdminController::class, 'churn'])->name('analytics.churn');
});
```

### Key Features

**1. Dashboard**
- Total tenants (active, trial, suspended, cancelled)
- MRR (Monthly Recurring Revenue)
- New signups this month
- Churn rate
- Top tenants by usage
- Recent activity feed

**2. Tenant Management**
- List all tenants with search/filter (status, plan, date)
- Create new tenant manually
- Edit tenant details (name, plan, status)
- View tenant usage stats:
  - Users, clients, bookings count
  - Storage usage
  - Last activity
  - Subscription status
- Impersonate tenant owner (support tool)
- Suspend/activate/delete tenant

**3. Plan Management**
- CRUD operations for plans
- Set pricing, features, limits
- Configure Stripe price IDs
- Reorder plans (sort_order)
- Mark plans as active/inactive

**4. Analytics**
- MRR over time chart
- Signup trends
- Churn analysis
- Feature adoption rates
- Average revenue per tenant
- Tenant lifetime value

---

## Implementation Phases

### Phase 1: Database Schema (Week 1)
**Goal:** Create foundation for multi-tenancy

**Tasks:**
1. Create migration for `tenants` table
2. Create migration for `plans` table
3. Create migration for `tenant_invitations` table
4. Create migration to add `tenant_id` to all tenant-scoped tables
5. Create Tenant, Plan, TenantInvitation models with relationships
6. Create database seeder for default plans
7. Run migrations on fresh database (test)

**Deliverables:**
- ✅ Database schema complete
- ✅ Models with relationships
- ✅ 3 default plans seeded

**Testing:**
```bash
php artisan migrate:fresh --seed
php artisan tinker
>>> Tenant::factory()->create()
>>> Plan::all()
```

### Phase 2: Authentication & Roles (Week 1-2)
**Goal:** Implement super admin and tenant-level roles

**Tasks:**
1. Update User model with new role constants
2. Add permission helper methods to User model
3. Create SuperAdmin middleware
4. Create InitializeTenancy middleware
5. Update CompanyAdministrator middleware for tenant awareness
6. Register middleware in bootstrap/app.php
7. Create first super admin user via seeder
8. Test role checks and middleware

**Deliverables:**
- ✅ Role system implemented
- ✅ Middleware protecting routes
- ✅ Super admin user created

**Testing:**
```bash
php artisan tinker
>>> $admin = User::where('role', 'super_admin')->first()
>>> $admin->isSuperAdmin() // true
>>> $admin->tenant_id // null
```

### Phase 3: Tenant-Aware Routing (Week 2)
**Goal:** Enable subdomain-based tenant isolation

**Tasks:**
1. Update routes/web.php to separate central vs tenant routes
2. Apply InitializeTenancy middleware to tenant routes
3. Configure local DNS for testing (*.clientbridge.test)
4. Add tenant() global helper function
5. Test tenant isolation (User A cannot see Tenant B data)
6. Update all models with tenant global scope

**Deliverables:**
- ✅ Subdomain routing works
- ✅ Tenant context initialized correctly
- ✅ Data isolation verified

**Testing:**
```bash
# Test subdomain routing
curl -H "Host: acme.clientbridge.test" http://localhost/dashboard
curl -H "Host: globex.clientbridge.test" http://localhost/dashboard

# Test data isolation
php artisan tinker
>>> tenancy()->initialize(Tenant::find(1))
>>> User::count() // Only Tenant 1 users
>>> tenancy()->initialize(Tenant::find(2))
>>> User::count() // Only Tenant 2 users
```

### Phase 4: Feature Control Migration (Week 2-3)
**Goal:** Move from config-based to database-driven features

**Tasks:**
1. Define plan features and limits in seeder
2. Add hasFeature() and isWithinLimit() to Tenant model
3. Create tenant_has_feature() helper function
4. Search and replace all config('business.features.*') calls
5. Update controllers to check tenant limits before creating resources
6. Update views to conditionally render features
7. Test feature restrictions

**Deliverables:**
- ✅ Features controlled by plan, not config
- ✅ All config() calls replaced
- ✅ Limits enforced

**Testing:**
```bash
php artisan test --filter=FeatureControlTest
```

### Phase 5: Subscription Billing (Week 3)
**Goal:** Enable subscription payments via Stripe

**Tasks:**
1. Create Stripe products and prices for each plan
2. Extend StripeService with subscription methods
3. Create SubscriptionController for upgrade/downgrade/cancel
4. Update webhook handler for subscription events
5. Create billing page in tenant admin panel
6. Test subscription lifecycle (create, upgrade, cancel)
7. Test webhook events in Stripe test mode

**Deliverables:**
- ✅ Subscription creation works
- ✅ Plan changes handled correctly
- ✅ Webhooks updating tenant status

**Testing:**
```bash
# Test subscription creation
php artisan tinker
>>> $tenant = Tenant::first()
>>> $plan = Plan::where('slug', 'professional')->first()
>>> app(StripeService::class)->createSubscription($tenant, $plan)

# Trigger test webhook
stripe trigger customer.subscription.updated
```

### Phase 6: Super Admin Panel (Week 3-4)
**Goal:** Build UI for platform management

**Tasks:**
1. Create super admin layout (resources/views/super-admin/)
2. Create dashboard with key metrics
3. Create tenant CRUD views (index, create, edit)
4. Create plan CRUD views
5. Add tenant impersonation feature
6. Add analytics charts (MRR, signups, churn)
7. Style with existing Tailwind components

**Deliverables:**
- ✅ Functional super admin panel
- ✅ Tenant management UI
- ✅ Plan management UI
- ✅ Analytics dashboard

**Testing:**
```bash
# Access as super admin
php artisan tinker
>>> $admin = User::where('role', 'super_admin')->first()
>>> auth()->login($admin)

# Visit /super-admin/dashboard
```

### Phase 7: Tenant Onboarding (Week 4)
**Goal:** Enable self-service signup

**Tasks:**
1. Create tenant registration form
2. Create TenantRegistrationController
3. Handle tenant creation + owner user creation
4. Send welcome email with login link
5. Start trial period automatically
6. Create onboarding checklist in tenant dashboard
7. Test full signup flow

**Deliverables:**
- ✅ Public registration form
- ✅ Tenant + owner created together
- ✅ Trial starts automatically

### Phase 8: Testing & Migration (Week 4)
**Goal:** Ensure quality and handle existing data

**Tasks:**
1. Write feature tests for multi-tenancy
2. Write tests for tenant isolation
3. Write tests for feature restrictions
4. Write tests for subscription billing
5. Create migration script for existing production data
6. Test migration on staging environment
7. Document rollback procedures

**Deliverables:**
- ✅ Comprehensive test suite
- ✅ Migration script for production data
- ✅ Rollback plan documented

---

## Technical Considerations & Decisions Needed

### 1. Database Strategy
**Question:** Single database with `tenant_id` column or separate database per tenant?

**Options:**
- **A. Single Database (Shared Schema)**
  - Pros: Simpler queries, easier backups, better resource utilization
  - Cons: Risk of data leaks if queries miss tenant_id, harder to scale
  
- **B. Separate Databases (Current Config)**
  - Pros: Complete data isolation, per-tenant backups, easier tenant export
  - Cons: More complex queries, database connection overhead, harder migrations

**Current Setup:** Stancl configured for separate databases (`tenant{id}`)

**Recommendation:** Start with **Option A** (single database with tenant_id) for MVP due to simplicity. Migrate to Option B later if needed for compliance (HIPAA, SOC2) or scale.

**Decision Required:** Choose A or B before Phase 1

---

### 2. Environment Strategy
**Question:** How to handle local dev, staging, and production?

**Current:** Testing in production on Laravel Cloud

**Recommendation:**
- **Local:** Herd with SQLite, subdomain testing (*.clientbridge.test)
- **Staging:** Laravel Cloud staging environment with MySQL
- **Production:** Laravel Cloud production with MySQL

**Decision Required:** Set up staging environment before Phase 3

---

### 3. Google OAuth Multi-Tenant
**Question:** How to handle OAuth redirects for multiple subdomains?

**Problem:** Google OAuth doesn't support wildcard redirect URIs

**Options:**
- **A. Central Auth Proxy**
  - Flow: tenant.app → auth.app → Google → auth.app → tenant.app
  - Pros: Single Google OAuth app, simple config
  - Cons: Extra redirect hop
  
- **B. Per-Tenant OAuth Apps**
  - Each tenant registers own Google app
  - Pros: Clean redirects
  - Cons: Complex setup, not self-service friendly

**Recommendation:** **Option A** for MVP

**Decision Required:** Implement auth proxy in Phase 3

---

### 4. Existing Data Migration
**Question:** What to do with current production data?

**Options:**
- **A. Migrate to Default Tenant**
  - Create "Legacy" tenant, assign all existing data
  - Pros: Preserves data, allows gradual migration
  - Cons: May not reflect true multi-tenant structure
  
- **B. Fresh Start**
  - Multi-tenant mode requires fresh install
  - Pros: Clean implementation
  - Cons: Can't use for existing customers

**Recommendation:** **Option A** - migration script in Phase 8

**Decision Required:** Confirm approach before Phase 1

---

### 5. Domain Strategy
**Question:** Subdomains only or support custom domains?

**Options:**
- **A. Subdomains Only (MVP)**
  - tenant.clientbridge.app
  - Pros: Simple DNS, easy SSL
  - Cons: Less professional for customers
  
- **B. Custom Domains (Phase 2)**
  - app.acmecorp.com
  - Pros: Professional, white-label capability
  - Cons: DNS verification, SSL management, higher complexity

**Recommendation:** **Option A** for MVP, add Option B post-launch

**Decision Required:** MVP = subdomains only?

---

### 6. Trial Strategy
**Question:** How long should trials be? Should features be limited?

**Options:**
- **A. Full-Feature Trial**
  - 14-day trial of highest plan
  - Pros: Best customer experience
  - Cons: May reduce conversions
  
- **B. Limited-Feature Trial**
  - 14-day trial of Starter plan
  - Pros: Creates upgrade incentive
  - Cons: May frustrate users
  
- **C. Freemium**
  - Forever free Starter plan with limits
  - Pros: Widest adoption
  - Cons: Support costs

**Recommendation:** **Option A** (14-day full-feature trial)

**Decision Required:** Confirm trial strategy before Phase 5

---

### 7. Feature Limits Enforcement
**Question:** What happens when tenant exceeds limits?

**Examples:**
- Max 5 users, they try to create 6th
- Max 10GB storage, they upload file that exceeds it

**Options:**
- **A. Hard Block**
  - Prevent action, show upgrade prompt
  - Pros: Forces upgrades
  - Cons: Bad UX if unexpected
  
- **B. Soft Limit + Grace Period**
  - Allow overage, send warning, enforce after 7 days
  - Pros: Better UX
  - Cons: More complex
  
- **C. Overage Billing**
  - Allow overage, charge extra
  - Pros: Flexible
  - Cons: Requires usage tracking

**Recommendation:** **Option A** for users/clients, **Option B** for storage

**Decision Required:** Define enforcement per limit type

---

### 8. Tenant Suspension vs. Deletion
**Question:** What happens when subscription lapses?

**Options:**
- **A. Immediate Suspension**
  - Block access immediately on payment failure
  - Pros: Encourages payment
  - Cons: May lose customers
  
- **B. Grace Period**
  - 7-day grace period, then suspend
  - Pros: More forgiving
  - Cons: Free usage
  
- **C. Read-Only Mode**
  - Can view data but not create/edit
  - Pros: Preserves data access
  - Cons: More complex

**Recommendation:** **Option B** (7-day grace, then suspend)

**Data Retention:** Keep suspended tenant data for 90 days before deletion

**Decision Required:** Confirm suspension policy

---

## Risk Assessment

### High Risk
1. **Data Isolation Bugs**
   - Risk: User from Tenant A sees Tenant B data
   - Mitigation: Comprehensive tests, global scopes, code reviews
   
2. **Production Data Migration**
   - Risk: Data loss during migration
   - Mitigation: Backup, staging test, rollback plan
   
3. **Billing Edge Cases**
   - Risk: Webhook failures, duplicate charges
   - Mitigation: Idempotency, retry logic, manual reconciliation

### Medium Risk
1. **Performance at Scale**
   - Risk: Slow queries with many tenants
   - Mitigation: Database indexing, query optimization, caching
   
2. **Storage Costs**
   - Risk: Unlimited storage per tenant
   - Mitigation: Enforce storage limits, cleanup old files

### Low Risk
1. **OAuth Redirect Complexity**
   - Risk: Auth proxy adds latency
   - Mitigation: Fast redirect, minimal overhead

---

## Success Metrics

### Technical Metrics
- ✅ All tests pass (100% critical paths covered)
- ✅ Tenant data isolation verified (0 leaks)
- ✅ Page load time < 500ms (median)
- ✅ Database queries optimized (< 20 per page)

### Business Metrics
- 🎯 10 tenants onboarded in first month
- 🎯 80% trial-to-paid conversion rate
- 🎯 < 5% churn rate
- 🎯 $5,000 MRR by month 3

---

## Next Steps

1. **Review this document** with team/stakeholders
2. **Make decisions** on all "Decision Required" items
3. **Set up staging environment** (Laravel Cloud)
4. **Create project board** with Phase 1 tasks
5. **Start Phase 1** database schema implementation

---

## Resources

- Stancl/Tenancy Docs: https://tenancyforlaravel.com/docs/v3/
- Stripe Subscriptions API: https://stripe.com/docs/billing/subscriptions/overview
- Laravel Multi-Tenancy Guide: https://laravel-news.com/multi-tenancy-guide

---

**Last Updated:** December 28, 2025  
**Status:** Planning Phase  
**Owner:** Development Team
