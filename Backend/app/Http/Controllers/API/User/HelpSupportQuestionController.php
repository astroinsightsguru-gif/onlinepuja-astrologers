<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\HelpSupportQuation;
use App\Models\AdminModel\HelpSupportQuationAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HelpSupportQuestionController extends Controller
{
    // Get all the help and support question
    public function getHelpSupportQuestion(Request $req)
    {
        try {
            $helpSupportQuestion = HelpSupportQuation::where('helpSupportId', $req->helpSupportId)->get();
            if ($helpSupportQuestion && count($helpSupportQuestion) > 0) {
                foreach ($helpSupportQuestion as $support) {
                    $subCategory = HelpSupportQuationAnswer::where('helpSupportQuationId', $support->id)->get();
                    if ($subCategory && count($subCategory) > 0) {
                        $support->isSubCategory = true;
                    } else {
                        $support->isSubCategory = false;
                    }
                }
            }
            return response()->json([
                'recordList' => $helpSupportQuestion,
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

    public function getHelpSupportSubSubCategory(Request $req)
    {
        try {
            $helpSupportSubSubCategory = DB::table('help_support_quation_answers')
                ->where('helpSupportQuationId', $req->helpSupportQuationId)
                ->get();
            return response()->json([
                'recordList' => $helpSupportSubSubCategory,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
