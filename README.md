<div align="center">

```
                                                __              
                                    ___ __ _  / /  ___ ____ ___ 
                                    (_-</  ' \/ _ \/ _ `/ -_) _ \
                                    /___/_/_/_/_.__/\_, /\__/_//_/
                                                /___/          
```                                                                                       

**Web Presence That Converts**

One connected platform where your leads come in, appointments get booked, payments are collected, and client relationships are managed — without juggling six different tools.

[![Built for SMB](https://img.shields.io/badge/Built%20For-Small%20%26%20Mid--Market-indigo?style=for-the-badge)](https://smbgen.app)
[![No Vendor Lock-in](https://img.shields.io/badge/No-Vendor%20Lock--In-emerald?style=for-the-badge)](LICENSE)
[![Open Source](https://img.shields.io/badge/Open-Source-blue?style=for-the-badge)](https://github.com/alexramsey92/smbgen)
[![Production Ready](https://img.shields.io/badge/Production-Ready-green?style=for-the-badge)](#deployment)

[🎯 Learn More](https://smbgen.app) • [📖 Docs](app/docs/README.md) • [🚀 Get Started](#-quick-start) • [🤝 Contribute](#-contributing)

</div>

---

## The Problem

Small and mid-market businesses run on a patchwork of tools that don't talk to each other.

- A contact form here
- A calendar there  
- An invoice in email
- Files scattered across Google Drive

**Every gap between tools is a place where leads leak, clients get frustrated, and time gets wasted.**

smbgen closes every gap. From the moment a prospect fills out your contact form to the moment they refer their first colleague, every interaction runs through the same connected system.

---

## The Solution: One Connected Platform

### The Customer Journey
```
Lead → Nurture → Propose → Close → Pay → Deliver → Retain → Refer
```

**Every step, one platform.**

✅ **Every lead captured, qualified, and routed into your CRM automatically**  
✅ **Bookings live in your calendar — no manual entry, no back-and-forth**  
✅ **Payments collected the moment work is delivered — not 30 days later**  
✅ **Clients have one private portal: files, messages, invoices, history**  
✅ **Everything syncs automatically — nothing needs manual intervention**  

---

## What You Get

### Six Core Tools (All Connected)

| Tool | What It Does | Why It Matters |
|------|-------------|-----------------|
| **Contact** | Lead capture with structured intake | Qualify leads faster, route smarter |
| **Book** | Appointment scheduling with confirmations | No back-and-forth, no double-booking |
| **Pay** | Payment collection & invoicing | Get paid now, not 30 days later |
| **Client Portal** | Private space for files, messages, billing | Clients stay in your system, not your email |
| **CRM** | Lead tracking & relationship management | Nothing falls through the cracks |
| **CMS** | Website content management | Update pages without calling a developer |

### Built for Your Industry

smbgen isn't generic. Workflows, intake forms, and defaults are customized for your specific business:

- **Real Estate**: Showing scheduling, property intake, buyer pre-qualification
- **Home Services**: Service booking, address capture, dispatch routing  
- **Legal**: Consultation booking, confidential intake, secure document portal
- **Health & Wellness**: Health history, appointment types, service-based billing
- **Agencies & Consulting**: Project tracking, proposal workflows, retainer billing

👉 [Browse all industries →](https://smbgen.app/industries)

---

## Why smbgen?

| Consideration | smbgen | Cobbled Tools | Other Platforms |
|---|---|---|---|
| **All-in-One** | ✅ One codebase | ❌ Six subscriptions | ✅ But locked in |
| **Customizable** | ✅ Full source code | ✅ Yes | ❌ Limited |
| **Industry-Specific** | ✅ Built in | ❌ Generic | ❌ Generic |
| **Affordable** | ✅ Self-hosted | ✅ But fragmented | ✅ High SaaS fees |
| **Time to Revenue** | ✅ Days | ❌ 3-6 months | ✅ Quick (limited) |
| **Vendor Lock-in** | ✅ None | ✅ None | ❌ You're stuck |

---

## 🚀 Quick Start (5 Minutes)

### For Builders & Developers

> Want to customize this for your business? [Full setup guide →](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md)

### Prerequisites
```bash
✓ PHP 8.4+  ✓ Composer  ✓ Node.js 24+  ✓ Git  ✓ SQLite or MySQL
```

### Step 1: Clone & Install
```bash
git clone https://github.com/alexramsey92/smbgen.git && cd smbgen
composer install && npm install
```

### Step 2: Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### Step 3: Database Setup
```bash
# SQLite (default, no setup needed)
touch database/database.sqlite

# Or MySQL — edit .env with your credentials
```

### Step 4: Migrate & Seed
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
```

### Step 5: Run
```bash
npm run build              # Production build
npm run dev               # Or watch mode

php artisan serve        # Local server
# OR
# Use Laravel Herd — auto-serves at https://smbgen.test
```

✅ **Live at:** `http://localhost:8000` or `https://smbgen.test`

---

## ⚙️ Configure for Your Business

### Enable/Disable Features
```env
FEATURE_BOOKING=true              # Appointment booking
FEATURE_BILLING=true              # Stripe & invoices  
FEATURE_MESSAGES=true             # Internal messaging
FEATURE_CMS=true                  # Page builder
FEATURE_BLOG=true                 # Blog system
FEATURE_FILE_MANAGEMENT=true      # File uploads
FEATURE_INSPECTION_REPORTS=false  # Inspection module
```

### Connect External Services
```env
# Google Calendar & OAuth (for bookings & sign-in)
GOOGLE_CLIENT_ID=xxx
GOOGLE_CLIENT_SECRET=xxx

# Anthropic Claude AI (for content generation)  
ANTHROPIC_API_KEY=sk-ant-xxx

# Stripe (for payments)
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx

# Resend (for transactional emails)
RESEND_API_KEY=re_xxx

# Cloud Storage (S3 or Cloudflare R2)
AWS_BUCKET=your-bucket
AWS_ENDPOINT=https://xxx.r2.cloudflarestorage.com
```

👉 [All environment variables →](app/docs/ENV_EXAMPLE.md)

---

## 🧪 Built to Last

Production-ready means comprehensive test coverage. Every feature is tested, every interaction verified.

```bash
# Run all tests
php artisan test

# Test a specific area
php artisan test tests/Feature/BookingTest.php

# Run with code coverage report
php artisan test --coverage
```

**Quality Standards**
- ✅ Pest PHP 3 for clean, modern tests
- ✅ Feature & unit test separation
- ✅ HTTP & Livewire component testing
- ✅ Database seeding for test isolation

---

## 🔧 Common Commands

```bash
# 🚀 Development
npm run dev                    # Watch frontend files & reload
php artisan serve             # Start local server
php artisan tinker            # Interactive PHP REPL

# 🧹 Code Quality  
vendor/bin/pint               # Format code with Pint
php artisan test              # Run all tests

# 🗄️ Database
php artisan migrate           # Run pending migrations
php artisan migrate:fresh --seed  # Reset & seed DB
php artisan make:migration name   # Create new migration

# 🔄 Cache & Clear
php artisan optimize:clear    # Clear all caches
php artisan config:cache      # Cache config (production)

# 🔐 Security
php artisan key:generate      # Generate app key
php artisan jwt:secret        # Generate JWT key (if using)
```

---

## 📂 Project Structure (Organized & Scalable)

```
app/
  ├── Console/Commands/       # Custom Artisan commands
  ├── Http/
  │   ├── Controllers/        # Request handlers (+ Admin/ subfolder)
  │   ├── Requests/           # Form validation & rules
  │   └── Middleware/         # Request middleware
  ├── Models/                 # Eloquent models with relations
  ├── Services/               # Business logic (AI, Stripe, Google, etc.)
  ├── Jobs/                   # Queued jobs & background tasks
  ├── Mail/                   # Email classes & templates
  ├── Listeners/              # Event listeners
  ├── Policies/               # Authorization rules
  └── docs/                   # Full internal documentation

resources/
  ├── views/
  │   ├── admin/              # Admin dashboard & tools
  │   ├── client/             # Client portal pages
  │   ├── cms/                # CMS-generated pages
  │   ├── blog/               # Blog pages & posts
  │   ├── components/         # Reusable Blade components
  │   └── emails/             # Email templates
  ├── css/                    # Tailwind CSS configuration
  └── js/                     # Alpine.js utilities

tests/
  ├── Feature/                # Integration & HTTP tests
  └── Unit/                   # Unit tests

database/
  ├── migrations/             # Schema changes
  ├── factories/              # Model factories for tests
  └── seeders/                # Database seeders

routes/
  ├── web.php                 # Public & authenticated routes
  └── auth.php                # Authentication routes
```

---

## 📚 Documentation (Comprehensive Guides)

All documentation lives in [`app/docs/`](app/docs/) — organized by topic:

**Getting Started**
- [Developer Setup](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md) — Windows/Mac/Linux
- [Feature Flags](app/docs/FEATURE_FLAGS.md) — Enable/disable features
- [Environment Variables](app/docs/ENV_EXAMPLE.md) — All `.env` settings

**Building Blocks**
- [Booking System](app/docs/BOOKING_CUSTOM_FIELDS_IMPLEMENTATION.md)
- [Blog Engine](app/docs/BLOG_IMPLEMENTATION_SUMMARY.md)
- [AI Features](app/docs/AI_IMPLEMENTATION_COMPLETE.md)
- [Stripe Billing](app/docs/STRIPE_SUBSCRIPTION_SETUP.md)
- [Google Calendar](app/docs/GOOGLE_CALENDAR_DEBUG_QUICKREF.md)

**Operations**
- [Domain Setup](app/docs/DOMAIN_CONNECTION_GUIDE.md)
- [Admin Features](app/docs/SUPER_ADMIN_SETUP.md)
- [Theme System](app/docs/THEME_SYSTEM.md)

---

## 🚀 Deploy Your Platform

smbgen runs anywhere: your laptop, a shared server, a VPS, or the cloud.

### Development (Local)
- **Windows** → [Laravel Herd](https://herd.laravel.com) (free, instant setup)
- **Mac** → Laravel Herd or [Valet](https://laravel.com/docs/12.x/valet)
- **Linux** → PHP + Composer + Node.js

### Go Live (Production)
- Shared hosting with SSH access
- VPS (DigitalOcean, Linode, AWS, etc.)
- Platform-as-a-Service (Forge, Vapor, Ploi)
- Docker and Kubernetes ready

**Quick VPS Deploy:**
```bash
bash deployment/vps-deploy.sh
```

👉 [Full deployment guide →](deployment/README.md)

---

## 🤝 Contributing

We welcome contributions! Before you get started, please read our community guidelines:

- 📘 [GitHub contribution guide](.github/CONTRIBUTING.md)
- 📘 [Code of Conduct](.github/CODE_OF_CONDUCT.md)
- 🔒 [Security Policy](.github/SECURITY.md)

Then follow these steps:

1. **Fork the repo** and create a feature branch
   ```bash
   git checkout -b feature/my-awesome-feature
   ```

2. **Make your changes**
   ```bash
   npm run dev                # Watch assets
   php artisan serve          # Run local server
   ```

3. **Test & format**
   ```bash
   php artisan test           # Run all tests
   vendor/bin/pint            # Format code
   ```

4. **Commit & push**
   ```bash
   git commit -m "feat: add amazing feature"
   git push origin feature/my-awesome-feature
   ```

5. **Open a pull request** to `main`

👉 [Full contribution guide →](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md)

---

## 📞 Support & Issues

- 📖 [Full Documentation](app/docs/README.md)
- 💬 [Questions & Discussions](https://github.com/alexramsey92/smbgen/discussions)
- 🐛 [Report Issues](https://github.com/alexramsey92/smbgen/issues)
- ⚠️ [Known Limitations](app/docs/REMAINING_ISSUES.md)

---

## 📜 License

Licensed under the **Business Source License 1.1 (BSL)**.
Production use is free for organizations with 10 or fewer
employees/contractors. Organizations above that threshold require a commercial
license. On 2029-04-13, this project converts to GPL-2.0-or-later.
See [LICENSE](LICENSE) for full terms.

---

<div align="center">

### No vendor lock-in. No "call for pricing." Just a platform that works for your business.

**[Get Started →](#-quick-start)** • **[Explore Docs](app/docs/README.md)** • **[Contribute](app/docs/DEVELOPER_CONTRIBUTOR_START_HERE.md)**

</div>
