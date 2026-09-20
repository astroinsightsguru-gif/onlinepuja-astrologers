<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class SkillController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addSkill()
    {
        return view('pages.skill-list');
    }

    public function addSkillApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:skills',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                Skill::create([
                    'name' => $req->name,
                    'displayOrder' => null,
                    'createdBy' => Auth::user()->id,
                    'modifiedBy' => Auth::user()->id,
                ]);
                return redirect()->route('skills');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Skill Api

    public function getSkill(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $skills = Skill::query();
                $skills->orderBy('id', 'DESC');
                $skills->skip($paginationStart);
                $skills->take($this->limit);
                $skills = $skills->get();
                $skillCount = Skill::query();
                $skillCount = $skillCount->count();
                $totalPages = ceil($skillCount / $this->limit);
                $totalRecords = $skillCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.skill-list', compact('skills', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Status Changed Api

    public function skillStatus(Request $request)
    {
        return view('pages.skill-list');
    }

    public function skillStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $skill = Skill::find($request->status_id);
                if ($skill) {
                    $skill->isActive = !$skill->isActive;
                    $skill->update();
                }
                return redirect()->route('skills');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Delete Skill Api

    public function deleteSkill(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $skill = Skill::find($request->del_id);
                if ($skill) {
                    $skill->delete();
                }
                return redirect()->route('skills');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Edit Skill
    public function editskill(Request $request)
    {
        return view('pages.skill-list');
    }

    public function editSkillApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $skill = Skill::find($request->filed_id);
                if ($skill) {
                    $skill->name = $request->name;
                    $skill->displayOrder = null;
                    $skill->update();
                    return redirect()->route('skills');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
