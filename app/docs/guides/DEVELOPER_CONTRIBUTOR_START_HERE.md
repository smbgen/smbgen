# Developer & Contributor Getting Started Guide

Welcome to **smbgen**! This guide will help you set up the development environment and understand the technology stack, regardless of your OS or experience level.

## Table of Contents
1. [What is smbgen?](#what-is-smbgen)
2. [Technology Stack Explained](#technology-stack-explained)
3. [Development Environment Setup](#development-environment-setup)
4. [Quick Start](#quick-start)
5. [Common Issues & Solutions](#common-issues--solutions)
6. [Where to Go From Here](#where-to-go-from-here)

---

## What is smbgen?

smbgen is a **Laravel-based client management platform** with:
- **CMS** (Content Management System) for managing web pages and content
- **Booking system** for appointment scheduling
- **Multi-tenancy support** - serve multiple clients under different domains/subdomains
- **AI content generation** using Claude/Anthropic
- **File management** for storing client documents
- **Email composer** and messaging system
- **Billing & invoicing** (Stripe integration)
- **Admin dashboard** for business management

**Think of it as:** A white-label SaaS platform where you can offer CMS + booking + file management services to multiple clients.

---

## Technology Stack Explained

### Backend (Server-Side)

**Laravel (PHP Framework)** - The foundation
- **What it is:** A PHP web framework that handles requests, database operations, and business logic
- **What it does:** When a user visits your site, Laravel processes the request and returns a response
- **Version:** Laravel 12 (latest, released Nov 2024)
- **PHP:** 8.4.15 (requires PHP 8.0+)

**Key Laravel Concepts for Beginners:**
- **Routes** (`routes/web.php`) - Define what URLs map to which code
- **Controllers** (`app/Http/Controllers/`) - Handle the logic for requests
- **Models** (`app/Models/`) - Represent database tables (e.g., User model = users table)
- **Migrations** (`database/migrations/`) - Version control for database schema
- **Artisan** - Laravel's command-line tool for development tasks

**Example:** When you visit `/admin/dashboard`, Laravel routes the request to `AdminController@dashboard()`, which queries the database using the `User` model and returns an HTML view.

### Database

**SQLite (Local Development) / MySQL (Production)**
- **SQLite** - Built-in, file-based database. Perfect for local development.
- **MySQL** - Full-featured database for production. What you'll use on Laravel Cloud.

**Database Concepts:**
- **Tables** - Think of them as spreadsheets (users, bookings, clients)
- **Columns** - The fields in each row (name, email, created_at)
- **Relationships** - How tables connect (a user has many bookings)

### Frontend (User-Facing)

**Blade Templates** (PHP templating)
- HTML with embedded PHP for dynamic content
- Files: `resources/views/*.blade.php`
- Example: `<h1>{{ $user->name }}</h1>` displays the user's name

**Alpine.js** (JavaScript library)
- Lightweight, reactive JavaScript for interactive elements
- Makes forms, dropdowns, modals work without page reloads
- Files: Inline in `.blade.php` or `resources/js/`

**Tailwind CSS** (Utility-first CSS)
- Build styled components by combining small CSS classes
- Example: `<button class="bg-blue-600 text-white px-4 py-2 rounded">`
- Faster than writing custom CSS

**Livewire** (Real-time UI)
- Write interactive components without leaving Laravel/PHP
- Handles real-time form updates, pagination, filtering
- Version 3 (latest)

### Tools & Services

**Composer** - PHP package manager
- Like `npm` for JavaScript or `pip` for Python
- Manages Laravel, packages, and dependencies
- File: `composer.json`

**Node.js & npm** - JavaScript ecosystem
- Builds frontend assets (CSS, JavaScript)
- Runs development server for Vite
- Files: `package.json`, `resources/css/`, `resources/js/`

**Vite** - Frontend build tool
- Compiles your CSS and JavaScript for production
- Provides hot-reload during development
- Replaces Webpack (older Laravel standard)

**Git** - Version control
- Tracks changes to code
- Allows collaboration with other developers
- GitHub is where this code is hosted

**Laravel Herd** (Windows) / Homestead (Mac/Linux) / Valet (Mac)
- Local development servers
- Provides PHP, Composer, MySQL in one package
- Makes `laravel.test` domain work locally

---

## Development Environment Setup

Choose your operating system:

### Windows Setup

#### Prerequisites
- Git Bash (comes with Git for Windows)
- Laravel Herd (~3 minutes to install)
- VSCode (optional but recommended)

#### Step 1: Install Laravel Herd

1. Download from https://herd.laravel.com/
2. Run the installer
3. It installs PHP, Composer, Laravel CLI, and MySQL automatically
4. Herd runs as a service in your system tray

**Check Installation:**
```bash
php --version
composer --version
```

#### Step 2: Configure Git Bash for Laravel Herd

Git Bash can't execute `.bat` files directly. Create `~/.bashrc` to fix this:

**Location:** `C:\Users\<your-username>\.bashrc`

**Contents:**
```bash
# Laravel Herd Aliases
alias php="php.bat"
alias herd="herd.bat"
alias laravel="laravel.bat"

# Composer function for Git Bash compatibility
composer() {
    cmd //c "C:\Users\<your-username>\.config\herd\bin\composer.bat $*"
}
export -f composer

# Useful shortcuts
alias artisan="php artisan"
alias migrate="php artisan migrate"
```

**How to Create the File:**
1. Open Git Bash
2. Run: `nano ~/.bashrc`
3. Paste the contents above
4. Press Ctrl+X, then Y, then Enter

**Load Configuration:**
```bash
source ~/.bashrc
```

#### Step 3: Clone the Repository

```bash
cd ~/Projects  # or wherever you want to keep code
git clone https://github.com/alexramsey92/smbgen.git
cd smbgen
```

#### Step 4: Install Dependencies

```bash
# Install PHP packages
composer install

# Install JavaScript packages
npm install

# Build frontend assets
npm run build
```

#### Step 5: Configure Environment

```bash
# Copy example environment file
cp .env.example .env

# Generate encryption key
php artisan key:generate

# Create database (SQLite)
touch database/database.sqlite

# Run migrations (create tables)
php artisan migrate
```

#### Step 6: Start Development

**Option A: Use Herd (Recommended)**
- Open Herd app from system tray
- Navigate to http://smbgen.test
- It automatically serves your application

**Option B: Use Laravel's Built-in Server**
```bash
php artisan serve
# Visit: http://localhost:8000
```

**Watch Frontend Changes:**
```bash
npm run dev
# In another terminal, let this run while you develop
```

---

### Mac Setup

#### Prerequisites
- Homebrew (package manager)
- Git
- VSCode (optional)

#### Step 1: Install Laravel Valet (Recommended)

```bash
# Install Homebrew if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php

# Install Composer
brew install composer

# Install Laravel Valet
composer global require laravel/valet
valet install
```

#### Step 2: Clone and Setup

```bash
cd ~/Projects  # or your preferred location
git clone https://github.com/alexramsey92/smbgen.git
cd smbgen

# Install dependencies
composer install
npm install

# Create .env file
cp .env.example .env
php artisan key:generate

# Create database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Serve with Valet
valet park
# Now visit http://smbgen.test
```

**If Using Homestead Instead:**
```bash
# Installation is more complex - see: https://laravel.com/docs/homestead
# But provides more isolation and flexibility
```

---

### Linux Setup (Ubuntu/Debian)

#### Prerequisites
- Package manager (`apt`)
- Git
- VSCode (optional)

#### Step 1: Install PHP and Dependencies

```bash
# Update package manager
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.4 php8.4-cli php8.4-mbstring php8.4-xml php8.4-sqlite3 php8.4-curl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and npm
sudo apt install -y nodejs npm

# Install Git
sudo apt install -y git
```

#### Step 2: Clone and Setup

```bash
cd ~/projects  # or your preferred location
git clone https://github.com/alexramsey92/smbgen.git
cd smbgen

# Install dependencies
composer install
npm install

# Create .env file
cp .env.example .env
php artisan key:generate

# Create database
touch database/database.sqlite
chmod 664 database/database.sqlite
chmod 775 database/

# Run migrations
php artisan migrate
```

#### Step 3: Start Development

```bash
# Terminal 1: Start PHP development server
php artisan serve
# Visit http://localhost:8000

# Terminal 2: Watch frontend changes
npm run dev
```

**Optional: Install Laravel Valet for .test domains**
```bash
composer global require laravel/valet
valet install
cd ~/projects/smbgen
valet link
# Visit http://smbgen.test
```

---

## Quick Start

### First Time Running The App

1. **Start your development environment:**
   - **Windows (Herd):** Open Herd app, visit http://smbgen.test
   - **Mac (Valet):** `valet link`, visit http://smbgen.test
   - **Linux:** Run `php artisan serve`, visit http://localhost:8000

2. **Watch for frontend changes:**
   ```bash
   npm run dev  # Keep this running in another terminal
   ```

3. **Create a test user:**
   ```bash
   php artisan tinker
   >>> $user = User::factory()->create(['email' => 'test@example.com'])
   >>> $user->update(['is_super_admin' => true])
   >>> exit
   ```

4. **Login:** 
   - Email: `test@example.com`
   - Password: generated from factory (check `.env` or use password reset)

### Project Structure

```
smbgen/
├── app/                    # Application code
│   ├── Http/              # Controllers, requests, middleware
│   ├── Models/            # Database models (User, Client, Booking, etc.)
│   ├── Jobs/              # Background jobs (emails, imports)
│   ├── Mail/              # Email classes
│   └── docs/              # Documentation (what you're reading!)
├── config/                # Configuration files (database, mail, ai, etc.)
├── database/
│   ├── migrations/        # Database schema changes
│   ├── factories/         # Test data generators
│   └── seeders/           # Populate database with test data
├── resources/
│   ├── views/             # Blade templates (.blade.php files)
│   ├── css/               # Tailwind CSS
│   └── js/                # Alpine.js and JavaScript
├── routes/                # URL routing (web.php, api.php)
├── storage/               # Files, logs, cache
├── tests/                 # Unit and feature tests
├── vendor/                # Installed PHP packages (don't edit)
├── node_modules/          # Installed JavaScript packages (don't edit)
├── .env.example           # Environment template
├── composer.json          # PHP dependencies
├── package.json           # JavaScript dependencies
└── artisan                # Command runner
```

### Common Development Tasks

**Run database migrations:**
```bash
php artisan migrate
```

**Create a new model:**
```bash
php artisan make:model Client -m  # -m creates migration too
```

**Create a new controller:**
```bash
php artisan make:controller ClientController -r  # -r creates resource methods
```

**Create a new migration:**
```bash
php artisan make:migration add_column_to_users_table
```

**Run tests:**
```bash
php artisan test
```

**Clear caches:**
```bash
php artisan optimize:clear
```

**Check routes:**
```bash
php artisan route:list
```

**Interactive PHP shell (Tinker):**
```bash
php artisan tinker
>>> User::count()
=> 5
>>> exit
```

---

## Common Issues & Solutions

### Issue: "Laravel application not found"

**On Windows:**
```bash
# Make sure Herd is running (check system tray)
# If it crashes, restart it

# Try accessing from Git Bash:
php artisan serve
# Then visit http://localhost:8000
```

**On Mac/Linux:**
```bash
# Make sure the dev server is running
php artisan serve
# Visit http://localhost:8000
```

### Issue: "Composer command not found"

**Windows:**
```bash
# Edit ~/.bashrc and verify composer function is there:
nano ~/.bashrc

# Should contain:
# composer() {
#     cmd //c "C:\Users\<username>\.config\herd\bin\composer.bat $*"
# }

# Then reload:
source ~/.bashrc
composer --version
```

**Mac/Linux:**
```bash
# Check if Composer is installed
composer --version

# If not found, reinstall:
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Issue: "php: command not found"

**Windows (Herd):**
1. Verify Herd is running (system tray)
2. Close and reopen Git Bash
3. Verify Path: `echo $PATH | grep herd`

**Mac:**
```bash
# Install PHP
brew install php
# Restart terminal
```

**Linux:**
```bash
# Install PHP
sudo apt install php8.4-cli
```

### Issue: "Class not found" after `git pull`

```bash
# Regenerate autoload files
composer dump-autoload --optimize

# Clear Laravel caches
php artisan optimize:clear

# Discover packages
php artisan package:discover
```

### Issue: "SQLSTATE[HY000] database disk image is malformed"

The SQLite database file is corrupted:

```bash
# Delete the corrupted database
rm database/database.sqlite

# Create a new one
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### Issue: "Target class [auth] does not exist"

This happens after git pull. The bootstrap file has an unguarded auth call:

```bash
# Edit bootstrap/app.php
nano bootstrap/app.php

# Find the exceptions->context function and wrap auth() in a try/catch:
# $exceptions->context(fn () => [
#     'user_id' => (function () {
#         try {
#             return function_exists('auth') && app()->bound('auth') ? auth()->id() : null;
#         } catch (\Throwable $e) {
#             return null;
#         }
#     })(),
# ]);
```

### Issue: "npm ERR! code ERESOLVE"

```bash
# Force npm to resolve dependencies
npm install --legacy-peer-deps

# Or update npm
npm install -g npm@latest
npm install
```

---

## Where to Go From Here

### Learn the Codebase

1. **Start with the Dashboard:**
   - Route: `routes/web.php` - search for "admin.dashboard"
   - Controller: `app/Http/Controllers/Admin/DashboardController.php`
   - View: `resources/views/admin/dashboard.blade.php`
   
   This shows you how a request flows through the system.

2. **Understand a Core Feature:**
   - Bookings: Start in `app/Http/Controllers/BookingController.php`
   - Clients: Start in `app/Http/Controllers/ClientController.php`
   - CMS: Start in `app/Http/Controllers/CmsController.php`

3. **Check the Models:**
   - `app/Models/` directory shows all database entities
   - Read the relationships between models

### Reading Documentation

- **Feature Documentation:** `app/docs/` has guides for each feature
- **API Documentation:** Routes in `routes/api.php`
- **Database Schema:** `database/migrations/` shows table structure
- **Configuration:** `config/` folder for app, database, mail, etc.

### Making Your First Contribution

1. **Create a feature branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes:**
   - Edit code files
   - Run `npm run build` to compile frontend changes
   - Test with `php artisan test` or manual testing

3. **Commit your changes:**
   ```bash
   git add .
   git commit -m "Clear description of what you changed"
   ```

4. **Push and create a pull request:**
   ```bash
   git push origin feature/your-feature-name
   ```
   - Go to GitHub and click "New Pull Request"
   - Describe your changes clearly
   - Wait for feedback

### Testing

**Run all tests:**
```bash
php artisan test
```

**Run specific test file:**
```bash
php artisan test tests/Feature/BookingTest.php
```

**Run test with specific filter:**
```bash
php artisan test --filter=testUserCanBook
```

### Debugging

**Use Tinker for quick testing:**
```bash
php artisan tinker
>>> $user = User::find(1)
>>> $user->bookings()->count()
=> 3
```

**Check logs:**
```bash
tail -f storage/logs/laravel.log
```

**Use dd() to dump and die:**
```php
// In any Laravel file:
dd($variable);  // Will output and stop execution
```

---

## Getting Help

1. **Check the docs:** `app/docs/` has guides for specific features
2. **Search GitHub issues:** https://github.com/alexramsey92/smbgen/issues
3. **Laravel documentation:** https://laravel.com/docs
4. **Ask in conversations:** When confused, ask clearly with error messages

---

## Key Files to Understand

**For beginners, read these in order:**

1. `routes/web.php` - All URLs defined here
2. `app/Http/Controllers/Admin/DashboardController.php` - Example controller
3. `resources/views/admin/dashboard.blade.php` - Example view
4. `app/Models/User.php` - Example model
5. `config/app.php` - Application configuration
6. `database/migrations/` - Table structure

---

## Next Steps

- [ ] Complete setup for your OS above
- [ ] Run `php artisan serve` and visit http://localhost:8000
- [ ] Create a test user with `php artisan tinker`
- [ ] Explore the dashboard
- [ ] Read the specific feature documentation in `app/docs/`
- [ ] Make a small change and run tests to verify it works

**Welcome to the team! 🚀**

---

**Updated:** January 5, 2026  
**Maintained by:** smbgen Team  
**Questions?** Check [app/docs/](./README.md) for more guides or create a GitHub issue
