<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AstrologerAssistantController extends Controller
{
    //Add astrologer assistant
    public function addAstrologerAssistant(Request $req)
    {
        try {

            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'astrologerId',
                'name',
                'email',
                'contactNo',
                'gender',
                'birthdate',
                'assistantPrimarySkillId',
                'assistantAllSkillId',
                'assistantLanguageId',
                'experienceInYears',
                'profile',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
                'name' => 'required',
                'contactNo' => 'required',
                'gender' => 'required',
                'birthdate' => 'required',
                'assistantPrimarySkillId' => 'required',
                'assistantAllSkillId' => 'required',
                'assistantLanguageId' => 'required',
                'experienceInYears' => 'required',
            ]);

            // //Send failed response if request is not valid
            if ($validator->fails()) {
                error_log($req);
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $astrologerAssistant = AstrologerAssistant::create([
                'astrologerId' => $req->astrologerId,
                'name' => $req->name,
                'email' => $req->email,
                'contactNo' => $req->contactNo,
                'gender' => $req->gender,
                'birthdate' => $req->birthdate,
                'primarySkill' => implode(',', array_column($req->assistantPrimarySkillId, 'id')),
                'allSkill' => implode(',', array_column($req->assistantAllSkillId, 'id')),
                'languageKnown' => implode(',', array_column($req->assistantLanguageId, 'id')),
                'experienceInYears' => $req->experienceInYears,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);
            if ($req->profile) {
                $time = Carbon::now()->timestamp;
                if (Str::contains($req->profile, 'storage')) {
                    $path = $req->profile;
                } else {
                    $destinationpath = 'public/storage/images/';
                    $imageName = 'astrologerAssistant_' . $astrologerAssistant->id . $time;
                    $path = $destinationpath . $imageName . '.png';
                    File::delete($path);
                    file_put_contents($path, base64_decode($req->profile));
                }
            } else {
                $path = null;
            }
            $astrologerAssistant->profile = $path;
            $astrologerAssistant->update();

            if ($astrologerAssistant) {
                $astrologerAssistant->allSkill = array_map('intval', explode(',', $astrologerAssistant->allSkill));
                $astrologerAssistant->primarySkill = array_map('intval', explode(',', $astrologerAssistant->primarySkill));
                $astrologerAssistant->languageKnown = array_map('intval', explode(',', $astrologerAssistant->languageKnown));
                $allSkill = DB::table('skills')
                    ->whereIn('id', $astrologerAssistant->allSkill)
                    ->select('name', 'id')
                    ->get();
                $primarySkill = DB::table('skills')
                    ->whereIn('id', $astrologerAssistant->primarySkill)
                    ->select('name', 'id')
                    ->get();
                $languageKnown = DB::table('languages')
                    ->whereIn('id', $astrologerAssistant->languageKnown)
                    ->select('languageName', 'id')
                    ->get();
                $astrologerAssistant->allSkill = $allSkill;
                $astrologerAssistant->primarySkill = $primarySkill;
                $astrologerAssistant->languageKnown = $languageKnown;
            }
            return response()->json([
                'message' => 'Astrologer assistant add sucessfully',
                'recordList' => $astrologerAssistant,
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

    //Get astrologer assistant
    public function getAstrologerAssistant(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId',
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $astrologerAssistant = DB::Table('astrologer_assistants')
                ->where('astrologerId', '=', $req->astrologerId)
                ->where('isDelete', '=', false)
                ->get();

            if ($s = $req->input(key:'s')) {
                $astrologerAssistant->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            if ($astrologerAssistant && count($astrologerAssistant) > 0) {
                foreach ($astrologerAssistant as $assistant) {
                    $assistant->allSkill = array_map('intval', explode(',', $assistant->allSkill));
                    $assistant->primarySkill = array_map('intval', explode(',', $assistant->primarySkill));
                    $assistant->languageKnown = array_map('intval', explode(',', $assistant->languageKnown));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $assistant->allSkill)
                        ->select('name', 'id')
                        ->get();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $assistant->primarySkill)
                        ->select('name', 'id')
                        ->get();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $assistant->languageKnown)
                        ->select('languageName', 'id')
                        ->get();
                    $assistant->allSkill = $allSkill;
                    $assistant->primarySkill = $primarySkill;
                    $assistant->languageKnown = $languageKnown;
                }
            }
            return response()->json([
                'recordList' => $astrologerAssistant,
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

    //Update astrologer assistant
    public function updateAstrologerAssistant(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $req->validate = ([
                'astrologerId',
                'name',
                'email',
                'contactNo',
                'gender',
                'birthdate',
                'primarySkill',
                'allSkill',
                'languageKnown',
                'experienceInYears',

            ]);

            $astrologerAssistant = AstrologerAssistant::find($req->id);

            if ($req->profile) {
                if (Str::contains($req->profile, 'storage')) {
                    $path = $req->profile;
                } else {
                    $time = Carbon::now()->timestamp;
                    $destinationpath = 'public/storage/images/';
                    $imageName = 'astrologerAssistant_' . $req->id . $time;
                    $path = $destinationpath . $imageName . '.png';
                    File::delete($astrologerAssistant->profile);
                    file_put_contents($path, base64_decode($req->profile));
                }
            } else {
                $path = null;
            }
            if ($astrologerAssistant) {
                $astrologerAssistant->astrologerId = $req->astrologerId;
                $astrologerAssistant->name = $req->name;
                $astrologerAssistant->email = $req->email;
                $astrologerAssistant->contactNo = $req->contactNo;
                $astrologerAssistant->gender = $req->gender;
                $astrologerAssistant->birthdate = $req->birthdate;
                $astrologerAssistant->primarySkill = implode(',', array_column($req->assistantPrimarySkillId, 'id'));
                $astrologerAssistant->allSkill = implode(',', array_column($req->assistantAllSkillId, 'id'));
                $astrologerAssistant->languageKnown = implode(',', array_column($req->assistantLanguageId, 'id'));
                $astrologerAssistant->experienceInYears = $req->experienceInYears;
                $astrologerAssistant->profile = $path;
                $astrologerAssistant->update();
                if ($astrologerAssistant) {
                    $astrologerAssistant->allSkill = array_map('intval', explode(',', $astrologerAssistant->allSkill));
                    $astrologerAssistant->primarySkill = array_map('intval', explode(',', $astrologerAssistant->primarySkill));
                    $astrologerAssistant->languageKnown = array_map('intval', explode(',', $astrologerAssistant->languageKnown));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $astrologerAssistant->allSkill)
                        ->select('name', 'id')
                        ->get();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $astrologerAssistant->primarySkill)
                        ->select('name', 'id')
                        ->get();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $astrologerAssistant->languageKnown)
                        ->select('languageName', 'id')
                        ->get();
                    $astrologerAssistant->allSkill = $allSkill;
                    $astrologerAssistant->primarySkill = $primarySkill;
                    $astrologerAssistant->languageKnown = $languageKnown;
                }
                return response()->json([
                    'message' => 'astrologer assistant update sucessfully',
                    'recordList' => $astrologerAssistant,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Astrologer assistant is not found',
                    'status' => 400,
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

    //Delete astrologer assistant
    public function deleteAstrologerAssistant(Request $req)
    {
        try {
            $astrologerAssistant = AstrologerAssistant::find($req->id);
            if ($astrologerAssistant) {
                $astrologerAssistant->delete();
                return response()->json([
                    'message' => 'Astrologer assistant delete Sucessfully',
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Astrologer assistant is not found',
                    'status' => 400,
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

    public function blockAstrologerAssistant(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $blockAssisant = array(
                'assistantId' => $req->assistantId,
                'userId' => $id,
            );
            DB::table('blockassistant')->insert($blockAssisant);
            return response()->json([
                'message' => 'astrologer assistant block sucessfully',
                'recordList' => $blockAssisant,
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

    public function unblockAstrologerAssistant(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            DB::table('blockassistant')
                ->where('userId', '=', $id)
                ->where('assistantId', '=', $req->assistantId)
                ->delete();
            return response()->json([
                'message' => 'astrologer assistant unblock sucessfully',
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
