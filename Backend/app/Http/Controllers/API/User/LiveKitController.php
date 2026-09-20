<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * LiveKit (free self-hosted WebRTC SFU) access-token minting for the v2 apps.
 *
 * Replaces the per-minute-billed Agora/Zego/HMS token endpoints for the new
 * Flutter apps (docs/new-apps/04 §4.2 + 05 §5.3). The LiveKit server itself
 * runs on our own VPS in Docker, so voice/video/live minutes are free.
 *
 * The token is a standard HS256 JWT with the LiveKit video grant; the API key
 * must be set as the `kid` header so the LiveKit server can pick the matching
 * secret. Implemented in pure PHP — no new composer packages required.
 *
 * Env: LIVEKIT_API_KEY, LIVEKIT_API_SECRET, LIVEKIT_WS_URL.
 */
class LiveKitController extends Controller
{
    public function token(Request $req)
    {
        try {
            $apiKey = (string) env('LIVEKIT_API_KEY', '');
            $apiSecret = (string) env('LIVEKIT_API_SECRET', '');
            if ($apiKey === '' || $apiSecret === '') {
                return response()->json([
                    'status' => 400,
                    'error' => ['livekit' => ['LiveKit is not configured on the server (set LIVEKIT_API_KEY / LIVEKIT_API_SECRET).']],
                ], 400);
            }

            // Room name: prefer explicit `room`, fall back to the session id so
            // both sides join the same room for one consultation session.
            $room = trim((string) ($req->room ?? $req->roomId ?? $req->sessionId ?? ''));
            if ($room === '') {
                return response()->json([
                    'status' => 400,
                    'error' => ['room' => ['The room field is required.']],
                ], 400);
            }

            $identity = trim((string) ($req->identity ?? ''));
            if ($identity === '') {
                $identity = 'guest-' . bin2hex(random_bytes(4));
            }

            $ttl = (int) ($req->ttl ?? 7200);
            if ($ttl < 300) {
                $ttl = 300;
            }
            if ($ttl > 86400) {
                $ttl = 86400;
            }

            $now = Carbon::now()->timestamp;
            $payload = [
                'exp' => $now + $ttl,
                'iss' => $apiKey,
                'nbf' => $now - 10,
                'sub' => $identity,
                'name' => (string) ($req->displayName ?? $identity),
                'metadata' => (string) ($req->metadata ?? ''),
                'video' => [
                    'room' => $room,
                    'roomJoin' => true,
                    'canPublish' => true,
                    'canPublishData' => true,
                    'canSubscribe' => true,
                    'hidden' => false,
                    'recorder' => false,
                ],
            ];

            return response()->json([
                'status' => 200,
                'recordList' => [
                    'token' => self::encodeJwt($payload, $apiKey, $apiSecret),
                    'wsUrl' => (string) env('LIVEKIT_WS_URL', 'wss://rtc.onlinepuja.live'),
                    'room' => $room,
                    'identity' => $identity,
                    'expiresIn' => $ttl,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * HS256 JWT with the LiveKit-required `kid` header.
     * (base64url segments signed with the LiveKit API secret.)
     */
    private static function encodeJwt(array $payload, string $apiKey, string $secret): string
    {
        $b64 = static function (string $raw): string {
            return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
        };

        $header = ['alg' => 'HS256', 'typ' => 'JWT', 'kid' => $apiKey];

        $segments = [
            $b64((string) json_encode($header)),
            $b64((string) json_encode($payload)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = $b64($signature);

        return implode('.', $segments);
    }
}
