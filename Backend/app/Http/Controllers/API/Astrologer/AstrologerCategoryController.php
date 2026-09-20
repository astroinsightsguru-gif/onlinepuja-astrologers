<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\AstrologerCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AstrologerCategoryController extends Controller
{

    //Get all the data of the astrologer category
    public function getAstrologerCategory(Request $req)
    {
        try {

            $astrologerCategory = AstrologerCategory::query();
            if ($s = $req->input(key:'s')) {
                $astrologerCategory->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }
            $categoryCount = $astrologerCategory->count();
            $astrologerCategory->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $astrologerCategory->skip($req->startIndex);
                $astrologerCategory->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $astrologerCategory->get(),
                'status' => 200,
                'totalRecords' => $categoryCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show only active astrologer category
    public function activeAstrologerCategory(Request $req)
{
    try {
        // Helper closure to handle image URLs
        $formatImageUrl = function ($path) {
            if (empty($path)) {
                return null;
            }
            // If already a full URL, leave as-is
            if (preg_match('/^https?:\/\//', $path)) {
                return $path;
            }
            // Otherwise convert to full URL using asset()
            return asset($path);
        };

        // Fetch all active astrologer categories
        $astrologerCategories = AstrologerCategory::query()
            ->where('isActive', '=', '1')
            ->get();

        $result = [];

        foreach ($astrologerCategories as $category) {
            // Format category image
            $category->image = $formatImageUrl($category->image ?? null);

            // Fetch astrologers for this category
            $astrologers = Astrologer::query()
                ->select('astrologers.*', DB::raw('
                    (CASE WHEN abp.astrologer_id IS NOT NULL THEN 1 ELSE 0 END) as is_boosted,
                    COALESCE(AVG(user_reviews.rating), 0) as rating
                '))
                ->leftJoin('astrologer_boosted_profiles as abp', function ($join) {
                    $join->on('astrologers.id', '=', 'abp.astrologer_id')
                         ->where('abp.boosted_datetime', '>=', Carbon::now()->subHours(24));
                })
                ->leftJoin('user_reviews', 'astrologers.id', '=', 'user_reviews.astrologerId')
                ->where([
                    ['astrologers.isActive', true],
                    ['astrologers.isVerified', true],
                    ['astrologers.isDelete', false]
                ])
                ->whereRaw('FIND_IN_SET(?, astrologers.astrologerCategoryId)', [$category->id])
                ->groupBy('astrologers.id')
                ->orderByRaw('
                    CASE
                        WHEN abp.astrologer_id IS NOT NULL THEN 1
                        ELSE 2
                    END
                ')->orderByRaw('
                    CASE
                        WHEN astrologers.chatStatus = "Online" OR astrologers.callStatus = "Online" THEN 1
                        ELSE 2
                    END
                ')->orderBy('rating', 'DESC')
                ->inRandomOrder();

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $astrologers->skip($req->startIndex);
                $astrologers->take($req->fetchRecord);
            } else {
                $astrologers->take(25);
            }

            $astrologers = $astrologers->get();

            $enrichedAstrologers = [];

            if ($astrologers && count($astrologers) > 0) {
                foreach ($astrologers as $astro) {
                    // Format document and profile URLs
                    $astro->profileImage = $formatImageUrl($astro->profileImage ?? null);
                    $astro->aadhar_card = $formatImageUrl($astro->aadhar_card ?? null);
                    $astro->pan_card = $formatImageUrl($astro->pan_card ?? null);
                    $astro->certificate = $formatImageUrl($astro->certificate ?? null);

                    // Fetch reviews
                    $review = DB::table('user_reviews')
                        ->where('astrologerId', '=', $astro->id)
                        ->get();

                    $astro->rating = 0;
                    if ($review && count($review) > 0) {
                        $avgRating = 0;
                        foreach ($review as $re) {
                            $avgRating += $re->rating;
                        }
                        $avgRating = $avgRating / count($review);
                        $astro->rating = $avgRating;
                    }

                    // Categories
                    $astrologerCategoryIds = array_map('intval', explode(',', $astro->astrologerCategoryId));
                    $astrologerCategoriesNames = DB::table('astrologer_categories')
                        ->whereIn('id', $astrologerCategoryIds)
                        ->pluck('name')->all();
                    $astro->astrologerCategory = implode(",", $astrologerCategoriesNames);

                    // Skills
                    $allSkillIds = array_map('intval', explode(',', $astro->allSkill));
                    $allSkills = DB::table('skills')->whereIn('id', $allSkillIds)->pluck('name')->all();
                    $astro->allSkill = implode(",", $allSkills);

                    $primarySkillIds = array_map('intval', explode(',', $astro->primarySkill));
                    $primarySkills = DB::table('skills')->whereIn('id', $primarySkillIds)->pluck('name')->all();
                    $astro->primarySkill = implode(",", $primarySkills);

                    // Languages
                    $languageIds = array_map('intval', explode(',', $astro->languageKnown));
                    $languages = DB::table('languages')->whereIn('id', $languageIds)->pluck('languageName')->all();
                    $astro->languageKnown = implode(",", $languages);

                    // Reviews count
                    $astro->reviews = $review ? count($review) : 0;

                    $enrichedAstrologers[] = $astro;
                }
            }

            $category->astrologers = $enrichedAstrologers;

            $result[] = $category;
        }

        return response()->json([
            'recordList' => $result,
            'status' => 200,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => false,
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}


}
