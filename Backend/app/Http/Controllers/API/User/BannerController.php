<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BannerController extends Controller
{
  
    //Get all the banners
    public function getBanner(Request $req)
    {
        try {
            $banner = DB::table('banners')
                ->leftjoin('banner_types', 'banners.bannerTypeId', '=', 'banner_types.id')
                ->select('banner_types.name as bannerType', 'banner_types.appId', 'banners.*');
            $banner->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $banner->skip($req->startIndex);
                $banner->limit($req->fetchRecord);
            }

            $bannerCount = DB::table('banners')
                ->leftjoin('banner_types', 'banners.bannerTypeId', '=', 'banner_types.id')
                ->count();

            return response()->json([
                'recordList' => $banner->get(),
                'status' => 200,
                'totalRecords' => $bannerCount,
            ], 200);
        } catch (\Exception$e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getBannerType()
    {
        try {
            $bannerType = DB::table('banner_types')->where('isActive', '=', 1)->get();
            return response()->json([
                'message' => 'Banner Type Get sucessfully',
                'recordList' => $bannerType,
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
