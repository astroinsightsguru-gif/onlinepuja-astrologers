<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserReminderController extends Controller
{
    public function sendNotificationMyUsers()
    {
        try {
            // Current time + 10 minutes
            $targetTime = Carbon::now()->addMinutes(10)->format('H:i:s');
            $todayDate = Carbon::now()->format('Y-m-d');

            // Get all schedules which are exactly 10 min ahead
            $schedules = DB::table('liveastro')
                ->join('astrologers', 'astrologers.id', '=', 'liveastro.astrologerId')
                ->where('liveastro.schedule_live_date', $todayDate)
                ->where('liveastro.schedule_live_time', $targetTime)
                ->select(
                    'liveastro.*',
                    'astrologers.name as astrologerName'
                )
                ->get();

            $totalNotified = 0;

            foreach ($schedules as $schedule) {
                // Get all users who set reminder for this astrologer
                $reminderUsers = DB::table('user_reminders')
                    ->join('user_device_details', 'user_device_details.userId', '=', 'user_reminders.userId')
                    ->where('user_reminders.astrologerId', $schedule->astrologerId)
                    ->select('user_reminders.userId', 'user_device_details.subscription_id', 'user_device_details.subscription_id_web')
                    ->get();

                if ($reminderUsers->isEmpty()) {
                    continue;
                }

                // Collect all player IDs (for OneSignal)
                $userPlayerIds = $reminderUsers
                    ->pluck('subscription_id')
                    ->merge($reminderUsers->pluck('subscription_id_web'))
                    ->filter()
                    ->values()
                    ->toArray();

                if (!empty($userPlayerIds)) {
                    $notificationData = [
                        'title' => "Reminder: {$schedule->astrologerName} is going live soon",
                        'body' => [
                            'description' => "Your astrologer {$schedule->astrologerName} will be live at {$schedule->schedule_live_time}.",
                            'notificationType' => 40
                        ],
                    ];

                    $oneSignalService = new OneSignalService();
                    $oneSignalService->sendNotification($userPlayerIds, $notificationData);

                    // Save into user_notifications table
                    foreach ($reminderUsers as $user) {
                        DB::table('user_notifications')->insert([
                            'userId' => $user->userId,
                            'title' => $notificationData['title'],
                            'description' => $notificationData['body']['description'],
                            'createdBy' => 0,
                            'modifiedBy' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $totalNotified += count($reminderUsers);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Reminder notifications sent successfully!',
                'total_users_notified' => $totalNotified,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while sending reminders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
