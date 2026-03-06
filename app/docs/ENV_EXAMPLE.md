# Environment Configuration Guide

## Overview

This document explains environment variables and feature flags for smbgen. See `.env.example` for a complete template.

---

## Core Application Settings

**APP_NAME** - Application name used in browser titles, emails, and notifications (e.g., "RTS Environmental")

**APP_KEY** - Laravel encryption key (auto-generated via `php artisan key:generate`)

**APP_ENV** - Environment mode: `local`, `staging`, `production`

**APP_DEBUG** - Debug mode: `true` (local) or `false` (production)

**APP_URL** - Base application URL (e.g., `https://yourdomain.com`)

---

## Company Branding

**COMPANY_NAME** - Your company/business name. Used throughout the application including login screen, admin panel navbar, emails, and footers. This is the primary name setting.

**BUSINESS_NAME** (optional) - Only set this if you want a different name for public-facing CMS pages and navbar. If not set, it will use `COMPANY_NAME`.

**BUSINESS_TAGLINE** - Short tagline/slogan (e.g., "Mold & Asbestos Inspector")

**BUSINESS_DESCRIPTION** - Brief company description for SEO and marketing

**BUSINESS_EMAIL** - Primary business contact email

**BUSINESS_PHONE** - Primary business phone number

**BUSINESS_WEBSITE** - Company website URL

---

## Database

**DB_CONNECTION** - Database driver: `sqlite` (default) or `mysql`

**DB_DATABASE** - Database path for SQLite: `./database/database.sqlite`

For MySQL:
- **DB_HOST** - Database host (default: `127.0.0.1`)
- **DB_PORT** - Database port (default: `3306`)
- **DB_USERNAME** - Database username
- **DB_PASSWORD** - Database password

---

## Email Configuration

**MAIL_MAILER** - Mail driver: `smtp`

**MAIL_HOST** - SMTP server hostname

**MAIL_PORT** - SMTP port (typically `587` for TLS, `465` for SSL)

**MAIL_USERNAME** - SMTP username

**MAIL_PASSWORD** - SMTP password

**MAIL_ENCRYPTION** - Encryption method: `tls` or `ssl`

**MAIL_FROM_ADDRESS** - Default sender email address

**MAIL_FROM_NAME** - Default sender name

---

## Feature Flags

Feature flags control which features are enabled. Set to `true` or `false`.

**FEATURE_APPOINTMENTS** - Booking and scheduling system (default: `true`)

**FEATURE_EMAIL_COMPOSER** - Email composer with tracking (default: `true`)

**FEATURE_CMS** - Content management system (default: `true`)

**FEATURE_BLOG** - Blog module with post editor and AI content tools (default: `true`)

**FEATURE_MESSAGES** - Internal messaging between clients and staff (default: `true`)

**FEATURE_SOCIAL_ACCOUNTS** - Social account management (default: `true`)

---

## Google OAuth & Calendar Integration

**GOOGLE_CLIENT_ID** - OAuth 2.0 Client ID from Google Cloud Console

**GOOGLE_CLIENT_SECRET** - OAuth 2.0 Client Secret from Google Cloud Console

**GOOGLE_REDIRECT_URI** - OAuth callback URL (e.g., `https://yourdomain.com/auth/google/callback`)

### Setup Instructions

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project or select existing project
3. Enable Google Calendar API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback` (local development)
   - `https://yourdomain.com/auth/google/callback` (production)
   - `https://yourdomain.com/admin/calendar/callback` (calendar connection)

---

## OpenAI Integration (Optional)

**OPENAI_API_KEY** - OpenAI API key for AI features (if enabled)

Get your API key from [OpenAI Platform](https://platform.openai.com)

## AI Content Generation (Anthropic Claude)

**AI_CONTENT_GENERATION_ENABLED** — Master switch for AI content features (default: `false`).

**AI_PROVIDER** — AI provider slug. Currently only `anthropic` is supported (default: `anthropic`).

**ANTHROPIC_API_KEY** — Claude API key. Required when AI is enabled. Can also be set in Admin → AI Settings; the database-stored key takes precedence over `.env`.

**ANTHROPIC_MODEL** — Claude model to use (default: `claude-opus-4-1`).

**ANTHROPIC_MAX_TOKENS** — Max tokens per request (default: `4096`).

**ANTHROPIC_TEMPERATURE** — Creativity level 0–1 (default: `0.7`). Lower = more deterministic.

**AI_RATE_LIMIT_ENABLED** — Toggle rate limiting for AI requests (default: `true`).

**AI_MAX_REQUESTS_PER_HOUR** — Hourly request cap (default: `60`).

**AI_MAX_REQUESTS_PER_DAY** — Daily request cap (default: `200`).

**AI_LOGGING_ENABLED** — Log prompts/responses/tokens for auditing (default: `true`).

---

## Environment Examples

### Local Development
```env
APP_NAME="RTS Environmental"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

COMPANY_NAME="RTS Environmental"
BUSINESS_TAGLINE="Mold & Asbestos Inspector"
BUSINESS_EMAIL=info@example.com

DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

FEATURE_APPOINTMENTS=true
FEATURE_EMAIL_COMPOSER=true
FEATURE_CMS=true
FEATURE_BLOG=true
FEATURE_MESSAGES=true
FEATURE_INSPECTION_REPORTS=true
FEATURE_BILLING=true

AI_CONTENT_GENERATION_ENABLED=false
AI_PROVIDER=anthropic
ANTHROPIC_API_KEY=
ANTHROPIC_MODEL=claude-opus-4-1
ANTHROPIC_MAX_TOKENS=4096
ANTHROPIC_TEMPERATURE=0.7
AI_RATE_LIMIT_ENABLED=true
AI_MAX_REQUESTS_PER_HOUR=60
AI_MAX_REQUESTS_PER_DAY=200
AI_LOGGING_ENABLED=true
```

### Production
```env
APP_NAME="RTS Environmental"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

COMPANY_NAME="RTS Environmental"
BUSINESS_TAGLINE="Mold & Asbestos Inspector"
BUSINESS_EMAIL=info@yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=smbgen
DB_USERNAME=your-username
DB_PASSWORD=your-secure-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password

FEATURE_APPOINTMENTS=true
FEATURE_EMAIL_COMPOSER=true
FEATURE_CMS=true
FEATURE_BLOG=true
FEATURE_MESSAGES=true
FEATURE_INSPECTION_REPORTS=true
FEATURE_BILLING=true

AI_CONTENT_GENERATION_ENABLED=false
AI_PROVIDER=anthropic
ANTHROPIC_API_KEY=
ANTHROPIC_MODEL=claude-opus-4-1
ANTHROPIC_MAX_TOKENS=4096
ANTHROPIC_TEMPERATURE=0.7
AI_RATE_LIMIT_ENABLED=true
AI_MAX_REQUESTS_PER_HOUR=60
AI_MAX_REQUESTS_PER_DAY=200
AI_LOGGING_ENABLED=true
```

---

## After Changing Configuration

Clear Laravel's caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

For production deployments with config caching enabled:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Security Best Practices

- ✅ Never commit `.env` files to git (already in `.gitignore`)
- ✅ Use strong, unique passwords for database and email
- ✅ Rotate secrets after making repository public
- ✅ Keep `APP_DEBUG=false` in production
- ✅ Use HTTPS in production (`APP_URL=https://...`)
- ✅ Restrict database access to localhost when possible
