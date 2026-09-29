<?php
// Patch ChatRequestController.php
$controllerPath = '/home/onlinepuja.live/backend/app/Http/Controllers/API/User/ChatRequestController.php';
$content = file_get_contents($controllerPath);

$target = "            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                \$id = Auth::guard('api')->user()->id;
            }
            \$id = \$req->userId ? \$req->userId : \$id;";

$replacement = "            \$id = \$req->userId;
            if (!\$id && Auth::guard('api')->check()) {
                \$id = Auth::guard('api')->user()->id;
            }
            if (!\$id) {
                return response()->json(['error' => 'UserId or Auth token required', 'status' => 401], 401);
            }";

if (strpos($content, $target) !== false) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($controllerPath, $content);
    echo "ChatRequestController patched successfully!\n";
} else {
    echo "Target string not found in ChatRequestController\n";
}

// Patch CallRequestController.php for call_type & call_duration
$callControllerPath = '/home/onlinepuja.live/backend/app/Http/Controllers/API/User/CallRequestController.php';
$callContent = file_get_contents($callControllerPath);

$targetCall = '    private function processCallRequest($id, $req)
    {';

$replCall = '    private function processCallRequest($id, $req)
    {
        $rawType = $req->call_type ?? $req->callType;
        if ($rawType == 11 || (string)$rawType === "11" || strtolower((string)$rawType) === "video") {
            $req->merge(["call_type" => 11]);
        } else {
            $req->merge(["call_type" => 10]);
        }
        if (!$req->call_duration || !is_numeric($req->call_duration)) {
            $req->merge(["call_duration" => 300]);
        }';

if (strpos($callContent, $targetCall) !== false) {
    $callContent = str_replace($targetCall, $replCall, $callContent);
    file_put_contents($callControllerPath, $callContent);
    echo "CallRequestController patched successfully!\n";
} else {
    echo "Target string not found in CallRequestController\n";
}
