#!/bin/bash

# MaintainXtra Trial System Cron Jobs Setup
# This script sets up the necessary cron jobs for trial management

echo "Setting up MaintainXtra Trial System Cron Jobs..."

# Get the current directory (assuming this script is in the project root)
PROJECT_PATH=$(pwd)

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from your Laravel project root directory."
    exit 1
fi

echo "Project path: $PROJECT_PATH"

# Create a temporary crontab file
TEMP_CRONTAB=$(mktemp)

# Export current crontab
crontab -l > "$TEMP_CRONTAB" 2>/dev/null || echo "# MaintainXtra Trial System Cron Jobs" > "$TEMP_CRONTAB"

# Add comment separator
echo "" >> "$TEMP_CRONTAB"
echo "# MaintainXtra Trial System - Process trial expiration and lock accounts" >> "$TEMP_CRONTAB"

# Add cron job for processing trial expiration (daily at 2:00 AM)
echo "0 2 * * * cd $PROJECT_PATH && php artisan trials:process-expiration >> storage/logs/trial-expiration.log 2>&1" >> "$TEMP_CRONTAB"

echo "" >> "$TEMP_CRONTAB"
echo "# MaintainXtra Trial System - Send reminder emails (daily at 9:00 AM)" >> "$TEMP_CRONTAB"

# Add cron job for sending trial reminders (daily at 9:00 AM)
echo "0 9 * * * cd $PROJECT_PATH && php artisan trials:send-reminders >> storage/logs/trial-reminders.log 2>&1" >> "$TEMP_CRONTAB"

echo "" >> "$TEMP_CRONTAB"
echo "# MaintainXtra Trial System - Clean up old log files (weekly on Sunday at 3:00 AM)" >> "$TEMP_CRONTAB"

# Add cron job for cleaning up old log files (weekly on Sunday at 3:00 AM)
echo "0 3 * * 0 find $PROJECT_PATH/storage/logs -name 'trial-*.log' -mtime +30 -delete" >> "$TEMP_CRONTAB"

# Install the new crontab
crontab "$TEMP_CRONTAB"

# Clean up temporary file
rm "$TEMP_CRONTAB"

echo "✅ Cron jobs have been set up successfully!"
echo ""
echo "📋 Installed cron jobs:"
echo "   • Daily at 2:00 AM: Process trial expiration and lock accounts"
echo "   • Daily at 9:00 AM: Send trial reminder emails"
echo "   • Weekly on Sunday at 3:00 AM: Clean up old trial log files"
echo ""
echo "📁 Log files will be created in:"
echo "   • storage/logs/trial-expiration.log"
echo "   • storage/logs/trial-reminders.log"
echo ""
echo "🔍 To view current crontab: crontab -l"
echo "🔧 To edit crontab manually: crontab -e"
echo "❌ To remove all crontab entries: crontab -r"
echo ""
echo "🚀 The trial system is now fully automated!" 