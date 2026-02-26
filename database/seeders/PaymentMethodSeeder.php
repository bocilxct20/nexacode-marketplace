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
                    'Login ke akun BCA Mobile atau Internet Banking Anda',
                    'Pilih m-Transfer > Antar Rekening BCA',
                    'Masukkan nomor rekening dan nominal yang tertera di atas',
                    'Selesaikan transfer dan simpan bukti pembayaran',
                    'Unggah bukti pembayaran pada halaman konfirmasi',
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
                    'Login ke aplikasi Livin\' by Mandiri atau Internet Banking',
                    'Pilih menu Transfer > Ke Rekening Mandiri',
                    'Masukkan nomor rekening dan nominal yang tertera di atas',
                    'Selesaikan transfer dan simpan bukti pembayaran',
                    'Unggah bukti pembayaran pada halaman konfirmasi',
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
                    'Login ke aplikasi BRImo atau Internet Banking BRI',
                    'Pilih menu Transfer > Tambah Daftar Baru > BRI',
                    'Masukkan nomor rekening dan nominal yang tertera di atas',
                    'Selesaikan transfer dan simpan bukti pembayaran',
                    'Unggah bukti pembayaran pada halaman konfirmasi',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'qris',
                'name' => 'QRIS',
                'qris_static' => null, // Admin needs to upload this
                'instructions' => [
                    'Buka aplikasi mobile banking atau e-wallet (Gopay, OVO, Dana, LinkAja)',
                    'Pilih menu Scan QRIS',
                    'Scan kode QR yang ditampilkan pada halaman pembayaran',
                    'Pastikan nama merchant adalah NEXACODE dan nominal sesuai',
                    'Unggah bukti pembayaran pada halaman konfirmasi',
                ],
                'is_active' => false, // Inactive until admin uploads QRIS
                'sort_order' => 4,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(['name' => $method['name']], $method);
        }
    }
}
