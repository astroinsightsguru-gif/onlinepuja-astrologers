<?php

namespace App\Jobs;

use App\Models\Notification;
use App\services\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    protected $userId;
    protected $authUserId;

    public function __construct($notification, $userId, $authUserId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
        $this->authUserId = $authUserId;
    }

    public function handle()
    {
        $userDeviceDetail = DB::table('user_device_details')
            ->where('userId', '=', $this->userId)
            ->select('subscription_id', 'subscription_id_web')
            ->first();

        if (empty($userDeviceDetail)) {
            \Log::error("No device details found for user: {$this->userId}");
            return;
        }

        $oneSignalService = new OneSignalService();
        $userDeviceDetails = array_values((array) $userDeviceDetail);

        $notificationData = [
            'title' => $this->notification->title,
            'body' => ['description' => $this->notification->description, 'notificationType' => 15],
        ];

        try {
            $oneSignalService->sendNotification($userDeviceDetails, $notificationData);

            DB::table('user_notifications')->insert([
                'userId' => $this->userId,
                'title' => $this->notification->title,
                'description' => $this->notification->description,
                'createdBy' => $this->authUserId,
                'modifiedBy' => $this->authUserId,
            ]);
        } catch (\Exception $e) {
            \Log::error('OneSignal error: ' . $e->getMessage());
        }
    }
}
