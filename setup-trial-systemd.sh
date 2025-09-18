#!/bin/bash

# MaintainXtra Trial System - Systemd Timer Setup
# This script sets up systemd timers for trial management (alternative to cron)

echo "Setting up MaintainXtra Trial System with Systemd Timers..."

# Get the current directory
PROJECT_PATH=$(pwd)

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from your Laravel project root directory."
    exit 1
fi

echo "Project path: $PROJECT_PATH"

# Create systemd service files
echo "Creating systemd service files..."

# Trial expiration service
cat > /tmp/maintainxtra-trial.service << EOF
[Unit]
Description=MaintainXtra Trial System Service
After=network.target

[Service]
Type=oneshot
User=www-data
Group=www-data
WorkingDirectory=$PROJECT_PATH
ExecStart=/usr/bin/php artisan trials:process-expiration
StandardOutput=append:$PROJECT_PATH/storage/logs/trial-expiration.log
StandardError=append:$PROJECT_PATH/storage/logs/trial-expiration.log
Environment=APP_ENV=production

[Install]
WantedBy=multi-user.target
EOF

# Trial reminders service
cat > /tmp/maintainxtra-reminders.service << EOF
[Unit]
Description=MaintainXtra Trial Reminder Service
After=network.target

[Service]
Type=oneshot
User=www-data
Group=www-data
WorkingDirectory=$PROJECT_PATH
ExecStart=/usr/bin/php artisan trials:send-reminders
StandardOutput=append:$PROJECT_PATH/storage/logs/trial-reminders.log
StandardError=append:$PROJECT_PATH/storage/logs/trial-reminders.log
Environment=APP_ENV=production

[Install]
WantedBy=multi-user.target
EOF

# Trial expiration timer
cat > /tmp/maintainxtra-trial.timer << EOF
[Unit]
Description=Run MaintainXtra Trial Expiration Daily at 2:00 AM
Requires=maintainxtra-trial.service

[Timer]
OnCalendar=*-*-* 02:00:00
Persistent=true

[Install]
WantedBy=timers.target
EOF

# Trial reminders timer
cat > /tmp/maintainxtra-reminders.timer << EOF
[Unit]
Description=Run MaintainXtra Trial Reminders Daily at 9:00 AM
Requires=maintainxtra-reminders.service

[Timer]
OnCalendar=*-*-* 09:00:00
Persistent=true

[Install]
WantedBy=timers.target
EOF

# Copy service files to systemd directory
echo "Installing systemd service files..."
sudo cp /tmp/maintainxtra-trial.service /etc/systemd/system/
sudo cp /tmp/maintainxtra-reminders.service /etc/systemd/system/
sudo cp /tmp/maintainxtra-trial.timer /etc/systemd/system/
sudo cp /tmp/maintainxtra-reminders.timer /etc/systemd/system/

# Reload systemd
sudo systemctl daemon-reload

# Enable and start timers
echo "Enabling and starting timers..."
sudo systemctl enable maintainxtra-trial.timer
sudo systemctl enable maintainxtra-reminders.timer
sudo systemctl start maintainxtra-trial.timer
sudo systemctl start maintainxtra-reminders.timer

# Clean up temporary files
rm /tmp/maintainxtra-trial.service
rm /tmp/maintainxtra-reminders.service
rm /tmp/maintainxtra-trial.timer
rm /tmp/maintainxtra-reminders.timer

echo "✅ Systemd timers have been set up successfully!"
echo ""
echo "📋 Installed systemd timers:"
echo "   • maintainxtra-trial.timer - Daily at 2:00 AM: Process trial expiration"
echo "   • maintainxtra-reminders.timer - Daily at 9:00 AM: Send trial reminders"
echo ""
echo "🔍 Management commands:"
echo "   • Check timer status: sudo systemctl status maintainxtra-trial.timer"
echo "   • List all timers: sudo systemctl list-timers"
echo "   • Enable timers: sudo systemctl enable maintainxtra-*.timer"
echo "   • Disable timers: sudo systemctl disable maintainxtra-*.timer"
echo "   • Start timers: sudo systemctl start maintainxtra-*.timer"
echo "   • Stop timers: sudo systemctl stop maintainxtra-*.timer"
echo ""
echo "📁 Log files will be created in:"
echo "   • $PROJECT_PATH/storage/logs/trial-expiration.log"
echo "   • $PROJECT_PATH/storage/logs/trial-reminders.log"
echo ""
echo "🚀 The trial system is now fully automated with systemd timers!" 