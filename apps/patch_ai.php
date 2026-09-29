<?php
$f = '/home/onlinepuja.live/backend/app/Http/Controllers/ApiChatGPTController.php';
$c = file_get_contents($f);

// Remove any broken line
$c = preg_replace('/^\s*if \(!->has.*$/m', '', $c);

// Insert clean question fallback
$target = '    public function ask(Request $request)
    {';
$repl = '    public function ask(Request $request)
    {
        if (!$request->has(\'message\') && $request->has(\'question\')) {
            $request->merge([\'message\' => $request->input(\'question\')]);
        }';

$c = str_replace($target, $repl, $c);
file_put_contents($f, $c);
echo "SUCCESS\n";
