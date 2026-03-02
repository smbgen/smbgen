# Super Admin Setup Guide

## Overview

Super Admins are special users who manage the entire multi-tenant platform from the central/landlord application. They can:

- View all tenants and their statistics
- Create, suspend, activate, and delete tenants
- Manage tenant domains and subscriptions
- Impersonate tenant administrators
- Extend trial periods
- Monitor platform health and revenue

**Important**: Super admins exist ONLY in the central database and are NOT tenant users. They operate outside the tenant context.

## Creating a Super Admin

### Method 1: Using the Seeder (Recommended)

Run the SuperAdminSeeder to create a super admin with a randomly generated secure password:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

**Output:**
```
═══════════════════════════════════════════════════════════
  SUPER ADMIN CREATED
═══════════════════════════════════════════════════════════

Super Admin Credentials:
  Email:    superadmin@clientbridge.app
  Password: JUHeKKEcg~y2Z7q9Wd2M9UmqnQ~^ZeQtzP
  Role:     super_admin

Access URL: https://your-app.test/super-admin

═══════════════════════════════════════════════════════════
⚠️  SAVE THESE CREDENTIALS NOW! They will not be shown again.
💡 You can reset password via: /forgot-password
🔒 This account can manage ALL tenants in the system.
═══════════════════════════════════════════════════════════
```

**⚠️ IMPORTANT**: Save the generated password immediately! It will not be displayed again.

### Method 2: Using Custom Email/Name

Override the default email and name using environment variables:

```bash
SUPER_ADMIN_EMAIL=admin@yourcompany.com \
SUPER_ADMIN_NAME="John Doe" \
php artisan db:seed --class=SuperAdminSeeder
```

Or add to your `.env` file:

```env
SUPER_ADMIN_EMAIL=admin@yourcompany.com
SUPER_ADMIN_NAME="Platform Administrator"
```

Then run:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

### Method 3: Using Tinker (Manual)

If you need to create a super admin manually with a specific password:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@clientbridge.app',
    'password' => Hash::make('your-secure-password'),
    'role' => 'super_admin',
    'email_verified_at' => now(),
    'tenant_id' => null,
]);
```

## Accessing the Super Admin Dashboard

1. Navigate to your application's login page: `/login`
2. Enter the super admin credentials
3. You will be automatically redirected to: `/super-admin`

## Super Admin Features

### Dashboard (`/super-admin`)

View platform-wide statistics:
- Total tenants
- Active trials
- Paying customers
- Monthly Recurring Revenue (MRR)
- Recent tenant signups

### Tenant Management (`/super-admin/tenants`)

**List & Search Tenants:**
- Search by name, email, or subdomain
- Filter by plan (trial, starter, professional, enterprise)
- Filter by status (active/inactive)
- Paginated results

**View Tenant Details** (`/super-admin/tenants/{tenant}`):
- Tenant information (name, email, plan, status)
- Trial expiration date
- Associated domains
- List of tenant users and their roles
- Billing information (Stripe IDs)

**Tenant Actions:**
- **Impersonate**: Log in as the tenant's admin to help troubleshoot issues
- **Suspend**: Deactivate tenant access (sets `is_active = false`)
- **Activate**: Reactivate a suspended tenant
- **Delete**: Permanently remove tenant and all associated data (⚠️ dangerous!)

### Domain Management

Add, remove, or set primary domains for tenants:
- Add custom domains: `tenant.example.com` or `customdomain.com`
- Remove non-primary domains
- Set primary domain for tenant access

### Subscription Management

- **Upgrade Plan**: Manually change tenant's plan (trial → starter → professional → enterprise)
- **Extend Trial**: Add 1-90 days to trial period
- View Stripe customer and subscription IDs

### Impersonation

When impersonating a tenant admin:
1. You'll be logged in as their company administrator
2. You have full access to their tenant database
3. Your original super admin session is preserved
4. All actions are logged as the impersonated user

**To stop impersonating**: (Feature coming soon - currently requires re-login)

## Security Best Practices

### 1. Protect Super Admin Credentials

- Use a password manager to store credentials
- Generate strong passwords (32+ characters)
- Enable 2FA if available (future feature)
- Never share super admin credentials

### 2. Limit Super Admin Accounts

- Create only as many super admins as necessary
- Each admin should have their own account
- Remove accounts when team members leave

### 3. Monitor Super Admin Activity

- All super admin actions are logged in the activity log
- Review impersonation sessions regularly
- Monitor tenant deletions and suspensions

### 4. Environment-Specific Access

**Local Development:**
```env
SUPER_ADMIN_EMAIL=superadmin@clientbridge.test
```

**Staging:**
```env
SUPER_ADMIN_EMAIL=admin@staging.clientbridge.com
```

**Production:**
```env
SUPER_ADMIN_EMAIL=admin@clientbridge.com
```

## Multi-Tenancy Architecture

### Central vs Tenant Database

ClientBridge uses Stancl Tenancy for complete data isolation:

**Central Database** (landlord):
- Stores `tenants` table
- Stores `domains` table
- Stores super admin users (`role = 'super_admin'`, `tenant_id = null`)
- Manages tenant configuration

**Tenant Databases** (separate per tenant):
- Stores tenant-specific data (clients, bookings, invoices, users, etc.)
- Each tenant has their own MySQL database: `tenant{uuid}`
- Company administrators and client users live here
- Complete data isolation between tenants

### User Roles Explained

| Role | Database | Access Level | Description |
|------|----------|--------------|-------------|
| `super_admin` | Central | All tenants | Platform administrator |
| `company_administrator` | Tenant | Single tenant | Tenant's admin |
| `client` | Tenant | Limited | Tenant's client portal user |

### Tenancy Flow

1. **Super admin logs in** → Central database, `/super-admin` dashboard
2. **Tenant admin logs in** → Redirected to tenant context via subdomain/domain
3. **Tenant context initialized** → Switches to tenant's database automatically
4. **All queries scoped** → Only sees their tenant's data

## Common Tasks

### Reset Super Admin Password

If you forget the super admin password:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::where('role', 'super_admin')
    ->where('email', 'superadmin@clientbridge.app')
    ->first();

$admin->update([
    'password' => Hash::make('new-secure-password')
]);

echo "Password reset successfully!";
```

### Create Additional Super Admins

Run the seeder with a different email:

```bash
SUPER_ADMIN_EMAIL=admin2@clientbridge.com \
SUPER_ADMIN_NAME="Second Admin" \
php artisan db:seed --class=SuperAdminSeeder
```

### List All Super Admins

```bash
php artisan tinker
```

```php
\App\Models\User::where('role', 'super_admin')->get(['id', 'name', 'email']);
```

### Remove Super Admin Access

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'old-admin@example.com')->delete();
```

## Troubleshooting

### "403 Unauthorized" When Accessing `/super-admin`

**Cause**: User doesn't have `role = 'super_admin'`

**Solution**: Verify the user's role in the database:

```sql
SELECT id, email, role FROM users WHERE email = 'superadmin@clientbridge.app';
```

If role is incorrect, update it:

```php
\App\Models\User::where('email', 'superadmin@clientbridge.app')
    ->update(['role' => 'super_admin']);
```

### Login Redirects to `/dashboard` Instead of `/super-admin`

**Cause**: Login redirect logic doesn't recognize super admin role

**Solution**: Check [app/Http/Controllers/Auth/AuthenticatedSessionController.php](../Http/Controllers/Auth/AuthenticatedSessionController.php):

```php
// Should redirect super_admin to /super-admin
if ($user->role === 'super_admin') {
    return redirect('/super-admin');
}
```

### Can't See Tenants Table Error

**Cause**: Migrations haven't been run

**Solution**:

```bash
php artisan migrate
```

Ensure migration `2026_01_02_000002_add_super_admin_and_tenant_subscription_tracking.php` has run.

### Super Admin Appears in Tenant Context

**Cause**: `tenant_id` is not null

**Solution**:

```php
\App\Models\User::where('role', 'super_admin')
    ->update(['tenant_id' => null]);
```

Super admins should NEVER have a `tenant_id`.

## Production Deployment Checklist

- [ ] Create super admin via seeder
- [ ] Save credentials in secure password manager
- [ ] Change password after first login
- [ ] Verify access to `/super-admin`
- [ ] Test tenant creation
- [ ] Test tenant impersonation
- [ ] Configure `SUPER_ADMIN_EMAIL` in production `.env`
- [ ] Document super admin email for team
- [ ] Set up activity log monitoring
- [ ] Test Stripe subscription webhooks

## Related Documentation

- [Multi-Tenant Setup](../MULTI_TENANT_SETUP.md)
- [Subscription System](../STRIPE_SUBSCRIPTION_SETUP.md)
- [Domain Management](../DOMAIN_CONNECTION_GUIDE.md)
- [Tenancy Best Practices](./planning/MULTI_TENANCY_IMPLEMENTATION.md)

## Support

For issues or questions about super admin setup:
1. Check the troubleshooting section above
2. Review the activity logs for errors
3. Consult the development team

---

**Last Updated**: January 4, 2026  
**Version**: 1.0.0
