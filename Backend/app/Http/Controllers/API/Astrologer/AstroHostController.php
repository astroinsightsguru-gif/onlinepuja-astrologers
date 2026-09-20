<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrohost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AstroHostController extends Controller
{
    public function addAstrohost(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId',
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

            $astrohost = Astrohost::query()
                ->where('astrologerId', '=', $req->astrologerId)
                ->get();
            error_log($astrohost);
            if ($astrohost && count($astrohost) > 0) {
                $astrohost[0]->hostId = $req->hostId;
                $astrohost[0]->update();
            } else {
                //Create a new skill
                $astrohost = Astrohost::create([
                    'astrologerId' => $req->astrologerId,
                    'hostId' => $req->hostId,
                ]);
            }

            return response()->json([
                'message' => 'Astrohost add sucessfully',
                'recordList' => $astrohost,
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

    public function getAstrohost(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId',
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

            $astrohost = AStrohost::query()
                ->where('astrologerId', '=', $req->astrologerId)
                ->get();
            return response()->json([
                'message' => 'Astrohost get sucessfully',
                'recordList' => $astrohost,
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
