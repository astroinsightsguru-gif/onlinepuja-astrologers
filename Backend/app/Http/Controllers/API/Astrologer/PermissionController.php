<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    //Add Permisssion
    public function addPermission(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only('name');

            //Validate the data
            $validator = Validator::make($data, [
                'name' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create a new permission
            $permission = Permission::create([
                'name' => $req->name,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            return response()->json([
                'message' => 'Permisssion add sucessfully',
                'recordList' => $permission,
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

    //Get all the permission
    public function getPermission(Request $req)
    {
        try {
            $permisssion = Permission::query();
            if ($s = $req->input(key:'s')) {
                $permisssion->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            return response()->json([
                'recordList' => $permisssion->get(),
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

    //Update permission
    public function updatePermission(Request $req, $id)
    {
        try {
            $req->validate = ([
                'name' => 'required',
            ]);

            $permission = Permission::find($id);
            if ($permission) {
                $permission->name = $req->name;
                $permission->update();
                return response()->json([
                    'message' => 'Permission update sucessfully',
                    'recordList' => $permission,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Permission is not found',
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

    // Active/InActive Permission
    public function activePermission(Request $req, $id)
    {
        try {
            $permission = Permission::find($id);
            if ($permission) {
                $permission->isActive = $req->isActive;
                $permission->update();
                return response()->json([
                    'message' => 'Permission status change sucessfully',
                    'recordList' => $permission,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Permission status not change.',
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
}
