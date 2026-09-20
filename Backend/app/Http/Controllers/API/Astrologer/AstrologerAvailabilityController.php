<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class AstrologerAvailabilityController extends Controller
{
    //Add a astrologer available time
    public function addAstrologerAvailable(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'astrologerId',
                'fromTime',
                'toTime',
                'day',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            //Add a available time of astrologer
            for ($i = 0; $i < 2; $i++) {
                $astrologerAvailability = AstrologerAvailability::create([
                    'astrologerId' => $req->astrologerId,
                    'fromTime' => $req->fromTime,
                    'toTime' => $req->toTime,
                    'day' => $req->day,
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                ]);
            }

            return response()->json([
                'message' => 'Astrologer available time add sucessfully',
                'recordList' => $astrologerAvailability,
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

    public function addAstrologerAvailability(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            if ($req->astrologerAvailability && count($req->astrologerAvailability) > 0) {
                foreach ($req->astrologerAvailability as $astrologer) {
                    foreach ($astrologer['time'] as $availability) {
                        AstrologerAvailability::create([
                            'astrologerId' => $req->astrologerId,
                            'day' => $astrologer['day'],
                            'fromTime' => $availability['fromTime'],
                            'toTime' => $availability['toTime'],
                            'createdBy' => $id,
                            'modifiedBy' => $id,
                        ]);
                    }
                }
                return response()->json([
                    'message' => 'Astrologer availability add sucessfully',
                    'recordList' => [],
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function updateAstrologerAvailability(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            $astrologerAvailability = DB::table('astrologer_availabilities')
                ->where('astrologerId', '=', $req->astrologerId);
            if ($astrologerAvailability) {
                $astrologerAvailability->delete();
                if ($req->astrologerAvailability && count($req->astrologerAvailability) > 0) {
                    foreach ($req->astrologerAvailability as $astrologer) {
                        foreach ($astrologer['time'] as $availability) {
                            $astrologerAvailability = AstrologerAvailability::create([
                                'astrologerId' => $req->astrologerId,
                                'day' => $astrologer['day'],
                                'fromTime' => $availability['fromTime'],
                                'toTime' => $availability['toTime'],
                                'createdBy' => $id,
                                'modifiedBy' => $id,
                            ]);
                        }
                    }
                    return response()->json([
                        'message' => 'Astrologer Availability Update sucessfully',
                        'recordList' => [],
                        'status' => 200,
                    ], 200);
                }

            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstrologerAvailability(Request $req)
    {
        try {
            $data = $req->only(
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $astrologerAvailability = DB::table('astrologer_availabilities')
                ->where('astrologerId', '=', $req->astrologerId)
                ->get();
            $working = [];
            if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                $day = [];

                $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($day as $days) {
                    $day = array(
                        'day' => $days,
                    );
                    $currentday = $days;
                    $result = array_filter(json_decode($astrologerAvailability), function ($event) use ($currentday) {
                        return $event->day === $currentday;
                    });
                    $ti = [];

                    foreach ($result as $available) {
                        $time = array(
                            'fromTime' => $available->fromTime,
                            'toTime' => $available->toTime,
                        );
                        array_push($ti, $time);

                    }
                    $weekDay = array(
                        'day' => $days,
                        'time' => $ti,
                    );
                    array_push($working, $weekDay);
                }

            }
            return response()->json([
                'recordList' => $working,
                'status' => 200,
                'message' => 'Astrolgoer Availabilty get successfully',
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
