<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\services\FCMService;
use App\services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductRecommendController extends Controller
{
    public function addProductRecommend(Request $req)
    {
        try {
            $data = $req->only(
                'productId',
                'userId',
                'astrologerId',
            );

            // Validate the data
            $validator = Validator::make($data, [
                'productId' => 'required',
                'userId' => 'required',
                'astrologerId' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $productRecommId = DB::table('product_recommends')->updateOrInsert(
                [
                    'productId' => $req->productId,
                    'userId' => $req->userId,
                    'astrologerId' => $req->astrologerId,
                ],
                [
                    'recommDateTime' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            // Fetch the full inserted record
            $productRecomm = DB::table('product_recommends')->where('id', $productRecommId)->first();

            $astrologer = DB::Table('astrologers')
                ->leftjoin('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                ->where('astrologers.id', '=', $req->astrologerId)
                ->select('astrologers.name', 'astrologers.profileImage', 'user_device_details.fcmToken')
                ->get();

            $userDeviceDetail = DB::table('user_device_details')
                ->JOIN('users', 'users.id', '=', 'user_device_details.userId')
                ->WHERE('user_device_details.userId', '=', $req->userId)
                ->SELECT('user_device_details.*', 'users.name')
                ->get();

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                // One signal FOr notification send
                $oneSignalService = new OneSignalService();
                //  $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                $notification = [
                    'title' => 'Product Recommendation',
                    'body' => [
                        'description' => 'Hey ' . $userDeviceDetail[0]->name . ', You received a Product Recommendation from ' . $astrologer[0]->name,
                    ],
                ];
                // Send the push notification using the OneSignalService
                $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

                $notification = array(
                    'userId' => $userDeviceDetail[0]->userId,
                    'title' => 'Product Recommendation',
                    'description' => 'Hey ' . $userDeviceDetail[0]->name . ', You received a Product Recommendation from ' . $astrologer[0]->name,
                    'notificationId' => null,
                    'createdBy' => $userDeviceDetail[0]->userId,
                    'modifiedBy' => $userDeviceDetail[0]->userId,
                    'notification_type' => 51,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                DB::table('user_notifications')->insert($notification);
            }

            return response()->json([
                'message' => 'Added in product recommend successfully',
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

    public function getProductRecommend(Request $req)
    {
        try {
            $data = $req->only(
                'userId',
            );

            // Validate the data
            $validator = Validator::make($data, [
                'userId' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $productRecomm = DB::table('product_recommends')
                ->join('astromall_products', 'astromall_products.id', '=', 'product_recommends.productId')
                ->join('astrologers', 'astrologers.id', '=', 'product_recommends.astrologerId')
                ->where('product_recommends.userId', '=', $req->userId)
                ->where('product_recommends.recommDateTime', '>=', Carbon::now()->subDay())
                ->select(
                    'product_recommends.*',
                    'astrologers.name as astrologerName',
                    'astromall_products.name as productName',
                    'astromall_products.productImage',
                    'astromall_products.amount',
                    'astromall_products.slug',
                )
                ->groupBy('productId')
                ->get();

            return response()->json([
                'message' => 'Get Product successfully',
                'recordList' => $productRecomm,
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
}
