<?php
require '/home/onlinepuja.live/backend/vendor/autoload.php';
$app = require_once '/home/onlinepuja.live/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$news = Illuminate\Support\Facades\DB::table('astrotalk_in_news')->get();
echo "Found " . count($news) . " items in astrotalk_in_news:\n";
foreach ($news as $n) {
    echo "ID: {$n->id}, Channel: {$n->channel}, Desc: {$n->description}\n";
}
