<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    //Show only active skill
    public function activeSkills()
    {
        try {
            $skill = Skill::query()->where('isActive', '=', '1');
            return response()->json([
                'recordList' => $skill->get(),
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

    public function getAppSkills(Request $req)
    {
        try {
            $skill = Skill::query();
            $skill->where('isActive', '=', true);
            $skill->where('isDelete', '=', false);
            if ($s = $req->input(key:'s')) {
                $skill->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $skill->skip($req->startIndex);
                $skill->take($req->fetchRecord);
            }
            $skillCount = Skill::query();
            return response()->json([
                'recordList' => $skill->get(),
                'status' => 200,
                'totalRecord' => $skillCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
