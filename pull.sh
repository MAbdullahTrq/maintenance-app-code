#!/bin/bash
cd /var/www/maintenance-app || exit 1
git pull
npm run build
# Set permissions
sudo chown -R www-data:www-data /var/www/maintenance-app/storage /var/www/maintenance-app/bootstrap/cache                                                             
sudo chmod -R 755 /var/www/maintenance-app/storage /var/www/maintenance-app/bootstrap/cache
# Clear all caches (this is the most important one)
php artisan optimize:clear

# If the above doesn't work, run these individually:
php artisan config:clear
php artisan route:clear  
php artisan view:clear

# Then rebuild the optimized caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
