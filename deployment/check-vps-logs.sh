#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Check recent errors in Laravel log
echo "==================== RECENT LARAVEL ERRORS ===================="
echo "📂 Project root: $PROJECT_ROOT"
echo ""

# Get last 200 lines and filter for error patterns
tail -200 "$PROJECT_ROOT/storage/logs/laravel.log" | grep -A 20 "^\[" | tail -100

echo ""
echo "==================== GOOGLE CALENDAR RELATED ===================="
echo ""

# Look for Google Calendar specific errors
tail -200 "$PROJECT_ROOT/storage/logs/laravel.log" | grep -i "google\|calendar\|booking"

echo ""
echo "==================== MOST RECENT ERROR ===================="
echo ""

# Get the last error entry
tail -200 "$PROJECT_ROOT/storage/logs/laravel.log" | grep -A 30 "ERROR" | tail -40
