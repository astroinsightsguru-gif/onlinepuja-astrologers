<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\OrderAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OrderAddressController extends Controller
{
    //Add order address
    public function addOrderAddress(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }



            $data = $req->only(
                'userId',
                'name',
                'phoneNumber',
                'phoneNumber2',
                'flatNo',
                'locality',
                'landmark',
                'city',
                'state',
                'country',
                'pincode',
                'countryCode'
            );

            //Validate the data
            $validator = Validator::make($data, [
                'userId' => 'required',
                'name' => 'required',
                'phoneNumber' => 'required',
                'flatNo' => 'required',
                'locality' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'pincode' => 'required',
                'countryCode' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create astrologer assistant
            $orderAddress = OrderAddress::create([
                'userId' => $req->userId,
                'name' => $req->name,
                'phoneNumber' => $req->phoneNumber,
                'phoneNumber2' => $req->phoneNumber2,
                'flatNo' => $req->flatNo,
                'locality' => $req->locality,
                'landmark' => $req->landmark,
                'city' => $req->city,
                'state' => $req->state,
                'country' => $req->country,
                'pincode' => $req->pincode,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'countryCode' => $req->countryCode,
                'alternateCountryCode' => $req->alternateCountryCode,
            ]);

            return response()->json([
                'message' => 'Order address add sucessfully',
                'recordList' => $orderAddress,
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

    //Update order address
    public function updateOrderAddress(Request $req, $id)
    {
        try {
            $req->validate = ([
                'userId',
                'name',
                'phoneNumber',
                'phoneNumber2',
                'flatNo',
                'locality',
                'landmark',
                'city',
                'state',
                'country',
                'pincode',
                'countryCode',
            ]);

            $orderAddress = OrderAddress::find($id);
            if ($orderAddress) {
                $orderAddress->userId = $req->userId;
                $orderAddress->name = $req->name;
                $orderAddress->phoneNumber = $req->phoneNumber;
                $orderAddress->phoneNumber2 = $req->phoneNumber2;
                $orderAddress->flatNo = $req->flatNo;
                $orderAddress->locality = $req->locality;
                $orderAddress->landmark = $req->landmark;
                $orderAddress->city = $req->city;
                $orderAddress->state = $req->state;
                $orderAddress->country = $req->country;
                $orderAddress->pincode = $req->pincode;
                $orderAddress->countryCode = $req->countryCode;
                $orderAddress->alternateCountryCode = $req->alternateCountryCode;
                $orderAddress->update();
                return response()->json([
                    'message' => 'Order address update sucessfully',
                    'recordList' => $orderAddress,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Order address is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Get the details of order address
    public function getOrderAddress(Request $req)
    {
        try {
            $orderAddress = OrderAddress::query()
                ->where('userId', '=', $req->userId);
            if ($s = $req->input(key:'s')) {
                $orderAddress->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $orderAddress->get(),
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
