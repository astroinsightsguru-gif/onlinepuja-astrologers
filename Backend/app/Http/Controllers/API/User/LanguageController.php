<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;


class LanguageController extends Controller
{
    //Get a language
    public function getLanguages()
    {
        try {

            $language = Language::all();
            return response()->json([
                'recordList' => $language,
                'status' => 200,
            ],200);
        } catch (\Exception$e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ],500);
        }
    }
}
