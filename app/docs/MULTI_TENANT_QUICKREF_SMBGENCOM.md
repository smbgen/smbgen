# SMBGen Multi-Tenant Quick Reference

## URL Structure

| URL | Purpose | Notes |
|-----|---------|-------|
| `smbgen.com` | Marketing homepage | Central domain |
| `smbgen.com/trial` | Trial signup form | Public, no auth |
| `smbgen.com/super-admin` | Your control panel | Super admin only |
| `smbgen.com/{slug}` | Customer instance | Path-based tenant |
| `smbgen.com/{slug}/login` | Customer login | Tenant-scoped |
| `smbgen.com/{slug}/admin/dashboard` | Customer admin | Company administrator |

## Key Commands

### Development
```bash
composer dev                  # Start dev server
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Fresh migrations (WARNING: deletes data)
```

### Tenancy Management
```bash
# List tenants
php artisan tenants:list

# Run migrations for all tenants
php artisan tenants:migrate

# Run command for specific tenant
php artisan tenants:run {tenant-id} -- migrate

# Seed specific tenant
php artisan tenants:seed --tenant={tenant-id}
```

### Super Admin User Creation
```bash
php artisan tinker
>>> App\Models\User::create([
...     'name' => 'Your Name',
...     'email' => 'your@email.com',
...     'password' => bcrypt('password'),
...     'role' => 'company_administrator',
...     'is_super_admin' => true,
...     'email_verified_at' => now(),
... ]);
```

## File Locations

| Component | Path |
|-----------|------|
| Tenancy Config | [config/tenancy.php](config/tenancy.php) |
| Tenant Model | [app/Models/Tenant.php](app/Models/Tenant.php) |
| Routes | [routes/web.php](routes/web.php) |
| Middleware Setup | [bootstrap/app.php](bootstrap/app.php) |
| Super Admin Portal | [app/Http/Controllers/SuperAdmin/](app/Http/Controllers/SuperAdmin/) |
| Trial Signup | [app/Http/Controllers/TrialSignupController.php](app/Http/Controllers/TrialSignupController.php) |

## Environment Variables

### Development
```env
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=127.0.0.1,localhost,smbgen.test
```

### Production (smbgen.com)
```env
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=smbgen.com
APP_URL=https://smbgen.com
APP_ENV=production
APP_DEBUG=false
```

### Customer Dedicated Instance
```env
TENANCY_ENABLED=false
APP_URL=https://app.customer-domain.com
```

## Super Admin Features

Access: `smbgen.com/super-admin`

- **Dashboard:** Overview of all tenants
- **Tenants:** Create, edit, suspend, activate
- **Domains:** Add custom domains to tenants
- **Users:** Manage super admin users
- **Diagnostics:** System health, run migrations, clear cache
- **Impersonate:** Login as tenant admin (for support)

## How Tenancy Works

1. Request: `smbgen.com/acme/dashboard`
2. Checks: Is `smbgen.com` in `TENANCY_CENTRAL_DOMAINS`? Yes
3. Extracts: `acme` from path
4. Finds: Tenant with domain/id `acme`
5. Switches: Database connection to `tenantacme` or `tenant{uuid}`
6. Scopes: All queries to that tenant's database

## Database Schema

### Central Database
- `tenants` - All tenant records
- `domains` - Tenant domain/slug mappings
- `users` - Central users (super admins only)
- `subscription_tiers` - Available plans

### Tenant Database (per tenant)
- `users` - Tenant's users
- `clients` - Tenant's clients
- `bookings` - Tenant's bookings
- `cms_pages` - Tenant's CMS content
- `invoices` - Tenant's invoices
- (All tenant-scoped data)

## Workflow: Trial to Production

### Trial Phase (smbgen.com)
1. Customer signs up at `/trial`
2. Gets `smbgen.com/{slug}`
3. Uses for 14-30 days

### Conversion
1. Customer likes product
2. Signs contract with you
3. Choose path:
   - **Path A:** Stay on `smbgen.com/{slug}` (cheaper, upgrade tier)
   - **Path B:** Deploy dedicated instance (premium, white-label)

### Path B: Dedicated Deployment
1. Deploy new Laravel Cloud instance
2. Set `TENANCY_ENABLED=false`
3. Export trial data
4. Import to dedicated instance
5. Point customer domain
6. Customer pays hosting + your fee

## Common Issues

### "Tenant not found"
- Check slug matches domain in `domains` table
- Verify `TENANCY_ENABLED=true`
- Clear config cache: `php artisan config:clear`

### Super Admin 403
- Check `is_super_admin` = 1 in users table
- Access `/super-admin` without tenant prefix
- Super admin routes are central (not tenant-scoped)

### Subdomain not working
- You're using **path-based**, not subdomain-based
- URLs: `smbgen.com/tenant` not `tenant.smbgen.com`
- To switch: change middleware to `InitializeTenancyByDomain`

## Marketing Site Options

### Option 1: CMS Homepage
- Super Admin → CMS → Create page `home`
- Set as published
- Customize via drag-and-drop

### Option 2: Static View
- Edit `resources/views/landing.blade.php`
- Add marketing copy, CTAs
- Link to `/trial` signup

### Option 3: External Site
- Separate marketing site elsewhere
- Link to `smbgen.com/trial` for signups

## Billing/Payments

### Trial Users (Free)
- Tier: "trial"
- Duration: 14-30 days
- Features: Full or limited

### Paid Users (Shared)
- Stay at `smbgen.com/{slug}`
- Upgrade tier via Stripe
- Monthly/yearly billing
- Feature gates based on tier

### Dedicated Users (Premium)
- Own Laravel Cloud instance
- Own domain
- Pay hosting directly
- Pay you service fee

## Links

- **Setup Guide:** [OPTION_A_SETUP.md](OPTION_A_SETUP.md)
- **Deployment Checklist:** [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)
- **Laravel Tenancy Docs:** https://tenancyforlaravel.com/docs/v3
- **Stancl/Tenancy GitHub:** https://github.com/archtechx/tenancy
- **Laravel Cloud:** https://cloud.laravel.com

## Support Contacts

- Super Admin: `alex@oldlinecyber.com`
- Platform: `smbgen.com`
- Status Page: `smbgen.com/super-admin/diagnostics`
