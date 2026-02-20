# Production Deployment Guide (Automated) 🚀

Ikuti langkah-langkah ini untuk memindahkan perubahan dari **Lokal** ke **VPS Baru** menggunakan script otomatis.

## 1. Persiapan di Komputer Lokal (Lokal)
Pastikan semua perubahan sudah tersimpan dan didorong ke GitHub.
```bash
# Tambahkan semua file yang berubah
git add .

# Buat catatan perubahan
git commit -m "Fix: Upgrade Flux Pro v2.11.1 and Update VPS Auto-Installer"

# Kirim ke GitHub
git push origin main
```

## 2. Persiapan di VPS Baru (Fresh VPS)
Gunakan SSH untuk masuk ke VPS kamu, lalu jalankan perintah ini:

### A. Clone Project
```bash
cd /var/www
# Ganti repository_url dengan URL GitHub kamu
git clone <repository_url> nexacode-marketplace
cd nexacode-marketplace
```

### B. Jalankan Auto-Installer
Hanya butuh satu perintah ini untuk menginstal PHP 8.4, Nginx, MySQL, Flux Pro, dll:
```bash
sudo bash deployment/vps-setup.sh
```

## 3. Update Project (Jika sudah pernah install)
Kalau kamu melakukan perubahan kode lagi di lokal, cara updatenya di VPS adalah:
```bash
cd /var/www/nexacode-marketplace
git pull origin main
sudo bash deployment/vps-setup.sh
```

## 4. Tips Tambahan
- **Folder `packages/`**: Jangan hapus folder ini! Folder ini berisi file `.zip` Flux Pro yang dibutuhkan saat instalasi.
- **Database**: Password database dibuat secara acak oleh script dan disimpan di file `.env`.
- **SSL/HTTPS**: Script otomatis menjalankan Certbot untuk mengaktifkan HTTPS.

**Website sekarang sudah Online dengan standar Production! 🚀**
