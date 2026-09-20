<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Puja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminModel\SystemFlag;
use Carbon\Carbon;
use App\services\OneSignalService;
use Illuminate\Support\Facades\Auth;

class PujaController extends Controller
{
    public function astrologerPujaList(Request $request)
    {
       
        try {
            $pujas = Puja::where('astrologerId', $request->astrologerId)
            ->where('created_by', 'astrologer')
            ->where('puja_start_datetime', '>', Carbon::now())
            ->select('id', 'puja_title', 'puja_place', 'puja_price', 'long_description', 'puja_start_datetime', 'puja_end_datetime', 'isAdminApproved', 'puja_images', 'puja_duration')
            ->orderBy('id', 'DESC')
            ->get();
            return response()->json([
                'recordList' => $pujas,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

    }

    public function addAstrologerPuja(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'puja_title' => 'required|string|max:255',
                'long_description' => 'required|string',
                'puja_start_datetime' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        if (Carbon::parse($value) < Carbon::now()) {
                            $fail('The puja start datetime must be in the future.');
                        }
                    },
                ],
                'puja_duration' => 'required',
                'puja_place' => 'required|string|max:255',
                'puja_price' => 'required|numeric',
                'astrologerId' => 'required',
                'puja_images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,avif,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            DB::beginTransaction();

            // Generate slug
            $slug = Str::slug($request->puja_title, '-');
            $originalSlug = $slug;
            $counter = 1;
            
            $query = DB::table('pujas')->where('slug', $slug);
            if ($request->has('puja_id')) {
                $query->where('id', '!=', $request->puja_id);
            }
            
            while ($query->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
                $query = DB::table('pujas')->where('slug', $slug);
                if ($request->has('puja_id')) {
                    $query->where('id', '!=', $request->puja_id);
                }
            }

            // Handle image uploads - this will completely replace existing images
            $imagePaths = [];
            
            if ($request->hasFile('puja_images')) {
                // Delete old images if updating
                if ($request->has('puja_id')) {
                    $existingPuja = Puja::find($request->puja_id);
                    if ($existingPuja && $existingPuja->puja_images) {
                        foreach ($existingPuja->puja_images as $oldImage) {
                            if (file_exists(public_path($oldImage))) {
                                unlink(public_path($oldImage));
                            }
                        }
                    }
                }
                
                // Upload new images
                foreach ($request->file('puja_images') as $file) {
                    $name = time().rand().'.'.$file->getClientOriginalExtension();
                    $path = $file->move('public/storage/images/puja_images', $name);
                    $imagePaths[] = 'public/storage/images/puja_images/'.$name;
                }
            } elseif ($request->has('puja_id')) {
                // Keep existing images if no new images are provided
                $existingPuja = Puja::find($request->puja_id);
                if ($existingPuja && $existingPuja->puja_images) {
                    $imagePaths = $existingPuja->puja_images;
                }
            }

            $pujaStartDatetime = Carbon::parse($request->puja_start_datetime);
            $pujaEndDatetime = $pujaStartDatetime->copy()->addMinutes($request->puja_duration);

            // Prepare puja data
            $pujaData = [
                'astrologerId' => $request->astrologerId, 
                'puja_title' => $request->puja_title,
                'slug' => $slug,
                'puja_price' => $request->puja_price,
                'long_description' => $request->long_description,
                'puja_start_datetime' => $request->puja_start_datetime,
                'puja_end_datetime' => $pujaEndDatetime,
                'puja_duration' => $request->puja_duration,
                'puja_place' => $request->puja_place,
                'puja_images' => $imagePaths,
                'created_by' => 'astrologer',
            ];

            // Determine if we're updating or creating
            $identifier = $request->has('puja_id') 
                ? ['id' => $request->puja_id, 'astrologerId' => $request->astrologerId]
                : ['astrologerId' => $request->astrologerId, 'puja_title' => $request->puja_title];

            $puja = Puja::updateOrCreate($identifier, $pujaData);

            DB::commit();

            return response()->json([
                'message' => $request->has('puja_id') ? 'Puja Updated Successfully' : 'Puja Added Successfully',
                'recordList' => $puja,
                'status' => 200,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function deleteAstrologerPuja(Request $request)
    {
       
        try {
            $puja = Puja::where('id', $request->id)
            ->where('astrologerId', $request->astrologerId)
            ->where('created_by','astrologer')
            ->firstOrFail();
            if($puja){
                
                $puja->delete();
                return response()->json([
                    "status" => 200,
                    "message" => 'Puja deleted successfully',
                ]);
            }else{
                return response()->json([
                    "status" => 400,
                    "message" => 'No Puja Found',
                ]);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

    }

    public function sendPujatoUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'astrologerId' => 'required',
                'userId' => 'required',
                'puja_id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $puja = Puja::where('id', $request->puja_id)
            ->where('astrologerId', $request->astrologerId)
            ->where('created_by','astrologer')
            ->firstOrFail();
        
            if($puja){
                if($puja->isAdminApproved !="Approved"){
                    return response()->json([
                        "status" => 400,
                        "message" => 'Puja is not approved from admin',
                    ],400);
                }

                if (Carbon::parse($puja->puja_start_datetime)->lte(Carbon::now())) {
                    return response()->json([
                        "status" => 400,
                        "message" => 'Puja start date/time must be in the future.',
                    ], 400);
                }

                $existingPuja = DB::table('user_pujarequest_by_astrologers')
                ->where('astrologerId', $request->astrologerId)
                ->where('userId', $request->userId)
                ->where('puja_id', $request->puja_id)
                ->first();

                if ($existingPuja) {
                    return response()->json([
                    "status" => 400,
                        'message' => 'This puja is already suggested to user.'
                    ],400);
                }

                $pujaData = [
                    'astrologerId' => $request->astrologerId, 
                    'puja_id' => $request->puja_id,
                    'userId' => $request->userId,
                    'puja_start_datetime' => $puja->puja_start_datetime,
                    'puja_end_datetime' => $puja->puja_end_datetime,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                DB::table('user_pujarequest_by_astrologers')->insert($pujaData);

                $userDeviceDetail = DB::table('user_device_details as device')
                ->JOIN('users', 'users.id', '=', 'device.userId')
                ->WHERE('users.id', '=', $request->userId)
                ->SELECT('device.*', 'users.id as userId', 'users.name')
                ->get();

                $astrologer = DB::table('astrologers')->where('id',$request->astrologerId)->first();

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {

                    $oneSignalService = new OneSignalService();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                        $notification = [
                        'title' => 'Puja Received',
                        'body' => ['description' => 'Hey ' . $userDeviceDetail[0]->name . ', you have received puja from '. $astrologer->name],
                        ];
                        $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

                        $notification = array(
                        'userId' => $userDeviceDetail[0]->userId,
                        'title' => 'Puja Received',
                        'description' => 'Hey ' . $userDeviceDetail[0]->name . ', you have received puja from '. $astrologer->name,
                        'notificationId' => null,
                        'createdBy' => $userDeviceDetail[0]->userId,
                        'modifiedBy' => $userDeviceDetail[0]->userId,
                        'notification_type' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
    
                    );
                        DB::table('user_notifications')->insert($notification);
                }

                return response()->json([
                    "status" => 200,
                    "message" => 'Puja sent successfully',
                ]);
            }else{
                return response()->json([
                    "status" => 400,
                    "message" => 'No Puja Found',
                ],400);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    // Show Suggested Puja to user
    public function suggestedAstrologerPuja(Request $request)
    {
        
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $astrologerPujaList = Puja::join('user_pujarequest_by_astrologers','user_pujarequest_by_astrologers.puja_id','pujas.id')
            ->where('user_pujarequest_by_astrologers.userId',$id)
            ->join('astrologers','astrologers.id','user_pujarequest_by_astrologers.AstrologerId')
            ->select('pujas.*','astrologers.name as astrologername','astrologers.slug as astrologerslug','user_pujarequest_by_astrologers.id as puja_suggested_id')
            ->where('pujas.puja_start_datetime', '>=', Carbon::now())
            ->get();
            
            return response()->json([
                "status" => 200,
                "recordList" => $astrologerPujaList,
                "message" => 'Puja fetch successfully',
            ]);
        

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
       
    }


    public function deleteSuggestedPuja(Request $request)
    {
       
        try {
            $puja = DB::table('user_pujarequest_by_astrologers')
            ->where('id',$request->id)
            ->delete();

            if ($puja) {
                return response()->json([
                    "status" => 200,
                    "message" => 'Suggested puja deleted successfully',
                ]);
            } else {
                return response()->json([
                    "status" => 400,
                    "message" => 'No suggested puja found with this ID',
                ], 400);
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
