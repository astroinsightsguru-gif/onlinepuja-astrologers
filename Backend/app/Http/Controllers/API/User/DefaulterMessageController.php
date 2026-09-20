<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;  // Import DB facade
use Illuminate\Support\Facades\Validator;

class DefaulterMessageController extends Controller
{
    public function storeDefaulterMessage(Request $request)
    {
        $validated = $request->validate([
            'message'   => 'required',
            'sender_id'    => 'required',
        ]);
        try
        {
            DB::table('defaulter_messages')->insert([
                'user_id'       => $request->sender_id,
                'type'          => $request->sender_type,
                'message'       => $request->message,
                'sender_id'     => $request->sender_id,
                'sender_type'   => $request->sender_type,
                'receiver_id'   => $request->receiver_id,
                'receiver_type' => $request->receiver_type,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return response()->json([
                'message'   => 'Warning: Offensive content detected. Please be respectful.'    ,
                'status'    =>200,
                ],200);
        } catch (\Exception$e) {
            return Response()->json([
                'error'     => false,
                'message'   => $e->getMessage(),
                'status'    => 500,
            ], 500);
        }
    }



}
