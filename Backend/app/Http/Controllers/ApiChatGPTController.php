<?php

namespace App\Http\Controllers;
use Auth;
use App\services\ApiOpenAIService;
use Illuminate\Http\Request;

class ApiChatGPTController extends Controller
{
    protected $apiOpenAIService;
    
    public function __construct(ApiOpenAIService $apiOpenAIService)
    {
        $this->apiOpenAIService = $apiOpenAIService;
    }
    
    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'astrologerId' => 'required'
        ]);
        
        if (Auth::guard('api')->user()) {
            $response = $this->apiOpenAIService->askChatGPT($validated['message'], $validated['astrologerId']);

            return response()->json([
                'message' => $response,
                'status'  => 200,
            ],200);
        } else {
            return response()->json([
                'message' => 'Access Denied: You must be logged in to view this page. Please log in to continue.',
                'status'  => 403,
                ], 403);
            
        }
    }

    public function askMaster(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        if (Auth::guard('api')->user()) {
            $response = $this->apiOpenAIService->askChatGPTMaster($validated['message']);

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

    
}
