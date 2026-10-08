<?php

namespace App\Http\Controllers;

use Auth;
use App\services\AiEngineService;
use App\Models\User;
use App\Models\AiAstrologerModel\AiAstrologer;
use Illuminate\Http\Request;

class ApiChatGPTController extends Controller
{
    protected AiEngineService $aiEngine;

    public function __construct(AiEngineService $aiEngine)
    {
        $this->aiEngine = $aiEngine;
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'astrologerId' => 'nullable'
        ]);

        $user = Auth::guard('api')->user();
        $context = $user ? [
            'name'       => $user->name,
            'birthDate'  => $user->birthDate,
            'birthPlace' => $user->birthPlace,
        ] : null;

        $response = $this->aiEngine->generateText($validated['message'], null, $context);

        return response()->json([
            'message' => $response,
            'status'  => 200,
        ], 200);
    }

    public function askMaster(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'context' => 'nullable|array',
            'language' => 'nullable|string',
        ]);

        $user = Auth::guard('api')->user();
        $context = $validated['context'] ?? ($user ? [
            'name'       => $user->name,
            'birthDate'  => $user->birthDate,
            'birthPlace' => $user->birthPlace,
            'gender'     => $user->gender ?? null,
        ] : null);

        $systemPrompt = "You are Acharya Vashistha, the Divine Master Vedic Astrologer and Spiritual AI for OnlinePuja.live. "
            . "You have deep mastery over Parashari Jyotish, Jaimini Sutras, KP System, Prashna Kundli, Vedic Vastu, and Panchang. "
            . "Your goal is to provide deeply empathetic, authentic, respectful Vedic astrological answers. "
            . "Structure your response with: "
            . "1. 🕉️ Mangal Shloka / Vedic Greeting (Pranam / Om Tat Sat). "
            . "2. 🪐 Planetary Analysis / Graha Dasha breakdown (Sun, Moon, Jupiter, Saturn, Rahu/Ketu). "
            . "3. 💡 Divine Clarity & Practical Guidance on love, career, wealth, health, or family. "
            . "4. 🪔 Vedic Upay / Remedies (Mantra japa, auspicious gemstones, Rudraksha, sacred puja/havan recommendations on OnlinePuja.live). "
            . "Always speak in a divine, reverent, and uplifting tone. Respond in the language used by the user (Hindi, English, or regional language).";

        try {
            $dbPrompt = AiAstrologer::where('type', 'master')->value('system_intruction');
            if (!empty($dbPrompt)) {
                $systemPrompt = $dbPrompt . "\n" . $systemPrompt;
            }
        } catch (\Throwable $e) {}

        $reply = $this->aiEngine->generateText(
            $validated['message'],
            $systemPrompt,
            $context
        );

        return response()->json([
            'message' => $reply,
            'status'  => 200,
            'master_name' => 'Acharya Vashistha (Master Vedic AI)',
        ], 200);
    }

    public function generateImage(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'size'   => 'nullable|string',
        ]);

        $result = $this->aiEngine->generateImage($validated['prompt'], 'photorealistic', '1:1');

        return response()->json($result, (!empty($result['success']) && $result['success']) ? 200 : 500);
    }

    public function generateVideo(Request $request)
    {
        $validated = $request->validate([
            'prompt'    => 'required|string',
            'image_url' => 'nullable|url',
            'duration'  => 'nullable|integer',
        ]);

        $result = $this->aiEngine->generateVideo(
            $validated['prompt'],
            $validated['image_url'] ?? null,
            $validated['duration'] ?? 5
        );

        return response()->json($result, (!empty($result['success']) && $result['success']) ? 200 : 500);
    }
}
