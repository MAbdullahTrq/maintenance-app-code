#!/bin/bash

# MaintainXtra Trial System - Status Checker
# This script checks the status of the trial system and cron jobs

echo "🔍 MaintainXtra Trial System Status Check"
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get project path
PROJECT_PATH=$(pwd)

echo -e "${BLUE}📁 Project Path:${NC} $PROJECT_PATH"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found. Please run this script from your Laravel project root directory.${NC}"
    exit 1
fi

# Check Laravel installation
echo -e "${BLUE}🔧 Laravel Status:${NC}"
if php artisan --version > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Laravel is working${NC}"
else
    echo -e "${RED}❌ Laravel is not working${NC}"
fi
echo ""

# Check trial commands
echo -e "${BLUE}⚙️ Trial Commands:${NC}"
if php artisan list | grep -q "trials:process-expiration"; then
    echo -e "${GREEN}✅ trials:process-expiration command available${NC}"
else
    echo -e "${RED}❌ trials:process-expiration command not found${NC}"
fi

if php artisan list | grep -q "trials:send-reminders"; then
    echo -e "${GREEN}✅ trials:send-reminders command available${NC}"
else
    echo -e "${RED}❌ trials:send-reminders command not found${NC}"
fi
echo ""

# Check cron service
echo -e "${BLUE}⏰ Cron Service:${NC}"
if systemctl is-active --quiet cron; then
    echo -e "${GREEN}✅ Cron service is running${NC}"
elif systemctl is-active --quiet crond; then
    echo -e "${GREEN}✅ Crond service is running${NC}"
else
    echo -e "${RED}❌ Cron service is not running${NC}"
fi
echo ""

# Check systemd timers
echo -e "${BLUE}🕐 Systemd Timers:${NC}"
if systemctl is-active --quiet maintainxtra-trial.timer; then
    echo -e "${GREEN}✅ maintainxtra-trial.timer is active${NC}"
else
    echo -e "${YELLOW}⚠️ maintainxtra-trial.timer is not active${NC}"
fi

if systemctl is-active --quiet maintainxtra-reminders.timer; then
    echo -e "${GREEN}✅ maintainxtra-reminders.timer is active${NC}"
else
    echo -e "${YELLOW}⚠️ maintainxtra-reminders.timer is not active${NC}"
fi
echo ""

# Check crontab entries
echo -e "${BLUE}📋 Crontab Entries:${NC}"
CRONTAB_ENTRIES=$(crontab -l 2>/dev/null | grep -c "trials:")
if [ "$CRONTAB_ENTRIES" -gt 0 ]; then
    echo -e "${GREEN}✅ Found $CRONTAB_ENTRIES trial-related crontab entries${NC}"
    crontab -l | grep "trials:" | while read line; do
        echo "   $line"
    done
else
    echo -e "${YELLOW}⚠️ No trial-related crontab entries found${NC}"
fi
echo ""

# Check log files
echo -e "${BLUE}📄 Log Files:${NC}"
if [ -f "storage/logs/trial-expiration.log" ]; then
    echo -e "${GREEN}✅ trial-expiration.log exists${NC}"
    echo "   Last modified: $(stat -c %y storage/logs/trial-expiration.log)"
    echo "   Size: $(du -h storage/logs/trial-expiration.log | cut -f1)"
else
    echo -e "${YELLOW}⚠️ trial-expiration.log does not exist${NC}"
fi

if [ -f "storage/logs/trial-reminders.log" ]; then
    echo -e "${GREEN}✅ trial-reminders.log exists${NC}"
    echo "   Last modified: $(stat -c %y storage/logs/trial-reminders.log)"
    echo "   Size: $(du -h storage/logs/trial-reminders.log | cut -f1)"
else
    echo -e "${YELLOW}⚠️ trial-reminders.log does not exist${NC}"
fi
echo ""

# Check storage permissions
echo -e "${BLUE}🔐 Storage Permissions:${NC}"
if [ -w "storage/logs" ]; then
    echo -e "${GREEN}✅ storage/logs is writable${NC}"
else
    echo -e "${RED}❌ storage/logs is not writable${NC}"
fi
echo ""

# Test trial commands
echo -e "${BLUE}🧪 Testing Trial Commands:${NC}"
echo "Testing trials:process-expiration..."
if timeout 30s php artisan trials:process-expiration > /dev/null 2>&1; then
    echo -e "${GREEN}✅ trials:process-expiration command works${NC}"
else
    echo -e "${RED}❌ trials:process-expiration command failed${NC}"
fi

echo "Testing trials:send-reminders..."
if timeout 30s php artisan trials:send-reminders > /dev/null 2>&1; then
    echo -e "${GREEN}✅ trials:send-reminders command works${NC}"
else
    echo -e "${RED}❌ trials:send-reminders command failed${NC}"
fi
echo ""

# Check database migration
echo -e "${BLUE}🗄️ Database Migration:${NC}"
if php artisan migrate:status | grep -q "trial_fields_to_users_table"; then
    echo -e "${GREEN}✅ Trial migration is applied${NC}"
else
    echo -e "${RED}❌ Trial migration is not applied${NC}"
    echo "   Run: php artisan migrate"
fi
echo ""

# Summary
echo -e "${BLUE}📊 Summary:${NC}"
echo "=========================================="

# Count issues
ISSUES=0
if ! systemctl is-active --quiet cron && ! systemctl is-active --quiet crond; then
    ((ISSUES++))
fi
if ! systemctl is-active --quiet maintainxtra-trial.timer && ! systemctl is-active --quiet maintainxtra-reminders.timer; then
    ((ISSUES++))
fi
if [ "$CRONTAB_ENTRIES" -eq 0 ]; then
    ((ISSUES++))
fi
if [ ! -w "storage/logs" ]; then
    ((ISSUES++))
fi

if [ "$ISSUES" -eq 0 ]; then
    echo -e "${GREEN}🎉 All systems are operational!${NC}"
else
    echo -e "${YELLOW}⚠️ Found $ISSUES issue(s) that need attention${NC}"
fi

echo ""
echo -e "${BLUE}📞 Next Steps:${NC}"
echo "1. If cron/systemd is not running, start the service"
echo "2. If no crontab entries, run: ./crontab-setup.sh"
echo "3. If migration not applied, run: php artisan migrate"
echo "4. If permissions issues, run: sudo chown -R www-data:www-data ."
echo "5. Check log files for any errors"
echo ""
echo "🔍 For detailed setup instructions, see: CRON_SETUP_GUIDE.md" 