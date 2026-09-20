<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PujaOrder;
use Carbon\Carbon;
use App\Jobs\SendPujaReminderJob;

class SendPujaReminders extends Command
{
    protected $signature = 'puja:send-reminders';
    protected $description = 'Send reminders 5 minutes before puja start time';

    public function handle()
    {
        // Get pujas starting in exactly 5 minutes
        $reminderTime = Carbon::now()->addMinutes(15);

        $upcomingPujas = PujaOrder::where('puja_start_datetime', '>=', now())
            ->where('puja_start_datetime', '<=', $reminderTime)
            ->where('reminder_sent', false)
            ->get();

        foreach ($upcomingPujas as $puja) {
            // Dispatch job to send notifications
            SendPujaReminderJob::dispatch($puja);

            // Mark as reminder sent
            $puja->update(['reminder_sent' => true]);
        }

        $this->info("Sent reminders for {$upcomingPujas->count()} pujas.");
    }
}
