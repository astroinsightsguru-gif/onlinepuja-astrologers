<?php

namespace App\Jobs;

use App\Models\Notification;
use App\services\FCMService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendBatchNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;  // 5 min max (parallel sending is fast, but large batches need headroom)

    protected $notificationData;
    protected $userIds;
    protected $authUserId;
    protected $notificationType;
    protected $extraData;

    /**
     * Create a new job instance.
     *
     * @param array $notificationData - Array with 'title' and 'description'
     * @param array $userIds - Array of user IDs
     * @param int $authUserId
     * @param int $notificationType - Notification type ID (default 15)
     * @param array $extraData - Extra data like audio URL, image URL, etc.
     */
    public function __construct($notificationData, array $userIds, $authUserId, $notificationType = 15, $extraData = [])
    {
        $this->notificationData = $notificationData;
        $this->userIds = $userIds;
        $this->authUserId = $authUserId;
        $this->notificationType = $notificationType;
        $this->extraData = $extraData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (empty($this->userIds)) {
            Log::warning('SendBatchNotificationJob called with empty user IDs array');
            return;
        }

        // 1. Fetch all FCM tokens for these users in ONE query
        $devices = DB::table('user_device_details')
            ->whereIn('userId', $this->userIds)
            ->whereNotNull('fcmToken')
            ->where('fcmToken', '!=', '')
            ->select('userId', 'fcmToken')
            ->get();

        if ($devices->isEmpty()) {
            Log::warning('No FCM tokens found for batch of ' . count($this->userIds) . ' users');
            // Still insert notification records even if no FCM tokens (user can see in-app)
            $this->insertNotificationRecords();
            return;
        }

        // 2. Prepare notification data with dynamic type and extra data
        $bodyData = [
            'description' => $this->notificationData['description'] ?? '',
            'notificationType' => $this->notificationType,
            // 'image' => $this->notificationData['image'] ?? null,
        ];

        // Merge extra data (audio_url, image_url, etc.)
        if (!empty($this->extraData)) {
            $bodyData = array_merge($bodyData, $this->extraData);
        }

        $notificationPayload = [
            'title' => $this->notificationData['title'] ?? '',
            'body' => $bodyData,
        ];

        try {
            // 3. Use FCMService::send() — now uses curl_multi for parallel sending
            $responses = FCMService::send($devices, $notificationPayload);

            // Fetch the exact tokens that were SENT to FCMService (filtered and indexed)
            $sentTokens = $devices->pluck('fcmToken')->filter(fn($t) => !empty($t))->values()->all();
            $invalidTokens = [];

            // Map responses back to tokens
            if (!empty($responses)) {
                foreach ($responses as $index => $response) {
                    // Check if FCM returned an error for this specific token
                    if (isset($response['error'])) {
                        // FCM v1 error code is in details[0]['errorCode'] or sometimes inferred from status
                        $errorCode = $response['error']['details'][0]['errorCode'] ?? null;
                        $status = $response['error']['status'] ?? '';

                        // If token is UNREGISTERED, mark it for removal
                        if (($errorCode === 'UNREGISTERED' || $status === 'NOT_FOUND') && isset($sentTokens[$index])) {
                            $invalidTokens[] = $sentTokens[$index];
                        }

                        // Optional: Log other specific errors for debugging
                        if ($errorCode && $errorCode !== 'UNREGISTERED') {
                            Log::warning("FCM delivery error for token index {$index}: {$errorCode} - {$status}");
                        }
                    }
                }
            }

            // Remove invalid/unregistered tokens from database to keep it clean
            if (!empty($invalidTokens)) {
                // DB::table('user_device_details')
                //     ->whereIn('fcmToken', $invalidTokens)
                //     ->update(['fcmToken' => null]);

                Log::info('Cleared ' . count(array_unique($invalidTokens)) . ' invalid FCM tokens (UNREGISTERED/NOT_FOUND)');
            }

            // 4. Insert notification records in bulk (chunked to avoid max_allowed_packet issues)
            $this->insertNotificationRecords();

            // Log::info('SendBatchNotificationJob completed', [
            //     'users' => count($this->userIds),
            //     // 'tokens_sent' => count($tokensList),
            //     'invalid_tokens' => count($invalidTokens),
            // ]);
        } catch (\Exception $e) {
            Log::error('FCM batch send error: ' . $e->getMessage(), [
                'user_count' => count($this->userIds),
                'token_count' => $devices->count()
            ]);
            throw $e;  // Re-throw so the job can retry
        }
    }

    /**
     * Insert notification records in bulk, chunked to avoid DB packet limits
     */
    private function insertNotificationRecords()
    {
        $now = Carbon::now();
        $records = [];

        foreach ($this->userIds as $userId) {
            $records[] = [
                'userId' => $userId,
                'title' => $this->notificationData['title'] ?? '',
                'description' => $this->notificationData['description'] ?? '',
                'createdBy' => $this->authUserId,
                'modifiedBy' => $this->authUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks of 500 to avoid MySQL max_allowed_packet errors
        if (!empty($records)) {
            foreach (array_chunk($records, 500) as $chunk) {
                DB::table('user_notifications')->insert($chunk);
            }
        }
    }
}
