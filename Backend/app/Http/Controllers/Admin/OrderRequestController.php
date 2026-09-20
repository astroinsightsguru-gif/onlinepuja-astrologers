<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use PDF;
use Response;

class OrderRequestController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getOrderRequest(Request $request)
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
                            $od->gstAmount = $od->payableAmount / $od->gstPercent;
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
                return view('pages.order-request', compact('orderRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function printPdf(Request $request)
    {
        try {
            $this->path = env('APP_URL');
            $orderRequest = UserOrder::join('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
                ->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
                ->join('users', 'users.id', '=', 'order_request.userId')
                ->leftjoin('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
                ->where('order_request.orderType', '=', 'astromall')
                ->select('order_request.*', 'users.name as userName', 'users.contactNo as userContactNo', 'astromall_products.name as productName', 'product_categories.name as categoryName', 'astromall_products.productImage',
                    'order_addresses.name as addressUserName', 'order_addresses.phoneNumber', 'order_addresses.phoneNumber2', 'order_addresses.flatNo', 'order_addresses.locality', 'order_addresses.landmark', 'order_addresses.city', 'order_addresses.state', 'order_addresses.country', 'order_addresses.pincode')
                ->orderBy('order_request.id', 'DESC');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $orderRequest = $orderRequest->where(function ($q) use ($searchString) {
                    $q
                        ->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $orderRequest = $orderRequest->get();
            if ($orderRequest && count($orderRequest) > 0) {
                foreach ($orderRequest as $od) {
                    if ($od->gstPercent > 0) {
                        $od->gstAmount = $od->payableAmount / $od->gstPercent;
                        $od->gstAmount = number_format($od->gstAmount, 2, '.', ',');
                    } else {
                        $od->gstAmount = 0;
                    }
                }
            }
            $data = [
                'title' => 'Order Request Report',
                'date' => Carbon::now()->format('d-m-Y h:i a'),
                'orderRequest' => $orderRequest,
            ];
            $pdf = PDF::loadView('pages.order-request-report', $data);
            return $pdf->download('orderRequest.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportOrderRequestCSV(Request $request)
    {
        $orderRequest = UserOrder::join('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
            ->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
            ->join('users', 'users.id', '=', 'order_request.userId')
            ->leftjoin('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
            ->where('order_request.orderType', '=', 'astromall')
            ->select('order_request.*', 'users.name as userName',
                'users.contactNo as userContactNo', 'astromall_products.name as productName',
                'product_categories.name as categoryName', 'astromall_products.productImage',
                'order_addresses.name as addressUserName', 'order_addresses.phoneNumber', 'order_addresses.phoneNumber2', 'order_addresses.flatNo', 'order_addresses.locality', 'order_addresses.landmark', 'order_addresses.city', 'order_addresses.state', 'order_addresses.country', 'order_addresses.pincode')
            ->orderBy('order_request.id', 'DESC');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $orderRequest = $orderRequest->where(function ($q) use ($searchString) {
                $q
                    ->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $orderRequest = $orderRequest->get();
        if ($orderRequest && count($orderRequest) > 0) {
            foreach ($orderRequest as $od) {
                if ($od->gstPercent > 0) {
                    $od->gstAmount = $od->payableAmount / $od->gstPercent;
                    $od->gstAmount = number_format($od->gstAmount, 2, '.', ',');
                } else {
                    $od->gstAmount = 0;
                }
            }
        }

        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('orderRequest.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'User',
            'Product',
            'Amount',
            'PaymentMethod',
            'OrderDate',
            'OrderAddress',
        ]);

        for ($i = 0; $i < count($orderRequest); $i++) {
            fputcsv($handle, [
                $i + 1,
                $orderRequest[$i]->userName,
                $orderRequest[$i]->productName . '(' . $orderRequest[$i]->categoryName . ')',
                $orderRequest[$i]->payableAmount,
                $orderRequest[$i]->paymentMethod,
                date('d-m-Y h:i a', strtotime($orderRequest[$i]->created_at)),
                '(' . $orderRequest[$i]->flatNo . ',' . $orderRequest[$i]->landmark . ',' . $orderRequest[$i]->city . ',' . $orderRequest[$i]->state . ',' . $orderRequest[$i]->country . '-' . $orderRequest[$i]->pincode . ')',
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'astrologerEarning.csv', $headers);
    }
}
