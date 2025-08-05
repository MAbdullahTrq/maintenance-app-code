# MaintainXtra Trial System - Cron Jobs Setup Guide

This guide will help you set up the necessary cron jobs for the trial system automation on your Linux server.

## 🚀 Quick Setup (Recommended)

### Option 1: Automated Setup Script
```bash
# Make the script executable
chmod +x crontab-setup.sh

# Run the setup script
./crontab-setup.sh
```

### Option 2: Manual Setup
Follow the steps below to set up cron jobs manually.

## 📋 Manual Cron Setup

### Step 1: Access Crontab
```bash
# Open crontab for editing
crontab -e
```

### Step 2: Add Trial System Cron Jobs

Add the following lines to your crontab (replace `/path/to/your/project` with your actual project path):

```bash
# MaintainXtra Trial System - Process trial expiration and lock accounts
# Runs daily at 2:00 AM
0 2 * * * cd /path/to/your/project && php artisan trials:process-expiration >> storage/logs/trial-expiration.log 2>&1

# MaintainXtra Trial System - Send reminder emails
# Runs daily at 9:00 AM
0 9 * * * cd /path/to/your/project && php artisan trials:send-reminders >> storage/logs/trial-reminders.log 2>&1

# MaintainXtra Trial System - Clean up old log files
# Runs weekly on Sunday at 3:00 AM
0 3 * * 0 find /path/to/your/project/storage/logs -name 'trial-*.log' -mtime +30 -delete
```

### Step 3: Save and Exit
- Press `Ctrl + X` to exit
- Press `Y` to save changes
- Press `Enter` to confirm

## 🔧 Cron Job Details

### 1. Trial Expiration Processing
- **Schedule**: Daily at 2:00 AM
- **Command**: `php artisan trials:process-expiration`
- **Purpose**: 
  - Locks accounts past grace period
  - Deletes accounts past 90-day retention
  - Logs all actions

### 2. Trial Reminder Emails
- **Schedule**: Daily at 9:00 AM
- **Command**: `php artisan trials:send-reminders`
- **Purpose**:
  - Sends reminder emails to users with expired trials
  - Tracks email sending to prevent spam
  - Sends at specific intervals (Day 37, 60, 85)

### 3. Log Cleanup
- **Schedule**: Weekly on Sunday at 3:00 AM
- **Command**: `find /path/to/your/project/storage/logs -name 'trial-*.log' -mtime +30 -delete`
- **Purpose**: Removes trial log files older than 30 days

## 📁 Log Files

The cron jobs will create the following log files:
- `storage/logs/trial-expiration.log` - Trial expiration processing logs
- `storage/logs/trial-reminders.log` - Trial reminder email logs

## 🔍 Monitoring and Management

### View Current Crontab
```bash
crontab -l
```

### Edit Crontab
```bash
crontab -e
```

### Remove All Crontab Entries
```bash
crontab -r
```

### Check Cron Service Status
```bash
# Ubuntu/Debian
sudo systemctl status cron

# CentOS/RHEL
sudo systemctl status crond
```

### Start/Stop Cron Service
```bash
# Ubuntu/Debian
sudo systemctl start cron
sudo systemctl stop cron

# CentOS/RHEL
sudo systemctl start crond
sudo systemctl stop crond
```

## 🧪 Testing Cron Jobs

### Test Trial Expiration Command
```bash
cd /path/to/your/project
php artisan trials:process-expiration
```

### Test Trial Reminders Command
```bash
cd /path/to/your/project
php artisan trials:send-reminders
```

### Check Log Files
```bash
# View trial expiration logs
tail -f storage/logs/trial-expiration.log

# View trial reminder logs
tail -f storage/logs/trial-reminders.log
```

## ⚠️ Important Notes

### 1. File Permissions
Ensure your web server has proper permissions:
```bash
# Set proper ownership
sudo chown -R www-data:www-data /path/to/your/project

# Set proper permissions
sudo chmod -R 755 /path/to/your/project
sudo chmod -R 775 /path/to/your/project/storage
```

### 2. PHP Path
If PHP is not in your system PATH, use the full path:
```bash
# Find PHP path
which php

# Use full path in crontab
0 2 * * * cd /path/to/your/project && /usr/bin/php artisan trials:process-expiration >> storage/logs/trial-expiration.log 2>&1
```

### 3. Environment Variables
If your Laravel app needs specific environment variables, add them to the crontab:
```bash
0 2 * * * cd /path/to/your/project && export APP_ENV=production && php artisan trials:process-expiration >> storage/logs/trial-expiration.log 2>&1
```

### 4. Timezone
Ensure your server timezone is set correctly:
```bash
# Check current timezone
date

# Set timezone if needed
sudo timedatectl set-timezone Your/Timezone
```

## 🚨 Troubleshooting

### Common Issues

1. **Cron jobs not running**
   - Check if cron service is running: `sudo systemctl status cron`
   - Check crontab syntax: `crontab -l`
   - Check system logs: `sudo tail -f /var/log/syslog`

2. **Permission denied errors**
   - Check file permissions: `ls -la /path/to/your/project`
   - Ensure proper ownership: `sudo chown -R www-data:www-data /path/to/your/project`

3. **PHP not found**
   - Use full PHP path: `which php`
   - Update crontab with full path

4. **Log files not created**
   - Check storage directory permissions
   - Ensure storage/logs directory exists
   - Check disk space: `df -h`

### Debug Mode
To run commands in debug mode, add `-v` flag:
```bash
0 2 * * * cd /path/to/your/project && php artisan trials:process-expiration -v >> storage/logs/trial-expiration.log 2>&1
```

## 📞 Support

If you encounter issues:
1. Check the log files in `storage/logs/`
2. Verify cron service is running
3. Test commands manually first
4. Check file permissions and ownership

## ✅ Verification Checklist

- [ ] Cron service is running
- [ ] Crontab entries are added correctly
- [ ] Project path is correct
- [ ] File permissions are set properly
- [ ] PHP path is correct
- [ ] Log files are being created
- [ ] Commands work when run manually
- [ ] Timezone is set correctly

Your trial system is now fully automated! 🎉 