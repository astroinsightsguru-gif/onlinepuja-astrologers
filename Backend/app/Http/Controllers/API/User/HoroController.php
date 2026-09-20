<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\HororscopeSign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HoroController extends Controller
{

    //Get a all data of horoscope sign
    public function getHororscopeSign(Request $req)
{
    try {

        // Helper to format image URLs
        $formatImageUrl = function ($path) {
            if (empty($path)) return null;
            if (preg_match('/^https?:\/\//', $path)) return $path; 
            return asset($path); 
        };

        $hororscopeSign = HororscopeSign::query();
        if ($s = $req->input('s')) {
            $hororscopeSign->whereRaw("name LIKE '%" . $s . "%' ");
        }

        $hororscopeSignCount = $hororscopeSign->count();
        $hororscopeSign->orderBy('id', 'DESC');

        if ($req->startIndex >= 0 && $req->fetchRecord) {
            $hororscopeSign->skip($req->startIndex);
            $hororscopeSign->take($req->fetchRecord);
        }

        $hororscopeSignList = $hororscopeSign->get();

        // Convert image paths to full URLs
        foreach ($hororscopeSignList as $sign) {
            $sign->image = $formatImageUrl($sign->image ?? null);
        }

        return response()->json([
            'recordList' => $hororscopeSignList,
            'status' => 200,
            'totalRecords' => $hororscopeSignCount,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => false,
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}


    //Show only active hororscope sign
    public function activeHororscopeSigns()
    {
        try {
            $hororscopeSign = HororscopeSign::query()->where('isActive', '=', '1');
            return response()->json([
                'recordList' => $hororscopeSign->get(),
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
