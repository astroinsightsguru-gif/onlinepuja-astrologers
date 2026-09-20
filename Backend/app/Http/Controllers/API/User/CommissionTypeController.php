<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\CommissionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommissionTypeController extends Controller
{
    //Get all the commissionn type
    public function getCommissionType(Request $req)
    {
        try {
            $commissionType = CommissionType::query();
            if ($s = $req->input(key: 's')) {
                $commissionType->whereRaw(sql: "name LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $commissionType->get(),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500
            ],500);
        }
    }


  
}
