<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class GeneralHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'general'],
            [
                'name' => 'Pertanyaan Umum',
                'icon' => 'information-circle',
                'description' => 'Bantuan umum mengenai NexaCode Marketplace dan cara kerjanya.',
                'sort_order' => 1,
            ]
        );

        $articles = [
            [
                'title' => 'Apa itu NexaCode Marketplace?',
                'slug' => 'apa-itu-nexacode',
                'content' => "NexaCode Marketplace adalah platform digital terkemuka untuk aset berkualitas tinggi. Kami menghubungkan ribuan developer dan desainer berbakat dengan pelanggan global yang membutuhkan solusi digital profesional.\n\n### Mengapa Memilih Kami?\n- **Produk Terverifikasi**: Setiap aset diperiksa manual oleh tim kami.\n- **Transparan**: Sistem komisi dan pembayaran yang jelas.\n- **Support Terpadu**: Hubungan langsung antara author dan buyer.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Cara Mendaftar Akun',
                'slug' => 'cara-mendaftar-akun',
                'content' => "Mendaftar di NexaCode sangatlah mudah dan gratis. Ikuti langkah berikut:\n\n1. Kunjungi halaman [Register](/register).\n2. Masukkan nama, email, dan password yang kuat.\n3. Atau gunakan **Sign in with Google** untuk akses instan.\n4. Jangan lupa verifikasi email kamu untuk mulai bertransaksi atau berjualan.",
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Keamanan Akun & OTP',
                'slug' => 'keamanan-akun-2fa',
                'content' => "Keamanan akun kamu adalah prioritas kami:\n\n- **Verifikasi OTP**: Untuk tindakan sensitif, kami mengirimkan kode OTP unik ke email terdaftar kamu.\n- **Sign in with Google**: Gunakan OAuth Google untuk login yang lebih cepat dan aman.\n- **Last Login Tracking**: Pantau aktivitas login akun kamu (IP & Perangkat) langsung di Dashboard Keamanan.",
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Lupa Password?',
                'slug' => 'lupa-password',
                'content' => "Jika kamu lupa password, klik 'Forgot Password' pada halaman login. Kami akan mengirimkan link reset password ke email terdaftar kamu. Link ini berlaku selama 60 menit demi keamanan.",
                'is_featured' => false,
                'sort_order' => 4,
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
