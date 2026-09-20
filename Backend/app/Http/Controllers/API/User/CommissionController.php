<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CommissionController extends Controller
{

    //Get all the Commissionn
    public function getCommission(Request $req)
    {
        try {
            $commission = DB::table('commissions')
                ->join('astrologers', 'astrologers.id', '=', 'commissions.astrologerId')
                ->join('commission_types', 'commission_types.id', '=', 'commissions.commissionTypeId')
                ->select('astrologers.name as astrolgoerName', 'astrologers.contactNo', 'commissions.*', 'commission_types.name as commssionType');

            $commissionCount = DB::table('commissions')
                ->join('astrologers', 'astrologers.id', '=', 'commissions.astrologerId')
                ->join('commission_types', 'commission_types.id', '=', 'commissions.commissionTypeId');

            if ($req->astrologerId) {
                $commission->where('commissions.astrologerId', '=', $req->astrologerId);
                $commissionCount->where('commissions.astrologerId', '=', $req->astrologerId);
            }

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $commission->skip($req->startIndex);
                $commission->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $commission->get(),
                'status' => 200,
                'totalRecords'=>$commissionCount->count()
            ], 200);
        } catch (\Exception$e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

}
