<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AstrologerCategory;
use App\Models\CourseCategory;
use App\Models\PujaCategory;
use App\Models\PujaSubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Exception;

class AstrologerCategoryController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addAstrolgerCategory()
    {
        return view('pages.astrologer-category-list');
    }

    public function addAstrolgerCategoryApi(Request $req)
    {
        try {
            // Validate input
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:astrologer_categories',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ]);
            }

            // Check login
            if (!Auth::guard('web')->check()) {
                return response()->json([
                    'status' => false,
                    'redirect' => '/admin/login'
                ]);
            }

            // Create category first to get ID
            $astrologerCategory = AstrologerCategory::create([
                'name' => $req->name,
                'image' => '',
                'displayOrder' => null,
                'createdBy' => Auth()->user()->id,
                'modifiedBy' => Auth()->user()->id,
            ]);

            $finalPath = null;

            // If image exists
            if ($req->hasFile('image')) {
                $file = $req->file('image');

                // Get image content
                $imageContent = file_get_contents($file->getRealPath());

                // Create final image name
                $extension = $file->getClientOriginalExtension();
                $imageName = 'astrologerCategory_' . $astrologerCategory->id . '_' . time() . '.' . $extension;

                // Upload to active storage
                // (Correct signature: content, filename, folder_name)
                $finalPath = StorageHelper::uploadToActiveStorage(
                    $imageContent,
                    $imageName,
                    'astrologer_categories'
                );
            }

            // Update record with final image path
            $astrologerCategory->image = $finalPath;
            $astrologerCategory->save();

            return response()->json([
                'status' => true,
                'message' => 'Category added successfully',
                'redirect' => route('astrologerCategories')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getAstrolgerCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $categories = AstrologerCategory::query();
                $categoryCount = $categories->count();
                $categories->orderBy('id', 'DESC');
                $categories->skip($paginationStart);
                $categories->take($this->limit);
                $categories = $categories->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view(
                    'pages.astrologer-category-list',
                    compact('categories', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editAstrolgerCategory()
    {
        return view('pages.astrologer-category-list');
    }

    public function editAstrolgerCategoryApi(Request $request)
    {
        try {
            // Check login
            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            // Find the category
            $astrologerCategory = AstrologerCategory::find($request->filed_id);
            if (!$astrologerCategory) {
                return back()->with('error', 'Astrologer category not found!');
            }

            // Keep previous image if user does NOT upload new one
            $finalPath = $astrologerCategory->image;

            // If new image uploaded
            if ($request->hasFile('image')) {
                // New file
                $file = $request->file('image');

                // Read content
                $imageContent = file_get_contents($file->getRealPath());

                // Build new file name
                $extension = $file->getClientOriginalExtension();
                $imageName = 'astrologerCategory_' . $astrologerCategory->id . '_' . time() . '.' . $extension;

                // Upload to active storage
                // correct signature: (content, filename, folder)
                $finalPath = StorageHelper::uploadToActiveStorage(
                    $imageContent,
                    $imageName,
                    'astrologer_categories'
                );
            }

            // Update record
            $astrologerCategory->update([
                'name' => $request->name,
                'image' => $finalPath,
                'displayOrder' => null,
                'modifiedBy' => Auth()->user()->id,
            ]);

            return redirect()->route('astrologerCategories')->with('message', 'Category updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function astrologyCategoryStatus(Request $request)
    {
        return view('pages.astrologer-category-list');
    }

    public function astrologyCategoryStatusApi(Request $request)
    {
        try {
            $astrologerCategory = AstrologerCategory::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->isActive = !$astrologerCategory->isActive;
                $astrologerCategory->update();
                return redirect()->route('astrologerCategories');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    public function pujaCategoryList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $categories = PujaCategory::query();
                $categoryCount = $categories->count();
                $categories->orderBy('id', 'DESC');
                $categories->skip($paginationStart);
                $categories->take($this->limit);
                $categories = $categories->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view(
                    'pages.puja-category',
                    compact('categories', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------

    public function addPujaCategory(Request $req)
    {
        try {
            // Validation
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:puja_categories',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            /* -------------------------------------------------
                    CREATE CATEGORY (image empty first)
            -------------------------------------------------- */
            $pujaCategory = PujaCategory::create([
                'name' => $req->name,
                'image' => '',
                'displayOrder' => null,
                'createdBy' => Auth()->user()->id,
                'modifiedBy' => Auth()->user()->id,
            ]);

            /* -------------------------------------------------
                     IMAGE UPLOAD LOGIC (YOUR NEW BLOCK)
            -------------------------------------------------- */

            $path = null;
            $time = Carbon::now()->timestamp;

            if ($req->hasFile('image')) {  // using your <input name="image">

                $imageContent = file_get_contents($req->file('image')->getRealPath());
                $extension = $req->file('image')->getClientOriginalExtension() ?? 'png';
                $imageName = 'pujaCategory_' . $pujaCategory->id . '_' . $time . '.' . $extension;

                try {
                    // Upload to active storage (local / external)
                    $path = StorageHelper::uploadToActiveStorage(
                        $imageContent,
                        $imageName,
                        'pujaCategory'
                    );
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            /* -------------------------------------------------
                     SAVE IMAGE PATH TO DB
            -------------------------------------------------- */

            $pujaCategory->image = $path;
            $pujaCategory->update();

            return redirect()
                ->route('puja-categories-list')
                ->with('message', 'Data added Successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------
    public function editPujaCategory(Request $request)
    {
        try {
            // return back()->with('error', 'This Option is disabled for Demo!');
            if (Auth::guard('web')->check()) {
                $pujaCategory = PujaCategory::find($request->filed_id);
                if (request('image')) {
                    $image = base64_encode(file_get_contents($request->file('image')));
                } elseif ($pujaCategory->image) {
                    $image = $pujaCategory->image;
                } else {
                    $image = null;
                }

                if ($pujaCategory) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $destinationpath = 'public/storage/images/';
                            $imageName = 'pujaCategory_' . $request->filed_id;
                            $path = $destinationpath . $imageName . $time . '.png';
                            File::delete($pujaCategory->image);
                            file_put_contents($path, base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    $pujaCategory->name = $request->name;
                    $pujaCategory->image = $path;
                    $pujaCategory->update();
                    return redirect()->route('puja-categories-list');
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------------
    public function PujaCategoryStatus(Request $request)
    {
        try {
            $astrologerCategory = PujaCategory::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->isActive = !$astrologerCategory->isActive;
                $astrologerCategory->update();
                return redirect()->route('puja-categories-list');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------------------------------------------------------------
    public function pujaSubCategories(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $AllCategories = PujaCategory::where('isActive', 1)->get();

                // Query to join partnerCategory with partnerSubCategory
                $categories = PujaSubCategory::query()
                    ->join('puja_categories', 'puja_categories.id', '=', 'puja_subcategories.category_id')
                    ->select('puja_categories.name as category_name', 'puja_subcategories.*')
                    ->orderBy('puja_categories.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                $categoryCount = PujaSubCategory::count();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * ($page - 1)) + $this->limit, $totalRecords);

                return view('pages.puja-subcategory', compact('AllCategories', 'categories', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function addPujaSubCategory(Request $req)
    {
        try {
            // ✅ Admin authentication check first
            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            // ✅ Validate request
            $validator = Validator::make($req->all(), [
                'categoriesId' => 'required|exists:puja_categories,id',
                'name' => 'required|string|max:255|unique:puja_subcategories,name',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->getMessageBag()->toArray(),
                ]);
            }

            DB::beginTransaction();

            // ✅ Create subcategory (image will be updated later)
            $pujaSubCategory = PujaSubCategory::create([
                'category_id' => $req->categoriesId,
                'name' => $req->name,
                'image' => null,
            ]);

            $imagePath = null;

            // ✅ Image upload (if exists)
            if ($req->hasFile('image')) {
                $file = $req->file('image');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension() ?: 'png';

                $imageName = 'pujaSubCategory_' . $pujaSubCategory->id . '_' . time() . '.' . $extension;

                // Upload to active storage (Local / S3 / DO)
                $imagePath = StorageHelper::uploadToActiveStorage(
                    $imageContent,
                    $imageName,
                    'puja_subcategories'
                );

                if (!$imagePath) {
                    throw new Exception('Image upload failed');
                }

                $pujaSubCategory->update([
                    'image' => $imagePath,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Puja Sub Category added successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }

    // --------------------------------------------------------------------------------------------------------------------------------------------------------

    public function editPujaSubCategory(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            $PujaSubCategory = PujaSubCategory::find($request->filed_id);

            if (!$PujaSubCategory) {
                return back()->with('error', 'Sub Category not found!');
            }

            /* ----------------------------------------------------
                       IMAGE UPLOAD LOGIC (NEW BLOCK)
            ---------------------------------------------------- */

            $path = $PujaSubCategory->image;  // old image
            $time = Carbon::now()->timestamp;

            if ($request->hasFile('image')) {  // <-- using your field name

                $imageContent = file_get_contents($request->file('image')->getRealPath());
                $extension = $request->file('image')->getClientOriginalExtension() ?? 'png';
                $imageName = 'PujaSubCategory_' . $PujaSubCategory->id . '_' . $time . '.' . $extension;

                try {
                    // Upload new image to active storage (local/external)
                    $path = StorageHelper::uploadToActiveStorage(
                        $imageContent,
                        $imageName,
                        'pujaSubCategory'
                    );
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            /* ----------------------------------------------------
                        UPDATE SUBCATEGORY DATA
            ---------------------------------------------------- */

            $PujaSubCategory->category_id = $request->categoriesId;
            $PujaSubCategory->name = $request->name;
            $PujaSubCategory->image = $path;
            $PujaSubCategory->update();

            return redirect()->route('puja-subcategories-list');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------------------------------
    public function PujaSubCategoryStatus(Request $request)
    {
        try {
            $PujaSubCategory = PujaSubCategory::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $PujaSubCategory->isActive = !$PujaSubCategory->isActive;
                $PujaSubCategory->update();
                return redirect()->route('puja-subcategories-list');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------------------

    // --------------------------------------------------------------------------------------------------------------------------------

    public function courseCategoryList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $categories = CourseCategory::query();
                $categoryCount = $categories->count();
                $categories->orderBy('id', 'DESC');
                $categories->skip($paginationStart);
                $categories->take($this->limit);
                $categories = $categories->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view(
                    'pages.course-category',
                    compact('categories', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------

    public function addCourseCategory(Request $req)
    {
        try {
            // Validation
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:course_categories',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            /* -------------------------------------------------
                    CREATE CATEGORY (Image Empty First)
            -------------------------------------------------- */
            $courseCategory = CourseCategory::create([
                'name' => $req->name,
                'image' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            /* -------------------------------------------------
                      IMAGE UPLOAD LOGIC (YOUR BLOCK)
                    (Replaces base64 + file_put_contents)
            -------------------------------------------------- */

            $path = null;
            $time = Carbon::now()->timestamp;

            if ($req->hasFile('image')) {  // <-- image field name used

                $imageContent = file_get_contents($req->file('image')->getRealPath());
                $extension = $req->file('image')->getClientOriginalExtension() ?? 'png';
                $imageName = 'courseCategory_' . $courseCategory->id . '_' . $time . '.' . $extension;

                try {
                    // Upload to active storage (local / external)
                    $path = StorageHelper::uploadToActiveStorage(
                        $imageContent,
                        $imageName,
                        'courseCategory'
                    );
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            /* -------------------------------------------------
                            UPDATE FINAL PATH
            -------------------------------------------------- */

            $courseCategory->image = $path;
            $courseCategory->update();

            return redirect()
                ->route('course-categories-list')
                ->with('message', 'Data added Successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function editCourseCategory(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            $courseCategory = CourseCategory::find($request->filed_id);

            if (!$courseCategory) {
                return back()->with('error', 'Category not found!');
            }

            /* ----------------------------------------------------
                    IMAGE UPLOAD LOGIC (REPLACED WITH YOUR CODE)
            ---------------------------------------------------- */

            $path = $courseCategory->image;  // default old image
            $time = Carbon::now()->timestamp;

            if ($request->hasFile('image')) {
                $imageContent = file_get_contents($request->file('image')->getRealPath());
                $extension = $request->file('image')->getClientOriginalExtension() ?? 'png';
                $imageName = 'courseCategory_' . $courseCategory->id . '_' . $time . '.' . $extension;

                try {
                    // Upload to active storage (local/external)
                    $path = StorageHelper::uploadToActiveStorage(
                        $imageContent,
                        $imageName,
                        'courseCategory'
                    );
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            /* ----------------------------------------------------
                        UPDATE CATEGORY DATA
            ---------------------------------------------------- */

            $courseCategory->name = $request->name;
            $courseCategory->image = $path;
            $courseCategory->update();

            return redirect()->route('course-categories-list');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------------
    public function CourseCategoryStatus(Request $request)
    {
        try {
            $astrologerCategory = CourseCategory::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->isActive = !$astrologerCategory->isActive;
                $astrologerCategory->update();
                return redirect()->route('course-categories-list');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------------------------------------------------------------
}
