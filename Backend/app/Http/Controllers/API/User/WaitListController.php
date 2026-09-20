<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\WaitList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\services\FCMService;
use App\services\OneSignalService;

class WaitListController extends Controller
{
    public function addWaitList(Request $req)
    {
        try {


            $waitList = WaitList::create([
                'userName' => $req->userName,
                'profile' => $req->profile,
                'time' => $req->time,
                'channelName' => $req->channelName,
                'userId' => $req->userId,
                'requestType' => $req->requestType,
                'userFcmToken' => $req->userFcmToken,
                'status' => $req->status,
                'astrologerId' => $req->astrologerId,
            ]);
             $userDeviceDetail = DB::table('user_device_details')
                ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('user_device_details.*','astrologers.userId as astrologerUserId', 'astrologers.name')
                ->get();


                // by me
                $user = DB::table('users')->where('users.id', '=', $req->userId)
                ->join('user_device_details', 'user_device_details.userId', 'users.id')
                ->select('users.name','profile','user_device_details.fcmToken')
                ->get();

            if ($req->requestType == 'Chat') {

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {

                     $oneSignalService = new OneSignalService();
                    // $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => 'Receive Chat',
                            'body' => [
                                "notificationType" => 10,
                                'call_duration' => 100,
                                'description' => 'Hey '.$userDeviceDetail[0]->name.', You received a chat request from ' . $user[0]->name,
                                'icon' => 'public/notification-icon/chat.png',
                            ],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
                }
            }
            if ($req->requestType == 'Audio') {
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {


                           // One signal FOr notification send
                 $oneSignalService = new OneSignalService();
                //  $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                 $notification = [
                    'title' => 'Receive Audio Call',
                            'body' => [
                                "notificationType" => 11,
                                'call_duration' => 100,
                                'description' => 'Hey '.$userDeviceDetail[0]->name.', You received a audio call request from ' . $user[0]->name,
                                'icon' => 'public/notification-icon/telephone-call.png',
                            ],
                 ];
                 // Send the push notification using the OneSignalService
                 $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

                }
            }
            if ($req->requestType == 'Video') {
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {


                    $oneSignalService = new OneSignalService();
                    // $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => 'Receive Video Call',
                        'body' => [
                            'call_duration' => 100,
                            "notificationType" => 12,
                            'description' => 'Hey '.$userDeviceDetail[0]->name.', You received a video call request from ' . $user[0]->name,
                            'icon' => 'public/notification-icon/video-camera.png',
                        ],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
                }
            }
            return response()->json([
                'message' => 'Add to waitlist successfully',
                'recordList' => $waitList,
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

    public function getWaitList(Request $req)
    {
        try {

            $waitList = DB::table('waitlist')
                ->where('channelName', '=', $req->channelName)
                ->join('user_device_details','user_device_details.userId','waitlist.userId')
                ->select('waitlist.*','user_device_details.subscription_id')
                ->get();
            return response()->json([
                'message' => 'Get waitlist successfully',
                'recordList' => $waitList,
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

    public function deleteFromWaitList(Request $req)
    {
        try {

            $waitList = WaitList::find($req->id);
            $waitList->delete();
            return response()->json([
                'message' => 'Delete waitlist successfully',
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

    public function editWaitList(Request $req)
    {
        try {
            $waitList = WaitList::find($req->id);
            if ($waitList) {
                $waitList->status = $req->status;
                $waitList->update();
            }
            return response()->json([
                'message' => 'Get waitlist successfully',
                'recordList' => $waitList,
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
}
