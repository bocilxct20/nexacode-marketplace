<?php

namespace Database\Seeders;

use App\Models\Earning;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\ProductVersion;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\SupportReply;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\EarningStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $authorRole = Role::firstOrCreate(['slug' => 'author'], [
            'name' => 'Author',
            'description' => 'Sellers who can upload and manage products.',
        ]);

        $buyerRole = Role::firstOrCreate(['slug' => 'buyer'], [
            'name' => 'Buyer',
            'description' => 'Customers who purchase products.',
        ]);

        // Create Authors with Tiers
        $plans = \App\Models\SubscriptionPlan::all();
        
        $authorData = [
            ['name' => 'NexaLabs Studio', 'username' => 'nexalabs', 'email' => 'team@nexalabs.dev'],
            ['name' => 'PixelForge UI', 'username' => 'pixelforge', 'email' => 'hello@pixelforge.io'],
            ['name' => 'Ahmad Dani', 'username' => 'ahmaddani', 'email' => 'dani@nexacode.id'],
            ['name' => 'WebArtisan ID', 'username' => 'webartisan', 'email' => 'contact@webartisan.co.id'],
            ['name' => 'CodeCrafters', 'username' => 'codecrafters', 'email' => 'support@codecrafters.com'],
            ['name' => 'SaaS Mastery', 'username' => 'saasmastery', 'email' => 'admin@saasmastery.tech']
        ];

        $authors = collect();
        foreach ($authorData as $index => $data) {
            $author = User::firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => bcrypt('Password123!'),
                'email_verified_at' => now(),
            ]);
            $author->roles()->attach($authorRole);
            
            // Assign varying plans
            if ($index === 0) {
                // At least one Elite
                $plan = $plans->where('slug', 'elite')->first();
            } elseif ($index === 1 || $index === 2) {
                // Some Pro
                $plan = $plans->where('slug', 'pro')->first();
            } else {
                // Rest are Basic
                $plan = $plans->where('slug', 'basic')->first();
            }
            
            $author->update([
                'subscription_plan_id' => $plan->id,
                'subscription_ends_at' => now()->addMonths(1),
                'xp' => rand(1000, 10000),
                'level' => rand(5, 20),
            ]);

            $authors->push($author);
        }

        // Create Buyers
        $buyerData = [
            ['name' => 'Budi Santoso', 'username' => 'budisantoso', 'email' => 'budi.santoso@gmail.com'],
            ['name' => 'Reza Pratama', 'username' => 'rezapratama', 'email' => 'reza.p@yahoo.com'],
            ['name' => 'Sarah Wijaya', 'username' => 'sarahwijaya', 'email' => 'sarah.w@outlook.com'],
            ['name' => 'Digital Agency JKT', 'username' => 'digitaljkt', 'email' => 'hello@digitaljkt.co.id'],
            ['name' => 'Global Tech Solusi', 'username' => 'globaltech', 'email' => 'info@globaltech.id']
        ];

        $buyers = collect();
        foreach ($buyerData as $data) {
            $buyer = User::firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => bcrypt('Password123!'),
                'email_verified_at' => now(),
            ]);
            $buyer->roles()->attach($buyerRole);
            $buyers->push($buyer);
        }

        // Create Tags
        $tags = [
            ['name' => 'PHP Scripts', 'slug' => 'php-scripts'],
            ['name' => 'WordPress', 'slug' => 'wordpress'],
            ['name' => 'Mobile Apps', 'slug' => 'mobile-apps'],
            ['name' => 'HTML/CSS', 'slug' => 'html-css'],
            ['name' => 'UI Kits', 'slug' => 'ui-kits'],
            ['name' => 'Plugins', 'slug' => 'plugins'],
        ];

        foreach ($tags as $tagData) {
            ProductTag::firstOrCreate(['slug' => $tagData['slug']], $tagData);
        }

        $allTags = ProductTag::all();

        // Create Products
        $productNames = [
            'Premium SaaS Starter Kit',
            'E-commerce Multi-vendor Platform',
            'AI Image Generator Dashboard',
            'Modern Portfolio Template',
            'Real Estate Management System',
            'Learning Management System (LMS)',
            'Hospital Management Script',
            'Crypto Exchange Portal',
            'Fitness Tracking Mobile App',
            'Ultimate CRM & HRMS',
        ];

        $products = collect();

        foreach ($productNames as $name) {
            $product = Product::firstOrCreate(['slug' => Str::slug($name)], [
                'author_id' => $authors->random()->id,
                'name' => $name,
                'description' => 'Source code premium untuk ' . $name . '. Dibangun menggunakan stack teknologi teringan dan paling modern. Dilengkapi dengan dokumentasi instalasi lengkap, panduan deployment, dan dukungan update versi gratis selamanya. Sangat cocok digunakan untuk mempercepat proses development project klien atau startup Anda.',
                'thumbnail' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=800&auto=format&fit=crop',
                'demo_url' => 'https://example.com/demo',
                'price' => rand(50000, 499000),
                'status' => 'approved',
                'avg_rating' => rand(40, 50) / 10,
                'sales_count' => rand(10, 500),
            ]);

            $products->push($product);

            // Attach 1-2 random tags if none exist
            if ($product->tags()->count() === 0) {
                $product->tags()->attach($allTags->random(rand(1, 2))->pluck('id'));
            }

            // Create 2 versions
            ProductVersion::firstOrCreate([
                'product_id' => $product->id,
                'version_number' => '1.0.0'
            ], [
                'changelog' => 'Initial release.',
                'file_path' => 'products/' . $product->slug . '-v1.0.0.zip',
            ]);

            ProductVersion::firstOrCreate([
                'product_id' => $product->id,
                'version_number' => '1.1.0'
            ], [
                'changelog' => 'Bug fixes and performance improvements.',
                'file_path' => 'products/' . $product->slug . '-v1.1.0.zip',
            ]);
        }

        // Create some Orders, Licenses, and Earnings
        foreach ($buyers as $buyer) {
            // Each buyer buys 1-3 products
            $purchasedProducts = $products->random(rand(1, 3));
            
            foreach ($purchasedProducts as $product) {
                $order = new Order([
                    'buyer_id' => $buyer->id,
                    'total_amount' => $product->price,
                    'status' => OrderStatus::COMPLETED,
                ]);
                $order->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                ]);

                // Create Earning for author (80% commission)
                Earning::create([
                    'author_id' => $product->author_id,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'amount' => $product->price * 0.8,
                    'status' => EarningStatus::PENDING,
                ]);

                // Create a support ticket for half the orders
                if (rand(0, 1)) {
                    $ticket = SupportTicket::create([
                        'user_id' => $buyer->id,
                        'product_id' => $product->id,
                        'subject' => 'Question about ' . $product->name,
                        'status' => 'open',
                        'priority' => 'medium',
                    ]);

                    SupportReply::create([
                        'support_ticket_id' => $ticket->id,
                        'user_id' => $buyer->id,
                        'message' => 'Halo min, saya butuh panduan untuk proses instalasi script ini di VPS Ubuntu. Apakah ada dokumentasinya?',
                    ]);
                }
            }
        }
    }
}
