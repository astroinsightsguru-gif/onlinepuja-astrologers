<?php

namespace App\Http\Controllers;

use Auth;
use App\services\AiEngineService;
use App\Models\User;
use App\Models\AiAstrologerModel\AiAstrologer;
use Illuminate\Http\Request;

class ApiChatGPTController extends Controller
{
    protected ;

    public function __construct(AiEngineService )
    {
        ->aiEngine = ;
    }

    public function ask(Request )
    {
         = ->validate([
            'message' => 'required|string',
            'astrologerId' => 'nullable'
        ]);

         = Auth::guard('api')->user();
         =  ? [
            'name'       => ->name,
            'birthDate'  => ->birthDate,
            'birthPlace' => ->birthPlace,
        ] : null;

         = ->aiEngine->generateText(['message'], null, );

        return response()->json([
            'message' => ,
            'status'  => 200,
        ], 200);
    }

    public function askMaster(Request )
    {
         = ->validate([
            'message' => 'required|string',
        ]);

         = Auth::guard('api')->user();
         =  ? [
            'name'       => ->name,
            'birthDate'  => ->birthDate,
            'birthPlace' => ->birthPlace,
        ] : null;

         = null;
        try {
             = AiAstrologer::where('type', 'master')->value('system_intruction');
        } catch (\Exception ) {}

         = ->aiEngine->generateText(
            ['message'],
            ,
            
        );

        return response()->json([
            'message' => ,
            'status'  => 200,
        ], 200);
    }

    public function generateImage(Request )
    {
         = ->validate([
            'prompt' => 'required|string',
            'size'   => 'nullable|string',
        ]);

         = ->aiEngine->generateImage(['prompt'], ['size'] ?? '1024x1024');

        return response()->json(, ['success'] ? 200 : 500);
    }

    public function generateVideo(Request )
    {
         = ->validate([
            'prompt'    => 'required|string',
            'image_url' => 'nullable|url',
            'duration'  => 'nullable|integer',
        ]);

         = ->aiEngine->generateVideo(
            ['prompt'],
            ['image_url'] ?? null,
            ['duration'] ?? 5
        );

        return response()->json(, ['success'] ? 200 : 500);
    }
}
