<?php

namespace App\AI;

use App\AI\Contracts\TextProvider;
use App\AI\Drivers\OmniRouteDriver;
use App\Brain\Vault;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Ordered failover chain manager with per-provider circuit breaker.
 * Chain order lives in the vault (ai_text_chain / ai_image_chain).
 * After N consecutive failures a provider is benched for a cooldown,
 * so a dead key degrades quality instead of stopping the system.
 */
class ProviderManager
{
    protected const MAX_FAILS = 3;
    protected const COOLDOWN_SECONDS = 600;

    protected array $creds;

    public function __construct()
    {
        $this->creds = Vault::aiCredentials();
    }

    public function textProviders(): array
    {
        return [
            'omniroute'    => new OmniRouteDriver(),
            'openai'       => $this->openAiCompat('openai', 'https://api.openai.com/v1', $this->creds['openai_key'], 'gpt-4o'),
            'openrouter'   => $this->openAiCompat('openrouter', 'https://openrouter.ai/api/v1', $this->creds['openrouter_key'], 'openai/gpt-4o-mini'),
            'gemini'       => $this->openAiCompat('gemini', 'https://generativelanguage.googleapis.com/v1beta/openai', $this->creds['gemini_key'], 'gemini-1.5-flash'),
            'pollinations' => new \App\AI\Drivers\PollinationsDriver(),
        ];
    }

    protected function openAiCompat(string $slug, string $base, string $key, string $defaultModel): TextProvider
    {
        return new class($slug, $base, $key, $defaultModel) implements TextProvider
        {
            public function __construct(
                protected string $slug,
                protected string $base,
                protected string $key,
                protected string $defaultModel
            ) {}

            public function slug(): string
            {
                return $this->slug;
            }

            public function label(): string
            {
                return ucfirst($this->slug);
            }

            public function isConfigured(): bool
            {
                return $this->key !== '';
            }

            public function chat(string $system, string $prompt, array $args = []): array
            {
                $fail = fn (string $e) => ['ok' => false, 'text' => '', 'model' => '', 'usage' => [], 'error' => $e];

                try {
                    $res = Http::asJson()->withToken($this->key)
                        ->timeout(15)
                        ->post(rtrim($this->base, '/').'/chat/completions', [
                            'model'       => $args['model'] ?? $this->defaultModel,
                            'messages'    => [
                                ['role' => 'system', 'content' => $system],
                                ['role' => 'user', 'content' => $prompt],
                            ],
                            'temperature' => (float) ($args['temperature'] ?? 0.7),
                            'max_tokens'  => (int) ($args['max_tokens'] ?? 1200),
                        ]);
                } catch (\Throwable $e) {
                    return $fail($e->getMessage());
                }

                if ($res->failed()) {
                    return $fail('HTTP '.$res->status());
                }

                $j = $res->json();

                return [
                    'ok'    => true,
                    'text'  => $j['choices'][0]['message']['content'] ?? '',
                    'model' => $j['model'] ?? $this->defaultModel,
                    'usage' => $j['usage'] ?? [],
                    'error' => '',
                ];
            }

            public function models(): array
            {
                try {
                    $res = Http::withToken($this->key)->timeout(12)
                        ->get(rtrim($this->base, '/').'/models');
                } catch (\Throwable $e) {
                    return [];
                }

                $out = [];

                foreach ((array) $res->json('data') as $m) {
                    if ($id = $m['id'] ?? null) {
                        $out[] = ['id' => $id, 'label' => $m['name'] ?? $id];
                    }
                }

                return $out;
            }
        };
    }

    /* ------------------------------------------- circuit breaker ---- */

    protected function benchKey(string $slug): string
    {
        return "ai.bench.$slug";
    }

    protected function isBenched(string $slug): bool
    {
        return Cache::get($this->benchKey($slug), 0) > time();
    }

    protected function recordFailure(string $slug): void
    {
        $k = "ai.fails.$slug";
        $fails = Cache::get($k, 0) + 1;
        Cache::put($k, $fails, 3600);

        if ($fails >= self::MAX_FAILS) {
            Cache::put($this->benchKey($slug), time() + self::COOLDOWN_SECONDS, self::COOLDOWN_SECONDS);
            Cache::forget($k);
        }
    }

    protected function recordSuccess(string $slug): void
    {
        Cache::forget("ai.fails.$slug");
        Cache::forget($this->benchKey($slug));
    }

    /* --------------------------------------------------- chat ------- */

    /**
     * Run a chat down the ordered chain; first success wins.
     */
    public function chat(string $system, string $prompt, array $args = []): array
    {
        $chain = array_filter(array_map('trim', explode(',', $this->creds['ai_text_chain'])));
        $providers = $this->textProviders();
        $errors = [];

        foreach ($chain as $slug) {
            if (! isset($providers[$slug]) || $this->isBenched($slug)) {
                continue;
            }

            /** @var TextProvider $p */
            $p = $providers[$slug];

            if (! $p->isConfigured()) {
                continue;
            }

            $res = $p->chat($system, $prompt, $args);

            if ($res['ok'] && trim($res['text']) !== '') {
                $this->recordSuccess($slug);

                return $res + ['provider' => $slug, 'error' => ''];
            }

            $this->recordFailure($slug);
            $errors[$slug] = $res['error'];
        }

        return [
            'ok' => false, 'text' => '', 'model' => '', 'provider' => '',
            'usage' => [], 'error' => 'All providers failed: '.implode(' | ', $errors),
        ];
    }

    /* --------------------------------------- live model sync -------- */

    /**
     * Sync every configured provider's catalogue into ai_models.
     * Scheduled twice a day — mirrors VMSAI_Model_Sync.
     */
    public function syncModels(): array
    {
        $report = ['providers' => [], 'total' => 0, 'errors' => []];
        $now = now();

        foreach ($this->textProviders() as $slug => $p) {
            if (! $p->isConfigured()) {
                continue;
            }

            try {
                $models = $p->models();
            } catch (\Throwable $e) {
                $report['errors'][$slug] = $e->getMessage();
                continue;
            }

            foreach ($models as $m) {
                \DB::table('ai_models')->updateOrInsert(
                    ['provider' => $slug, 'kind' => 'text', 'model_id' => $m['id']],
                    ['label' => $m['label'], 'enabled' => true, 'last_synced_at' => $now]
                );
            }

            $report['providers'][$slug] = count($models);
            $report['total'] += count($models);
        }

        // Image models via OmniRoute when ready.
        $omni = new OmniRouteDriver();

        if ($omni->isConfigured()) {
            foreach ($omni->models('image') as $m) {
                \DB::table('ai_models')->updateOrInsert(
                    ['provider' => 'omniroute', 'kind' => 'image', 'model_id' => $m['id']],
                    ['label' => $m['label'], 'enabled' => true, 'last_synced_at' => $now]
                );
                $report['total']++;
            }
        }

        \DB::table('brain_memories')->updateOrInsert(
            ['k' => 'last_model_sync'],
            ['v' => json_encode($report), 'source' => 'model-sync', 'updated_at' => now(), 'created_at' => now()]
        );

        return $report;
    }
}
