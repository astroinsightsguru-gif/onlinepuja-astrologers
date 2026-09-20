<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\services\FCMService;
use App\services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssistantChatController extends Controller
{
    public function getAssistantChat(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $chatData = DB::table('assistantchat')
                ->where('senderId', '=', $req->senderId)
                ->where('receiverId', '=', $req->receiverId)
                ->get();
            if (!($chatData && count($chatData) > 0)) {
                $partnerChatData = DB::table('assistantchat')
                    ->where('senderId', '=', $req->receiverId)
                    ->where('receiverId', '=', $req->senderId)
                    ->get();
                if (!($partnerChatData && count($partnerChatData) > 0)) {
                    $chatId = $req->senderId . '_' . $req->receiverId;

                    $chatData = array(
                        'senderId' => $req->senderId,
                        'receiverId' => $req->receiverId,
                        'astrologerId' => $req->astrologerId,
                        'chatId' => $chatId,
                        'customerId' => $req->customerId,
                    );
                    DB::table('assistantchat')->insert($chatData);
                } else {
                    $chatId = $partnerChatData[0]->chatId;
                }
            } else {
                $chatId = $chatData[0]->chatId;
            }
            $userDeviceDetail = DB::table('user_device_details')
                ->join('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('user_device_details.*', 'astrologers.userId as astrologerUserId')
                ->get();

            $customer = DB::Table('users')
                ->where('id', '=', $req->customerId)
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {

                 // One signal FOr notification send
                 $oneSignalService = new OneSignalService();
                //  $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                 $notification = [
                    'title' => 'Receive Assistant Chat Request',
                        'body' => [
                            "firebaseChatId" => $chatId,
                            'customerId' => $req->customerId,
                            'customerName' => $customer[0]->name,
                            'description' => '',
                        ],
                 ];
                 // Send the push notification using the OneSignalService
                 $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
                $notification = array(
                    'userId' => $userDeviceDetail[0]->astrologerUserId,
                    'title' => 'Get Assistant Chat Request From ' . $customer[0]->name,
                    'description' => '',
                    'notificationId' => null,
                    'createdBy' => $userDeviceDetail[0]->astrologerUserId,
                    'modifiedBy' => $userDeviceDetail[0]->astrologerUserId,
                    'notification_type' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                DB::table('user_notifications')->insert($notification);
            }
            return response()->json([
                "status" => 200,
                "recordList" => $chatId,
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAssistantChatHistory(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $assistantChat = DB::Table('assistantchat')
                ->join('astrologers', 'astrologers.id', '=', 'assistantchat.astrologerId')
                ->where('customerId', '=', $id)
                ->select('assistantchat.*', 'astrologers.profileImage', 'astrologers.name as astrologerName')
                ->get();

            return response()->json([
                'recordList' => $assistantChat,
                'status' => 200,
                'message' => 'Get Assistant Successfully',
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function customerPaidSession(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $assistantChat = DB::table('chatrequest')
                ->where('astrologerId', '=', $req->astrologerId)
                ->where('userId', '=', $id)
                ->where('chatStatus', '=', 'Completed')
                ->where('deduction', '>', 0)
                ->limit(1)
                ->get();

            if (!($assistantChat && count($assistantChat) > 0)) {
                $callAssistantChat = DB::table('callrequest')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->where('userId', '=', $id)
                    ->where('callStatus', '=', 'Completed')
                    ->where('deduction', '>', 0)
                    ->limit(1)
                    ->get();
                if ($callAssistantChat && count($callAssistantChat) > 0) {
                    $isAvailable = true;
                } else {
                    $isAvailable = false;
                }
            } else {
                $isAvailable = true;
            }
            return response()->json([
                'status' => 200,
                'isAvailable' => $isAvailable,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAssistantChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $assistantChatRequest = DB::Table('assistantchat')
                ->join('users', 'users.id', '=', 'assistantchat.customerId')
                ->select('assistantchat.*', 'users.name as userName', 'users.profile', 'users.contactNo as contactNo')
                ->where('astrologerId', '=', $req->astrologerId)->get();
            return response()->json([
                "status" => 200,
                "recordList" => $assistantChatRequest,
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function deleteAssistantChat(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            DB::Table('assistantchat')->where('id', '=', $req->id)->delete();
            return response()->json([
                "status" => 200,
                "message" => 'delete Assistant Chat Successfully',
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
