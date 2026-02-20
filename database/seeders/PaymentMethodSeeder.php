<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'type' => 'bank_transfer',
                'name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'NEXACODE',
                'instructions' => [
                    'Login to your BCA mobile banking or internet banking',
                    'Select Transfer > To BCA Account',
                    'Enter the account number and amount shown above',
                    'Complete the transfer and save the receipt',
                    'Upload the receipt on the payment page',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'bank_transfer',
                'name' => 'Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'NEXACODE',
                'instructions' => [
                    'Login to your Mandiri mobile banking or internet banking',
                    'Select Transfer > To Mandiri Account',
                    'Enter the account number and amount shown above',
                    'Complete the transfer and save the receipt',
                    'Upload the receipt on the payment page',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'bank_transfer',
                'name' => 'BRI',
                'account_number' => '5555666677778888',
                'account_name' => 'NEXACODE',
                'instructions' => [
                    'Login to your BRI mobile banking or internet banking',
                    'Select Transfer > To BRI Account',
                    'Enter the account number and amount shown above',
                    'Complete the transfer and save the receipt',
                    'Upload the receipt on the payment page',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'qris',
                'name' => 'QRIS',
                'qris_static' => null, // Admin needs to upload this
                'instructions' => [
                    'Open your mobile banking or e-wallet app',
                    'Select QRIS payment option',
                    'Scan the QR code displayed on the payment page',
                    'Verify the amount and complete the payment',
                    'Upload the payment receipt',
                ],
                'is_active' => false, // Inactive until admin uploads QRIS
                'sort_order' => 4,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
