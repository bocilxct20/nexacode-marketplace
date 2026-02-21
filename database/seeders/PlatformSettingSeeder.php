<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Affiliate
            ['key' => 'affiliate_share_of_commission', 'value' => '50'],  // 50% of platform cut → affiliate

            // Marketplace rules
            ['key' => 'min_withdrawal',      'value' => '50000'],  // RpMin for author withdrawal request
            ['key' => 'currency_code',       'value' => 'IDR'],
            ['key' => 'currency_symbol',     'value' => 'Rp'],
            ['key' => 'auto_approve_authors',  'value' => '0'],
            ['key' => 'auto_approve_products', 'value' => '0'],
            ['key' => 'maintenance_mode',      'value' => '0'],

            // General
            ['key' => 'site_name',     'value' => 'NexaCode Marketplace'],
            ['key' => 'support_email', 'value' => 'support@nexacode.id'],

            // SEO
            ['key' => 'meta_title',       'value' => 'NexaCode - Digital Marketplace'],
            ['key' => 'meta_description', 'value' => 'Premium source code and digital products for developers.'],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
