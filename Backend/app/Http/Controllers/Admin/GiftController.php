<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\Gift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class GiftController extends Controller
{
    // Add Gift API
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addGift()
    {
        return view('pages.gift-list');
    }

    public function addGiftApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:gifts',
                'amount' => 'required',
                'image' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (Auth::guard('web')->check()) {
                // ✅ Create gift first
                $gift = Gift::create([
                    'name' => $req->name,
                    'amount' => $req->amount,
                    'amount_usd' => $req->amount_usd,
                    'displayOrder' => null,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                    'image' => '',
                ]);

                $path = null;

                // ✅ Handle image upload
                if ($req->hasFile('image')) {
                    $file = $req->file('image');
                    $imageContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension();
                    $imageName = 'gift_' . $gift->id . '_' . time() . '.' . $extension;

                    // Upload using helper (auto-selects active storage)
                    $path = StorageHelper::uploadToActiveStorage(
                        $imageContent,
                        $imageName,
                        'gifts'
                    );
                }

                $gift->image = $path;
                $gift->save();

                return redirect()->route('gifts');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Gift API
    public function setGiftPage(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $req->page ? $req->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $gifts = Gift::query();
                $giftCount = $gifts->count();
                $gifts->orderBy('id', 'DESC');
                $gifts->skip($paginationStart);
                $gifts->take($this->limit);
                $gifts = $gifts->get();
                $totalPages = (int) ceil($giftCount / $this->limit);
                $totalRecords = $giftCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.gift-list', compact('gifts', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getGift(Request $req)
    {
        try {
            $page = $req->page ? $req->page : 1;
            $paginationStart = ($page - 1) * $this->limit;
            $gifts = Gift::query();
            $giftCount = $gifts->count();
            $gifts->orderBy('id', 'DESC');
            $gifts->skip($paginationStart);
            $gifts->take($this->limit);
            $gifts = $gifts->get();
            $totalPages = (int) ceil($giftCount / $this->limit);
            $totalRecords = $giftCount;
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
            return view('pages.gift-list', compact('gifts', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Delete Gift API

    public function deleteGift(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $gift = Gift::find($request->del_id);
                if ($gift) {
                    $gift->delete();
                }
                return redirect()->route('gifts');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Edit Skill API
    public function editGift()
    {
        return view('pages.gift-list');
    }

    public function editGiftApi(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $gift = Gift::find($req->filed_id);
            if (!$gift) {
                return back()->with('error', 'Gift not found.');
            }

            $path = $gift->image;

            // ✅ Handle image upload (if new image provided)
            if ($req->hasFile('image')) {
                $file = $req->file('image');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension();
                $imageName = 'gift_' . $gift->id . '_' . time() . '.' . $extension;

                // Upload new image
                $path = StorageHelper::uploadToActiveStorage(
                    $imageContent,
                    $imageName,
                    'gifts'
                );
            }

            // ✅ Update gift details
            $gift->update([
                'name' => $req->name,
                'amount' => $req->amount,
                'amount_usd' => $req->amount_usd,
                'image' => $path,
                'displayOrder' => null,
                'modifiedBy' => Auth()->user()->id,
            ]);

            return redirect()->route('gifts');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function giftStatus(Request $request)
    {
        return view('pages.gift-list');
    }

    public function giftStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $gift = Gift::find($request->status_id);
                if ($gift) {
                    $gift->isActive = !$gift->isActive;
                    $gift->update();
                    return redirect()->route('gifts');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
