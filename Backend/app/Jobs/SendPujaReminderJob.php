<?php

// app/Jobs/SendPujaReminderJob.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PujaOrder;
use App\services\OneSignalService;
use Illuminate\Support\Facades\DB;

class SendPujaReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $puja;

    public function __construct(PujaOrder $puja)
    {
        $this->puja = $puja;
    }

    public function handle()
    {
        // Notification for user
         $users=DB::table('users')->where('id',$this->puja->user_id)->first();
          // Notification for astrologer
        $astrologers=DB::table('astrologers')->where('id',$this->puja->astrologer_id)->first();
        $this->sendNotification(
            $this->puja->user_id,
            "Your puja is starting soon!",
            "Your puja is scheduled to begin with " . (!empty($astrologers->name) ? $astrologers->name : 'paertner') . " at " . $this->puja->puja_start_datetime
        );
        

       
        $this->sendNotification(
            $astrologers->userId,
            "Puja reminder for your upcoming session",
            "You have a puja scheduled with  ".(!empty($users->name) ? $users->name : 'paertner')." at " . $this->puja->puja_start_datetime
        );
    }

    protected function sendNotification($userId, $title, $message)
    {
        $userDeviceDetail = DB::table('user_device_details')
            ->where('userId', '=', $userId)
            ->select('subscription_id', 'subscription_id_web')
            ->first();

        if (!empty($userDeviceDetail)) {
            $oneSignalService = new OneSignalService();
            $userDeviceDetails = array_values((array)$userDeviceDetail);
            
            $notificationData = [
                'title' => $title,
                'body' => ['description' => $message, "notificationType" => 30],
            ];

            $oneSignalService->sendNotification($userDeviceDetails, $notificationData);

            DB::table('user_notifications')->insert([
                'userId' => $userId,
                'title' => $title,
                'description' => $message,
                'createdBy' => 0, // System-generated
                'modifiedBy' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}