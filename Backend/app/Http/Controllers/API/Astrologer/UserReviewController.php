<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\UserReview;
use App\Models\UserModel\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserReviewController extends Controller
{
    //Add review
    public function addUserReview(Request $req, User $user, Astrologer $astrologer)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'userId',
                'rating',
                'review',
                'astrologerId',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'rating' => 'required',
                'review' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create ticket
            $userReview = UserReview::create([
                'userId' => $id,
                'rating' => $req->rating,
                'review' => $req->review,
                'astrologerId' => $req->astrologerId,
                'astromallProductId' => $req->astromallProductId,
                'appId' => $req->appId,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'isPublic' => $req->isPublic,
            ]);

            return response()->json([
                'message' => 'User review add sucessfully',
                'recordList' => $userReview,
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

    // //Get a user review

    public function getUserReview()
    {
        try {
            $userReview = DB::table('user_reviews')
                ->join('users', 'user_reviews.userId', '=', 'users.id')
                ->join('astromall_products', 'user_reviews.astromallProductId', '=', 'astromall_products.id')
                ->select('users.name', 'users.profile')
                ->get();
            return response()->json([
                'recordList' => $userReview,
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

    public function getAstrologerUserReview(Request $req)
    {
        try {

            $astrologerUserReview = DB::table('user_reviews')
                ->join('users', 'user_reviews.userId', '=', 'users.id')
                ->join('astrologers', 'user_reviews.astrologerId', '=', 'astrologers.id')
                ->where('user_reviews.userId', '=', $req->userId);
            if ($req->userId) {
                $astrologerUserReview = $astrologerUserReview->where('user_reviews.userId', '=', $req->userId);
            }
            $astrologerUserReview = $astrologerUserReview->select('users.name', 'users.profile', 'user_reviews.*');
            $astrologerUserReview = $astrologerUserReview->get();
            return response()->json([
                'recordList' => $astrologerUserReview,
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

    public function getUserHistoryReview(Request $req)
    {
        {
            try {

                $astrologerUserReview = DB::table('user_reviews')
                    ->join('users', 'user_reviews.userId', '=', 'users.id')
                    ->join('astrologers', 'user_reviews.astrologerId', '=', 'astrologers.id')
                    ->where('user_reviews.astrologerId', '=', $req->astrologerId);
                if ($req->userId) {
                    $astrologerUserReview = $astrologerUserReview->where('user_reviews.userId', '=', $req->userId);
                }
                $astrologerUserReview = $astrologerUserReview->select('users.name', 'users.profile', 'user_reviews.*');
                $astrologerUserReview = $astrologerUserReview->get();
                return response()->json([
                    'recordList' => $astrologerUserReview,
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

    //Update user review
    public function updateUserReview(Request $req, $id)
    {
        try {
            $req->validate = ([
                'userId',
                'rating',
                'review',
                'astrologerId',
            ]);

            $userReview = UserReview::find($id);
            if ($userReview) {
                $userReview->userId = $req->userId;
                $userReview->rating = $req->rating;
                $userReview->review = $req->review;
                $userReview->astrologerId = $req->astrologerId;
                $userReview->isPublic = $req->isPublic;
                $userReview->update();
                return response()->json([
                    'message' => 'User review update sucessfully',
                    'recordList' => $userReview,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User review is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $req->isPublic,
                'status' => 500,
            ], 500);
        }
    }

    //Delete user review
    public function deleteUserReview($id)
    {
        try {
            $userReview = UserReview::find($id);
            if ($userReview) {
                $userReview->delete();
                return response()->json([
                    'message' => 'User review delete Sucessfully',
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User review is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


}
