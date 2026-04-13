# CLIENTBRIDGE - Comprehensive Codebase Overview

**Version:** 1.0.0  
**Framework:** Laravel 12.32.5  
**PHP Version:** 8.4.14  
**Database:** MySQL  
**Created by:** Alexander Ramsey

---

## 📋 Table of Contents

1. [Executive Summary](#executive-summary)
2. [Platform Overview](#platform-overview)
3. [Core Features](#core-features)
4. [Technical Architecture](#technical-architecture)
5. [Key Integrations](#key-integrations)
6. [Database Schema](#database-schema)
7. [Feature Flags](#feature-flags)
8. [Directory Structure](#directory-structure)
9. [Security & Authentication](#security--authentication)
10. [Deployment](#deployment)
11. [Development Workflow](#development-workflow)
12. [Testing](#testing)
13. [Documentation](#documentation)

---

## Executive Summary

**CLIENTBRIDGE** is a comprehensive client management and business automation platform built for service professionals including contractors, inspectors, realtors, and field service providers. The platform unifies booking systems, lead management, client communications, file storage, and business operations into a single, cohesive application.

### Target Market
- Contractors and home service providers
- Property inspectors
- Real estate professionals
- Field service businesses
- Professional service consultants

### Value Proposition
One unified platform that eliminates the need for multiple disconnected tools by providing:
- Zero-friction appointment booking with Google Calendar sync
- Intelligent lead capture and conversion
- Professional client communications
- Secure file management
- Business analytics and reporting

---

## Platform Overview

### Purpose
CLIENTBRIDGE serves as a **unified business management platform** designed to streamline operations for service-based businesses. It replaces the need for multiple disparate tools (scheduling software, CRM, email management, file storage) with a single integrated solution.

### Business Model
- **SaaS Platform:** Subscription-based service at `clientbridge.app`
- **Services Hybrid:** Quick audits with upsell opportunities for ongoing service
- **Scalable Architecture:** Built to grow from individual professionals to enterprise teams

### Core Philosophy
1. **Zero Friction:** Eliminate unnecessary steps in booking, communication, and client management
2. **Property-Aware:** Built specifically for businesses that work on physical properties
3. **Professional First:** Enterprise-grade features with small business pricing
4. **Integration Native:** Seamless connections with Google Workspace, Stripe, and essential business tools

---

## Core Features

### 1. Client Management System

**Purpose:** Complete CRM for managing client relationships, interactions, and data.

**Key Components:**
- `app/Models/Client.php` - Core client model
- `app/Http/Controllers/ClientController.php` - Client CRUD operations
- Client import/export functionality (CSV and VCF/vCard formats)

**Capabilities:**
- Full CRUD operations for client records
- Property address tracking for real estate use cases
- Interaction history and notes
- Client file management
- CSV and phone contacts import
- Export to CSV for backup/analysis
- Client provisioning for user account creation

**Routes:**
```
GET     /admin/clients              - List all clients
POST    /admin/clients              - Create new client
GET     /admin/clients/create       - Client creation form
GET     /admin/clients/{client}     - View client details
PATCH   /admin/clients/{client}     - Update client
DELETE  /admin/clients/{client}     - Delete client
GET     /admin/clients/export/csv   - Export clients to CSV
```

### 2. Booking & Scheduling System

**Purpose:** Zero-friction appointment scheduling with automatic Google Calendar synchronization.

**Key Components:**
- `app/Models/Booking.php` - Booking records
- `app/Models/Availability.php` - Availability rules
- `app/Services/GoogleCalendarService.php` - Calendar integration
- `app/Http/Controllers/BookingController.php` - Public booking wizard
- `app/Http/Controllers/Admin/BookingController.php` - Admin management

**Capabilities:**
- Public-facing booking wizard with real-time availability
- Google Calendar bidirectional sync
- Automatic Google Meet link generation
- Configurable availability rules (duration, breaks, blackout dates)
- 15-minute grace period between meetings
- Property address collection (configurable)
- Automatic lead creation from bookings
- Email confirmations and reminders
- Convert booking to client functionality
- Break period configuration between appointments

**Configuration:**
```env
FEATURE_BOOKING=true
BOOKING_REQUIRE_PROPERTY_ADDRESS=false
BOOKING_SHOW_PROPERTY_ADDRESS=true
BOOKING_REQUIRE_PHONE=false
BOOKING_SHOW_PHONE=true
BOOKING_CREATE_LEAD=true
```

**Routes:**
```
GET     /book                       - Public booking wizard
POST    /book                       - Create booking
GET     /book/availability          - Check availability
GET     /book/confirmation          - Booking confirmation
GET     /admin/bookings             - Admin booking list
GET     /admin/bookings/dashboard   - Booking dashboard
POST    /admin/bookings/{id}/convert-to-client  - Convert to client
```

### 3. Lead Management & Conversion

**Purpose:** Capture, track, and convert leads from multiple sources including CMS forms, bookings, and direct submissions.

**Key Components:**
- `app/Models/LeadForm.php` - Lead records with form data
- `app/Http/Controllers/Admin/LeadController.php` - Lead management
- `app/Http/Controllers/LeadFormController.php` - Form submissions
- `app/Http/Controllers/CmsFormSubmissionController.php` - CMS form handling

**Capabilities:**
- Multi-source lead capture (CMS forms, booking system, API)
- Custom form field storage in JSON format
- Lead source tracking (referrer, IP, user agent)
- One-click conversion to client
- Lead export to CSV
- Form submission tracking with metadata
- Automatic email notifications

**Database Fields:**
- Standard fields: name, email, phone, message, property_address
- `form_data` JSON column for custom form fields
- `cms_page_id` for tracking form source
- `source_site` for external integrations
- IP address and user agent tracking

**Routes:**
```
GET     /admin/leads                - Lead list with filters
GET     /admin/leads/{lead}         - Lead details
POST    /admin/leads/{lead}/convert - Convert to client
GET     /admin/leads/export/csv     - Export leads
POST    /cms/form/{slug}            - Submit CMS form
```

### 4. Content Management System (CMS)

**Purpose:** Build and manage landing pages with embedded lead capture forms.

**Key Components:**
- `app/Models/CmsPage.php` - CMS pages with form configuration
- `app/Http/Controllers/Admin/CmsPageController.php` - Admin management
- `app/Http/Controllers/CmsPagePublicController.php` - Public pages
- `database/seeders/HomeProPageSeeder.php` - Example multi-page website

**Capabilities:**
- Dynamic page creation with slug-based routing
- Customizable head content (CSS, JavaScript)
- Flexible body content
- Embedded lead capture forms with custom fields
- Form builder with field types:
  - text, email, tel, textarea
  - select dropdowns
  - checkbox, radio buttons
- Publish/draft status
- SEO optimization
- Special handling for home/landing pages
- Email notifications for form submissions

**Form Configuration Example:**
```php
'form_fields' => [
    [
        'name' => 'full_name',
        'type' => 'text',
        'label' => 'Full Name',
        'required' => true,
    ],
    [
        'name' => 'service_needed',
        'type' => 'select',
        'label' => 'Service',
        'options' => 'Inspection,Remediation,Other',
        'required' => true,
    ],
]
```

**Routes:**
```
GET     /admin/cms              - CMS page list
POST    /admin/cms              - Create page
GET     /admin/cms/create       - Page creation form
GET     /admin/cms/{page}/edit  - Edit page
GET     /{slug}                 - Public page view
POST    /cms/form/{slug}        - Form submission
```

**Example Implementation:**
The `HomeProPageSeeder` demonstrates a complete multi-page website with:
- Consistent navigation across 7 pages
- Two-level navbar (action bar + page links)
- Embedded contact forms
- Mobile-responsive design
- Property-specific lead capture

### 5. Communication Hub

**Purpose:** Professional email management and internal messaging system.

**Key Components:**
- `app/Mail/` - Email classes
- `app/Http/Controllers/Admin/EmailController.php` - Email management
- `app/Http/Controllers/MessageController.php` - Internal messaging
- `app/Models/EmailLog.php` - Email tracking

**Email System Capabilities:**
- Template-based professional emails
- Multiple recipient support
- Email tracking (opens and clicks)
- SMTP configuration testing
- Send history and resend functionality
- Booking-specific email templates
- Email verification

**Internal Messaging:**
- Client-staff communication
- Message threading
- Read/unread status
- Message replies
- Secure in-platform messaging

**Routes:**
```
GET     /admin/email            - Email management
POST    /admin/email/send       - Send email
GET     /admin/email-logs       - Email history
POST    /admin/email-logs/test-smtp  - Test SMTP
GET     /messages               - Message inbox
POST    /messages               - Send message
POST    /messages/{id}/reply    - Reply to message
```

### 6. File Management System

**Purpose:** Secure document storage and sharing with clients.

**Key Components:**
- `app/Http/Controllers/ClientFileController.php` - Client file access
- `app/Http/Controllers/Admin/AdminClientFileController.php` - Admin file management
- File storage in `storage/app/client-files/`

**Capabilities:**
- Secure file uploads per client
- File metadata tracking
- Download with authentication
- Admin file overview across all clients
- Client-specific file access
- File type validation
- Size limits enforcement

**Routes:**
```
GET     /documents              - Client file list
POST    /documents/upload       - Upload file
GET     /documents/download/{file}  - Download file
DELETE  /documents/{file}       - Delete file
GET     /admin/clients/{id}/files   - Client files (admin)
GET     /admin/clients/files        - All files (admin)
```

### 7. Billing & Payment System

**Purpose:** Invoice management and payment processing via Stripe.

**Key Components:**
- `app/Http/Controllers/Admin/AdminBillingController.php` - Invoice management
- `app/Http/Controllers/BillingController.php` - Client billing
- `app/Http/Controllers/PaymentController.php` - Payment processing
- Stripe integration for payment processing
- QuickBooks integration for accounting

**Capabilities:**
- Invoice creation and management
- Stripe payment link generation
- Payment processing
- Invoice history
- Email invoice delivery
- QuickBooks sync
- Hourly rate configuration
- Multiple payment methods

**Configuration:**
```env
FEATURE_BILLING=false  # Enable/disable billing feature
BILLING_HOURLY_RATE_CENTS=20000  # $200.00/hour
STRIPE_PUBLIC_KEY=your_key
STRIPE_SECRET_KEY=your_key
STRIPE_WEBHOOK_SECRET=your_secret
```

**Routes:**
```
GET     /billing                - Client billing dashboard
POST    /billing/invoices/{id}/pay  - Pay invoice
GET     /admin/billing          - Admin billing management
POST    /admin/billing/{user}   - Create invoice
POST    /admin/billing/invoices/{id}/send  - Send invoice
```

### 8. Inspection Reports (Optional)

**Purpose:** Generate and manage property inspection reports with Google Drive integration.

**Key Components:**
- `app/Http/Controllers/Admin/InspectionReportController.php`
- `app/Models/InspectionReport.php`
- Google Drive integration for report storage

**Capabilities:**
- Report generation
- PDF export
- Google Drive backup
- Report resend functionality
- Client access to reports

**Configuration:**
```env
FEATURE_INSPECTION_REPORTS=false  # Enable/disable
```

### 9. User Management & Authentication

**Purpose:** Secure multi-role user authentication and authorization.

**Key Components:**
- Laravel Breeze for authentication scaffolding
- `app/Models/User.php` - User accounts
- `app/Http/Controllers/Admin/UserController.php` - User management
- Role-based access control
- Google OAuth integration

**User Roles:**
- **Admin:** Full system access
- **Staff:** Client and booking management
- **Client:** Limited portal access

**Authentication Methods:**
- Traditional email/password
- Google OAuth (via Socialite)
- Magic links
- Email verification
- Password reset

**Security Features:**
- IP whitelisting for admin routes
- Passkey authentication support
- Email verification enforcement
- Password strength requirements
- Rate limiting

**Routes:**
```
GET     /login                  - Login page
POST    /login                  - Authenticate
POST    /logout                 - Logout
GET     /register               - Registration
POST    /register               - Create account
GET     /forgot-password        - Password reset request
GET     /auth/google/redirect   - Google OAuth
GET     /auth/google/callback   - OAuth callback
```

### 10. Client Import System

**Purpose:** Bulk import clients from CSV files or phone contact exports.

**Key Components:**
- `app/Http/Controllers/ClientImportController.php`
- `app/Models/ClientImport.php`
- CSV parser with header normalization
- VCF/vCard parser for phone contacts

**Capabilities:**
- CSV import with validation
- Phone contacts import (VCF/vCard format)
- Import preview with error detection
- Batch processing with transaction safety
- Import history tracking
- Downloadable CSV template
- Support for iPhone and Android contact exports
- Handle contacts with phone numbers only

**Supported Formats:**
- CSV files (.csv, .txt)
- vCard files (.vcf, .vcard)

**Import Fields:**
- name (required)
- email
- phone
- property_address
- notes
- source_site

**Routes:**
```
GET     /admin/clients-import           - Import dashboard
POST    /admin/clients-import/upload    - Upload file
GET     /admin/clients-import/{id}/preview  - Preview import
POST    /admin/clients-import/{id}/process  - Process import
GET     /admin/clients-import/history   - Import history
```

**Template Files:**
- `public/examples/clients-import-template.csv` - Downloadable template with examples

---

## Technical Architecture

### Framework & Versions

**Core Stack:**
- **Laravel Framework:** 12.32.5
- **PHP:** 8.4.14
- **Database:** MySQL
- **Node.js:** 24.5+

**Key Packages:**
- **Livewire:** 3.6.4 - Reactive UI components
- **Laravel Breeze:** 2.3.8 - Authentication
- **Laravel Socialite:** 5.23.0 - OAuth integration
- **Laravel Nightwatch:** 1.14.0 - Debugging and monitoring
- **Laravel Prompts:** 0.3.7 - CLI interactions
- **Laravel MCP:** 0.2.1 - Model Context Protocol
- **Laravel Pint:** 1.25.1 - Code formatting
- **Laravel Sail:** 1.46.0 - Docker development
- **Pest:** 3.8.4 - Testing framework
- **PHPUnit:** 11.5.33 - Testing
- **Alpine.js:** 3.15.0 - Frontend interactivity
- **Tailwind CSS:** 3.4.18 - Styling

### Application Structure

```
app/
├── Console/Commands/       # Artisan commands
│   └── CleanExpiredPasswordResets.php
├── Http/
│   ├── Controllers/       # Application controllers
│   │   ├── Admin/        # Admin-only controllers
│   │   ├── Auth/         # Authentication controllers
│   │   └── ...           # Public controllers
│   ├── Middleware/       # Custom middleware
│   └── Requests/         # Form request validation
├── Models/               # Eloquent models
│   ├── Booking.php
│   ├── Client.php
│   ├── LeadForm.php
│   ├── CmsPage.php
│   ├── User.php
│   └── ...
├── Services/            # Business logic services
│   ├── GoogleCalendarService.php
│   └── ...
├── Mail/                # Email classes
├── Policies/            # Authorization policies
├── Providers/           # Service providers
├── View/                # View composers
└── docs/                # Project documentation
```

### Model Relationships

**Client Model:**
```php
- hasMany(Booking)
- hasMany(File)
- hasOne(User) // If provisioned
- property_address (text) // For real estate
```

**Booking Model:**
```php
- belongsTo(Client)
- belongsTo(User) // Assigned to
- property_address (text)
- break_period_minutes (integer)
```

**LeadForm Model:**
```php
- belongsTo(CmsPage) // Source page
- form_data (json) // Custom fields
- source_site (string)
- ip_address, user_agent
```

**CmsPage Model:**
```php
- hasMany(LeadForm) // Form submissions
- form_fields (json) // Form configuration
- has_form (boolean)
```

### Service Layer

**GoogleCalendarService:**
- OAuth authentication
- Calendar synchronization
- Event creation/update/deletion
- Availability checking
- Double-booking prevention

**Purpose:** Abstraction layer for complex business logic, keeping controllers thin and focused.

### Queue System

The application uses Laravel's queue system for:
- Email sending
- Calendar sync operations
- Large import processing
- Report generation

**Configuration:**
```env
QUEUE_CONNECTION=database  # or redis
```

**Running Workers:**
```bash
php artisan queue:work
```

---

## Key Integrations

### 1. Google Workspace

**Services Integrated:**
- Google Calendar (bidirectional sync)
- Google Drive (inspection report storage)
- Google OAuth (authentication)
- Google Meet (automatic meeting links)

**Configuration:**
```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=https://your-domain.test/admin/google-oauth/callback
```

**Routes:**
```
GET     /admin/google-oauth                 - Google OAuth settings
POST    /admin/google-oauth                 - Save OAuth credentials
GET     /admin/calendar/redirect            - Initiate calendar auth
GET     /admin/calendar/callback            - OAuth callback
POST    /admin/calendar/disconnect          - Disconnect calendar
GET     /admin/calendar/select              - Select calendar
POST    /admin/calendar/update              - Update calendar
```

### 2. Stripe Payment Processing

**Capabilities:**
- Payment collection
- Invoice generation
- Payment links
- Webhook handling
- Customer management

**Configuration:**
```env
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

**Webhook Endpoint:**
```
POST    /stripe/webhook
```

### 3. QuickBooks Accounting

**Purpose:** Sync invoices and payments to QuickBooks Online.

**Configuration:**
```env
QUICKBOOKS_CLIENT_ID=your_client_id
QUICKBOOKS_CLIENT_SECRET=your_client_secret
QUICKBOOKS_REDIRECT_URI=https://your-domain.test/admin/quickbooks/callback
QUICKBOOKS_ENVIRONMENT=development  # or production
```

### 4. Twilio & Bland AI (Optional)

**Purpose:** Phone system integration with AI-powered calling.

**Configuration:**
```env
BLAND_API_KEY=your_api_key
BLAND_PHONE_NUMBER=+1234567890
TWILIO_ACCOUNT_SID=your_sid
TWILIO_AUTH_TOKEN=your_token
TWILIO_PHONE_NUMBER=+1234567890
```

### 5. OpenAI

**Purpose:** AI-powered features and assistance.

**Configuration:**
```env
OPENAI_API_KEY=sk-...
```

**Features:**
- Cyber audit assistant
- SEO assistant
- Content generation

---

## Database Schema

### Core Tables

**users**
- Authentication and user accounts
- Role-based access (admin, staff, client)
- Google OAuth token storage

**clients**
- Core client records
- Contact information
- Property address (for real estate)
- Timestamps and soft deletes

**bookings**
- Appointment records
- Related to client and assigned user
- Property address
- Break period configuration
- Status tracking

**lead_forms**
- Lead submissions from all sources
- Standard fields: name, email, phone, message, property_address
- `form_data` JSON for custom fields
- `cms_page_id` for tracking source
- Metadata: IP, user agent, referrer

**cms_pages**
- Dynamic page content
- Form configuration JSON
- Publish status
- SEO settings

**client_imports**
- Import tracking
- File information
- Processing status
- Error logs

**availabilities**
- User availability rules
- Time slots
- Blackout dates

**email_logs**
- Email tracking
- Send history
- Open/click tracking

**messages**
- Internal messaging
- Read status
- Relationships between users/clients

### Migration Files

Located in `database/migrations/`, key migrations include:

- User and authentication tables
- Client management tables
- Booking system tables
- CMS and lead form tables
- Import system tables
- Email tracking tables
- Message system tables

---

## Feature Flags

### Configuration Location
`config/business.php`

### Available Flags

```php
'features' => [
    'booking' => true,              // Booking & scheduling system
    'billing' => false,             // Billing & invoicing
    'messages' => true,             // Internal messaging
    'cms' => true,                  // CMS & lead forms
    'inspection_reports' => false,  // Property inspection reports
]
```

### Environment Variables

```env
FEATURE_BOOKING=true
FEATURE_BILLING=false
FEATURE_MESSAGES=true
FEATURE_CMS=true
FEATURE_INSPECTION_REPORTS=false
```

### Usage in Code

**Controllers:**
```php
if (config('business.features.booking')) {
    // Booking feature code
}
```

**Blade Templates:**
```blade
@if(config('business.features.cms'))
    <a href="{{ route('admin.cms.index') }}">CMS</a>
@endif
```

**Routes:**
```php
Route::middleware(['auth'])->group(function () {
    if (config('business.features.booking')) {
        Route::resource('bookings', BookingController::class);
    }
});
```

### Booking Sub-Configuration

```env
BOOKING_REQUIRE_PROPERTY_ADDRESS=false
BOOKING_SHOW_PROPERTY_ADDRESS=true
BOOKING_REQUIRE_PHONE=false
BOOKING_SHOW_PHONE=true
BOOKING_CREATE_LEAD=true
```

---

## Directory Structure

```
clientbridge-laravel/
├── app/
│   ├── Console/Commands/          # Custom artisan commands
│   ├── docs/                      # Project documentation
│   ├── Helpers/                   # Helper functions
│   ├── Http/
│   │   ├── Controllers/          # Application controllers
│   │   │   ├── Admin/           # Admin-only controllers
│   │   │   └── Auth/            # Authentication controllers
│   │   ├── Middleware/          # Custom middleware
│   │   └── Requests/            # Form requests
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Email classes
│   ├── Models/                   # Eloquent models
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Services/                 # Business logic services
│   └── View/                     # View composers
├── bootstrap/
│   ├── app.php                   # Application bootstrap
│   └── cache/                    # Bootstrap cache
├── config/                       # Configuration files
│   ├── app.php
│   ├── business.php             # Business configuration
│   ├── approved_ips.php         # IP whitelist
│   ├── auth.php
│   ├── database.php
│   ├── services.php             # Third-party services
│   └── ...
├── database/
│   ├── factories/               # Model factories
│   ├── migrations/              # Database migrations
│   ├── schema/                  # Database schema
│   └── seeders/                 # Database seeders
├── deployment/                  # Deployment scripts
│   ├── vps-deploy.sh
│   ├── check-google-connection.sh
│   ├── restart-services.sh
│   └── README.md
├── public/
│   ├── build/                   # Compiled assets
│   ├── examples/               # Example files (CSV templates)
│   ├── storage -> ../storage/app/public
│   ├── index.php
│   └── robots.txt
├── resources/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript
│   └── views/                  # Blade templates
│       ├── admin/             # Admin views
│       ├── auth/              # Authentication views
│       ├── book/              # Booking wizard
│       ├── client/            # Client portal
│       ├── cms/               # CMS views
│       ├── components/        # Blade components
│       ├── layouts/           # Layout templates
│       └── ...
├── routes/
│   ├── auth.php               # Authentication routes
│   ├── console.php            # Console routes
│   ├── web.php                # Web routes
│   └── livewire-demo.php      # Livewire demo routes
├── scripts/                   # Setup and utility scripts
│   ├── setup-herd-gitbash.sh
│   ├── setup-nodejs-24-5-windows.sh
│   └── troubleshoot-*.sh
├── storage/
│   ├── app/
│   │   ├── client-files/      # Client uploaded files
│   │   └── public/            # Public storage
│   ├── framework/             # Framework cache
│   └── logs/                  # Application logs
├── tests/
│   ├── Feature/               # Feature tests
│   ├── Unit/                  # Unit tests
│   ├── Pest.php              # Pest configuration
│   └── TestCase.php
├── vendor/                    # Composer dependencies
├── .env.example              # Environment template
├── artisan                   # Artisan CLI
├── composer.json
├── package.json
├── phpunit.xml
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## Security & Authentication

### Authentication System

**Laravel Breeze:**
- Email/password authentication
- Registration
- Password reset
- Email verification
- Remember me functionality

**OAuth Integration:**
- Google authentication via Socialite
- Automatic account linking
- Token refresh handling

**Magic Links:**
- One-time login links
- Email-based authentication
- Time-limited validity

### Authorization

**Policies:**
- Client policies
- Booking policies
- File access policies
- CMS page policies

**Middleware:**
- `auth` - Requires authentication
- `verified` - Requires email verification
- `admin` - Admin-only access
- IP whitelist middleware for admin routes

### IP Whitelisting

**Configuration:** `config/approved_ips.php`

**Purpose:** Restrict admin access to approved IP addresses.

**Environment Variables:**
```env
APPROVED_IPS=127.0.0.1,192.168.1.100
```

### CSRF Protection

All forms include CSRF tokens via `@csrf` directive.

### File Upload Security

- Validation of file types
- Size limits enforcement
- Secure storage paths
- Authentication required for access
- Metadata tracking

### API Security

- Rate limiting on authentication routes
- CORS configuration
- Webhook signature verification (Stripe)
- API token authentication where applicable

---

## Deployment

### Production Environment

**Recommended Setup:**
- VPS or cloud hosting (DigitalOcean, Linode, AWS)
- NGINX or Apache web server
- MySQL 8.0+ or MariaDB
- Redis for caching and queues
- SSL certificate (Let's Encrypt)
- Supervisor for queue workers

### Deployment Script

Located at `deployment/vps-deploy.sh`

**Steps:**
1. Pull latest code
2. Install/update dependencies
3. Run migrations
4. Clear and rebuild cache
5. Restart services
6. Health checks

**Usage:**
```bash
cd deployment
./vps-deploy.sh
```

### Environment Configuration

**Production `.env` essentials:**
```env
APP_NAME=CLIENTBRIDGE
APP_ENV=production
APP_DEBUG=false
APP_URL=https://clientbridge.app

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=clientbridge_prod
DB_USERNAME=your_username
DB_PASSWORD=secure_password

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
# ... mail configuration

# Feature flags
FEATURE_BOOKING=true
FEATURE_BILLING=false
FEATURE_MESSAGES=true
FEATURE_CMS=true

# Google integration
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

### Queue Workers

**Supervisor Configuration:**
```ini
[program:clientbridge-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

### Scheduled Tasks

**Cron Configuration:**
```cron
* * * * * cd /path/to/application && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduled Commands:**
- Clean expired password resets (daily)
- Send booking reminders (hourly)
- Sync Google Calendar (every 5 minutes)
- Generate reports (as configured)

---

## Development Workflow

### Local Development

**Requirements:**
- PHP 8.4+
- Composer
- Node.js 24.5+
- MySQL/MariaDB
- Laravel Herd (recommended) or Valet

**Setup:**
```bash
# Clone repository
git clone https://github.com/alexramsey92/clientbridge-laravel.git
cd clientbridge-laravel

# Install dependencies
composer install
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Development mode (all services)
composer run dev
```

### Development Commands

**Start development environment:**
```bash
composer run dev
# Runs: server, queue worker, logs, and vite concurrently
```

**Individual services:**
```bash
php artisan serve          # Development server
php artisan queue:work     # Queue worker
php artisan pail          # Log viewer
npm run dev               # Vite dev server
```

### Code Quality

**Laravel Pint (Code Formatting):**
```bash
vendor/bin/pint           # Format all files
vendor/bin/pint --dirty   # Format changed files only
vendor/bin/pint --test    # Check without formatting
```

**Running Tests:**
```bash
composer test             # Run all tests
php artisan test         # Alternative syntax
php artisan test --filter=BookingTest  # Specific test
```

### Git Workflow

**Branch Strategy:**
- `main` - Production-ready code
- `develop` - Development branch
- `feature/*` - Feature branches
- `hotfix/*` - Production hotfixes

**Commit Convention:**
```
feat: Add phone contacts import
fix: Handle VCF files with phone-only contacts
docs: Update comprehensive overview
refactor: Extract navigation to shared variables
test: Add booking conversion tests
```

---

## Testing

### Test Framework

**Pest PHP 3.8.4:**
Modern testing framework built on PHPUnit.

**Test Types:**
- **Feature Tests:** Test entire features and workflows
- **Unit Tests:** Test individual methods and classes

### Running Tests

```bash
# All tests
composer test
php artisan test

# Specific file
php artisan test tests/Feature/BookingTest.php

# With coverage
php artisan test --coverage

# Filter by name
php artisan test --filter=booking
```

### Test Structure

**Location:** `tests/Feature/` and `tests/Unit/`

**Example Test:**
```php
it('creates a booking with property address', function () {
    $client = Client::factory()->create();
    
    $response = $this->postJson('/book', [
        'client_id' => $client->id,
        'booking_date' => now()->addDay()->format('Y-m-d'),
        'booking_time' => '10:00',
        'property_address' => '123 Main St',
    ]);
    
    $response->assertSuccessful();
    
    expect(Booking::first()->property_address)
        ->toBe('123 Main St');
});
```

### Test Database

**Configuration:**
Uses SQLite in-memory database for fast tests.

**Test Environment (.env.testing):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Factories

**Location:** `database/factories/`

**Key Factories:**
- `UserFactory`
- `ClientFactory`
- `BookingFactory`
- `LeadFormFactory`
- `ClientImportFactory`

**Usage:**
```php
$client = Client::factory()->create();
$booking = Booking::factory()->for($client)->create();
```

---

## Documentation

### Available Documentation

Located in `app/docs/`:

1. **COMPREHENSIVE_OVERVIEW.md** (this file)
   - Complete system overview

2. **BUSINESS_ANALYSIS.md**
   - Business logic and features
   - Use case analysis

3. **BOOKING_SYSTEM.md**
   - Booking system deep dive
   - Google Calendar integration
   - Configuration guide

4. **CMS_FORMS_TO_LEADS.md**
   - CMS form system
   - Lead capture workflow
   - Form builder guide

5. **FEATURE_FLAGS.md**
   - Feature flag system
   - Configuration options
   - Usage examples

6. **CONTEXT.md**
   - High-level project context
   - Business model
   - Target market

7. **PLATFORM_OVERVIEW.md**
   - Platform capabilities
   - Value proposition
   - Feature summaries

8. **CONSOLIDATION_PLAN.md**
   - Feature consolidation strategy
   - Implementation phases

9. **SESSION_SUMMARY.md**
   - Development session logs
   - Recent changes

10. **PROGRESS_REPORT.md**
    - Project progress tracking
    - Completed features

11. **ROADMAP_STATUS_OCT2025.md**
    - Feature status
    - Future roadmap

12. **DASHBOARD_REFACTOR_PLAN.md**
    - Dashboard improvements
    - UI/UX enhancements

13. **ARCHITECTURE_VISION.md**
    - Technical architecture
    - System design

14. **QUICK_REFERENCE.md**
    - Quick access to key info
    - Common patterns

15. **README.md** / **README_PROJECT.md**
    - Project setup
    - Getting started

### Code Documentation

**PHPDoc Blocks:**
All models, controllers, and services include comprehensive PHPDoc comments.

**Inline Comments:**
Complex logic includes explanatory comments.

**Blade Comments:**
View files include section markers and explanations.

---

## API Reference

### RESTful Conventions

The application follows Laravel's RESTful resource conventions:

- `GET /resource` - List all
- `POST /resource` - Create new
- `GET /resource/create` - Creation form
- `GET /resource/{id}` - View single
- `GET /resource/{id}/edit` - Edit form
- `PATCH /resource/{id}` - Update
- `DELETE /resource/{id}` - Delete

### Response Formats

**JSON APIs:**
```json
{
    "success": true,
    "data": {...},
    "message": "Operation successful"
}
```

**Error Responses:**
```json
{
    "success": false,
    "error": "Error description",
    "errors": {
        "field": ["Validation error"]
    }
}
```

---

## Performance Considerations

### Caching Strategy

**Redis:**
- Session storage
- Cache storage
- Queue backend

**Cache Keys:**
- `clients:all` - Client list
- `bookings:availability:{date}` - Availability cache
- `cms:page:{slug}` - CMS page content

### Database Optimization

**Indexes:**
- Foreign keys indexed
- Frequently queried fields indexed
- Composite indexes where appropriate

**Query Optimization:**
- Eager loading to prevent N+1 queries
- Database query caching
- Proper use of `select()` to limit columns

### Asset Optimization

**Vite:**
- Code splitting
- Tree shaking
- Minification
- CSS purging with Tailwind

**Images:**
- Proper image sizing
- Lazy loading where appropriate

---

## Support & Maintenance

### Logging

**Location:** `storage/logs/`

**Log Channels:**
- `daily` - Daily log rotation
- `slack` - Critical errors to Slack (if configured)
- `stderr` - Standard error output

**View Logs:**
```bash
php artisan pail          # Real-time log viewer
tail -f storage/logs/laravel.log  # Traditional tail
```

### Debugging

**Laravel Nightwatch:**
Enhanced debugging and monitoring capabilities.

**Debug Mode:**
```env
APP_DEBUG=true  # Development only!
```

**Debugging Tools:**
- Laravel Telescope (if installed)
- Laravel Debugbar (if installed)
- Browser DevTools
- Log files

### Troubleshooting

**Common Issues:**

1. **Migration errors**
   ```bash
   php artisan migrate:fresh  # Fresh start
   php artisan migrate:rollback  # Undo last
   ```

2. **Permission errors**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Cache issues**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

4. **Queue not processing**
   ```bash
   php artisan queue:restart
   ```

### Monitoring

**Health Checks:**
- Application availability
- Database connectivity
- Queue worker status
- Disk space
- Memory usage

**Deployment Scripts:**
Multiple monitoring scripts in `deployment/`:
- `check-google-connection.sh`
- `check-vite-processes.sh`
- `check-vps-logs.sh`

---

## Future Roadmap

### Planned Features

**Phase 1: Core Enhancements**
- Enhanced analytics dashboard
- Advanced reporting features
- Custom branding per client
- Multi-language support

**Phase 2: Automation**
- Advanced automation workflows
- Email sequences
- Lead scoring
- Automated follow-ups

**Phase 3: Mobile**
- Native mobile applications
- Progressive Web App (PWA)
- Mobile-optimized booking

**Phase 4: Integrations**
- Additional calendar services
- CRM integrations
- Marketing automation
- Accounting software

**Phase 5: Enterprise**
- White-label solution
- Multi-tenant architecture
- Advanced permissions
- API for third-party integrations

### Contributing

**Guidelines:**
1. Fork the repository
2. Create feature branch
3. Follow code style (Laravel Pint)
4. Write tests
5. Submit pull request

**Code Standards:**
- PSR-12 coding standard
- Laravel best practices
- Comprehensive testing
- Documentation updates

---

## Credits & License

**Created by:** Alexander Ramsey  
**Website:** https://alexanderramsey.com  
**Platform:** https://clientbridge.app

**License:** MIT License

**Built With:**
- Laravel Framework
- Livewire
- Alpine.js
- Tailwind CSS
- Google Workspace APIs
- Stripe API
- OpenAI API

---

## Appendix

### Environment Variables Reference

**Application:**
```env
APP_NAME=CLIENTBRIDGE
APP_ENV=local|development|staging|production
APP_DEBUG=true|false
APP_URL=https://clientbridge.app
```

**Database:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clientbridge
DB_USERNAME=root
DB_PASSWORD=
```

**Mail:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@clientbridge.app
MAIL_FROM_NAME="${APP_NAME}"
```

**Google:**
```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=...
```

**Stripe:**
```env
STRIPE_PUBLIC_KEY=...
STRIPE_SECRET_KEY=...
STRIPE_WEBHOOK_SECRET=...
```

**Feature Flags:**
```env
FEATURE_BOOKING=true
FEATURE_BILLING=false
FEATURE_MESSAGES=true
FEATURE_CMS=true
FEATURE_INSPECTION_REPORTS=false
```

**Business Configuration:**
```env
BUSINESS_NAME=CLIENTBRIDGE
BUSINESS_COMPANY_NAME=CLIENTBRIDGE
BUSINESS_EMAIL=support@clientbridge.app
BUSINESS_PHONE=
BUSINESS_WEBSITE=https://clientbridge.app
BOOKING_REQUIRE_PROPERTY_ADDRESS=false
BOOKING_SHOW_PROPERTY_ADDRESS=true
BILLING_HOURLY_RATE_CENTS=20000
```

### Artisan Commands Reference

**Database:**
```bash
php artisan migrate
php artisan migrate:fresh
php artisan migrate:rollback
php artisan db:seed
```

**Cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

**Queue:**
```bash
php artisan queue:work
php artisan queue:restart
php artisan queue:failed
php artisan queue:retry all
```

**Custom Commands:**
```bash
php artisan clean:expired-password-resets
```

### Key File Locations

**Configuration:**
- `config/business.php` - Business settings
- `config/approved_ips.php` - IP whitelist
- `config/services.php` - Third-party services

**Models:**
- `app/Models/Client.php`
- `app/Models/Booking.php`
- `app/Models/LeadForm.php`
- `app/Models/CmsPage.php`
- `app/Models/ClientImport.php`

**Controllers:**
- `app/Http/Controllers/BookingController.php`
- `app/Http/Controllers/ClientController.php`
- `app/Http/Controllers/Admin/`

**Views:**
- `resources/views/admin/` - Admin interface
- `resources/views/book/` - Booking wizard
- `resources/views/cms/` - CMS pages
- `resources/views/layouts/` - Layouts

**Services:**
- `app/Services/GoogleCalendarService.php`

**Tests:**
- `tests/Feature/` - Feature tests
- `tests/Unit/` - Unit tests

---

**Last Updated:** November 23, 2025  
**Version:** 1.0.0  
**Documentation Status:** Complete ✅
