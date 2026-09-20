<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function index(Request $req)
    {
        $input = $req->all();
        $fcmtoken = $input['fcm_token'];
        $userid = $input['user_id'];
        $user = User::findorfail($userid);
        $user->fcmtoken =  $fcmtoken;
        $user->save();
        return response()->json([
            'success'=>true,
            'message'=>'User Updated successfully'
        ]);
    }
}
