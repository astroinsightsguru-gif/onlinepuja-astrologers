<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseOrder;
use App\Models\UserModel\Payment;
use App\Models\UserModel\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;

class CourseController extends Controller
{

    public function getCourseCategory(Request $request)
    {
        try {

            $getCoursecategory=CourseCategory::where('isActive',1)->orderBy('id', 'DESC')->get();
            $coursecategoryCount = Course::count();
            return response()->json([
                'recordList' => $getCoursecategory,
                'status' => 200,
                'totalRecords' => $coursecategoryCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    #----------------------------------------------------

    public function getCourse(Request $request)
    {
        try {

            $getCourse = Course::with('category')
            ->where('isActive', 1);

            if ($request->course_category_id) {
            $getCourse = $getCourse->where('course_category_id', $request->course_category_id);
            }

            $getCourse = $getCourse->orderBy('id', 'DESC')->get();
            foreach ($getCourse as $course) {
            $course->category_name = $course->category ? $course->category->name : null;
            }

            $courseCount = Course::count();
            return response()->json([
                'recordList' => $getCourse,
                'status' => 200,
                'totalRecords' => $courseCount,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

     #----------------------------------------------------

    public function getCourseDetails(Request $request)
    {
        try {

            $getCourse = Course::with('category', 'chapters')
            ->where('isActive', 1)
            ->where('id', $request->course_id)
            ->first();

            $courseOrder=CourseOrder::where('course_id', $request->course_id)->where('astrologerId', $request->astrologerId)->first();
            $getCourse->courseOrderStatus=false;
            if($courseOrder){
                $getCourse->courseOrderStatus=true;
            }

            $courseCount = Course::where('isActive', 1)
            ->where('id',$request->course_id)->count();
            return response()->json([
                'recordList' => $getCourse,
                'status' => 200,
                'totalRecords' => $courseCount,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

     #----------------------------------------------------

    public function getCourseChapters(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'course_id' => 'required',
            ]);

            // Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            $getChapters=CourseChapter::where('course_id',$request->course_id)->where('isActive',1)->orderBy('id', 'DESC')->get();
            foreach ($getChapters as $chapters) {
                $chapters->course ? $chapters->course->name : null;
            }
            $chaptersCount = CourseChapter::count();
            return response()->json([
                'recordList' => $getChapters,
                'status' => 200,
                'totalRecords' => $chaptersCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

     #----------------------------------------------------


        public function addCourseOrder(Request $request){

            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $request->only(
                'astrologerId',
                'course_id',

            );

            $validator = Validator::make($data, [
                'astrologerId' => 'required',
                'course_id'=>'required',

            ]);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            // $user_country=User::where('id',$id)->where('country','India')->first(); // commented by bhushan borse 04, june 2025
            $user_country=User::where('id',$id)->where('countryCode','+91')->first(); // added by bhushan borse 04, june 2025
            $inr_usd_conv_rate = DB::table('systemflag')->where('name','UsdtoInr')->select('value')->first();

            // $Gstpercentage = DB::table('systemflag')
            // ->where('name', 'Gst')
            // ->select('value')
            // ->first();

            $course = Course::findOrFail($request->course_id);

           //   $payableAmount=$course->course_price;
           $payableAmount = $user_country ? $course->course_price : convertusdtoinr($course->course_price);

        //    $gstPercent=number_format($course->course_price * ($Gstpercentage->value / 100), 2);
           $totalPayable= number_format($payableAmount, 2);

           $payableAmount=str_replace(',', '', $payableAmount);
           $totalPayable=str_replace(',', '', $totalPayable);
           $totalwalletchekpayable = str_replace(',', '', $totalPayable);

        //    if($user_country){
        //         $payableAmount=convertinrtousd($payableAmount);
        //         $totalPayable=convertinrtousd($totalPayable);
        //     }


           $wallet = DB::table('user_wallets')
           ->where('userId', '=', $id)
           ->get();

           $commission = DB::table('commissions')
           ->where('commissionTypeId', '=', '8')
           ->where('astrologerId', '=', $request->astrologerId)
           ->get();
            if ($commission && count($commission) > 0) {
                $adminCommission = ($commission[0]->commission * $totalPayable) / 100;
            } else {
                $syscommission = DB::table('systemflag')->where('name', 'GiftCommission')->select('value')->get();

                $adminCommission = $totalPayable;
            }

           if (!$wallet->isEmpty()  && $wallet[0]->amount>=$totalwalletchekpayable)
           {
                   // Prepare order data as an array
                    $orderData = [
                        'astrologerId' => $request->astrologerId,
                        'course_id' => $request->course_id,
                        'course_price' => $payableAmount,
                        // 'course_gst_amount' => $gstPercent,
                        'course_total_price' => $totalPayable,
                        'payment_type' => 'wallet',
                        'course_order_status' => 'success',
                        'course_completion_status' => 'incomplete',
                        'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                        'created_at' =>Carbon::now(),
                        'updated_at' =>Carbon::now()

                    ];
                    $order = CourseOrder::create($orderData);

               if($order){

                   $wallet = DB::table('user_wallets')->where('userId', '=', $id)->first();
                   $walletData = [
                    // 'amount' => $wallet->amount - ($user_country ? ((int)$totalPayable * $inr_usd_conv_rate->value) : (int)$totalPayable), // commented by bhushan borse 04, june 2025
                    'amount' => $wallet->amount - ((int)$totalPayable), // added by bhushan borse 04, june 2025
                ];

                   DB::table('user_wallets')->where('id', $wallet->id)->update($walletData);

                   // Prepare transaction data as an array
                   $orderRequest = array(
                       'userId' => $id,
                       'astrologerId' => $request->astrologerId,
                       'orderType' => 'course',
                       'course_id' => $request->course_id,
                       'payableAmount' => $payableAmount,
                    //    'gstPercent' => $Gstpercentage->value,
                       'totalPayable' => $totalPayable,
                       'orderStatus' => 'Complete',
                       'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                       'created_at' =>Carbon::now(),
                        'updated_at' =>Carbon::now()

                   );
                   DB::Table('order_request')->insert($orderRequest);
                   $Orderid = DB::getPdo()->lastInsertId();
                   // Prepare transaction data as an array
                   $transactionData = [
                       'userId' => $id,
                       'astrologerId' => $request->astrologerId,
                       'orderId' => $Orderid,
                       'amount' => $totalPayable,
                       'isCredit' => false,
                       'transactionType' => 'courseOrder',
                       'created_at' => Carbon::now(),
                       'updated_at' => Carbon::now(),
                       'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                   ];
                   DB::table('wallettransaction')->insert($transactionData);



                    // Commission
                if ($commission && count($commission) > 0 ) {
                    $adminGetCommission = array(
                        'commissionTypeId' => 8,
                        "amount" => $adminCommission,
                        "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                        "orderId" => $Orderid,
                        "createdBy" => $id,
                        "modifiedBy" => $id,
                        'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                        'created_at' =>Carbon::now(),
                        'updated_at' =>Carbon::now()

                    );
                    DB::table('admin_get_commissions')->insert($adminGetCommission);
                }elseif($syscommission && count($syscommission) > 0){
                    $adminGetCommission = array(
                        'commissionTypeId' => 8,
                        "amount" => $adminCommission,
                        "commissionId" => null,
                        "orderId" => $Orderid,
                        "createdBy" => $id,
                        "modifiedBy" => $id,
                        'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                        'created_at' =>Carbon::now(),
                        'updated_at' =>Carbon::now()
                    );
                    DB::table('admin_get_commissions')->insert($adminGetCommission);
                }

                   return response()->json([
                       'message' => 'Order Placed sucessfully!',
                       'recordList' => $order,
                       'status' => 200,
                   ], 200);
               }

                return response()->json([
                   'error' => false,
                   'message' => 'Order Failed!',
                   'status' => 500,
               ], 500);
           }

            // Create a new payment record
            $payment = Payment::create([
                // 'amount' => $user_country ? ($totalPayable * $inr_usd_conv_rate->value) : $totalPayable, // commented by bhushan borse 04, june 2025
                'amount' => $totalPayable, // added by bhushan borse 04, june 2025
                'cashback_amount' => 0,
                'userId' => $id,
                'paymentStatus' => 'pending',
                'payment_for' => 'course',
                'payment_order_info' => ['payment_type' => 'online',...$request->all()],
                'createdBy' => $id,
                'modifiedBy' => $id,
                'created_at' =>Carbon::now(),
                'updated_at' =>Carbon::now()

            ]);

            $lastPayment = Payment::where('userId', $id)->latest()->first();

            $HttpSession = new HttpSession();
            $HttpSession->set('courseOrderRequest',['payment_type' => 'online',...$request->all()]);


            return response()->json([
                    'message' => 'Pay Online.',
                    'redirect' => url('/') . "/payment?payid={$lastPayment->id}",
                    'status' => 200,
                ], 200);

        }

         #----------------------------------------------------


        public function getCourseOrderlist(Request $request)
        {
            $data = $request->only(
                'astrologerId',
            );

            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            $courseOrder = CourseOrder::with('course','courseChapters')->where('astrologerId',$request->astrologerId)->get();

            $courseOrdercount = CourseOrder::where('astrologerId',$request->astrologerId)->count();

            return response()->json([
                'recordList' => $courseOrder,
                'status' => 200,
                'totalRecords' => $courseOrdercount,
            ], 200);
        }

         #----------------------------------------------------


         public function courseCompletion(Request $request)
         {
            $data = $request->only(
                'course_order_id',
            );

            $validator = Validator::make($data, [
                'course_order_id' => 'required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            $courseOrder = CourseOrder::where('id',$request->course_order_id)->update(['course_completion_status' => 'completed']);
           if($courseOrder){
                return response()->json([
                    'status' => 200,
                    'message' => 'Course Completed Successfully!',
                ], 200);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'No Course Found!',
                ], 400);
            }
         }


           public function checkReferral(Request $request)
         {

             $data = $request->only(
                 'referral_token',
             );

             $validator = Validator::make($data, [
                 'referral_token' => 'required',
             ]);

             if ($validator->fails()) {
                 DB::rollback();
                 return response()->json([
                     'error' => $validator->messages(),
                     'status' => 400,
                 ], 400);
             }

             $referralUser = User::where('referral_token',$request->referral_token)->first();

             if($referralUser){
                 return response()->json([
                     'user' => $referralUser,
                    'status' => 200,
                 ], 200);
             }else{
                 return response()->json([
                    'status' => 400,
                    'message' => 'No Referral Found!',
                 ], 400);
             }
         }


}
