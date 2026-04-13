# SMBGen Architecture Documentation

**Version:** 1.0  
**Last Updated:** January 2025  
**Laravel Version:** 12.x  
**PHP Version:** 8.4

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [Feature Flag System](#feature-flag-system)
3. [Authentication & Authorization](#authentication--authorization)
4. [Admin Panel Structure](#admin-panel-structure)
5. [Business Settings System](#business-settings-system)
6. [Google OAuth Integration](#google-oauth-integration)
7. [Google Calendar Integration](#google-calendar-integration)
8. [Email System](#email-system)
9. [Booking System](#booking-system)
10. [Database Architecture](#database-architecture)
11. [VPS Deployment](#vps-deployment)
12. [Troubleshooting Guide](#troubleshooting-guide)

---

## System Overview

SMBGen is a Laravel-based client management platform designed for virtual consulting sessions. It combines booking management, email communication, and Google Calendar integration with a focus on security and ease of use.

### Technology Stack

- **Backend:** Laravel 12.x with PHP 8.4
- **Frontend:** Blade templates with Livewire, Alpine.js, Tailwind CSS
- **Database:** SQLite (local), MySQL/PostgreSQL ready
- **Email:** NixiHost SMTP (rtsenviro.com:465 SSL)
- **API Integrations:** Google OAuth2, Google Calendar API, OpenAI
- **Server:** Nginx + PHP-FPM 8.4 on Ubuntu VPS
- **Build Tools:** Vite for frontend assets

### Core Features

✅ **Admin Dashboard** - Central hub for all business operations  
✅ **Business Settings** - Customizable branding and configuration  
✅ **Email Composer** - Template-based email system with history  
✅ **Booking Management** - Calendar-integrated appointment scheduling  
✅ **Google Calendar Sync** - Automatic event creation with Meet links  
✅ **User Management** - Role-based access control (admin/user)  
✅ **Theme Customization** - Color scheme controls for branding

---

## Feature Flag System

Feature flags allow gradual rollout and easy enable/disable of major features without code changes.

### Location

Defined in `.env` file:

```env
# Feature Flags
FEATURE_APPOINTMENTS=true
FEATURE_EMAIL_COMPOSER=true
```

### Usage in Code

**Checking a feature flag:**

```php
// In Blade templates
@if(env('FEATURE_EMAIL_COMPOSER'))
    <div>Email Composer is enabled</div>
@endif

// In Controllers
if (config('app.feature_email_composer') === true) {
    // Feature-specific logic
}
```

**Best Practice: Route Safety**

Always check if routes exist before linking to them:

```blade
@if(env('FEATURE_EMAIL_COMPOSER') && Route::has('admin.email.index'))
    <a href="{{ route('admin.email.index') }}">Email Composer</a>
@endif
```

This prevents errors when code is deployed but routes aren't registered yet.

### Adding New Feature Flags

1. **Add to `.env`:**
   ```env
   FEATURE_NEW_FEATURE=true
   ```

2. **Add to `.env.example`:**
   ```env
   FEATURE_NEW_FEATURE=false
   ```

3. **Use in code:**
   ```php
   @if(env('FEATURE_NEW_FEATURE'))
       <!-- Feature UI -->
   @endif
   ```

4. **Document in this file** under "Core Features"

---

## Authentication & Authorization

### User Roles

- **Admin** - Full access to all features
- **User** - Limited access (client view)

### Admin Protection

Admin routes are protected by multiple layers:

```php
// routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});
```

**Middleware Chain:**
1. `auth` - Requires logged-in user
2. `admin` - Checks `is_admin` field on User model

### Rate Limiting

Configured in `routes/web.php`:

```php
// Admin routes: 5 requests per minute
->middleware(['throttle:admin'])

// Login: 1 request per minute
->middleware(['throttle:login'])
```

### IP Whitelisting (VPS)

Nginx config restricts admin access to approved IPs:

```nginx
location /admin {
    allow 68.33.53.17;   # Your IP
    deny all;
    try_files $uri $uri/ /index.php?$query_string;
}
```

See `deployment/whitelist-my-ip.sh` to add your IP automatically.

---

## Admin Panel Structure

### Dashboard Layout

**Location:** `resources/views/admin/dashboard.blade.php`

The dashboard is composed of feature cards organized by category:

```
┌─────────────────────────────────────────┐
│  Overview Cards (Stats)                 │
├─────────────────────────────────────────┤
│  System Status                          │
├─────────────────────────────────────────┤
│  Debug Tools (if APP_DEBUG=true)        │
├─────────────────────────────────────────┤
│  Feature Cards (Email, Bookings, etc.)  │
└─────────────────────────────────────────┘
```

### Card Component Pattern

Each feature uses a consistent card structure:

```blade
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0 mr-4">
                <!-- Icon -->
            </div>
            <div>
                <h3 class="text-lg font-semibold">Feature Name</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Description</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('...') }}" class="btn-primary">Action</a>
        </div>
    </div>
</div>
```

### Navigation

**Main Navigation:** `resources/views/layouts/navigation.blade.php`

Admin links appear only when authenticated as admin:

```blade
@if(auth()->check() && auth()->user()->is_admin)
    <x-nav-link :href="route('admin.dashboard')">
        {{ __('Admin Dashboard') }}
    </x-nav-link>
@endif
```

---

## Business Settings System

Dynamic configuration stored in database, separate from `.env` file.

### Architecture

**Migration:** `database/migrations/2025_09_10_000001_create_business_settings_table.php`

```sql
CREATE TABLE business_settings (
    id INTEGER PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NULL,              -- Changed from JSON to TEXT
    type VARCHAR(255) DEFAULT 'string',  -- boolean, integer, string, json
    description TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Model:** `app/Models/BusinessSetting.php`

Key features:
- Static `get($key, $default)` method with type casting
- Static `set($key, $value, $type)` method with type conversion
- Type casting: boolean, integer, string, json

```php
// Get a setting (with type casting)
$companyName = BusinessSetting::get('company_name', 'SMBGen');
$isEnabled = BusinessSetting::get('feature_x', false);  // Returns bool

// Set a setting
BusinessSetting::set('company_name', 'New Company Name', 'string');
BusinessSetting::set('is_enabled', true, 'boolean');  // Stores as '1' or '0'
```

### Available Settings

| Key | Type | Description | Syncs to .env |
|-----|------|-------------|---------------|
| `app_name` | string | Application name | Yes → `APP_NAME` |
| `company_name` | string | Company/business name | No |
| `theme_primary_color` | string | Primary color (hex) | No |
| `theme_secondary_color` | string | Secondary color (hex) | No |
| `theme_background_color` | string | Background color (hex) | No |
| `theme_text_color` | string | Text color (hex) | No |
| `enable_bookings` | boolean | Enable booking system | No |
| `enable_notifications` | boolean | Enable email notifications | No |
| `booking_buffer_minutes` | integer | Minutes between bookings | No |
| `default_session_duration` | integer | Default session length (min) | No |

### Theme Colors

Colors are customizable via admin panel with live preview:

**Default Colors:**
- Primary: `#3b82f6` (Blue)
- Secondary: `#10b981` (Green)
- Background: `#1f2937` (Dark Gray)
- Text: `#f3f4f6` (Light Gray)

**Usage in Blade:**

```blade
<style>
:root {
    --color-primary: {{ BusinessSetting::get('theme_primary_color', '#3b82f6') }};
    --color-secondary: {{ BusinessSetting::get('theme_secondary_color', '#10b981') }};
}
</style>
```

### .env Synchronization

When `app_name` changes in Business Settings, it automatically updates `.env`:

**Controller Method:** `BusinessSettingsController@updateEnvFile()`

This ensures consistency between database settings and environment configuration.

---

## Google OAuth Integration

OAuth2 authentication for Google services (Calendar, Drive, etc.).

### Setup Process

1. **Create Google Cloud Project**
   - Go to [console.cloud.google.com](https://console.cloud.google.com)
   - Create new project: "SMBGen"

2. **Enable APIs**
   - Google Calendar API
   - Google Drive API (if needed)

3. **Create OAuth Credentials**
   - Credentials → Create Credentials → OAuth 2.0 Client ID
   - Application type: Web application
   - Authorized redirect URIs:
     ```
     https://houston1.oldlinecyber.com/auth/google/callback
     http://smbgen.test/auth/google/callback
     ```

4. **Configure .env**
   ```env
   GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-client-secret-here
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```

### OAuth Flow

```
User → Click "Connect Google"
     ↓
Google Login Page (consent screen)
     ↓
Google redirects to: /auth/google/callback?code=...
     ↓
Controller exchanges code for tokens
     ↓
Store tokens in database (users.google_token, google_refresh_token)
     ↓
Redirect to dashboard with success message
```

### Token Management

**Stored per user in `users` table:**

```php
$user->google_token         // Access token (expires in 1 hour)
$user->google_refresh_token // Refresh token (never expires unless revoked)
```

**Token Refresh:**

When access token expires, use refresh token to get new access token:

```php
// app/Services/GoogleCalendarService.php
private function refreshAccessToken($user) {
    $client = new \Google_Client();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->refreshToken($user->google_refresh_token);
    
    $newToken = $client->getAccessToken();
    $user->update(['google_token' => json_encode($newToken)]);
}
```

### Scopes Required

```php
$client->setScopes([
    \Google_Service_Calendar::CALENDAR,
    \Google_Service_Calendar::CALENDAR_EVENTS,
]);
```

---

## Google Calendar Integration

Automatic event creation with Google Meet links.

### Service Class

**Location:** `app/Services/GoogleCalendarService.php`

**Key Methods:**

```php
// Create a calendar event with Meet link
createEvent($user, $title, $description, $startTime, $endTime, $attendees)

// Check if user has active Google connection
isConnected($user)

// Refresh expired access token
refreshAccessToken($user)

// List user's calendars
listCalendars($user)

// Get availability for date range
getAvailability($user, $startDate, $endDate)
```

### Event Creation Flow

```php
$service = new GoogleCalendarService();

$event = $service->createEvent(
    user: $user,
    title: "Consultation with Client",
    description: "45-minute virtual consultation session",
    startTime: "2025-01-15 14:00:00",
    endTime: "2025-01-15 14:45:00",
    attendees: ['client@example.com']
);

// Returns Google Calendar Event with:
// - Event ID
// - Meet link (hangoutLink)
// - iCalUID
```

### Meet Link Generation

Google automatically creates Meet links when:
- Event is created via API
- Request includes `conferenceDataVersion: 1`
- User has Google Meet enabled

**Controller usage:**

```php
$booking = Booking::create([...]);

$event = app(GoogleCalendarService::class)->createEvent(
    $user,
    "Booking #{$booking->id}",
    "Client consultation",
    $booking->start_time,
    $booking->end_time,
    [$booking->client_email]
);

$booking->update([
    'google_event_id' => $event->getId(),
    'meet_link' => $event->getHangoutLink(),
]);
```

### Availability Checking

Before showing available time slots, check user's calendar:

```php
$busyTimes = $service->getAvailability(
    $user,
    '2025-01-15 00:00:00',
    '2025-01-15 23:59:59'
);

// Returns array of busy periods:
[
    ['start' => '2025-01-15 10:00:00', 'end' => '2025-01-15 10:45:00'],
    ['start' => '2025-01-15 14:00:00', 'end' => '2025-01-15 14:45:00'],
]
```

### Error Handling

```php
try {
    $event = $service->createEvent(...);
} catch (\Google_Service_Exception $e) {
    // API error (rate limit, invalid token, etc.)
    Log::error('Google Calendar API Error: ' . $e->getMessage());
    
} catch (\Exception $e) {
    // General error
    Log::error('Event creation failed: ' . $e->getMessage());
}
```

---

## Email System

SMTP-based email system with template support and history tracking.

### SMTP Configuration

**Provider:** NixiHost  
**Host:** rtsenviro.com  
**Port:** 465 (SSL)  
**From:** smbgen@rtsenviro.com

**`.env` configuration:**

```env
MAIL_MAILER=smtp
MAIL_HOST=rtsenviro.com
MAIL_PORT=465
MAIL_USERNAME=smbgen@rtsenviro.com
MAIL_PASSWORD=your-password-here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=smbgen@rtsenviro.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Email Composer Feature

**Routes:**

```php
Route::get('/admin/email', [EmailController::class, 'index'])->name('admin.email.index');
Route::post('/admin/email/send', [EmailController::class, 'send'])->name('admin.email.send');
Route::get('/admin/email/history', [EmailController::class, 'history'])->name('admin.email.history');
```

**Controller:** `app/Http/Controllers/Admin/EmailController.php`

**Key Features:**
- Multi-recipient support (comma-separated)
- Template selection
- CC/BCC support
- Email history with status tracking
- Attachment support (future)

### Email Templates

Stored in `resources/views/emails/` directory:

```
emails/
├── booking-confirmation.blade.php
├── booking-reminder.blade.php
├── invoice.blade.php
└── welcome.blade.php
```

**Template Variables:**

```blade
{{-- Available in all templates --}}
{{ $companyName }}
{{ $appName }}
{{ $primaryColor }}

{{-- Booking templates --}}
{{ $bookingDate }}
{{ $bookingTime }}
{{ $meetLink }}
{{ $clientName }}

{{-- Invoice templates --}}
{{ $invoiceNumber }}
{{ $amount }}
{{ $paymentLink }}
```

### Sending Emails

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;

Mail::to($booking->client_email)
    ->send(new BookingConfirmation($booking));
```

### Email History

**Table:** `email_history`

```php
Schema::create('email_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('to');
    $table->string('cc')->nullable();
    $table->string('subject');
    $table->text('body');
    $table->string('status'); // sent, failed, pending
    $table->text('error_message')->nullable();
    $table->timestamps();
});
```

### Spam Prevention (Planned)

Future safeguards:
- Rate limiting: 10 emails per minute per user
- Daily send limit: 100 emails per user
- Admin review queue for bulk sends
- SPF/DKIM verification checks
- Bounce tracking

---

## Booking System

Calendar-integrated appointment scheduling with availability management.

### Database Schema

**Table:** `bookings`

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained(); // Admin who owns the booking
    $table->string('client_name')->nullable();
    $table->string('client_email');
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->integer('duration_minutes')->default(45);
    $table->text('notes')->nullable();
    $table->string('status')->default('pending'); // pending, confirmed, cancelled
    $table->string('google_event_id')->nullable();
    $table->string('meet_link')->nullable();
    $table->timestamps();
});
```

### Booking Flow

```
1. Client selects date/time from available slots
        ↓
2. System checks admin's Google Calendar availability
        ↓
3. Client provides name, email, optional notes
        ↓
4. Booking created in database
        ↓
5. Google Calendar event created with Meet link
        ↓
6. Confirmation email sent to client
        ↓
7. Admin receives notification
```

### Availability Logic

**Controller:** `app/Http/Controllers/BookingController.php`

```php
public function availableSlots(Request $request) {
    $date = $request->input('date');
    $admin = User::where('is_admin', true)->first();
    
    // Get busy times from Google Calendar
    $busyTimes = app(GoogleCalendarService::class)
        ->getAvailability($admin, $date, $date);
    
    // Generate 45-min slots from 9 AM to 5 PM
    $slots = [];
    for ($hour = 9; $hour < 17; $hour++) {
        $slotStart = Carbon::parse("$date $hour:00");
        $slotEnd = $slotStart->copy()->addMinutes(45);
        
        // Check if slot overlaps with busy times
        $isAvailable = !$this->isSlotBusy($slotStart, $slotEnd, $busyTimes);
        
        if ($isAvailable) {
            $slots[] = [
                'start' => $slotStart->format('Y-m-d H:i:s'),
                'end' => $slotEnd->format('Y-m-d H:i:s'),
            ];
        }
    }
    
    return response()->json($slots);
}
```

### Grace Period (Planned)

**Feature:** 15-minute buffer between bookings

**Implementation:**

```php
// When checking availability, add buffer
$slotEndWithBuffer = $slotEnd->copy()->addMinutes(15);

// Check if next booking starts within buffer period
$nextBooking = Booking::where('user_id', $admin->id)
    ->where('start_time', '<=', $slotEndWithBuffer)
    ->where('start_time', '>=', $slotEnd)
    ->first();

if ($nextBooking) {
    $isAvailable = false; // Not available due to grace period
}
```

---

## Database Architecture

### Primary Tables

#### `users`
```sql
id, name, email, password, email_verified_at,
is_admin (boolean),
google_token (json),
google_refresh_token (text),
remember_token, created_at, updated_at
```

#### `business_settings`
```sql
id, key, value (text), type, description, created_at, updated_at
```

#### `bookings`
```sql
id, user_id, client_name, client_email,
start_time, end_time, duration_minutes,
notes, status, google_event_id, meet_link,
created_at, updated_at
```

#### `email_history`
```sql
id, user_id, to, cc, subject, body,
status, error_message, created_at, updated_at
```

### Database Driver

**Local Development:** SQLite (`database/database.sqlite`)  
**Production:** MySQL/PostgreSQL supported

**Switch in `.env`:**

```env
# SQLite (default)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smbgen
DB_USERNAME=root
DB_PASSWORD=
```

### Migrations

Run migrations:

```bash
php artisan migrate
```

Refresh (reset + migrate):

```bash
php artisan migrate:fresh
```

Seed with test data:

```bash
php artisan db:seed
```

---

## VPS Deployment

### Server Configuration

**VPS:** houston1.oldlinecyber.com  
**User:** root / alex  
**OS:** Ubuntu 22.04  
**Web Server:** Nginx  
**PHP:** PHP-FPM 8.4  
**SSL:** Let's Encrypt (certbot)

### Deployment Script

**Location:** `deployment/vps-deploy.sh`

**Usage:**

```bash
ssh root@houston1.oldlinecyber.com
cd /home/alex/smbgen
bash deployment/vps-deploy.sh
```

**What it does:**

1. Git pull latest code
2. Composer install (production mode)
3. Run database migrations
4. Clear all caches (config, route, view)
5. Rebuild optimized caches
6. Set storage permissions (www-data)
7. Build frontend assets (npm)
8. Restart PHP-FPM + Nginx

### Nginx Configuration

**Location:** `/etc/nginx/sites-available/smbgen`

**Key sections:**

```nginx
server {
    listen 443 ssl http2;
    server_name houston1.oldlinecyber.com;
    root /home/alex/smbgen/public;
    
    # SSL
    ssl_certificate /etc/letsencrypt/live/houston1.oldlinecyber.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/houston1.oldlinecyber.com/privkey.pem;
    
    # Rate limiting
    limit_req zone=admin burst=5;
    limit_req zone=login burst=1;
    
    # IP Whitelisting for /admin
    location /admin {
        allow 68.33.53.17;
        deny all;
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### PHP-FPM Configuration

**Pool:** `/etc/php/8.4/fpm/pool.d/www.conf`

```ini
user = www-data
group = www-data
listen = /var/run/php/php8.4-fpm.sock
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
```

### File Permissions

```bash
# Storage and cache must be writable by web server
chown -R www-data:www-data storage bootstrap/cache

# Application files owned by deploy user
chown -R alex:alex /home/alex/smbgen
```

### Git Repository

**Remote:** github.com/alexramsey92/smbgen  
**Branch:** main

**Deployment workflow:**

```bash
# Local
git add -A
git commit -m "Feature: ..."
git push origin main

# VPS
ssh root@houston1.oldlinecyber.com
cd /home/alex/smbgen
bash deployment/vps-deploy.sh
```

---

## Troubleshooting Guide

### Common Issues

#### 500 Internal Server Error

**Diagnosis:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx error logs
tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
tail -f /var/log/php8.4-fpm.log

# Run debug script
bash deployment/debug-vps.sh
```

**Common Causes:**
- Permissions: `storage/` or `bootstrap/cache/` not writable
- Missing .env file
- Invalid .env syntax
- Database connection failed
- Composer dependencies not installed

**Fixes:**
```bash
# Fix permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reinstall dependencies
composer install --no-dev --optimize-autoloader
```

#### Route Not Found

**Error:** `Route [admin.email.index] not defined`

**Cause:** Code references routes that aren't registered yet

**Solution:**

1. Add route safety checks:
```blade
@if(Route::has('admin.email.index'))
    <a href="{{ route('admin.email.index') }}">Email Composer</a>
@endif
```

2. Clear route cache:
```bash
php artisan route:clear
php artisan route:cache
```

#### Vite Manifest Not Found

**Error:** `Vite manifest not found at: /public/build/manifest.json`

**Cause:** Frontend assets not built on VPS

**Solution:**

```bash
# Install Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
apt-get install -y nodejs

# Build assets
npm install
npm run build
```

#### Google Calendar API Errors

**Error:** `Invalid token` or `Token expired`

**Solution:**

```php
// Refresh access token
$service = new GoogleCalendarService();
$service->refreshAccessToken($user);
```

**Error:** `Insufficient permission`

**Solution:**
- Check OAuth scopes include `CALENDAR` and `CALENDAR_EVENTS`
- User needs to re-authorize application

#### Email Sending Failed

**Diagnosis:**
```bash
# Test SMTP connection
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

**Common Causes:**
- Invalid SMTP credentials in `.env`
- Firewall blocking port 465
- SPF/DKIM not configured

**Check SMTP settings:**
```env
MAIL_MAILER=smtp
MAIL_HOST=rtsenviro.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl  # Must be 'ssl' for port 465
```

#### IP Whitelisting Issues

**Error:** 403 Forbidden on `/admin` routes

**Cause:** Your IP not whitelisted in Nginx config

**Solution:**

```bash
# Auto-detect and whitelist your IP
bash deployment/whitelist-my-ip.sh

# Or manually:
ssh root@houston1.oldlinecyber.com
nano /etc/nginx/sites-available/smbgen

# Add your IP:
location /admin {
    allow YOUR.IP.ADDRESS.HERE;
    allow 68.33.53.17;  # Existing IPs
    deny all;
}

# Test and reload
nginx -t
systemctl reload nginx
```

#### Database Migration Errors

**Error:** `Syntax error in migration`

**Solution:**

```bash
# Check migration files for syntax errors
php artisan migrate --pretend

# Roll back last migration
php artisan migrate:rollback

# Fresh migrate (CAUTION: deletes all data)
php artisan migrate:fresh

# Or use fix script
bash deployment/fix-migrations.sh
```

### Performance Optimization

#### Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Enable OPcache (already in PHP 8.4)
php -i | grep opcache
```

#### Queue Workers

For background jobs:

```bash
# Start queue worker
php artisan queue:work --daemon

# Or use Supervisor
sudo nano /etc/supervisor/conf.d/smbgen-worker.conf
```

### Debugging Commands

```bash
# Show all routes
php artisan route:list

# Show environment variables
php artisan env

# Run tests
php artisan test

# Check database connection
php artisan db:show

# Show Laravel info
php artisan about
```

---

## Development Workflow

### Local Development

```bash
# Start Herd (automatic)
# Visit: http://smbgen.test

# Watch for file changes
npm run dev

# Clear caches during development
php artisan optimize:clear
```

### Making Changes

1. **Create feature branch (optional):**
   ```bash
   git checkout -b feature/new-feature
   ```

2. **Make changes and test locally**

3. **Commit changes:**
   ```bash
   git add -A
   git commit -m "Feature: Description"
   ```

4. **Push to GitHub:**
   ```bash
   git push origin main
   ```

5. **Deploy to VPS:**
   ```bash
   ssh root@houston1.oldlinecyber.com
   cd /home/alex/smbgen
   bash deployment/vps-deploy.sh
   ```

### Code Quality

```bash
# Run tests
php artisan test

# Static analysis (if installed)
./vendor/bin/phpstan analyse

# Code formatting (if installed)
./vendor/bin/pint
```

---

## Security Considerations

### Environment Variables

**NEVER commit `.env` to git!**

Sensitive values:
- Database credentials
- API keys (Google, OpenAI)
- SMTP passwords
- App keys

### Rate Limiting

Configured per route group:

```php
// Admin: 5 requests/min
Route::middleware(['throttle:admin'])

// Login: 1 request/min  
Route::middleware(['throttle:login'])

// Public: 60 requests/min (default)
Route::middleware(['throttle:api'])
```

### CSRF Protection

All POST requests require CSRF token:

```blade
<form method="POST">
    @csrf
    <!-- Form fields -->
</form>
```

### SQL Injection Prevention

Always use Eloquent or query builder:

```php
// GOOD: Protected against SQL injection
User::where('email', $request->input('email'))->first();

// BAD: Vulnerable
DB::select("SELECT * FROM users WHERE email = '{$_GET['email']}'");
```

### XSS Prevention

Blade automatically escapes output:

```blade
{{-- Safe: HTML entities escaped --}}
{{ $userInput }}

{{-- Unsafe: Raw HTML --}}
{!! $trustedHtml !!}
```

---

## Additional Resources

- **Laravel Documentation:** [laravel.com/docs/12.x](https://laravel.com/docs/12.x)
- **Google Calendar API:** [developers.google.com/calendar](https://developers.google.com/calendar)
- **Tailwind CSS:** [tailwindcss.com/docs](https://tailwindcss.com/docs)
- **Livewire:** [livewire.laravel.com](https://livewire.laravel.com)

---

## Support

For technical issues, check:
1. `storage/logs/laravel.log` for Laravel errors
2. `/var/log/nginx/error.log` for server errors
3. Run `deployment/debug-vps.sh` for diagnostics
4. See Troubleshooting Guide above

For feature requests, see `ROADMAP.md`
