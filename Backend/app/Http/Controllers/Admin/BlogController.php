<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Blog;
use App\Models\WebHomeFaq;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
if (!defined('DESTINATIONPATH')) define('DESTINATIONPATH', 'public/storage/images/');
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class BlogController extends Controller
{
    public $path;
    public $limit = 6;
    public $paginationStart;

    public function addBlog()
    {
        return view('pages.blog-list');
    }

    public function addBlogApi(Request $req)
    {
        try {
            // Check login
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // ----------------------------
            // Detect file extensions
            // ----------------------------
            $blogExt = $req->hasFile('blogImage') ? $req->file('blogImage')->getClientOriginalExtension() : null;
            $previewExt = $req->hasFile('previewImage') ? $req->file('previewImage')->getClientOriginalExtension() : null;

            // Allowed types (optional)
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if ($blogExt && !in_array(strtolower($blogExt), $allowed)) {
                return response()->json(['error' => 'Invalid blog image type']);
            }

            if ($previewExt && !in_array(strtolower($previewExt), $allowed)) {
                return response()->json(['error' => 'Invalid preview image type']);
            }

            // Read file contents
            $imageContent = $req->hasFile('blogImage')
                ? file_get_contents($req->file('blogImage')->getRealPath())
                : null;

            $previewContent = $req->hasFile('previewImage')
                ? file_get_contents($req->file('previewImage')->getRealPath())
                : null;

            // Generate unique file names
            $time = now()->timestamp;
            $blogName = 'blog_' . $time . '.' . ($blogExt ?? 'png');
            $previewName = 'preview_' . $time . '.' . ($previewExt ?? 'png');

            // Generate unique slug
            $slug = Str::slug($req->title, '-');
            $originalSlug = $slug;
            $counter = 1;

            while (DB::table('blogs')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create blog record without images first
            $blog = Blog::create([
                'title' => $req->title,
                'blogImage' => '',
                'previewImage' => '',
                'slug' => $slug,
                'description' => $req->description,
                'viewer' => $req->viewer,
                'author' => $req->author,
                'createdBy' => Auth::user()->id,
                'modifiedBy' => Auth::user()->id,
                'postedOn' => $req->postedOn,
            ]);

            // Upload paths
            $blogPath = null;
            $previewPath = null;

            // Upload blog image
            if ($imageContent) {
                $blogPath = StorageHelper::uploadToActiveStorage(
                    $imageContent,
                    $blogName,
                    'blog'
                );
            }

            // Upload preview image
            if ($previewContent) {
                $previewPath = StorageHelper::uploadToActiveStorage(
                    $previewContent,
                    $previewName,
                    'blog'
                );
            }

            // Update blog record with paths
            $blog->blogImage = $blogPath ?? '';
            $blog->previewImage = $previewPath ?? '';
            $blog->update();

            return response()->json([
                'success' => 'Blog Added',
                'blog' => $blog
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBlog(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $blog = Blog::query();
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $blog->whereRaw(sql: "title LIKE '%" . $request->searchString . "%' ");
                }
                $blog->orderBy('id', 'DESC');
                $blogCount = $blog->count();
                $blog->skip($paginationStart);
                $blog->take($this->limit);
                $blogs = $blog->get();
                $totalPages = ceil($blogCount / $this->limit);
                $totalRecords = $blogCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.blog-list', compact('blogs', 'totalPages', 'searchString', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', '', $e->getMessage());
        }
    }

    public function getBlogById(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $blogDetail = Blog::find($request->id);
                return view('pages.blog-detail', compact('blogDetail'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editBlog()
    {
        return view('pages.blog-list');
    }

    public function editBlogApi(Request $request)
    {
        try {
            // Check login
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Find blog
            $blog = Blog::find($request->filed_id);
            if (!$blog) {
                return response()->json(['error' => 'Blog not found'], 404);
            }

            $time = Carbon::now()->timestamp;

            // Handle blog image
            $imageContent = $request->hasFile('eblogImage') ? file_get_contents($request->file('eblogImage')->getRealPath()) : null;
            $previewContent = $request->hasFile('previewImages') ? file_get_contents($request->file('previewImages')->getRealPath()) : null;

            $extension = $blog->extension ?? ($request->hasFile('eblogImage') ? $request->file('eblogImage')->getClientOriginalExtension() : 'png');

            // Default paths
            $blogPath = $blog->blogImage;
            $previewPath = $blog->previewImage;

            // Upload new images to storage using StorageHelper
            if ($imageContent) {
                $imageName = 'blog_' . $blog->id . '_' . $time . '.' . $extension;
                $blogPath = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'blog');
            }

            if ($previewContent) {
                $previewName = 'blogpreview_' . $blog->id . '_' . $time . '.png';
                $previewPath = StorageHelper::uploadToActiveStorage($previewContent, $previewName, 'blog');
            }

            // Generate unique slug
            $slug = Str::slug($request->title, '-');
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('blogs')
                    ->where('slug', $slug)
                    ->where('id', '!=', $blog->id)
                    ->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Update blog record
            $blog->title = $request->title;
            $blog->slug = $slug;
            $blog->blogImage = $blogPath;
            $blog->previewImage = $previewPath;
            $blog->description = $request->editdescription;
            $blog->author = $request->author;
            $blog->postedOn = $request->postedOn;
            $blog->extension = $extension;
            $blog->update();

            return response()->json([
                'success' => 'Blog Updated Successfully',
                'blog' => $blog
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function blogStatus(Request $request)
    {
        return view('pages.blog-list');
    }

    public function blogStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $blog = Blog::find($request->status_id);
                if ($blog) {
                    $blog->isActive = !$blog->isActive;
                    $blog->update();
                }
                return redirect()->route('blogs');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteBlog(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $blog = Blog::find($request->del_id);
                if ($blog) {
                    $blog->delete();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------
    public function webFaqList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $webfaq = WebHomeFaq::query();
                $categoryCount = $webfaq->count();
                $webfaq->orderBy('id', 'DESC');
                $webfaq->skip($paginationStart);
                $webfaq->take($this->limit);
                $webfaq = $webfaq->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view(
                    'pages.web-home-faq',
                    compact('webfaq', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------
    public function addWebFaq(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'title' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                $webfaq = WebHomeFaq::create([
                    'title' => $req->title,
                    'description' => $req->description,
                ]);
                $webfaq->update();
                return redirect()->route('web-faq-list')->with('message', 'Data added Successfully');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------
    public function editWebFaq(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $webfaq = WebHomeFaq::find($request->filed_id);
                if ($webfaq) {
                    $webfaq->title = $request->title;
                    $webfaq->description = $request->description;
                    $webfaq->update();
                    return redirect()->route('web-faq-list');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------------

    public function deleteWebFaq(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $webfaq = WebHomeFaq::find($request->faq_id);

                if ($webfaq) {
                    $webfaq->isDelete = 1;
                    $webfaq->delete();
                }
                return redirect()->route('web-faq-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------
}
