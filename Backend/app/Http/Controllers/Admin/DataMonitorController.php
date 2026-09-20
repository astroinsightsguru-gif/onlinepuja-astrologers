<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class DataMonitorController extends Controller
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

            // Use Guzzle or any HTTP client to make requests to Firestore REST API
            $fireBaseProjectId = systemflag('firebaseProjectId');
            $httpClient = new \GuzzleHttp\Client();
            $response = $httpClient->get("https://firestore.googleapis.com/v1/projects/{$fireBaseProjectId}/databases/(default)/documents/supportChat/{$chatId}/userschat/{$req->id}/messages");

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
            return back()->with('error', $e->getMessage());
        }
    }

    public function chatsMonitoring(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $astrologers = DB::table('astrologers')
                    ->where('astrologers.isDelete', false)
                    ->where('astrologers.isVerified', true)
                    ->select('id', 'userId', 'name')
                    ->get();

                $users = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('users.isDelete', false)
                    ->where('user_roles.roleId', 3)
                    ->whereNot('users.contactNo', null)
                    ->select('users.id', 'users.name', 'users.contactNo')
                    ->orderByDesc('users.id')
                    ->get();

                // Start building the query
                $query = DB::table('chatrequest')
                    ->select(
                        'users.id as userId',
                        'users.name as Username',
                        'users.contactNo as contactNo',
                        'astrologers.userId as astroUserId',
                        'astrologers.name as astrologerName',
                        'chatrequest.chatId',
                        'chatrequest.created_at as created_at'
                    )
                    ->join('astrologers', 'chatrequest.astrologerId', '=', 'astrologers.id')
                    ->join('users', 'users.id', '=', 'chatrequest.userId')
                    ->where('chatrequest.chatStatus', 'Completed');

                // Apply filters based on request inputs
                if ($request->filled('astrologerId')) {
                    $query->where('chatrequest.astrologerId', $request->astrologerId);
                }

                if ($request->filled('userId')) {
                    $query->where('chatrequest.userId', $request->userId);
                }

                if ($request->filled('date')) {
                    $query->whereDate('chatrequest.created_at', $request->date);
                }

                // Execute the query with pagination
                $completedChats = $query
                    ->distinct()
                    ->groupBy('chatrequest.astrologerId', 'chatrequest.userId')
                    ->orderBy('chatrequest.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                $totalRecords = $query->count();
                // Count defaulter messages for each user
                $astroDefaulterCounts = DB::table('defaulter_messages')
                    ->select('user_id', DB::raw('count(*) as defaulter_count'))
                    ->where('type', 'astrologer')
                    ->groupBy('user_id')
                    ->pluck('defaulter_count', 'user_id')
                    ->toArray();

                $userDefaulterCounts = DB::table('defaulter_messages')
                    ->select('user_id', DB::raw('count(*) as defaulter_count'))
                    ->where('type', 'user')
                    ->groupBy('user_id')
                    ->pluck('defaulter_count', 'user_id')
                    ->toArray();

                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.data-monitor.chats-monitoring', compact('completedChats', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'userDefaulterCounts', 'astroDefaulterCounts', 'astrologers', 'users'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function userChatMonitoring(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $paginationStart = ($page - 1) * $this->limit;
        $type = 'Users';
        $userDefaulter = DB::table('defaulter_messages')
            ->join('users', 'users.id', '=', 'defaulter_messages.user_id')  // Join with the users table
            ->select(
                'users.id as user_id',
                'users.name',
                'users.contactNo',
                'users.email',
                DB::raw('count(defaulter_messages.id) as defaulter_count')  // Count messages
            )
            ->where('defaulter_messages.type', 'user')  // Filter by type
            ->groupBy('users.id', 'users.name', 'users.email')  // Group by user fields
            ->get();

        $totalRecords = $userDefaulter->count();

        $userDefaulterCounts = DB::table('defaulter_messages')
            ->select('user_id', DB::raw('count(*) as defaulter_count'))
            ->where('type', 'user')
            ->groupBy('user_id')
            ->pluck('defaulter_count', 'user_id')
            ->toArray();

        $totalPages = ceil($totalRecords / $this->limit);
        $page = min($page, $totalPages);
        $start = ($this->limit * ($page - 1)) + 1;
        $end = min($this->limit * $page, $totalRecords);
        return view('pages.data-monitor.user-data-monitoring', compact('type', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'userDefaulter', 'userDefaulterCounts'));
    }

    public function userDataMonitoringId(Request $request, $id)
    {
        $page = $request->page ? $request->page : 1;
        $paginationStart = ($page - 1) * $this->limit;
        $type = 'Users';
        $userDefaulter = DB::table('defaulter_messages')
            ->join('users as sender', 'sender.id', '=', 'defaulter_messages.user_id')  // Join for sender details
            ->join('users as receiver', 'receiver.id', '=', 'defaulter_messages.receiver_id')  // Join for receiver details
            ->select(
                'sender.id as sender_id',
                'sender.name as sender_name',
                'sender.contactNo as sender_contact',
                'sender.email as sender_email',
                'receiver.id as receiver_id',
                'receiver.name as receiver_name',
                'receiver.contactNo as receiver_contact',
                'receiver.email as receiver_email',
                'defaulter_messages.message as message',
                'defaulter_messages.created_at as date'
            )
            ->where('defaulter_messages.user_id', $id)  // Filter by type
            ->orderBy('defaulter_messages.id', 'desc')  // Group by user fields
            ->get();
        $totalRecords = $userDefaulter->count();

        $totalPages = ceil($totalRecords / $this->limit);
        $page = min($page, $totalPages);
        $start = ($this->limit * ($page - 1)) + 1;
        $end = min($this->limit * $page, $totalRecords);
        return view('pages.data-monitor.defaulter-message', compact('type', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'userDefaulter'));
    }

    public function counsellorDataMonitoring(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $paginationStart = ($page - 1) * $this->limit;
        $type = 'Counsellors';
        $userDefaulter = DB::table('defaulter_messages')
            ->join('users', 'users.id', '=', 'defaulter_messages.user_id')  // Join with the users table
            ->select(
                'users.id as user_id',
                'users.name',
                'users.contactNo',
                'users.email',
                DB::raw('count(defaulter_messages.id) as defaulter_count')  // Count messages
            )
            ->where('defaulter_messages.type', 'astrologer')  // Filter by type
            ->groupBy('users.id', 'users.name', 'users.email')  // Group by user fields
            ->get();

        $totalRecords = $userDefaulter->count();

        $userDefaulterCounts = DB::table('defaulter_messages')
            ->select('user_id', DB::raw('count(*) as defaulter_count'))
            ->where('type', 'astrologer')
            ->groupBy('user_id')
            ->pluck('defaulter_count', 'user_id')
            ->toArray();

        $totalPages = ceil($totalRecords / $this->limit);
        $page = min($page, $totalPages);
        $start = ($this->limit * ($page - 1)) + 1;
        $end = min($this->limit * $page, $totalRecords);
        return view('pages.data-monitor.user-data-monitoring', compact('type', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'userDefaulterCounts', 'userDefaulter'));
    }

    public function callsMonitoring(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $astrologers = DB::table('astrologers')
                    ->where('astrologers.isDelete', false)
                    ->where('astrologers.isVerified', true)
                    ->select('id', 'userId', 'name')
                    ->get();

                $users = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('users.isDelete', false)
                    ->where('user_roles.roleId', 3)
                    ->whereNot('users.contactNo', null)
                    ->select('users.id', 'users.name', 'users.contactNo')
                    ->orderByDesc('users.id')
                    ->get();

                $completedCalls = DB::table('callrequest')
                    ->select('users.id as userId', 'users.name as Username', 'users.contactNo as contactNo', 'astrologers.userId as astroUserId', 'astrologers.name as astrologerName', 'callrequest.channelName', 'callrequest.created_at as created_at', 'callrequest.id as callId', 'callrequest.sId')
                    ->join('astrologers', 'callrequest.astrologerId', '=', 'astrologers.id')
                    ->join('users', 'users.id', '=', 'callrequest.userId')
                    ->where('callrequest.callStatus', 'Completed')
                    // ->groupBy('callrequest.astrologerId')
                    ->orderBy('callrequest.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                // return $completedCalls;
                $totalRecords = $completedCalls->count();
                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.data-monitor.calls-monitoring', compact('completedCalls', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'astrologers', 'users'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------------------block-keywords-CRUD-------------------------------------------------------------------

    public function blockKeywords(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $paginationStart = ($page - 1) * $this->limit;

        $patterns = DB::table('block-keywords')
            ->orderBy('id', 'desc')
            ->get();
        $totalRecords = $patterns->count();

        $totalPages = ceil($totalRecords / $this->limit);
        $page = min($page, $totalPages);
        $start = ($this->limit * ($page - 1)) + 1;
        $end = min($this->limit * $page, $totalRecords);
        return view('pages.data-monitor.block-keyword-list', compact('totalPages', 'totalRecords', 'start', 'end', 'page', 'patterns'));
    }

    public function createKeywords()
    {
        $keywords = DB::table('block-keywords')
            ->orderBy('id', 'desc')
            ->get();
        return view('pages.data-monitor.create-keyword', compact('keywords'));
    }

    public function storeKeyword(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'pattern' => 'required|string',
        ]);

        $type = $request->input('type');
        $pattern = $request->input('pattern');

        if ($type === 'offensive-word') {
            $existingData = DB::table('block-keywords')->where('type', $type)->first();

            if ($existingData) {
                $existingPatterns = json_decode($existingData->pattern, true) ?? [];

                if (!in_array($pattern, $existingPatterns)) {
                    $existingPatterns[] = $pattern;

                    DB::table('block-keywords')->where('type', $type)->update([
                        'pattern' => json_encode($existingPatterns),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                DB::table('block-keywords')->insert([
                    'type' => $type,
                    'pattern' => json_encode([$pattern]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            $existingData = DB::table('block-keywords')->where('type', $type)->first();

            if ($existingData) {
                DB::table('block-keywords')->where('type', $type)->update([
                    'pattern' => $pattern,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('block-keywords')->insert([
                    'type' => $type,
                    'pattern' => $pattern,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        return redirect(route('block.keywords'))->with('success', 'Added successful!');
    }

    public function editKeyword(Request $request, $id)
    {
        $keyword = DB::table('block-keywords')->where('id', $id)->first();
        return view('pages.data-monitor.edit-keyword', compact('keyword'));
    }

    public function updateKeyword(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'pattern' => 'required',
        ]);

        $type = $request->input('type');
        $pattern = $request->input('pattern');

        $keywordsArray = array_map('trim', explode(',', $pattern));

        // Convert the array into a JSON format (with quotes around each item)
        $keywordsJson = json_encode($keywordsArray);

        $existingData = DB::table('block-keywords')->where('id', $request->id)->first();

        if (!$existingData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        if ($type == 'offensive-word') {
            DB::table('block-keywords')->where('id', $request->id)->update([
                'type' => $type,
                'pattern' => $keywordsJson,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('block-keywords')->where('id', $request->id)->update([
                'type' => $type,
                'pattern' => $pattern,
                'updated_at' => now(),
            ]);
        }

        return redirect(route('block.keywords'))->with('success', 'updated successful!');
    }

    public function deleteKeyword($id)
    {
        DB::table('block-keywords')->where('id', $id)->delete();

        return redirect(route('block.keywords'))->with('success', 'Keyword deleted successfully!');
    }
}
