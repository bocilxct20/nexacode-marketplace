<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAnalytics;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i);
                
                ProductAnalytics::create([
                    'product_id' => $product->id,
                    'date' => $date->toDateString(),
                    'views_count' => rand(50, 200),
                    'sales_count' => rand(0, 5),
                    'revenue' => rand(0, 5) * ($product->price * 0.8),
                ]);
            }
        }
    }
}
