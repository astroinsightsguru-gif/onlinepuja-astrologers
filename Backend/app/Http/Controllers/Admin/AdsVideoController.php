<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologyVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class AdsVideoController extends Controller
{
    public $path;
    public $limit = 6;
    public $paginationStart;

    public function addAdsVideo()
    {
        return view('pages.adsVideo');
    }

    public function addAdsVideoApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $req->validate([
                    'youtubeLink' => 'required|string',
                    'videoTitle' => 'required|string',
                    'coverImage' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                ]);

                // Step 2: Create video entry first (so we get ID)
                $adsVideo = AstrologyVideo::create([
                    'youtubeLink' => $req->youtubeLink,
                    'coverImage' => '',
                    'videoTitle' => $req->videoTitle,
                    'description' => $req->description,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);

                $path = null;

                // Step 3: Handle file upload (if exists)
                if ($req->hasFile('coverImage')) {
                    // Read file content as binary
                    $file = $req->file('coverImage');
                    $fileContent = file_get_contents($file->getRealPath());

                    // Create unique file name
                    $time = Carbon::now()->timestamp;
                    $fileName = 'coverImage_' . $adsVideo->id . '_' . $time . '.' . $file->getClientOriginalExtension();

                    // Upload via helper (auto chooses bucket or local)
                    $path = StorageHelper::uploadToActiveStorage($fileContent, $fileName, 'ads_videos');
                }

                $adsVideo->coverImage = $path;
                $adsVideo->save();

                return redirect()->route('adsVideos')->with('success', 'Video added successfully!');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getAdsVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $adsVideo = AstrologyVideo::orderBy('id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                $adsVideoCount = AstrologyVideo::count();

                $totalPages = ceil($adsVideoCount / $this->limit);
                $totalRecords = $adsVideoCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                return view(
                    'pages.adsVideo',
                    compact('adsVideo', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Edit

    public function editAdsVideo()
    {
        return view('pages.adsVideo');
    }

    public function editAdsVideoApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Step 1: Find existing video
                $adsVideo = AstrologyVideo::find($req->filed_id);
                if (!$adsVideo) {
                    return redirect()->route('adsVideos')->with('error', 'Video not found!');
                }

                // Step 2: Initialize path variable
                $path = $adsVideo->coverImage;

                // Step 3: Handle new uploaded file (if exists)
                if ($req->hasFile('coverImage')) {
                    // Read new file content
                    $file = $req->file('coverImage');
                    $fileContent = file_get_contents($file->getRealPath());

                    // Generate unique file name
                    $time = Carbon::now()->timestamp;
                    $fileName = 'coverImage_' . $adsVideo->id . '_' . $time . '.' . $file->getClientOriginalExtension();

                    // Delete old file from local if it existed and was stored locally
                    if ($adsVideo->coverImage && Str::contains($adsVideo->coverImage, 'storage')) {
                        $oldPath = public_path(str_replace('public/', '', $adsVideo->coverImage));
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Upload new image (auto selects active bucket or local)
                    $path = StorageHelper::uploadToActiveStorage($fileContent, $fileName, 'ads_videos');
                }

                // Step 4: Update fields
                $adsVideo->youtubeLink = $req->youtubeLink;
                $adsVideo->videoTitle = $req->videoTitle;
                $adsVideo->coverImage = $path;
                $adsVideo->description = $req->description;
                $adsVideo->modifiedBy = Auth()->user()->id;
                $adsVideo->update();

                return redirect()->route('adsVideos')->with('success', 'Video updated successfully!');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function videoStatus(Request $request)
    {
        return view('pages.adsVideo');
    }

    public function videoStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $adsVideo = AstrologyVideo::find($request->status_id);
                if ($adsVideo) {
                    $adsVideo->isActive = !$adsVideo->isActive;
                    $adsVideo->update();
                }
                return redirect()->route('adsVideos');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                AstrologyVideo::find($request->del_id)->delete();
                return redirect()->route('adsVideos');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
