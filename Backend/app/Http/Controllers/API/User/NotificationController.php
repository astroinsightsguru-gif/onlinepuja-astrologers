<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Notification;
use App\services\FCMService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    //Get all the Notifications
    public function getNotification(Request $req)
    {
        try {

            $notification = Notification::query();
            if ($s = $req->input(key:'s')) {
                $notification->whereRaw(sql:"title LIKE '%" . $s . "%' ");
            }
            $notification->orderBy("id", "DESC");
            $notificationCount = $notification->count();
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $notification->skip($req->startIndex);
                $notification->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $notification->get(),
                'status' => 200,
                'totalRecords' => $notificationCount,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function sendNotification(Request $req)
    {
        try {

            $notification = Notification::find($req->id);
            if ($req->userIds && count(json_decode($req->userIds)) > 0) {

                foreach (json_decode($req->userIds) as $user) {
                    $userDeviceDetail = DB::table('user_device_details')->where('userId', '=', $user)->get();
                    if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                        $response = FCMService::send(

                            $userDeviceDetail,
                            [
                                'title' => $notification->title,
                                'body' => ['description' => $notification->description],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notification = array(
                                'userId' => $user,
                                'title' => $notification->title,
                                'description' => $notification->description,
                                'notificationId' => $req->id,
                                'createdBy' => 1,
                                'modifiedBy' => 1,
                                 'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now(),
                            );
                            DB::table('user_notifications')->insert($notification);
                        }
                    }
                }
            } elseif ($req->role && $req->role == 'Users') {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('user_roles', 'user_roles.userId', '=' . 'user_device_details.userId')
                    ->where('user_roles.roleId', '=', 3)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    foreach ($userDeviceDetail as $detail) {
                        $details = array($detail);
                        $response = FCMService::send(
                            collect($details),
                            [
                                'title' => $notification[0]->title,
                                'body' => ['description' => $notification->description],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notification = array(
                                'userId' => $detail->userId,
                                'title' => $notification->title,
                                'description' => $notification->description,
                                'notificationId' => $req->id,
                                'createdBy' => 1,
                                'modifiedBy' => 1,
                                 'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                            );
                            DB::table('user_notifications')->insert($notification);
                        }
                    }
                }
            } elseif ($req->role && $req->role == 'Astrologer') {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('user_roles', 'user_roles.userId', '=' . 'user_device_details.userId')
                    ->where('user_roles.roleId', '=', 2)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    foreach ($userDeviceDetail as $detail) {
                        $details = array($detail);
                        $response = FCMService::send(
                            collect($details),
                            [
                                'title' => $notification->title,
                                'body' => ['description' => $notification->description],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notification = array(
                                'userId' => $detail->userId,
                                'title' => $notification->title,
                                'description' => $notification->description,
                                'notificationId' => $req->id,
                                'createdBy' => 1,
                                'modifiedBy' => 1,
                                 'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                            );
                            DB::table('user_notifications')->insert($notification);
                        }
                    }
                }
            } else {
                $userDeviceDetails = DB::table('user_device_details')
                    ->get();
                if ($userDeviceDetails && count($userDeviceDetails) > 0) {
                    foreach ($userDeviceDetails as $detail) {
                        $details = array($detail);
                        $response = FCMService::send(
                            collect($details),
                            [
                                'title' => $notification->title,
                                'body' => ['description' => $notification->description],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notifications = array(
                                'userId' => $detail->userId,
                                'title' => $notification->title,
                                'description' => $notification->description,
                                'notificationId' => $req->id,
                                'createdBy' => 1,
                                'modifiedBy' => 1,
                                 'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                            );
                            DB::table('user_notifications')->insert($notifications);
                        }

                    }
                }
            }
            return response()->json([
                'message' => 'Notification send sucessfully',
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

    public function deleteNotification(Request $req)
    {
        try {
            $notification = DB::table('notifications')
                ->where('id', '=', $req->id)
                ->delete();
            return response()->json([
                'message' => 'Notification Deleted sucessfully',
                'recordList' => $notification,
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

    public function deleteUserNotification(Request $req)
    {
        try {
            DB::table('user_notifications')->where('id', '=', $req->id)->delete();
            return response()->json([
                'message' => 'Notification Deleted Successfully',
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

    public function deleteAllUserNotification(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            else {
                $userId = Auth::guard('api')->user()->id;
            }
            $userId = $req->userId ? $req->userId : $userId;
            DB::table('user_notifications')->where('userId', '=', $userId)->delete();
            return response()->json([
                'message' => 'Notification Deleted Successfully',
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

    public function getUser(Request $req)
    {
        try {
            $user = DB::Table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->where('isDelete', '=', false)
                ->where('isActive', '=', true)
                ->where('user_roles.roleId', '=', 3)
                ->select('users.*')
                ->get();
            return response()->json([
                'message' => 'Get User Suucessfully',
                'status' => 200,
                'recordList' => $user,
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
