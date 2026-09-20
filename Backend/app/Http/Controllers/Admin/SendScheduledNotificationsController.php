<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendScheduledNotificationsController extends Controller
{
    public function sendUserNotifications()
    {
        try {
            $now = Carbon::now();

            // Get due scheduled notifications
            $scheduledNotifications = DB::table('user_notifications_scheduler')
                ->where('status', 0)
                ->where('isActive', 1)
                ->where('isDelete', 0)
                ->where('auto_send_time', '<=', $now)
                ->get();

            $sentCount = 0;

            foreach ($scheduledNotifications as $sched) {
                // Dispatch notification job
                SendNotificationJob::dispatch(
                    (object) [
                        'title' => $sched->title,
                        'description' => $sched->description,
                        'id' => $sched->notificationId,
                        'type' => $sched->notification_type
                    ],
                    $sched->userId,
                    $sched->createdBy
                );

                // Save to user_notifications
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

                // Mark as processed
                DB::table('user_notifications_scheduler')
                    ->where('id', $sched->id)
                    ->update([
                        'status' => 1,
                        'updated_at' => now()
                    ]);

                $sentCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Processed $sentCount scheduled notifications",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function resetAstroFreePaid()
    {
        try {
            // Update all astrologers and reset AstroFreePaid to 0
            DB::table('astrologers')->update([
                'AstroFreePaid' => 0,
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'message' => "All astrologers' AstroFreePaid counts have been reset to 0 successfully.",
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
