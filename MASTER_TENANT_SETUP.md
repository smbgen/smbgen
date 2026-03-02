# Master Tenant Setup Guide

## Architecture Overview

**Master Tenant (smbgen.com)** = Your marketing site with full CMS capabilities
- You manage this like any other tenant
- Edit CMS pages, manage users, create content
- This is what the public sees when they visit smbgen.com

**Super Admin Access** = Special access to manage ALL tenants
- Login as super admin at `/super-admin`
- Button to "Manage Master Tenant Site" switches you to master tenant admin
- You exist as both super admin AND company administrator on master tenant

## Setup Steps on Laravel Cloud

### 1. Update Environment Variables

In Laravel Cloud dashboard, set:
```
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=
MASTER_TENANT_ID=<will-fill-after-step-2>
```

### 2. Create Master Tenant via Tinker

SSH into Laravel Cloud and run:

```php
php artisan tinker

// Create master tenant
$masterTenant = App\Models\Tenant::create([
    'id' => \Illuminate\Support\Str::uuid(),
    'name' => 'SMBGen Marketing',
    'email' => 'admin@smbgen.com',
    'plan' => 'enterprise',
    'is_active' => true,
]);

// Add domain
$masterTenant->domains()->create([
    'domain' => 'smbgen.com'
]);

// IMPORTANT: Copy this UUID and add to MASTER_TENANT_ID in env
echo "Master Tenant ID: " . $masterTenant->id;

// Run migrations for master tenant database
php artisan tenants:migrate --tenants={paste-tenant-id-here}
```

### 3. Update MASTER_TENANT_ID in Environment

Take the UUID from step 2 and update:
```
MASTER_TENANT_ID=9d123456-7890-abcd-ef12-3456789abcde
```

### 4. Create Your User in Master Tenant Database

```php
php artisan tinker

// Initialize master tenant context
$tenant = App\Models\Tenant::find('your-master-tenant-id');
$tenant->run(function () {
    // Create your user as company administrator in master tenant
    App\Models\User::create([
        'name' => 'Your Name',
        'email' => 'your-email@example.com',
        'password' => bcrypt('your-password'),
        'role' => 'company_administrator',
        'email_verified_at' => now(),
        'tenant_id' => null, // Will be set automatically
    ]);
});
```

### 5. Test the Setup

1. Visit `smbgen.com/super-admin` - Login as super admin
2. Click "Manage Master Tenant Site" button
3. Should redirect to `smbgen.com/admin/dashboard`
4. You're now managing the master tenant's CMS and content

## How It Works

### Authentication Layers

1. **Super Admin** (Central DB, role = super_admin)
   - Access: `/super-admin`
   - Can view/manage all tenants
   - Can impersonate any tenant

2. **Company Administrator** (Tenant DB, role = company_administrator)
   - Access: `/admin/*`
   - Manages their tenant's site
   - Edit CMS, users, clients

3. **Client** (Tenant DB, role = client)
   - Access: `/dashboard`, `/documents`, etc.
   - Client portal only

### User Existence

Your account exists in TWO places:
- **Central Database** as super_admin (for managing all tenants)
- **Master Tenant Database** as company_administrator (for managing smbgen.com)

### Domain Resolution

- `smbgen.com` → Resolves to master tenant → Shows master tenant CMS/content
- `tenant.smbgen.com` → Resolves to tenant → Shows their CMS/content
- `customdomain.com` → Resolves to tenant → Shows their CMS/content

## Troubleshooting

**500 Error on all routes:**
- Check `TENANCY_ENABLED=true`
- Check `TENANCY_CENTRAL_DOMAINS=` (should be EMPTY or localhost only for local dev)

**"Master tenant not configured" error:**
- Set `MASTER_TENANT_ID` in environment variables
- Make sure UUID matches actual tenant ID

**Can't access master tenant:**
- Verify domain exists: `App\Models\Tenant::find('uuid')->domains`
- Check you have company_administrator role in master tenant DB

**CMS pages not showing:**
- Login to master tenant admin
- Create CMS page with slug 'home'
- Set is_published = true
