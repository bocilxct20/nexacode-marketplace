<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = \App\Models\Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrator',
            'description' => 'System administrator with full access.',
        ]);

        $authorRole = \App\Models\Role::firstOrCreate(['slug' => 'author'], [
            'name' => 'Author',
            'description' => 'Sellers who can upload and manage products.',
        ]);

        $buyerRole = \App\Models\Role::firstOrCreate(['slug' => 'buyer'], [
            'name' => 'Buyer',
            'description' => 'Customers who purchase products.',
        ]);

        // 2. Create Admin User
        $admin = User::firstOrCreate(['email' => env('ADMIN_EMAIL', 'admin@nexacode.id')], [
            'name' => 'Nexacode Admin',
            'username' => 'admin',
            'password' => bcrypt(env('ADMIN_PASSWORD', 'ChangeMe_AtFirstLogin!')),
        ]);

        if (!$admin->roles()->where('slug', 'admin')->exists()) {
            $admin->roles()->attach($adminRole);
        }
        if (!$admin->roles()->where('slug', 'author')->exists()) {
            $admin->roles()->attach($authorRole);
        }
        if (!$admin->roles()->where('slug', 'buyer')->exists()) {
            $admin->roles()->attach($buyerRole);
        }

        // Test Buyer & Author — Skip in production
        if (app()->environment('local', 'staging')) {
            $buyer = User::firstOrCreate(['email' => 'buyer@nexacode.id'], [
                'name' => 'Test Buyer',
                'username' => 'testbuyer',
                'password' => bcrypt('password'),
            ]);
            if (!$buyer->roles()->where('slug', 'buyer')->exists()) {
                $buyer->roles()->attach($buyerRole);
            }

            $author = User::firstOrCreate(['email' => 'author@nexacode.id'], [
                'name' => 'Test Author',
                'username' => 'testauthor',
                'password' => bcrypt('password'),
            ]);
            if (!$author->roles()->where('slug', 'author')->exists()) {
                $author->roles()->attach($authorRole);
            }
            if (!$author->roles()->where('slug', 'buyer')->exists()) {
                $author->roles()->attach($buyerRole);
            }
        }

        // 5. Plans, Payment Methods & Platform Settings
        $this->call([
            SubscriptionPlanSeeder::class,
            PaymentMethodSeeder::class,
            CategorySeeder::class,
            PlatformSettingSeeder::class,
        ]);

        // 6. Marketplace Seeding (Products, Sales, etc.)
        $this->call(MarketplaceSeeder::class);

        // 7. Help Center
        $this->call(HelpCenterSeeder::class);
    }
}
