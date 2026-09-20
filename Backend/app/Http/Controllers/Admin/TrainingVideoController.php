<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel\TrainingVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class TrainingVideoController extends Controller
{
    public $path;
    public $limit = 6;
    public $paginationStart;

    public function getTrainingVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $videos = TrainingVideo::orderBy('id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                $adsVideoCount = TrainingVideo::count();

                $totalPages = ceil($adsVideoCount / $this->limit);
                $totalRecords = $adsVideoCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                return view(
                    'pages.training-videos',
                    compact('videos', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addTrainingVideo(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Handle cover image
            $path = null;
            if ($req->cover_image) {
                // Base64
                if (!Str::contains($req->cover_image, 'storage')) {
                    $imageContent = base64_decode($req->cover_image);
                    $imageName = 'cover_image_' . time() . '.png';
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'training_videos');
                } else {
                    $path = $req->cover_image;
                }
            }

            // Handle file upload
            if ($req->hasFile('cover_image')) {
                $file = $req->file('cover_image');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension() ?? 'png';
                $imageName = 'cover_image_' . time() . '.' . $extension;
                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'training_videos');
            }

            // Save video record
            $Video = TrainingVideo::create([
                'video_link' => $req->video_link,
                'type' => $req->type,
                'cover_image' => $path,
                'title' => $req->title,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->back();
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editTrainingVideo(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $Video = TrainingVideo::find($req->filed_id);
            if (!$Video) {
                return redirect()->back()->with('error', 'Video not found');
            }

            // Handle cover image
            $path = $Video->cover_image;  // keep existing by default

            if ($req->cover_image) {
                if (!Str::contains($req->cover_image, 'storage')) {
                    $imageContent = base64_decode($req->cover_image);
                    $imageName = 'cover_image_' . $Video->id . '_' . time() . '.png';
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'training_videos');
                } else {
                    $path = $req->cover_image;
                }
            }

            if ($req->hasFile('cover_image')) {
                $file = $req->file('cover_image');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension() ?? 'png';
                $imageName = 'cover_image_' . $Video->id . '_' . time() . '.' . $extension;
                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'training_videos');
            }

            // Update video record
            $Video->video_link = $req->video_link;
            $Video->type = $req->type;
            $Video->cover_image = $path;
            $Video->title = $req->title;
            $Video->update();

            return redirect()->back();
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteTrainingVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                TrainingVideo::find($request->del_id)->delete();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function statusTrainingVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $video = TrainingVideo::find($request->status_id);
                if ($video) {
                    $video->isActive = !$video->isActive;
                    $video->update();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
