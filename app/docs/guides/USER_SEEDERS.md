# User Seeders

Reference for all user-seeding options in smbgen.

---

## Quick Reference

| Seeder | Use When | Password |
|---|---|---|
| `UserSeeder` | Fresh dev setup / `migrate:fresh --seed` | Hardcoded (printed to terminal) |
| `SuperAdminSeeder` | Creating/resetting admin in any env | Auto-generated random 32-char |
| `UserSeederWithPasswordPrompt` | You want to set your own password interactively | Prompted at runtime |

---

## UserSeeder (default)

Run automatically as part of `DatabaseSeeder`:

```bash
php artisan migrate:fresh --seed
```

Or run alone:

```bash
php artisan db:seed --class=UserSeeder
```

**Creates:**

| Field | Value |
|---|---|
| Email | `admin@smbgen.com` |
| Password | `JUHeKKEcg~y2Z7q9Wd2M9UmqnQ~^ZeQtzP` |
| Role | `company_administrator` |
| Email verified | Yes |

Also creates a demo client user (`demo@smbgen.com`) with a randomly generated password — printed to the terminal on first creation.

**Notes:**
- Uses `firstOrCreate` — safe to re-run, will not overwrite existing users
- Credentials are printed to the terminal **only on first creation**

---

## SuperAdminSeeder

Best option for creating or **resetting** the admin account in any environment. Generates a secure random 32-character password each run.

```bash
php artisan db:seed --class=SuperAdminSeeder
```

**Reads from `.env`** (falls back to defaults if not set):

```env
ADMIN_EMAIL=admin@smbgen.com
ADMIN_NAME=Admin
```

**Output example:**
```
═══════════════════════════════════════════════════════════
  ADMIN USER CREATED
═══════════════════════════════════════════════════════════

Admin Credentials:
  Email:    admin@smbgen.com

  PASSWORD: <random-32-char-password>

Access URL: http://smbgen.test/admin/dashboard

═══════════════════════════════════════════════════════════
⚠️  SAVE THESE CREDENTIALS NOW! They will not be shown again.
💡 You can reset password via: /forgot-password
═══════════════════════════════════════════════════════════
```

**Notes:**
- Uses `updateOrCreate` — **will reset the password** if admin already exists
- Always prints credentials to terminal regardless of whether account is new or updated
- Useful for production first-time setup and password recovery

---

## UserSeederWithPasswordPrompt

Interactive seeder — prompts you to enter the password at the terminal. Good when you want to set a known password from the start.

```bash
php artisan db:seed --class=UserSeederWithPasswordPrompt
```

Prompts:
1. Create Admin User? → enter password for `admin@smbgen.com`
2. Create Demo Client? → enter password for `demo@smbgen.com`

**Notes:**
- Uses `updateOrCreate` — will overwrite existing passwords
- Either user can be skipped at the prompt

---

## Roles

| Role constant | Value | Access |
|---|---|---|
| `User::ROLE_ADMINISTRATOR` | `company_administrator` | Full admin panel (`/admin/*`) |
| `User::ROLE_CLIENT` | `client` | Client portal (`/dashboard`) |
| `User::ROLE_USER` | `user` | Client portal (`/dashboard`) |

---

## Resetting Admin Password

**Via seeder (easiest):**

```bash
php artisan db:seed --class=SuperAdminSeeder
```

**Via tinker:**

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::where('email', 'admin@smbgen.com')
    ->update(['password' => Hash::make('your-new-password')]);
```

**Via the app:**
Navigate to `/forgot-password` and use the email reset flow.

---

## Verifying Admin Access

After seeding, check the account exists and has the right role:

```bash
php artisan tinker
```

```php
User::where('email', 'admin@smbgen.com')->first(['id', 'email', 'role', 'email_verified_at']);
```

Login at `/login` → redirects to `/admin/dashboard` for `company_administrator` role.

---

## Related

- [Developer Setup](../planning/DEVELOPMENT_SETUP.md)
- [Email Verification](./EMAIL_VERIFICATION_TESTING.md)

---

**Last Updated:** March 2026
