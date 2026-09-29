<?php
require '/home/onlinepuja.live/backend/vendor/autoload.php';
$svc1 = new App\Services\OtpService();
echo "App\\Services\\OtpService instance: " . get_class($svc1) . PHP_EOL;

$svc2 = new App\services\OtpService();
echo "App\\services\\OtpService instance: " . get_class($svc2) . PHP_EOL;

echo "SUCCESS: Both namespaces instantiate correctly!\n";
