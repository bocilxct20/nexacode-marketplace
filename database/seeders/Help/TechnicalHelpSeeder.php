<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class TechnicalHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'technical'],
            [
                'name' => 'Bantuan Teknis',
                'icon' => 'wrench-screwdriver',
                'description' => 'Panduan teknis seputar instalasi, hosting, dan troubleshooting script.',
                'sort_order' => 5,
            ]
        );

        $articles = [
            [
                'title' => 'Persyaratan Server Umum',
                'slug' => 'persyaratan-server-umum',
                'content' => "Hampir semua script di NexaCode membutuhkan:\n\n- **PHP 8.2+** (Direkomendasikan).\n- **MySQL 8.0+** atau MariaDB 10.4+.\n- **Extension**: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML.\n- **Memory Limit**: Minimal 256MB.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Cara Instalasi Script Laravel',
                'slug' => 'cara-instalasi-script-laravel',
                'content' => "Langkah dasar instalasi script berbasis Laravel:\n\n1. Upload file ke hosting.\n2. Buat database kosong.\n3. Konfigurasi file `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD).\n4. Jalankan `php artisan migrate --seed`.\n5. Setting symlink storage dengan `php artisan storage:link`.",
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Mengapa Muncul Error 500?',
                'slug' => 'mengapa-muncul-error-500',
                'content' => "Error 500 biasanya disebabkan oleh:\n\n- **Izin Folder**: Pastikan folder `storage` dan `bootstrap/cache` writable (chmod 775).\n- **Mis-konfigurasi .env**: Cek kembali detail database.\n- **Versi PHP**: Pastikan hosting kamu menggunakan PHP minimal versi 8.1.",
                'is_featured' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($articles as $article) {
            HelpArticle::updateOrCreate(
                ['slug' => $article['slug']],
                array_merge($article, ['help_category_id' => $category->id])
            );
        }
    }
}
