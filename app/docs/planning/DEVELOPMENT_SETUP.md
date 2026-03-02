# Development Setup Guide
**smbgen - Professional Development Workflow**

**Last Updated:** December 28, 2025

---

## Current Problem

**⚠️ Testing in Production**
- Changes deployed directly to Laravel Cloud production
- No staging environment for validation
- Risk of breaking live customer data
- Difficult to collaborate with multiple developers

**Goal:** Establish proper development workflow with local → staging → production pipeline

---

## Development Environment Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    LOCAL DEVELOPMENT                         │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Windows + Laravel Herd + SQLite                   │    │
│  │  - Fast local development                          │    │
│  │  - *.smbgen.test subdomains                  │    │
│  │  - Hot module reloading (Vite)                     │    │
│  │  - Email testing (MailHog/Mailpit)                 │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                            │
                     git push to main
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    STAGING ENVIRONMENT                       │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Laravel Cloud (staging branch)                    │    │
│  │  - MySQL database                                  │    │
│  │  - staging.smbgen.com                        │    │
│  │  - Real Stripe test mode                           │    │
│  │  - Real Google OAuth (test credentials)           │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                            │
                    Manual promotion after QA
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  PRODUCTION ENVIRONMENT                      │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Laravel Cloud (production branch)                 │    │
│  │  - MySQL database                                  │    │
│  │  - app.smbgen.com                            │    │
│  │  - Real Stripe live mode                           │    │
│  │  - Real Google OAuth (production credentials)      │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

---

## Local Development Setup

### Prerequisites

**Required Software:**
- ✅ Laravel Herd (installed)
- ✅ Git (installed)
- ✅ Node.js 24.5+ (installed)
- ✅ Composer 2.x (comes with Herd)
- ✅ PHP 8.4+ (managed by Herd)

**Optional but Recommended:**
- GitHub CLI (`gh`) for easier PR management
- Mailpit (for email testing, built into Herd)
- Redis (for queue/cache, optional locally)

### Initial Setup (New Developer)

#### 1. Clone Repository
```bash
cd ~/Herd  # Or your Herd projects directory
git clone https://github.com/your-org/smbgen.git
cd smbgen
```

#### 2. Install Dependencies
```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

#### 3. Environment Configuration
```bash
# Copy example environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure local environment
```

**Edit `.env` for local development:**
```ini
APP_NAME="smbgen Local"
APP_ENV=local
APP_DEBUG=true
APP_URL=https://smbgen.test
APP_TIMEZONE=UTC

# Database - Use SQLite for local speed
DB_CONNECTION=sqlite
# DB_DATABASE will default to database/database.sqlite

# Mail - Use Herd's Mailpit
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

# Stripe - Use test keys
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_test_xxxxx

# Google OAuth - Use test credentials
GOOGLE_CLIENT_ID=xxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxxx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Tenancy - Disabled by default for local
TENANCY_ENABLED=false

# Features - All enabled locally
FEATURE_BOOKING=true
FEATURE_BILLING=true
FEATURE_MESSAGES=true
FEATURE_CMS=true
FEATURE_FILE_MANAGEMENT=true
FEATURE_INSPECTION_REPORTS=true
FEATURE_PHONE_SYSTEM=true

# Queues - Sync for local (no worker needed)
QUEUE_CONNECTION=sync

# Cache - File-based locally
CACHE_STORE=file
SESSION_DRIVER=file
```

#### 4. Database Setup
```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed with sample data
php artisan db:seed

# Create a test admin user
php artisan tinker
>>> User::create([
...   'name' => 'Admin User',
...   'email' => 'admin@test.com',
...   'password' => bcrypt('password'),
...   'role' => 'company_administrator',
...   'email_verified_at' => now(),
... ]);
>>> exit
```

#### 5. Link Storage
```bash
php artisan storage:link
```

#### 6. Start Development Server
```bash
# Terminal 1: Start Vite dev server (hot reload)
npm run dev

# Terminal 2: Start queue worker (if needed)
php artisan queue:work

# Herd automatically serves at: https://smbgen.test
```

#### 7. Access Application
- **Main App:** https://smbgen.test
- **Admin Panel:** https://smbgen.test/admin
- **Login:** admin@test.com / password
- **Mailpit (emails):** http://localhost:8025

---

## Multi-Tenancy Local Testing

### Setup Subdomain Testing

**1. Configure Herd for Subdomain Routing**

Herd automatically handles `*.test` domains. No additional DNS configuration needed.

**2. Create Test Tenants**
```bash
php artisan tinker

# Create first tenant
$tenant1 = \Stancl\Tenancy\Database\Models\Tenant::create([
    'id' => 'acme',
    'data' => ['name' => 'Acme Corp'],
]);
$tenant1->domains()->create(['domain' => 'acme.smbgen.test']);

# Create second tenant
$tenant2 = \Stancl\Tenancy\Database\Models\Tenant::create([
    'id' => 'globex',
    'data' => ['name' => 'Globex Inc'],
]);
$tenant2->domains()->create(['domain' => 'globex.smbgen.test']);

exit
```

**3. Enable Tenancy Mode**
```ini
# .env
TENANCY_ENABLED=true
```

**4. Test Tenant Isolation**
```bash
# Visit different subdomains
open https://acme.smbgen.test
open https://globex.smbgen.test

# Each should have separate data
```

---

## Common Development Tasks

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run with coverage (requires xdebug)
php artisan test --coverage

# Run tests in parallel (faster)
php artisan test --parallel
```

### Database Management
```bash
# Fresh database with seeds
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_example_table

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### Code Quality
```bash
# Format code with Laravel Pint
vendor/bin/pint

# Check code style without fixing
vendor/bin/pint --test

# Run static analysis (if installed)
vendor/bin/phpstan analyse
```

### Debugging
```bash
# Enable query logging
DB::enableQueryLog();
// ... run code ...
dd(DB::getQueryLog());

# Tail logs
tail -f storage/logs/laravel.log

# Dump server (better than dd)
php artisan dump-server
# Then use dump() instead of dd()

# Check routes
php artisan route:list

# Check events
php artisan event:list
```

### Asset Compilation
```bash
# Development (watch for changes)
npm run dev

# Production build
npm run build

# Check for issues
npm run lint
```

---

## Staging Environment Setup

### Laravel Cloud Staging Configuration

#### 1. Create Staging Environment in Laravel Cloud

**Via Laravel Cloud Dashboard:**
1. Go to your project in Laravel Cloud
2. Create new environment: "Staging"
3. Connect to `staging` branch in GitHub
4. Configure environment variables

#### 2. Staging Environment Variables

```ini
APP_NAME="smbgen Staging"
APP_ENV=staging
APP_DEBUG=true  # Can debug staging issues
APP_URL=https://staging.smbgen.com

# Database - Provided by Laravel Cloud
DB_CONNECTION=mysql
DB_HOST=<provided-by-laravel-cloud>
DB_PORT=3306
DB_DATABASE=<provided-by-laravel-cloud>
DB_USERNAME=<provided-by-laravel-cloud>
DB_PASSWORD=<provided-by-laravel-cloud>

# Mail - Use real SMTP but with test recipients
MAIL_MAILER=smtp
MAIL_HOST=<your-smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<your-smtp-username>
MAIL_PASSWORD=<your-smtp-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="staging@smbgen.com"
MAIL_FROM_NAME="smbgen Staging"

# Stripe - TEST MODE
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_test_xxxxx

# Google OAuth - Staging credentials
GOOGLE_CLIENT_ID=xxxxx-staging.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxxx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Tenancy - Can test multi-tenant on staging
TENANCY_ENABLED=true

# Features - Test feature flags
FEATURE_BOOKING=true
FEATURE_BILLING=true
FEATURE_MESSAGES=true
FEATURE_CMS=true
FEATURE_FILE_MANAGEMENT=true
FEATURE_INSPECTION_REPORTS=false
FEATURE_PHONE_SYSTEM=false

# Queues - Use Redis
QUEUE_CONNECTION=redis

# Cache - Use Redis
CACHE_STORE=redis
SESSION_DRIVER=redis

# Redis - Provided by Laravel Cloud
REDIS_HOST=<provided-by-laravel-cloud>
REDIS_PASSWORD=<provided-by-laravel-cloud>
REDIS_PORT=6379
```

#### 3. Deploy to Staging

```bash
# Create and switch to staging branch
git checkout -b staging

# Push to staging
git push origin staging

# Laravel Cloud auto-deploys on push to staging branch
```

#### 4. Staging Database Setup

**Option A: Fresh Database (Recommended)**
```bash
# SSH into staging (via Laravel Cloud CLI)
php artisan migrate:fresh --seed --force
```

**Option B: Copy from Production (Careful!)**
```bash
# Dump production DB
php artisan db:dump

# Import to staging
php artisan db:restore staging
```

---

## Git Workflow

### Branch Strategy

```
main (protected)
  ├── staging (auto-deploy to staging)
  ├── feature/multi-tenancy
  ├── feature/subscription-billing
  ├── bugfix/booking-validation
  └── hotfix/critical-payment-bug
```

### Feature Development Workflow

```bash
# 1. Start from main
git checkout main
git pull origin main

# 2. Create feature branch
git checkout -b feature/tenant-management

# 3. Make changes, commit frequently
git add .
git commit -m "Add tenant model and migration"

# 4. Push to remote
git push origin feature/tenant-management

# 5. Create Pull Request on GitHub
gh pr create --base main --head feature/tenant-management

# 6. After code review and approval
git checkout main
git pull origin main

# 7. Merge to staging for testing
git checkout staging
git merge feature/tenant-management
git push origin staging

# 8. After staging validation, merge to main
git checkout main
git merge feature/tenant-management
git push origin main
```

### Commit Message Convention

```bash
# Format: <type>(<scope>): <subject>

# Examples:
git commit -m "feat(tenancy): add tenant model and migration"
git commit -m "fix(booking): validate date ranges correctly"
git commit -m "refactor(auth): extract OAuth logic to service"
git commit -m "test(billing): add subscription cancellation tests"
git commit -m "docs(readme): update setup instructions"
git commit -m "chore(deps): upgrade Laravel to 12.x"

# Types:
# feat: New feature
# fix: Bug fix
# refactor: Code change without adding feature or fixing bug
# test: Adding tests
# docs: Documentation changes
# chore: Maintenance tasks, dependency updates
# perf: Performance improvements
# style: Code style changes (formatting, not CSS)
```

---

## Testing Strategy

### Test Types

```php
// 1. Unit Tests - Fast, isolated
tests/Unit/
  ├── Models/TenantTest.php
  ├── Services/StripeServiceTest.php
  └── Helpers/DateHelperTest.php

// 2. Feature Tests - Full HTTP stack
tests/Feature/
  ├── Auth/LoginTest.php
  ├── Admin/TenantManagementTest.php
  ├── Booking/CreateBookingTest.php
  └── Billing/SubscriptionTest.php

// 3. Browser Tests (Pest + Laravel Dusk)
tests/Browser/
  ├── TenantOnboardingTest.php
  └── AdminDashboardTest.php
```

### Testing Multi-Tenancy

```php
// tests/Feature/TenancyTest.php

use App\Models\Tenant;
use App\Models\User;
use function Pest\Laravel\{actingAs, get};

it('isolates tenant data', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    
    tenancy()->initialize($tenant1);
    $user1 = User::factory()->create(['name' => 'User 1']);
    
    tenancy()->initialize($tenant2);
    $user2 = User::factory()->create(['name' => 'User 2']);
    
    tenancy()->initialize($tenant1);
    expect(User::count())->toBe(1);
    expect(User::first()->name)->toBe('User 1');
    
    tenancy()->initialize($tenant2);
    expect(User::count())->toBe(1);
    expect(User::first()->name)->toBe('User 2');
});

it('prevents cross-tenant data access', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    
    tenancy()->initialize($tenant1);
    $user1 = User::factory()->create();
    
    tenancy()->initialize($tenant2);
    $user2 = User::factory()->create();
    
    actingAs($user2)
        ->get(route('users.show', $user1->id))
        ->assertForbidden();
});
```

### Pre-Commit Checklist

Before pushing code:
```bash
# 1. Run tests
php artisan test

# 2. Format code
vendor/bin/pint

# 3. Check for obvious issues
php artisan route:list  # Ensure no broken routes
php artisan config:clear  # Clear config cache
php artisan view:clear  # Clear view cache

# 4. Ensure .env.example is updated
# If you added new env variables, update .env.example

# 5. Update CHANGELOG.md (if applicable)
```

---

## Debugging Tools

### Laravel Telescope (Recommended for Local/Staging)

```bash
# Install Telescope
composer require laravel/telescope --dev

# Install assets
php artisan telescope:install

# Migrate
php artisan migrate

# Access at /telescope
```

**Only enable in non-production:**
```php
// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class, // Only if not production
];
```

### Ray (Premium Alternative)

```bash
# Install Ray
composer require spatie/laravel-ray --dev

# Use anywhere in code
ray($user, $request);
```

### Laravel Debugbar

```bash
# Install
composer require barryvdh/laravel-debugbar --dev

# Automatically shows in browser (local only)
```

---

## Environment Variable Management

### Critical: Keep Secrets Secure

**Never commit:**
- Real API keys (Stripe, Google)
- Database passwords
- Session secrets
- Webhook secrets

**Use .env.example as template:**
```ini
# .env.example (SAFE to commit)
STRIPE_KEY=pk_test_replace_with_your_key
STRIPE_SECRET=sk_test_replace_with_your_key

# .env (NEVER commit)
STRIPE_KEY=pk_test_51abc123...
STRIPE_SECRET=sk_test_51xyz789...
```

### Environment-Specific Variables

| Variable | Local | Staging | Production |
|----------|-------|---------|------------|
| `APP_DEBUG` | true | true | **false** |
| `APP_ENV` | local | staging | production |
| `DB_CONNECTION` | sqlite | mysql | mysql |
| `QUEUE_CONNECTION` | sync | redis | redis |
| `TENANCY_ENABLED` | false | true | true |
| Stripe | test keys | test keys | **live keys** |

---

## Common Issues & Solutions

### Issue: "Vite manifest not found"
```bash
# Solution: Build assets
npm run build

# Or run dev server
npm run dev
```

### Issue: "Class not found" after pulling changes
```bash
# Solution: Regenerate autoload
composer dump-autoload
```

### Issue: "Table not found"
```bash
# Solution: Run migrations
php artisan migrate

# Or fresh if in local
php artisan migrate:fresh --seed
```

### Issue: "419 Page Expired" on forms
```bash
# Solution: Clear config cache
php artisan config:clear
php artisan cache:clear
```

### Issue: Subdomain not working locally
```bash
# Check Herd is running
herd status

# Verify domain
ping acme.smbgen.test

# Restart Herd
herd restart
```

### Issue: Queue jobs not processing
```bash
# Ensure queue worker is running
php artisan queue:work

# Or use horizon if installed
php artisan horizon

# Check failed jobs
php artisan queue:failed
```

---

## Performance Optimization

### Local Development Speed Tips

```bash
# Use SQLite instead of MySQL
DB_CONNECTION=sqlite

# Disable query logging in local
DB_QUERY_LOG=false

# Use file cache instead of Redis
CACHE_STORE=file

# Disable unnecessary services
GOOGLE_CALENDAR_ENABLED=false
```

### Optimize Autoloader
```bash
composer install --optimize-autoloader --no-dev
```

### Cache Configuration (Production/Staging Only)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Never cache in local development** - makes changes not reflect immediately

---

## Deployment Checklist

### Before Deploying to Staging
- [ ] All tests pass locally (`php artisan test`)
- [ ] Code formatted (`vendor/bin/pint`)
- [ ] No debug statements left (dd, dump, ray)
- [ ] Migrations are reversible (have `down()` method)
- [ ] Environment variables documented in `.env.example`
- [ ] CHANGELOG.md updated (if using)
- [ ] Feature flag set correctly for staging

### Before Deploying to Production
- [ ] All staging tests pass
- [ ] Staging manually tested by QA
- [ ] Database backup created
- [ ] Rollback plan documented
- [ ] Monitoring/alerts configured
- [ ] Stakeholders notified of deployment
- [ ] Off-hours deployment scheduled (if risky)

---

## Quick Reference

### Essential Commands

```bash
# Start dev server
npm run dev

# Run tests
php artisan test

# Format code
vendor/bin/pint

# Migrate database
php artisan migrate

# Clear all cache
php artisan optimize:clear

# Generate IDE helper (if installed)
php artisan ide-helper:generate
```

### Important URLs (Local)

- **App:** https://smbgen.test
- **Admin:** https://smbgen.test/admin
- **Mailpit:** http://localhost:8025
- **Telescope:** https://smbgen.test/telescope

### Important URLs (Staging)

- **App:** https://staging.smbgen.com
- **Admin:** https://staging.smbgen.com/admin

---

## Getting Help

### Internal Resources
- This documentation
- CONTRIBUTING.md for code standards
- MULTI_TENANCY_IMPLEMENTATION.md for architecture
- Ask in team Slack/Discord

### External Resources
- [Laravel Docs](https://laravel.com/docs)
- [Stancl Tenancy Docs](https://tenancyforlaravel.com)
- [Pest Docs](https://pestphp.com)
- [Herd Docs](https://herd.laravel.com)

---

**Last Updated:** December 28, 2025  
**Maintained By:** Development Team
