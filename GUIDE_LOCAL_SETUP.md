# Panduan Setup Lokal & Recovery Git 🚀

Ikuti langkah-langkah di bawah ini untuk melakukan clone ulang dari Git dan menjalankan project secara lokal di Windows.

## 1. Download/Clone Ulang dari Git

Buka **PowerShell** atau **Command Prompt**, lalu jalankan perintah berikut:

```powershell
# Pindah ke direktori kerja kamu
cd C:\Users\dani\Documents\

# Hapus atau pindahkan folder lama jika perlu (opsional)
# Rename-Item nexacode-marketplace nexacode-marketplace-backup

# Clone repository
git clone https://github.com/bocilxct20/nexacode-marketplace.git
cd nexacode-marketplace
```

## 2. Setup Project Lokal ("Up")

Setelah berada di dalam folder project, jalankan rangkaian perintah ini:

### A. Konfigurasi Environment & Dependencies
```powershell
# Copy file environment
cp .env.example .env

# Install dependencies PHP & JS
composer install
npm install

# Generate App Key
php artisan key:generate
```

### B. Konfigurasi Database
1. Buat database kosong di MySQL (misal: `marketplace_db`).
2. Update file `.env` dengan kredensial database kamu:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=marketplace_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan migrasi dan seeder:
   ```powershell
   php artisan migrate --seed
   ```

### C. ⚠️ Fix Flux Pro Assets (PENTING)
Karena artifact Flux Pro saat ini memiliki kekurangan folder `dist`, jalankan perintah PowerShell ini untuk menyalin asset dari versi Lite:

```powershell
if (Test-Path "vendor/livewire/flux-pro") {
    Copy-Item -Path "vendor/livewire/flux/dist" -Destination "vendor/livewire/flux-pro/" -Recurse -Force
    echo "✅ Flux Pro assets fixed!"
}
```

## 3. Menjalankan Project

Jalankan dua perintah ini di dua jendela terminal terpisah:

**Terminal 1 (Backend):**
```powershell
php artisan serve
```

**Terminal 2 (Frontend/Vite):**
```powershell
npm run dev
```

Sekarang project kamu sudah bisa diakses di `http://127.0.0.1:8000`.
