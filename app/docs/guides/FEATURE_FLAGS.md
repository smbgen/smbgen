# Feature Flags

This document lists the **five core feature flags** used to toggle functionality across deployments. Flags live in your `.env` and are read via `config('business.features.<flag>')` after `config:cache`/`config:clear`.

## Core Feature Flags

The application uses these five flags to control major functionality:

### 1. **FEATURE_BOOKING** (default: `true`)
- **Controls:** Appointment scheduling, booking wizard, availability calendar, Google Calendar integration
- **Routes:** `/book/*`, `/admin/bookings/*`, `/admin/availability/*`
- **Config key:** `config('business.features.booking')`
- **Usage in Blade:** `@if(config('business.features.booking'))`

### 2. **FEATURE_BILLING** (default: `false`)
- **Controls:** Invoicing, payment processing, QuickBooks integration, billing dashboard
- **Routes:** `/billing/*`, `/admin/billing/*`, `/admin/quickbooks/*`
- **Config key:** `config('business.features.billing')`
- **Usage in Blade:** `@if(config('business.features.billing'))`
- **Integration:** QuickBooks Online for invoice generation and payment processing

### 3. **FEATURE_MESSAGES** (default: `true`)
- **Controls:** Internal messaging between clients and staff
- **Routes:** `/messages/*`
- **Config key:** `config('business.features.messages')`
- **Usage in Blade:** `@if(config('business.features.messages'))`

### 4. **FEATURE_CMS** (default: `true`)
- **Controls:** Content management system, page editor, form builder, lead capture
- **Routes:** `/admin/cms/*`, dynamic page routes `/{slug}`
- **Config key:** `config('business.features.cms')`
- **Usage in Blade:** `@if(config('business.features.cms'))`

### 5. **FEATURE_BLOG** (default: `true`)
- **Controls:** Blog module, post editor, AI content generation routes
- **Routes:** `/admin/blog/*`, `/admin/ai/*`
- **Config key:** `config('business.features.blog')`
- **Usage in Blade:** `@if(config('business.features.blog'))`

## How to Toggle Features

1. Open your `.env` file
2. Set the flag to `true` (enabled) or `false` (disabled):
   ```bash
   FEATURE_APPOINTMENTS=true
   FEATURE_EMAIL_COMPOSER=true
   FEATURE_CMS=true
   FEATURE_BLOG=true
   FEATURE_MESSAGES=true
   FEATURE_INSPECTION_REPORTS=true
   FEATURE_BILLING=true
   ```
3. Clear config cache: `php artisan config:clear`
4. In production, rebuild cache: `php artisan config:cache`

## Technical Details

**Boolean Coercion:**
- All feature flags use `filter_var(..., FILTER_VALIDATE_BOOLEAN)` in `config/business.php`
- This ensures flags always return proper booleans (never null)
- Empty/missing env variables default to `true` for BOOKING, MESSAGES, CMS
- BILLING and INSPECTION_REPORTS default to `false` (opt-in features)

**Best Practices:**
- Always read from config: `config('business.features.booking')` 
- Never use `env()` directly in application code
- After changing flags, clear views: `php artisan view:clear`
- Use explicit checks in routes and views to prevent errors

## Migration from Old Flags

If upgrading from an older version, these flags were consolidated:

| Old Flag | New Flag | Notes |
|----------|----------|-------|
| `FEATURE_APPOINTMENTS` | `FEATURE_BOOKING` | Renamed for clarity |
| *(none)* | `FEATURE_BILLING` | New flag added |
| `FEATURE_MESSAGES` | `FEATURE_MESSAGES` | Unchanged |
| `FEATURE_CMS` | `FEATURE_CMS` | Unchanged |
| `FEATURE_EMAIL_COMPOSER` | *(removed)* | Consolidated into messages |
| `FEATURE_SOCIAL_ACCOUNTS` | *(removed)* | Feature removed |
| `FEATURE_HOME_LANDING` | *(removed)* | Consolidated into CMS |
