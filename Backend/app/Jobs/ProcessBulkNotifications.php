<?php

namespace App\Jobs;

use App\Jobs\SendBatchNotificationJob;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessBulkNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $reqData;
    protected $authUserId;
    protected $batchSize = 1000;

    // We pass the request data array, not the Request object itself
    public function __construct($reqData, $authUserId)
    {
        $this->reqData = $reqData;
        $this->authUserId = $authUserId;
    }

    public function handle()
    {
        $notification = Notification::find($this->reqData['notification_id']);
        if (!$notification)
            return;

        $image = null;
        // if ($notification->image) {
        //     // Fix: Check if image is already a URL (e.g. S3 link) before prepending APP_URL
        //     if (filter_var($notification->image, FILTER_VALIDATE_URL)) {
        //         $image = $notification->image;
        //     } else {
        //         // Use config('app.url') for relative paths in queue context
        //         $image = rtrim(config('app.url'), '/') . '/' . ltrim($notification->image, '/');
        //     }
        // }
        $notificationData = [
            'title' => $notification->title,
            'description' => $notification->description,
            'image' => $image,
        ];

        $userIds = $this->reqData['userIds'] ?? [];
        $role = $this->reqData['role'] ?? null;

        // CASE 1: Specific Users
        if (!empty($userIds) && $userIds !== ['all']) {
            $this->dispatchBatchJobs($notificationData, $userIds);
        }
        // CASE 2: Roles (User/Astrologer)
        elseif ($role && in_array($role, ['User', 'Astrologer'])) {
            $roleId = ($role == 'User') ? 3 : 2;

            // Collect user IDs and dispatch in batches
            DB::table('user_device_details')
                ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
                ->where('user_roles.roleId', '=', $roleId)
                ->where('user_device_details.isActive', 1)
                ->where('user_device_details.isDelete', 0)
                ->select('user_device_details.userId')
                ->orderBy('user_device_details.userId')  // Required for chunk()
                ->chunk($this->batchSize, function ($users) use ($notificationData) {
                    $userIds = $users->pluck('userId')->toArray();
                    SendBatchNotificationJob::dispatch($notificationData, $userIds, $this->authUserId);
                });
        }
        // CASE 3: Never Recharged
        elseif ($role == 'User Never Recharged') {
            DB::table('user_device_details')
                ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
                ->where('user_roles.roleId', '=', 3)
                ->whereNotExists(function ($query) {
                    $query
                        ->select(DB::raw(1))
                        ->from('user_wallets')
                        ->whereRaw('user_device_details.userId = user_wallets.userId');
                })
                ->select('user_device_details.userId')
                ->orderBy('user_device_details.userId')  // Required for chunk()
                ->chunk($this->batchSize, function ($users) use ($notificationData) {
                    $userIds = $users->pluck('userId')->toArray();
                    SendBatchNotificationJob::dispatch($notificationData, $userIds, $this->authUserId);
                });
        }
        // CASE 4: Not Used Free Chat/Call
        elseif ($role == 'User Not Used Free Chat/Call') {
            DB::table('user_device_details')
                ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
                ->where('user_roles.roleId', '=', 3)
                ->whereNotExists(function ($query) {
                    $query
                        ->select(DB::raw(1))
                        ->from('chatrequest')
                        ->whereRaw('user_device_details.userId = chatrequest.userId')
                        ->orWhereExists(function ($subquery) {
                            $subquery
                                ->select(DB::raw(1))
                                ->from('callrequest')
                                ->whereRaw('user_device_details.userId = callrequest.userId');
                        });
                })
                ->select('user_device_details.userId')
                ->orderBy('user_device_details.userId')  // Required for chunk()
                ->chunk($this->batchSize, function ($users) use ($notificationData) {
                    $userIds = $users->pluck('userId')->toArray();
                    SendBatchNotificationJob::dispatch($notificationData, $userIds, $this->authUserId);
                });
        }
        // CASE 5: All Users
        else {
            DB::table('user_device_details')
                ->select('userId')
                ->orderBy('userId')  // Required for chunk()
                ->chunk($this->batchSize, function ($users) use ($notificationData) {
                    $userIds = $users->pluck('userId')->toArray();
                    SendBatchNotificationJob::dispatch($notificationData, $userIds, $this->authUserId);
                });
        }
    }

    /**
     * Helper method to dispatch batch jobs for an array of user IDs
     */
    protected function dispatchBatchJobs($notificationData, array $userIds)
    {
        // Split into batches of 2000
        $batches = array_chunk($userIds, $this->batchSize);

        foreach ($batches as $batch) {
            SendBatchNotificationJob::dispatch($notificationData, $batch, $this->authUserId);
        }
    }
}
