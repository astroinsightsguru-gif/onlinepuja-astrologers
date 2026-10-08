<?php

namespace App\services;

use App\AI\ProviderManager;
use App\AI\Drivers\OmniRouteDriver;
use App\AI\VedicPromptEnhancer;
use App\Brain\Vault;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class AiEngineService
{
    protected ProviderManager $manager;
    protected Client $client;

    public function __construct()
    {
        $this->manager = app(ProviderManager::class);
        $this->client = new Client(['timeout' => 60]);
    }

    /**
     * Generate text completion using the Growth OS Vault failover chain
     * (OmniRoute -> Gemini -> OpenRouter -> OpenAI -> Pollinations).
     */
    public function generateText(string $userPrompt, ?string $systemPrompt = null, ?array $context = null): string
    {
        $system = $systemPrompt ?? "You are an expert Vedic astrologer and spiritual guide for Online Puja. Provide clear, compassionate, and authentic guidance based on traditional Vedic astrology, Jyotish shastra, and spiritual principles in simple Hinglish (Hindi + English).";

        $fullPrompt = $userPrompt;
        if (!empty($context)) {
            $contextStr = "User Details: Name: " . ($context['name'] ?? 'Devotee') . 
                          ", DOB: " . ($context['birthDate'] ?? 'Unknown') . 
                          ", Place: " . ($context['birthPlace'] ?? 'Unknown') . ". ";
            $fullPrompt = $contextStr . "\nQuestion: " . $userPrompt;
        }

        // Try the ProviderManager failover chain first
        try {
            $response = $this->manager->chat($system, $fullPrompt);
            if (!empty($response['ok']) && !empty($response['text'])) {
                return trim($response['text']);
            }
        } catch (\Throwable $e) {
            Log::warning("ProviderManager chat error: " . $e->getMessage());
        }

        // Direct Fallback to Gemini if ProviderManager had an issue
        $creds = Vault::aiCredentials();
        $geminiKey = $creds['gemini_key'] ?: env('GEMINI_API_KEY');
        if ($geminiKey) {
            try {
                $res = $this->client->post('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $geminiKey,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gemini-1.5-flash',
                        'messages' => [
                            ['role' => 'system', 'content' => $system],
                            ['role' => 'user', 'content' => $fullPrompt],
                        ],
                        'temperature' => 0.7,
                    ],
                ]);
                $body = json_decode($res->getBody(), true);
                if (!empty($body['choices'][0]['message']['content'])) {
                    return trim($body['choices'][0]['message']['content']);
                }
            } catch (\Throwable $e) {
                Log::warning("Direct Gemini fallback failed: " . $e->getMessage());
            }
        }

        return "Pranam. The cosmic planetary transits are realigning. Please try asking again in a few moments.";
    }

    /**
     * Generate Authentic Spiritual & Festival Images (Photorealistic Vedic Art)
     */
    public function generateImage(string $rawPrompt, string $style = 'photorealistic', string $aspectRatio = '1:1'): array
    {
        $enhanced = VedicPromptEnhancer::enhance($rawPrompt, $style, $aspectRatio);
        $prompt = $enhanced['prompt'];
        $negativePrompt = $enhanced['negative_prompt'];
        $size = $enhanced['size'];

        $creds = Vault::aiCredentials();
        $url = rtrim($creds['omniroute_url'] ?? 'https://ai.vmstudio.digital', '/') . '/v1/images/generations';
        $key = $creds['omniroute_key'] ?: env('OMNIROUTE_API_KEY');

        // 1. Try OmniRoute if key is configured
        if (!empty($key)) {
            try {
                $response = $this->client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $key,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => [
                        'model'           => 'flux',
                        'prompt'          => $prompt,
                        'negative_prompt' => $negativePrompt,
                        'n'               => 1,
                        'size'            => $size,
                    ],
                    'timeout' => 25,
                ]);

                $data = json_decode($response->getBody(), true);
                if (!empty($data['data'][0]['url'])) {
                    return [
                        'success'  => true,
                        'url'      => $data['data'][0]['url'],
                        'prompt'   => $prompt,
                        'provider' => 'omniroute_flux',
                    ];
                }
            } catch (\Throwable $e) {
                Log::info("OmniRoute image returned: " . $e->getMessage() . " — Falling back to free direct Flux tier.");
            }
        }

        // 2. Guaranteed Free Tier: Direct Pollinations Flux (Keyless, 100% Free, Unlimited)
        $cleanPrompt = rawurlencode($prompt);
        $w = 1024;
        $h = 1024;
        if ($aspectRatio === '4:5') { $w = 1024; $h = 1280; }
        if ($aspectRatio === '9:16') { $w = 768; $h = 1344; }
        if ($aspectRatio === '16:9') { $w = 1344; $h = 768; }

        $pollinationsUrl = "https://image.pollinations.ai/prompt/{$cleanPrompt}?model=flux&width={$w}&height={$h}&nologo=true&seed=" . rand(1000, 999999);

        return [
            'success'  => true,
            'url'      => $pollinationsUrl,
            'prompt'   => $prompt,
            'provider' => 'flux_free',
            'model'    => 'flux',
        ];
    }

    /**
     * Generate Spiritual Video Reels (Instagram / Facebook Reels, Darshan Reel)
     */
    public function generateVideo(string $rawTopic, ?string $imageUrl = null, int $duration = 5): array
    {
        $creds = Vault::aiCredentials();
        $url = rtrim($creds['omniroute_url'] ?? 'https://ai.vmstudio.digital', '/') . '/v1/videos/generations';
        $key = $creds['omniroute_key'] ?: env('OMNIROUTE_API_KEY');

        // Retrieve configured model from Vault (e.g. kie/grok-imagine/text-to-video, segmind/hunyuan-video-t2v, fal-ai/...)
        $videoModel = Vault::get('model_omniroute_video', 'fal-ai/xai/grok-imagine-video/text-to-video');

        $enhanced = VedicPromptEnhancer::enhance($rawTopic, 'video_reel', '9:16');
        $videoPrompt = $enhanced['prompt'];

        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'OmniRoute video requires a configured API key with video provider credits (e.g. fal.ai, segmind, or grok-imagine).'
            ];
        }

        try {
            $payload = [
                'model'           => $videoModel,
                'prompt'          => $videoPrompt,
                'negative_prompt' => VedicPromptEnhancer::NEGATIVE_PROMPT,
                'duration'        => $duration,
            ];
            if ($imageUrl) {
                $payload['image_url'] = $imageUrl;
            }

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $key,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 45,
            ]);

            $data = json_decode($response->getBody(), true);
            $videoUrl = $data['data']['video_url'] ?? $data['video_url'] ?? $data['url'] ?? null;

            if ($videoUrl) {
                return [
                    'success'   => true,
                    'video_url' => $videoUrl,
                    'task_id'   => $data['task_id'] ?? null,
                    'prompt'    => $videoPrompt,
                    'provider'  => 'omniroute',
                    'model'     => $videoModel,
                ];
            }

            return [
                'success' => false,
                'message' => 'OmniRoute accepted request but did not return immediate video URL. Task ID: ' . ($data['task_id'] ?? 'N/A')
            ];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $resBody = (string) $e->getResponse()->getBody();
            $errData = json_decode($resBody, true);
            $errMsg = $errData['error']['message'] ?? $resBody;

            if (str_contains($errMsg, 'Exhausted balance') || str_contains($errMsg, 'Top up')) {
                $friendly = "OmniRoute upstream video provider balance exhausted. Please top up credits at your provider dashboard (e.g. fal.ai) to generate AI videos.";
            } elseif (str_contains($errMsg, 'Invalid video model')) {
                $friendly = "Invalid video model '{$videoModel}'. Configured models: " . Vault::get('ai_video_models', 'fal-ai/xai/grok-imagine-video/text-to-video');
            } else {
                $friendly = "OmniRoute Video API: " . substr($errMsg, 0, 200);
            }

            return ['success' => false, 'message' => $friendly];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Video generation error: ' . $e->getMessage()];
        }
    }
}
