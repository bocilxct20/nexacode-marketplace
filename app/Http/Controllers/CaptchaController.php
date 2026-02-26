<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    /**
     * Generate a super-strong image captcha.
     *
     * Security layers:
     * - UUID per-instance key → prevents multi-tab session collision
     * - 6-char alphanumeric (no ambiguous 0/O/1/I/l chars)
     * - PNG rendered with noise pixels, random lines, per-char rotation
     * - Answer stored as HMAC-SHA256 (never plain) → tamper-proof
     * - 5-minute expiry baked into session
     * - One-time use: token deleted after first use in Register component
     */
    public function generate(Request $request): Response
    {
        // Validate the instance key (UUID format) to prevent session key injection
        $key = $request->query('key', '');
        if (!preg_match('/^[0-9a-f\-]{36}$/i', $key)) {
            abort(422, 'Invalid captcha key.');
        }

        $text = $this->generateText();

        // Store HMAC of lowercased answer under per-instance session key
        session([
            "captcha_{$key}" => [
                'token'      => hash_hmac('sha256', strtoupper($text), config('app.key')),
                'expires_at' => now()->addMinutes(5)->toDateTimeString(),
            ]
        ]);

        $image = $this->renderImage($text);

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /** 6 uppercase alphanumeric chars — no ambiguous 0/O/1/I/l */
    private function generateText(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $text  = '';
        for ($i = 0; $i < 6; $i++) {
            $text .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $text;
    }

    /** Render a 170×55 distorted PNG using GD */
    private function renderImage(string $text)
    {
        $width  = 170;
        $height = 55;

        $img = imagecreatetruecolor($width, $height);

        // ── Background ──────────────────────────────────────────────────────
        $bgColor = imagecolorallocate($img, 15, 15, 30);
        imagefill($img, 0, 0, $bgColor);

        // ── Wavy sine-wave background stripes ───────────────────────────────
        for ($y = 0; $y < $height; $y++) {
            $stripeColor = imagecolorallocatealpha(
                $img,
                random_int(20, 50),
                random_int(20, 80),
                random_int(80, 150),
                random_int(60, 90)
            );
            imageline($img, 0, $y, $width, $y + random_int(-3, 3), $stripeColor);
        }

        // ── Random noise lines ───────────────────────────────────────────────
        for ($i = 0; $i < 8; $i++) {
            $lineColor = imagecolorallocatealpha(
                $img,
                random_int(100, 200),
                random_int(100, 200),
                random_int(100, 200),
                random_int(30, 60)
            );
            imageline(
                $img,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $lineColor
            );
        }

        // ── Random noise dots ────────────────────────────────────────────────
        for ($i = 0; $i < 300; $i++) {
            $dotColor = imagecolorallocatealpha(
                $img,
                random_int(150, 255),
                random_int(150, 255),
                random_int(150, 255),
                random_int(40, 80)
            );
            imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $dotColor);
        }

        // ── Draw each character with individual slight rotation ──────────────
        $fontPath = $this->getFontPath();
        $fontSize = 22;
        $x        = 12;

        for ($i = 0; $i < strlen($text); $i++) {
            $char  = $text[$i];
            $angle = random_int(-18, 18);
            $y     = random_int(38, 46);

            $r = random_int(160, 255);
            $g = random_int(160, 255);
            $b = random_int(160, 255);

            // Dark shadow for depth
            $shadow = imagecolorallocate($img, max(0, $r - 80), max(0, $g - 80), max(0, $b - 80));
            imagettftext($img, $fontSize, $angle, $x + 1, $y + 1, $shadow, $fontPath, $char);

            $charColor = imagecolorallocate($img, $r, $g, $b);
            imagettftext($img, $fontSize, $angle, $x, $y, $charColor, $fontPath, $char);

            $x += random_int(22, 26);
        }

        // ── Overlay arcs for extra distortion ───────────────────────────────
        for ($i = 0; $i < 3; $i++) {
            $arcColor = imagecolorallocatealpha(
                $img,
                random_int(100, 200),
                random_int(100, 200),
                random_int(100, 200),
                random_int(50, 70)
            );
            imagearc(
                $img,
                random_int(0, $width),
                random_int(0, $height),
                random_int(60, 160),
                random_int(20, 60),
                0, 360,
                $arcColor
            );
        }

        return $img;
    }

    /** Return absolute path to a TTF font (priority: bundled > Windows > Linux) */
    private function getFontPath(): string
    {
        // 1. Bundled monospace font (best visual quality + consistency)
        $bundled = public_path('fonts/RobotoMono-Bold.ttf');
        if (file_exists($bundled)) {
            return $bundled;
        }

        // 2. Windows system font fallback
        $windows = 'C:/Windows/Fonts/arial.ttf';
        if (file_exists($windows)) {
            return $windows;
        }

        // 3. Linux/Ubuntu fallback
        $linux = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
        if (file_exists($linux)) {
            return $linux;
        }

        abort(500, 'No TTF font found for captcha rendering. Please ensure public/fonts/RobotoMono-Bold.ttf exists.');
    }
}
