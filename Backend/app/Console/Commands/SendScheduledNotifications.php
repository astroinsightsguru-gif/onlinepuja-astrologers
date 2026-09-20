<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendNotificationJob;
use Carbon\Carbon;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notifications when auto_send_time is reached';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Get all scheduled notifications where auto_send_time <= now and status == 0
        $scheduledNotifications = DB::table('user_notifications_scheduler')
            ->where('status', 0)
            ->where('isActive', 1)
            ->where('isDelete', 0)
            ->where('auto_send_time', '<=', $now)
            ->get();

        foreach ($scheduledNotifications as $sched) {
            // Dispatch the notification
            SendNotificationJob::dispatch(
                (object)[
                    'title' => $sched->title,
                    'description' => $sched->description,
                    'id' => $sched->notificationId,
                    'type' => $sched->notification_type
                ],
                $sched->userId,
                $sched->createdBy
            );

            // Save into user_notifications table
            DB::table('user_notifications')->insert([
                'userId' => $sched->userId,
                'title' => $sched->title,
                'description' => $sched->description,
                'notificationId' => $sched->notificationId,
                'chatRequestId' => $sched->chatRequestId,
                'callRequestId' => $sched->callRequestId,
                'isActive' => 1,
                'isDelete' => 0,
                'notification_type' => $sched->notification_type,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
                'createdBy' => $sched->createdBy,
                'modifiedBy' => $sched->modifiedBy,
            ]);

            // Update status to 1
            DB::table('user_notifications_scheduler')
                ->where('id', $sched->id)
                ->update(['status' => 1, 'updated_at' => now()]);

            $this->info("Notification sent to userId: {$sched->userId}");
        }

        return 0;
    }
}
