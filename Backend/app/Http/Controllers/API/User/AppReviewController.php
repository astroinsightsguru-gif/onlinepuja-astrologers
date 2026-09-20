<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\AppReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppReviewController extends Controller
{
    //Add a App review

    public function addAppReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'review',
                'appId',
            );
            $validator = Validator::make($data, [
                'review' => 'required',
                'appId' => 'required',
            ]);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $appReview = AppReview::create([
                'userId' => $id,
                'review' => $req->review,
                'appId' => $req->appId,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);
            return response()->json([
                'message' => 'App review add sucessfully',
                'recordList' => $appReview,
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

    //Get a App review

    public function getAppReview(Request $req)
    {
        try {
			
			if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $appReview = DB::table('app_reviews')
                ->join('users', 'app_reviews.userId', '=', 'users.id')
                //->where('app_reviews.appId', '=', $req->appId)
				   ->where('app_reviews.userId', '=', $id)
                ->select('app_reviews.review', 'users.profile', 'users.name', 'users.location')
                ->get();
            return response()->json([
                'recordList' => $appReview,
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
