# Production Deployment Checklist 🚀

Gunakan panduan ini saat kamu melakukan SSH ke VPS.

## 1. Persiapan Server (Ubuntu 22.04+)
- [ ] Update OS: `sudo apt update && sudo apt upgrade -y`
- [ ] Install PHP 8.2 & Extensions: `php-fpm, php-mysql, php-xml, php-curl, php-mbstring, php-zip, php-intl`.
- [ ] Install Nginx & MySQL/MariaDB.
- [ ] Install Composer & Node.js (via NVM).

## 2. Clone Project
```bash
cd /var/www
git clone <repository_url> nexacode-marketplace
cd nexacode-marketplace
```

> [!IMPORTANT]
> **Flux Pro Error Fix:** Pastikan folder `packages/` yang berisi file `.zip` Flux Pro sudah ter-upload ke root folder project di VPS. Tanpa folder ini, `composer install` akan gagal karena Flux Pro di-install dari artifact lokal.

## 3. Konfigurasi Environment
```bash
cp .env.example .env
nano .env
```
**Atur variabel penting ini:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://nexacode.id`
- `ADMIN_EMAIL=admin@nexacode.id`
- `ADMIN_PASSWORD=PasswordRahasiaKamu123`
- `DB_DATABASE=marketplace_db` (Sesuaikan)
- `REVERB_HOST=nexacode.id`
- `REVERB_PORT=443`
- `REVERB_SCHEME=https`

## 4. Install Dependencies & Build
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
```

## 5. Setup Permissions
```bash
sudo chown -R www-data:www-data /var/www/nexacode-marketplace
sudo chmod -R 775 /var/www/nexacode-marketplace/storage
sudo chmod -R 775 /var/www/nexacode-marketplace/bootstrap/cache
```

## 6. Setup Services
- [ ] **Nginx**: Salin `deployment/nginx.conf` ke `/etc/nginx/sites-available/marketplace` dan aktifkan.
- [ ] **Supervisor**: Salin `deployment/supervisor.conf` ke `/etc/supervisor/conf.d/marketplace.conf`.
- [ ] **Scheduler**: Jalankan `./setup-scheduler.sh`.
- [ ] **SSL**: Jalankan `sudo certbot --nginx -d nexacode.id`.

## 7. Final Check
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Website sekarang sudah Online! 🚀**
