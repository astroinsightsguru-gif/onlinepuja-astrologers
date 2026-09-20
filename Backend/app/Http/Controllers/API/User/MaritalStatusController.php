<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\MaritalStatus;
use Illuminate\Http\Request;

class MaritalStatusController extends Controller
{
    //Get a Marital status
    public function getMaritalStatus(Request $req)
    {
        try {
            $maritalStatus = MaritalStatus::query();
            if ($s = $req->input(key: 's')) {
                $maritalStatus->whereRaw(sql: "maritalStatus LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $maritalStatus->get(),
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
