<?php

namespace Database\Seeders\Help;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use Illuminate\Database\Seeder;

class SafetyHelpSeeder extends Seeder
{
    public function run(): void
    {
        $category = HelpCategory::updateOrCreate(
            ['slug' => 'safety'],
            [
                'name' => 'Keamanan & Kebijakan',
                'icon' => 'shield-check',
                'description' => 'Kebijakan privasi, syarat ketentuan, dan keamanan bertransaksi.',
                'sort_order' => 6,
            ]
        );

        $articles = [
            [
                'title' => 'Ketentuan Refund (Wajib Baca)',
                'slug' => 'ketentuan-refund-wajib-baca',
                'content' => "Keadilan bagi pembeli dan penjual adalah tujuan kami:\n\n- **14 Hari Jeda**: Author memiliki waktu maksimal 14 hari untuk memperbaiki bug yang dilaporkan sebelum refund dikabulkan.\n- **Tidak Berlaku**: Jika kamu berubah pikiran (change of mind) atau salah membeli produk.\n- **Penyalahgunaan**: Refund berulang yang mencurigakan dapat mengakibatkan banned akun.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Keamanan Data Pribadi',
                'slug' => 'keamanan-data-pribadi',
                'content' => "NexaCode tidak pernah membagikan data pribadi kamu kepada pihak ketiga tanpa izin. Kami mengenkripsi data sensitif dan menggunakan gateway pembayaran resmi yang terdaftar di OJK.",
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Ketentuan Refund & Dispute',
                'slug' => 'ketentuan-refund-dispute',
                'content' => "Kami menjamin kualitas setiap produk. Namun, refund dapat diajukan jika:\n\n- **Produk Error**: Tidak dapat berjalan sesuai deskripsi teknis.\n- **Support Tidak Aktif**: Author tidak merespon ticket bantuan dalam 3 hari kerja.\n\n### Catatan Penting\nDana hasil penjualan ditahan selama **24 jam** di sistem kami sebelum diteruskan ke Author. Hal ini memberikan window waktu untuk pengecekan awal bagi pembeli. Gunakan fitur **Report** atau **Refund Request** melalui Dashboard Pesanan jika menemukan masalah serius.",
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Syarat Penjualan & Hak Cipta',
                'slug' => 'syarat-penjualan-hak-cipta',
                'content' => "Author wajib memiliki hak cipta penuh atas produk yang dijual. Dilarang keras menjual produk hasil 'nulled' atau script orang lain tanpa hak penggunaan yang sah.",
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
