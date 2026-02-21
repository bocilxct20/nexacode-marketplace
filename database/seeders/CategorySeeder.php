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
                'description' => 'Premium Laravel applications, packages, and boilerplate code.',
                'icon' => 'lucide-code-2',
                'sort_order' => 1,
            ],
            [
                'name' => 'Node JS Apps',
                'description' => 'Scalable backend applications and NPM packages built with Node JS.',
                'icon' => 'lucide-server',
                'sort_order' => 2,
            ],
            [
                'name' => 'Vue Components',
                'description' => 'Interactive UI components and dashboard templates for Vue.js.',
                'icon' => 'lucide-layers',
                'sort_order' => 3,
            ],
            [
                'name' => 'React Templates',
                'description' => 'High-performance React templates and Next.js starter kits.',
                'icon' => 'lucide-atom',
                'sort_order' => 4,
            ],
            [
                'name' => 'Mobile Apps',
                'description' => 'Native and cross-platform mobile app source code.',
                'icon' => 'lucide-smartphone',
                'sort_order' => 5,
            ],
            [
                'name' => 'WordPress Themes',
                'description' => 'Premium themes and plugins for WordPress websites.',
                'icon' => 'lucide-globe',
                'sort_order' => 6,
            ],
            [
                'name' => 'UI Kits',
                'description' => 'Figma, Sketch, and Tailwind CSS design assets.',
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
