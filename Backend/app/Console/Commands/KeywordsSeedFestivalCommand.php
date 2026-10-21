<?php

namespace App\Console\Commands;

use App\Brain\RunLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the keyword table with queries devotees type leading up to festivals
 * in both English and Hindi.
 */
class KeywordsSeedFestivalCommand extends Command
{
    protected $signature = 'keywords:seed-festival
        {--horizon=75 : Only seed festivals falling within this many days}
        {--languages=en,hi : Comma-separated languages to seed}';

    protected $description = 'Seed festival-intent keywords (English + Hindi) for upcoming festivals';

    protected const HINDI_NAMES = [
        'Janmashtami' => 'जन्माष्टमी',
        'Ganesh Chaturthi' => 'गणेश चतुर्थी',
        'Pitru Paksha Begins' => 'पितृ पक्ष',
        'Navratri Begins' => 'नवरात्रि',
        'Durga Ashtami' => 'दुर्गा अष्टमी',
        'Dussehra (Vijayadashami)' => 'दशहरा',
        'Karwa Chauth' => 'करवा चौथ',
        'Dhanteras' => 'धनतेरस',
        'Diwali' => 'दिवाली',
        'Govardhan Puja' => 'गोवर्धन पूजा',
        'Bhai Dooj' => 'भाई दूज',
        'Chhath Puja' => 'छठ पूजा',
        'Vaikuntha Chaturdashi' => 'बैकुंठ चतुर्दशी',
        'Dev Deepawali Varanasi' => 'देव दीपावली वाराणसी',
        'Gita Jayanti' => 'गीता जयंती',
        'Tulsi Vivah Ends / Purnima' => 'तुलसी विवाह',
        'Makar Sankranti / Pongal' => 'मकर संक्रांति',
        'Vasant Panchami' => 'वसंत पंचमी',
        'Maha Shivratri' => 'महाशिवरात्रि',
        'Holika Dahan' => 'होलिका दहन',
        'Holi' => 'होली',
        'Ram Navami' => 'राम नवमी',
    ];

    protected const TEMPLATES = [
        'en' => [
            ['%s 2026 date and shubh muhurat', 'informational', 30],
            ['%s puja vidhi at home step by step', 'informational', 28],
            ['%s puja samagri list', 'informational', 26],
            ['book pandit for %s puja online', 'commercial', 42],
            ['%s puja online booking', 'commercial', 45],
            ['%s varanasi ghats deepdaan booking', 'commercial', 48],
            ['%s kashi vishwanath special sankalp puja', 'commercial', 50],
        ],
        'hi' => [
            ['%s 2026 शुभ मुहूर्त', 'informational', 24],
            ['%s पूजा विधि', 'informational', 22],
            ['%s पूजा सामग्री लिस्ट', 'informational', 20],
            ['%s पूजा के लिए पंडित बुकिंग', 'commercial', 34],
            ['%s व्रत कथा', 'informational', 26],
            ['%s वाराणसी घाट दीपदान ऑनलाइन संकल्प', 'commercial', 40],
            ['%s काशी विश्वनाथ विशेष पूजा बुकिंग', 'commercial', 44],
        ],
    ];

    public function handle(): int
    {
        $languages = array_values(array_filter(array_map('trim', explode(',', (string) $this->option('languages')))));
        $horizon = max(1, (int) $this->option('horizon'));

        $festivals = DB::table('hindu_festivals')
            ->whereBetween('date', [now()->toDateString(), now()->addDays($horizon)->toDateString()])
            ->orderBy('date')
            ->get();

        if ($festivals->isEmpty()) {
            $this->warn("No festivals in the next {$horizon} days — nothing to seed.");

            return self::SUCCESS;
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($festivals as $festival) {
            $cluster = \Illuminate\Support\Str::slug($festival->name) ?: 'festival';

            foreach ($languages as $language) {
                $templates = self::TEMPLATES[$language] ?? null;

                if (! $templates) {
                    $this->warn("No templates for language '{$language}' — skipped.");

                    continue;
                }

                $name = $language === 'hi'
                    ? (self::HINDI_NAMES[$festival->name] ?? $festival->name)
                    : $this->plainName($festival->name);

                foreach ($templates as [$template, $intent, $difficulty]) {
                    $phrase = trim(sprintf($template, $name));

                    if (mb_strlen($phrase) > 190) {
                        $skipped++;

                        continue;
                    }

                    $exists = DB::table('keywords')->where('phrase', $phrase)->exists();

                    if ($exists) {
                        $skipped++;

                        continue;
                    }

                    DB::table('keywords')->insert([
                        'phrase' => $phrase,
                        'language' => $language,
                        'source' => 'festival',
                        'est_volume' => null,
                        'difficulty' => $difficulty,
                        'intent' => $intent,
                        'cluster' => $cluster,
                        'status' => 'new',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $inserted++;
                }
            }

            $this->line("• {$festival->date} {$festival->name}");
        }

        $this->info("Seeded {$inserted} new keyword(s) across ".count($languages)." language(s); {$skipped} already present.");

        RunLog::recordUnlessNoop(
            'keywords:seed-festival', 'seo', 'ok',
            "Seeded {$inserted} festival keyword(s)",
            ['inserted' => $inserted, 'skipped' => $skipped, 'festivals' => $festivals->count(), 'languages' => $languages],
            $inserted, 0
        );

        return self::SUCCESS;
    }

    protected function plainName(string $name): string
    {
        $name = preg_replace('/\s*\(.*?\)\s*/', ' ', $name);
        $name = preg_replace('/\s+(Begins|Ends)$/i', '', trim($name));

        return trim($name);
    }
}
