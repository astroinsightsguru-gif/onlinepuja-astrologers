<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\AstrotalkInNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdsBannerController extends Controller
{
    //Get an adsBanner
    public function getAdsBanner(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $adsBanner = AstrotalkInNews::query();
            if ($s = $req->input(key:'s')) {
                $adsBanner->whereRaw(sql:"channel LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $adsBanner->get(),
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
