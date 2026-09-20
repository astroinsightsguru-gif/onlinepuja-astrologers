<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\ReportType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReportTypeController extends Controller
{

    //Get all report type data
    public function getReportTypes(Request $req)
{
    try {
        $reportType = ReportType::query();
        if (!empty($req->searchString)) {
            $reportType->where('title', 'LIKE', '%' . $req->searchString . '%');
        }

        $reportType->where('isActive', true);

        $reportTypeCount = $reportType->count();

        if (isset($req->startIndex) && isset($req->fetchRecord)) {
            $reportType->skip($req->startIndex)->take($req->fetchRecord);
        }

        $recordList = $reportType->get();

        $recordList->transform(function ($item) {
            if (!empty($item->reportImage)) {
                if (!preg_match('/^https?:\/\//', $item->reportImage)) {
                    $item->reportImage = asset($item->reportImage);
                }
            } else {
                $item->reportImage = asset('images/default-report.png');
            }
            return $item;
        });

        return response()->json([
            'recordList' => $recordList,
            'status' => 200,
            'totalRecords' => $reportTypeCount,
        ]);
    } catch (\Exception $e) {
        // ❌ Error handling
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'status' => 500,
        ]);
    }
}


}
