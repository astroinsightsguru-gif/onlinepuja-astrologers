<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Ticket;
use App\services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\services\OneSignalService;

class TicketController extends Controller
{
    //add a ticket
    public function addTicket(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'helpSupportId',
                'subject',
                'description',
                // 'ticketNumber',
                'userId',
				'sender_type'
            );

            //Validate the data
            $validator = Validator::make($data, [
                'subject' => 'required',
                'description' => 'required',
                'userId' => 'required',
				'sender_type' => 'required|in:Astrologer,User',
				
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create ticket
            do {
                $randomTicketNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                // Check if the generated ticket number is unique
                $existingTicket = Ticket::where('ticketNumber', $randomTicketNumber)->first();
            } while ($existingTicket);

            $ticket = Ticket::create([
                'helpSupportId' => $req->helpSupportId,
                'subject' => $req->subject,
                'description' => $req->description,
                'ticketNumber' => $randomTicketNumber,
                'userId' => $req->userId,
                'createdBy' => $id,
                'modifiedBy' => $id,
				'sender_type' => $req->sender_type,
                'ticketStatus' => 'WAITING',
                'chatId' => null,
            ]);


            $chat = $ticket->id . '_' . $req->userId;
            $chatData = array(
                'chatId' => $chat,
            );
            $ticket->update($chatData);
            return response()->json([
                'message' => 'Ticket add sucessfully',
                'recordList' => $ticket,
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

    //Update ticket
    public function updateTicket(Request $req, $id)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $req->validate = ([
                'helpSupportQuestionId',
                'subject',
                'description',
                'ticketNumber',
                'userId',
            ]);

            $ticket = Ticket::find($id);
            if ($ticket) {
                $ticket->helpSupportQuestionId = $req->helpSupportQuestionId;
                $ticket->subject = $req->subject;
                $ticket->description = $req->description;
                $ticket->ticketNumber = $ticket->ticketNumber;
                $ticket->userId = $req->userId;
                $ticket->update();
                return response()->json([
                    'message' => 'Ticket update sucessfully',
                    'recordList' => $ticket,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Ticket is not found',
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

    //Get a ticket
    public function getTicket(Request $req)
    {
        try {
            if($req->sender_type == 'User'){
                $ticket = DB::Table('tickets')
                    ->where('sender_type','User')
                    ->join('users', 'users.id', '=', 'tickets.userId')
                    ->join('help_supports', 'help_supports.id', '=', 'tickets.helpSupportId');
                if ($req->userId) {
                    $ticket = $ticket->where('userId', '=', $req->userId);
                }
                $ticketCount = $ticket->count();
                $ticket = $ticket->select('tickets.*', 'users.name as userName', 'help_supports.name');
            }else{
                $ticket = DB::Table('tickets')
                    ->where('sender_type','Astrologer')
                    ->join('users', 'users.id', '=', 'tickets.userId');
                if ($req->userId) {
                    $ticket = $ticket->where('userId', '=', $req->userId);
                }
                $ticketCount = $ticket->count();
                $ticket = $ticket->select('tickets.*', 'users.name as userName');

            }

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $ticket = $ticket->skip($req->startIndex);
                $ticket = $ticket->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $ticket->get(),
                'status' => 200,
                'totalRecords' => $ticketCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    //Delete all ticket
    public function deleteAllTicket()
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $ticket = Ticket::query()
                ->where('userId', '=', $id)->delete();

            return response()->json([
                'ticket' => $ticket,
                'message' => 'All tickets are deleted.',
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

    public function addTicketReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $ticketReview = array(
                'review' => $req->review,
                'rating' => $req->rating,
                'userId' => $id,
                'ticketId' => $req->ticketId,
            );
            DB::table('ticketreview')->insert($ticketReview);
            return response()->json([
                'message' => 'review add successfully',
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

    public function getTicketReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $ticketReview = DB::table('ticketreview')
                ->where('userId', '=', $id)
                ->where('ticketId', '=', $req->ticketId)
                ->get();
            return response()->json([
                'recordList' => $ticketReview,
                'message' => 'review get successfully',
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

    public function updateTicketReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $ticketReview = array(
                'rating' => $req->rating,
                'review' => $req->review,
            );
            DB::Table('ticketreview')
                ->where('id', '=', $req->id)
                ->update($ticketReview);
            return response()->json([
                'message' => 'review update successfully',
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

    public function closeTicket(Request $req)
    {
        try {
            $ticket = array(
                'ticketStatus' => 'CLOSED',
            );
            DB::table('tickets')
                ->where('id', '=', $req->id)
                ->update($ticket);
            $userDeviceDetail = DB::table('user_device_details as ud')
                ->join('tickets', 'tickets.userId', '=', 'ud.userId')
                ->where('tickets.id', '=', $req->id)
                ->select('ud.*')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {


                  // One signal FOr notification send
                  $oneSignalService = new OneSignalService();
                //   $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                  $notification = [
                    'title' => 'Customer support status update',
                        'body' => ['description' => 'Customer support status update', 'status' => 'CLOSED', 'icon' => 'public/notification-icon/support-ticket.png',],
                  ];
                  // Send the push notification using the OneSignalService
                  $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
            }
            return response()->json([
                'message' => 'ticket update successfully',
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

    public function deleteTicket(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            DB::Table('tickets')
                ->where('id', '=', $req->id)
                ->delete();
            return response()->json([
                'message' => 'Ticket Deleted sucessfully',
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

    public function restartTicket(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $ticket = array(
                'ticketStatus' => 'WAITING',
            );
            DB::table('tickets')
                ->where('id', '=', $req->id)
                ->update($ticket);
            return response()->json([
                'message' => 'ticket restart successfully',
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

    public function pauseTicket(Request $req)
    {
        try {

            $ticket = array(
                'ticketStatus' => 'PAUSED',
            );
            DB::table('tickets')
                ->where('id', '=', $req->id)
                ->update($ticket);
            $userDeviceDetail = DB::table('user_device_details')
                ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                ->where('tickets.id', '=', $req->id)
                ->select('user_device_details.*')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {

                  // One signal FOr notification send
                  $oneSignalService = new OneSignalService();
                //   $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                  $notification = [
                    'title' => 'Notification for customer support status updates',
                        'body' => ['description' => 'Notification for customer support status updates', 'status' => 'PAUSED','icon' => 'public/notification-icon/support-ticket.png'],
                  ];
                  // Send the push notification using the OneSignalService
                  $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
            }
            return response()->json([
                'message' => 'ticket update successfully',
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

    public function checkOpenTicket(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $status = array(
                'PAUSED', 'OPEN', 'WAITING',
            );
            $ticket = DB::table('tickets')
                ->where('userId', '=', $id)
                ->whereIn('ticketStatus', $status)
                ->get();
            $isOpened = false;
            if ($ticket && count($ticket) > 0) {
                $isOpened = true;
            }

            return response()->json([
                'recordList' => $isOpened,
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

    public function updateTicketStatus(Request $req)
    {
        try {

            $data = array(
                'ticketStatus' => 'OPEN',
            );
            DB::table('tickets')->where('id', '=', $req->ticketId)->update($data);
            $userDeviceDetail = DB::table('user_device_details')
                ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                ->where('tickets.id', '=', $req->ticketId)
                ->select('user_device_details.*')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {


                  // One signal FOr notification send
                  $oneSignalService = new OneSignalService();
                //   $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                  $notification = [
                    'title' => 'Notification for customer support status update',
                        'body' => ['description' => 'Notification for customer support status update', 'status' => 'OPEN','icon' => 'public/notification-icon/support-ticket.png'],
                  ];
                  // Send the push notification using the OneSignalService
                  $response = $oneSignalService->sendNotification($userPlayerIds, $notification);


            }
            return response()->json([
                'message' => 'ticket status update successfully',
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
