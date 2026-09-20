<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefundExpiredAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:refund-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all expired scheduled appointments that are still pending and automatically refund the users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        
        // Fetch all active scheduled call requests and their associated appointment records
        $appointments = DB::table('callrequest')
            ->join('call_request_apoinments', 'callrequest.id', '=', 'call_request_apoinments.callId')
            ->where('callrequest.IsSchedule', 1)
            ->where('callrequest.callStatus', 'Pending')
            ->where('call_request_apoinments.status', 'Pending')
            ->select('callrequest.*', 'call_request_apoinments.amount', 'call_request_apoinments.id as appointment_record_id')
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            $scheduleDateTime = Carbon::parse($appointment->schedule_date . ' ' . $appointment->schedule_time);
            $duration = ($appointment->call_duration / 60) ?? 15;
            $endDateTime = $scheduleDateTime->copy()->addMinutes($duration);

            if ($now->greaterThan($endDateTime)) {
                DB::transaction(function () use ($appointment, $now) {
                    // 1. Update callrequest status to Rejected
                    DB::table('callrequest')->where('id', $appointment->id)->update([
                        'callStatus' => 'Rejected',
                        'updated_at' => $now
                    ]);

                    // 2. Update call_request_apoinments status to Refunded
                    DB::table('call_request_apoinments')->where('id', $appointment->appointment_record_id)->update([
                        'status' => 'Refunded',
                        'updated_at' => $now
                    ]);

                    // 3. Add refunded amount to user's wallet
                    $wallet = DB::table('user_wallets')->where('userId', $appointment->userId)->first();
                    if ($wallet) {
                        DB::table('user_wallets')->where('userId', $appointment->userId)->update([
                            'amount' => (float)$wallet->amount + (float)$appointment->amount,
                            'updated_at' => $now,
                            'modifiedBy' => $appointment->userId,
                        ]);
                    } else {
                        DB::table('user_wallets')->insert([
                            'userId' => $appointment->userId,
                            'amount' => $appointment->amount,
                            'isActive' => 1,
                            'isDelete' => 0,
                            'createdBy' => $appointment->userId,
                            'modifiedBy' => $appointment->userId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    // 4. Insert refund record in wallettransaction table
                    $inr_usd_conv_rate = DB::table('systemflag')->where('name', 'UsdtoInr')->select('value')->first();
                    DB::table('wallettransaction')->insert([
                        'userId' => $appointment->userId,
                        'amount' => $appointment->amount,
                        'isCredit' => true,
                        'transactionType' => 'Refund',
                        'callId' => $appointment->id,
                        'astrologerId' => $appointment->astrologerId,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'inr_usd_conversion_rate' => $inr_usd_conv_rate ? $inr_usd_conv_rate->value : 1,
                    ]);

                    // 5. Send notification to user about the refund
                    $astrologer = DB::table('astrologers')->where('id', $appointment->astrologerId)->first();
                    $astrologerName = $astrologer ? $astrologer->name : 'Astrologer';
                    DB::table('user_notifications')->insert([
                        'userId' => $appointment->userId,
                        'title' => 'Appointment Refunded',
                        'description' => 'Your expired appointment with ' . $astrologerName . ' has been refunded to your wallet.',
                        'notificationId' => null,
                        'createdBy' => $appointment->userId,
                        'modifiedBy' => $appointment->userId,
                        'notification_type' => 1,
                        'callRequestId' => $appointment->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });

                $count++;
            }
        }

        $this->info("Successfully processed and refunded {$count} expired scheduled call appointments.");
        return 0;
    }
}
