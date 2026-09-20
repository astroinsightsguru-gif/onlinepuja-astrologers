<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\TrainingVideo;
use Illuminate\Http\Request;

class TrainingVideoController extends Controller
{
    public function getTrainingVideo(Request $request)
    {
        try {

            $getVideo = TrainingVideo::where('isActive', 1)->get();
            $Count = TrainingVideo::where('isActive', 1)->count();
            return response()->json([
                'recordList' => $getVideo,
                'status' => 200,
                'totalRecords' => $Count,
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
