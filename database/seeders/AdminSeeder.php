<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role admin tersedia
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrator',
            'description' => 'System administrator with full access.',
        ]);

        // Ambil data dari .env atau gunakan default
        $email = env('ADMIN_EMAIL', 'admin@nexacode.id');
        $username = env('ADMIN_USERNAME', 'admin');
        $password = env('ADMIN_PASSWORD', 'password123'); // Ganti di .env VPS!

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Nexacode Admin',
                'username' => $username,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        // Pasang role admin jika belum ada
        if (!$admin->hasRole('admin')) {
            $admin->roles()->attach($adminRole);
        }

        // Pastikan juga punya role author & buyer untuk testing
        $authorRole = Role::where('slug', 'author')->first();
        $buyerRole = Role::where('slug', 'buyer')->first();

        if ($authorRole && !$admin->hasRole('author')) $admin->roles()->attach($authorRole);
        if ($buyerRole && !$admin->hasRole('buyer')) $admin->roles()->attach($buyerRole);

        $this->command->info("Admin account created/updated: $email");
    }
}
