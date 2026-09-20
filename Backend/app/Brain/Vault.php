<?php

namespace App\Brain;

use Illuminate\Support\Facades\Crypt;

/**
 * Encrypted settings vault for the Brain kernel.
 * Values are AES-256-GCM encrypted (Laravel Crypt, APP_KEY-derived).
 * Any key may be overridden by an environment constant: BRAIN_<UPPER_KEY>.
 */
class Vault
{
    public static function get(string $key, ?string $default = ''): string
    {
        $env = 'BRAIN_'.strtoupper(preg_replace('/[^a-z0-9]/i', '_', $key));

        if (($v = getenv($env)) !== false && $v !== '') {
            return $v;                                  // constant always wins
        }

        $row = \DB::table('brain_settings')->where('key', $key)->first();

        if (! $row || $row->value_enc === null) {
            return $default;
        }

        try {
            return Crypt::decryptString($row->value_enc);
        } catch (\Throwable $e) {
            report($e);

            return $default;
        }
    }

    public static function set(string $key, ?string $value, bool $isSecret = true): void
    {
        $enc = $value === null ? null : Crypt::encryptString($value);

        \DB::table('brain_settings')->updateOrInsert(
            ['key' => $key],
            ['value_enc' => $enc, 'is_secret' => $isSecret, 'updated_at' => now(), 'created_at' => now()]
        );
    }

    /** Convenience: every credential the AI layer needs. */
    public static function aiCredentials(): array
    {
        return [
            'omniroute_url'   => rtrim(self::get('omniroute_url', 'https://ai.vmstudio.digital'), '/'),
            'omniroute_key'   => self::get('omniroute_key'),          // placeholder until provided
            'openai_key'      => self::get('openai_key'),
            'openrouter_key'  => self::get('openrouter_key'),
            'gemini_key'      => self::get('gemini_key'),
            'ollama_url'      => self::get('ollama_url', 'http://localhost:11434'),
            'ai_text_chain'   => self::get('ai_text_chain', 'omniroute,openai,gemini,openrouter,pollinations,ollama'),
            'ai_image_chain'  => self::get('ai_image_chain', 'omniroute,pollinations,pexels'),
        ];
    }
}
