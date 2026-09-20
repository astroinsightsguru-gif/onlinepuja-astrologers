<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserNotificationController extends Controller
{
    //Add user notification
    public function addUserNotification(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'userId',
                'title',
                'description',
                'notificationId',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'userId' => 'required',
                'title' => 'required',
                'description' => 'required',
                'notificationId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create a new user notification
            $userNotification = UserNotification::create([
                'userId' => $req->userId,
                'title' => $req->title,
                'description' => $req->description,
                'notificationId' => $req->notificationId,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            return response()->json([
                'message' => 'User notification add sucessfully',
                'recordList' => $userNotification,
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

    //Get all the user Notifications
    public function getUserNotification(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }


            // $userNotification = UserNotification::query();
            // $userNotification = $userNotification->where('userId', '=', $id)->OrderBy('id', 'DESC');

            $notifications = DB::table('user_notifications')
                ->leftJoin('chatrequest', 'chatrequest.id', '=', 'user_notifications.chatRequestId')
                ->leftJoin('callrequest', 'callrequest.id', '=', 'user_notifications.callRequestId')
                ->leftJoin('astrologers', function ($join) {
                    $join->on('astrologers.id', '=', 'chatrequest.astrologerId')
                        ->orOn('astrologers.id', '=', 'callrequest.astrologerId');
                })
                ->leftJoin('user_device_details', 'user_device_details.userId', '=', 'astrologers.userId')
                ->leftJoin('user_wallets', 'user_wallets.userId', '=', 'user_notifications.userId')
                ->where('user_notifications.userId', '=', $id)
                ->select(
                    'user_notifications.*',
                    'user_device_details.fcmToken',
                    //'user_notifications.id as notificationId',
                    'user_notifications.notification_type as notification_type',
                    'astrologers.name as astrologerName',
                    'astrologers.charge',
                    'astrologers.videoCallRate',
                    'user_wallets.amount as walletamount',
                    'astrologers.id as astrologerId',
                    'astrologers.profileImage as astroprofileImage',
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.id, NULL) as chatId'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.id, NULL) as callId'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.chatId, NULL) as firebaseChatId'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.channelName, NULL) as channelName'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.totalMin, NULL) as totalMin'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.totalMin, NULL) as totalMin'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.call_type, NULL) as call_type'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.token, NULL) as token'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.callStatus, NULL) as callStatus'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.chatStatus, NULL) as chatStatus'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.call_duration, NULL) as call_duration'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.chat_duration, NULL) as chat_duration'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.call_method, NULL) as call_method'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.IsSchedule, NULL) as IsSchedule'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.schedule_date, NULL) as schedule_date'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.schedule_time, NULL) as schedule_time'),
                )
                ->orderBy('user_notifications.id', 'DESC')
                ->get();



            if ($s = $req->input(key: 's')) {
                $notifications->whereRaw(sql: "title LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $notifications,
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

    //Update user notification
    public function updateUserNotification(Request $req, $id)
    {
        try {
            $req->validate = ([
                'userId' => 'required',
                'title' => 'required',
                'description' => 'required',
                'notificationId' => 'required',
            ]);

            $userNotification = UserNotification::find($id);
            if ($userNotification) {
                $userNotification->userId = $req->userId;
                $userNotification->title = $req->title;
                $userNotification->description = $req->description;
                $userNotification->notificationId = $req->notificationId;
                $userNotification->update();
                return response()->json([
                    'message' => 'User notification update sucessfully',
                    'recordList' => $userNotification,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User notification is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Active/InActive User notification
    public function activeUserNotification(Request $req, $id)
    {
        try {
            $userNotification = UserNotification::find($id);
            if ($userNotification) {
                $userNotification->isActive = $req->isActive;
                $userNotification->update();
                return response()->json([
                    'message' => 'User notification status change sucessfully',
                    'recordList' => $userNotification,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User notification status not change.',
                    'status' => 400,
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
