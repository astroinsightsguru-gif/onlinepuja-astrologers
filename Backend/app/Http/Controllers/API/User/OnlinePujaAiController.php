<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\services\AiEngineService;
use Illuminate\Http\Request;

class OnlinePujaAiController extends Controller
{
    protected AiEngineService $aiEngine;

    public function __construct(AiEngineService $aiEngine)
    {
        $this->aiEngine = $aiEngine;
    }

    /**
     * Handle Vedic AI Jyotish consultation chats for OnlinePuja live.
     * Uses Growth OS / ProviderManager failover chain:
     * OmniRoute -> Gemini -> OpenRouter -> OpenAI -> Pollinations
     */
    public function chat(Request $request)
    {
        $prompt = trim((string) ($request->message ?? $request->question ?? ''));
        if ($prompt === '') {
            return response()->json([
                'status' => 400,
                'message' => 'Question or message is required.'
            ], 400);
        }

        $context = $request->context ?? [];
        $systemPrompt = "You are 'OnlinePuja AI', an enlightened Vedic Jyotish master and spiritual advisor for onlinepuja.live. " .
            "Offer clear, empathetic, and authentic guidance based on Vedic astrology, planetary transits, and sacred Hindu rituals. " .
            "Respond in warm, respectful Hinglish (Hindi + English). When relevant, suggest auspicious remedies like chanting mantras, wearing gemstones, or participating in sacred temple pujas.";

        try {
            $reply = $this->aiEngine->generateText($prompt, $systemPrompt, $context);
            return response()->json([
                'status' => 200,
                'reply' => $reply,
                'message' => $reply,
                'engine' => 'GrowthOS-Brain',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'OnlinePuja AI is temporarily reflecting on sacred scriptures. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
