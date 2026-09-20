<?php

namespace App\Brain;

use App\AI\ProviderManager;
use Illuminate\Support\Facades\Http;

/**
 * Tri-source keyword research — no subscription needed:
 *   1. Google autocomplete (live demand)
 *   2. AI expansion from the Brain profile (for zero-history sites)
 *   3. GSC queries (wired later when OAuth client is added)
 *
 * Scoring favours striking-distance long-tails over vanity head terms.
 */
class KeywordResearch
{
    protected const SEEDS_DEFAULT = 'online puja,puja booking,book pandit online,kundli matching online,astrology consultation,havan online,e-puja';

    protected const COMMERCIAL = ['online', 'booking', 'book', 'hire', 'price', 'cost', 'service', 'best', 'top', 'expert'];

    public function __construct(protected ProviderManager $ai)
    {
    }

    public function seeds(): array
    {
        $custom = \App\Brain\Vault::get('brain_seed_keywords');

        return array_filter(array_map('trim', explode(',', $custom ?: self::SEEDS_DEFAULT)));
    }

    /** Run the full sweep. Returns per-source counts. */
    public function run(): array
    {
        $report = ['autocomplete' => 0, 'ai' => 0];
        $seen = [];

        foreach ($this->seeds() as $seed) {
            foreach ($this->autocomplete($seed) as $phrase) {
                $seen[$phrase] = ['source' => 'autocomplete', 'cluster' => $seed];
            }
        }

        // AI expansion pass (one call, many seeds).
        $aiList = $this->aiExpand();

        foreach ($aiList as $phrase) {
            if (! isset($seen[$phrase])) {
                $seen[$phrase] = ['source' => 'ai', 'cluster' => 'expansion'];
            }
        }

        foreach ($seen as $phrase => $meta) {
            $this->store($phrase, $meta['source'], $meta['cluster']);
            $report[$meta['source']]++;
        }

        return $report;
    }

    protected function autocomplete(string $seed): array
    {
        try {
            $res = Http::timeout(10)->get(
                'https://suggestqueries.google.com/complete/search',
                ['client' => 'firefox', 'hl' => 'en', 'q' => $seed]
            );
        } catch (\Throwable $e) {
            return [];
        }

        $out = [];

        foreach ((array) $res->json()[1] ?? [] as $s) {
            if (($p = trim((string) $s)) !== '' && stripos($p, 'astroway') === false) {
                $out[] = $p;
            }
        }

        return array_slice($out, 0, 8);
    }

    protected function aiExpand(): array
    {
        $seeds = implode(', ', $this->seeds());

        $res = $this->ai->chat(
            'You are an SEO keyword researcher for an Indian online puja booking platform. Reply with STRICT JSON only.',
            "Seed topics: {$seeds}\n".
            'Generate 18 realistic search phrases Indian users would type, mixing informational and booking-intent, '.
            '3-6 words each, lowercase, no duplicates, no quotes inside. '.
            'JSON shape: {"keywords":["phrase one","phrase two"]}',
            ['json' => true, 'temperature' => 0.9, 'max_tokens' => 600]
        );

        if (! $res['ok']) {
            return [];
        }

        $j = json_decode(preg_replace('/^```(json)?|```$/', '', trim($res['text'])), true);

        return array_slice(array_map('trim', (array) ($j['keywords'] ?? [])), 0, 18);
    }

    protected function store(string $phrase, string $source, string $cluster): void
    {
        if (strlen($phrase) < 4 || strlen($phrase) > 90) {
            return;
        }

        \DB::table('keywords')->updateOrInsert(
            ['phrase' => $phrase],
            [
                'source' => $source,
                'cluster' => $cluster,
                'intent' => $this->intent($phrase),
                'difficulty' => $this->difficulty($phrase),
                'est_volume' => null, // filled once GSC OAuth is wired
                'updated_at' => now(), 'created_at' => now(),
            ]
        );
    }

    protected function intent(string $p): string
    {
        foreach (self::COMMERCIAL as $m) {
            if (str_contains($p, $m)) {
                return 'commercial';
            }
        }

        return str_starts_with($p, 'how') || str_starts_with($p, 'what') || str_starts_with($p, 'why')
            ? 'informational' : 'informational';
    }

    /** Higher = more winnable. Pure heuristics until GSC lands. */
    protected function difficulty(string $p): int
    {
        $words = str_word_count($p);
        $score = 50;

        if ($words >= 3 && $words <= 6) {
            $score -= 15;                       // long-tail sweet spot
        }

        if ($words > 6) {
            $score -= 25;                       // very specific = easy win, low volume
        }

        foreach (['free', 'meaning', 'benefits', 'vidhi', 'samagri'] as $x) {
            if (str_contains($p, $x)) {
                $score -= 10;
            }
        }

        foreach (['astrology', 'horoscope'] as $head) {
            if ($p === $head || $p === "{$head} online") {
                $score += 30;                   // vanity head term
            }
        }

        return max(5, min(95, $score));
    }
}
