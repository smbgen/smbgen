# Multi-Tenant Trial System - Setup Guide

## ✅ Implementation Complete

The multi-tenant trial system with super admin portal is now implemented. Here's what was added:

### Files Created

**Controllers:**
- `app/Http/Controllers/TrialSignupController.php` - Public trial signup flow
- `app/Http/Controllers/SuperAdmin/DashboardController.php` - Super admin dashboard
- `app/Http/Controllers/SuperAdmin/TenantController.php` - Tenant management

**Middleware:**
- `app/Http/Middleware/SuperAdmin.php` - Super admin access control

**Views:**
- `resources/views/trial/signup.blade.php` - Public trial signup form
- `resources/views/super-admin/dashboard.blade.php` - Super admin dashboard
- `resources/views/super-admin/tenants/index.blade.php` - Tenant list
- `resources/views/super-admin/tenants/show.blade.php` - Tenant details

**Database:**
- `database/migrations/2026_01_02_000002_add_super_admin_and_tenant_subscription_tracking.php`

**Configuration:**
- Updated `config/tenancy.php` - Made `central_domains` env-driven
- Updated `bootstrap/app.php` - Registered `superAdmin` middleware
- Updated `routes/web.php` - Added trial + super admin routes

---

## 🚀 Laravel Cloud Setup

### 1. Set Environment Variables

Add these to your Laravel Cloud environment:

```bash
# Tenancy
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=clientbridge-laravel-multi-tenant-main-6ymj12.laravel.cloud

# App URL
APP_URL=https://clientbridge-laravel-multi-tenant-main-6ymj12.laravel.cloud

# Database (use what Cloud provides)
DB_CONNECTION=mysql
DB_HOST=<cloud-db-host>
DB_PORT=3306
DB_DATABASE=<cloud-db-name>
DB_USERNAME=<cloud-db-user>
DB_PASSWORD=<cloud-db-password>

# Cache & Queue (recommended)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

### 2. Deploy to Cloud

Push your code and deploy:
```bash
git add .
git commit -m "Add multi-tenant trial system with super admin"
git push origin main
```

### 3. Run Migrations

After deployment, run migrations on Cloud:
```bash
php artisan migrate --force
```

### 4. Create Your Super Admin Account

Option A - Via Tinker (on Cloud):
```bash
php artisan tinker
$user = User::where('email', 'alex@oldlinecyber.com')->first();
$user->is_super_admin = true;
$user->save();
```

Option B - Via Database (direct):
```sql
UPDATE users SET is_super_admin = 1 WHERE email = 'alex@oldlinecyber.com';
```

### 5. Enable Background Workers

In Laravel Cloud dashboard, ensure:
- ✅ Queue worker is running (`php artisan queue:work`)
- ✅ Scheduler is enabled (runs `php artisan schedule:run` every minute)

---

## 🎯 Usage

### Public Trial Flow

1. **Trial Signup**: `https://<your-domain>/trial`
   - Users enter company name, email, password
   - System auto-creates tenant + subdomain
   - User is logged in as company administrator

2. **Tenant Access**: `https://<subdomain>.<your-domain>`
   - Each tenant gets isolated environment
   - 14-day trial period
   - Full feature access

### Super Admin Portal

1. **Access**: Login as super admin → `/super-admin`
2. **Features**:
   - View all tenants
   - Monitor trial status
   - Track revenue (MRR)
   - Impersonate any tenant
   - Suspend/activate tenants
   - View tenant users & domains

### Local Development

For local testing:

```bash
# Set in .env
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=127.0.0.1,localhost,clientbridge-laravel.test

# Run migrations
php artisan migrate

# Make yourself super admin
php artisan tinker
$user = User::find(1);
$user->is_super_admin = true;
$user->save();

# Test trial signup
Visit: http://clientbridge-laravel.test/trial
```

---

## 📋 Next Steps

### Phase 2: Domain Management (Optional)
- Add tenant custom domain settings page
- Implement DNS verification
- Enable domain switching

### Phase 3: Billing Integration (Optional)
- Connect Stripe subscriptions
- Auto-upgrade from trial to paid
- Handle failed payments

### Phase 4: Tenant Scoping (Critical)
- Add `tenant_id` to all existing tables
- Implement global scopes on models
- Test data isolation

---

## 🔍 Testing Checklist

- [ ] Public trial signup works
- [ ] Tenant is created with subdomain
- [ ] User can log in after signup
- [ ] Super admin can access `/super-admin`
- [ ] Super admin can view all tenants
- [ ] Super admin can impersonate tenants
- [ ] Super admin can suspend/activate tenants
- [ ] Trial expiration dates are tracked

---

## 🐛 Troubleshooting

**Issue: "Super admin access required" error**
- Solution: Make sure your user has `is_super_admin = true`

**Issue: Tenant migrations not running**
- Solution: Check `database/migrations/tenant/` folder exists
- Run: `php artisan tenants:migrate --tenants=<tenant-id>`

**Issue: Subdomain not resolving locally**
- Solution: Add to `/etc/hosts` or use Laravel Valet/Herd's `valet link --secure` with wildcard

**Issue: TENANCY_ENABLED not working**
- Solution: Clear config cache: `php artisan config:clear`

---

## 📚 Resources

- **Stancl/Tenancy Docs**: https://tenancyforlaravel.com/docs/v3
- **Laravel Cloud**: https://cloud.laravel.com
- **Domain Management**: For Dubsado-style domain UX, see `IMPLEMENTATION_FILES/tenant_domain_management.md` (create when ready)

---

**Status**: ✅ Ready for deployment to Laravel Cloud
**Last Updated**: January 2, 2026
