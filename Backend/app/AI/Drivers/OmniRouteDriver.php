<?php

namespace App\AI\Drivers;

use App\AI\Contracts\TextProvider;
use App\AI\Contracts\ImageProvider;
use App\Brain\Vault;
use Illuminate\Support\Facades\Http;

/**
 * OmniRoute — your self-hosted / cloud OpenAI-compatible gateway.
 *
 *   POST {base}/v1/chat/completions
 *   POST {base}/v1/images/generations
 *   POST {base}/v1/videos/generations
 *   GET  {base}/v1/models
 */
class OmniRouteDriver implements TextProvider, ImageProvider
{
    protected function base(): string
    {
        $base = rtrim(Vault::aiCredentials()['omniroute_url'] ?? 'https://ai.vmstudio.digital', '/');

        return str_ends_with($base, '/v1') ? $base : $base.'/v1';
    }

    protected function key(): string
    {
        return (string) (Vault::aiCredentials()['omniroute_key'] ?? '');
    }

    public function isReady(): bool
    {
        return $this->key() !== '' && ! str_contains($this->key(), 'REPLACE_WITH');
    }

    /* ---------------- TextProvider ---------------- */

    public function slug(): string
    {
        return 'omniroute';
    }

    public function label(): string
    {
        return 'OmniRoute';
    }

    public function isConfigured(): bool
    {
        return $this->isReady();
    }

    public function chat(string $system, string $prompt, array $args = []): array
    {
        $fail = fn (string $e) => ['ok' => false, 'text' => '', 'model' => '', 'usage' => [], 'error' => $e];

        if (! $this->isReady()) {
            return $fail('OmniRoute key/URL not configured yet.');
        }

        $payload = array_filter([
            'model'       => $args['model'] ?? 'gpt-4o',
            'messages'    => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => (float) ($args['temperature'] ?? 0.7),
            'max_tokens'  => (int) ($args['max_tokens'] ?? 1200),
            'response_format' => ($args['json'] ?? false) ? ['type' => 'json_object'] : null,
        ]);

        try {
            $res = Http::asJson()->withToken($this->key())
                ->timeout(90)
                ->post($this->base().'/chat/completions', $payload);
        } catch (\Throwable $e) {
            return $fail($e->getMessage());
        }

        if ($res->failed()) {
            return $fail('HTTP '.$res->status().' :: '.substr($res->body(), 0, 300));
        }

        $j = $res->json();

        return [
            'ok'    => true,
            'text'  => $j['choices'][0]['message']['content'] ?? '',
            'model' => $j['model'] ?? ($payload['model']),
            'usage' => $j['usage'] ?? [],
            'error' => '',
        ];
    }

    /* ---------------- ImageProvider ---------------- */

    public function create(string $prompt, array $args = []): array
    {
        $fail = fn (string $e) => ['ok' => false, 'binary' => '', 'url' => '', 'mime' => '', 'credit' => '', 'error' => $e];

        if (! $this->isReady()) {
            return $fail('OmniRoute not configured.');
        }

        $size = $args['size'] ?? '1024x1024';
        $body = [
            'model'           => $args['image_model'] ?? 'flux',
            'prompt'          => $prompt,
            'n'               => 1,
            'size'            => $size,
            'response_format' => 'b64_json',
        ];

        if (! empty($args['negative_prompt'])) {
            $body['negative_prompt'] = $args['negative_prompt'];
        }

        try {
            $res = Http::asJson()->withToken($this->key())
                ->timeout(120)
                ->post($this->base().'/images/generations', $body);
        } catch (\Throwable $e) {
            return $fail($e->getMessage());
        }

        if ($res->failed()) {
            return $fail('HTTP '.$res->status().' :: '.substr($res->body(), 0, 300));
        }

        $b64 = $res->json('data.0.b64_json');

        if ($b64 && ($bin = base64_decode($b64, true))) {
            return ['ok' => true, 'binary' => $bin, 'url' => 'data:image/png;base64,'.$b64, 'mime' => 'image/png', 'credit' => 'Generated via OmniRoute Flux', 'error' => ''];
        }

        if ($url = $res->json('data.0.url')) {
            return ['ok' => true, 'binary' => '', 'url' => $url, 'mime' => 'image/png', 'credit' => 'Generated via OmniRoute Flux', 'error' => ''];
        }

        return $fail('No usable image returned.');
    }

    /* ---------------- Live model sync ---------------- */

    public function models(string $kind = 'text'): array
    {
        if (! $this->isReady()) {
            return [];
        }

        try {
            $res = Http::withToken($this->key())->timeout(15)->get($this->base().'/models');
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
}
