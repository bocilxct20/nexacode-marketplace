<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class SupportHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'support'],
            [
                'name' => 'Support & Layanan Priority',
                'icon' => 'chat-bubble-left-right',
                'description' => 'Cara mendapatkan bantuan teknis dan fasilitas khusus untuk member Elite.',
                'sort_order' => 7,
            ]
        );

        $articles = [
            [
                'title' => 'Cara Membuat Support Ticket',
                'slug' => 'cara-membuat-support-ticket',
                'content' => "Jika kamu butuh bantuan dari Author produk:\n\n1. Buka menu **Support** di dashboard.\n2. Klik 'Create Ticket'.\n3. **Penting**: Kamu hanya bisa membuat ticket untuk produk yang sudah kamu beli dan status pesanan sudah **Completed**.\n4. Deskripsikan masalah dengan jelas dan lampirkan screenshot jika perlu.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Priority Support (Elite Author)',
                'slug' => 'priority-support-elite-author',
                'content' => "Elite Author mendapatkan penanganan khusus dari tim internal NexaCode. Ticket yang kamu ajukan akan mendapatkan label 'High Priority' dan dijawab oleh dedicated manajer.",
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Etika Bertanya di Help Center',
                'slug' => 'etika-bertanya-di-help-center',
                'content' => "Gunakan bahasa yang sopan. Author adalah manusia yang butuh waktu untuk memeriksa kode. Respon biasanya diberikan dalam 1-3 hari kerja tergantung tingkat kesulitan masalah.",
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
