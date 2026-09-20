<?php

namespace App\Console;

use App\Http\Controllers\Admin\HoroscopeController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // COMMAND: Puja Reminders (Every Minute)
        $schedule
            ->command('puja:send-reminders')
            ->everyMinute()
            ->name('puja_send_reminders')
            ->withoutOverlapping();

        // COMMAND: Update Payment Status (Every Minute)
        $schedule
            ->command('payment:status-update')
            ->everyMinute()
            ->name('payment_status_update')
            ->withoutOverlapping();

        // 🧠 BRAIN: Live AI model sync (twice daily)
        $schedule->command('ai:sync-models')
            ->twiceDaily(2, 14)
            ->name('ai_model_sync')
            ->withoutOverlapping();

        // 🧠 BRAIN: Daily traffic snapshot into goals
        $schedule->command('goal:snapshot')
            ->dailyAt('23:40')
            ->name('goal_snapshot')
            ->withoutOverlapping();

        // 🧠 SELF-RESEARCHING: keyword tri-source sweep (weekly)
        $schedule->command('seo:research')
            ->weeklyOn(0, '02:00')
            ->name('seo_research')
            ->withoutOverlapping();

        // 🧠 SELF-GROWING: AI content drafts (daily)
        $schedule->command('content:generate --limit=2')
            ->dailyAt('05:00')
            ->name('content_generate')
            ->withoutOverlapping();

        // 🧠 SELF-HEALING: nightly technical scan
        $schedule->command('seo:scan')
            ->dailyAt('03:10')
            ->name('seo_scan')
            ->withoutOverlapping();

        // 🧠 SELF-HEALING: God Mode auto-fixes (weekly, capped, revertable)
        $schedule->command('seo:fix --god --limit=10')
            ->weeklyOn(0, '03:40')
            ->name('seo_god_fix')
            ->withoutOverlapping();

        // 🗓️ CALL: Daily Horoscope Generation (12:10 AM)
        $schedule
            ->call(function () {
                app(HoroscopeController::class)->generateDailyHorscope();
            })
            ->dailyAt('00:10')
            ->name('horoscope_generate_daily')
            ->withoutOverlapping();

        // CALL: Weekly Horoscope Generation (Monday, 12:30 AM)
        // NOTE: This task and the one below are identical in timing. Ensure this is intentional.
        $schedule
            ->call(function () {
                app(HoroscopeController::class)->generateWeeklyHorscope();
            })
            ->weeklyOn(1, '00:30')
            ->name('horoscope_generate_weekly')
            ->withoutOverlapping();

        // 📅 CALL: Yearly Horoscope Generation (Monday, 12:30 AM)
        // NOTE: This runs at the same time as weekly horoscope generation.
        $schedule
            ->call(function () {
                app(HoroscopeController::class)->generateYearlyHorscope();
            })
            ->weeklyOn(1, '00:30')
            ->name('horoscope_generate_yearly')
            ->withoutOverlapping();

        // COMMAND: Scheduled Notifications (Every Minute)
        $schedule
            ->command('notifications:send-scheduled')
            ->everyMinute()
            ->name('notifications_send_scheduled')
            ->withoutOverlapping();

        // COMMAND: Live Schedule Reminders (Every Minute)
        $schedule
            ->command('live-schedule:reminder')
            ->everyMinute()
            ->name('live_schedule_reminder')
            ->withoutOverlapping();

        // COMMAND: Call/Chat Deletion (Every Five Minutes)
        $schedule
            ->command('call-chat:delete')
            ->everyFiveMinutes()
            ->name('call_chat_delete')
            ->withoutOverlapping();

        // COMMAND: Reset Astro Free/Paid Status (Daily)
        $schedule
            ->command('reset:astro-free-paid')
            ->daily()
            ->name('reset_astro_free_paid')
            ->withoutOverlapping();

        $schedule
            ->command('payment:status-update')
            ->everyMinute()
            ->name('payment_status_update')
            ->withoutOverlapping();

         // COMMAND: Refund Expired Scheduled Call Appointments (Daily)
        $schedule->command('appointments:refund-expired')
            ->daily()
            ->name('refund_expired_appointments')
            ->withoutOverlapping();

        // CALL: Internal Webhook Trigger (Every Minute)
        $schedule
            ->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
