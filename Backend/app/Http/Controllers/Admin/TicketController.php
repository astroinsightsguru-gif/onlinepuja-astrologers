<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\OneSignalService;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;
if (!defined('NOTIFICATIONDESC')) define('NOTIFICATIONDESC', 'Notification for customer support status update');

class TicketController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getTicket(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $ticket = DB::Table('tickets')
                    ->where('sender_type', 'User')
                    ->join('users', 'users.id', '=', 'tickets.userId')
                    ->join('help_supports', 'help_supports.id', '=', 'tickets.helpSupportId');
                $ticketCount = $ticket->count();
                $ticket = $ticket->select('tickets.*', 'users.name as userName', 'help_supports.name', 'users.contactNo as contactNo');
                $ticket = $ticket->skip($paginationStart);
                $ticket = $ticket->take($this->limit);
                $countQuery = clone $ticket;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $ticket->whereBetween('tickets.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('tickets.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $ticket->where('tickets.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('tickets.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $ticket->where('tickets.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('tickets.created_at', '<=', $to_date . ' 23:59:59');
                }
                $tickit = $ticket->orderBy('id', 'DESC')->get();
                if ($tickit && count($tickit) > 0) {
                    for ($i = 0; $i < count($tickit); $i++) {
                        $ticketReview = DB::table('ticketreview')->where('ticketId', '=', $tickit[$i]->id)->get();
                        $tickit[$i]->ticketReview = $ticketReview;
                    }
                }
                $totalPages = ceil($ticketCount / $this->limit);
                $totalRecords = $ticketCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.ticket', compact('tickit', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getAstrologerTicket(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $ticket = DB::Table('tickets')
                    ->where('sender_type', 'Astrologer')
                    ->join('users', 'users.id', '=', 'tickets.userId');

                $ticketCount = $ticket->count();
                $ticket = $ticket->select('tickets.*', 'users.name as userName', 'users.contactNo as contactNo');
                $ticket = $ticket->skip($paginationStart);
                $ticket = $ticket->take($this->limit);
                $countQuery = clone $ticket;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $ticket->whereBetween('tickets.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('tickets.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $ticket->where('tickets.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('tickets.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $ticket->where('tickets.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('tickets.created_at', '<=', $to_date . ' 23:59:59');
                }
                $tickit = $ticket->orderBy('id', 'DESC')->get();
                if ($tickit && count($tickit) > 0) {
                    for ($i = 0; $i < count($tickit); $i++) {
                        $ticketReview = DB::table('ticketreview')->where('ticketId', '=', $tickit[$i]->id)->get();
                        $tickit[$i]->ticketReview = $ticketReview;
                    }
                }
                $totalPages = ceil($ticketCount / $this->limit);
                $totalRecords = $ticketCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.astrologerTickets.index', compact('tickit', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function closeTicket(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $ticket = array(
                    'ticketStatus' => 'CLOSED',
                );
                DB::table('tickets')
                    ->where('id', '=', $request->ticket_id)
                    ->update($ticket);

                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $request->ticket_id)
                    ->select('user_device_details.*')
                    ->get();

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    // One signal FOr notification send
                    $oneSignalService = new OneSignalService();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => NOTIFICATIONDESC,
                        'body' => ['description' => NOTIFICATIONDESC, 'status' => 'CLOSED', 'icon' => 'public/notification-icon/support-ticket.png'],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
                }

                $ticket = DB::table('tickets')
                    ->where('id', '=', $request->ticket_id)
                    ->get();

                // Firestore REST API Integration
                $projectId = systemflag('firebaseprojectId');
                $baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents";

                $postData = [
                    'fields' => [
                        'message' => ['stringValue' => 'We hope that your issue has been resolved from our end. In case you are still facing any concerns then you can connect with us anytime. We will always love to help you!'],
                        'createdAt' => ['timestampValue' => Carbon::now()->toIso8601String()],
                        'updatedAt' => ['timestampValue' => Carbon::now()->toIso8601String()],
                        'userId1' => ['stringValue' => $request->ticket_id],
                        'userId2' => ['stringValue' => $ticket[0]->userId],
                        'status' => ['stringValue' => 'CLOSED'],
                    ],
                ];

                $response = Http::post("{$baseUrl}/supportChat/{$ticket[0]->chatId}/userschat/{$ticket[0]->userId}/messages", [
                    'fields' => $postData['fields'],
                ]);

                $secondMsg = [
                    'message' => 'Thank you for contacting Us! Have a great day ahead!',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'userId1' => $ticket[0]->userId,
                    'userId2' => $request->ticket_id,
                    'status' => 'CLOSED',
                ];

                // Firestore REST API Integration for the second message
                $response = Http::post("{$baseUrl}/supportChat/{$ticket[0]->chatId}/userschat/{$request->ticket_id}/messages", [
                    'fields' => $secondMsg,
                ]);

                return response()->json([
                    'success' => ['Send Notification Successfully'],
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function pauseTicket(Request $request)
    {
        try {
            $ticket = array(
                'ticketStatus' => 'PAUSED',
            );
            DB::table('tickets')
                ->where('id', '=', $request->ticket_id)
                ->update($ticket);
            $userDeviceDetail = DB::table('user_device_details')
                ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                ->where('tickets.id', '=', $request->ticket_id)
                ->select('user_device_details.*')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                // One signal FOr notification send
                $oneSignalService = new OneSignalService();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                $notification = [
                    'title' => NOTIFICATIONDESC,
                    'body' => ['description' => NOTIFICATIONDESC, 'status' => 'PAUSED', 'icon' => 'public/notification-icon/support-ticket.png'],
                ];
                // Send the push notification using the OneSignalService
                $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
            }
            $ticket = DB::table('tickets')
                ->where('id', '=', $request->ticket_id)
                ->get();
            $db = new FirestoreClient([
                'projectId' => systemflag('firebaseprojectId'),
            ]);
            $postData = array(
                'message' => "Seems like you are not active as we have not received a response from you,don't worry you can continue your chat from where you have left by resuming the ticket.",
                'createdAt' => Carbon::now(),
                'updatedAt' => Carbon::now(),
                'userId1' => $request->ticket_id,
                'userId2' => $ticket[0]->userId,
                'status' => 'PAUSED',
            );
            $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($ticket[0]->userId)->collection('messages')->add($postData);
            $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($request->ticket_id)->collection('messages')->add($postData);
            return response()->json([
                'success' => ['Send Notification Successfully'],
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
