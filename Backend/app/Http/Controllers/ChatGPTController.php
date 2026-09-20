<?php

namespace App\Http\Controllers;

use App\services\OpenAIService;
use Illuminate\Http\Request;

class ChatGPTController extends Controller
{
    protected $openAIService;
    
    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    
    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'astrologerId' => 'required'
        ]);
        
        if (authcheck()) {
            // $response = $this->openAIService->askChatGPT($validated['message']);
            $response = $this->openAIService->askChatGPT($validated['message'], $validated['astrologerId']);

            return response()->json([
                'message' => $response,
            ]);
        } else {
            return response()->json(['warning' => 'Access Denied: You must be logged in to view this page. Please log in to continue.'], 403);
            
        }
    }

    public function askMaster(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        if (authcheck()) {
            $response = $this->openAIService->askChatGPTMaster($validated['message']);

            return response()->json([
                'message' => $response,
                'status'  => 200,
            ],200);
        } else {
            return response()->json([
                'message' => 'Access Denied: You must be logged in to view this page. Please log in to continue.',
                'status'  =>403,
            ], 403);
            
        }
    }





    // protected $openAIService;
    
    // public function __construct(OpenAIService $openAIService)
    // {
    //     $this->openAIService = $openAIService;
    // }
    
    // public function ask(Request $request)
    // {
    //     $question = $request->input('message');
    //     $answer = $this->openAIService->askQuestion($question);
    
    //     return response()->json([
    //                 'message' => $response,
    //             ]);
    //     // return view('chat', ['question' => $question, 'answer' => $answer]);
    // }
    
    
}
