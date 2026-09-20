<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use App\Models\AstrologerModel\Blog;
use App\Models\PujaCategory;

class BlogController extends Controller
{
    public function getBlog(Request $request)
    {
        $bloglist = Blog::query()->where('isActive', 1)->orderBy('created_at', 'desc')->paginate(6);
        return view('frontend.pages.blogs', [
            'bloglist' => $bloglist
        ]);
    }
    public function getBlogDetails(Request $request,$slug)
    {

        $blog = Blog::where('slug',$slug)->first();

        $blog->viewer = $blog->viewer + 1;
        $blog->update();
        // dd($blog);
        $latestBlogs = Blog::where('slug','!=', $slug)->where('isActive', 1)->latest()->take(5)->get();
        return view('frontend.pages.blog-details', [
            'blog' => $blog,
            'latestBlogs'=>$latestBlogs

        ]);
    }


    public function pujaCategory(Request $request)
    {
        $currentDatetime = \Carbon\Carbon::now();
        $pujaCategories = PujaCategory::where('isActive',1)->paginate(6);
        return view('frontend.pages.puja-category-list', compact('pujaCategories'));
    }

}
