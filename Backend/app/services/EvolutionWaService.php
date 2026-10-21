<?php

namespace App\Services;

use App\Brain\Vault;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWaService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $instance;

    public function __construct()
    {
        $this->apiUrl = rtrim(Vault::get('evolution_api_url', config('services.evolution.url', 'https://wa.vmstudio.digital')), '/');
        $this->apiKey = Vault::get('evolution_api_key', config('services.evolution.key', 'bebf8554b69b4eff665465956069890dadeb84547a964ba0ac129e43e98576a2'));
        $this->instance = Vault::get('evolution_instance', config('services.evolution.instance', 'onlinepuja'));
    }

    /**
     * Normalize Indian or international phone numbers for WhatsApp.
     */
    public function cleanNumber(string $rawNumber): string
    {
        $clean = preg_replace('/[^0-9]/', '', $rawNumber);
        if (strlen($clean) === 10) {
            $clean = '91' . $clean; // Default to India (+91)
        }
        return $clean;
    }

    /**
     * Send Plain or Formatted Text Message.
     */
    public function sendText(string $mobile, string $text): array
    {
        $number = $this->cleanNumber($mobile);
        $endpoint = "{$this->apiUrl}/message/sendText/{$this->instance}";

        try {
            $res = Http::withHeaders([
                'apikey'       => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(12)->post($endpoint, [
                'number' => $number,
                'text'   => $text,
            ]);

            if ($res->successful()) {
                return ['ok' => true, 'data' => $res->json()];
            }

            Log::error('Evolution API sendText failed', ['status' => $res->status(), 'body' => $res->body()]);
            return ['ok' => false, 'error' => 'HTTP ' . $res->status() . ' :: ' . substr($res->body(), 0, 200)];
        } catch (\Throwable $e) {
            Log::error('Evolution API sendText exception: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send Media Message (Video Reels MP4, Images, Audio, PDF).
     */
    public function sendMedia(
        string $mobile,
        string $mediaUrl,
        string $caption = '',
        string $mediaType = 'video',
        ?string $fileName = null
    ): array {
        $number = $this->cleanNumber($mobile);
        $endpoint = "{$this->apiUrl}/message/sendMedia/{$this->instance}";

        $mediaPayload = $mediaUrl;

        // If local file exists, send as base64 for 100% reliable delivery
        if (str_starts_with($mediaUrl, 'https://onlinepuja.live/')) {
            $localRel = str_replace('https://onlinepuja.live/', '', $mediaUrl);
            $localPath = public_path($localRel);
            if (!file_exists($localPath) && str_starts_with($localRel, 'storage/')) {
                $localPath = storage_path('app/public/' . substr($localRel, 8));
            }
            if (file_exists($localPath) && filesize($localPath) < 15 * 1024 * 1024) {
                $mime = mime_content_type($localPath) ?: ($mediaType === 'video' ? 'video/mp4' : 'image/jpeg');
                $mediaPayload = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($localPath));
            }
        }

        $payload = [
            'number'    => $number,
            'mediatype' => $mediaType, // 'image', 'video', 'document', 'audio'
            'media'     => $mediaPayload,
            'caption'   => $caption,
        ];

        if ($fileName) {
            $payload['fileName'] = $fileName;
        }

        try {
            $res = Http::withHeaders([
                'apikey'       => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($endpoint, $payload);

            if ($res->successful()) {
                return ['ok' => true, 'data' => $res->json()];
            }

            Log::error('Evolution API sendMedia failed, falling back to text', ['status' => $res->status(), 'body' => $res->body()]);
            // Fallback to high-contrast text with media link
            $textFallback = $caption . (!empty($mediaUrl) ? "\n\n🎬 Watch Sacred Video Reel:\n{$mediaUrl}" : '');
            return $this->sendText($mobile, $textFallback);
        } catch (\Throwable $e) {
            Log::error('Evolution API sendMedia exception: ' . $e->getMessage() . ', falling back to text');
            $textFallback = $caption . (!empty($mediaUrl) ? "\n\n🎬 Watch Sacred Video Reel:\n{$mediaUrl}" : '');
            return $this->sendText($mobile, $textFallback);
        }
    }

    /**
     * Send Interactive CTA Button Menu.
     * $buttons format: [['id' => 'book_puja', 'text' => '🙏 Book Puja'], ['id' => 'astrologer', 'text' => '🔮 Talk to Pandit']]
     */
    public function sendButtons(
        string $mobile,
        string $title,
        string $description,
        array $buttons,
        ?string $footer = 'OnlinePuja.live · Sanctified Vedic Services'
    ): array {
        $number = $this->cleanNumber($mobile);
        $endpoint = "{$this->apiUrl}/message/sendButtons/{$this->instance}";

        $formattedButtons = [];
        foreach ($buttons as $i => $btn) {
            $formattedButtons[] = [
                'buttonId'   => $btn['id'] ?? ('btn_' . $i),
                'buttonText' => ['displayText' => $btn['text'] ?? 'Click'],
                'type'       => 1,
            ];
        }

        $payload = [
            'number'      => $number,
            'title'       => $title,
            'description' => $description,
            'footer'      => $footer,
            'buttons'     => $formattedButtons,
        ];

        try {
            $res = Http::withHeaders([
                'apikey'       => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(12)->post($endpoint, $payload);

            if ($res->successful()) {
                return ['ok' => true, 'data' => $res->json()];
            }

            // Fallback to text with numbering if buttons fail on recipient client
            $textFallback = "{$title}\n\n{$description}\n\n";
            foreach ($buttons as $idx => $b) {
                $textFallback .= ($idx + 1) . ". " . ($b['text'] ?? '') . "\n";
            }
            if ($footer) {
                $textFallback .= "\n_{$footer}_";
            }
            return $this->sendText($mobile, $textFallback);
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get Current Connection State (open, close, connecting).
     */
    public function getConnectionState(): array
    {
        $endpoint = "{$this->apiUrl}/instance/connectionState/{$this->instance}";
        try {
            $res = Http::withHeaders(['apikey' => $this->apiKey])->timeout(6)->get($endpoint);
            if ($res->successful()) {
                $data = $res->json();
                $state = $data['instance']['state'] ?? 'unknown';
                return ['ok' => true, 'state' => $state, 'raw' => $data];
            }
            return ['ok' => false, 'state' => 'error', 'status' => $res->status()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'state' => 'offline', 'error' => $e->getMessage()];
        }
    }

    /**
     * Request a fresh QR Code or Pairing Code for WhatsApp login.
     */
    public function requestQrCode(): array
    {
        $endpoint = "{$this->apiUrl}/instance/connect/{$this->instance}";
        try {
            $res = Http::withHeaders(['apikey' => $this->apiKey])->timeout(10)->get($endpoint);
            if ($res->successful()) {
                $data = $res->json();
                return [
                    'ok'          => true,
                    'base64'      => $data['base64'] ?? null,
                    'pairingCode' => $data['pairingCode'] ?? null,
                    'code'        => $data['code'] ?? null,
                ];
            }
            return ['ok' => false, 'error' => 'HTTP ' . $res->status()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
