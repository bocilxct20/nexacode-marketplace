<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laravel Scripts',
                'description' => 'Aplikasi Laravel premium, package, dan boilerplate framework.',
                'icon' => 'lucide-code-2',
                'sort_order' => 1,
            ],
            [
                'name' => 'Node JS Apps',
                'description' => 'Aplikasi backend dan API yang scalable dibangun menggunakan Node JS.',
                'icon' => 'lucide-server',
                'sort_order' => 2,
            ],
            [
                'name' => 'Vue Components',
                'description' => 'Komponen antarmuka interaktif dan template admin untuk Vue.js.',
                'icon' => 'lucide-layers',
                'sort_order' => 3,
            ],
            [
                'name' => 'React Templates',
                'description' => 'Template React berkinerja tinggi dan starter kit Next.js.',
                'icon' => 'lucide-atom',
                'sort_order' => 4,
            ],
            [
                'name' => 'Mobile Apps',
                'description' => 'Source code aplikasi mobile full native maupun cross-platform.',
                'icon' => 'lucide-smartphone',
                'sort_order' => 5,
            ],
            [
                'name' => 'WordPress Themes',
                'description' => 'Tema desain modern dan plugin eksklusif untuk WordPress.',
                'icon' => 'lucide-globe',
                'sort_order' => 6,
            ],
            [
                'name' => 'UI Kits',
                'description' => 'Koleksi aset desain untuk Figma, Sketch, dan Tailwind CSS.',
                'icon' => 'lucide-layout-template',
                'sort_order' => 7,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                $category
            );
        }
    }
}
