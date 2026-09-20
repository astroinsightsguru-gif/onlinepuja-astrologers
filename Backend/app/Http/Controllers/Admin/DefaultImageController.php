<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel\DefaultProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
if (!defined('DESTINATIONPATH')) define('DESTINATIONPATH', 'public/storage/images/');
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class DefaultImageController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getDefaultImage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $customerProfile = DB::table('defaultprofile')
                    ->where('isDelete', 0)
                    ->orderby('id', 'DESC');

                $defaultImageCount = DB::table('defaultprofile')
                    ->where('isDelete', 0)
                    ->count();
                $customerProfile = $customerProfile->skip($paginationStart);
                $customerProfile = $customerProfile->take($this->limit);
                $customerProfile = $customerProfile->get();
                $totalPages = ceil($defaultImageCount / $this->limit);
                $totalRecords = $defaultImageCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.default-profile', compact('customerProfile', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addDefaultImage(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Step 1: Validate inputs
                $req->validate([
                    'name' => 'required|string|max:255',
                    'profile' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                ]);

                // Step 2: Create record first
                $default = DefaultProfile::create([
                    'name' => $req->name,
                    'profile' => '',
                ]);

                $path = null;

                // Step 3: Handle file upload if available
                if ($req->hasFile('profile')) {
                    $file = $req->file('profile');
                    $fileContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension();
                    $time = Carbon::now()->timestamp;
                    $fileName = 'defaultprofile_' . $default->id . '_' . $time . '.' . $extension;

                    // Upload to active bucket or local storage
                    $path = StorageHelper::uploadToActiveStorage($fileContent, $fileName, 'default_profiles');
                }

                // Step 4: Save final image path
                $default->profile = $path;
                $default->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Default image added successfully!',
                    'image_url' => $path
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updateDefaultImage(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Step 1: Find existing record
                $default = DefaultProfile::find($req->filed_id);
                if (!$default) {
                    return redirect()->back()->with('error', 'Default profile not found!');
                }

                $path = $default->profile;

                // Step 2: Handle new file if uploaded
                if ($req->hasFile('profile')) {
                    $file = $req->file('profile');
                    $fileContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension();
                    $time = Carbon::now()->timestamp;
                    $fileName = 'defaultprofile_' . $default->id . '_' . $time . '.' . $extension;

                    // Delete old file from local if it existed
                    if ($default->profile && Str::contains($default->profile, 'storage')) {
                        $oldPath = public_path(str_replace('public/', '', $default->profile));
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Upload to active bucket or fallback to local
                    $path = StorageHelper::uploadToActiveStorage($fileContent, $fileName, 'default_profiles');
                }

                // Step 3: Update name and image path
                $default->name = $req->name;
                $default->profile = $path;
                $default->save();

                return redirect()->back()->with('success', 'Default image updated successfully!');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function activeInactiveDefaultProfile(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $default = DefaultProfile::find($req->status_id);
                $default->isActive = !$default->isActive;
                $default->update();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
