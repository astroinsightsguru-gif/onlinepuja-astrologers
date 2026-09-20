<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LiveUserController extends Controller
{
    //Add Gift
    public function addLiveUser(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'channelName',
                'fcmToken',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'channelName' => 'required',
                'fcmToken' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $liveUser = array(
                'userId'=>$id,
                'fcmToken'=>$req->fcmToken,
                'channelName'=>$req->channelName
            );
            DB::table('liveuser')->insert($liveUser);
            return response()->json([
                'message' => 'LiveUser add sucessfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getLiveUser(request $req)
     {
        try {

            $data = $req->only(
                'channelName',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'channelName' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $liveUser = Db::table('liveuser')
            ->join('users', 'users.id', '=', 'liveuser.userId')
            ->where('channelName', '=', $req->channelName)
            ->select('liveuser.*', 'users.name as userName', 'users.profile')
            ->get();
            return response()->json([
                'recordList'=>$liveUser,
                'message' => 'LiveUser get successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function deleteLiveUser(request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $liveUser =DB::table('liveuser')
            ->where('userId', '=', $id)
            ->delete();
            return response()->json([
                'recordList'=>$liveUser,
                'message' => 'LiveUser delete successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
