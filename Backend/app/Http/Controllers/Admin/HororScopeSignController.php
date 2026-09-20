<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horosign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class HororScopeSignController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addHororScopeSign()
    {
        return view('pages.horor-scope-sign-list');
    }

    public function addHororScopeSignApi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:hororscope_signs',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (Auth::guard('web')->check()) {
                $hororscopeSign = Horosign::create([
                    'name' => $request->name,
                    'displayOrder' => null,
                    'image' => '',
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);

                $path = null;

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $imageContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension();
                    $imageName = 'sign_' . $hororscopeSign->id . '_' . time() . '.' . $extension;

                    $path = \App\Helpers\StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'horoscope_signs');
                }

                $hororscopeSign->image = $path;
                $hororscopeSign->update();

                return redirect()->route('horoscopeSigns');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getHororScopeSign(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $signs = Horosign::query();
                $hororscopeSignCount = $signs->count();
                $signs->orderBy('id', 'DESC');
                $signs->skip($paginationStart);
                $signs->take($this->limit);
                $signs = $signs->get();
                $totalPages = ceil($hororscopeSignCount / $this->limit);
                $totalRecords = $hororscopeSignCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.horor-scope-sign-list', compact('signs', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editHororScopeSign()
    {
        return view('pages.horor-scope-sign-list');
    }

    public function editHororScopeSignApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $hororScopeSign = Horosign::find($req->filed_id);
                if (!$hororScopeSign) {
                    return back()->with('error', 'Invalid Horoscope Sign ID!');
                }

                $path = $hororScopeSign->image;  // Default old image path

                if ($req->hasFile('image')) {
                    // New image uploaded
                    $file = $req->file('image');
                    $imageContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension();
                    $imageName = 'sign_' . $req->filed_id . '_' . time() . '.' . $extension;

                    // Upload to active storage (S3/local/Spaces etc.)
                    $path = \App\Helpers\StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'horoscope_signs');

                    // Delete old image if exists and stored locally
                    if ($hororScopeSign->image && file_exists(public_path($hororScopeSign->image))) {
                        @unlink(public_path($hororScopeSign->image));
                    }
                }

                // Update record
                $hororScopeSign->name = $req->name;
                $hororScopeSign->displayOrder = null;
                $hororScopeSign->image = $path;
                $hororScopeSign->update();

                return redirect()->route('horoscopeSigns');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function horoScopeStatus(Request $request)
    {
        return view('pages.horor-scope-sign-list');
    }

    public function horoScopeStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $hororscopeSign = Horosign::find($request->status_id);
                if ($hororscopeSign) {
                    $hororscopeSign->isActive = !$hororscopeSign->isActive;
                    $hororscopeSign->update();
                    return redirect()->route('horoscopeSigns');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
