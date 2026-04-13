# Spec: `tenant_admin` Role + Self-Serve Google Onboarding

## Context

smbgen is a Laravel 12 / PHP 8.4 SaaS platform. The current role system has:

- `company_administrator` — smbgen super-admin (the platform owner). Full access to everything.
- `client` — end-customers of businesses served by the platform.
- `user` — generic, barely used.

**Problem:** There is no role for a paying customer (e.g. a real estate agent) who signs up and manages their own instance. They need access to the admin dashboard scoped to their own data only — not global access that `company_administrator` has.

**Goal:** Add a `tenant_admin` role for self-serve paying customers. They sign up via Google OAuth, land in the admin dashboard, and only see their own data. The `company_administrator` retains super-admin visibility across all tenants.

---

## Role Definitions (after this change)

| Role | Who | Access |
|------|-----|--------|
| `company_administrator` | smbgen platform owner | Full access, all tenants, super-admin views |
| `tenant_admin` | Paying customer (e.g. realtor) | Admin dashboard, own data only |
| `client` | End-customer of a tenant | Client portal only |
| `user` | Generic | Basic dashboard |

---

## Files to Change

### 1. `app/Models/User.php`

Add the new role constant alongside existing ones:

```php
const ROLE_USER = 'user';
const ROLE_CLIENT = 'client';
const ROLE_ADMINISTRATOR = 'company_administrator';
const ROLE_ADMINISTRATOR_LEGACY = 'administrator';
const ROLE_TENANT_ADMIN = 'tenant_admin'; // ADD THIS
```

Update `isAdministrator()` to include `tenant_admin` so they pass the `CompanyAdministrator` middleware and access the admin dashboard:

```php
public function isAdministrator(): bool
{
    return in_array($this->role, [
        self::ROLE_ADMINISTRATOR,
        self::ROLE_ADMINISTRATOR_LEGACY,
        self::ROLE_TENANT_ADMIN, // ADD THIS
    ], true);
}
```

Add a new `isSuperAdmin()` method for places only the platform owner should access:

```php
public function isSuperAdmin(): bool
{
    return in_array($this->role, [
        self::ROLE_ADMINISTRATOR,
        self::ROLE_ADMINISTRATOR_LEGACY,
    ], true);
}
```

Add a `isTenantAdmin()` convenience method:

```php
public function isTenantAdmin(): bool
{
    return $this->role === self::ROLE_TENANT_ADMIN;
}
```

---

### 2. `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

**`handleGoogleCallback()`** — currently assigns `role = 'client'` to all new Google OAuth signups. Change it so new signups via the public registration flow get `tenant_admin` instead:

```php
$user = User::firstOrCreate(
    ['email' => $googleUser->getEmail()],
    [
        'name'              => $googleUser->getName(),
        'google_id'         => $googleUser->getId(),
        'role'              => User::ROLE_TENANT_ADMIN, // CHANGED from 'client'
        'email_verified_at' => now(),
        'password'          => Hash::make(Str::random(32)),
    ]
);
```

**Redirect after Google login** — update the redirect logic to handle `tenant_admin`:

```php
if ($user->role === User::ROLE_ADMINISTRATOR || $user->role === User::ROLE_TENANT_ADMIN) {
    return redirect()->intended('/admin/dashboard');
} else {
    return redirect()->intended('/dashboard');
}
```

Also update the identical redirect block in `store()` (email/password login):

```php
if ($user->role === 'company_administrator' || $user->role === 'tenant_admin') {
    return redirect()->route('admin.dashboard');
} else {
    return redirect()->route('dashboard');
}
```

---

### 3. `app/Http/Controllers/Auth/VerifyEmailController.php`

Two places redirect based on `company_administrator` — update both to also handle `tenant_admin`:

```php
// Before:
if ($user->role === 'company_administrator') {
// After:
if ($user->role === 'company_administrator' || $user->role === 'tenant_admin') {
```

---

### 4. `app/Http/Middleware/CompanyAdministrator.php`

No change needed here — it calls `isAdministrator()` which we already updated to include `tenant_admin`. Verify it reads:

```php
if (auth()->check() && auth()->user()->isAdministrator()) {
    return $next($request);
}
```

---

### 5. Add `SuperAdministrator` Middleware (new file)

Create `app/Http/Middleware/SuperAdministrator.php` for routes only the platform owner (`company_administrator`) should access:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Platform administrator access required.');
    }
}
```

Register it in `bootstrap/app.php` (Laravel 12 style) alongside `CompanyAdministrator`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        // existing aliases...
        'super.admin' => \App\Http\Middleware\SuperAdministrator::class,
    ]);
})
```

---

### 6. `routes/web.php` — Protect Super-Admin-Only Routes

Wrap any routes that should be visible only to the platform owner (not tenant admins) with the new `super.admin` middleware. These are the routes that show cross-tenant data:

- Any route showing all users across tenants
- Platform billing/subscription management
- Any future "all tenants" overview dashboard

Example pattern:

```php
Route::middleware(['auth', 'super.admin'])->group(function () {
    // platform-owner-only routes here
});
```

For now, identify and wrap at minimum:
- `admin.users.index` (list of all users)
- Any route in the admin panel that queries users/bookings/leads without a `user_id` scope

---

### 7. Admin Dashboard Views — Conditional Super-Admin UI

In admin blade views, hide cross-tenant UI elements from `tenant_admin` users. Use the new helper:

```blade
@if(auth()->user()->isSuperAdmin())
    {{-- Platform-wide stats, all-users tables, tenant management --}}
@endif
```

Specifically audit:
- `resources/views/admin/` — any view with "all users" tables or platform-level metrics
- The sidebar/nav — hide any links to user management or platform settings for `tenant_admin`

---

### 8. `app/Policies/InvoicePolicy.php`

Currently:
```php
return $user->id === $invoice->user_id || $user->role === 'company_administrator';
```

Update to include `tenant_admin` for their own invoices:
```php
return $user->id === $invoice->user_id
    || $user->role === 'company_administrator'
    || $user->role === 'tenant_admin';
```

---

### 9. Migration (documentation only, no schema change needed)

No database migration is required — `role` is already a string column. The new value `tenant_admin` is just a new string. However, add a comment in the users migration or a seeder note documenting the valid role values:

```
Valid roles: user, client, company_administrator, administrator (legacy), tenant_admin
```

---

### 10. Onboarding Redirect After Google Signup (new behavior)

After a **new** `tenant_admin` signs up via Google OAuth for the first time, redirect them to an onboarding wizard instead of the dashboard directly. Detect "new user" by checking if `googleCredential` is null:

In `handleGoogleCallback()`, after login:

```php
if ($user->wasRecentlyCreated && $user->role === User::ROLE_TENANT_ADMIN) {
    return redirect()->route('onboarding.start');
}

if ($user->role === User::ROLE_ADMINISTRATOR || $user->role === User::ROLE_TENANT_ADMIN) {
    return redirect()->intended('/admin/dashboard');
}
```

The onboarding route `onboarding.start` does not need to be built in this task — just ensure the redirect target exists or fails gracefully (redirect to `/admin/dashboard` if route not found). The onboarding wizard is a separate spec.

---

## What NOT to Change in This Task

- Do not restructure the admin dashboard views beyond the conditional super-admin UI hiding described above.
- Do not touch the booking system, availability, or Google Calendar service — those are already correctly scoped by `user_id`.
- Do not add multi-tenancy database isolation — the existing `user_id` scoping on bookings, leads, and availability is sufficient for the first 20-30 tenants.
- Do not change the `client` role or client portal.

---

## Testing Checklist

After implementation, verify:

- [ ] New Google OAuth signup → user gets `role = tenant_admin`
- [ ] `tenant_admin` user → redirected to `/admin/dashboard` after login
- [ ] `tenant_admin` user → passes `CompanyAdministrator` middleware
- [ ] `tenant_admin` user → blocked by `super.admin` middleware (403)
- [ ] `company_administrator` user → passes both middlewares
- [ ] `client` user → still redirected to `/dashboard`, not admin
- [ ] `User::isAdministrator()` returns true for `tenant_admin`
- [ ] `User::isSuperAdmin()` returns false for `tenant_admin`
- [ ] `User::isSuperAdmin()` returns true for `company_administrator`
- [ ] Existing email/password login still works for all roles
- [ ] Existing `company_administrator` session unaffected

---

## Stack Reference

- Laravel 12 / PHP 8.4
- Pest PHP for tests — write feature tests in `tests/Feature/Auth/` for the checklist above
- Middleware registration is in `bootstrap/app.php` (not `Kernel.php` — Laravel 12 style)
- Run tests: `/c/Users/alexr/.config/herd/bin/php84/php.exe artisan test`
