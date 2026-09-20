<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class CourseController extends Controller
{
    public $limit = 15;

    // ---------------------------------------------------------------
    // COURSE CATEGORY LIST
    // ---------------------------------------------------------------
    public function courseCategoryList(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ?? 1;
            $paginationStart = ($page - 1) * $this->limit;
            $totalRecords = CourseCategory::count();
            $totalPages = ceil($totalRecords / $this->limit);
            $start = ($this->limit * ($page - 1)) + 1;
            $end = min($this->limit * $page, $totalRecords);

            $categories = CourseCategory::orderBy('id', 'DESC')
                ->skip($paginationStart)
                ->take($this->limit)
                ->get();

            return view('pages.course-category', compact(
                'categories', 'totalPages', 'totalRecords', 'start', 'end', 'page'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // ADD COURSE CATEGORY
    // ---------------------------------------------------------------
    public function addCourseCategory(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:course_categories',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->getMessageBag()->toArray()]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Create record first to get ID
            $courseCategory = CourseCategory::create([
                'name' => $req->name,
                'image' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $path = null;
            if ($req->hasFile('image')) {
                $file = $req->file('image');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension();
                $imageName = 'courseCategory_' . $courseCategory->id . '_' . time() . '.' . $extension;
                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'course_categories');
            }

            $courseCategory->image = $path;
            $courseCategory->save();

            return redirect()->route('course-categories-list')->with('message', 'Course Category added successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // EDIT COURSE CATEGORY
    // ---------------------------------------------------------------
    public function editCourseCategory(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $courseCategory = CourseCategory::find($req->filed_id);
            if (!$courseCategory) {
                return back()->with('error', 'Course Category not found!');
            }

            $path = $courseCategory->image;

            if ($req->hasFile('image')) {
                $file = $req->file('image');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension();
                $imageName = 'courseCategory_' . $req->filed_id . '_' . time() . '.' . $extension;

                // Delete old image from storage if exists
                if ($courseCategory->image && Storage::exists($courseCategory->image)) {
                    Storage::delete($courseCategory->image);
                }

                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'course_categories');
            }

            $courseCategory->name = $req->name;
            $courseCategory->image = $path;
            $courseCategory->updated_at = Carbon::now();
            $courseCategory->save();

            return redirect()->route('course-categories-list')->with('message', 'Course Category updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // COURSE CATEGORY STATUS TOGGLE
    // ---------------------------------------------------------------
    public function CourseCategoryStatus(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $category = CourseCategory::find($request->status_id);
            if (!$category) {
                return back()->with('error', 'Category not found!');
            }

            $category->isActive = !$category->isActive;
            $category->save();

            return redirect()->route('course-categories-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // COURSE LIST
    // ---------------------------------------------------------------
    public function CourseList(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ?? 1;
            $paginationStart = ($page - 1) * $this->limit;
            $totalRecords = Course::count();
            $totalPages = ceil($totalRecords / $this->limit);
            $start = ($this->limit * ($page - 1)) + 1;
            $end = min($this->limit * $page, $totalRecords);

            $courses = Course::orderBy('id', 'DESC')->skip($paginationStart)->take($this->limit)->get();
            $categories = CourseCategory::where('isActive', 1)->get();

            return view('pages.course', compact(
                'courses', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'categories'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // ADD COURSE
    // ---------------------------------------------------------------
    public function addCourse(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|string|max:255',
                'course_category_id' => 'required|integer',
                'course_price' => 'required|numeric',
                'course_price_usd' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->getMessageBag()->toArray()]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Create course first to get ID
            $course = Course::create([
                'name' => $req->name,
                'course_category_id' => $req->course_category_id,
                'description' => $req->description,
                'course_price' => $req->course_price,
                'course_price_usd' => $req->course_price_usd,
                'course_badge' => json_encode($req->course_badge ?: []),
                'image' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $path = null;

            // ✅ Handle image upload — store to public/storage/images/ and save web path to DB
            if ($req->hasFile('image')) {
                $file = $req->file('image');
                $extension = $file->getClientOriginalExtension();
                $imageName = 'course_' . $course->id . '_' . Carbon::now()->timestamp . '.' . $extension;

                // Ensure directory exists
                $dir = public_path('storage/images');
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                // Move file to public/storage/images/
                $file->move($dir, $imageName);

                // ✅ Save web-accessible path (not server filesystem path)
                $path = 'public/storage/images/' . $imageName;
            }

            $course->image = $path;
            $course->save();

            return redirect()->route('CourseList-list')->with('message', 'Course added successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // EDIT COURSE
    // ✅ Fixed: same image path fix as addCourse
    // ---------------------------------------------------------------
    public function editCourse(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'course_category_id' => 'required|integer',
                'course_price' => 'required|numeric',
                'course_price_usd' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first());
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $course = Course::find($request->filed_id);
            if (!$course) {
                return back()->with('error', 'Course not found!');
            }

            $path = $course->image;  // Keep existing image by default

            // ✅ Handle new image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $imageName = 'course_' . $course->id . '_' . Carbon::now()->timestamp . '.' . $extension;

                // Ensure directory exists
                $dir = public_path('storage/images');
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                // Delete old image from disk if it exists
                if ($course->image) {
                    $oldPath = public_path($course->image);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                // Move new file
                $file->move($dir, $imageName);

                // ✅ Save web-accessible path
                $path = 'public/storage/images/' . $imageName;
            }

            $course->update([
                'name' => $request->name,
                'course_category_id' => $request->course_category_id,
                'description' => $request->description,
                'course_price' => $request->course_price,
                'course_price_usd' => $request->course_price_usd,
                'course_badge' => json_encode($request->course_badge ?: []),
                'image' => $path,
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('CourseList-list')->with('message', 'Course updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // COURSE STATUS TOGGLE
    // ---------------------------------------------------------------
    public function CourseStatus(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $course = Course::find($request->status_id);
            if (!$course) {
                return back()->with('error', 'Course not found!');
            }

            $course->isActive = !$course->isActive;
            $course->save();

            return redirect()->route('CourseList-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // DELETE COURSE
    // ✅ Fixed: uses public_path() to resolve correct disk path
    // ---------------------------------------------------------------
    public function deleteCourse(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $course = Course::find($request->del_id);
            if (!$course) {
                return back()->with('error', 'Course not found!');
            }

            // ✅ Delete image from disk using public_path()
            if ($course->image) {
                $imagePath = public_path($course->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            $course->delete();

            return redirect()->route('CourseList-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // COURSE CHAPTER LIST
    // ---------------------------------------------------------------
    public function CourseChapterList(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ?? 1;
            $paginationStart = ($page - 1) * $this->limit;
            $totalRecords = CourseChapter::count();
            $totalPages = ceil($totalRecords / $this->limit);
            $start = ($this->limit * ($page - 1)) + 1;
            $end = min($this->limit * $page, $totalRecords);

            $courseschapter = CourseChapter::orderBy('id', 'DESC')
                ->skip($paginationStart)
                ->take($this->limit)
                ->get();

            $courses = Course::where('isActive', 1)->get();

            return view('pages.course-chapters', compact(
                'courseschapter', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'courses'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // VIEW ADD COURSE CHAPTER FORM
    // ---------------------------------------------------------------
    public function viewCourseChapter(Request $request)
    {
        try {
            $courses = Course::where('isActive', 1)->get();
            $currency = DB::table('systemflag')->where('name', 'CurrencySymbol')->select('value')->first();

            return view('pages.add-course-chapter', compact('courses', 'currency'));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // ADD COURSE CHAPTER
    // ---------------------------------------------------------------
    public function addCourseChapter(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|integer',
                'chapter_name' => 'required|string|max:255',
                'chapter_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'chapter_document' => 'nullable|mimes:pdf,doc,docx|max:10240',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Handle chapter images
            $imagePaths = [];
            if ($request->hasFile('chapter_images')) {
                foreach ($request->file('chapter_images') as $file) {
                    $name = 'chapter_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/images/chapter_images'), $name);
                    $imagePaths[] = 'public/storage/images/chapter_images/' . $name;
                }
            }

            // Handle chapter document
            $document = null;
            if ($request->hasFile('chapter_document')) {
                $file = $request->file('chapter_document');
                $name = 'chapter_doc_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/chapter_document'), $name);
                $document = 'public/storage/images/chapter_document/' . $name;
            }

            CourseChapter::create([
                'course_id' => $request->course_id,
                'chapter_name' => $request->chapter_name,
                'chapter_description' => $request->chapter_description ?? null,
                'youtube_link' => $request->youtube_link ?? null,
                'chapter_document' => $document,
                'chapter_images' => $imagePaths,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('course-chapter-list')->with('success', 'Chapter added successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // EDIT COURSE CHAPTER (show form)
    // ---------------------------------------------------------------
    public function editCourseChapter($id)
    {
        $chapters = CourseChapter::findOrFail($id);
        $courses = Course::where('isActive', 1)->get();

        return view('pages.add-course-chapter', compact('chapters', 'courses'));
    }

    // ---------------------------------------------------------------
    // UPDATE COURSE CHAPTER
    // ---------------------------------------------------------------
    public function updateCourseChapter(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|integer',
                'chapter_name' => 'required|string|max:255',
                'chapter_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'chapter_document' => 'nullable|mimes:pdf,doc,docx|max:10240',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $chapter = CourseChapter::findOrFail($id);

            // Start with existing images; if not provided, it means they were all removed
            $imagePaths = $request->input('existing_images', []);

            // Append newly uploaded images
            if ($request->hasFile('chapter_images')) {
                foreach ($request->file('chapter_images') as $file) {
                    $name = 'chapter_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/images/chapter_images'), $name);
                    $imagePaths[] = 'public/storage/images/chapter_images/' . $name;
                }
            }

            // Handle document replacement
            $document = $chapter->chapter_document ?? null;
            if ($request->hasFile('chapter_document')) {
                if ($document && File::exists(public_path($document))) {
                    File::delete(public_path($document));
                }
                $file = $request->file('chapter_document');
                $name = 'chapter_doc_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/chapter_document'), $name);
                $document = 'public/storage/images/chapter_document/' . $name;
            }

            $chapter->update([
                'course_id' => $request->course_id,
                'chapter_name' => $request->chapter_name,
                'chapter_description' => $request->chapter_description ?? null,
                'youtube_link' => $request->youtube_link ?? null,
                'chapter_document' => $document,
                'chapter_images' => $imagePaths,
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('course-chapter-list')->with('success', 'Chapter updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // COURSE CHAPTER STATUS TOGGLE
    // ---------------------------------------------------------------
    public function CourseChapterStatus(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $chapter = CourseChapter::find($request->status_id);
            if (!$chapter) {
                return back()->with('error', 'Chapter not found!');
            }

            $chapter->isActive = !$chapter->isActive;
            $chapter->save();

            return redirect()->route('course-chapter-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // DELETE COURSE CHAPTER
    // ---------------------------------------------------------------
    public function deleteCourseChapter(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $chapter = CourseChapter::find($request->del_id);
            if (!$chapter) {
                return back()->with('error', 'Chapter not found!');
            }

            $chapter->delete();

            return redirect()->route('course-chapter-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // COURSE ORDER LIST
    // ---------------------------------------------------------------
    public function courseOrderList(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ?? 1;
            $paginationStart = ($page - 1) * $this->limit;
            $from_date = $request->from_date ?? null;
            $to_date = $request->to_date ?? null;

            $query = CourseOrder::with('course', 'astrologer');

            // Date filters
            if ($from_date && $to_date) {
                $query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            } elseif ($from_date) {
                $query->where('created_at', '>=', $from_date . ' 00:00:00');
            } elseif ($to_date) {
                $query->where('created_at', '<=', $to_date . ' 23:59:59');
            }

            $totalRecords = $query->count();
            $totalPages = ceil($totalRecords / $this->limit);
            $start = ($this->limit * ($page - 1)) + 1;
            $end = min($this->limit * $page, $totalRecords);

            $courseOrderlist = (clone $query)
                ->skip($paginationStart)
                ->take($this->limit)
                ->get();

            $currency = DB::table('systemflag')->where('name', 'CurrencySymbol')->select('value')->first();

            return view('pages.course-order-list', compact(
                'courseOrderlist', 'totalPages', 'totalRecords', 'start', 'end', 'page',
                'currency', 'from_date', 'to_date'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
