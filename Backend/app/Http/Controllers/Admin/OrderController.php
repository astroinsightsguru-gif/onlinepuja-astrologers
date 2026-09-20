<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\UserModel\User;
use App\Models\UserModel\UserOrder;
use App\services\FCMService;
use App\services\OneSignalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getOrders(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $orderRequest = UserOrder::join('astromall_products as astromall', 'astromall.id', '=', 'order_request.productId')
                    ->join('product_categories as category', 'category.id', '=', 'astromall.productCategoryId')
                    ->join('users as us', 'us.id', '=', 'order_request.userId')
                    ->leftjoin('order_addresses as address', 'address.id', '=', 'order_request.orderAddressId')
                    ->where('order_request.orderType', '=', 'astromall')
                    ->select('order_request.*', 'us.name as userName', 'us.contactNo as userContactNo', 'astromall.name as productName', 'category.name as categoryName', 'astromall.productImage',
                        'address.name as addressUserName', 'address.phoneNumber', 'address.phoneNumber2', 'address.flatNo', 'address.locality', 'address.landmark', 'address.city', 'address.state', 'address.country', 'address.pincode')
                    ->orderBy('order_request.id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $orderRequest = $orderRequest->where(function ($q) use ($searchString) {
                        $q
                            ->where('us.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('us.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                // Clone query for counting records
                $countQuery = clone $orderRequest;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $orderRequest->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $orderRequest->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $orderRequest->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                }

                $orderCount = DB::table('order_request')
                    ->join('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
                    ->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->leftjoin('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
                    ->where('order_request.orderType', '=', 'astromall');
                if ($searchString) {
                    $orderCount = $orderCount->where(function ($q) use ($searchString) {
                        $q
                            ->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $orderCount = $orderCount->count();
                $orderRequest->skip($paginationStart);
                $orderRequest->take($this->limit);
                $orderRequest = $orderRequest->get();
                if ($orderRequest && count($orderRequest) > 0) {
                    foreach ($orderRequest as $od) {
                        if ($od->gstPercent > 0) {
                            $od->gstAmount = $od->payableAmount * ($od->gstPercent / 100);
                            $od->gstAmount = number_format($od->gstAmount, 2, '.', ',');
                        } else {
                            $od->gstAmount = 0;
                        }
                    }
                }

                $totalPages = ceil($orderCount / $this->limit);
                $totalRecords = $orderCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.order', compact('orderRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($id)
    {
        $order = UserOrder::join('astromall_products as astromall', 'astromall.id', '=', 'order_request.productId')
            ->join('product_categories as category', 'category.id', '=', 'astromall.productCategoryId')
            ->join('users as us', 'us.id', '=', 'order_request.userId')
            ->leftjoin('order_addresses as address', 'address.id', '=', 'order_request.orderAddressId')
            ->where('order_request.id', '=', $id)
            ->where('order_request.orderType', '=', 'astromall')
            ->select('order_request.*', 'us.name as userName', 'us.contactNo as userContactNo', 'us.email as userEmail', 'astromall.name as productName', 'category.name as categoryName', 'astromall.productImage',
                'address.name as addressUserName', 'address.phoneNumber', 'address.phoneNumber2', 'address.flatNo', 'address.locality', 'address.landmark', 'address.city', 'address.state', 'address.country', 'address.pincode')
            ->orderBy('order_request.id', 'DESC')
            ->first();

        if ($order) {
            if ($order->gstPercent > 0) {
                $order->gstAmount = $order->payableAmount * ($order->gstPercent / 100);
                $order->gstAmount = number_format($order->gstAmount, 2, '.', ',');
            } else {
                $order->gstAmount = 0;
            }
        }
        $currencySymbol = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();

        $logo = DB::table('systemflag')
            ->where('name', 'AdminLogo')
            ->select('value')
            ->first();

        $gst = DB::table('systemflag')
            ->where('name', 'Gst')
            ->select('value')
            ->first();

        $appname = DB::table('systemflag')
            ->where('name', 'AppName')
            ->select('value')
            ->first();

        if (!$order) {
            // Handle case where order with given ID is not found
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Generate PDF invoice view with order data
        $pdf = PDF::loadView('pages.invoice', compact('order', 'currencySymbol', 'logo', 'gst', 'appname'));
        return $pdf->stream();
        // return $pdf->download('invoice-'.$order->id.'.pdf');
    }

    public function changeOrderStatus(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $data = array(
                    'orderStatus' => $request->status,
                    'updated_at' => Carbon::now(),
                );

                // $user_country=User::where('id',$request->userId)->where('country','India')->first(); // commented
                $user_country = User::where('id', $request->userId)->where('countryCode', '+91')->first();  // added
                $inr_usd_conv_rate = DB::table('systemflag')->where('name', 'UsdtoInr')->select('value')->first();
                // --------------------------------------cancelled-------------------------------------------
                if ($request->status == 'Cancelled') {
                    $order1 = DB::table('order_request')->where('id', '=', $request->id)->get();

                    $data1 = array(
                        'orderStatus' => $request->status,
                        'updated_at' => Carbon::now(),
                    );
                    $orderRequest1 = DB::table('order_request')->where('id', '=', $request->id)->update($data1);

                    $wallet = DB::table('user_wallets')
                        ->where('userId', '=', $request->userId)
                        ->get();

                    $wallets = array(
                        // 'amount' => $wallet[0]->amount + ($user_country ? ($order1[0]->totalPayable * $inr_usd_conv_rate->value) : $order1[0]->totalPayable),
                        'amount' => $wallet[0]->amount + $order1[0]->totalPayable,
                    );

                    DB::table('user_wallets')
                        ->where('id', $wallet[0]->id)
                        ->update($wallets);

                    $transaction = array(
                        'userId' => $request->userId,
                        'amount' => $order1[0]->totalPayable,
                        'isCredit' => true,
                        'transactionType' => 'astromallOrder',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'inr_usd_conversion_rate' => $inr_usd_conv_rate->value,
                    );
                    $res = array('totalPayable' => $order1[0]->totalPayable);
                    // return $transaction;
                    DB::table('wallettransaction')->insert($transaction);
                    return redirect()->back();
                } else {
                    // --------------------------cancelled------------------------------------
                    DB::table('order_request')->where('id', '=', $request->id)->update($data);

                    if ($request->status == 'Delivered') {
                        $orderRequest = DB::table('order_request')->where('id', '=', $request->id)->first();
                        $req['payableAmount'] = str_replace(',', '', $orderRequest->payableAmount);
                        $req['totalPayable'] = str_replace(',', '', $orderRequest->totalPayable);

                        // $pro_reccommend=DB::table('product_recommends')->where('userId',$orderRequest->userId)->where('productId',$orderRequest->productId)->where('recommDateTime', '>=', Carbon::now()->subDay())->latest()->first();

                        $pro_reccommend = DB::table('order_request')
                            ->join('product_recommends', 'product_recommends.id', 'order_request.pro_recommend_id')
                            ->select('order_request.*', 'product_recommends.astrologerId')
                            ->where('order_request.id', '=', $request->id)
                            ->latest()
                            ->first();
                        // dd($pro_reccommend);

                        $productrefComm = DB::table('systemflag')->where('name', 'productRefCommission')->first();
                        $productrefComminrs = ($productrefComm->value * $orderRequest->payableAmount) / 100;

                        if ($pro_reccommend) {
                            $astrologerId = $pro_reccommend->astrologerId;
                            // $astrologercountry=Astrologer::where('id', $astrologerId)->where('country','India')->first();   // commented
                            $astrologercountry = Astrologer::where('id', $astrologerId)->where('countryCode', '+91')->first();  // added
                            $astrologerUserId = DB::table('astrologers')
                                ->where('id', '=', $astrologerId)
                                ->selectRaw('userId,name')
                                ->get();
                            $astrologerWallet = DB::table('user_wallets')
                                ->where('userId', '=', $astrologerUserId[0]->userId)
                                ->get();

                            $astrologerWalletData = array(
                                // 'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + ($astrologercountry ? ($productrefComminrs * $inr_usd_conv_rate->value) : $productrefComminrs) : ($astrologercountry ? ($productrefComminrs * $inr_usd_conv_rate->value) : $productrefComminrs),
                                'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $productrefComminrs : $productrefComminrs,
                                'userId' => $astrologerUserId[0]->userId,
                                'createdBy' => $astrologerUserId[0]->userId,
                                'modifiedBy' => $astrologerUserId[0]->userId,
                            );

                            if ($astrologerWallet && count($astrologerWallet) > 0) {
                                DB::Table('user_wallets')
                                    ->where('userId', '=', $astrologerUserId[0]->userId)
                                    ->update($astrologerWalletData);
                            } else {
                                DB::Table('user_wallets')->insert($astrologerWalletData);
                            }

                            $astrologerWalletTransaction = array(
                                'amount' => $productrefComminrs,
                                'userId' => $astrologerUserId[0]->userId,
                                'createdBy' => $orderRequest->userId,
                                'modifiedBy' => $orderRequest->userId,
                                'isCredit' => true,
                                'transactionType' => 'ProductRefCommission',
                                'astrologerId' => $astrologerId,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'inr_usd_conversion_rate' => $inr_usd_conv_rate->value,
                            );
                            DB::table('wallettransaction')->insert($astrologerWalletTransaction);
                        }
                    }

                    $userDeviceDetail = DB::table('user_device_details')
                        ->join('order_request', 'order_request.userId', '=', 'user_device_details.userId')
                        ->where('order_request.id', '=', $request->id)
                        ->select('user_device_details.*')
                        ->get();
                    if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                        $title = $request->status == 'Confirmed' ? 'Your Order has been accept from admin' : 'Your Order ' . $request->status . ' Successfully';

                        // One signal FOr notification send
                        $oneSignalService = new OneSignalService();
                        // $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                        $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                        $notification = [
                            'title' => $title,
                            'body' => ['description' => $title, 'status' => ''],
                        ];
                        // Send the push notification using the OneSignalService
                        $response = $oneSignalService->sendNotification($userPlayerIds, $notification);
                    }

                    $title = $request->status == 'Confirmed' ? 'Your Order has been accept from admin' : 'Your Order ' . $request->status . ' Successfully';
                    $notification = array(
                        'userId' => $userDeviceDetail[0]->userId,
                        'title' => $title,
                        'description' => $title,
                        'notificationId' => null,
                        'createdBy' => $userDeviceDetail[0]->userId,
                        'modifiedBy' => $userDeviceDetail[0]->userId,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    );
                    DB::table('user_notifications')->insert($notification);

                    return redirect()->back();
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------------------------------------
    public function productRecommend(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $productrecommend = DB::table('product_recommends')
                    ->join('users', 'users.id', 'product_recommends.userId')
                    ->join('astrologers', 'astrologers.id', 'product_recommends.astrologerId')
                    ->join('astromall_products', 'astromall_products.id', 'product_recommends.productId')
                    ->leftJoin('order_request', 'order_request.pro_recommend_id', 'product_recommends.id')
                    ->select(
                        'users.name as userName',
                        'product_recommends.*',
                        'astrologers.name as astrologerName',
                        'astromall_products.name as productName',
                        'astromall_products.productImage',
                        DB::raw('CASE WHEN order_request.pro_recommend_id IS NOT NULL THEN true ELSE false END as product_recommend')
                    )
                    ->orderBy('product_recommends.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                $totalRecords = $productrecommend->count();

                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.product-recommend', compact('productrecommend', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
