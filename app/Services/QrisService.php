<?php

namespace App\Services;

class QrisService
{
    /**
     * Generate dynamic QRIS from static QRIS with specific amount
     * Based on: https://github.com/verssache/qris-dinamis
     *
     * @param string $staticQris The static QRIS code
     * @param float $amount The transaction amount
     * @param array|null $fee Optional fee configuration ['type' => 'fixed'|'percent', 'value' => amount]
     * @return string The dynamic QRIS code
     */
    public function generateDynamic(string $staticQris, float $amount, ?array $fee = null): string
    {
        // Remove the last 4 characters (CRC16 checksum)
        $qris = substr($staticQris, 0, -4);

        // Change from static (010211) to dynamic (010212)
        $step1 = str_replace('010211', '010212', $qris);

        // Split by '5802ID' to insert amount
        $step2 = explode('5802ID', $step1);

        // Format amount: 54 + length (2 digits) + amount
        $amountStr = number_format($amount, 0, '', '');
        $uang = '54' . sprintf('%02d', strlen($amountStr)) . $amountStr;

        // Add fee if provided
        if ($fee) {
            $feeStr = $this->formatFee($fee);
            $uang .= $feeStr;
        }

        $uang .= '5802ID';

        // Combine parts
        $fix = $step2[0] . $uang . $step2[1];

        // Add CRC16 checksum
        $fix .= $this->calculateCRC16($fix);

        return $fix;
    }

    /**
     * Format fee for QRIS
     *
     * @param array $fee Fee configuration
     * @return string Formatted fee string
     */
    private function formatFee(array $fee): string
    {
        $type = $fee['type'] ?? 'fixed';
        $value = $fee['value'] ?? 0;

        if ($type === 'fixed') {
            // Fixed fee in Rupiah: 55020256 + length + value
            return '55020256' . sprintf('%02d', strlen($value)) . $value;
        } elseif ($type === 'percent') {
            // Percentage fee: 55020357 + length + value
            return '55020357' . sprintf('%02d', strlen($value)) . $value;
        }

        return '';
    }

    /**
     * Calculate CRC16 checksum for QRIS
     *
     * @param string $str The string to calculate checksum for
     * @return string The 4-character hex checksum
     */
    private function calculateCRC16(string $str): string
    {
        $crc = 0xFFFF;
        $strlen = strlen($str);

        for ($c = 0; $c < $strlen; $c++) {
            $crc ^= ord($str[$c]) << 8;
            
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        $hex = strtoupper(dechex($crc));

        // Pad with zero if needed
        if (strlen($hex) == 3) {
            $hex = '0' . $hex;
        }

        return $hex;
    }
}
