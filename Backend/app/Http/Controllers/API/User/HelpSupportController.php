<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\HelpSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HelpSupportController extends Controller
{
    //Get all the help and support
    public function getHelpSupport(Request $req)
    {
        try {
            $helpSupport = HelpSupport::query();
            if ($s = $req->input(key:'s')) {
                $helpSupport->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $helpSupport->skip($req->startIndex);
                $helpSupport->take($req->fetchRecord);
            }
            $helpSupportCount = HelpSupport::query();
            $helpSupport = $helpSupport->get();
            if ($helpSupport && count($helpSupport) > 0) {
                foreach ($helpSupport as $support) {
                    $subCategory = DB::table('help_support_quations')->where('helpSupportId', '=', $support->id)->get();
                    if ($subCategory && count($subCategory) > 0) {
                        $support->isSubCategory = true;
                    } else {
                        $support->isSubCategory = false;
                    }
                }
            }
            return response()->json([
                'recordList' => $helpSupport,
                'status' => 200,
                'totalRecord' => $helpSupportCount->count(),
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
