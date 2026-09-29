<?php
require '/home/onlinepuja.live/backend/vendor/autoload.php';
$app = require_once '/home/onlinepuja.live/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TABLES MATCHING 'video' or 'aarti' or 'darshan' ===\n";
$tables = DB::select("SHOW TABLES");
foreach ($tables as $t) {
    $vals = array_values((array)$t);
    $name = $vals[0];
    if (stripos($name, 'video') !== false || stripos($name, 'aarti') !== false || stripos($name, 'darshan') !== false || stripos($name, 'news') !== false) {
        echo "Table: $name\n";
        $rows = DB::table($name)->limit(5)->get();
        echo json_encode($rows, JSON_PRETTY_PRINT) . "\n\n";
    }
}
