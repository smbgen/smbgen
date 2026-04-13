#!/usr/bin/env bash

# Setup Node.js 24.5 for Windows - CRITICAL for npm install compatibility
# Run this script if you're having npm install issues on Windows

set -euo pipefail

echo "🔧 Node.js 24.5 Setup for Windows"
echo "=================================="
echo ""
echo "🚨 CRITICAL: Node.js 24.5 is required for npm install to work on Windows as of October 2025"
echo "   Newer versions (25+) and older versions cause npm install failures"
echo ""

# Check current Node.js version
if command -v node >/dev/null 2>&1; then
    CURRENT_VERSION=$(node -v)
    echo "📍 Current Node.js version: $CURRENT_VERSION"
    
    if [[ "$CURRENT_VERSION" == v24.5* ]]; then
        echo "✅ You already have Node.js 24.5 - you're all set!"
        echo ""
        echo "🧪 Test npm install:"
        echo "   cd your-project && npm install"
        exit 0
    else
        echo "⚠️  You have $CURRENT_VERSION but need v24.5.x"
    fi
else
    echo "❌ Node.js not installed"
fi

echo ""
echo "🔍 Checking for nvm (Node Version Manager)..."

# Check if nvm is available
if command -v nvm >/dev/null 2>&1; then
    echo "✅ nvm is available"
    
    # Install Node.js 24.5
    echo "📦 Installing Node.js 24.5..."
    nvm install 24.5
    
    # Set as default
    echo "🔧 Setting Node.js 24.5 as default..."
    nvm alias default 24.5
    
    # Use Node.js 24.5
    echo "🔄 Switching to Node.js 24.5..."
    nvm use 24.5
    
    # Verify installation
    if command -v node >/dev/null 2>&1; then
        NEW_VERSION=$(node -v)
        echo "✅ Successfully installed: $NEW_VERSION"
        
        if [[ "$NEW_VERSION" == v24.5* ]]; then
            echo "✅ Node.js 24.5 is now active and ready!"
        else
            echo "❌ Something went wrong - version is still $NEW_VERSION"
            exit 1
        fi
    else
        echo "❌ Node.js installation failed"
        exit 1
    fi
    
elif [[ -f "$HOME/.nvm/nvm.sh" ]]; then
    echo "🔧 nvm found but not loaded, loading it..."
    # shellcheck source=/dev/null
    source "$HOME/.nvm/nvm.sh"
    
    if command -v nvm >/dev/null 2>&1; then
        echo "✅ nvm loaded successfully"
        # Repeat the installation process
        nvm install 24.5
        nvm alias default 24.5
        nvm use 24.5
        
        NEW_VERSION=$(node -v)
        echo "✅ Successfully installed: $NEW_VERSION"
    else
        echo "❌ Failed to load nvm"
        exit 1
    fi
else
    echo "❌ nvm not found - you need to install it first"
    echo ""
    echo "📋 Install nvm for Windows:"
    echo "   1. Download nvm-windows from: https://github.com/coreybutler/nvm-windows"
    echo "   2. Run the installer (nvm-setup.exe)"
    echo "   3. Restart your terminal/Git Bash"
    echo "   4. Run this script again"
    echo ""
    echo "📋 Alternative - Direct Node.js 24.5 install:"
    echo "   1. Uninstall current Node.js from Windows Programs"
    echo "   2. Download Node.js 24.5.x from: https://nodejs.org/dist/v24.5.0/"
    echo "   3. Install the Windows .msi package"
    echo "   4. Restart Git Bash and verify: node --version"
    exit 1
fi

echo ""
echo "🧪 Testing Node.js and npm:"
echo "  Node.js version: $(node -v)"
echo "  npm version: $(npm -v)"

echo ""
echo "✅ Node.js 24.5 setup complete!"
echo ""
echo "📋 Next steps:"
echo "   1. Restart your terminal/Git Bash"
echo "   2. Verify version: node --version"
echo "   3. Try npm install in your project"
echo "   4. Run the main setup script: bash scripts/setup-windows-herd-buildproject-and-run-dev.sh"
echo ""
echo "💡 If you still have npm install issues:"
echo "   - Make sure you're in a new terminal session"
echo "   - Run: npm cache clean --force"
echo "   - Try: npm install --prefer-online"