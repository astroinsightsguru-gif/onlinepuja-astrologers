<?php
require '/home/onlinepuja.live/backend/vendor/autoload.php';
$app = require_once '/home/onlinepuja.live/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('astrotalk_in_news')->where('id', 10)->update([
    'channel' => 'Aaj Tak',
    'description' => 'Online Puja launches 24/7 Live Sanctum Darshan & Virtual Aarti streaming from Kashi Vishwanath, Mahakaleshwar & Somnath.',
    'newsDate' => '2026-09-28',
]);

DB::table('astrotalk_in_news')->where('id', 7)->update([
    'channel' => 'India TV',
    'description' => 'India TV features Online Puja for connecting devotees worldwide with verified Vedic Pandits and authentic temple rituals.',
    'newsDate' => '2026-09-25',
]);

DB::table('astrotalk_in_news')->where('id', 2)->update([
    'channel' => 'NDTV',
    'description' => 'NDTV highlights Online Puja as a premier spiritual-tech platform delivering certified AstroMall samagri and live consultation.',
    'newsDate' => '2026-09-20',
]);

echo "Database news entries updated successfully!\n";
