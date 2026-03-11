# adapted from the intellectual property of Alexander Ramsey
# AlexanderRamsey.com and smbgen.com
# prtl7 is a clone of clientbridge.app/smbgen v1

A modern multi-tenant SaaS platform for service professionals. Built with **Laravel 12**, featuring appointment booking, content management, AI-powered content generation, and comprehensive admin dashboards.

**[📖 Full Documentation →](app/docs/README.md)** | **[🚀 Get Started →](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md)**

---

## ✨ Features

### Booking & Scheduling
- ✅ Appointment booking with custom form fields
- ✅ Google Calendar integration (auto event creation)
- ✅ Google Meet links added to calendar events
- ✅ Customizable booking form builder
- ✅ Email confirmations and reminders

### Content Management
- ✅ Visual page builder (CMS)
- ✅ Lead capture forms
- ✅ Form-to-lead conversion
- ✅ Dynamic page routing
- ✅ Responsive design

### Blog System
- ✅ WYSIWYG editor (TinyMCE)
- ✅ Content blocks (text, images, video, code, callouts, gallery)
- ✅ Comments system
- ✅ SEO optimization
- ✅ AI-powered content generation

### AI Features
- ✅ Claude AI integration (Anthropic)
- ✅ Generate blog posts from prompts
- ✅ Auto-generate SEO metadata (title, description, keywords)
- ✅ Content improvement suggestions
- ✅ Multiple content type templates
- ✅ Tenant-specific API key management

### Billing & Subscriptions
- ✅ Stripe payment processing
- ✅ Subscription management with trial periods
- ✅ Plan upgrades/downgrades
- ✅ Customer billing portal
- ✅ Invoice tracking
- ✅ Refund capabilities

### Multi-Tenancy
- ✅ Complete tenant isolation (database, files, settings)
- ✅ Custom domain & subdomain support
- ✅ Domain management UI (add/remove/set primary)
- ✅ Per-tenant configuration
- ✅ Tenant admin impersonation for support

### Admin Dashboard
- ✅ Comprehensive dashboard with widgets
- ✅ User and tenant management
- ✅ Super admin controls
- ✅ Feature flag management
- ✅ Theme customization

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
| **PHP** | PHP | 8.4.15 |
| **Frontend** | Tailwind CSS, Alpine.js | 3.x, 3.x |
| **Components** | Livewire | 3 |
| **Database** | SQLite/MySQL/PostgreSQL | Latest |
| **Testing** | Pest PHP | 3 |
| **Build** | Vite | Latest |
| **Package Manager** | Composer, npm | Latest |
| **Tenancy** | Stancl/Tenancy | 3.x |
| **AI** | Anthropic Claude | Latest |
| **Payments** | Stripe | Latest |

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.4+
- Composer
- Node.js 24.5+ (for Windows npm compatibility)
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
# set company name and app name inside the env
# set app url to reflect herd for local testing eg APP_URL=http://prtl7-app.test
```

### 4. Configure Database
```bash
# For SQLite (default):
touch database/database.sqlite

# For MySQL, edit .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=smbgen.com
# DB_USERNAME=root
# DB_PASSWORD=secret
```

### 5. Run Migrations and add users
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
# note these creds
# creates demo and admin user
# use password mgr locally with local url
```

### 6. Build Assets
```bash
npm run build
# or for development with watch:
npm run dev
```

### 7. Start Development Server

**Option A: Using Laravel Herd (Windows, Mac)**
```bash
# Herd auto-serves at: https://smbgen.com.test
# Just open it in your browser
```

**Option B: Using Laravel's Built-in Server**
```bash
php artisan serve
# Visit: http://localhost:8000
```

---

## ⚙️ Configuration

### Feature Flags
Enable/disable features in `.env`:

```env
FEATURE_BOOKING=true        # Appointment booking
FEATURE_BILLING=false       # Stripe billing
FEATURE_MESSAGES=true       # Internal messaging
FEATURE_CMS=true            # Content management
FEATURE_BLOG=true           # Blog system
```

See [FEATURE_FLAGS.md](app/docs/FEATURE_FLAGS.md) for details.

### API Keys (Optional)
```env
# Google Calendar Integration
GOOGLE_CLIENT_ID=xxx
GOOGLE_CLIENT_SECRET=xxx
GOOGLE_REDIRECT_URI=https://yourdomain.test/auth/google/callback

# Anthropic Claude (for AI features)
ANTHROPIC_API_KEY=sk-ant-xxx

# Stripe (for billing)
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

See [ENV_EXAMPLE.md](app/docs/ENV_EXAMPLE.md) for all environment variables.

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run with coverage
php artisan test --coverage

# Format code before committing
vendor/bin/pint
```

---

## 📚 Documentation

Full documentation available in `app/docs/`:

**Getting Started**
- [Developer Setup Guide](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md) - Windows/Mac/Linux setup
- [Feature Flags](app/docs/FEATURE_FLAGS.md) - Enable/disable features
- [Environment Variables](app/docs/ENV_EXAMPLE.md) - All `.env` settings

**Features**
- [Booking System](app/docs/BOOKING_CUSTOM_FIELDS_IMPLEMENTATION.md)
- [Blog System](app/docs/BLOG_IMPLEMENTATION_SUMMARY.md)
- [AI Features](app/docs/AI_IMPLEMENTATION_COMPLETE.md)
- [Stripe Billing](app/docs/STRIPE_SUBSCRIPTION_SETUP.md)
- [Google Calendar](app/docs/GOOGLE_CALENDAR_DEBUG_QUICKREF.md)

**Infrastructure**
- [Multi-Tenancy](app/docs/MULTI_TENANT_SETUP.md)
- [Domain Setup](app/docs/DOMAIN_CONNECTION_GUIDE.md)
- [Admin Features](app/docs/SUPER_ADMIN_SETUP.md)
- [Theme System](app/docs/THEME_SYSTEM.md)

---

## 🔧 Common Commands

```bash
# Development
npm run dev              # Watch frontend files
php artisan serve       # Start server (alternative to Herd)
php artisan migrate     # Run migrations
php artisan tinker      # Interactive PHP shell

# Code Quality
vendor/bin/pint         # Format code
php artisan test        # Run tests

# Database
php artisan migrate:refresh --seed    # Reset and seed database
php artisan make:migration name       # Create migration
php artisan make:model Name -m        # Create model with migration

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
├── Http/Requests/             # Form validation
├── Models/                    # Eloquent models
├── Services/                  # Business logic
├── Jobs/                      # Background jobs
├── Mail/                      # Email classes
├── Listeners/                 # Event listeners
├── Policies/                  # Authorization policies
└── docs/                      # Project documentation

resources/
├── views/                     # Blade templates
│   ├── admin/                 # Admin dashboard
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
├── api.php                    # API routes (if used)
└── console.php                # Artisan commands
```

---

## 🚀 Deployment

### Local Development
- **Windows:** Laravel Herd (recommended, auto-installs everything)
- **Mac:** Laravel Valet or Herd
- **Linux:** PHP + Composer + Node.js manually

### Laravel Boost https://laravel.com/docs/12.x/boost
Follow instructions to install Laravel boost ^

### Production
Deployment scripts available in `deployment/` folder for VPS setups.

```bash
# Quick deploy
bash deployment/vps-deploy.sh
```

For detailed instructions, see `deployment/README.md`.

---

## 🤝 Contributing

1. Create a feature branch from current branch
2. Make your changes
3. Run tests: `php artisan test`
4. Format code: `vendor/bin/pint`
5. Commit with clear messages
6. Push and create a pull request

**Important:** Always test locally before submitting PRs. Check [DEVELOPER_CONTRIBUTOR_START_HERE.md](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md) for setup help.

---

## 📋 Current Branch Status

**Branch:** `feature/subscription-trial-domain-management`

**Latest Updates:**
- ✅ Multi-tenant domain management UI enhanced
- ✅ AI settings controller with comprehensive error handling
- ✅ PostgreSQL BusinessSetting type casting fixed
- ✅ Environment naming simplified (COMPANY_NAME as primary)
- ✅ Documentation reorganized and cleaned up
- ✅ Redundant guides consolidated
- ✅ Developer onboarding guide created
- ✅ Feature flags verified and documented

**Known Status:**
- Feature flags: FEATURE_BOOKING, FEATURE_BILLING, FEATURE_MESSAGES, FEATURE_CMS, FEATURE_BLOG
- Multi-tenancy: Fully functional with domain management
- AI: Claude integration active per-tenant
- Billing: Stripe integration with trial support
- Development: Windows/Mac/Linux setup guides included

---

## 📞 Support & Questions

- 📖 Check [app/docs/README.md](app/docs/README.md) for documentation index
- 🐛 Report issues on GitHub
- 💡 Feature requests welcome in discussions
- 🚀 See [REMAINING_ISSUES.md](app/docs/REMAINING_ISSUES.md) for known limitations

---

## 📄 License

Licensed under the MIT License. See [LICENSE](LICENSE) file.

---

**Built with ❤️ by [Alexander Ramsey](https://alexanderramsey.com/)**

*A modern platform for service professionals to manage bookings, content, and customer relationships all in one place.*
