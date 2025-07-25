# Deploying the MaintainXtra on Ubuntu CLI

## 1. System Requirements

First, install all required system packages:

```bash
sudo apt update
sudo apt install -y git curl nginx mysql-server php8.2 php8.2-fpm php8.2-mysql \
  php8.2-common php8.2-cli php8.2-mbstring php8.2-xml php8.2-zip php8.2-curl \
  php8.2-gd composer unzip nodejs npm
```

## 2. Set Up MySQL

```bash
sudo mysql_secure_installation
```

Create a database and user:

```bash
sudo mysql -e "CREATE DATABASE maintenance_app;"
sudo mysql -e "CREATE USER 'maintenance_user'@'localhost' IDENTIFIED BY 'your_secure_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON maintenance_app.* TO 'maintenance_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

## 3. Clone the Repository

```bash
cd /var/www
sudo git clone https://github.com/yourusername/maintenance-app-code.git maintenance-app
sudo chown -R www-data:www-data maintenance-app
cd maintenance-app
```

## 4. Install Dependencies

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build
```

## 5. Configure the Application

```bash
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
```

Edit the .env file with your database and PayPal settings:

```bash
sudo nano .env
```

Update the following:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maintenance_app
DB_USERNAME=maintenance_user
DB_PASSWORD=your_secure_password

# Configure PayPal settings
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_SANDBOX=false  # Set to true for testing environment
```

## 6. Set Up the Database

```bash
sudo -u www-data php artisan migrate --seed
```

## 7. Configure Storage Access

```bash
sudo -u www-data php artisan storage:link
sudo chmod -R 775 storage bootstrap/cache
```

## 8. Set Up Nginx

Create a new Nginx site configuration:

```bash
sudo nano /etc/nginx/sites-available/maintenance-app
```

Add the following configuration:

```
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/maintenance-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/maintenance-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 9. Set Up Queue Worker (Optional, for background jobs)

Create a systemd service for the Laravel queue worker:

```bash
sudo nano /etc/systemd/system/laravel-queue.service
```

Add the following content:

```
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/maintenance-app
ExecStart=/usr/bin/php /var/www/maintenance-app/artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
```

## 10. Set Up Task Scheduler

Add Laravel's scheduler to the crontab:

```bash
sudo -u www-data crontab -e
```

Add the following line:

```
* * * * * cd /var/www/maintenance-app && php artisan schedule:run >> /dev/null 2>&1
```

## 11. Secure Your Application (Optional but Recommended)

Set up SSL with Let's Encrypt:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

## 12. Final Steps

Check permissions one last time:

```bash
sudo chown -R www-data:www-data /var/www/maintenance-app
sudo find /var/www/maintenance-app -type f -exec chmod 644 {} \;
sudo find /var/www/maintenance-app -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/maintenance-app/storage
sudo chmod -R 775 /var/www/maintenance-app/bootstrap/cache
```

## Default Login Credentials

After deployment, you can log in with these default credentials:

- **Super Property Manager**:
  - Email: admin@example.com
  - Password: password

- **Property Manager**:
  - Email: manager@example.com
  - Password: password

- **Technicians**:
  - Email: john@example.com
  - Password: password
  
  - Email: jane@example.com
  - Password: password

**Important**: Change these default passwords immediately after first login!
