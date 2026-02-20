<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HelpCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Help\GeneralHelpSeeder::class,
            \Database\Seeders\Help\BuyerHelpSeeder::class,
            \Database\Seeders\Help\AuthorHelpSeeder::class,
            \Database\Seeders\Help\AffiliateHelpSeeder::class,
            \Database\Seeders\Help\TechnicalHelpSeeder::class,
            \Database\Seeders\Help\SafetyHelpSeeder::class,
            \Database\Seeders\Help\SupportHelpSeeder::class,
        ]);
    }
}
