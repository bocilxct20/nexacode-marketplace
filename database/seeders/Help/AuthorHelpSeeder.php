<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class AuthorHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'selling'],
            [
                'name' => 'Penjualan & Author',
                'icon' => 'rocket-launch',
                'description' => 'Panduan lengkap cara menjadi author dan mulai berjualan di NexaCode.',
                'sort_order' => 3,
            ]
        );

        $articles = [
            [
                'title' => 'Panduan Menjadi Author',
                'slug' => 'panduan-menjadi-author',
                'content' => "Ubah skill coding kamu menjadi pendapatan pasif. Klik 'Become an Author' di dashboard, lengkapi profil, dan tunggu tim kami melakukan review identitas dalam 1x24 jam.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Standar Kualitas Produk',
                'slug' => 'standar-kualitas-produk',
                'content' => "Kami hanya menerima produk berkualitas tinggi. Pastikan:\n\n- **Kode Bersih**: Penamaan variabel jelas dan terdokumentasi.\n- **No Malicious Code**: Dilarang memasukkan backdoor atau script berbahaya.\n- **Visual Bagus**: Gunakan banner preview yang menarik (1200x800px disarankan).",
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Sistem Komisi & Elite Perks',
                'slug' => 'sistem-komisi-elite-perks',
                'content' => "NexaCode memberikan bagi hasil terbaik yang transparan berdasarkan paket kamu:\n\n- **Basic**: Komisi **80%** untuk Author.\n- **Pro**: Komisi **85%** untuk Author.\n- **Elite**: Komisi **90%** untuk Author.\n\n### Elite Perks\nKhusus untuk **Elite Author**, kamu mendapatkan fasilitas **Instant Approval** (produk langsung terbit tanpa antrian review manual) dan slot featured produk tidak terbatas di halaman utama.",
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Sistem Level & XP Author',
                'slug' => 'sistem-level-xp-author',
                'content' => "Tingkatkan reputasi kamu dengan sistem Leveling kami. \n\n- **Cara Mendapat XP**: Setiap penjualan senilai **Rp 1.000** akan memberikan kamu **1 XP**.\n- **Badge Profesi**: Semakin tinggi level kamu, semakin tinggi kredibilitas kamu di mata pembeli.\n- **Visibilitas**: Produk dari Author dengan level tinggi akan mendapatkan prioritas di hasil pencarian.",
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'title' => 'Aturan Penarikan Saldo (Payout)',
                'slug' => 'aturan-penarikan-saldo-payout',
                'content' => "Kamu bisa menarik hasil penjualan dengan ketentuan berikut:\n\n- **Minimum Threshold**: Rp 10.000.\n- **Hold Period**: Untuk keamanan, pendapatan akan tertahan selama **24 jam** sebelum masuk ke saldo yang bisa ditarik.\n- **Waktu Proses**: Cair dalam 1-3 hari kerja ke rekening bank lokal atau e-wallet kamu.\n- **Update Rekening**: Pastikan detail bank sudah dikonfigurasi di Profile Settings.",
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'title' => 'Rilis Update & Versi Baru',
                'slug' => 'rilis-update-versi-baru',
                'content' => "Menjaga produk tetap up-to-date sangat penting bagi kepercayaan pembeli. Gunakan fitur **'Create Version'** di menu My Products untuk merilis bugfix atau penambahan fitur baru.",
                'is_featured' => false,
                'sort_order' => 5,
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
