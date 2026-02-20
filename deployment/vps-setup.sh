#!/bin/bash

# Nexacode Marketplace - VPS Auto-Installer Script (Ubuntu 24.04)
# ⚠️ RUN THIS AS ROOT OR WITH SUDO ⚠️

set -e

echo "🚀 Starting Nexacode Marketplace VPS Setup..."

# --- 1. Variables (Update These) ---
PROJECT_NAME="nexacode-marketplace"
DOMAIN="nexacode.id"
EMAIL="admin@nexacode.id"
DB_NAME="marketplace_db"
DB_USER="nexacode_user"
# Generate safe alphanumeric password to avoid shell/SQL escaping issues
DB_PASS=$(LC_ALL=C tr -dc 'A-Za-z0-9' < /dev/urandom | head -c16)

echo "------------------------------------------"
echo "Project: $PROJECT_NAME"
echo "Domain: $DOMAIN"
echo "DB Password (SAVED TO .env): $DB_PASS"
echo "------------------------------------------"

# --- 2. Update System ---
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# --- 3. Install Dependencies (PHP 8.4, Nginx, MySQL, etc.) ---
echo "🐘 Installing PHP 8.4 and extensions..."
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.4-fpm php8.4-mysql php8.4-xml php8.4-curl php8.4-mbstring php8.4-zip php8.4-intl php8.4-bcmath php8.4-gd php8.4-soap php8.4-readline php8.4-redis curl git unzip supervisor nginx mysql-server

# --- 4. Setup MySQL ---
echo "🗄️ Setting up database..."
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
# Always update password in case user already exists
mysql -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# --- 5. Install Composer ---
echo "🎼 Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# --- 6. Install Node.js (via NVM) ---
echo "🟢 Installing Node.js..."
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install 20
nvm use 20

# --- 7. Project Setup ---
echo "🏗️ Setting up project folder..."

# Get current script location to find project root
CURRENT_DIR=$(pwd)

# Cek apakah kita sudah di /var/www
if [[ "$CURRENT_DIR" != "/var/www/$PROJECT_NAME"* ]]; then
    echo "📦 Moving project from $CURRENT_DIR to /var/www/$PROJECT_NAME..."
    mkdir -p /var/www
    if [ -d "/var/www/$PROJECT_NAME" ]; then
        echo "⚠️ Target directory /var/www/$PROJECT_NAME already exists. Updating files..."
        # Copy instead of move if target exists to avoid losing data or permission issues
        cp -R "$CURRENT_DIR/." "/var/www/$PROJECT_NAME/"
    else
        mv "$CURRENT_DIR" "/var/www/$PROJECT_NAME"
    fi
fi

cd /var/www/$PROJECT_NAME

# Function to safely set .env variables
set_env() {
    local key=$1
    local value=$2
    if grep -q "^$key=" .env; then
        sed -i "s|^$key=.*|$key=$value|" .env
    elif grep -q "^# $key=" .env; then
        sed -i "s|^# $key=.*|$key=$value|" .env
    else
        echo "$key=$value" >> .env
    fi
}

# Setup .env
if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "📝 Configuring .env..."
set_env "APP_ENV" "production"
set_env "APP_DEBUG" "false"
set_env "APP_URL" "https://$DOMAIN"
set_env "DB_CONNECTION" "mysql"
set_env "DB_HOST" "127.0.0.1"
set_env "DB_PORT" "3306"
set_env "DB_DATABASE" "$DB_NAME"
set_env "DB_USERNAME" "$DB_USER"
set_env "DB_PASSWORD" "$DB_PASS"
set_env "ADMIN_EMAIL" "$EMAIL"
set_env "ADMIN_PASSWORD" "Password123"

# Install PHP Deps
echo "🎼 Installing PHP dependencies..."
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MEMORY_LIMIT=-1
# Sync Flux Pro artifact and update dependencies
composer require livewire/flux-pro:2.11.1 livewire/flux:2.11.1 -W --no-interaction --no-scripts
composer install --no-dev --optimize-autoloader --no-interaction

# Clear config cache and run migrations
# We use CACHE_STORE=array and SESSION_DRIVER=array to avoid database dependency before tables exist
echo "🧼 Cleaning configuration and running migrations..."
CACHE_STORE=array SESSION_DRIVER=array php artisan config:clear
CACHE_STORE=array SESSION_DRIVER=array php artisan key:generate --force
CACHE_STORE=array SESSION_DRIVER=array php artisan migrate --force
CACHE_STORE=array SESSION_DRIVER=array php artisan db:seed --class=AdminSeeder --force
php artisan storage:link --force

# Now safe to initialize Flux and clear caches with the real configuration
echo "💎 Initializing Flux UI..."
php artisan cache:clear
php artisan flux:publish --all --no-interaction || echo "⚠️ Flux publish failed or skipped"
php artisan view:clear
php artisan route:clear
php artisan config:cache

# Install JS Deps & Build
echo "📦 Installing JS dependencies and building assets..."
npm install
npm run build

# Permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/$PROJECT_NAME
chmod -R 775 /var/www/$PROJECT_NAME/storage
chmod -R 775 /var/www/$PROJECT_NAME/bootstrap/cache

# --- 8. Configure Nginx ---
echo "🌐 Configuring Nginx..."
cp deployment/nginx.conf /etc/nginx/sites-available/$PROJECT_NAME
# Use | as delimiter to avoid path slash conflicts
sed -i "s|server_name .*|server_name $DOMAIN;|g" /etc/nginx/sites-available/$PROJECT_NAME
sed -i "s|root .*|root /var/www/$PROJECT_NAME/public;|g" /etc/nginx/sites-available/$PROJECT_NAME
ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx

# --- 9. Configure Supervisor ---
echo "👷 Configuring Supervisor..."
mkdir -p /var/www/$PROJECT_NAME/storage/logs
cp deployment/supervisor.conf /etc/supervisor/conf.d/$PROJECT_NAME.conf
# Fix paths in supervisor
sed -i "s|/var/www/nexacode-marketplace|/var/www/$PROJECT_NAME|g" /etc/supervisor/conf.d/$PROJECT_NAME.conf
supervisorctl reread
supervisorctl update
supervisorctl restart all

# --- 10. Setup Scheduler ---
if [ -f "setup-scheduler.sh" ]; then
    chmod +x setup-scheduler.sh
    ./setup-scheduler.sh --force
fi

# --- 11. SSL (Certbot) ---
echo "🔒 Setting up SSL with Certbot..."
apt install -y python3-certbot-nginx
certbot --nginx -d $DOMAIN --non-interactive --agree-tos -m $EMAIL

echo "✅ ALL DONE! Your website is ready at https://$DOMAIN"
echo "Admin Login: $EMAIL / Password123 (Please change after login!)"
