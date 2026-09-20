<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBulkNotifications;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class NotificationController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addNotification()
    {
        return view('pages.notification-list');
    }

    public function addNotificationApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                Notification::create([
                    'title' => $req->title,
                    'description' => $req->did,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Skill Api

    public function getNotification(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $notifications = Notification::query();
                $notifications->orderBy('id', 'DESC');
                $notificationCount = $notifications->count();
                $notifications->skip($paginationStart);
                $notifications->take($this->limit);
                $notifications = $notifications->get();
                $totalPages = ceil($notificationCount / $this->limit);
                $totalRecords = $notificationCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                $users = DB::Table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('isDelete', '=', false)
                    ->where('isActive', '=', true)
                    // ->where('user_roles.roleId', '=', 3)
                    ->select('users.*', 'user_roles.roleId')
                    ->get();

                //  filters by wallet never recharged
                $wallet_empty = DB::table('users')
                    ->leftJoin('user_wallets', 'users.id', '=', 'user_wallets.userId')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->whereNull('user_wallets.userId')  // Users who don't have any wallet records
                    ->where('users.isDelete', '=', false)
                    ->where('users.isActive', '=', true)
                    ->where('user_roles.roleId', '=', 3)
                    ->select('users.*')
                    ->get();

                $usersNotUsedFree = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->whereNotExists(function ($query) {
                        $query
                            ->select(DB::raw(1))
                            ->from('chatrequest')
                            ->whereColumn('chatrequest.userId', 'users.id')
                            ->where('chatrequest.chatStatus', '=', 'Completed');
                    })
                    ->whereNotExists(function ($query) {
                        $query
                            ->select(DB::raw(1))
                            ->from('callrequest')
                            ->whereColumn('callrequest.userId', 'users.id')
                            ->where('callrequest.callStatus', '=', 'Completed');
                    })
                    ->where('users.isDelete', '=', false)
                    ->where('users.isActive', '=', true)
                    ->where('user_roles.roleId', '=', 3)
                    ->select('users.*')
                    ->get();

                return view('pages.notification-list', compact('notifications', 'users', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'wallet_empty', 'usersNotUsedFree'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editNotification()
    {
        return view('pages.notification-list');
    }

    public function editNotificationApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $notification = Notification::find($req->filed_id);
                if ($notification) {
                    $notification->title = $req->title;
                    $notification->description = $req->did;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function notifcationStatus(Request $request)
    {
        return view('pages.notification-list');
    }

    public function notifcationStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $notification = Notification::find($request->status_id);
                if ($notification) {
                    $notification->isActive = !$notification->isActive;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function sendNotification(Request $req)
    {
        try {
            // Clean input
            $userIds = ($req->userIds === ['all']) ? [] : $req->userIds;
            if ($userIds) {
                $userIds = json_decode(json_encode($userIds));  // Ensure array format
            }

            // Prepare data array to pass to the job
            // (Queue jobs serialize data, so pass simple arrays, not Request objects)
            $requestData = [
                'notification_id' => $req->notification_id,
                'userIds' => $userIds,
                'role' => $req->role
            ];

            $authUserId = 1;  // Or Auth::id();

            // Dispatch the Master Job
            // This takes milliseconds to execute
            ProcessBulkNotifications::dispatch($requestData, $authUserId);

            return response()->json([
                'success' => ['Notifications have been queued and are processing in the background.'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => [$e->getMessage()]]);
        }
    }
}
