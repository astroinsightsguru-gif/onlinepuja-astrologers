<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\HelpSupport;
use App\Models\AdminModel\HelpSupportQuation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class HelpSupportController extends Controller
{
    public function addHelpSupport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                HelpSupport::create([
                    'name' => $request->title,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                return redirect()->route('helpSupport');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getHelpSupport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupport = HelpSupport::query()->get();
                return view('pages.help-support', compact('helpSupport'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editHelpSupport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupport = HelpSupport::find($request->id);
                if ($helpSupport) {
                    $helpSupport->name = $request->title;
                    $helpSupport->update();
                }
                return redirect()->route('helpSupport');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addHelpSupportSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                HelpSupportQuation::create([
                    'helpSupportId' => $request->supportId,
                    'question' => $request->subCategory,
                    'answer' => $request->did,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                    'isChatWithus' => $request->isChatWithus && $request->isChatWithus == 'on' ? true : false,
                ]);
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getHelpSupportSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupportSubCategory = DB::Table('help_support_quations')
                    ->where('helpSupportId', '=', $request->helpSupportId)
                    ->get();
                if ($helpSupportSubCategory && count($helpSupportSubCategory) > 0) {
                    foreach ($helpSupportSubCategory as $support) {
                        $subCategory = DB::table('help_support_quation_answers')->where('helpSupportQuationId', '=', $support->id)->get();
                        if ($subCategory && count($subCategory) > 0) {
                            $support->isSubCategory = true;
                        } else {
                            $support->isSubCategory = false;
                        }
                    }
                }
                return view('pages.help-support-queston-answer', compact('helpSupportSubCategory'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addHelpSupportSubSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $questionAnswer = array(
                    'helpSupportId' => $request->supportId,
                    'helpSupportQuationId' => $request->supportQuestionId,
                    'title' => $request->title,
                    'description' => $request->did,
                    'isChatWithus' => $request->isChatWithus && $request->isChatWithus == 'on' ? true : false,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                );
                DB::Table('help_support_quation_answers')
                    ->insert($questionAnswer);
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getHelpSupportSubSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupportSubSubCategory = DB::table('help_support_quation_answers')
                    ->where('helpSupportQuationId', $request->helpSupportSubCategoryId)
                    ->get();
                return view('pages.help-support-sub-category', compact('helpSupportSubSubCategory'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editHelpSupportSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupportQuestion = HelpSupportQuation::find($request->id);
                if ($helpSupportQuestion) {
                    $helpSupportQuestion->helpSupportId = $request->supportId;
                    $helpSupportQuestion->question = $request->title;
                    $helpSupportQuestion->answer = $request->editdid;
                    $helpSupportQuestion->isChatWithus = $request->isChatWithus && $request->isChatWithus == 'on' ? true : false;
                    $helpSupportQuestion->update();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteHelpSupport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupport = HelpSupport::find($request->del_id);
                DB::Table('help_support_quations')
                    ->where('helpSupportId', '=', $request->del_id)
                    ->delete();
                DB::Table('help_support_quation_answers')
                    ->where('helpSupportId', '=', $request->del_id)
                    ->delete();
                if ($helpSupport) {
                    $helpSupport->delete();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteSubSupport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $helpSupportQuestion = HelpSupportQuation::find($request->del_id);
                if ($helpSupportQuestion) {
                    $helpSupportQuestion->delete();

                    DB::Table('help_support_quation_answers')
                        ->where('helpSupportQuationId', '=', $request->del_id)
                        ->delete();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editHelpSupportSubSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $questionAnswer = array(
                    'helpSupportId' => $request->supportId,
                    'helpSupportQuationId' => $request->supportQuestionId,
                    'title' => $request->title,
                    'description' => $request->editdid,
                    'isChatWithus' => $request->isChatWithus && $request->isChatWithus == 'on' ? true : false,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                );
                DB::Table('help_support_quation_answers')
                    ->where('id', '=', $request->id)
                    ->update($questionAnswer);
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteHelpSupportSubSubCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::Table('help_support_quation_answers')->where('id', '=', $request->del_id)->delete();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
