# Subscription Tier System

## Overview

The subscription tier system manages feature access and resource limits for tenants. It provides a flexible framework for defining different pricing tiers with specific features and usage limits.

## Architecture

### Core Components

#### SubscriptionTier Model
Located at `app/Models/SubscriptionTier.php`

**Key Methods:**
- `hasFeature(string $feature): bool` - Check if tier includes a feature
- `getLimit(string $limitKey): ?int` - Get resource limit value
- `formattedPrice(): string` - Display price as currency ($47.00)
- `tenants(): HasMany` - Get all tenants on this tier

**Scopes:**
- `active()` - Only active tiers
- `ordered()` - Order by sort_order

**Attributes:**
- `features` (json) - Array of feature flags
- `limits` (json) - Array of resource limits

#### Tenant Model Updates
The `Tenant` model now includes:
- `subscription_tier_id` - Foreign key to subscription tiers
- `subscriptionTier(): BelongsTo` - Relationship to tier
- `hasFeature(string $feature): bool` - Check tier features
- `getLimit(string $limitKey): ?int` - Get tier limit
- `isWithinLimit(string $limitKey, int $usage): bool` - Verify limit compliance
- `isOnTier(string $tierSlug): bool` - Check if on specific tier

## Available Tiers

### Free Trial
- **Price:** $0/month
- **Duration:** 14 days (configurable)
- **Features:** Booking, Client Area
- **Limits:**
  - 3 services
  - 10 clients
  - 1 user
  - 100 bookings/month
  - 1 GB storage
  - No API access

### Starter ($47/month)
- **Price:** $47/month
- **Features:** Booking, Client Area, Messaging, Landing Pages, Basic Branding
- **Limits:**
  - 10 services
  - 50 clients
  - 3 users
  - 500 bookings/month
  - 10 GB storage
  - No API access

### Professional ($97/month)
- **Price:** $97/month
- **Features:** All Starter features + CMS, Billing, API Access, Custom Domain, Advanced Reporting, Priority Support
- **Limits:**
  - 50 services
  - 500 clients
  - 10 users
  - 5,000 bookings/month
  - 100 GB storage
  - 50,000 API calls/month

### Dedicated (Custom Pricing)
- **Price:** Custom per contract
- **Features:** All features enabled (white-label, phone system, unlimited everything)
- **Limits:** Essentially unlimited (999 = unlimited marker)

## Database Schema

### subscription_tiers table
```sql
CREATE TABLE subscription_tiers (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    price_cents UNSIGNED INT DEFAULT 0,
    billing_period VARCHAR(50) DEFAULT 'monthly',
    stripe_price_id VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    features JSON,
    limits JSON,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

### tenants table (updated)
```sql
ALTER TABLE tenants ADD COLUMN subscription_tier_id BIGINT UNSIGNED NULL
    FOREIGN KEY REFERENCES subscription_tiers(id)
    ON DELETE RESTRICT;
```

## Usage Examples

### Check if Tenant Has Feature
```php
if ($tenant->hasFeature('cms')) {
    // Show CMS link
}
```

### Verify Resource Usage Within Limit
```php
if ($tenant->isWithinLimit('max_users', $currentUserCount)) {
    // User can create more users
} else {
    // Display upgrade prompt
}
```

### Get Resource Limit
```php
$maxClients = $tenant->getLimit('max_clients');
$remaining = $maxClients - $currentClientCount;
```

### Check Specific Tier
```php
if ($tenant->isOnTier('professional')) {
    // Professional-only features
}
```

### Change Tenant Tier (Super Admin)
```php
// In TenantController@changeTier
POST /super-admin/tenants/{tenant}/change-tier
{
    "subscription_tier_id": 3
}
```

## Super Admin Interface

### Location
`/super-admin/tenants/{id}`

### Features
1. **Display Current Tier** - Shows tier name, description, price
2. **Feature Matrix** - Visual grid of included/excluded features
3. **Resource Limits** - Display all limits (services, users, storage, etc.)
4. **Change Tier** - Dropdown to switch tenant to different tier
5. **Extend Trial** - Extend free trial period (days)

### Tier Management UI
- Change tier via dropdown/button
- Displays current tier with badge styling
- Shows feature breakdown for current tier
- Color-coded feature indicators (green = included, gray = disabled)

## Implementation Checklist

### Models & Database ✅
- [x] Create `SubscriptionTier` model
- [x] Create `subscription_tiers` table migration
- [x] Add `subscription_tier_id` to tenants table
- [x] Create `SubscriptionTierSeeder` with 4 tiers
- [x] Update Tenant model with tier relationships

### Controllers ✅
- [x] Create `TenantController@changeTier` action
- [x] Add validation for tier changes
- [x] Add route for changing tier

### Views ✅
- [x] Update super-admin tenants show view
- [x] Add subscription details section
- [x] Display feature matrix
- [x] Display resource limits
- [x] Add change tier form/modal

### Testing ✅
- [x] Create `SubscriptionTierTest` feature test
- [x] Test tier existence and pricing
- [x] Test feature checking
- [x] Test limit enforcement
- [x] Test tier changes

### Documentation ✅
- [x] Create this guide
- [x] Document tier features and limits
- [x] Document usage examples

## Tier Enforcement Patterns

### In Controllers
```php
public function store(Request $request, Tenant $tenant)
{
    if (!$tenant->hasFeature('cms')) {
        abort(403, 'CMS feature not available on your tier');
    }
    
    if (!$tenant->isWithinLimit('max_clients', $tenant->clients()->count() + 1)) {
        return redirect()->back()
            ->with('error', 'Client limit reached. Upgrade your plan.');
    }
    
    // Process request...
}
```

### In Middleware
```php
class EnforceFeatureLimit extends Middleware
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        if (!tenant()->hasFeature($feature)) {
            abort(403, "Feature '{$feature}' not available on your tier");
        }
        return $next($request);
    }
}
```

### In Blade Templates
```blade
@if($tenant->hasFeature('cms'))
    <a href="{{ route('admin.cms.index') }}">CMS Editor</a>
@else
    <div class="upgrade-prompt">
        Upgrade to Professional or Dedicated plan to use CMS
    </div>
@endif
```

## Next Steps

1. **Implement Feature Enforcement**
   - Create middleware for feature gates
   - Add checks in controllers for resource limits
   - Display upgrade prompts on feature-locked areas

2. **Stripe Integration**
   - Link Stripe price IDs to tiers
   - Implement subscription creation/updates
   - Handle billing period changes

3. **Tier Notifications**
   - Alert tenants when approaching limits
   - Prompt upgrades when limits exceeded
   - Show upgrade recommendations based on usage

4. **Analytics & Usage Tracking**
   - Track feature usage per tenant
   - Display usage dashboards
   - Recommend tier upgrades

5. **Advanced Pricing**
   - Add annual billing discount
   - Implement usage-based pricing add-ons
   - Support custom pricing for Dedicated tier

## Troubleshooting

### Tenant Can't See Features on Their Tier
1. Verify `subscription_tier_id` is set in database
2. Confirm tenant's `is_active` is true (inactive tenants have no features)
3. Check that tier's feature flag is true: `tier->features['feature_key']`

### Limits Not Being Enforced
1. Verify `getLimit()` returns correct value
2. Check that you're using `isWithinLimit()` correctly (< not <=)
3. Ensure current usage is accurate before comparison

### Tier Change Not Appearing
1. Reload the page (no real-time update in UI yet)
2. Check route is correctly named `super-admin.tenants.change-tier`
3. Verify super admin has correct role: 'super_admin'

## Testing

Run the test suite:
```bash
php artisan test tests/Feature/SubscriptionTierTest.php
```

Tests cover:
- Tier creation and pricing
- Feature checking
- Limit enforcement
- Tier changes via super admin
