<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;  // Import DB facade

class DefaulterMessageController extends Controller
{
    public function storeDefaulterMessage(Request $request)
    {
        $validated = $request->validate([
            'message'   => 'required',
            'userId'    => 'required',
        ]);

         DB::table('defaulter_messages')->insert([
            'message'       => $request->message,
            'user_id'       => $request->userId, 
            'type'          => $request->type,
            'sender_id'     =>$request->sender_id,
            'sender_type'   =>$request->sender_type,
            'receiver_id'   =>$request->receiver_id,
            'receiver_type' =>$request->receiver_type,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json(['message' => 'Warning: Offensive content detected. Please be respectful.']);
    }



}
