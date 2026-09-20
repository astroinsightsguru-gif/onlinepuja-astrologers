<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class HundredMsService
{
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

        $this->baseUrl = systemflag('hmsApiUrl');
    }


    /**
     * Create a new room
     */
    public function createHmsRoom($role, $name, $timeduration)
    {
        try {
            $hmsApiUrl = systemflag('hmsApiUrl');
            $managementToken = hmsGenerateManagementToken();
            $hmsTemplateId = systemflag('hmsTemplateId');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $managementToken,
                'Content-Type' => 'application/json'
            ])->post($hmsApiUrl . '/rooms', [
                'name' => $name,
                'description' => $role,
                'template_id' => $hmsTemplateId,
                'region' => 'in',
                'max_duration_seconds' => (int) $timeduration,
                'recording_info' => [
                    'enabled' => true
                ]
            ]);

            if ($response->successful()) {
                $roomData = $response->json();

                $roomId = $roomData['id'];
                // Generate auth token for the user
                $hmsSessionToken = $this->generateHmsRoomCode($roomId, $role);

                // Return both room data and session token
                return $hmsSessionToken;
            } else {
                // FIXED: Don't return response()->json() from a private function
                // Instead return error data
                $errorData = $response->json();
                return [
                    'success' => false,
                    'message' => 'Failed to create room',
                    'error' => $errorData,
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Room creation failed: ' . $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile())
            ];
        }
    }

     public function generateHmsRoomCode($roomId, $role)
    {
        try {
            $hmsApiUrl = systemflag('hmsApiUrl');
            $managementToken = hmsGenerateManagementToken();

            // Try the room codes endpoint (current 100ms approach)
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $managementToken,
                'Content-Type' => 'application/json'
            ])->post($hmsApiUrl . '/room-codes/room/' . $roomId . '/role/'. $role);

            if ($response->successful()) {
                $codeData = $response->json();
                $responseToken = $this->hmsGenerateAuthToken($codeData['data'][0]['code'] ?? $codeData['code']);
                if ($responseToken) {
                    return [
                        'success' => true,
                        'auth_token' => $responseToken['auth_token'],
                        'room_code' => $codeData['data'][0]['code'] ?? $codeData['code'],
                        'room_id' => $roomId,
                        'role' => $codeData['data'][0]['role'] ?? $codeData['role']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to generate auth token',
                    ];
                }
            } else {
                $errorResponse = $response->json();
                return [
                    'success' => false,
                    'message' => 'Failed to generate room code',
                    'error' => $errorResponse,
                    'status_code' => $response->status(),
                    'fallback_attempted' => ''
                ];
            }
        } catch (\Exception $e) {
            \Log::error('HMS Room Code Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return [
                'success' => false,
                'message' => 'Room code generation failed: ' . $e->getMessage(),
                'exception_line' => $e->getLine()
            ];
        }
    }

     private function hmsGenerateAuthToken($roomeCode)
    {
        try {
            $hmsApiUrl = systemflag('hmsApiUrl');
            $managementToken = hmsGenerateManagementToken();
            $requestData = [
                'code' => $roomeCode,
            ];

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $managementToken,
                'Content-Type' => 'application/json'
            ])->post($hmsApiUrl . '/token', $requestData);


            if ($response->successful()) {
                $tokenData = $response->json();

                // Check if token exists in response
                if (!isset($tokenData['token'])) {
                    return [
                        'success' => false,
                        'message' => 'Auth token not found in response',
                        'response_data' => $tokenData
                    ];
                }

                return [
                    'success' => true,
                    'auth_token' => $tokenData['token'],
                ];
            } else {
                $errorResponse = $response->json();
                return [
                    'success' => false,
                    'message' => 'Failed to generate auth token',
                    'error' => $errorResponse,
                    'status_code' => $response->status(),
                    'request_data' => $requestData
                ];
            }
        } catch (\Exception $e) {
            \Log::error('HMS Auth Token Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return [
                'success' => false,
                'message' => 'Auth token generation failed: ' . $e->getMessage(),
                'exception_line' => $e->getLine()
            ];
        }
    }

    /**
     * Update room details
     */
    public function updateRoom(string $roomId, array $updateData): array
    {
        try {
            $token = hmsGenerateManagementToken();

            $response = $this->client->post($this->baseUrl . "/rooms/{$roomId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $updateData
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Update Room Error', $e);
            throw new \Exception('Failed to update room: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * End/disable a room
     */
    public function endRoom(string $roomId, string $reason = 'Room ended by host', bool $lock = true): array
    {
        try {
            $managementToken = hmsGenerateManagementToken();

            $response = $this->client->post($this->baseUrl . "/active-rooms/{$roomId}/end-room", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $managementToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'reason' => $reason,
                    'lock' => $lock,
                ]
            ]);

            Log::info('100ms Room Ended', [
                'room_id' => $roomId,
                'reason' => $reason
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('End Room Error', $e);
            throw new \Exception('Failed to end room: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Remove peers from room
     */
    public function removePeers(string $sessionId, array $peerIds, string $reason = 'Removed by admin'): array
    {
        try {
           $managementToken = hmsGenerateManagementToken();

            $response = $this->client->post($this->baseUrl . "/active-rooms/{$sessionId}/remove-peers", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $managementToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'peer_ids' => $peerIds,
                    'reason' => $reason,
                ]
            ]);

            Log::info('100ms Peers Removed', [
                'session_id' => $sessionId,
                'peer_ids' => $peerIds,
                'reason' => $reason
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Remove Peers Error', $e);
            throw new \Exception('Failed to remove peers: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Mute/Unmute peers
     */
    public function updatePeerAudio(string $sessionId, array $peerIds, bool $mute = true): array
    {
        try {
             $managementToken = hmsGenerateManagementToken();

            $response = $this->client->post($this->baseUrl . "/active-rooms/{$sessionId}/mute-peers", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $managementToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'peer_ids' => $peerIds,
                    'enabled' => !$mute,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Update Peer Audio Error', $e);
            throw new \Exception('Failed to update peer audio: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Send message to room
     */
    public function sendMessage(string $sessionId, string $message, string $type = 'chat'): array
    {
        try {
            $token = hmsGenerateManagementToken();

            $response = $this->client->post($this->baseUrl . "/active-rooms/{$sessionId}/send-message", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => $message,
                    'type' => $type,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Send Message Error', $e);
            throw new \Exception('Failed to send message: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Get recording details
     */
    public function getRecordings(string $roomId, int $limit = 10, int $start = 0): array
    {
        try {
            $token = hmsGenerateManagementToken();

            $response = $this->client->get($this->baseUrl . "/recordings", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => [
                    'room_id' => $roomId,
                    'limit' => $limit,
                    'start' => $start,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Get Recordings Error', $e);
            throw new \Exception('Failed to get recordings: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Start live stream
     */
    public function startLiveStream(string $roomId, array $rtmpUrls, array $config = []): array
    {
        try {
            $token = hmsGenerateManagementToken();

            $requestData = array_merge([
                'rtmp_urls' => $rtmpUrls,
            ], $config);

            $response = $this->client->post($this->baseUrl . "/live-streams/room/{$roomId}/start", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData
            ]);

            Log::info('100ms Live Stream Started', ['room_id' => $roomId]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Start Live Stream Error', $e);
            throw new \Exception('Failed to start live stream: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Stop live stream
     */
    public function stopLiveStream(string $roomId): array
    {
        try {
            $token = hmsGenerateManagementToken();

            $response = $this->client->post($this->baseUrl . "/live-streams/room/{$roomId}/stop", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]);

            Log::info('100ms Live Stream Stopped', ['room_id' => $roomId]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Stop Live Stream Error', $e);
            throw new \Exception('Failed to stop live stream: ' . $this->getErrorMessage($e));
        }
    }


    /**
     * Create webhook endpoint
     */
    public function createWebhook(string $url, array $events = []): array
    {
        try {
            $token = hmsGenerateManagementToken();

            $defaultEvents = [
                'room.started',
                'room.finished',
                'peer.join.success',
                'peer.leave.success'
            ];

            $response = $this->client->post($this->baseUrl . "/webhooks", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'url' => $url,
                    'events' => !empty($events) ? $events : $defaultEvents,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->logError('Create Webhook Error', $e);
            throw new \Exception('Failed to create webhook: ' . $this->getErrorMessage($e));
        }
    }

    /**
     * Helper method to log errors
     */
    private function logError(string $context, RequestException $e): void
    {
        $errorData = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'url' => $e->getRequest()->getUri()->__toString(),
            'method' => $e->getRequest()->getMethod(),
        ];

        if ($e->hasResponse()) {
            $errorData['response_status'] = $e->getResponse()->getStatusCode();
            $errorData['response_body'] = $e->getResponse()->getBody()->getContents();
        }

        Log::error("100ms API Error - {$context}", $errorData);
    }

    /**
     * Get user-friendly error message from exception
     */
    private function getErrorMessage(RequestException $e): string
    {
        if ($e->hasResponse()) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            return $response['message'] ?? $response['error'] ?? 'Unknown API error';
        }

        return $e->getMessage();
    }

}
