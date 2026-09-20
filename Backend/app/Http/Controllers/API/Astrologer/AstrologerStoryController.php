<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerStory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\StorageHelper;
use Str;

class AstrologerStoryController extends Controller
{
   
   public function addStory(Request $req)
{
    try {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
        }

        $validator = Validator::make($req->all(), [
            'astrologerId' => 'required',
            'mediaType' => 'required|in:text,image,video',
            'media' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages(),
                'status' => 400,
            ], 400);
        }

        $mediaPaths = [];
        $time = now()->timestamp;

       if ($req->mediaType === 'image') {
        // Case 1: Web upload (multipart files)
        if ($req->hasFile('media')) {
            foreach ($req->file('media') as $media) {
                $imageContent = file_get_contents($media->getRealPath());
                $extension = $media->getClientOriginalExtension() ?? 'png';
                $imageName = 'storyimg_' . $req->astrologerId . '_' . $time . '.' . $extension;

                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'story/images');
                $mediaPaths[] = $path;
            }
        }
        // Case 2: API upload (Base64 string)
        elseif ($req->filled('media')) {
            $base64Media = is_array($req->media) ? $req->media : [$req->media];
            foreach ($base64Media as $base64) {
                if (Str::contains($base64, 'storage')) {
                    $mediaPaths[] = $base64;
                    continue;
                }

                $imageName = 'storyimg_' . $req->astrologerId . '_' . $time . '.png';
                $path = StorageHelper::uploadToActiveStorage(base64_decode($base64), $imageName, 'story/images');
                $mediaPaths[] = $path;
            }
        }

    } elseif ($req->mediaType === 'video') {
        // Case 1: Web upload (file)
        if ($req->hasFile('media')) {
            $video = $req->file('media');
            $videoContent = file_get_contents($video->getRealPath());
            $extension = $video->getClientOriginalExtension() ?? 'mp4';
            $videoName = 'storyvideo_' . $req->astrologerId . '_' . $time . '.' . $extension;

            $path = StorageHelper::uploadToActiveStorage($videoContent, $videoName, 'story/videos');
            $mediaPaths[] = $path;
        }
        // Case 2: API upload (Base64)
        elseif ($req->filled('media')) {
            $base64 = $req->media;

            if (Str::contains($base64, 'storage')) {
                $mediaPaths[] = $base64;
            } else {
                $videoName = 'storyvideo_' . $req->astrologerId . '_' . $time . '.mp4';
                $path = StorageHelper::uploadToActiveStorage(base64_decode($base64), $videoName, 'story/videos');
                $mediaPaths[] = $path;
            }
        }

    } else {
        // Fallback: Use raw media path (if any)
        $mediaPaths[] = $req->media;
    }
        // $convertToUrl = function ($value) {
        //     if (empty($value)) return null;
        //     if (Str::startsWith($value, ['http://', 'https://'])) return $value;
        //     return asset($value);
        // };

        $stories = [];
        foreach ($mediaPaths as $mediaPath) {
            $story = AstrologerStory::create([
                'astrologerId' => $req->astrologerId,
                'media' => $mediaPath,
                'mediaType' => $req->mediaType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if ($story) {
                // $story->media = $convertToUrl($story->media);
                // $story->astrologerName = Astrologer::where('id', $req->astrologerId)->value('name');
                $story->mediaType = $req->mediaType;
                $stories[] = $story;
            }
        }

        return response()->json([
            'message' => 'Story added successfully',
            'recordList' => $stories,
            'status' => 200,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}



public function getStory(Request $req)
{
    $user = Auth::guard('api')->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
    }

    $validator = Validator::make($req->all(), [
        'astrologerId' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422);
    }

    try {
        $twentyFourHoursAgo = Carbon::now()->subHours(24);

        $stories = AstrologerStory::select(
                'astrologer_stories.*',
                DB::raw('(SELECT COUNT(story_view_counts.id) FROM story_view_counts WHERE storyId = astrologer_stories.id) AS StoryViewCount')
            )
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('astrologerId', $req->astrologerId)
            ->orderBy('created_at', 'DESC')
            ->get();

        //  Get astrologer info once
        $astrologer = DB::table('astrologers')
            ->where('id', $req->astrologerId)
            ->select('id', 'name', 'profileImage')
            ->first();

        //  Convert file paths to full URLs
        $convertToUrl = function ($value) {
            if (empty($value)) {
                return null;
            }

            if (Str::startsWith($value, ['http://', 'https://'])) {
                return $value;
            }

            return asset($value);
        };

        $profileUrl = null;
        if ($astrologer) {
            $profileUrl = $convertToUrl($astrologer->profileImage ?? '');
        }

        $storyList = [];

        foreach ($stories as $story) {
            //  Check if this story was viewed by current user
            $isViewed = DB::table('story_view_counts')
                ->where('storyId', $story->id)
                ->where('userId', $user->id)
                ->exists();

            $storyList[] = [
                'id' => $story->id,
                'astrologerId' => $story->astrologerId,
                'mediaType' => $story->mediaType,
                'media' => $story->mediaType != 'text' ? $convertToUrl($story->media) : $story->media,
                'created_at' => $story->created_at,
                'updated_at' => $story->updated_at,
                'StoryViewCount' => (int) $story->StoryViewCount,
                'storyView' => $isViewed,
                'astrologerName' => $astrologer->name ?? null,
                'profile' => $profileUrl,
            ];
        }

        return response()->json([
            'message' => 'Stories fetched successfully',
            'recordList' => $storyList,
            'status' => 200,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}


    




    public function getAstrologerStory(Request $req)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
        }


        try {
            $twentyFourHoursAgo = Carbon::now()->subHours(24);
            $stories = AstrologerStory::join('astrologers', 'astrologers.id', '=', 'astrologer_stories.astrologerId')
                ->where('astrologer_stories.created_at', '>=', $twentyFourHoursAgo)
                ->where('astrologer_stories.created_at', '<=', Carbon::now())
                ->select(
                    'astrologer_stories.astrologerId',
                    'astrologers.name',
                    'astrologers.profileImage',
                    DB::raw('COUNT(astrologer_stories.id) as story_count'),
                    DB::raw('MAX(astrologer_stories.created_at) as latest_story_date'),

                    DB::raw(
                        '(CASE WHEN (select Count(story_view_counts.id) from story_view_counts inner join astrologer_stories as sub_story ON sub_story.id = story_view_counts.storyId where sub_story.astrologerId=astrologer_stories.astrologerId AND story_view_counts.userId="' . $user->id . ' ") = COUNT(astrologer_stories.id) THEN 1 ELSE 0 END) as allStoriesViewed'
                    )
                )
                ->groupBy('astrologer_stories.astrologerId', 'astrologers.name', 'astrologers.profileImage')
                ->orderBy('latest_story_date', 'DESC')
                ->get();

                 // 🔹 Fix profileImage path dynamically
                $stories->transform(function ($story) {
                    if ($story->profileImage) {
                        // Check if already full URL
                        if (!preg_match('/^https?:\/\//', $story->profileImage)) {
                            $story->profileImage = asset($story->profileImage);
                        }
                    } else {
                        // Default image if profileImage is null
                        $story->profileImage = asset('images/default-user.png');
                    }
                    return $story;
                });


            return response()->json([
                'message' => 'Stories Fetch successfully',
                'recordList' => $stories,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }




    public function clickStory(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only('userId', 'storyId');

            $validator = Validator::make($data, [
                'storyId' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            // Check if the combination of userId and storyId already exists
            if (DB::table('story_view_counts')->where('userId', $id)->where('storyId', $req->storyId)->exists()) {
                return response()->json(['message' => 'Story already viewed', 'status' => 200], 200);
            }

            // Insert data into story_view_counts table
            $countview = DB::table('story_view_counts')->insert([
                'userId' => $id,
                'storyId' => $req->storyId,
            ]);

            if ($countview) {
                return response()->json(['message' => 'Story Viewed successfully', 'status' => 200], 200);
            } else {
                return response()->json(['error' => 'Failed to insert data', 'status' => 500], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function deleteStory(Request $request)
    {
        // return redirect()->back()->with('error', 'This Option is disabled for Demo!');
        try {

            $story = AstrologerStory::find($request->StoryId);
            $path='';
            if ($story) {
                if ($story->media_type === 'image') {
                    $path = $story->media_path;
                } elseif ($story->media_typeype === 'video') {
                    $path = $story->media_path;
                }
                if (File::exists($path)) {
                    File::delete($path);
                }

                $story->delete();

                return response()->json(['message' => 'Story Deleted successfully', 'status' => 200], 200);

            }else{

                return response()->json(['message' => 'Story Not Found', 'status' => 400], 400);
            }

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
