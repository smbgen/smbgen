#!/usr/bin/env bash

# Setup Windows Herd Build Project and Run Dev
# This script sets up the smbgen Laravel project on Windows with Herd and starts the dev environment

set -euo pipefail

PROJECT_NAME="smbgen"
DB_FILE="./database/database.sqlite"

echo "🚀 Setting up smbgen Laravel project with Herd on Windows..."

# Check if we're in the right directory
if [[ ! -f "composer.json" ]] || [[ ! -f "artisan" ]]; then
    echo "❌ Error: This doesn't appear to be a Laravel project directory."
    echo "   Please run this script from the root of the smbgen Laravel project."
    exit 1
fi

echo "✅ Found Laravel project files"

# Setup Herd aliases first
echo "🔧 Setting up Herd aliases for Git Bash..."
if [[ -f "scripts/setup-herd-gitbash.sh" ]]; then
    bash scripts/setup-herd-gitbash.sh
else
    echo "⚠️  Warning: setup-herd-gitbash.sh not found, you may need to manually configure PHP paths"
fi

# Reload shell configuration from multiple possible profile files
echo "🔄 Reloading shell configuration..."
for profile in "$HOME/.bash_profile" "$HOME/.bashrc" "$HOME/.profile"; do
    if [[ -f "$profile" ]]; then
        echo "   Loading $profile..."
        # shellcheck source=/dev/null
        source "$profile" 2>/dev/null || true
    fi
done

# Set up PHP aliases if needed (same logic as herd setup script)
if ! command -v php >/dev/null 2>&1; then
    if command -v php.bat >/dev/null 2>&1; then
        echo "🔧 Setting up PHP alias for php.bat..."
        shopt -s expand_aliases  # Enable alias expansion in scripts
        alias php='php.bat'
        
        # Test the alias
        if php -v >/dev/null 2>&1; then
            echo "   ✅ PHP alias working"
        else
            echo "   ⚠️  PHP alias set but may not work in script context"
        fi
    fi
fi

if ! command -v composer >/dev/null 2>&1; then
    if command -v composer.bat >/dev/null 2>&1; then
        echo "🔧 Setting up Composer alias for composer.bat..."
        shopt -s expand_aliases  # Enable alias expansion in scripts
        alias composer='composer.bat'
        
        # Test the alias
        if composer --version >/dev/null 2>&1; then
            echo "   ✅ Composer alias working"
        else
            echo "   ⚠️  Composer alias set but may not work in script context"
        fi
    fi
fi

# Alternative approach: Use .bat commands directly if aliases don't work
PHP_CMD="php"
COMPOSER_CMD="composer"

if ! command -v php >/dev/null 2>&1; then
    if command -v php.bat >/dev/null 2>&1; then
        echo "🔧 Will use php.bat directly instead of alias"
        PHP_CMD="php.bat"
    fi
fi

if ! command -v composer >/dev/null 2>&1; then
    if command -v composer.bat >/dev/null 2>&1; then
        echo "🔧 Will use composer.bat directly instead of alias"
        COMPOSER_CMD="composer.bat"
    fi
fi

# Verify PHP is available
echo "🔍 Verifying PHP installation..."
if ! $PHP_CMD -v >/dev/null 2>&1; then
    echo "❌ Error: PHP not found after trying multiple profile sources and .bat commands."
    echo ""
    echo "🔧 Debug information:"
    echo "   Attempted command: $PHP_CMD"
    echo "   Current PATH: $PATH"
    echo ""
    echo "🔍 Checking for php.bat:"
    if command -v php.bat >/dev/null 2>&1; then
        echo "   ✅ php.bat found at: $(command -v php.bat)"
        echo "   💡 Try manually: php.bat -v"
    else
        echo "   ❌ php.bat not found either"
    fi
    echo ""
    echo "🔍 Checking common Herd paths:"
    HERD_PATHS=(
        "/c/Users/$USER/AppData/Local/herd/bin"
        "/c/Users/$USER/.config/herd/bin"
        "/c/Users/$USER/AppData/Local/Laravel/Herd/bin" 
        "/c/Program Files/Laravel/Herd/bin"
    )
    
    for herd_path in "${HERD_PATHS[@]}"; do
        if [[ -d "$herd_path" ]]; then
            echo "   ✅ Found Herd at: $herd_path"
            if [[ -f "$herd_path/php.bat" ]]; then
                echo "      🔧 Try: export PATH=\"$herd_path:\$PATH\" && php.bat -v"
            fi
        else
            echo "   ❌ Not found: $herd_path"
        fi
    done
    echo ""
    echo "📋 Please:"
    echo "   1. Ensure Laravel Herd is installed: https://herd.laravel.com/windows"
    echo "   2. Run the troubleshooting script: bash scripts/troubleshoot-herd-php.sh"
    echo "   3. Try running setup script with: PHP_CMD=php.bat bash $0"
    echo "   4. Restart Git Bash completely and try again"
    exit 1
fi

PHP_VERSION=$($PHP_CMD -v | head -n 1)
echo "✅ Found PHP: $PHP_VERSION (using $PHP_CMD)"

# Verify Composer is available
echo "🔍 Verifying Composer installation..."
if ! $COMPOSER_CMD --version >/dev/null 2>&1; then
    echo "❌ Error: Composer not found after trying multiple profile sources and .bat commands."
    echo ""
    echo "🔧 Debug information:"
    echo "   Attempted command: $COMPOSER_CMD"
    if command -v composer.bat >/dev/null 2>&1; then
        echo "   ✅ composer.bat found at: $(command -v composer.bat)"
        echo "   💡 Try manually: composer.bat --version"
    else
        echo "   ❌ composer.bat not found either"
    fi
    echo ""
    echo "📋 Please:"
    echo "   1. Ensure Composer is installed: https://getcomposer.org/download/"
    echo "   2. Or install via Herd if it includes Composer"
    echo "   3. Try running setup script with: COMPOSER_CMD=composer.bat bash $0"
    echo "   4. Run the troubleshooting script: bash scripts/troubleshoot-herd-php.sh"
    exit 1
fi

COMPOSER_VERSION=$($COMPOSER_CMD --version | head -n 1)
echo "✅ Found Composer: $COMPOSER_VERSION (using $COMPOSER_CMD)"

# Create Laravel storage directories (prevents pre-autoload-dump script failures)
echo "📁 Creating Laravel storage directories..."
mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs 2>/dev/null || true
mkdir -p bootstrap/cache 2>/dev/null || true

# Verify bootstrap/cache was created (critical for Laravel)
if [[ ! -d "bootstrap/cache" ]]; then
    echo "⚠️  Warning: Failed to create bootstrap/cache directory"
    echo "🔧 Attempting alternative method..."
    mkdir bootstrap 2>/dev/null || true
    mkdir bootstrap/cache 2>/dev/null || true
    
    if [[ ! -d "bootstrap/cache" ]]; then
        echo "❌ Error: Unable to create bootstrap/cache directory"
        echo ""
        echo "💡 This directory is required for Laravel to function."
        echo "   Please run the fix script:"
        echo "   bash scripts/fix-bootstrap-cache.sh"
        echo ""
        echo "   Or fix manually in PowerShell:"
        echo "   New-Item -Path \"bootstrap\\cache\" -ItemType Directory -Force"
        echo "   attrib -r +a .\\bootstrap\\cache"
        exit 1
    fi
fi

echo "✅ Laravel directories created"

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
$COMPOSER_CMD install --optimize-autoloader

# Check if .env exists, if not copy from .env.example
if [[ ! -f ".env" ]]; then
    echo "🔧 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ Created .env file"
else
    echo "✅ .env file already exists"
fi

# Generate application key if not set
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    $PHP_CMD artisan key:generate
    echo "✅ Application key generated"
else
    echo "✅ Application key already set"
fi

# Create SQLite database file if it doesn't exist
echo "🗄️  Setting up SQLite database..."
if [[ ! -f "$DB_FILE" ]]; then
    echo "🔧 Creating SQLite database file..."
    mkdir -p database
    touch "$DB_FILE"
    echo "✅ Created database file: $DB_FILE"
else
    echo "✅ Database file already exists: $DB_FILE"
fi

# Update .env to use SQLite if not already configured
echo "🔧 Configuring database connection..."
if ! grep -q "DB_CONNECTION=sqlite" .env 2>/dev/null; then
    # Update .env for SQLite
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env 2>/dev/null || echo "DB_CONNECTION=sqlite" >> .env
    sed -i 's/^DB_HOST=.*/#DB_HOST=127.0.0.1/' .env 2>/dev/null || true
    sed -i 's/^DB_PORT=.*/#DB_PORT=3306/' .env 2>/dev/null || true
    sed -i 's/^DB_DATABASE=.*/DB_DATABASE=.\/database\/database.sqlite/' .env 2>/dev/null || echo "DB_DATABASE=./database/database.sqlite" >> .env
    sed -i 's/^DB_USERNAME=.*/#DB_USERNAME=root/' .env 2>/dev/null || true
    sed -i 's/^DB_PASSWORD=.*/#DB_PASSWORD=/' .env 2>/dev/null || true
    echo "✅ Configured .env for SQLite database"
else
    echo "✅ Database already configured for SQLite"
fi

# Run database migrations
echo "🏗️  Running database migrations..."
$PHP_CMD artisan migrate --force

# Check if Node.js/npm is available
echo "🔍 Checking for Node.js and npm..."
if command -v node >/dev/null 2>&1 && command -v npm >/dev/null 2>&1; then
    NODE_VERSION=$(node -v)
    NPM_VERSION=$(npm -v)
    echo "✅ Found Node.js: $NODE_VERSION"
    echo "✅ Found npm: $NPM_VERSION"
    
    # CRITICAL: Check for Node.js 24.5 (required for Windows npm install as of Oct 2025)
    if [[ "$NODE_VERSION" == v24.5* ]]; then
        echo "✅ Node.js 24.5 detected - npm install should work properly"
    elif [[ "$NODE_VERSION" == v24* ]]; then
        echo "⚠️  Node.js 24.x detected but not v24.5 - may cause npm install issues"
        echo "   💡 Consider: nvm install 24.5 && nvm alias default 24.5 && nvm use 24.5"
    else
        echo "⚠️  WARNING: Node.js $NODE_VERSION detected"
        echo "   🚨 CRITICAL: Node.js 24.5 is required for Windows npm install as of October 2025"
        echo "   💡 Fix with: nvm install 24.5 && nvm alias default 24.5 && nvm use 24.5"
        echo "   📋 Newer Node.js versions break npm install on Windows"
        echo ""
        echo "🤔 Continue anyway? (y/N)"
        read -r CONTINUE_ANYWAY
        if [[ ! "${CONTINUE_ANYWAY,,}" =~ ^(y|yes)$ ]]; then
            echo "❌ Setup cancelled. Please install Node.js 24.5 first."
            exit 1
        fi
        echo "⚠️  Proceeding with Node.js $NODE_VERSION (may fail)"
    fi
    
    # Install npm dependencies
    echo "📦 Installing npm dependencies..."
    if npm install; then
        echo "✅ npm install completed successfully"
        
        # Verify Vite installation
        if npm list vite >/dev/null 2>&1; then
            echo "✅ Vite is installed"
        else
            echo "⚠️  Vite not found in node_modules, attempting to install..."
            npm install vite --save-dev
        fi
        
        # Build assets for production
        echo "🔨 Building frontend assets..."
        if npm run build; then
            echo "✅ Frontend assets built successfully"
        else
            echo "❌ Frontend build failed"
            echo "🔧 Troubleshooting:"
            echo "   - Check if package.json is valid JSON"
            echo "   - Try: npm install --verbose"
            echo "   - Try: npx vite build"
            echo "   - Check node_modules/.bin/vite exists"
        fi
    else
        echo "❌ npm install failed"
        echo "🔧 Troubleshooting:"
        echo "   - Check if package.json is valid: npm run build --dry-run"
        echo "   - Clear npm cache: npm cache clean --force"
        echo "   - Delete node_modules and try again: rm -rf node_modules && npm install"
    fi
    
else
    echo "⚠️  Warning: Node.js/npm not found. Frontend assets won't be built."
    echo ""
    echo "🚨 CRITICAL: You need Node.js 24.5 for Windows npm install compatibility"
    echo "📋 Install Node.js 24.5:"
    echo "   1. Run the setup script: bash scripts/setup-nodejs-24-5-windows.sh"
    echo "   2. Or visit: https://nodejs.org/dist/v24.5.0/"
    echo "   3. Use nvm: nvm install 24.5 && nvm alias default 24.5"
fi

# Clear and cache configuration
echo "🧹 Clearing and caching configuration..."
$PHP_CMD artisan config:clear
$PHP_CMD artisan cache:clear
$PHP_CMD artisan route:clear
$PHP_CMD artisan view:clear

# Optimize for development
echo "⚡ Optimizing for development..."
$PHP_CMD artisan config:cache
$PHP_CMD artisan route:cache

# Set proper permissions for storage and bootstrap/cache
echo "🔒 Setting storage permissions..."
if [[ -d "storage" ]]; then
    chmod -R 775 storage 2>/dev/null || true
fi
if [[ -d "bootstrap/cache" ]]; then
    chmod -R 775 bootstrap/cache 2>/dev/null || true
fi

# Check if Laravel Herd is serving the site
echo "🌐 Checking Herd site availability..."
HERD_URL="http://${PROJECT_NAME}.test"
if command -v herd >/dev/null 2>&1; then
    echo "✅ Herd is available"
    echo "🌐 Your site should be available at: $HERD_URL"
else
    echo "⚠️  Herd command not found, but site may still be available at: $HERD_URL"
fi

echo ""
echo "🎉 Setup complete! Your smbgen Laravel project is ready."
echo ""
echo "📋 Next steps:"
echo "   1. Visit your site: $HERD_URL"
echo "   2. To start development with hot reload: npm run dev"
echo "   3. To start the Laravel development server: php artisan serve"
echo "   4. To run tests: php artisan test"
echo ""
echo "🔧 Development commands:"
echo "   npm run dev          - Start Vite dev server with hot reload"
echo "   npm run build        - Build assets for production"
echo "   php artisan serve    - Start Laravel dev server (if not using Herd)"
echo "   php artisan test     - Run the test suite"
echo "   php artisan tinker   - Laravel REPL for testing code"
echo ""
echo "🌐 URLs:"
echo "   Main site: $HERD_URL"
echo "   Admin: $HERD_URL/admin"
echo "   Login: $HERD_URL/login"
echo ""

# Offer to start dev server
echo "🚀 Would you like to start the development server now? (y/N)"
read -r START_DEV

if [[ "${START_DEV,,}" =~ ^(y|yes)$ ]]; then
    if command -v npm >/dev/null 2>&1; then
        echo "🔥 Starting Vite development server with hot reload..."
        echo "   Press Ctrl+C to stop the dev server"
        echo "   Visit $HERD_URL in your browser"
        echo ""
        npm run dev
    else
        echo "📡 Starting Laravel development server..."
        echo "   Press Ctrl+C to stop the server"
        echo "   Visit http://localhost:8000 in your browser"
        echo ""
        $PHP_CMD artisan serve
    fi
else
    echo "✅ Setup complete. Run 'npm run dev' when you're ready to start developing."
fi