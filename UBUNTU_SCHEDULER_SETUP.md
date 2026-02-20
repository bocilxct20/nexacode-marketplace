# Laravel Scheduler - Ubuntu Deployment

## Quick Setup (Copy-Paste)

### Method 1: Automatic Setup Script

```bash
# 1. Upload setup-scheduler.sh ke server
# 2. Berikan permission execute
chmod +x setup-scheduler.sh

# 3. Jalankan script
./setup-scheduler.sh
```

Script akan otomatis:
- ✅ Detect project path
- ✅ Detect PHP path
- ✅ Add cron entry
- ✅ Verify setup

---

### Method 2: Manual Setup (1 Command)

```bash
# Jalankan command ini dari root project Laravel
(crontab -l 2>/dev/null; echo "* * * * * cd $(pwd) && $(which php) artisan schedule:run >> /dev/null 2>&1") | crontab -
```

---

## Verifikasi Setup

### 1. Cek Crontab

```bash
crontab -l
```

Output yang diharapkan:
```
* * * * * cd /path/to/project && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### 2. Test Manual

```bash
cd /path/to/project
php artisan schedule:run
```

### 3. Lihat Scheduled Tasks

```bash
php artisan schedule:list
```

Output akan menampilkan semua scheduled tasks dan kapan mereka akan jalan.

---

## Monitoring

### Lihat Logs Real-time

```bash
tail -f storage/logs/laravel.log
```

### Test Earnings Command

```bash
php artisan earnings:release-pending
```

Output: `Released X pending earnings.`

---

## Troubleshooting

### Cron tidak jalan?

```bash
# Cek cron service
sudo service cron status

# Restart cron
sudo service cron restart

# Cek cron logs
grep CRON /var/log/syslog
```

### Permission issues?

```bash
# Set ownership
sudo chown -R www-data:www-data /path/to/project

# Set permissions
chmod -R 755 /path/to/project/storage
chmod -R 755 /path/to/project/bootstrap/cache
```

---

## Remove Cron (Jika Perlu)

```bash
# Remove scheduler cron
crontab -l | grep -v "artisan schedule:run" | crontab -
```

---

## Production Checklist

- [ ] Upload `setup-scheduler.sh` ke server
- [ ] Jalankan setup script atau manual command
- [ ] Verify dengan `crontab -l`
- [ ] Test dengan `php artisan schedule:run`
- [ ] Monitor logs selama 1 jam
- [ ] Verify earnings auto-release setelah 24 jam

---

**Setup Complete!** ✅

Scheduler akan otomatis menjalankan:
- `earnings:release-pending` setiap jam
- `chat:send-scheduled` setiap menit
- `app:remind-abandoned-checkouts` setiap 2 jam
- Dan semua scheduled tasks lainnya sesuai jadwal
