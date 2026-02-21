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
        $adminRole = \App\Models\Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'System administrator with full access.',
        ]);

        \App\Models\Role::create([
            'name' => 'Author',
            'slug' => 'author',
            'description' => 'Sellers who can upload and manage products.',
        ]);

        \App\Models\Role::create([
            'name' => 'Buyer',
            'slug' => 'buyer',
            'description' => 'Customers who purchase products.',
        ]);

        // 2. Create Admin User
        $admin = User::factory()->create([
            'name' => 'Nexacode Admin',
            'username' => 'admin',
            'email' => 'admin@nexacode.id',
            'password' => bcrypt('password123'),
        ]);

        $admin->roles()->attach($adminRole);
        $admin->roles()->attach(\App\Models\Role::where('slug', 'author')->first());
        $admin->roles()->attach(\App\Models\Role::where('slug', 'buyer')->first());

        // 3. Create Test Buyer
        $buyer = User::factory()->create([
            'name' => 'Test Buyer',
            'username' => 'testbuyer',
            'email' => 'buyer@nexacode.id',
            'password' => bcrypt('password'),
        ]);
        $buyer->roles()->attach(\App\Models\Role::where('slug', 'buyer')->first());

        // 4. Create Test Author
        $author = User::factory()->create([
            'name' => 'Test Author',
            'username' => 'testauthor',
            'email' => 'author@nexacode.id',
            'password' => bcrypt('password'),
        ]);
        $author->roles()->attach(\App\Models\Role::where('slug', 'author')->first());
        $author->roles()->attach(\App\Models\Role::where('slug', 'buyer')->first());

        // 5. Plans & Payment Methods
        $this->call([
            SubscriptionPlanSeeder::class,
            PaymentMethodSeeder::class,
        ]);

        // 6. Marketplace Seeding (Products, Sales, etc.)
        $this->call(MarketplaceSeeder::class);

        // 7. Help Center
        $this->call(HelpCenterSeeder::class);
    }
}
