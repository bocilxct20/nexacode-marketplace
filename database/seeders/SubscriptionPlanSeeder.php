<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 0,
                'commission_rate' => 20.00,
                'features' => [
                    'Commission Rate: 20%',
                    'Standard Support',
                    'Standard Profile Visibility',
                ],
                'is_active' => true,
                'is_default' => true,
                'allow_trial' => false,
                'is_elite' => false,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 49000,
                'commission_rate' => 15.00,
                'features' => [
                    'Commission Rate: 15%',
                    '3 Featured Product Slots',
                    'Pro Badge on Profile',
                    'Priority Support',
                ],
                'is_active' => true,
                'is_default' => false,
                'allow_trial' => true,
                'is_elite' => false,
            ],
            [
                'name' => 'Elite',
                'slug' => 'elite',
                'price' => 149000,
                'commission_rate' => 10.00,
                'features' => [
                    'Commission Rate: 10%',
                    'Unlimited Featured Slots',
                    'Elite Badge on Profile',
                    'Dedicated Manager',
                    'Early Access to New Features',
                ],
                'is_active' => true,
                'is_default' => false,
                'allow_trial' => false,
                'is_elite' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
