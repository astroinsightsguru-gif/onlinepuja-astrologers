<?php
$files = [
    '/home/onlinepuja.live/backend/app/Http/Controllers/API/Astrologer/AstrologerController.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('use App\services\OtpService;', 'use App\Services\OtpService;', $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
