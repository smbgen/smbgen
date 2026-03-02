# ClientBridge Documentation

Welcome to the ClientBridge documentation. Start here based on your role:

## 👤 New Developer?
**Start with:** [DEVELOPER_CONTRIBUTOR_START_HERE.md](DEVELOPER_CONTRIBUTOR_START_HERE.md)

Sets up your development environment for Windows/Mac/Linux and explains the tech stack.

---

## 📚 Documentation by Topic

### Getting Started
- [Developer Setup](DEVELOPER_CONTRIBUTOR_START_HERE.md) - Local environment setup
- [Feature Flags](FEATURE_FLAGS.md) - Enable/disable features
- [Environment Reference](ENV_EXAMPLE.md) - All `.env` variables explained

### Core Features

**Booking & Appointments**
- [Custom Fields](BOOKING_CUSTOM_FIELDS_IMPLEMENTATION.md) - Add custom form fields

**Content Management**
- [CMS Colors](CMS_COMPANY_COLORS.md) - Brand customization

**Blog System**
- [Blog Implementation](BLOG_IMPLEMENTATION_SUMMARY.md) - Current blog features

**Google Calendar Integration**
- [Debug Quickref](GOOGLE_CALENDAR_DEBUG_QUICKREF.md) - Troubleshooting calendar issues
- [Full Debug Guide](GOOGLE_CALENDAR_DEBUG_GUIDE.md) - Detailed debugging

**Billing & Subscriptions**
- [Stripe Setup](STRIPE_SUBSCRIPTION_SETUP.md) - Configure Stripe payments
- [Stripe Enhancements](STRIPE_ENHANCEMENTS.md) - Refunds, customer management
- [Subscription Implementation](SUBSCRIPTION_IMPLEMENTATION_SUMMARY.md) - Trial logic, plan management

**AI Features**
- [AI Implementation](AI_IMPLEMENTATION_COMPLETE.md) - Claude AI integration

### Multi-Tenancy
- [Setup Guide](MULTI_TENANT_SETUP.md) - Getting started with multi-tenancy
- [Implementation Status](MULTI_TENANCY_IMPLEMENTATION_STATUS.md) - What's been built
- [Domain Connection](DOMAIN_CONNECTION_GUIDE.md) - Custom domains and subdomains

### Administration
- [Super Admin Setup](SUPER_ADMIN_SETUP.md) - Admin panel features
- [Theme System](THEME_SYSTEM.md) - Theming and customization

### Known Issues & Planning
- [Remaining Issues](REMAINING_ISSUES.md) - Known bugs and limitations
- [Header Concerns](HEADER_CONCERNS_IMPLEMENTATION.md) - Header/navigation implementation notes

---

## 🔧 Quick Reference

### Common Tasks

**Enable a feature:**
```env
FEATURE_BLOG=true
FEATURE_BOOKING=true
```
Then: `php artisan optimize:clear`

**Start development:**
```bash
npm run dev     # Watch frontend
php artisan serve   # Run backend
```

**Run tests:**
```bash
php artisan test
```

**Access admin:**
Visit `/admin` after logging in

---

## 📖 What's in This Repository

**ClientBridge** is a multi-tenant SaaS platform for:
- Appointment booking with Google Calendar sync
- Content management (CMS)
- Blog publishing
- Lead capture via forms
- AI-powered content generation
- Subscription billing with Stripe
- File management
- Internal messaging

Built with **Laravel 12**, **Livewire 3**, **Tailwind CSS 3**, and **Alpine.js**.

---

## 🚀 Deployment

This app runs on:
- **Local:** Laravel Herd (Windows) or Valet (Mac)
- **Production:** Laravel Cloud or self-managed VPS

Deployments are typically manual (git push → automated tests).

---

## ❓ Need Help?

1. **Setup issues?** → [DEVELOPER_CONTRIBUTOR_START_HERE.md](DEVELOPER_CONTRIBUTOR_START_HERE.md)
2. **How do I...?** → Search this folder for relevant guide
3. **API question?** → Check routes with `php artisan route:list`
4. **Database question?** → Check migrations in `database/migrations/`

---

**Last updated:** January 5, 2026  
**Maintained by:** ClientBridge Team
