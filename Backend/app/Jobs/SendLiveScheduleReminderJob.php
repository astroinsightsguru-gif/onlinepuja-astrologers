<?php

namespace App\Jobs;

use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\AstrologerFollowers;
use App\Models\AstrologerModel\LiveAstro;
use App\services\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendLiveScheduleReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $liveSchedule;

    public function __construct(LiveAstro $liveSchedule)
    {
        $this->liveSchedule = $liveSchedule;
    }

    /**
     * Execute the job.
     */
     public function handle()
    {
        $astroFollowersId = AstrologerFollowers::where('astrologerId',$this->liveSchedule->astrologerId)->pluck('userId');
        $astrologer = Astrologer::where('id',$this->liveSchedule->astrologerId)->first();
        foreach($astroFollowersId as $userId){
            $this->sendNotification(
                $userId,
                "Astrologer live within minute!",
                "Astrologer Live scheduled to begin with " . (!empty($astrologers->name) ? $astrologers->name : 'partner') . " at " . $this->liveSchedule->schedule_live_date
            );

        }

        $this->sendNotification(
            $astrologer->userId,
            "Live Scheduled for your upcoming session",
            "You have a live scheduled with  all users at " . $this->liveSchedule->schedule_live_date
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
                'body' => ['description' => $message, "notificationType" => 31],
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
