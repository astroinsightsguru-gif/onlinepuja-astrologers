<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologyVideo;
use Illuminate\Http\Request;

class AdsVideoController extends Controller
{
    //Get an adsVideo
    public function getAdsVideo(Request $req)
    {
        try {
            $adsVideo = AstrologyVideo::query();
            $adsVideoCount = $adsVideo->count();
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $adsVideo = $adsVideo->skip($req->startIndex);
                $adsVideo = $adsVideo->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $adsVideo->get(),
                'status' => 200,
                'totalRecords' => $adsVideoCount,
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
