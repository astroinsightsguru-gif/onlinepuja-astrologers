<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductDetailController extends Controller
{

    //Get all astromall product details
    public function getProductDetails(Request $req)
    {
        try {
            if (!auth()->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401],401);
            }
            $productDetails = ProductDetail::query();
            if ($s = $req->input(key: 's')) {
                $productDetails->whereRaw(sql: "question LIKE '%" . $s . "%' ");
            }

            return response()->json([
                'recordList' => $productDetails->get(),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500
            ],500);
        }
    }
}
