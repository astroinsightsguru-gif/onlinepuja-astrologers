<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SocialScheduleCommand extends Command
{
    protected $signature = 'social:schedule {--days=60 : Days ahead to scan and queue}';

    protected $description = 'Automatically scan upcoming Hindu festivals and queue approved social media posts';

    public function handle(): int
    {
        $this->info('Scanning upcoming festivals for automated social campaigns...');

        // Ensure festivals table is seeded
        Artisan::call('brain:seed-festivals');

        $days = max(15, (int) $this->option('days'));
        $endDate = now()->addDays($days)->toDateString();

        $festivals = DB::table('hindu_festivals')
            ->where('scheduled', false)
            ->whereBetween('date', [now()->toDateString(), $endDate])
            ->orderBy('date', 'ASC')
            ->get();

        if ($festivals->isEmpty()) {
            $this->info('All upcoming festivals within the next ' . $days . ' days are already scheduled.');
            return self::SUCCESS;
        }

        $queued = 0;
        $baseUrl = rtrim(config('app.url', 'https://onlinepuja.live'), '/');

        foreach ($festivals as $fest) {
            $cleanName = preg_replace('/\s*\(.*?\)/', '', $fest->name);
            $deity = $fest->deity ?: 'Lord Ganesha';
            $hashTag = str_replace([' ', '-', '(', ')', '&'], '', $cleanName);

            $text = "🔱 May the divine blessings of {$deity} bring peace, prosperity, and spiritual fulfillment on the auspicious occasion of {$fest->name}! 🙏✨\n\n" .
                    "🪔 Perform authentic Vedic pujas with personal Sankalp from Varanasi Kashi Vishwanath with sanctum video proof & consecrated prasad delivery at home.\n\n" .
                    "👉 Book Puja Online: {$baseUrl}/puja/category/list\n" .
                    "📞 Book Vedic Pandit: {$baseUrl}/talk-to-partner";

            $hashtags = "#{$hashTag} #OnlinePuja #PujaBooking #OnlinePanditBooking #Varanasi #Kashi #VedicPuja #PoojaAtHome";

            $channels = json_decode((string) $fest->channels, true) ?: ['instagram', 'facebook', 'whatsapp', 'x'];

            DB::table('content_plans')->insert([
                'title'      => 'Wish: ' . $fest->name . ' (' . $fest->date . ')',
                'pillar'     => 'festival',
                'channel'    => 'social',
                'status'     => 'approved',
                'payload'    => json_encode([
                    'festival' => $fest->name,
                    'deity'    => $deity,
                    'text'     => $text,
                    'hashtags' => $hashtags,
                    'channels' => $channels,
                    'link'     => $baseUrl . '/puja/category/list',
                ]),
                'due_at'     => $fest->date . ' 06:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('hindu_festivals')->where('id', $fest->id)->update(['scheduled' => true]);
            $queued++;

            $this->line("✓ Queued social post for {$fest->name} on {$fest->date}");
        }

        $this->info("✓ Successfully queued {$queued} upcoming festival social posts.");
        return self::SUCCESS;
    }
}
