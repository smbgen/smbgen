# Custom Artisan Commands Guide

This document covers all custom Artisan commands in the CLIENTBRIDGE application, with detailed examples and use cases.

## Quick Reference

| Command | Purpose | Common Usage |
|---------|---------|--------------|
| `email:test` | Send test emails with tracking | `php artisan email:test --count=5 --to=test@example.com` |
| `email:templates` | Preview all email templates | `php artisan email:templates your@email.com` |
| `email:audit` | Audit/debug sent emails | `php artisan email:audit 123 --preview` |
| `calendar:diagnose` | Debug Google Calendar setup | `php artisan calendar:diagnose` |
| `deploy:notify` | Send deployment notifications | `php artisan deploy:notify --commits=20` |

---

## Email Commands

### `email:test` - Send Test Emails with Tracking

Sends test emails with tracking pixels and clickable links to verify email deliverability and tracking functionality.

**Signature:**
```bash
php artisan email:test {--count=10} {--to=}
```

**Options:**
- `--count=N` - Number of test emails to send (default: 10)
- `--to=EMAIL` - Recipient email address (defaults to first admin user)

**Examples:**

Send 10 test emails to the first admin:
```bash
php artisan email:test
```

Send 5 test emails to a specific address:
```bash
php artisan email:test --count=5 --to=test@example.com
```

Send 100 emails for stress testing:
```bash
php artisan email:test --count=100 --to=devtest@example.com
```

**What it does:**
- Creates tracked emails with unique tracking IDs
- Includes open tracking pixels
- Adds 3 clickable links per email (tracks clicks)
- Records all activity in `email_logs` table
- Shows progress bar during sending
- Provides summary with deliverability dashboard link

**Use Cases:**
- Testing email server configuration
- Verifying tracking pixel functionality
- Checking click tracking
- Load testing email delivery
- Debugging deliverability issues

**Notes:**
- Includes 100ms delay between emails to avoid rate limiting
- All emails are logged in the admin email logs dashboard
- View results at: `/admin/email-logs`

---

### `email:templates` - Preview All Email Templates

Sends all application email templates to a specified address with dummy data. Great for reviewing email designs and testing layouts.

**Signature:**
```bash
php artisan email:templates {email?} {--template=}
```

**Arguments:**
- `email` - Email address to send templates to (will prompt if not provided)

**Options:**
- `--template=NAME` - Send only a specific template

**Available Templates:**
- `booking-confirmation` - Booking/Appointment Confirmation
- `client-portal-access` - Client Portal Access Credentials
- `cms-form-admin` - CMS Form Submission (Admin Notification)
- `cms-form-client` - CMS Form Submission (Client Confirmation)
- `contact-inquiry-received` - Contact Inquiry Received (Auto-reply)
- `contact-inquiry-admin` - New Contact Inquiry (Admin Notification)
- `inspection-report` - Inspection Report
- `invoice` - Invoice with PDF attachment
- `magic-link` - Magic Login Link
- `new-lead` - New Lead Submitted
- `new-message` - New Message Received
- `server-error` - Server Error Notification

**Examples:**

Send all templates:
```bash
php artisan email:templates designer@example.com
```

Send only the invoice template:
```bash
php artisan email:templates designer@example.com --template=invoice
```

Interactive mode (prompts for email):
```bash
php artisan email:templates
```

**What it does:**
- Generates realistic dummy data for each template
- Creates temporary database records if needed (bookings, invoices, etc.)
- Sends each template with proper formatting
- Shows progress and success/failure for each template
- Cleans up temporary data after sending

**Use Cases:**
- Reviewing email designs before deployment
- Testing email layouts on different clients
- Sharing templates with designers/stakeholders
- QA testing of email content
- Debugging template rendering issues

**Notes:**
- Some templates create temporary database records (automatically cleaned up)
- PDF attachments are generated for invoice emails
- Uses real Mail driver (not queued)

---

### `email:audit` - Audit Email Logs

Inspect, preview, or resend previously sent emails from the email logs table. Useful for debugging email issues.

**Signature:**
```bash
php artisan email:audit {id} {--preview} {--send-to=} {--raw}
```

**Arguments:**
- `id` - Email log ID (from `email_logs` table)

**Options:**
- `--preview` - Show text-only preview in terminal
- `--send-to=EMAIL` - Send a test copy to this address
- `--raw` - Show raw HTML source

**Examples:**

Preview email #123:
```bash
php artisan email:audit 123 --preview
```

View raw HTML:
```bash
php artisan email:audit 123 --raw
```

Send a test copy:
```bash
php artisan email:audit 123 --send-to=test@example.com
```

Show metadata only:
```bash
php artisan email:audit 123
```

**What it displays:**
- Email status (sent, delivered, bounced, etc.)
- Recipient addresses (to, cc)
- Subject line
- Timestamps (sent, delivered, opened)
- Open count and click count
- Tracking ID
- Full email body (if requested)

**Use Cases:**
- Debugging why an email wasn't delivered
- Verifying email content after send
- Resending emails for testing
- Checking tracking data
- Investigating customer reports of missing emails

**Notes:**
- Email log IDs can be found in `/admin/email-logs`
- Preview mode strips HTML and shows plain text
- Test sends use the same HTML but new tracking IDs

---

## Calendar Commands

### `calendar:diagnose` - Diagnose Google Calendar Issues

Comprehensive diagnostic tool for troubleshooting Google Calendar integration issues.

**Signature:**
```bash
php artisan calendar:diagnose {--migrate}
```

**Options:**
- `--migrate` - Migrate legacy calendar data from `users` table to `google_credentials` table

**Examples:**

Run diagnostics:
```bash
php artisan calendar:diagnose
```

Run diagnostics and migrate legacy data:
```bash
php artisan calendar:diagnose --migrate
```

**What it checks:**
- ✅ Google OAuth configuration (client ID, secret, redirect URIs)
- ✅ Database schema (tables and columns)
- ✅ User connections (new and legacy)
- ✅ Token expiration status
- ✅ Google Client library availability
- ✅ Error logs for calendar-related issues

**What it reports:**
- Users with active Google Calendar connections
- Users with expired tokens
- Users with legacy data that needs migration
- Configuration issues
- Recent calendar-related errors

**Use Cases:**
- Troubleshooting "Calendar not connecting" issues
- Migrating from old to new calendar storage
- Verifying OAuth configuration
- Checking token expiration
- Investigating sync failures

**Migration Details:**
The `--migrate` flag:
- Copies `google_refresh_token` from `users` table
- Copies `google_calendar_id` from `users` table
- Creates new `GoogleCredential` records
- Preserves existing data (non-destructive)
- Only migrates users without existing credentials

**Notes:**
- Safe to run multiple times
- Does not modify production data without `--migrate`
- Outputs detailed diagnostic information
- Checks for common misconfigurations

---

## Deployment Commands

### `deploy:notify` - Send Deployment Notifications

Sends an email notification with recent git commit history after a deployment.

**Signature:**
```bash
php artisan deploy:notify {--commits=10}
```

**Options:**
- `--commits=N` - Number of recent commits to include (default: 10)

**Examples:**

Send notification with last 10 commits:
```bash
php artisan deploy:notify
```

Send notification with last 25 commits:
```bash
php artisan deploy:notify --commits=25
```

**What it includes:**
- Current git branch
- Current commit hash
- Deployment timestamp (with timezone)
- Environment (production, staging, etc.)
- List of recent commits with:
  - Commit hash
  - Author
  - Date
  - Commit message

**Recipient:**
Uses the first available:
1. `business.contact.email` config value
2. `mail.from.address` config value

**Use Cases:**
- Notifying team of production deploys
- Creating deployment audit trail
- Tracking what changed in each deploy
- Debugging "when did X change" questions

**Integration with CI/CD:**

Add to your deployment script:
```bash
# After successful deployment
php artisan deploy:notify --commits=20
```

Or in Laravel Forge deployment script:
```bash
cd /home/forge/yoursite.com
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan deploy:notify --commits=15
```

**Notes:**
- Requires git to be available
- Safe to run even if not a git repository (skips commit history)
- Email template: `resources/views/emails/deployment-notification.blade.php`

---

## User Management (Using Tinker)

While there's no dedicated user creation command, you can easily create users with passwords using Artisan Tinker.

### Create a User with Password

**Basic syntax:**
```bash
php artisan tinker --execute="App\Models\User::create(['name' => 'NAME', 'email' => 'EMAIL', 'password' => 'PASSWORD', 'role' => 'ROLE']);"
```

**Examples:**

Create an admin user:
```bash
php artisan tinker --execute="App\Models\User::create(['name' => 'Admin User', 'email' => 'admin@clientbridge.app', 'password' => 'SecurePassword123!', 'role' => 'company_administrator']);"
```

Create a regular user:
```bash
php artisan tinker --execute="App\Models\User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password123', 'role' => 'user']);"
```

Create a client user:
```bash
php artisan tinker --execute="App\Models\User::create(['name' => 'Client Name', 'email' => 'client@example.com', 'password' => 'ClientPass123', 'role' => 'client']);"
```

**Available Roles:**
- `company_administrator` - Full admin access
- `user` - Regular staff user
- `client` - Client portal access only

**Multi-line approach for complex creation:**

```bash
php artisan tinker
```

Then in the Tinker REPL:
```php
$user = App\Models\User::create([
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'password' => 'SecurePassword456!',
    'role' => 'company_administrator',
    'email_verified_at' => now(),
]);

echo "Created user #{$user->id}: {$user->email}\n";
```

### Update User Password

Change existing user's password:
```bash
php artisan tinker --execute="\$user = App\Models\User::where('email', 'admin@example.com')->first(); \$user->password = 'NewPassword123'; \$user->save(); echo 'Password updated';"
```

Or in Tinker REPL:
```php
$user = App\Models\User::where('email', 'admin@example.com')->first();
$user->password = 'NewSecurePassword!';
$user->save();
echo "Password updated for {$user->name}";
```

### List All Users

```bash
php artisan tinker --execute="App\Models\User::all()->each(fn(\$u) => print(\$u->id . ': ' . \$u->name . ' (' . \$u->email . ') - ' . \$u->role . PHP_EOL));"
```

### Find User by Email

```bash
php artisan tinker --execute="\$user = App\Models\User::where('email', 'admin@example.com')->first(); dump(['id' => \$user->id, 'name' => \$user->name, 'email' => \$user->email, 'role' => \$user->role]);"
```

### Check if User Exists

```bash
php artisan tinker --execute="echo App\Models\User::where('email', 'admin@example.com')->exists() ? 'User exists' : 'User not found';"
```

**Notes:**
- Passwords are automatically hashed (uses Laravel's `'hashed'` cast)
- Email must be unique
- `email_verified_at` defaults to `null` (user must verify email)
- Set `email_verified_at` to `now()` to skip verification

---

## Database Seeders

### Seed Availability for Specific User

Create availability/booking slots for a specific user (requires user to be a `company_administrator`).

**Usage:**
```bash
php artisan db:seed --class=AvailabilityForUserSeeder
```

**With custom email:**
```bash
SEED_USER_EMAIL=admin@example.com php artisan db:seed --class=AvailabilityForUserSeeder
```

**Windows (PowerShell):**
```powershell
$env:SEED_USER_EMAIL="admin@example.com"; php artisan db:seed --class=AvailabilityForUserSeeder
```

**What it does:**
- Creates availability rules for the specified user
- Sets up working hours (9 AM - 5 PM, Monday-Friday)
- Configures booking duration and buffer times
- Requires user to have `company_administrator` role

**Default email:**
- Uses `alexramsey92@gmail.com` if `SEED_USER_EMAIL` not set

---

## Common Workflows

### New Developer Setup

1. Create admin user:
```bash
php artisan tinker --execute="App\Models\User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'password', 'role' => 'company_administrator', 'email_verified_at' => now()]);"
```

2. Seed availability for admin:
```bash
SEED_USER_EMAIL=admin@test.com php artisan db:seed --class=AvailabilityForUserSeeder
```

3. Test email sending:
```bash
php artisan email:test --count=3 --to=admin@test.com
```

### Email Debugging Workflow

1. Send test emails:
```bash
php artisan email:test --count=5 --to=test@example.com
```

2. Check admin dashboard for email log IDs:
   Visit: `/admin/email-logs`

3. Audit specific email:
```bash
php artisan email:audit 123 --preview
```

4. Resend if needed:
```bash
php artisan email:audit 123 --send-to=debug@example.com
```

### Google Calendar Setup Workflow

1. Run diagnostics:
```bash
php artisan calendar:diagnose
```

2. If issues found, check docs:
   - `app/docs/GOOGLE_OAUTH_SETUP.md`
   - Admin dashboard: `/admin/bookings/dashboard` (troubleshooting section)

3. Migrate legacy data if needed:
```bash
php artisan calendar:diagnose --migrate
```

### Pre-Deployment Checklist

1. Test all email templates:
```bash
php artisan email:templates qa@example.com
```

2. Run calendar diagnostics:
```bash
php artisan calendar:diagnose
```

3. Clear all caches:
```bash
php artisan view:clear && php artisan config:clear && php artisan route:clear && php artisan cache:clear
```

4. After deployment, send notification:
```bash
php artisan deploy:notify --commits=20
```

---

## Tips & Best Practices

### Password Security
- Always use strong passwords in production
- Use password managers to generate secure passwords
- Never commit passwords to version control
- Rotate passwords regularly

### Email Testing
- Use dedicated test email addresses
- Monitor deliverability dashboards
- Check spam folders
- Test with multiple email providers (Gmail, Outlook, etc.)

### Tinker Safety
- Be careful with destructive operations (delete, truncate)
- Test queries with `->first()` before using `->delete()` or `->update()`
- Use transactions for complex operations
- Always have database backups

### Windows Users
- Use Git Bash or WSL for Unix-like commands
- PowerShell requires different env variable syntax:
  ```powershell
  $env:VARIABLE="value"; php artisan command
  ```
- Use `php.bat` instead of `php` if needed

### Production
- Never use `--force` flags without confirmation
- Always test commands in staging first
- Monitor logs after running commands
- Keep backups before major operations

---

## Troubleshooting

### Command Not Found
```bash
php artisan list | grep custom-command-name
```

If not listed, check:
- `app/Console/Commands/` directory
- `app/Console/Kernel.php` for registration
- Run `composer dump-autoload`

### Permission Denied (Linux/Mac)
```bash
chmod +x artisan
```

### Tinker Syntax Errors
- Always escape `$` in bash: `\$user`
- Use single quotes for outer string, double quotes inside
- Test complex commands in Tinker REPL first

### Email Not Sending
1. Check `.env` mail configuration
2. Test with `email:test` command
3. Check `storage/logs/laravel.log`
4. Verify mail server credentials

### Git Not Available (deploy:notify)
- Command will still work but skip commit history
- Install git or run from git-enabled environment

---

## Related Documentation

- [Common Artisan Commands](artisan-common-commands.md) - Standard Laravel commands
- [Google OAuth Setup](GOOGLE_OAUTH_SETUP.md) - Calendar integration guide
- [Email Template Guide](../views/emails/) - Email template development

---

## Need Help?

- Check Laravel logs: `storage/logs/laravel.log`
- Run diagnostics: `php artisan calendar:diagnose`
- Test emails: `php artisan email:test`
- Open Tinker: `php artisan tinker`

For command-specific help:
```bash
php artisan help command-name
```

Example:
```bash
php artisan help email:test
```
