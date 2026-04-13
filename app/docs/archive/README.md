# README.md

## 🎯 **START HERE: [TACTICAL_ROADMAP.md](./TACTICAL_ROADMAP.md)**
*Complete strategy, priorities, and action plan - October 3, 2025*

---

## ClientBridge

CLIENTBRIDGE is a **unified platform for service professionals** that combines booking system, email, files, and meetings in one place. Built for contractors, inspectors, realtors, and field service professionals who need to manage clients professionally.

**PLATFORM VISION:** Stop juggling scattered tools. CLIENTBRIDGE brings appointments, leads, files, and communication together in one unified platform. Professional tools built for people who serve clients.

### Core Features

**Zero-Friction Meetings**
* Google Meet links auto-generate for every booking
* No scheduling links, no manual setup
* One click and you're in the meeting room

**Calendar Intelligence**
* Real-time availability checking prevents double-bookings
* Customers only see open time slots
* Bi-directional sync with Google Calendar

**Property-Aware Leads**
* Capture property addresses, project scope, and contact details
* Purpose-built for real estate, contractors, and field service
* Lead source tracking and conversion analytics

**Unified Dashboard**
* Manage appointments, leads, communications, and files
* Mobile dashboard always in your pocket
* Desktop admin panel for deep work and analytics

**Professional Infrastructure**
* Stripe payment integration for invoices
* Enterprise-grade security and file management
* Responsive UI with Tailwind CSS + Livewire
* Google Workspace integration

Hosted at [https://clientbridge.app](https://clientbridge.app)

### Requirements

* PHP 8.3+
* Composer
* **Node.js 24.5** (CRITICAL: Use `nvm alias default 24.5` on Windows - newer versions break npm install as of October 2025)
* npm (comes with Node.js)
* SQLite (or preferred DB)
* **Stripe account** for payment processing

## Getting Started Locally on Windows

**STEP 1: Ensure Node.js 24.5 (CRITICAL)**
```bash
# Install/use Node.js 24.5 (required for npm install to work on Windows)
nvm install 24.5
nvm alias default 24.5
nvm use 24.5
node --version  # Must show v24.5.x
```

**STEP 2: Laravel Herd + Git Bash Setup**
Laravel Herd + Git Bash = bash_profile needed as follows:
```bash
alias php="php.bat"
alias herd="herd.bat"
alias laravel="laravel.bat"
alias composer="composer.bat"
```

Then, create .env from .env.example
Then run to populate key:
php artisan key:generate

Then, create a database.sqlite file in /database

Then run 

npm install && npm run build
composer install

And the local application will deploy.

## Windows Setup & Troubleshooting Scripts

The `scripts/` directory contains helpful setup and troubleshooting tools for Windows development:

### Setup Scripts
- **`setup-windows-herd-buildproject-and-run-dev.sh`** - Complete Windows project setup with Herd
  - Configures PHP/Composer aliases
  - Creates all Laravel directories
  - Installs dependencies and builds assets
  - Runs migrations and starts dev server
- **`setup-herd-gitbash.sh`** - Configure Herd aliases for Git Bash (.bash_profile setup)
- **`setup-nodejs-24-5-windows.sh`** - Install Node.js 24.5 (required for Windows npm compatibility)

### Fix Scripts
- **`fix-bootstrap-cache.sh`** - Fix "bootstrap\cache directory must be present and writable" error
  - Cleans problematic cache files
  - Tests write permissions
  - Provides Windows-specific permission fixes
- **`fix-autoload-php.sh`** - Fix missing vendor/autoload.php issues
  - Creates required directories
  - Runs composer install with proper error handling
  - Verifies Laravel is working after fix

### Troubleshooting Scripts
- **`troubleshoot-herd-php.sh`** - Diagnose PHP/Composer/Herd path issues
  - Checks profile files for aliases
  - Locates Herd installation
  - Tests PHP and Composer availability
  - Provides solutions for common issues
- **`troubleshoot-npm-vite.sh`** - Diagnose npm install and Vite issues
  - Checks Node.js version (must be 24.5 for Windows)
  - Verifies npm and Vite installation
  - Tests package.json validity
  - Provides fix steps for common npm errors
- **`debug-php-availability.sh`** - Quick check if PHP commands are working in Git Bash

### Quick Start
```bash
# Full automated setup
bash scripts/setup-windows-herd-buildproject-and-run-dev.sh

# Just fix bootstrap/cache error
bash scripts/fix-bootstrap-cache.sh

# Just fix autoload.php issues
bash scripts/fix-autoload-php.sh

# Diagnose PHP/Herd issues
bash scripts/troubleshoot-herd-php.sh

# Diagnose npm/Vite issues
bash scripts/troubleshoot-npm-vite.sh
```

### Windows Herd PHP Setup for Cursor/IDEs

**Editor Configuration (Cursor/VSCode)**:
In your `settings.json` (adjust `php84`/`php85` to match your Herd version):
```json
{
  "php.validate.executablePath": "C:\\Users\\alexr\\.config\\herd\\bin\\php84\\php.exe",
  "intelephense.environment.phpExecutablePath": "C:\\Users\\alexr\\.config\\herd\\bin\\php84\\php.exe"
}
```

**Terminal Configuration (optional)**:
```json
{
  "terminal.integrated.env.windows": {
    "PATH": "C:\\Users\\alexr\\.config\\herd\\bin;%PATH%"
  }
}
```

**Run Tests with Herd**:
```bash
# Using Herd's PHP directly
"C:\Users\alexr\.config\herd\bin\php.bat" vendor\bin\pest -q

# Or with aliases in Git Bash
php vendor/bin/pest -q
```

**Verify Herd PHP**:
```bash
"C:\Users\alexr\.config\herd\bin\php.bat" -v
```

### Payment Integration

ClientBridge includes basic Stripe payment integration for:
- **Product purchases** (landing page sales)
- **Client invoices** (admin-generated billing)
- **Secure checkout** with Stripe Checkout

#### Setup Stripe Integration

1. **Create Stripe account** at [stripe.com](https://stripe.com)
2. **Install Stripe SDK**: `composer require stripe/stripe-php`
3. **Get API keys** from Stripe Dashboard
4. **Configure environment variables**:
   ```env
   STRIPE_PUBLIC_KEY=pk_test_your_public_key
   STRIPE_SECRET_KEY=sk_test_your_secret_key
   STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
   ```
5. **Set up webhook** in Stripe Dashboard:
   - URL: `https://yourdomain.com/stripe/webhook`
   - Events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`

#### Using Payment Buttons

```php
// In your Blade templates
<x-payment-button 
    :amount="49900" 
    description="Cybersecurity Audit"
    payment_type="product"
    label="Purchase Audit"
/>
```

#### Payment Flow

1. **User clicks payment button** → Creates Stripe checkout session
2. **User completes payment** → Stripe redirects to success page
3. **Webhook processes** → Updates payment status in database
4. **Admin can view** → All payments in admin dashboard

#### Testing Payments

Use Stripe's test mode with these test card numbers:
- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Requires Authentication**: `4000 0025 0000 3155`

### Installation

```bash
git clone git@github.com:alexramsey92/clientbridge-laravel.git
cd clientbridge-laravel
composer install
cp .env.example .env
php artisan key:generate
For powershell run: Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
npm install && npm run build
php artisan migrate
```

### VPS Deployment Steps (Nixihost KVM VPS - Ubuntu 24.04)

1. **Set up domain DNS**: Point A record for `houston1.oldlinecyber.com` to your VPS IP.
2. **Install Nginx, PHP 8.3, and necessary extensions**.
3. **Create project directory**: Clone repo into `/home/alex/clientbridge`.
4. **Set permissions**:

   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   sudo usermod -aG www-data alex
   ```
5. **Configure Nginx**:
   Create `/etc/nginx/sites-available/houston1` with:

   ```nginx
   server {
       listen 80;
       server_name houston1.oldlinecyber.com;
       return 301 https://$host$request_uri;
   }

   server {
       listen 443 ssl;
       server_name houston1.oldlinecyber.com;

       root /home/alex/clientbridge/public;
       index index.php index.html;

       ssl_certificate /etc/letsencrypt/live/houston1.oldlinecyber.com/fullchain.pem;
       ssl_certificate_key /etc/letsencrypt/live/houston1.oldlinecyber.com/privkey.pem;
       include /etc/letsencrypt/options-ssl-nginx.conf;
       ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/run/php/php8.3-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.ht {
           deny all;
       }
   }
   ```
6. **Enable site**:

   ```bash
   sudo ln -s /etc/nginx/sites-available/houston1 /etc/nginx/sites-enabled/
   sudo nginx -t && sudo systemctl reload nginx
   ```
7. **Install SSL cert** with Certbot:

   ```bash
   sudo certbot --nginx -d houston1.oldlinecyber.com
   ```

---

# SECURITY.md

## VPS and Laravel Permissions Strategy

This VPS is used for development and is maintained by a single user (`alex`). To enable development and Artisan access while also running Nginx/PHP as `www-data`, the following setup was intentionally applied:

### Permissions Model

* `alex` is part of the `www-data` group:

  ```bash
  sudo usermod -aG www-data alex
  ```
* `storage/` and `bootstrap/cache/` are owned by `www-data:www-data` with `775` permissions:

  ```bash
  sudo chown -R www-data:www-data storage bootstrap/cache
  sudo chmod -R 775 storage bootstrap/cache
  ```

This allows both web server and artisan CLI (via `alex`) to write logs and cache safely.

### Security Considerations

* No other users have shell access
* `alex` has sudo privileges and is trusted
* Web root (`public/`) is scoped tightly
* No public file upload or execution is allowed without validation/sanitization

This setup is safe for internal development and small team production, but should be reviewed if multiple user roles or third-party shell access is ever introduced.

---

# Laravel Platform Notes

ClientBridge is built on **Laravel 12** with the following tech stack:

### Stack Highlights

* Laravel Breeze for basic auth scaffolding
* Blade templates with Bootstrap 5 for styling
* SQLite for local persistence (easily swappable)
* Artisan CLI and Laravel’s migration system for easy schema updates

### Useful Artisan Commands

* `php artisan migrate` – apply database schema
* `php artisan make:model ModelName -m` – create model with migration
* `php artisan serve` – local dev server (if not using Nginx)
* `php artisan config:cache` – cache config (watch for permission issues!)
* `php artisan route:list` – see available routes

### Development Tips

* Store config and sensitive credentials in `.env`
* Bootstrap, cache, and storage directories must be writable
* Use `php artisan tinker` to experiment with models and DB
* Use `composer dump-autoload` if class discovery breaks

---

---

## 🚀 Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Blade + Tailwind CSS + Livewire
- **Auth**: Laravel Breeze
- **Database**:
  - SQLite for local development
  - MySQL on production VPS
- **Deployment**: Unmanaged VPS (NixiHost) via Git and manual `.env` configuration

---

## 🧑‍💻 Local Development (via Herd on Windows)

### Requirements

- [Herd](https://herd.laravel.com/) (PHP, Valet-style local server)
- Composer (bundled with Herd)
- Git
- SQLite (used automatically if `.env` is configured)

### Setup Steps

```bash
git clone https://github.com/YOUR_USERNAME/clientbridge-laravel.git
cd clientbridge-laravel

# Install dependencies
composer install

# Create local SQLite DB
New-Item -Path ".\database\database.sqlite" -ItemType File

# Copy .env
cp .env.example .env

# Edit .env and set:
# DB_CONNECTION=sqlite
# DB_DATABASE=C:/Users/YOURNAME/Herd/clientbridge-laravel/database/database.sqlite

# Generate app key
php artisan key:generate

# Migrate tables
php artisan migrate

# Open in browser
http://clientbridge-laravel.test
```

---

# Seed Sqlite with user data

php artisan tinker

User::create([
  'name' => 'Test Client',
  'email' => 'client@example.com',
  'password' => bcrypt('password123')
]);

# Wipe sqlite locally
Remove-Item .\database\database.sqlite
New-Item -Path ".\database\database.sqlite" -ItemType File

# Rerun migrations
php artisan migrate

# Reseed users using seeders
php artisan db:seed --class=UserSeeder

    'name' => 'Admin User',
    'email' => 'admin@clientbridge.app',
    'password' => Hash::make('admin123'),

    'name' => 'Demo Client',
    'email' => 'demo@clientbridge.app',
    'password' => Hash::make('demo123'),


# Helpful commands

php artisan migrate:fresh --seed                                                                                                                                                                                                                                                         
php artisan optimize:clear                                                                                                                                                                                                                                                               
php artisan config:clear                                                                                                                                                                                                                                                                 
php artisan route:clear                                                                                                                                                                                                                                                                  
php artisan optimize:clear    

# HISTORY.md

## 🛠️ Local Development & Git Workflow

```bash
git status                 # Check repo status
git add .                  # Stage changes
git commit -m 'adjustments'  # Commit with message
git push                   # Push to remote
