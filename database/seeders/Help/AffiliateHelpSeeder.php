<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class AffiliateHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'affiliate'],
            [
                'name' => 'Program Affiliate',
                'icon' => 'share',
                'description' => 'Hasilkan pendapatan pasif dengan mengajak orang lain menggunakan NexaCode.',
                'sort_order' => 4,
            ]
        );

        $articles = [
            [
                'title' => 'Cara Kerja Link Affiliate',
                'slug' => 'cara-kerja-link-affiliate',
                'content' => 'Gunakan link khusus dari dashboard kamu. Setiap pembelian yang dilakukan melalui link tersebut akan memberikan kamu komisi referral instan.',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Perhitungan Komisi Referral',
                'slug' => 'perhitungan-komisi-referral',
                'content' => 'Kamu mendapatkan 10% dari setiap transaksi pelanggan baru yang menggunakan link kamu. Komisi ini berlaku untuk semua produk di marketplace.',
                'is_featured' => true,
                'sort_order' => 2,
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
