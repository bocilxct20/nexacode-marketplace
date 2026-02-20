<?php

namespace App\Services;

class ChatProtectionService
{
    /**
     * Patterns to detect potential platform leakage.
     */
    protected static $patterns = [
        'email' => '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}/i',
        'phone' => '/(\+?62|08)[0-9]{9,13}/', // Indonesian phone format
        'whatsapp' => '/(?:wa\.me|whatsapp\.com|08[0-9]{9,13})/i',
        'telegram' => '/(?:t\.me|telegram\.me)/i',
        'external_pay' => '/(?:paypal\.me|dana\.id|gopay|ovo|transfer bank)/i',
    ];

    /**
     * Scan text for suspicious patterns.
     * Returns an array of detected types.
     */
    public static function scan($text)
    {
        $detected = [];

        foreach (self::$patterns as $key => $pattern) {
            if (preg_match($pattern, $text)) {
                $detected[] = $key;
            }
        }

        return $detected;
    }

    /**
     * Get a warning message based on detected patterns.
     */
    public static function getWarningMessage(array $detected)
    {
        if (empty($detected)) return null;

        return "⚠️ Demi keamanan dan jaminan perlindungan NexaCode, harap tetap bertransaksi dan berkomunikasi di dalam platform. Berbagi kontak pribadi dapat membatalkan garansi bantuan kami.";
    }
}
