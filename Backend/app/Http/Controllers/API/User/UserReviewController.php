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
            $userReview = UserReview::updateOrCreate(
            [
                'userId' => $id,
                'astrologerId' => $req->astrologerId,
            ],
            [
                'rating' => $req->rating,
                'review' => $req->review,
                'astromallProductId' => $req->astromallProductId,
                'appId' => $req->appId,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'isPublic' => $req->isPublic,
            ]
        );

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
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $reviews = DB::table('user_reviews as urv')
                ->join('users as u', 'u.id', '=', 'urv.userId')
                ->where('urv.astrologerId', '=', $req->astrologerId)
                ->select('urv.*', 'u.name as userName', 'u.profile')
                ->get();

            $blockReview = DB::table('blockuserreview')
                ->where('userId', '=', $id)
                ->where('isBlocked', '=', true)
                ->get();
            $review = [];
            if ($blockReview && count($blockReview) > 0) {
                for ($i = 0; $i < count($reviews); $i++) {
                    $reviewId = $reviews[$i]->id;
                    $block = array_filter(json_decode(json_encode($blockReview)), function ($event) use ($reviewId) {
                        return $event->reviewId == $reviewId;
                    });
                    if (!$block) {
                        array_push($review, $reviews[$i]);
                    }
                }
            } else {
                $review = $reviews;
            }

            return response()->json([
                'recordList' => $review,
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
                $astrologerUserReview = DB::table('user_reviews as ur')
                    ->join('users', 'ur.userId', '=', 'users.id')
                    ->join('astrologers', 'ur.astrologerId', '=', 'astrologers.id')
                    ->where('ur.astrologerId', '=', $req->astrologerId);
                if ($req->userId) {
                    $astrologerUserReview = $astrologerUserReview->where('ur.userId', '=', $req->userId);
                }
                $astrologerUserReview = $astrologerUserReview->select('users.name', 'users.profile', 'ur.*');
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
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $userId = Auth::guard('api')->user()->id;
            }
            $userReview = UserReview::find($id);
            if ($userReview) {
                $userReview->userId = $userId;
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

    public function replyAstrologerReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'reviewId',
                'reply'
            );

            //Validate the data
            $validator = Validator::make($data, [
                'reviewId',
                'reply',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $review = UserReview::find($req->reviewId);
            if ($review) {
                $review->reply = $req->reply;
                $review->update();
            }
            return response()->json([
                'message' => 'reply Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function blockUserReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = array(
                'userId' => $id,
                'isBlocked' => $req->isBlocked,
                'isReported' => $req->isReported,
                'reviewId' => $req->id,
            );
            DB::table('blockuserreview')->insert($data);
            $userReview = DB::table('user_reviews')->where('id', '=', $req->id)->get();
            return response()->json([
                'message' => 'User review Block Successfully',
                'status' => 200,
                'recordList' => $userReview[0]->astrologerId ? $userReview[0]->astrologerId : $userReview[0]->astromallProductId,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getBlockReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $reviews = DB::table('user_reviews as ur')
                ->join('users as u', 'u.id', '=', 'ur.userId')
                ->where('astromallProductId', '=', $req->astromallProductId)
                ->select('ur.*', 'u.name as userName', 'u.profile')
                ->get();
            $blockReview = DB::table('blockuserreview')
                ->where('userId', '=', $id)
                ->get();
            $review = [];
            if ($blockReview && count($blockReview) > 0) {
                for ($i = 0; $i < count($reviews); $i++) {
                    $reviewId = $reviews[$i]->id;
                    $block = array_filter(json_decode(json_encode($blockReview)), function ($event) use ($reviewId) {
                        return $event->reviewId == $reviewId;
                    });
                    if (!$block) {
                        array_push($review, $reviews[$i]);
                    }
                }
            } else {
                $review = $reviews;
            }
            return response()->json([
                'message' => 'Get User review Successfully',
                'status' => 200,
                'recordList' => $review,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstrologerBlockReview(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $reviews = DB::table('user_reviews')
                ->join('users', 'users.id', '=', 'user_reviews.userId')
                ->where('astrologerId', '=', $req->astrologerId)
                ->select('user_reviews.*', 'users.name as userName', 'users.profile')
                ->get();
            $blockReview = DB::table('blockuserreview')
                ->where('userId', '=', $id)
                ->get();
            $review = [];
            if ($blockReview && count($blockReview) > 0) {
                for ($i = 0; $i < count($reviews); $i++) {
                    $reviewId = $reviews[$i]->id;
                    $block = array_filter(json_decode(json_encode($blockReview)), function ($event) use ($reviewId) {
                        return $event->reviewId == $reviewId;
                    });
                    if (!$block) {
                        array_push($review, $reviews[$i]);
                    }
                }
            } else {
                $review = $reviews;
            }
            return response()->json([
                'message' => 'Get User review Successfully',
                'status' => 200,
                'recordList' => $review,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstrologerUserReviewForAdmin(Request $req)
    {
        try {
            $reviews = DB::table('user_reviews')
                ->join('users', 'users.id', '=', 'user_reviews.userId')
                ->join('astrologers', 'astrologers.id', '=', 'user_reviews.astrologerId')
                ->select('user_reviews.*', 'users.name as userName', 'users.profile', 'users.contactNo', 'astrologers.name as astrologerName', 'astrologers.contactNo as astrologerContactNo')
                ->whereNotNull('user_reviews.astrologerId');
            $reviewsCount = $reviews->count();
            if ($req->startIndex && $req->fetchRecord) {
                $reviews->skip($req->startIndex);
                $reviews->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $reviews->get(),
                'status' => 200,
                'totalRecords' => $reviewsCount,
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
