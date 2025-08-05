# MaintainXtra Trial System - Linux Server Setup Guide

This guide provides complete instructions for setting up the trial system automation on your Linux server.

## 🚀 Quick Start

### Option 1: Automated Setup (Recommended)

```bash
# 1. Upload files to your server
# 2. Navigate to your Laravel project directory
cd /path/to/your/maintainxtra/project

# 3. Make scripts executable
chmod +x crontab-setup.sh
chmod +x setup-trial-systemd.sh
chmod +x check-trial-status.sh

# 4. Run the database migration
php artisan migrate

# 5. Set up cron jobs
./crontab-setup.sh

# 6. Verify setup
./check-trial-status.sh
```

### Option 2: Systemd Timers (Alternative)

```bash
# Use systemd timers instead of cron
./setup-trial-systemd.sh
```

## 📋 Prerequisites

Before setting up the trial system, ensure you have:

- [ ] Laravel application deployed and working
- [ ] Database configured and accessible
- [ ] PHP CLI available
- [ ] Proper file permissions set
- [ ] Cron service or systemd available

## 🔧 Step-by-Step Setup

### Step 1: Database Migration

```bash
# Run the migration to add trial fields
php artisan migrate
```

### Step 2: Set File Permissions

```bash
# Set proper ownership (adjust user/group as needed)
sudo chown -R www-data:www-data /path/to/your/project

# Set proper permissions
sudo chmod -R 755 /path/to/your/project
sudo chmod -R 775 /path/to/your/project/storage
```

### Step 3: Choose Automation Method

#### Method A: Cron Jobs (Traditional)

```bash
# Run the automated setup
./crontab-setup.sh
```

#### Method B: Systemd Timers (Modern)

```bash
# Run the systemd setup
./setup-trial-systemd.sh
```

### Step 4: Verify Setup

```bash
# Check the status of all components
./check-trial-status.sh
```

## 📊 Cron Jobs Overview

### Installed Cron Jobs

| Job | Schedule | Purpose | Log File |
|-----|----------|---------|----------|
| Trial Expiration | Daily 2:00 AM | Lock accounts, delete old data | `trial-expiration.log` |
| Trial Reminders | Daily 9:00 AM | Send reminder emails | `trial-reminders.log` |
| Log Cleanup | Weekly Sunday 3:00 AM | Remove old log files | N/A |

### Cron Job Details

```bash
# Trial expiration processing
0 2 * * * cd /path/to/your/project && php artisan trials:process-expiration >> storage/logs/trial-expiration.log 2>&1

# Trial reminder emails
0 9 * * * cd /path/to/your/project && php artisan trials:send-reminders >> storage/logs/trial-reminders.log 2>&1

# Log cleanup
0 3 * * 0 find /path/to/your/project/storage/logs -name 'trial-*.log' -mtime +30 -delete
```

## 🔍 Monitoring and Management

### Check Cron Service Status

```bash
# Ubuntu/Debian
sudo systemctl status cron

# CentOS/RHEL
sudo systemctl status crond
```

### View Current Crontab

```bash
crontab -l
```

### Edit Crontab

```bash
crontab -e
```

### Check Systemd Timers (if using systemd)

```bash
# List all timers
sudo systemctl list-timers

# Check specific timer status
sudo systemctl status maintainxtra-trial.timer
sudo systemctl status maintainxtra-reminders.timer
```

### Monitor Log Files

```bash
# View trial expiration logs
tail -f storage/logs/trial-expiration.log

# View trial reminder logs
tail -f storage/logs/trial-reminders.log

# Search for errors
grep -i error storage/logs/trial-*.log
```

## 🧪 Testing

### Test Commands Manually

```bash
# Test trial expiration processing
php artisan trials:process-expiration

# Test trial reminder emails
php artisan trials:send-reminders

# Check available commands
php artisan list | grep trials
```

### Test Cron Jobs

```bash
# Run cron job manually
sudo run-parts /etc/cron.daily

# Or test specific time
sudo cron -f
```

## 🚨 Troubleshooting

### Common Issues and Solutions

#### 1. Cron Jobs Not Running

**Symptoms**: No log files created, commands not executing

**Solutions**:
```bash
# Check cron service
sudo systemctl status cron

# Start cron service
sudo systemctl start cron

# Check system logs
sudo tail -f /var/log/syslog | grep CRON
```

#### 2. Permission Denied Errors

**Symptoms**: "Permission denied" in logs

**Solutions**:
```bash
# Fix ownership
sudo chown -R www-data:www-data /path/to/your/project

# Fix permissions
sudo chmod -R 755 /path/to/your/project
sudo chmod -R 775 /path/to/your/project/storage
```

#### 3. PHP Not Found

**Symptoms**: "php: command not found" in logs

**Solutions**:
```bash
# Find PHP path
which php

# Update crontab with full path
crontab -e
# Replace 'php' with '/usr/bin/php' or your PHP path
```

#### 4. Database Connection Issues

**Symptoms**: Database errors in logs

**Solutions**:
```bash
# Test database connection
php artisan tinker
# Try: DB::connection()->getPdo();

# Check .env file
cat .env | grep DB_
```

#### 5. Log Files Not Created

**Symptoms**: No log files in storage/logs

**Solutions**:
```bash
# Check storage directory exists
ls -la storage/logs/

# Create if missing
mkdir -p storage/logs

# Check disk space
df -h

# Test write permissions
touch storage/logs/test.log
```

### Debug Mode

Enable verbose output for troubleshooting:

```bash
# Add -v flag to commands in crontab
0 2 * * * cd /path/to/your/project && php artisan trials:process-expiration -v >> storage/logs/trial-expiration.log 2>&1
```

## 📈 Performance Monitoring

### Check Resource Usage

```bash
# Monitor CPU and memory usage
htop

# Check disk usage
df -h

# Monitor log file growth
watch -n 5 'ls -lh storage/logs/trial-*.log'
```

### Log Rotation

Set up log rotation to prevent log files from growing too large:

```bash
# Create logrotate configuration
sudo nano /etc/logrotate.d/maintainxtra

# Add configuration
/path/to/your/project/storage/logs/trial-*.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

## 🔒 Security Considerations

### File Permissions

```bash
# Secure sensitive files
chmod 600 .env
chmod 600 storage/logs/*.log

# Restrict access to storage
chmod 750 storage/
chmod 750 storage/logs/
```

### Cron Security

```bash
# Restrict cron access
sudo nano /etc/cron.allow
# Add only necessary users

# Check cron logs for suspicious activity
sudo tail -f /var/log/syslog | grep CRON
```

## 📞 Support and Maintenance

### Regular Maintenance Tasks

```bash
# Weekly: Check log files
tail -n 50 storage/logs/trial-*.log

# Monthly: Review cron job performance
grep "Completed" storage/logs/trial-*.log | tail -20

# Quarterly: Update and test commands
php artisan list | grep trials
```

### Backup Considerations

```bash
# Backup trial-related data
mysqldump -u username -p database_name users > trial_users_backup.sql

# Backup log files
tar -czf trial_logs_backup_$(date +%Y%m%d).tar.gz storage/logs/trial-*.log
```

## ✅ Verification Checklist

Before going live, verify:

- [ ] Database migration applied successfully
- [ ] Trial commands work when run manually
- [ ] Cron service is running
- [ ] Crontab entries are added correctly
- [ ] Log files are being created
- [ ] File permissions are set correctly
- [ ] PHP path is correct in crontab
- [ ] Timezone is set correctly
- [ ] Storage directory is writable
- [ ] No errors in system logs

## 🎉 Going Live

Once everything is verified:

1. **Monitor closely** for the first few days
2. **Check logs** daily to ensure jobs are running
3. **Test user flows** to ensure trial system works
4. **Set up alerts** for any failures
5. **Document** any customizations made

## 📚 Additional Resources

- [Laravel Task Scheduling](https://laravel.com/docs/scheduling)
- [Cron Job Management](https://help.ubuntu.com/community/CronHowto)
- [Systemd Timer Documentation](https://www.freedesktop.org/software/systemd/man/systemd.timer.html)
- [Linux Log Management](https://www.loggly.com/ultimate-guide/linux-logging-basics/)

Your trial system is now fully automated and ready for production! 🚀 