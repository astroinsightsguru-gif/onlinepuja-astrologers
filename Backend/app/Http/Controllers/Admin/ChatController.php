<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\CallRequest;
use App\Models\UserModel\ChatRequest;
use App\services\OneSignalService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getFireStoredata(Request $req)
    {
        try {
            $user = DB::table('tickets')
                ->join('users', 'users.id', '=', 'tickets.userId')
                ->select('users.name as userName', 'users.profile', 'tickets.userId', 'tickets.ticketStatus')
                ->where('tickets.id', '=', $req->id)
                ->get();

            $chatId = $req->id . '_' . $user[0]->userId;
            $data = array(
                'chatId' => $chatId,
                'userName' => $user[0]->userName,
                'userProfile' => $user[0]->profile,
                'userId' => $user[0]->userId,
                'ticketId' => $req->id,
                'ticketStatus' => $user[0]->ticketStatus,
            );

            $firebaseProjectId = DB::table('systemflag')->where('name', 'firebaseprojectId')->select('value')->first();
            // Use Guzzle or any HTTP client to make requests to Firestore REST API
            $httpClient = new \GuzzleHttp\Client();
            $response = $httpClient->get('https://firestore.googleapis.com/v1/projects/' . $firebaseProjectId->value . "/databases/(default)/documents/supportChat/{$chatId}/userschat/{$req->id}/messages");

            // Check if 'documents' key exists in the API response
            $apiResponse = json_decode($response->getBody()->getContents(), true);

            if (isset($apiResponse['documents'])) {
                $messages = $apiResponse['documents'];

                usort($messages, function ($a, $b) {
                    return strtotime($a['createTime']) - strtotime($b['createTime']);
                });

                return view('pages.chat', compact('messages', 'data'));
            } else {
                // Handle the case when there are no documents
                $messages = [];
                return view('pages.chat', compact('messages', 'data'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function createChat(Request $req)
    {
        try {
            $firebaseProjectId = DB::table('systemflag')->where('name', 'firebaseprojectId')->select('value')->first();
            $apiEndpoint = 'https://firestore.googleapis.com/v1/projects/' . $firebaseProjectId->value . '/databases/(default)/documents/';

            $postData = [
                'fields' => [
                    'message' => ['stringValue' => $req->message],
                    'createdAt' => ['timestampValue' => Carbon::now()->toIso8601String()],
                    'updatedAt' => ['timestampValue' => Carbon::now()->toIso8601String()],
                    'userId1' => ['integerValue' => $req->ticketId],
                    'userId2' => ['integerValue' => $req->senderId],
                    'status' => ['stringValue' => 'OPEN'],
                ],
            ];

            $client = new Client();

            if ($req->messageCount == 2 || $req->ticketStatus == 'WAITING') {
                $data = ['ticketStatus' => 'OPEN'];
                DB::table('tickets')->where('id', '=', $req->ticketId)->update($data);

                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $req->ticketId)
                    ->select('user_device_details.*')
                    ->get();

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    // One signal FOr notification send
                    $oneSignalService = new OneSignalService();
                    //  $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => 'Notification for customer support status update',
                        'body' => ['description' => 'Notification for customer support status update', 'status' => 'OPEN'],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

                    $notification = array(
                        'userId' => $userDeviceDetail[0]->userId,
                        'title' => 'Notification for customer support status update',
                        // 'description' => 'It seems like you have missed/rejected your chat from ' . $astrologer[0]->name . ' .You may initiate it again from the app.',
                        'description' => 'Notification for customer support status update',
                        'notificationId' => null,
                        'createdBy' => $userDeviceDetail[0]->userId,
                        'modifiedBy' => $userDeviceDetail[0]->userId,
                        'notification_type' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    );
                    DB::table('user_notifications')->insert($notification);
                }
            } else {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $req->ticketId)
                    ->select('user_device_details.*')
                    ->get();

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    // One signal FOr notification send
                    $oneSignalService = new OneSignalService();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => 'Receive Message',
                        'body' => ['description' => 'Receive Message'],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
                }

                $notification = array(
                    'userId' => $userDeviceDetail[0]->userId,
                    'title' => 'Receive Message',
                    // 'description' => 'It seems like you have missed/rejected your chat from ' . $astrologer[0]->name . ' .You may initiate it again from the app.',
                    'description' => 'Receive Message',
                    'notificationId' => null,
                    'createdBy' => $userDeviceDetail[0]->userId,
                    'modifiedBy' => $userDeviceDetail[0]->userId,
                    'notification_type' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                DB::table('user_notifications')->insert($notification);
            }

            $response = $client->post($apiEndpoint . 'supportChat/' . $req->chatId . '/userschat/' . $req->senderId . '/messages', [
                'json' => $postData,
            ]);

            $response = $client->post($apiEndpoint . 'supportChat/' . $req->chatId . '/userschat/' . $req->ticketId . '/messages', [
                'json' => $postData,
            ]);

            return response()->json([
                'success' => ['Send Message Successfully'],
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function Userchats(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $completedChats = DB::table('chatrequest')
                    ->select('users.name as Username', 'astrologers.name as astrologerName', 'chatrequest.chatId')
                    ->join('astrologers', 'chatrequest.astrologerId', '=', 'astrologers.id')
                    ->join('users', 'users.id', '=', 'chatrequest.userId')
                    ->where('chatrequest.chatStatus', 'Completed')
                    ->groupBy('chatrequest.userId')
                    ->orderBy('chatrequest.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();
                $totalRecords = $completedChats->count();

                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.user-chats', compact('completedChats', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getUserChatdata(Request $req, $id)
    {
        try {
            $user = DB::table('chatrequest')
                ->join('users', 'users.id', '=', 'chatrequest.userId')
                ->join('astrologers', 'chatrequest.astrologerId', '=', 'astrologers.id')
                ->select('users.name as userName', 'users.profile as userProfile', 'astrologers.name as astrologerName', 'astrologers.profileImage as astroImg', 'astrologers.id as astrologerId', 'users.id as userId')
                ->where('chatrequest.chatId', '=', $id)
                ->first();

            $chatId = $user->astrologerId . '_' . $user->userId;
            $firebaseProjectId = DB::table('systemflag')->where('name', 'firebaseprojectId')->select('value')->first();

            $httpClient = new \GuzzleHttp\Client();
            $response = $httpClient->get('https://firestore.googleapis.com/v1/projects/' . $firebaseProjectId->value . "/databases/(default)/documents/chats/{$chatId}/userschat/{$user->astrologerId}/messages");

            // Check if 'documents' key exists in the API response
            $apiResponse = json_decode($response->getBody()->getContents(), true);

            if (isset($apiResponse['documents'])) {
                $messages = $apiResponse['documents'];

                usort($messages, function ($a, $b) {
                    return strtotime($a['createTime']) - strtotime($b['createTime']);
                });

                return view('pages.user-view-chat', compact('messages', 'user'));
            } else {
                // Handle the case when there are no documents
                $messages = [];
                return view('pages.user-view-chat', compact('messages', 'user'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function CallChatDelete()
    {
        try {
            $pendingChatRequests = ChatRequest::where('chatStatus', 'Pending')->whereNotNull('validated_till')->where('validated_till', '<', Carbon::now())->delete();
            $pendingCallRequests = CallRequest::where('callStatus', 'Pending')->whereNotNull('validated_till')->where('validated_till', '<', Carbon::now())->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Exception-deleteChat:' . $e->getMessage());
            return false;
        }
    }
}
