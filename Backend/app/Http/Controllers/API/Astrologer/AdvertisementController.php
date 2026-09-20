<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\UserModel\AstrotalkInNews;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    //Get astrologer advertisement
    public function getAdvertisement(Request $req)
    {
        try {
            $advertisement = AstrotalkInNews::query();
            if ($s = $req->input(key:'s')) {
                $advertisement->whereRaw(sql:"channel LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $advertisement->get(),
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
