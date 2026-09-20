<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class BannerController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addBanner()
    {
        return view('pages.banner-list');
    }

    public function addBannerApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Step 1: Validate inputs
                $req->validate([
                    'fromDate' => 'required|date',
                    'toDate' => 'required|date',
                    'bannerTypeId' => 'required|integer',
                    'bannerImage' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                ]);

                // Step 2: Create banner record first (to get ID)
                $banner = Banner::create([
                    'bannerImage' => '',
                    'fromDate' => $req->fromDate,
                    'toDate' => $req->toDate,
                    'bannerTypeId' => $req->bannerTypeId,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);

                $path = null;

                // Step 3: Handle image upload if provided
                if ($req->hasFile('bannerImage')) {
                    // Get file and read binary content
                    $file = $req->file('bannerImage');
                    $fileContent = file_get_contents($file->getRealPath());

                    // Generate unique file name
                    $time = Carbon::now()->timestamp;
                    $fileName = 'banner_' . $banner->id . '_' . $time . '.' . $file->getClientOriginalExtension();

                    // Upload to active storage or local
                    $path = StorageHelper::uploadToActiveStorage($fileContent, $fileName, 'banners');
                }

                // Step 4: Update banner record with image path
                $banner->bannerImage = $path;
                $banner->save();

                return redirect()->route('banners')->with('success', 'Banner added successfully!');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Banner

    public function getBanner(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                // Constructing the query with proper joins and selections
                $banners = Banner::leftJoin('banner_types', 'banners.bannerTypeId', '=', 'banner_types.id')
                    ->select('banner_types.name as bannerType', 'banner_types.appId', 'banners.*')
                    ->orderBy('banners.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                // Counting total banners
                $bannerCount = Banner::leftJoin('banner_types', 'banners.bannerTypeId', '=', 'banner_types.id')->count();

                // Calculating pagination details
                $totalPages = ceil($bannerCount / $this->limit);
                $totalRecords = $bannerCount;
                $start = $paginationStart + 1;
                $end = min($paginationStart + $this->limit, $totalRecords);

                // Fetching banner types
                $bannerType = DB::table('banner_types')->where('isActive', '=', 1)->get();

                // Returning view with data
                return view('pages.banner-list', compact('banners', 'bannerType', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editBanner()
    {
        return view('pages.banner-list');
    }

    public function editBannerApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Step 1: Find existing banner
                $banner = Banner::find($req->filed_id);
                if (!$banner) {
                    return redirect()->route('banners')->with('error', 'Banner not found!');
                }

                $path = $banner->bannerImage;

                // Step 2: Handle new uploaded image if exists
                if ($req->hasFile('bannerImage')) {
                    // Get file content
                    $file = $req->file('bannerImage');
                    $fileContent = file_get_contents($file->getRealPath());

                    // Generate unique file name
                    $time = Carbon::now()->timestamp;
                    $fileName = 'banner_' . $banner->id . '_' . $time . '.' . $file->getClientOriginalExtension();

                    // Delete old local file if existed
                    if ($banner->bannerImage && Str::contains($banner->bannerImage, 'storage')) {
                        $oldPath = public_path(str_replace('public/', '', $banner->bannerImage));
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Upload file to active bucket or fallback to local
                    $path = StorageHelper::uploadToActiveStorage($fileContent, $fileName, 'banners');
                }

                // Step 3: Update record fields
                $banner->bannerImage = $path;
                $banner->fromDate = $req->fromDate;
                $banner->toDate = $req->toDate;
                $banner->bannerTypeId = $req->bannerTypeId;
                $banner->modifiedBy = Auth()->user()->id;
                $banner->update();

                return redirect()->route('banners')->with('success', 'Banner updated successfully!');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function bannerStatus(Request $request)
    {
        return view('pages.banner-list');
    }

    public function bannerStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $banner = Banner::find($request->status_id);
                if ($banner) {
                    $banner->isActive = !$banner->isActive;
                    $banner->update();
                }
                return redirect()->route('banners');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
