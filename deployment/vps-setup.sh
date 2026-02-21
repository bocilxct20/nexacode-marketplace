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

# --- 6. Install Node.js 20 (Global via NodeSource) ---
echo "🟢 Installing Node.js 20 system-wide..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# --- 7. Project Setup ---
echo "🏗️ Setting up project folder..."

# Identify the real user (who ran sudo)
REAL_USER=${SUDO_USER:-$(whoami)}
echo "👤 Project Owner: $REAL_USER"

# Add the user to www-data group
usermod -a -G www-data $REAL_USER || true

# Fix Git dubious ownership error for all users
git config --system --add safe.directory /var/www/$PROJECT_NAME || true

# Initial ownership fix
sudo chown -R $REAL_USER:www-data /var/www/$PROJECT_NAME || true
sudo chmod -R 775 /var/www/$PROJECT_NAME || true

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
set_env "APP_DEBUG" "true"
set_env "APP_URL" "https://$DOMAIN"
set_env "DB_CONNECTION" "mysql"
set_env "DB_HOST" "127.0.0.1"
set_env "DB_PORT" "3306"
set_env "DB_DATABASE" "$DB_NAME"
set_env "DB_USERNAME" "$DB_USER"
set_env "DB_PASSWORD" "$DB_PASS"
set_env "ADMIN_EMAIL" "$EMAIL"
set_env "ADMIN_PASSWORD" "Password123"
set_env "DOMAIN" "$DOMAIN"

# Generate Reverb Keys if not already present
if ! grep -q "^REVERB_APP_ID=" .env; then
    echo "🔑 Generating Reverb keys..."
    REVERB_APP_ID=$(LC_ALL=C tr -dc '0-9' < /dev/urandom | head -c10)
    REVERB_APP_KEY=$(LC_ALL=C tr -dc 'a-z0-9' < /dev/urandom | head -c20)
    REVERB_APP_SECRET=$(LC_ALL=C tr -dc 'a-z0-9' < /dev/urandom | head -c20)
    
    set_env "REVERB_APP_ID" "$REVERB_APP_ID"
    set_env "REVERB_APP_KEY" "$REVERB_APP_KEY"
    set_env "REVERB_APP_SECRET" "$REVERB_APP_SECRET"
    set_env "REVERB_HOST" "0.0.0.0"
    set_env "REVERB_PORT" "8080"
    set_env "REVERB_SCHEME" "https"
    
    # Frontend variables for Vite
    set_env "VITE_REVERB_APP_KEY" "$REVERB_APP_KEY"
    set_env "VITE_REVERB_HOST" "$DOMAIN"
    set_env "VITE_REVERB_PORT" "443"
    set_env "VITE_REVERB_SCHEME" "https"
fi

# Install PHP Deps
echo "🎼 Installing PHP dependencies..."
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MEMORY_LIMIT=-1
composer install --no-dev --optimize-autoloader --no-interaction

# Clear config cache and run migrations
# We use CACHE_STORE=array and SESSION_DRIVER=array to avoid database dependency before tables exist
echo "🧼 Cleaning configuration and running migrations..."
CACHE_STORE=array SESSION_DRIVER=array php artisan config:clear
CACHE_STORE=array SESSION_DRIVER=array php artisan key:generate --force
CACHE_STORE=array SESSION_DRIVER=array php artisan migrate --force
CACHE_STORE=array SESSION_DRIVER=array php artisan db:seed --force
php artisan storage:link --force

# Flux Pro Asset Fix
echo "💎 Checking Flux Pro assets..."
HAS_PRO_ASSETS=false

# Check for genuine Pro JS (>300KB) or Rich Text Editor assets (editor.js)
if [ -f "vendor/livewire/flux-pro/dist/flux.js" ] && [ $(stat -c%s "vendor/livewire/flux-pro/dist/flux.js") -gt 300000 ]; then
    HAS_PRO_ASSETS=true
    SOURCE_DIR="vendor/livewire/flux-pro/dist"
elif [ -f "vendor/livewire/flux/dist/flux.js" ] && [ $(stat -c%s "vendor/livewire/flux/dist/flux.js") -gt 300000 ]; then
    HAS_PRO_ASSETS=true
    SOURCE_DIR="vendor/livewire/flux/dist"
    echo "💎 Pro assets detected in Lite folder."
fi

if [ "$HAS_PRO_ASSETS" = true ]; then
    echo "✅ Genuine Flux Pro assets detected. Syncing to ALL vendor folders for consistency..."
    mkdir -p vendor/livewire/flux/dist
    mkdir -p vendor/livewire/flux-pro/dist
    
    # Files to sync (Complete set from local dist)
    FILES_TO_SYNC=("flux.js" "flux.min.js" "flux.module.js" "flux.css" "editor.js" "editor.min.js" "editor.module.js" "editor.css" "manifest.json")
    
    for FILE in "${FILES_TO_SYNC[@]}"; do
        if [ -f "$SOURCE_DIR/$FILE" ]; then
            # Sync to flux-pro (Target 1)
            [ "$SOURCE_DIR/$FILE" != "vendor/livewire/flux-pro/dist/$FILE" ] && cp "$SOURCE_DIR/$FILE" "vendor/livewire/flux-pro/dist/$FILE"
            # Sync to flux (Target 2)
            [ "$SOURCE_DIR/$FILE" != "vendor/livewire/flux/dist/$FILE" ] && cp "$SOURCE_DIR/$FILE" "vendor/livewire/flux/dist/$FILE"
        fi
    done
    
    # CRITICAL: Force flux.min.js to match flux.js (Pro) so it works even if DEBUG is false later
    echo "💎 Guaranteeing Pro parity: Mirroring flux.js -> flux.min.js..."
    cp vendor/livewire/flux-pro/dist/flux.js vendor/livewire/flux-pro/dist/flux.min.js
    cp vendor/livewire/flux-pro/dist/flux.js vendor/livewire/flux/dist/flux.min.js
else
    echo "⚠️ Genuine Flux Pro assets not found in vendor. Using Lite fallback as safety-net."
    mkdir -p vendor/livewire/flux-pro/dist
    cp vendor/livewire/flux/dist/flux-lite.min.js vendor/livewire/flux-pro/dist/flux.min.js
    cp vendor/livewire/flux/dist/flux-lite.min.js vendor/livewire/flux-pro/dist/flux.js
    cp vendor/livewire/flux/dist/flux.css vendor/livewire/flux-pro/dist/flux.css
    cp vendor/livewire/flux/dist/manifest.json vendor/livewire/flux-pro/dist/manifest.json
fi

chown -R $REAL_USER:www-data vendor/livewire/flux-pro/dist || true
chown -R $REAL_USER:www-data vendor/livewire/flux/dist || true

# Now safe to initialize Flux and clear caches with the real configuration
echo "💎 Initializing Flux UI..."
php artisan cache:clear

# Publish Flux assets (this might copy files to public/vendor/flux)
php artisan flux:publish --all --no-interaction || echo "⚠️ Flux publish failed or skipped"

# Double check public directory and fix if necessary (ensure we don't serve Lite JS if we have Pro JS)
if [ -d "public/vendor/flux" ]; then
    # Check if we have Pro JS in vendor but public is still Lite (<300KB)
    if [ "$HAS_PRO_ASSETS" = true ] && ([ ! -f "public/vendor/flux/flux.js" ] || [ $(stat -c%s "public/vendor/flux/flux.js") -lt 300000 ]); then
        echo "💎 Patching public Flux assets with Pro version..."
        for FILE in "${FILES_TO_SYNC[@]}"; do
            [ -f "vendor/livewire/flux-pro/dist/$FILE" ] && cp "vendor/livewire/flux-pro/dist/$FILE" "public/vendor/flux/$FILE"
        done
    fi
    chown -R $REAL_USER:www-data public/vendor/flux || true
fi

php artisan view:clear
php artisan route:clear
php artisan config:cache

# Permissions
echo "🔐 Setting permissions..."
chown -R $REAL_USER:www-data /var/www/$PROJECT_NAME
chmod -R 775 /var/www/$PROJECT_NAME/storage
chmod -R 775 /var/www/$PROJECT_NAME/bootstrap/cache

# Permissions fix before build
echo "🔐 Ensuring correct permissions for build..."
chown -R $REAL_USER:www-data /var/www/$PROJECT_NAME
chmod -R 775 /var/www/$PROJECT_NAME


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

# --- 12. Final Build & Permissions ---
# We do this at the very end to ensure all services are ready
echo "📦 Finalizing: Installing JS dependencies and building assets..."

# Final check for Flux Pro before build
echo "💎 Step 5: composer require livewire/flux-pro..."
composer require livewire/flux-pro

echo "💎 Step 6: composer dump-autoload..."
composer dump-autoload

npm install
npm run build

echo "🔐 Finalizing: Setting project ownership..."
chown -R $REAL_USER:www-data /var/www/$PROJECT_NAME
chmod -R 775 /var/www/$PROJECT_NAME/storage
chmod -R 775 /var/www/$PROJECT_NAME/bootstrap/cache

echo "✅ ALL DONE! Your website is ready at https://$DOMAIN"
echo "Admin Login: $EMAIL / Password123 (Please change after login!)"
