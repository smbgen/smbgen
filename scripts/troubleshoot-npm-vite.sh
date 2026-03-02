#!/usr/bin/env bash

# Troubleshoot npm/Vite issues in ClientBridge Laravel project
# Run this if npm run dev/build fails with "vite not recognized"

echo "🔧 npm/Vite Troubleshooting for ClientBridge"
echo "==========================================="
echo ""

# Check Node.js and npm
echo "📋 Current Environment:"
echo "  User: $USER"
echo "  Shell: $SHELL"
echo "  Working Directory: $(pwd)"
if command -v node >/dev/null 2>&1; then
    NODE_VERSION=$(node -v)
    echo "  Node.js: $NODE_VERSION"
    if [[ "$NODE_VERSION" != v24.5* ]]; then
        echo "  🚨 CRITICAL: Node.js 24.5 required for Windows npm install (current: $NODE_VERSION)"
        echo "  💡 Fix with: nvm install 24.5 && nvm alias default 24.5 && nvm use 24.5"
    else
        echo "  ✅ Node.js 24.5 detected (correct version for Windows)"
    fi
else
    echo "  Node.js: Not installed"
fi
echo ""

# Check package.json validity
echo "🔍 Checking package.json:"
if [[ -f "package.json" ]]; then
    echo "  ✅ package.json exists"
    
    # Test JSON validity
    if node -e "JSON.parse(require('fs').readFileSync('package.json', 'utf8'))" 2>/dev/null; then
        echo "  ✅ package.json is valid JSON"
    else
        echo "  ❌ package.json has invalid JSON syntax"
        echo "     Fix: Check for trailing commas, missing quotes, or PHP syntax mixed in"
    fi
    
    # Check scripts
    if grep -q '"dev".*"vite"' package.json; then
        echo "  ✅ dev script found: $(grep -o '"dev".*' package.json | head -1)"
    else
        echo "  ❌ dev script not found or incorrect"
    fi
    
    if grep -q '"build".*"vite build"' package.json; then
        echo "  ✅ build script found: $(grep -o '"build".*' package.json | head -1)"
    else
        echo "  ❌ build script not found or incorrect"
    fi
else
    echo "  ❌ package.json not found"
fi
echo ""

# Check node_modules
echo "🔍 Checking node_modules:"
if [[ -d "node_modules" ]]; then
    echo "  ✅ node_modules directory exists"
    
    # Check Vite installation
    if [[ -f "node_modules/.bin/vite" ]] || [[ -f "node_modules/.bin/vite.cmd" ]]; then
        echo "  ✅ Vite executable found in node_modules/.bin/"
        
        # Test Vite directly
        if npx vite --version >/dev/null 2>&1; then
            VITE_VERSION=$(npx vite --version 2>/dev/null)
            echo "  ✅ Vite works: $VITE_VERSION"
        else
            echo "  ❌ Vite installed but doesn't work"
        fi
    else
        echo "  ❌ Vite executable not found in node_modules/.bin/"
        echo "     Contents of node_modules/.bin/:"
        if [[ -d "node_modules/.bin" ]]; then
            ls -la node_modules/.bin/ | grep -E "(vite|laravel)" | head -5
        else
            echo "     node_modules/.bin/ doesn't exist"
        fi
    fi
    
    # Check if Vite is in package-lock.json/installed
    if npm list vite >/dev/null 2>&1; then
        VITE_INFO=$(npm list vite --depth=0 2>/dev/null | grep vite || echo "Not in direct dependencies")
        echo "  📦 Vite package: $VITE_INFO"
    else
        echo "  ❌ Vite not found in installed packages"
    fi
else
    echo "  ❌ node_modules directory not found"
    echo "     Run: npm install"
fi
echo ""

# Check specific Vite config
echo "🔍 Checking Vite configuration:"
if [[ -f "vite.config.js" ]]; then
    echo "  ✅ vite.config.js exists"
else
    echo "  ⚠️  vite.config.js not found (using default config)"
fi

if [[ -f "resources/js/app.js" ]]; then
    echo "  ✅ resources/js/app.js exists"
else
    echo "  ❌ resources/js/app.js not found"
fi

if [[ -f "resources/css/app.css" ]]; then
    echo "  ✅ resources/css/app.css exists"
else
    echo "  ❌ resources/css/app.css not found"
fi
echo ""

# Test npm scripts
echo "🧪 Testing npm scripts:"
echo "  Testing 'npm run build --dry-run':"
if npm run build --dry-run >/dev/null 2>&1; then
    echo "  ✅ npm run build would work"
else
    echo "  ❌ npm run build would fail"
fi

echo "  Testing 'npx vite --version':"
if npx vite --version >/dev/null 2>&1; then
    echo "  ✅ npx vite works: $(npx vite --version)"
else
    echo "  ❌ npx vite fails"
fi
echo ""

# Solutions
echo "🔧 Solutions:"
echo ""

if ! command -v node >/dev/null 2>&1; then
    echo "❌ PROBLEM: Node.js not installed"
    echo "   SOLUTION: Install Node.js 24.5 specifically"
    echo "   1. Install nvm: https://github.com/coreybutler/nvm-windows"
    echo "   2. Run: nvm install 24.5 && nvm alias default 24.5 && nvm use 24.5"
    echo ""
elif command -v node >/dev/null 2>&1; then
    NODE_VERSION=$(node -v)
    if [[ "$NODE_VERSION" != v24.5* ]]; then
        echo "❌ PROBLEM: Wrong Node.js version ($NODE_VERSION)"
        echo "   🚨 CRITICAL: Node.js 24.5 required for Windows npm install as of October 2025"
        echo "   SOLUTION: Switch to Node.js 24.5"
        echo "   1. nvm install 24.5"
        echo "   2. nvm alias default 24.5"
        echo "   3. nvm use 24.5"
        echo "   4. node --version (should show v24.5.x)"
        echo ""
    fi
fi

if [[ ! -f "package.json" ]]; then
    echo "❌ PROBLEM: package.json missing"
    echo "   SOLUTION: This should be a Laravel project with package.json"
    echo ""
elif ! node -e "JSON.parse(require('fs').readFileSync('package.json', 'utf8'))" 2>/dev/null; then
    echo "❌ PROBLEM: package.json has invalid JSON"
    echo "   SOLUTION: Fix JSON syntax in package.json (check for PHP syntax mixed in)"
    echo ""
fi

if [[ ! -d "node_modules" ]]; then
    echo "❌ PROBLEM: node_modules missing"
    echo "   SOLUTION: Run 'npm install'"
    echo ""
elif ! npm list vite >/dev/null 2>&1; then
    echo "❌ PROBLEM: Vite not installed"
    echo "   SOLUTION: Run 'npm install vite --save-dev'"
    echo ""
fi

if [[ -d "node_modules" ]] && ! [[ -f "node_modules/.bin/vite" || -f "node_modules/.bin/vite.cmd" ]]; then
    echo "❌ PROBLEM: Vite installed but executable missing"
    echo "   SOLUTION: Delete node_modules and reinstall:"
    echo "   rm -rf node_modules package-lock.json"
    echo "   npm install"
    echo ""
fi

echo "💡 QUICK FIXES:"
echo "1. Clean reinstall: rm -rf node_modules package-lock.json && npm install"
echo "2. Use npx directly: npx vite (for dev) or npx vite build"
echo "3. Install Vite globally: npm install -g vite"
echo "4. Check Windows PATH for npm global binaries"
echo ""

echo "🚀 MANUAL TEST:"
echo "Try running these commands one by one:"
echo "  npm install"
echo "  npx vite --version"
echo "  npx vite build"
echo "  npm run dev"