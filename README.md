# smbgen

A modern SaaS platform for service professionals. Built with **Laravel 12**, featuring client management, appointment booking, content management, AI-powered tools, and comprehensive admin dashboards.

**[📖 Full Documentation →](app/docs/README.md)** | **[🚀 Get Started →](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md)**

---

## ✨ Features

### Client Management
- ✅ Full client CRUD with file/document storage
- ✅ CSV import with preview and validation
- ✅ Client provisioning workflows
- ✅ Client portal access control (enable/disable per client)
- ✅ Magic link single-sign-on for passwordless client access
- ✅ Lead conversion (from bookings and form submissions)

### Presentations & Packages
- ✅ Create and manage presentation packages for clients
- ✅ Package file management with file preview
- ✅ Package status tracking and portal visibility toggle
- ✅ Featured file promotion
- ✅ Client-facing package review flow

### Booking & Scheduling
- ✅ Appointment booking with custom form fields
- ✅ Google Calendar integration (auto event creation)
- ✅ Google Meet links added to calendar events
- ✅ Availability and blackout date management
- ✅ Email confirmations and reminders

### File Management
- ✅ Client file uploads with cloud storage support (S3/R2)
- ✅ Admin file management per client
- ✅ User file management (files not tied to a client)
- ✅ Signed URLs for secure cloud file access
- ✅ Local storage fallback

### Messaging
- ✅ Internal messaging between clients and staff
- ✅ Message history with sender/recipient tracking
- ✅ Recent message summary on admin dashboard

### Content Management
- ✅ Visual page builder (CMS)
- ✅ Custom navbar and footer configuration
- ✅ Company color and branding customization
- ✅ Lead capture forms with form-to-lead conversion
- ✅ Image gallery/library management
- ✅ Dynamic page routing with catch-all slug support
- ✅ CMS-driven homepage override

### Blog System
- ✅ WYSIWYG editor (TinyMCE)
- ✅ Content blocks (text, images, video, code, callouts, gallery)
- ✅ Categories, tags, and comments
- ✅ SEO optimization per post
- ✅ RSS feed generation
- ✅ WordPress post import
- ✅ AI-powered content generation

### AI Features
- ✅ Claude AI integration (Anthropic) with per-tenant API key management
- ✅ Generate blog posts from prompts
- ✅ Auto-generate SEO metadata (title, description, keywords)
- ✅ Content improvement suggestions
- ✅ SEO Assistant — client-facing AI tool for SEO analysis
- ✅ Cyber Audit — client-facing AI assistant for security insights
- ✅ AI usage statistics tracking

### Billing & Payments
- ✅ Stripe payment processing
- ✅ Invoice generation and management
- ✅ Invoice line items
- ✅ Simple payment collection page (`/pay`)
- ✅ Stripe webhook handling
- ✅ Refund capabilities

### Inspection Reports
- ✅ Create and manage inspection reports
- ✅ Send reports to clients
- ✅ Google Drive integration for report storage
- ✅ Client-facing report view

### Authentication & Security
- ✅ Email/password registration and login with email verification
- ✅ Google OAuth (sign in and sign up)
- ✅ Password reset via email
- ✅ Magic link SSO (admin-generated passwordless access for clients)
- ✅ Role-based access control (`company_administrator`, `client`, `user`)
- ✅ Role-based redirects on login
- ✅ CSRF state verification on OAuth callback
- ✅ Unified guest layout across all auth views

### Admin Dashboard & Settings
- ✅ Comprehensive dashboard with widgets
- ✅ Business settings (company info, branding, theme)
- ✅ User management (create, edit, delete, password reset, email verification)
- ✅ Feature flag management via environment config
- ✅ Google OAuth credential management
- ✅ Setup wizard for new instances
- ✅ Activity logs (audit trail)
- ✅ Email delivery logs and SMTP testing
- ✅ Search across entities

### Developer Experience
- ✅ Livewire 3 for reactive components
- ✅ Tailwind CSS 3 with dark mode support
- ✅ Alpine.js for lightweight interactivity
- ✅ Pest PHP for testing
- ✅ Laravel Pint for code formatting
- ✅ Multi-OS development setup (Windows/Mac/Linux)

---

## 🏗️ Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Framework** | Laravel | 12 |
| **PHP** | PHP | 8.4+ |
| **Frontend** | Tailwind CSS, Alpine.js | 3.x, 3.x |
| **Components** | Livewire | 3 |
| **Database** | SQLite/MySQL/PostgreSQL | Latest |
| **Testing** | Pest PHP | 3 |
| **Build** | Vite | Latest |
| **Package Manager** | Composer, npm | Latest |
| **AI** | Anthropic Claude | Latest |
| **Payments** | Stripe | Latest |

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.4+
- Composer
- Node.js 24+
- Git
- SQLite or MySQL/PostgreSQL

### 1. Clone Repository
```bash
git clone https://github.com/alexramsey92/smbgen.git
cd smbgen
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
# Set COMPANY_NAME, APP_NAME, and APP_URL in .env
# e.g. APP_URL=http://smbgen.test for Herd
```

### 4. Configure Database
```bash
# For SQLite (default):
touch database/database.sqlite

# For MySQL, edit .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=smbgen
# DB_USERNAME=root
# DB_PASSWORD=secret
```

### 5. Run Migrations and Seed Users
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
# Creates a demo user and an admin user — note the credentials
```

### 6. Build Assets
```bash
npm run build
# or for development with file watching:
npm run dev
```

### 7. Start Development Server

**Option A: Laravel Herd (Windows, Mac) — recommended**
```bash
# Herd auto-serves at your configured .test domain
# e.g. https://smbgen.test
```

**Option B: Laravel's Built-in Server**
```bash
php artisan serve
# Visit: http://localhost:8000
```

---

## ⚙️ Configuration

### Feature Flags
Enable/disable features in `.env`:

```env
FEATURE_BOOKING=true              # Appointment booking
FEATURE_BILLING=false             # Stripe billing
FEATURE_MESSAGES=true             # Internal messaging
FEATURE_CMS=true                  # Content management
FEATURE_BLOG=true                 # Blog system
FEATURE_FILE_MANAGEMENT=true      # File uploads and storage
FEATURE_INSPECTION_REPORTS=false  # Inspection report module
```

See [FEATURE_FLAGS.md](app/docs/FEATURE_FLAGS.md) for details.

### API Keys (Optional)
```env
# Google Calendar & OAuth
GOOGLE_CLIENT_ID=xxx
GOOGLE_CLIENT_SECRET=xxx
GOOGLE_REDIRECT_URI=https://yourdomain.test/auth/google/callback

# Anthropic Claude (for AI features)
ANTHROPIC_API_KEY=sk-ant-xxx

# Stripe (for billing)
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Cloud Storage (optional — falls back to local)
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_DEFAULT_REGION=auto
AWS_BUCKET=xxx
AWS_ENDPOINT=https://xxx.r2.cloudflarestorage.com
```

See [ENV_EXAMPLE.md](app/docs/ENV_EXAMPLE.md) for all environment variables.

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run with filter
php artisan test --filter=ClientTest

# Format code before committing
vendor/bin/pint
```

---

## 📚 Documentation

Full documentation available in `app/docs/`:

**Getting Started**
- [Developer Setup Guide](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md) — Windows/Mac/Linux setup
- [Feature Flags](app/docs/FEATURE_FLAGS.md) — Enable/disable features
- [Environment Variables](app/docs/ENV_EXAMPLE.md) — All `.env` settings

**Features**
- [Booking System](app/docs/BOOKING_CUSTOM_FIELDS_IMPLEMENTATION.md)
- [Blog System](app/docs/BLOG_IMPLEMENTATION_SUMMARY.md)
- [AI Features](app/docs/AI_IMPLEMENTATION_COMPLETE.md)
- [Stripe Billing](app/docs/STRIPE_SUBSCRIPTION_SETUP.md)
- [Google Calendar](app/docs/GOOGLE_CALENDAR_DEBUG_QUICKREF.md)

**Infrastructure**
- [Domain Setup](app/docs/DOMAIN_CONNECTION_GUIDE.md)
- [Admin Features](app/docs/SUPER_ADMIN_SETUP.md)
- [Theme System](app/docs/THEME_SYSTEM.md)

---

## 🔧 Common Commands

```bash
# Development
npm run dev                          # Watch frontend files
php artisan serve                    # Start server (alternative to Herd)
php artisan migrate                  # Run migrations
php artisan tinker                   # Interactive PHP shell

# Code Quality
vendor/bin/pint                      # Format code
php artisan test                     # Run tests

# Database
php artisan migrate:refresh --seed   # Reset and seed database
php artisan make:migration name      # Create migration
php artisan make:model Name -m       # Create model with migration

# Cache
php artisan optimize:clear           # Clear all caches
php artisan config:cache             # Cache configuration (production)
```

---

## 📂 Project Structure

```
app/
├── Console/Commands/          # Artisan commands
├── Http/Controllers/          # Request controllers
│   └── Admin/                 # Admin-only controllers
├── Http/Requests/             # Form validation
├── Models/                    # Eloquent models
├── Services/                  # Business logic (AI, Google, Stripe, etc.)
├── Jobs/                      # Background jobs
├── Mail/                      # Email classes
├── Listeners/                 # Event listeners
├── Policies/                  # Authorization policies
└── docs/                      # Project documentation

resources/
├── views/                     # Blade templates
│   ├── admin/                 # Admin dashboard
│   ├── client/                # Client portal
│   ├── cms/                   # CMS pages
│   ├── blog/                  # Blog pages
│   └── emails/                # Email templates
├── css/                       # Tailwind CSS
└── js/                        # Alpine.js and utilities

tests/
├── Feature/                   # Integration tests
└── Unit/                      # Unit tests

database/
├── migrations/                # Schema changes
├── factories/                 # Model factories
└── seeders/                   # Database seeders

routes/
├── web.php                    # Web routes
└── auth.php                   # Auth routes
```

---

## 🚀 Deployment

### Local Development
- **Windows:** Laravel Herd (recommended — auto-installs PHP, Composer, and serves `.test` domains)
- **Mac:** Laravel Herd or Valet
- **Linux:** PHP + Composer + Node.js manually

### Production
Deployment scripts available in `deployment/` folder for VPS setups.

```bash
bash deployment/vps-deploy.sh
```

See [Laravel Boost](https://laravel.com/docs/12.x/boost) for cloud deployment options and `deployment/README.md` for VPS details.

---

## 🤝 Contributing

1. Create a feature branch from `main`
2. Make your changes
3. Run tests: `php artisan test`
4. Format code: `vendor/bin/pint`
5. Commit with clear messages
6. Push and open a pull request

See [DEVELOPER_CONTRIBUTOR_START_HERE.md](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md) for full setup instructions.

---

## 📞 Support & Questions

- 📖 [Documentation index](app/docs/README.md)
- 🐛 Report issues on GitHub
- 💡 Feature requests welcome in discussions
- 🚀 See [REMAINING_ISSUES.md](app/docs/REMAINING_ISSUES.md) for known limitations

---

## 📄 License

Licensed under the MIT License. See [LICENSE](LICENSE) file.

---

**Built with ❤️ by [Alexander Ramsey](https://alexanderramsey.com/)**

*A modern platform for service professionals to manage clients, bookings, content, and payments all in one place.*
