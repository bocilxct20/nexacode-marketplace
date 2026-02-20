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
DB_PASS=$(openssl rand -base64 12)

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
mkdir -p /var/www/$PROJECT_NAME
# Note: You should have cloned your repo manually or via this script if repository URL is provided.
# Assuming this script is run INSIDE the project folder or project is already there.

cd /var/www/$PROJECT_NAME

# Setup .env
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i "s/APP_ENV=local/APP_ENV=production/" .env
    sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env
    sed -i "s|APP_URL=http://localhost|APP_URL=https://$DOMAIN|" .env
    sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
    # Add Admin Credentials for Seeder
    echo "" >> .env
    echo "ADMIN_EMAIL=$EMAIL" >> .env
    echo "ADMIN_PASSWORD=Password123" >> .env
fi

# Install PHP Deps
composer install --no-dev --optimize-autoloader

# Install JS Deps & Build
npm install
npm run build

# Artisan commands
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan storage:link

# Permissions
chown -R www-data:www-data /var/www/$PROJECT_NAME
chmod -R 775 /var/www/$PROJECT_NAME/storage
chmod -R 775 /var/www/$PROJECT_NAME/bootstrap/cache

# --- 8. Configure Nginx ---
echo "🌐 Configuring Nginx..."
cp deployment/nginx.conf /etc/nginx/sites-available/$PROJECT_NAME
sed -i "s/your-domain.com/$DOMAIN/g" /etc/nginx/sites-available/$PROJECT_NAME
ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx

# --- 9. Configure Supervisor ---
echo "👷 Configuring Supervisor..."
cp deployment/supervisor.conf /etc/supervisor/conf.d/$PROJECT_NAME.conf
# Fix path in supervisor if necessary
sed -i "s|/var/www/marketplace|/var/www/$PROJECT_NAME|g" /etc/supervisor/conf.d/$PROJECT_NAME.conf
supervisorctl reread
supervisorctl update
supervisorctl start all

# --- 10. Setup Scheduler ---
chmod +x setup-scheduler.sh
./setup-scheduler.sh

# --- 11. SSL (Certbot) ---
echo "SSL Setup..."
apt install -y python3-certbot-nginx
certbot --nginx -d nexacode.id --non-interactive --agree-tos -m admin@nexacode.id

echo "✅ ALL DONE! Your website is ready at https://$DOMAIN"
echo "Admin Login: $EMAIL / Password123 (Please change after login!)"
