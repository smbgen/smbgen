# SMBGEN – High-Level Context for Copilot & Contributors

## General Overview

- **SMBGEN** (always all caps)
- Laravel-based unified platform by Alexander Ramsey.
- Purpose: One platform for service professionals - booking system, email, files, meetings.
- Target users: Contractors, inspectors, realtors, field service professionals.
- Marketing domain: `smbgen.app`

## Versioning & Roadmap

- **Current Version:** 1.0 (under development)
- Strategy:
  - Start with Tier 1 service for fast monetization.
  - Long-term: modular SaaS for clients, admins, scheduling, compliance, reporting, etc.

## Core Platform Offering

- **Unified Service Professional Platform**
  - Booking system with Google Meet auto-generation
  - Calendar intelligence with real-time availability
  - Property-aware lead capture and conversion
  - Unified client portal and dashboard
  - Built for contractors, inspectors, realtors

## Authentication & Access

- Onboarding supports **passkeys** and **Google Auth**
- Uses **Laravel Socialite** for Google integration
- Users can link a Google account to their account
- Admins can view linked social accounts per user
- Planned: IP-based access restriction
  - "Access Denied" screen with:
    - Return to login
    - Request access

## Client Portal (User-Facing Features)

- **Unified Client Portal**: Secure login portal for each client
- **Appointment Booking**: Book appointments with real-time availability
- **File Management**: Upload/download files with version control
- **In-App Messaging**: Direct communication with service professional
- **Project Status**: View project updates and completion status
- **Payment Portal**: Make payments for services through Stripe
- **Mobile Access**: Full portal access from any device
- **Activity Notifications**: Automated reminders and updates

## Admin Panel - Command Center

- **Unified Dashboard**: Everything in one place - appointments, leads, communications, files
- **Lead Management**: Capture, track, and convert leads with property details
- **Calendar Intelligence**: Real-time availability, Google Calendar sync, conflict prevention
- **Client Communication**: Template-based email, in-app messaging, file attachments
- **CRM System**: Full pipeline tracking, revenue forecasting, interaction history
- **File Management**: Secure document sharing with granular permissions
- **Payment Processing**: Stripe integration for invoicing and payments
- **Business Analytics**: Revenue tracking, lead conversion, performance metrics
- **Mobile Dashboard**: Access from any device for on-site work

## Middleware / Routing

- Laravel 12+ (`bootstrap/app.php` for middleware, not `app/Http/Kernel.php`)
- Custom middleware aliases (e.g., `companyAdministrator`)
- Routing/middleware bugs debugged/resolved (May 2025)

## Frontend

- Uses **Bootstrap** (preferred over Tailwind)
- Clean, SaaS business design
- Landing pages integrate Old Line Cyber / L7 partnership pricing & offers

## Infrastructure

- Deployed to hardened VPS/cloud environment
- Version controlled (Git)
- Integrations: Laravel, Stripe, Socialite
- Designed for scalability and security

## Marketing & Platform

- `smbgen.app` – unified platform for service professionals
- Target market: Contractors, inspectors, realtors, field service professionals
- Value proposition: One platform for booking system + email + files + meetings
- Lead capture: Property-aware forms with automatic Google Meet integration
- Professional positioning: "Built in America by service pros, for service pros"

## Investor / Growth Strategy

- SaaS + services hybrid
- Monetization: quick audits → upsell ongoing service
- Positioned as scalable, repeatable consulting platform

## Development Notes / Troubleshooting

- Debugged Socialite callback routes (Google OAuth)
- Middleware config issues (Laravel 12 structure) blocked progress, now resolved
- Auth stack (Google login) fully functional
- Admin view for linked social accounts
- Shared docs feature: client file navigation/upload, admin file management

## Style & Positioning

- **Unified platform for service professionals**
- **"Like your service body for the web"** - Everything in one place
- **Professional tools built for people who serve clients**
- **Zero-friction experience** - Google Meet auto-generation, real-time availability
- **Enterprise-grade but simple** - No learning curve, works like consumer apps
- Tone: Professional, practical, built by and for service professionals

---

**For Copilot & Contributors:**  
Use this file as your primary high-level reference.  
If you need deeper implementation details, check relevant code, documentation, or ask Alexander Ramsey.