<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    //Get a all list of coupon
    public function getCouponcode(Request $req)
    {
        try {
            $coupon = Coupon::query();
            if ($s = $req->input(key:'s')) {
                $coupon->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            if ($req->startIndex >= 0 && $req->fetchRecord > 0) {
                $coupon->skip($req->startIndex);
                $coupon->take($req->fetchRecord);
            }
            $couponCount = Coupon::query();
            return response()->json([
                'recordList' => $coupon->get(),
                'totalRecords' => $couponCount->count(),
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
