<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class PageManagementController extends Controller
{
    public function getPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $pages = DB::table('pages')->get();
                return view('pages.pages', compact('pages'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function addPageApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Generate base slug from title
                $baseSlug = Str::slug(trim($req->title));

                // If title empty or slug becomes empty, fallback to timestamp
                if (empty($baseSlug)) {
                    $baseSlug = 'page-' . time();
                }

                // Ensure uniqueness by appending counter if needed
                $slug = $baseSlug;
                $counter = 1;
                while (DB::table('pages')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                // Insert using auto-generated slug
                $page = DB::table('pages')->insert([
                    'title' => $req->title,
                    'slug' => $slug,
                    'type' => $req->type,
                    'description' => $req->description,
                ]);

                return response()->json([
                    'success' => 'Page Added',
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            // Return JSON error instead of dd so AJAX callers get a proper response
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function editPageApi(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Generate base slug from updated title
            $baseSlug = \Illuminate\Support\Str::slug(trim($request->title));

            // Fallback if slug empty
            if (empty($baseSlug)) {
                $baseSlug = 'page-' . time();
            }

            // Check existing slug collisions (except the current record)
            $slug = $baseSlug;
            $counter = 1;

            while (
                DB::table('pages')
                    ->where('slug', $slug)
                    ->where('id', '!=', $request->filed_id)
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Update page
            DB::table('pages')
                ->where('id', $request->filed_id)
                ->update([
                    'title' => $request->title,
                    'slug' => $slug,
                    'type' => $request->type,
                    'description' => $request->editdescription,
                ]);

            return response()->json([
                'success' => 'Page Updated Successfully',
                'slug' => $slug,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function pageStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = DB::table('pages')->find($request->status_id);
                if ($page) {
                    DB::table('pages')->where('id', $request->status_id)->update([
                        'isActive' => !$page->isActive
                    ]);
                }

                return redirect()->route('pages');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deletePage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = DB::table('pages')->find($request->del_id);
                if ($page) {
                    $page->delete();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // User Side View
    public function privacyPolicy(Request $request)
    {
        try {
            $privacy = DB::table('pages')->where('type', 'privacy')->first();
            return view('privacypolicy', compact('privacy'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function termscondition(Request $request)
    {
        try {
            $terms = DB::table('pages')->where('type', 'terms')->first();
            return view('terms', compact('terms'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
