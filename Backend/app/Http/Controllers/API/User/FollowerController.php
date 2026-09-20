<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerFollowers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\services\FCMService;
use App\services\OneSignalService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FollowerController extends Controller
{
    //Add Followers
    public function addFollowing(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'astrologerId',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $astrologerFollower = AstrologerFollowers::query()->where('userId', $id)->where('astrologerId', $req->astrologerId)->get();
            //Create a astrologer follower
            if (!($astrologerFollower && count($astrologerFollower) > 0)) {
                $follower = AstrologerFollowers::create([
                    'astrologerId' => $req->astrologerId,
                    'userId' => $id,
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                ]);
            } else {
                $follower = $astrologerFollower[0];
            }


             $userDeviceDetail = DB::table('user_device_details')
            ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
            ->WHERE('astrologers.id', '=', $req->astrologerId)
            ->SELECT('user_device_details.*','astrologers.userId as astrologerUserId', 'astrologers.name')
            ->get();

             $user = DB::table('users')->where('users.id', '=', $id)
            ->join('user_device_details', 'user_device_details.userId', 'users.id')
            ->select('users.id','users.name','users.profile','user_device_details.fcmToken')
            ->get();


             if ($userDeviceDetail && count($userDeviceDetail) > 0) {



                 // One signal FOr notification send
                 $oneSignalService = new OneSignalService();
                //  $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                 $notification = [
                    'title' => 'Receive Followers',
						'body' => [
                             'description' => 'Hey '.$userDeviceDetail[0]->name.', '. ($user[0]->name ?: 'User').' Started Following You',
                        ],
                 ];
                 // Send the push notification using the OneSignalService
                 $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

				 $notification = array(
                    'userId' => $userDeviceDetail[0]->astrologerUserId,
                    'title' => 'Hey '.$userDeviceDetail[0]->name.', '. ($user[0]->name ?: 'User').' Started Following You',
                    'description' => 'Hey '.$userDeviceDetail[0]->name.', '. ($user[0]->name ?: 'User').' Started Following You',
                    'notificationId' => null,
                    'createdBy' => $userDeviceDetail[0]->astrologerUserId,
                    'modifiedBy' => $userDeviceDetail[0]->astrologerUserId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                );
                DB::table('user_notifications')->insert($notification);
            }



            return response()->json([
                'message' => 'Astrologer follower add sucessfully',
                'recordList' => $follower,
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

    //Get all follower
    public function getFollowing(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            

            $follower = DB::table('astrologer_followers')
                ->join('astrologers', 'astrologer_followers.astrologerId', '=', 'astrologers.id')
                ->where('astrologer_followers.userId', '=', $id)
                ->select('astrologers.*');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $follower = $follower->skip($req->startIndex);
                $follower = $follower->take($req->fetchRecord);
            }
            $follower = $follower->get();
            
                 $isFreeAvailable = true;
            $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
            if ($isFreeChat->value == 1) {
                if ($id) {
                    $isChatRequest = DB::table('chatrequest')->where('userId', $id)->where('chatStatus', '=', 'Completed')->first();
                    $isCallRequest = DB::table('callrequest')->where('userId', $id)->where('callStatus', '=', 'Completed')->first();
                    if ($isChatRequest || $isCallRequest) {
                        $isFreeAvailable = false;
                    } else {
                        $isFreeAvailable = true;
                    }
                }
            } else {
                $isFreeAvailable = false;
            }
            if ($follower && count($follower) > 0) {
                foreach ($follower as $follow) {
                    $follow->isFreeAvailable = $isFreeAvailable;
                    $languages = DB::table('languages')
                        ->whereIn('id', explode(',', $follow->languageKnown))
                        ->select('languageName')
                        ->get();

                    $allSkill = DB::table('skills')
                        ->whereIn('id', explode(',', $follow->allSkill))
                        ->get('name');
                    $languageKnown = $languages->pluck('languageName')->all();
                    $allSkill = $allSkill->pluck('name')->all();
                    $follow->languageKnown = implode(",", $languageKnown);
                    $follow->allSkill = implode(",", $allSkill);
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', explode(',', $follow->primarySkill))
                        ->get('name');
                      $primary = $primarySkill->pluck('name')->all();
                       $follow->primarySkill = implode(",", $primary);
                    $review = DB::table('user_reviews')
                ->where('astrologerId', '=', $follow->id)
                ->get();
                    
                    $follow->rating = 0;
                    if ($review && count($review) > 0) {
                        $avgRating = 0;
                        foreach ($review as $re) {
                            $avgRating += $re->rating;
                        }
                        $avgRating = $avgRating / count($review);
                        $follow->rating = $avgRating;
                    }
                }
            }
            $followerCount = AstrologerFollowers::query()
                ->where('userId', '=', $id)
                ->get()->count();

            return response()->json([
                'recordList' => $follower,
                'status' => 200,
                'totalFollower' => $followerCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Update astromall product details
    public function updateFollowing(Request $req)
    {
        try {
            $req->validate = ([
                'astrologerId',
            ]);
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $follower = DB::table('astrologer_followers')
                ->where('userId', '=', $id)
                ->where('astrologerId', '=', $req->astrologerId);
            if ($follower) {
                $follower->delete();
                return response()->json([
                    'message' => 'Follower delete Sucessfully',
                    'status' => 200,

                ], 200);
            } else {
                return response()->json([
                    'message' => 'No Follower Found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getFollower(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $follower = DB::table('astrologer_followers')
                ->join('users', 'users.id', '=', 'astrologer_followers.userId')
                ->where('astrologerId', '=', $req->astrologerId)
                ->select('users.*');
            // ->get();
            return response()->json([
                'recordList' => $follower->get(),
                'status' => 200,
                'message' => "Get Follower Successfully",
                'totalCount' => $follower->count(),
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
