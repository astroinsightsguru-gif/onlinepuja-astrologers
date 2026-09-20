<?php

namespace App\Brain;

use App\AI\ProviderManager;

/**
 * The self-growing content loop:
 * keyword -> AI draft -> Critic lint -> rewrite-once -> content_plans(drafted).
 * Publishing is a separate, gated step (content:publish).
 */
class ContentPipeline
{
    protected const PERSONA = <<<'TXT'
You are the senior content lead for ONLINE PUJA (https://onlinepuja.live) — an Indian
platform where devotees book verified pandits for pujas, havans and astrology consultations.
Voice: warm, devotional yet practical; rooted in authentic Vedic tradition; helpful to a
first-time devotee. Never invent rituals or prices. Always write for Indian readers in
clear global English.
TXT;

    public function __construct(
        protected ProviderManager $ai,
        protected GoalPlanner $planner
    ) {}

    protected function profileAnchors(): string
    {
        $row = \DB::table('brain_profile')->orderByDesc('id')->first();
        $data = $row ? json_decode($row->data, true) : [];

        return $data['summary']
            ?? 'Online Puja helps devotees across India book verified pandits for pujas, havans, '
               .'kundli matching and astrology consultation entirely online, with live streaming of rituals.';
    }

    /**
     * Draft articles for up to $limit planned/new keywords.
     *
     * @return array{drafted:int,failed:int,results:array}
     */
    public function run(int $limit = 2, bool $publishImmediately = false): array
    {
        $kwTable = \DB::table('keywords');
        $keywords = $kwTable
            ->where('status', 'new')
            ->where('difficulty', '<=', 60)
            ->orderBy('difficulty')
            ->limit($limit)
            ->get();

        $out = ['drafted' => 0, 'failed' => 0, 'results' => []];

        foreach ($keywords as $kw) {
            $res = $this->draftOne($kw);
            $out['results'][] = $res + ['keyword' => $kw->phrase];

            if ($res['ok']) {
                $out['drafted']++;
            } else {
                $out['failed']++;
            }
        }

        return $out;
    }

    protected function draftOne(object $kw): array
    {
        $profile = $this->profileAnchors();

        $system = self::PERSONA."\nBUSINESS CONTEXT (anchor every claim to this):\n{$profile}\n\n".
            'Output STRICT JSON: {"title":string,"slug":string,"meta_description":string(140-158 chars),'.
            '"html":string}. "html" is ONLY the article fragment: start with an <h2>, use <h2>/<h3>/<p>/<ul>, '.
            'natural internal anchor suggestion is NOT needed, no <html>/<body>/styles. 900-1200 words.';

        $prompt = "Write a complete, genuinely useful article targeting the exact search phrase:\n\"{$kw->phrase}\"\n".
            "Search intent: {$kw->intent}. Include practical steps, what to expect, costs-in-principle guidance, ".
            "and a short FAQ (2 questions) at the end.";

        $draft = $this->ai->chat($system, $prompt, ['json' => true, 'temperature' => 0.85, 'max_tokens' => 3200]);

        if (! $draft['ok']) {
            $this->savePlan($kw, null, $draft['error']);

            return ['ok' => false, 'error' => $draft['error']];
        }

        $j = $this->decodeJson($draft['text']);

        if (! isset($j['title'], $j['slug'], $j['html'])) {
            $this->savePlan($kw, null, 'model returned non-JSON');

            return ['ok' => false, 'error' => 'bad-json'];
        }

        // Critic pass — rewrite once with failures fed back.
        $lint = Critic::lint($j['html'], $kw->phrase);

        if (! $lint["pass"]) {
            $retryPrompt = $prompt . " Rewrite the full article fixing these review failures: " . implode("; ", $lint["failures"]);
            $retry = $this->ai->chat($system, $retryPrompt, ["json" => true, "temperature" => 0.7, "max_tokens" => 3200]);
            $j2 = $retry["ok"] ? $this->decodeJson($retry["text"]) : null;

            if ($j2 && isset($j2["title"], $j2["slug"], $j2["html"])) {
                $j = $j2;
                $lint = Critic::lint($j["html"], $kw->phrase);
            }
        }

        $status = $lint['pass'] ? 'drafted' : 'critic_failed';
        $id = $this->savePlan($kw, $j, implode('; ', $lint['failures']), $status);

        return ['ok' => $lint['pass'], 'plan_id' => $id, 'status' => $status, 'failures' => $lint['failures']];
    }

    protected function decodeJson(string $text): ?array
    {
        $clean = preg_replace('/^```(json)?|```$/m', '', trim($text));
        $j = json_decode(trim($clean), true);

        return is_array($j) ? $j : null;
    }

    protected function savePlan(object $kw, ?array $j, string $note, string $status = 'failed'): int
    {
        return \DB::table('content_plans')->insertGetId([
            'title' => $j['title'] ?? ('Draft: '.$kw->phrase),
            'pillar' => $kw->cluster ?: 'general',
            'channel' => 'web',
            'keyword_id' => $kw->id,
            'status' => $status,
            'payload' => json_encode([
                'slug' => $j['slug'] ?? null,
                'meta_description' => $j['meta_description'] ?? null,
                'html' => $j['html'] ?? null,
                'critic_note' => $note,
                'provider' => 'brain',
            ]),
            'updated_at' => now(), 'created_at' => now(),
        ]);
    }
}
