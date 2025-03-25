#!/bin/bash

# Colors for better output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print section headers
print_section() {
    echo -e "\n${GREEN}==== $1 ====${NC}\n"
}

# Function to print errors
print_error() {
    echo -e "${RED}ERROR: $1${NC}"
    exit 1
}

# Function to print warnings
print_warning() {
    echo -e "${YELLOW}WARNING: $1${NC}"
}

# Function to ask yes/no questions
ask_yes_no() {
    while true; do
        read -p "$1 (y/n): " yn
        case $yn in
            [Yy]* ) return 0;;
            [Nn]* ) return 1;;
            * ) echo "Please answer yes (y) or no (n).";;
        esac
    done
}

# Ensure script is run as root
if [[ $EUID -ne 0 ]]; then
   print_error "This script must be run as root. Try using sudo."
fi

# Get deployment parameters
print_section "Configuration"

# Domain name
read -p "Enter your domain name (e.g., maintenance.example.com): " DOMAIN_NAME
if [ -z "$DOMAIN_NAME" ]; then
    DOMAIN_NAME="localhost"
    print_warning "No domain provided. Using 'localhost' instead."
fi

# Database configuration
read -p "Enter database name [maintenance_app]: " DB_NAME
DB_NAME=${DB_NAME:-maintenance_app}

read -p "Enter database username [maintenance_user]: " DB_USER
DB_USER=${DB_USER:-maintenance_user}

read -sp "Enter database password: " DB_PASSWORD
echo
if [ -z "$DB_PASSWORD" ]; then
    DB_PASSWORD=$(openssl rand -base64 12)
    print_warning "No password provided. Generated random password: $DB_PASSWORD"
    echo "Please make note of this password!"
fi

# PayPal configuration
read -p "Enter PayPal Client ID (leave blank to skip): " PAYPAL_CLIENT_ID
read -p "Enter PayPal Client Secret (leave blank to skip): " PAYPAL_CLIENT_SECRET

# SSL configuration
if ask_yes_no "Do you want to set up SSL with Let's Encrypt?"; then
    SETUP_SSL=true
else
    SETUP_SSL=false
fi

# Repository URL
read -p "Enter the Git repository URL [https://github.com/mabdullahtrq/maintenance-app-code.git]: " REPO_URL
REPO_URL=${REPO_URL:-https://github.com/mabdullahtrq/maintenance-app-code.git}

# Installation directory
read -p "Enter installation directory [/var/www/maintenance-app]: " INSTALL_DIR
INSTALL_DIR=${INSTALL_DIR:-/var/www/maintenance-app}

# Start installation
print_section "Installing System Requirements"

# Determine Ubuntu version
UBUNTU_VERSION=$(lsb_release -rs)
echo "Detected Ubuntu version: $UBUNTU_VERSION"

# Update package repositories
apt update || print_error "Failed to update package repositories"

# Install common prerequisites
apt install -y software-properties-common apt-transport-https lsb-release ca-certificates curl || print_error "Failed to install prerequisites"

# Add PHP repository
print_section "Adding PHP Repository"
add-apt-repository -y ppa:ondrej/php || print_error "Failed to add PHP repository"
apt update || print_error "Failed to update package repositories after adding PHP repository"

# Determine PHP version to install
PHP_VERSIONS=("8.2" "8.1" "8.0" "7.4")
PHP_VERSION=""

for version in "${PHP_VERSIONS[@]}"; do
    if apt-cache show php$version > /dev/null 2>&1; then
        PHP_VERSION=$version
        print_warning "PHP $PHP_VERSION is available and will be used"
        break
    fi
done

if [ -z "$PHP_VERSION" ]; then
    print_error "No suitable PHP version found. Please check your system and try again."
fi

# Install system packages with detected PHP version
print_section "Installing System Packages with PHP $PHP_VERSION"

apt install -y git curl nginx mysql-server \
    php$PHP_VERSION php$PHP_VERSION-fpm php$PHP_VERSION-mysql \
    php$PHP_VERSION-common php$PHP_VERSION-cli php$PHP_VERSION-mbstring \
    php$PHP_VERSION-xml php$PHP_VERSION-zip php$PHP_VERSION-curl \
    php$PHP_VERSION-gd php$PHP_VERSION-intl composer unzip || print_error "Failed to install system packages"

# Install Node.js and npm
print_section "Installing Node.js and npm"
curl -fsSL https://deb.nodesource.com/setup_18.x | bash - || print_error "Failed to setup Node.js repository"
apt install -y nodejs || print_error "Failed to install Node.js"

# Set up MySQL
print_section "Setting Up MySQL"

# Get MySQL root password
read -sp "Enter MySQL root password (leave blank if not set): " MYSQL_ROOT_PASSWORD
echo

# Determine MySQL version and adjust user creation command
MYSQL_VERSION=$(mysql --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -1)
print_warning "Detected MySQL version: $MYSQL_VERSION"

# Set up MySQL connection command with proper quoting
if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
    MYSQL_CONN="mysql"
else
    MYSQL_CONN="mysql -u root -p'$MYSQL_ROOT_PASSWORD'"
fi

print_warning "Creating and configuring database..."

# Try a completely different approach - use a SQL file to ensure commands execute properly
cat > /tmp/mysql_setup.sql << EOF
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS $DB_NAME;

-- For MySQL 8+, we need to handle authentication differently
DROP USER IF EXISTS '$DB_USER'@'localhost';
DROP USER IF EXISTS '$DB_USER'@'127.0.0.1';

-- Create users with native authentication
CREATE USER '$DB_USER'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';
CREATE USER '$DB_USER'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';

-- Grant privileges
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'127.0.0.1';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
EOF

# Execute SQL file directly
if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
    mysql < /tmp/mysql_setup.sql || print_error "Failed to create database and users"
else
    mysql -u root -p"$MYSQL_ROOT_PASSWORD" < /tmp/mysql_setup.sql || print_error "Failed to create database and users"
fi

# Remove SQL file for security
rm /tmp/mysql_setup.sql

print_warning "Database and users created successfully."

# Test connection using both possible hostnames
print_warning "Testing database connection..."
DB_HOST="127.0.0.1"

if mysql -u "$DB_USER" -p"$DB_PASSWORD" -h 127.0.0.1 -e "SELECT 1;" > /dev/null 2>&1; then
    print_warning "Connection successful using 127.0.0.1"
    DB_HOST="127.0.0.1"
elif mysql -u "$DB_USER" -p"$DB_PASSWORD" -h localhost -e "SELECT 1;" > /dev/null 2>&1; then
    print_warning "Connection successful using localhost"
    DB_HOST="localhost"
else
    print_warning "Connection failed with both 127.0.0.1 and localhost. Attempting to fix..."
    
    # Additional fix attempts - sometimes changing authentication plugin again helps
    if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
        mysql -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';" || print_warning "Could not alter user authentication"
        mysql -e "ALTER USER '$DB_USER'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';" || print_warning "Could not alter user authentication"
        mysql -e "FLUSH PRIVILEGES;" || print_warning "Could not flush privileges"
    else
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';" || print_warning "Could not alter user authentication"
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "ALTER USER '$DB_USER'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';" || print_warning "Could not alter user authentication" 
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "FLUSH PRIVILEGES;" || print_warning "Could not flush privileges"
    fi
    
    # Test connection again
    if mysql -u "$DB_USER" -p"$DB_PASSWORD" -h 127.0.0.1 -e "SELECT 1;" > /dev/null 2>&1; then
        print_warning "Connection successful after fix using 127.0.0.1"
        DB_HOST="127.0.0.1"
    elif mysql -u "$DB_USER" -p"$DB_PASSWORD" -h localhost -e "SELECT 1;" > /dev/null 2>&1; then
        print_warning "Connection successful after fix using localhost"
        DB_HOST="localhost"
    else
        # If all else fails, try using root for Laravel (not recommended for production)
        print_warning "Still cannot connect. Consider using root user for the database (not recommended for production)."
        if ask_yes_no "Do you want to use the root user for the database? (not recommended for production)"; then
            DB_USER="root"
            DB_PASSWORD="$MYSQL_ROOT_PASSWORD"
            DB_HOST="localhost"
            print_warning "Using root user for the database. Make sure to change this in production."
        else
            print_error "Failed to set up MySQL user. Please fix the database configuration manually."
        fi
    fi
fi

print_section "Cloning Repository"

# Create installation directory if it doesn't exist
mkdir -p $(dirname "$INSTALL_DIR") || print_error "Failed to create parent directory"

# Remove existing installation if it exists
if [ -d "$INSTALL_DIR" ]; then
    if ask_yes_no "Installation directory already exists. Do you want to remove it?"; then
        rm -rf "$INSTALL_DIR" || print_error "Failed to remove existing installation"
    else
        print_error "Installation directory already exists. Please remove it or choose a different directory."
    fi
fi

# Clone repository
git clone "$REPO_URL" "$INSTALL_DIR" || print_error "Failed to clone repository"

# Set directory ownership
chown -R www-data:www-data "$INSTALL_DIR" || print_error "Failed to set directory ownership"

print_section "Installing Dependencies"

# Fix npm cache permissions
print_warning "Fixing npm cache permissions"
mkdir -p /var/www/.npm
chown -R www-data:www-data /var/www/.npm || print_error "Failed to set npm cache permissions"

# Install PHP dependencies
cd "$INSTALL_DIR" || print_error "Failed to change to installation directory"

# First try to install with composer install
print_warning "Attempting to install dependencies using composer install"
if ! sudo -u www-data composer install --no-dev --optimize-autoloader; then
    print_warning "Composer install failed, trying composer update instead"
    # If composer install fails, try to update dependencies
    sudo -u www-data composer update --no-dev --optimize-autoloader || print_error "Failed to install PHP dependencies"
fi

# Install and build JavaScript dependencies
print_warning "Installing JavaScript dependencies"
sudo -u www-data npm install || print_error "Failed to install JavaScript dependencies"
print_warning "Building JavaScript assets"
sudo -u www-data npm run build || print_error "Failed to build JavaScript assets"

print_section "Configuring Application"

# Create environment file
sudo -u www-data cp .env.example .env || print_error "Failed to create environment file"

# Generate application key
sudo -u www-data php artisan key:generate || print_error "Failed to generate application key"

# Update environment file
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env || print_error "Failed to update database name"
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env || print_error "Failed to update database username"
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD='$DB_PASSWORD'/" .env || print_error "Failed to update database password"
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env || print_error "Failed to update database host"

# Add PayPal configuration if provided
if [ ! -z "$PAYPAL_CLIENT_ID" ] && [ ! -z "$PAYPAL_CLIENT_SECRET" ]; then
    sed -i "/^DB_/a PAYPAL_CLIENT_ID=$PAYPAL_CLIENT_ID\nPAYPAL_CLIENT_SECRET=$PAYPAL_CLIENT_SECRET\nPAYPAL_SANDBOX=true" .env || print_error "Failed to add PayPal configuration"
fi

print_section "Setting Up Database"

# Add troubleshooting options before migrations
print_warning "Attempting to run database migrations and seeders..."

# First, verify the MySQL connection one more time
if ! mysql -u "$DB_USER" -p"$DB_PASSWORD" -h "$DB_HOST" -e "USE $DB_NAME; SELECT 1;" > /dev/null 2>&1; then
    print_warning "Still having MySQL connection issues. Let's try to debug:"
    
    # Show MySQL users for debugging
    if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
        echo "Current MySQL users:"
        mysql -e "SELECT user, host, plugin FROM mysql.user WHERE user = '$DB_USER';" || print_warning "Could not view MySQL users"
    else
        echo "Current MySQL users:"
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "SELECT user, host, plugin FROM mysql.user WHERE user = '$DB_USER';" || print_warning "Could not view MySQL users"
    fi
    
    # Try to fix the issue with a more direct approach
    print_warning "Attempting to fix MySQL authentication with a different approach..."
    if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
        mysql -e "DROP USER IF EXISTS '$DB_USER'@'localhost';" || print_warning "Could not drop user"
        mysql -e "DROP USER IF EXISTS '$DB_USER'@'127.0.0.1';" || print_warning "Could not drop user"
        # Try creating without the plugin specification
        mysql -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';" || print_warning "Could not create user"
        mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';" || print_warning "Could not grant privileges"
        mysql -e "FLUSH PRIVILEGES;" || print_warning "Could not flush privileges"
    else
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "DROP USER IF EXISTS '$DB_USER'@'localhost';" || print_warning "Could not drop user"
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "DROP USER IF EXISTS '$DB_USER'@'127.0.0.1';" || print_warning "Could not drop user"
        # Try creating without the plugin specification
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';" || print_warning "Could not create user"
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';" || print_warning "Could not grant privileges"
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "FLUSH PRIVILEGES;" || print_warning "Could not flush privileges"
    fi
fi    
# Offer manual database setup as a last resort
if ask_yes_no "Would you like to manually set up the database tables by importing the Laravel migrations SQL dump?"; then
    print_warning "Preparing to manually set up the database..."
    
    # Generate an SQL dump file from Laravel migrations
    echo "-- Auto-generated SQL from Laravel migrations" > /tmp/laravel_migrations.sql
    echo "USE $DB_NAME;" >> /tmp/laravel_migrations.sql
    echo "-- Creating migrations table" >> /tmp/laravel_migrations.sql
    echo "CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL
    );" >> /tmp/laravel_migrations.sql
    
    # Add basic tables (this is a simplification - real migrations would be more complex)
    echo "-- Creating users table" >> /tmp/laravel_migrations.sql
    echo "CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        email_verified_at TIMESTAMP NULL,
        password VARCHAR(255) NOT NULL,
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );" >> /tmp/laravel_migrations.sql
    
    # Add admin user
    echo "-- Adding default admin user" >> /tmp/laravel_migrations.sql
    echo "INSERT INTO users (name, email, password, created_at, updated_at) 
            VALUES ('Admin User', 'admin@example.com', '\$2y\$12\$rGIE6BFIN2EMSWVrOfSYceP/Zk7sSA3zCOHwiCYQ.r7wvWVwTkKIa', NOW(), NOW());" >> /tmp/laravel_migrations.sql
    
    # Try to execute the SQL file
    if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
        mysql "$DB_NAME" < /tmp/laravel_migrations.sql || print_warning "Failed to import basic SQL structure"
    else
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$DB_NAME" < /tmp/laravel_migrations.sql || print_warning "Failed to import basic SQL structure"
    fi
    
    # Remove SQL file for security
    rm /tmp/laravel_migrations.sql
    
    print_warning "Basic database structure created manually. You may need to run additional migrations manually."
    
    # Skip the Laravel migration command
    print_warning "Skipping Laravel migrations as manual setup was chosen."
else
    # Try using root for migrations as a last resort
    if ask_yes_no "Would you like to attempt migrations using the root MySQL user? (not recommended for production)"; then
        # Update .env file temporarily to use root
        sed -i "s/DB_USERNAME=.*/DB_USERNAME=root/" .env || print_error "Failed to update database username"
        sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$MYSQL_ROOT_PASSWORD/" .env || print_error "Failed to update database password"
        sed -i "s/DB_HOST=.*/DB_HOST=localhost/" .env || print_error "Failed to update database host"
        
        # Run migrations as root
        sudo -u www-data php artisan migrate --seed || print_error "Failed to run database migrations and seeders even with root user"
        
        print_warning "Migration successful with root user. Consider changing the database user for production."
    else
        print_error "Failed to set up database. Please fix the database configuration manually before proceeding."
    fi
    
else
    # Normal path - database connection is working
    sudo -u www-data php artisan migrate --seed || print_error "Failed to run database migrations and seeders"
fi

print_section "Configuring Storage Access"

# Create storage symbolic link
sudo -u www-data php artisan storage:link || print_error "Failed to create storage symbolic link"

# Set permissions
chmod -R 775 storage bootstrap/cache || print_error "Failed to set permissions"

print_section "Setting Up Nginx"

# Create Nginx site configuration
cat > /etc/nginx/sites-available/maintenance-app << EOF
server {
    listen 80;
    server_name $DOMAIN_NAME;
    root $INSTALL_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/maintenance-app /etc/nginx/sites-enabled/ || print_error "Failed to enable Nginx site"

# Test Nginx configuration
nginx -t || print_error "Nginx configuration test failed"

# Restart Nginx
systemctl restart nginx || print_error "Failed to restart Nginx"

print_section "Setting Up Queue Worker"

# Create systemd service for queue worker
cat > /etc/systemd/system/laravel-queue.service << EOF
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=$INSTALL_DIR
ExecStart=/usr/bin/php $INSTALL_DIR/artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
EOF

# Enable and start the service
systemctl enable laravel-queue || print_error "Failed to enable queue worker service"
systemctl start laravel-queue || print_error "Failed to start queue worker service"

print_section "Setting Up Task Scheduler"

# Add Laravel's scheduler to the crontab
(crontab -u www-data -l 2>/dev/null || echo "") | grep -v "artisan schedule:run" | { cat; echo "* * * * * cd $INSTALL_DIR && php artisan schedule:run >> /dev/null 2>&1"; } | crontab -u www-data - || print_error "Failed to set up scheduler"

# Set up SSL if requested
if [ "$SETUP_SSL" = true ]; then
    print_section "Setting Up SSL"
    
    apt install -y certbot python3-certbot-nginx || print_error "Failed to install Certbot"
    certbot --nginx -d "$DOMAIN_NAME" --non-interactive --agree-tos --email webmaster@"$DOMAIN_NAME" || print_error "Failed to set up SSL"
fi

print_section "Final Steps"

# Check permissions one last time
chown -R www-data:www-data "$INSTALL_DIR" || print_error "Failed to set directory ownership"
find "$INSTALL_DIR" -type f -exec chmod 644 {} \; || print_error "Failed to set file permissions"
find "$INSTALL_DIR" -type d -exec chmod 755 {} \; || print_error "Failed to set directory permissions"
chmod -R 775 "$INSTALL_DIR/storage" || print_error "Failed to set storage permissions"
chmod -R 775 "$INSTALL_DIR/bootstrap/cache" || print_error "Failed to set cache permissions"

print_section "Installation Complete"

echo -e "${GREEN}The Maintenance App has been successfully installed at $INSTALL_DIR${NC}"
echo -e "You can access it at: ${YELLOW}http://$DOMAIN_NAME${NC}"

if [ "$SETUP_SSL" = true ]; then
    echo -e "Or with HTTPS: ${YELLOW}https://$DOMAIN_NAME${NC}"
fi

echo -e "\n${GREEN}Default Login Credentials:${NC}"
echo -e "Super Property Manager: ${YELLOW}admin@example.com${NC} / ${YELLOW}password${NC}"
echo -e "Property Manager: ${YELLOW}manager@example.com${NC} / ${YELLOW}password${NC}"
echo -e "Technician 1: ${YELLOW}john@example.com${NC} / ${YELLOW}password${NC}"
echo -e "Technician 2: ${YELLOW}jane@example.com${NC} / ${YELLOW}password${NC}"
echo -e "\n${RED}IMPORTANT: Change these default passwords immediately after first login!${NC}"

echo -e "\n${GREEN}Database Information:${NC}"
echo -e "Database: ${YELLOW}$DB_NAME${NC}"
echo -e "Username: ${YELLOW}$DB_USER${NC}"
echo -e "Password: ${YELLOW}$DB_PASSWORD${NC}"

echo -e "\n${GREEN}PHP Version:${NC} ${YELLOW}$PHP_VERSION${NC}" 