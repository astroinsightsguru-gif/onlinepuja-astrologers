<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;

class StoryController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function getStory(Request $req)
    {
        try {
            $page = $req->page ? $req->page : 1;
            $paginationStart = ($page - 1) * $this->limit;
            $story = AstrologerStory::query();
            $storyCount = $story->count();
            $story->orderBy('id', 'DESC');
            $story->skip($paginationStart);
            $story->take($this->limit);

            $story = $story
                ->join('astrologers', 'astrologers.id', 'astrologer_stories.astrologerId')
                ->select('astrologer_stories.*', 'astrologers.name', 'astrologers.profileImage', DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'))
                ->get();

            $totalPages = (int) ceil($storyCount / $this->limit);
            $totalRecords = $storyCount;
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
            return view('pages.story-list', compact('story', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteStory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $story = AstrologerStory::find($request->del_id);
                $path = '';
                if ($story) {
                    if ($story->mediaType === 'image') {
                        $path = $story->media;
                    } elseif ($story->mediaType === 'video') {
                        $path = $story->media;
                    }
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    $story->delete();
                }
                $story->delete();
                return redirect()->route('story-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
