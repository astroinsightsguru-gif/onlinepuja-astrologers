<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BannerStudioService
{
    protected string $storageDir;
    protected string $baseUrl;

    public function __construct()
    {
        $this->storageDir = public_path('storage/banners');
        $this->baseUrl = config('app.url', 'https://onlinepuja.live');
        if (!File::exists($this->storageDir)) {
            File::makeDirectory($this->storageDir, 0755, true);
        }
    }

    /**
     * Synthesize high-definition promotional banners for devotional campaigns.
     *
     * @param string $festivalName Festival title (e.g. "Dev Deepawali & Maha Deepdaan 2026")
     * @param string $format 'landscape' (1200x630) or 'square' (1080x1080)
     * @param array $options Custom text, pricing, dates, and badges
     * @return array
     */
    public function generateBanner(string $festivalName, string $format = 'landscape', array $options = []): array
    {
        if (!extension_loaded('gd')) {
            return ['success' => false, 'error' => 'PHP GD extension is not loaded'];
        }

        $width = $format === 'square' ? 1080 : 1200;
        $height = $format === 'square' ? 1080 : 630;

        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // Color Palette
        $navyDark = imagecolorallocate($img, 11, 14, 23);
        $navyLight = imagecolorallocate($img, 24, 30, 48);
        $saffron = imagecolorallocate($img, 245, 158, 11);
        $gold = imagecolorallocate($img, 251, 191, 36);
        $goldLight = imagecolorallocate($img, 254, 243, 199);
        $vermilion = imagecolorallocate($img, 225, 29, 72);
        $white = imagecolorallocate($img, 255, 255, 255);
        $slate = imagecolorallocate($img, 148, 163, 184);

        // 1. Draw rich gradient background (Twilight Navy to Deep Saffron Charcoal)
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = (int) (11 + ($ratio * 25));
            $g = (int) (14 + ($ratio * 16));
            $b = (int) (23 + ($ratio * 8));
            $color = imagecolorallocate($img, min(255, $r), min(255, $g), min(255, $b));
            imageline($img, 0, $y, $width, $y, $color);
        }

        // 2. Draw Sacred Temple Mandala Arch / Border
        $borderMargin = 20;
        imagesetthickness($img, 2);
        imagerectangle($img, $borderMargin, $borderMargin, $width - $borderMargin, $height - $borderMargin, $saffron);
        imagerectangle($img, $borderMargin + 6, $borderMargin + 6, $width - ($borderMargin + 6), $height - ($borderMargin + 6), $gold);

        // Corner Sacred Embellishments
        $corners = [
            [$borderMargin + 10, $borderMargin + 10],
            [$width - ($borderMargin + 25), $borderMargin + 10],
            [$borderMargin + 10, $height - ($borderMargin + 25)],
            [$width - ($borderMargin + 25), $height - ($borderMargin + 25)],
        ];
        foreach ($corners as $c) {
            imagefilledrectangle($img, $c[0], $c[1], $c[0] + 15, $c[1] + 15, $saffron);
        }

        // 3. Top Branding Header
        $brandHeader = "ONLINEPUJA.LIVE  •  OFFICIAL SACRED TEERTHA KASHI";
        imagestring($img, 4, ($width / 2) - (strlen($brandHeader) * 4), 40, $brandHeader, $goldLight);

        // 4. Main Event Title
        $title = mb_strtoupper($options['title'] ?? $festivalName);
        $titleFont = 5;
        $titleX = ($width / 2) - (strlen($title) * 4.5);
        $titleY = $format === 'square' ? 130 : 90;
        // Drop shadow
        imagestring($img, $titleFont, (int)$titleX + 2, (int)$titleY + 2, $title, $vermilion);
        imagestring($img, $titleFont, (int)$titleX, (int)$titleY, $title, $gold);

        // 5. Subtitle & Muhurat
        $subtitle = $options['subtitle'] ?? "108 Deepdaan on Manikarnika & Assi Ghats • Kartik Purnima 2026";
        $subX = ($width / 2) - (strlen($subtitle) * 4);
        $subY = $titleY + 40;
        imagestring($img, 4, (int)$subX, (int)$subY, $subtitle, $white);

        $muhurat = $options['muhurat'] ?? "Shubh Deepdaan Muhurat: 5:08 PM to 7:47 PM (Tuesday, Nov 24, 2026)";
        $muhX = ($width / 2) - (strlen($muhurat) * 3.5);
        imagestring($img, 3, (int)$muhX, (int)$subY + 30, $muhurat, $saffron);

        // 6. Tiered Offerings Box
        $boxTop = $format === 'square' ? 320 : 220;
        $boxHeight = $format === 'square' ? 360 : 210;
        imagefilledrectangle($img, 60, $boxTop, $width - 60, $boxTop + $boxHeight, $navyLight);
        imagerectangle($img, 60, $boxTop, $width - 60, $boxTop + $boxHeight, $saffron);

        // Offerings Title
        $pkgHeader = "CHOOSE YOUR SACRED DEEPDAAN SANKALP TIER";
        imagestring($img, 4, ($width / 2) - (strlen($pkgHeader) * 4.5), $boxTop + 15, $pkgHeader, $gold);

        // Packages
        $packages = $options['packages'] ?? [
            ["tier" => "11 Ganga Deepdaan", "price" => "Rs. 501", "desc" => "Pure Brass & Earthen Diyas with Ganga Aarti Sankalp"],
            ["tier" => "21 Sacred Deepdaan", "price" => "Rs. 1,100", "desc" => "Assi Ghat Dipavalli Invocation + Personal Gotra Chanting"],
            ["tier" => "51 Ghat Deepdaan", "price" => "Rs. 2,100", "desc" => "Manikarnika & Dashashwamedh Special Deepdaan with Purohit"],
            ["tier" => "108 Maha Deepdaan", "price" => "Rs. 3,500", "desc" => "Grand Ashtottara Sata Deepam + Kashi Prasad Delivered Home"],
        ];

        $yOffset = $boxTop + 55;
        foreach ($packages as $idx => $pkg) {
            $line = "• " . str_pad($pkg['tier'], 24) . " [" . $pkg['price'] . "]  —  " . $pkg['desc'];
            imagestring($img, 4, 90, $yOffset, $line, $white);
            $yOffset += $format === 'square' ? 70 : 35;
        }

        // 7. Trust Badges
        $badgeY = $boxTop + $boxHeight + ($format === 'square' ? 45 : 30);
        $badge1 = "✓ HD Video Sankalp delivered on WhatsApp";
        $badge2 = "✓ Consecrated Kashi Prasad couriered to your home";
        $badge3 = "✓ Authentic Vedic Purohits of Varanasi";

        imagestring($img, 3, 100, $badgeY, $badge1, $goldLight);
        imagestring($img, 3, ($width / 2) - (strlen($badge2) * 3), $badgeY, $badge2, $goldLight);
        imagestring($img, 3, $width - 340, $badgeY, $badge3, $goldLight);

        // 8. Footer Call-to-Action Bar
        $footerTop = $height - 75;
        imagefilledrectangle($img, $borderMargin + 6, $footerTop, $width - ($borderMargin + 6), $height - ($borderMargin + 6), $saffron);

        $ctaText = "BOOK SANKALP ONLINE: https://onlinepuja.live/pujas  •  HELPLINE: +91 70071 58014";
        $ctaX = ($width / 2) - (strlen($ctaText) * 4.5);
        imagestring($img, 5, (int)$ctaX, $footerTop + 15, $ctaText, $navyDark);

        // Save banner to disk
        $filename = 'banner_' . substr(md5($festivalName . $format . microtime(true)), 0, 14) . '.jpg';
        $fullPath = $this->storageDir . '/' . $filename;
        imagejpeg($img, $fullPath, 92);
        imagedestroy($img);

        $bannerUrl = $this->baseUrl . '/storage/banners/' . $filename;

        return [
            'success'    => true,
            'format'     => $format,
            'width'      => $width,
            'height'     => $height,
            'filename'   => $filename,
            'path'       => $fullPath,
            'banner_url' => $bannerUrl,
        ];
    }
}
