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
# Buat folder web server jika belum ada
sudo mkdir -p /var/www && cd /var/www

# Ganti URL di bawah dengan URL Repository GitHub kamu!
# Contoh: git clone https://github.com/UsernameKamu/nexacode.git nexacode-marketplace
sudo git clone <URL_GitHub_Kamu> nexacode-marketplace
cd nexacode-marketplace
```

### B. Jalankan Auto-Installer
```bash
sudo bash deployment/vps-setup.sh
```

## 3. Update Project (Cara Sinkronkan Perubahan)
Kalau kamu melakukan update kode lagi di lokal, ikuti langkah ini:

### A. Di Komputer Lokal
```bash
git add .
git commit -m "Update project: Deskripsi perubahan kamu"
git push origin main
```

### B. Di VPS (Terminal SSH)
Masuk ke folder project, tarik perubahan, lalu jalankan script setup:
```bash
cd /var/www/nexacode-marketplace

# Tarik update dari GitHub
git pull origin main

# Jalankan ulang setup (Otomatis urus composer, npm build, migration, & fix Flux)
sudo bash deployment/vps-setup.sh
```

## 4. Tips Tambahan
- **Idempoten**: Script `vps-setup.sh` bersifat aman untuk dijalankan berkali-kali. Ia hanya akan menginstall yang kurang atau melakukan build ulang asset yang berubah.
- **Database**: Password database TIDAK akan berubah walaupun script dijalankan ulang (sudah diamankan di `.env`).
- **Flux Fix**: Script akan otomatis memperbaiki folder `dist` Flux Pro setiap kali dijalankan.

**Website sekarang sudah Update dengan standar Production! 🚀**
