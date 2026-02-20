<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class BuyerHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'buying'],
            [
                'name' => 'Pembelian Produk',
                'icon' => 'shopping-cart',
                'description' => 'Segala hal yang perlu kamu ketahui tentang cara membeli produk digital kami.',
                'sort_order' => 2,
            ]
        );

        $articles = [
            [
                'title' => 'Cara Membeli Produk',
                'slug' => 'cara-membeli-produk',
                'content' => "Proses pembelian di NexaCode dirancang sangat simpel:\n\n1. Cari produk yang kamu butuhkan.\n2. Klik 'Buy Now' atau 'Add to Cart'.\n3. Pilih metode pembayaran di halaman Checkout.\n4. Lakukan pembayaran. Aset akan langsung tersedia di menu **My Purchases** setelah pembayaran terverifikasi otomatis.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Metode Pembayaran (Midtrans)',
                'slug' => 'metode-pembayaran-midtrans',
                'content' => "Kami bekerjasama dengan Midtrans untuk menyediakan metode pembayaran terlengkap dan teraman di Indonesia:\n\n- **QRIS**: Scan via GoPay, OVO, DANA, LinkAja, atau m-Banking apapun.\n- **Virtual Account**: Mandiri, BCA, BNI, BRI, Permata.\n- **Credit Card**: Visa & Master Card.\n- **Alfamart / Indomaret**: Pembayar di kasir retail terdekat.",
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Masalah Download Produk',
                'slug' => 'masalah-download-produk',
                'content' => "Jika kamu mengalami kegagalan saat mendownload:\n\n1. Pastikan koneksi internet stabil.\n2. Coba gunakan browser lain atau mode incognito.\n3. Jika file tetap tidak bisa diakses, segera buat **Support Ticket** untuk membantu kami mengirimkan file terbaru via email alternatif.",
                'is_featured' => false,
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
