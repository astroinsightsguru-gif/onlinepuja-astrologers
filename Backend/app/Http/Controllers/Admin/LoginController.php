<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login()
    {
        $data = ['Feni', 'Patel', 'Nishi', 'Shah'];
        return view('login.main', ['users' => $data]);
    }

    public function loginApi(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $user = DB::table('users')->where('email', '=', $request->email)->where('isDelete', false)->get();
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);

            } elseif (($user && count($user) > 0) && !password_verify($request->password, $user[0]->password)) {
                return response()->json([
                    'error' => ['Invalid Credential'],
                ]);
            } elseif (count($user) == 0) {
                return response()->json([
                    'error' => ['Invalid Credentials'],
                ]);
            }
            if ($request->email && $request->password && Auth::attempt(['email' => $request->email, 'password' => $request->password, 'isDelete' => 0])) {
                $userId = Auth::user()->id;
                $teamMember = DB::table('teammember')->where('userId', $userId)->first();
                if ($teamMember) {
                    $sideMenu = [];
                    $rolePages = DB::table('rolepages')
                        ->join('adminpages', 'adminpages.id', 'rolepages.adminPageId')
                        ->where('teamRoleId', $teamMember->teamRoleId)
                        ->select('adminpages.*')
                        ->get();
                    $pageGroup = DB::table('adminpages')
                        ->whereNull('pageGroup')
                        ->get();
                    for ($i = 0; $i < count($pageGroup); $i++) {
                        $pages = DB::table('adminpages')
                            ->where('pageGroup', $pageGroup[$i]->id)
                            ->get();
                        $pageGroup[$i]->sub_menu = [];
                        if ($pages && count($pages) > 0) {
                            for ($j = 0; $j < count($rolePages); $j++) {
                                $id = $rolePages[$j]->id;
                                $result = array_filter(json_decode($pages), function ($event) use ($id) {
                                    return $event->id === $id;
                                });
                                if ($result && count($result) > 0) {
                                    array_push($pageGroup[$i]->sub_menu, $rolePages[$j]);
                                }
                            }
                        }
                    }
                    for ($i = 0; $i < count($pageGroup); $i++) {
                        if ($pageGroup[$i]->sub_menu && count($pageGroup[$i]->sub_menu) > 0) {
                            array_push($sideMenu, $pageGroup[$i]);
                        }
                    }
                    $parentPages = DB::table('rolepages')
                        ->join('adminpages', 'adminpages.id', 'rolepages.adminPageId')
                        ->where('teamRoleId', $teamMember->teamRoleId)
                        ->whereNull('adminpages.pageGroup')
                        ->select('adminpages.*')
                        ->get();
                    for ($i=0; $i < count($parentPages); $i++) {
                        $parentPages[$i]->sub_menu  =[];
                    }
                    $sideMenu = array_merge($sideMenu, json_decode($parentPages));
                    $sort = [];
                    foreach ($sideMenu as $key => $row) {
                        $sort[$key] = $row->displayOrder;
                    }

                    array_multisort($sort, SORT_ASC, $sideMenu);
                    if ($sideMenu[0]->sub_menu && count($sideMenu[0]->sub_menu) > 0) {
                        $first = '/admin/' . $sideMenu[0]->sub_menu[0]->route;
                    } else {
                        $first = '/admin/' . $sideMenu[0]->route;
                    }
                } else {
                    $first = '/admin/dashboard';
                }
                return response()->json([
                    'success' => "Login Success",
                    'first' => $first,
                ]);
            } else {
                return redirect()->back()->with('error', 'Login Fail', 'Please try again.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

}
