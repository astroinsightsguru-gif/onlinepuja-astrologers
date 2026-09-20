<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\SystemFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Session\Session;

class SystemFlagController extends Controller
{

    public function getSystemFlag(Request $req)
{
    try {
        if (!isset($req['token'])) {
            $session = new Session();
            $req['token'] = $session?->get('token');
        }

        $systemFlag = SystemFlag::all();

        // ✅ Convert only image/video file paths in 'value' to full URL
        foreach ($systemFlag as $flag) {
            if ($flag->value && preg_match('/\.(jpg|jpeg|png|gif|mp4|mov|avi)$/i', $flag->value)) {
                $flag->value = asset($flag->value);
            }
        }

        return response()->json([
            'recordList' => $systemFlag,
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


    public function getSubCategory(Request $req)
    {
        try {
            $systemFlag = DB::table('systemflag')->join('sub_category', 'sub_category.parent_id', '=', 'systemflag.id')->where('name', 'Category')->get();
            return response()->json([
                'recordList' => $systemFlag,
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

    public function getAppLanguage(Request $req)
    {
        try {
            $appLanguage = DB::table('systemflag')->where('name', 'Language')->get();
            $appLanguage = array_map('intval', explode(',', $appLanguage[0]->value));
            $language = DB::table('languages')
                ->whereIn('id', $appLanguage)
                ->get();
            return response()->json([
                'status' => 200,
                'message' => 'Get Language Successfully',
                'recordList' => $language,
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
