#!/bin/bash

# Laravel Scheduler Cron Setup Script for Ubuntu
# This script automatically sets up Laravel scheduler to run every minute

echo "=========================================="
echo "Laravel Scheduler Cron Setup"
echo "=========================================="
echo ""

# Get the current directory (project root)
PROJECT_PATH=$(pwd)

echo "Project path: $PROJECT_PATH"
echo ""

# Check if artisan exists
if [ ! -f "$PROJECT_PATH/artisan" ]; then
    echo "❌ Error: artisan file not found!"
    echo "Please run this script from your Laravel project root directory."
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Get PHP path
PHP_PATH=$(which php)
echo "PHP path: $PHP_PATH"
echo ""

# Create the cron entry
CRON_ENTRY="* * * * * cd $PROJECT_PATH && $PHP_PATH artisan schedule:run >> /dev/null 2>&1"

echo "Cron entry to be added:"
echo "$CRON_ENTRY"
echo ""

# Check if cron entry already exists
if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
    echo "⚠️  Cron entry already exists!"
    echo ""
    if [[ "$1" == "--force" ]]; then
        echo "Forcing replacement..."
    else
        echo "Current crontab:"
        crontab -l | grep "artisan schedule:run"
        echo ""
        read -p "Do you want to replace it? (y/n): " -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "Setup cancelled."
            exit 0
        fi
    fi
    # Remove old entry
    crontab -l | grep -v "artisan schedule:run" | crontab -
    echo "✅ Old entry removed"
fi

# Add new cron entry
(crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -

echo ""
echo "=========================================="
echo "✅ Cron setup completed successfully!"
echo "=========================================="
echo ""
echo "Scheduler will run every minute."
echo ""
echo "To verify, run:"
echo "  crontab -l"
echo ""
echo "To test manually, run:"
echo "  php artisan schedule:run"
echo ""
echo "To view scheduled tasks, run:"
echo "  php artisan schedule:list"
echo ""
echo "Logs will be written to:"
echo "  $PROJECT_PATH/storage/logs/laravel.log"
echo ""
