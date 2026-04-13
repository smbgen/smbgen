#!/bin/bash

echo "🔍 Checking for Vite/Node processes..."
echo ""

# Check for running Vite processes
echo "Running Vite processes:"
ps aux | grep -E 'vite|node.*dev' | grep -v grep || echo "  None found"
echo ""

# Check for Node processes
echo "All Node processes:"
ps aux | grep node | grep -v grep || echo "  None found"
echo ""

# Check npm/node versions
echo "📦 Node version:"
node --version 2>/dev/null || echo "  Node not found"
echo ""

echo "📦 npm version:"
npm --version 2>/dev/null || echo "  npm not found"
echo ""

# Check if build files exist
echo "📁 Built assets:"
if [ -d "public/build" ]; then
    echo "  ✅ public/build directory exists"
    ls -lh public/build/*.css public/build/*.js 2>/dev/null | head -5 || echo "  No built files found"
else
    echo "  ❌ public/build directory does not exist"
fi
echo ""

# Check .env for APP_ENV
echo "🔧 Environment:"
grep "APP_ENV" .env 2>/dev/null || echo "  APP_ENV not set"
echo ""

echo "💡 Tips:"
echo "  - If Vite dev server is running, stop it with: pkill -f 'vite'"
echo "  - To kill all node processes: pkill node"
echo "  - Build assets with: npm run build"
echo "  - Check build output in: public/build/"
