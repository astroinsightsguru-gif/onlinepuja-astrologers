<?php

namespace App\AI\Drivers;

use App\AI\Contracts\TextProvider;
use Illuminate\Support\Facades\Http;

/**
 * Local Ollama fallback — runs on CPU, no external API key required.
 * Used when all online providers are exhausted or unavailable.
 */
class OllamaDriver implements TextProvider
{
    protected function base(): string
    {
        return rtrim(config('ai.ollama_url', 'http://localhost:11434'), '/');
    }

    public function slug(): string
    {
        return 'ollama';
    }

    public function label(): string
    {
        return 'Ollama (local)';
    }

    public function isConfigured(): bool
    {
        return true; // local endpoint, always available when container runs
    }

    public function chat(string $system, string $prompt, array $args = []): array
    {
        $fail = fn (string $e) => ['ok' => false, 'text' => '', 'model' => '', 'usage' => [], 'error' => $e];

        $payload = [
            'model'    => $args['model'] ?? 'llama3.2',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'stream'   => false,
        ];

        if (($temp = ($args['temperature'] ?? null)) !== null) {
            $payload['temperature'] = (float) $temp;
        }
        if (($max = ($args['max_tokens'] ?? null)) !== null) {
            $payload['max_tokens'] = (int) $max;
        }

        try {
            $res = Http::timeout(90)->post($this->base().'/api/chat', $payload);
        } catch (\Throwable $e) {
            return $fail($e->getMessage());
        }

        if ($res->failed()) {
            return $fail('HTTP ' . $res->status() . ' :: ' . substr($res->body(), 0, 300));
        }

        $j = $res->json();

        return [
            'ok'    => true,
            'text'  => $j['message']['content'] ?? '',
            'model' => $j['model'] ?? 'llama3.2',
            'usage' => [],
            'error' => '',
        ];
    }

    public function models(): array
    {
        try {
            $res = Http::timeout(15)->get($this->base() . '/api/tags');
        } catch (\Throwable $e) {
            return [];
        }

        $out = [];

        foreach ((array) $res->json('models') as $m) {
            if ($id = $m['name'] ?? null) {
                $out[] = ['id' => $id, 'label' => $id];
            }
        }

        return $out;
    }
}

/**
 * Local Ollama fallback — runs on CPU, no external API key required.
 * Used when all online providers are exhausted or unavailable.
 */
class OllamaDriver implements TextProvider
{
    protected function base(): string
    {
        return rtrim(config('ai.ollama_url', 'http://localhost:11434'), '/');
    }

    public function slug(): string
    {
        return 'ollama';
    }

    public function label(): string
    {
        return 'Ollama (local)';
    }

    public function isConfigured(): bool
    {
        return true; // local endpoint, always available when container runs
    }

    public function chat(string $system, string $prompt, array $args = []): array
    {
        $fail = fn (string $e) => ['ok' => false, 'text' => '', 'model' => '', 'usage' => [], 'error' => $e];

        $payload = [
            'model'    => $args['model'] ?? 'llama3.2',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'stream'   => false,
        ];

        if (($temp = ($args['temperature'] ?? null)) !== null) {
            $payload['temperature'] = (float) $temp;
        }
        if (($max = ($args['max_tokens'] ?? null)) !== null) {
            $payload['max_tokens'] = (int) $max;
        }

        try {
            $res = Http::timeout(90)->post($this->base().'/api/chat', $payload);
        } catch (\\Throwable $e) {
            return $fail($e->getMessage());
        }

        if ($res->failed()) {
            return $fail('HTTP ' . $res->status() . ' :: ' . substr($res->body(), 0, 300));
        }

        $j = $res->json();

        return [
            'ok'    => true,
            'text'  => $j['message']['content'] ?? '',
            'model' => $j['model'] ?? 'llama3.2',
            'usage' => [],
            'error' => '',
        ];
    }

    public function models(): array
    {
        try {
            $res = Http::timeout(15)->get($this->base() . '/api/tags');
        } catch (\\Throwable $e) {
            return [];
        }

        $out = [];

        foreach ((array) $res->json('models') as $m) {
            if ($id = $m['name'] ?? null) {
                $out[] = ['id' => $id, 'label' => $id];
            }
        }

        return $out;
    }
}