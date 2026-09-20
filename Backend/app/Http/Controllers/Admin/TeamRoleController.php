<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel\TeamMember;
use App\Models\AdminModel\TeamRole;
use App\Models\UserModel\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class TeamRoleController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public $id;

    public function addTeamRoleApi(Request $req)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:teamrole',
            ]);
            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first());
            }

            if (Auth::guard('web')->check()) {
                $teamRole = TeamRole::create([
                    'name' => $req->name,
                ]);
                for ($i = 0; $i < count($req->page); $i++) {
                    if (array_key_exists('value', $req->page[$i]['page'])) {
                        $data = array(
                            'teamRoleId' => $teamRole->id,
                            'adminPageId' => $req->page[$i]['page']['id'],
                        );
                        DB::table('rolepages')->insert($data);
                    }
                }
                DB::commit();
                return redirect()->route('teamRole');
            } else {
                DB::rollback();
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Skill Api

    public function getTeamRole(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $teamRole = DB::table('teamrole');
                $teamRole->orderBy('id', 'DESC');
                $teamRoleCount = $teamRole->count();
                $teamRole->skip($paginationStart);
                $teamRole->take($this->limit);
                $teamRole = $teamRole->get();
                $totalPages = ceil($teamRoleCount / $this->limit);
                $totalRecords = $teamRoleCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.team-role', compact('teamRole', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Delete Skill Api

    public function deleteTeamRole(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('teamrole')->where('id', $request->del_id)->delete();
                DB::table('teammember')->where('teamRoleId', $request->del_id)->delete();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editTeamRoleApi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:teamrole,name,' . $request->id,
            ]);
            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first());
            }

            if (Auth::guard('web')->check()) {
                $teamRole = TeamRole::find($request->id);
                $teamRole->name = $request->name;
                $teamRole->update();
                DB::table('rolepages')->where('teamRoleId', $request->id)->delete();
                for ($i = 0; $i < count($request->page); $i++) {
                    if (array_key_exists('value', $request->page[$i]['page'])) {
                        $data = array(
                            'teamRoleId' => $teamRole->id,
                            'adminPageId' => $request->page[$i]['page']['id'],
                        );
                        DB::table('rolepages')->insert($data);
                    }
                }
                return redirect()->route('teamRole');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function redirectAddTeamRole()
    {
        try {
            $pages = DB::table('adminpages')->get();
            $rolePages = [];
            for ($i = 0; $i < count($pages); $i++) {
                $childPages = DB::table('adminpages')->where('pageGroup', $pages[$i]->id)->first();
                if (!$childPages) {
                    array_push($rolePages, $pages[$i]);
                }
            }
            $pages = $rolePages;
            return view('pages/add-team-role', compact('pages'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function redirectEditTeamRole(Request $req)
    {
        try {
            $role = DB::table('teamrole')->where('id', $req->id)->first();
            $pages = DB::table('adminpages')->get();
            $rolePages = [];
            for ($i = 0; $i < count($pages); $i++) {
                $childPages = DB::table('adminpages')->where('pageGroup', $pages[$i]->id)->first();
                if (!$childPages) {
                    array_push($rolePages, $pages[$i]);
                }
            }
            $pages = $rolePages;
            $rolePages = DB::table('rolepages')->where('teamRoleId', $req->id)->get();
            for ($i = 0; $i < count($pages); $i++) {
                $id = $pages[$i]->id;
                $result = array_filter(json_decode($rolePages), function ($event) use ($id) {
                    return $event->adminPageId === $id;
                });
                if ($result && count($result) > 0) {
                    $pages[$i]->isPermitted = true;
                } else {
                    $pages[$i]->isPermitted = false;
                }
            }
            return view('pages/edit-team-role', compact('pages', 'role'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getTeamMember(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $teamMember = DB::table('teammember')->join('teamrole', 'teamrole.id', 'teammember.teamRoleId')->where('teammember.isDelete', false);
                $teamMem = DB::table('teamrole')->get();
                $teamMember->orderBy('teammember.id', 'DESC');
                $teamMemberCount = $teamMember->count();
                $teamMember->skip($paginationStart);
                $teamMember->take($this->limit);
                $teamMembers = $teamMember->select('teammember.*', 'teamrole.name as teamRole');
                $teamMembers = $teamMember->get();
                $totalPages = ceil($teamMemberCount / $this->limit);
                $totalRecords = $teamMemberCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.team-list', compact('teamMembers', 'teamMem', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addTeam()
    {
        return view('pages.team-list');
    }

    public function addTeamApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'contactNo' => 'required',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->where(function ($query) {
                        $query->where('isDelete', '=', '0');
                    }),
                ],
                'teamRoleId' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Handle base64 profile if sent
            $profile = null;
            if ($req->profile) {
                $profile = base64_encode(file_get_contents($req->file('profile')));
            }

            // Insert into users table
            $data = [
                'name' => $req->name,
                'contactNo' => $req->contactNo,
                'email' => $req->email,
                'password' => $req->password ? bcrypt($req->password) : $req->password,
                'userRoleName' => 'SUBADMIN',
            ];
            DB::table('users')->insert($data);
            $userId = DB::getPdo()->lastInsertId();

            // Insert into team_members table
            $teamMember = TeamMember::create([
                'name' => $req->name,
                'email' => $req->email,
                'contactNo' => $req->contactNo,
                'password' => $req->password ? bcrypt($req->password) : $req->password,
                'userId' => $userId,
                'teamRoleId' => $req->teamRoleId,
            ]);

            // Handle profile upload via StorageHelper (local/external)
            $path = null;
            $time = Carbon::now()->timestamp;

            if ($req->hasFile('profile')) {
                $imageContent = file_get_contents($req->file('profile')->getRealPath());
                $extension = $req->file('profile')->getClientOriginalExtension() ?? 'png';
                $imageName = 'user_' . $userId . '_' . $time . '.' . $extension;

                try {
                    // Upload to active storage
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profile');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            } elseif ($profile) {
                // If base64 profile was sent
                $imageName = 'user_' . $userId . '_' . $time . '.png';
                try {
                    $path = StorageHelper::uploadToActiveStorage(base64_decode($profile), $imageName, 'profile');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            // Update profile path in users and team_members tables
            if ($path) {
                DB::table('users')->where('id', $userId)->update(['profile' => $path]);
                $teamMember->profile = $path;
                $teamMember->update();
            }

            return response()->json(['message' => 'Team member added successfully', 'status' => 200]);
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteTeamMember(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $data = array(
                    'isDelete' => true,
                );
                DB::table('teammember')->where('id', $request->del_id)->update($data);
                DB::table('users')->where('id', $request->userId)->update($data);
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editTeamMemberApi(Request $req)
    {
        try {
            $teamMember = TeamMember::find($req->filed_id);
            if (!$teamMember) {
                return response()->json(['error' => 'Team member not found', 'status' => 404]);
            }

            $this->id = $teamMember->userId;

            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'contactNo' => 'required',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->where(function ($query) {
                        $query
                            ->where('isdelete', '=', '0')
                            ->where('id', '!=', $this->id);
                    }),
                ],
                'teamRoleId' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Handle profile upload (base64 or file)
            $path = null;
            $time = Carbon::now()->timestamp;

            if ($req->hasFile('profile')) {
                $imageContent = file_get_contents($req->file('profile')->getRealPath());
                $extension = $req->file('profile')->getClientOriginalExtension() ?? 'png';
                $imageName = 'teamMember_' . $teamMember->userId . '_' . $time . '.' . $extension;

                try {
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profile');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            } elseif ($req->profile) {
                $imageContent = base64_decode(base64_encode(file_get_contents($req->file('profile'))));
                $imageName = 'teamMember_' . $teamMember->userId . '_' . $time . '.png';
                try {
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profile');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            } elseif ($teamMember->profile) {
                $path = $teamMember->profile;
            }

            // Update team member
            $teamMember->profile = $path;
            $teamMember->name = $req->name;
            $teamMember->email = $req->email;
            $teamMember->contactNo = $req->contactNo;
            $teamMember->password = $req->password ? bcrypt($req->password) : $teamMember->password;
            $teamMember->teamRoleId = $req->teamRoleId;
            $teamMember->update();

            // Update related user
            $user = User::find($teamMember->userId);
            if ($user) {
                $user->name = $req->name;
                $user->contactNo = $req->contactNo;
                $user->email = $req->email;
                $user->profile = $path;
                $user->password = $req->password ? bcrypt($req->password) : $user->password;
                $user->update();
            }

            return redirect()->back();
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
