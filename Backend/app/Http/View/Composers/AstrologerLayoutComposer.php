<?php

namespace App\Http\View\Composers;

use App\Models\AdminModel\SystemFlag;
use App\Models\AstrologerModel\AstrologerStory;
use App\Models\UserModel\UserWallet;
use App\Models\ProfileBoost;
use App\Models\ProfileBoosted;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Session\Session;

class AstrologerLayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = astroauthcheck();
        $isLoggedIn = (bool) $user;

        // Fetch all system flags once
        $getsystemflags = SystemFlag::all();
        $flags = $getsystemflags->keyBy('name');

        // Initial values
        $getProfile = ['data' => ['totalWalletAmount' => 0]];
        $profileBoostData = ['recordList' => []];
        $getUserNotification = ['recordList' => []];
        $chatRequests = [];
        $stories = [];
        $astrologerId = 0;
        $astologerliveSection = null;
        $astologerliveSectionCheck = null;

        if ($isLoggedIn) {
            $astrologerId = $user->astrologerId;

            // 1. Profile Data (equivalent to /api/getProfile)
            $userWallet = UserWallet::where('userId', $user->id)->first();
            $walletAmount = $userWallet ? $userWallet->amount : 0;

            $totalWalletAmount = $user->countryCode === '+91'
                ? $walletAmount
                : (function_exists('convertinrtousd') ? convertinrtousd($walletAmount) : $walletAmount);

            $getProfile = [
                'success' => true,
                'status' => 200,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'totalWalletAmount' => $totalWalletAmount,
                    'profile' => $user->profile
                ]
            ];

            // 2. Profile Boost Data (equivalent to /api/getProfileboost)
            $profileboostListing = ProfileBoost::first();
            if ($profileboostListing) {
                $monthlyBoostCount = ProfileBoosted::where('astrologer_id', $astrologerId)
                    ->whereYear('boosted_datetime', Carbon::now()->year)
                    ->whereMonth('boosted_datetime', Carbon::now()->month)
                    ->count();

                $profileboostListing->remaining_boost = $profileboostListing->profile_boost - $monthlyBoostCount;
                $profileBoostData = [
                    'recordList' => $profileboostListing->toArray(),
                    'status' => 200
                ];
            }

            // 3. Notifications (equivalent to /api/getUserNotification)
            $notifications = DB::table('user_notifications')
                ->leftJoin('chatrequest', 'chatrequest.id', '=', 'user_notifications.chatRequestId')
                ->leftJoin('callrequest', 'callrequest.id', '=', 'user_notifications.callRequestId')
                ->leftJoin('astrologers', function ($join) {
                    $join
                        ->on('astrologers.id', '=', 'chatrequest.astrologerId')
                        ->orOn('astrologers.id', '=', 'callrequest.astrologerId');
                })
                ->leftJoin('user_device_details', 'user_device_details.userId', '=', 'astrologers.userId')
                ->where('user_notifications.userId', '=', $user->id)
                ->select(
                    'user_notifications.*',
                    'user_device_details.fcmToken',
                    'user_notifications.notification_type as notification_type',
                    'astrologers.name as astrologerName',
                    'astrologers.id as astrologerId',
                    'astrologers.profileImage as astroprofileImage',
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.id, NULL) as chatId'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.id, NULL) as callId'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.chatId, NULL) as firebaseChatId'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.channelName, NULL) as channelName'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.totalMin, NULL) as totalMin'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.call_type, NULL) as call_type'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.token, NULL) as token'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.callStatus, NULL) as callStatus'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.chatStatus, NULL) as chatStatus'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.call_duration, NULL) as call_duration'),
                    DB::raw('IF(chatrequest.id IS NOT NULL, chatrequest.chat_duration, NULL) as chat_duration'),
                    DB::raw('IF(callrequest.id IS NOT NULL, callrequest.call_method, NULL) as call_method'),
                )
                ->orderBy('user_notifications.id', 'DESC')
                ->get();

            $getUserNotification = [
                'recordList' => $notifications->map(function ($item) {
                    return (array) $item;
                })->toArray(),
                'status' => 200
            ];

            // 4. Chat Requests
            $chatRequests = DB::table('chatrequest')
                ->where('astrologerId', $astrologerId)
                ->get()
                ->map(function ($item) {
                    return (array) $item;
                })
                ->toArray();

            // 5. Stories
            $twentyFourHoursAgo = Carbon::now()->subHours(24);
            $stories = AstrologerStory::select(
                '*',
                DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'),
            )
                ->where('created_at', '>=', $twentyFourHoursAgo)
                ->where('created_at', '<=', Carbon::now())
                ->where('astrologerId', $astrologerId)
                ->orderBy('created_at', 'DESC')
                ->get();

            // 6. Live Status
            $astologerliveSectionCheck = DB::table('liveastro')
                ->where('astrologerId', $astrologerId)
                ->first();

            $astologerliveSection = DB::table('astrologers')
                ->where('id', $astrologerId)
                ->select('live_sections')
                ->first();
        }

        $astrologerCategories = DB::table('astrologer_categories')
            ->where('isActive', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        // Language settings
        $webLanguage = $flags->get('WebLanguage');
        $selectedLanguages = $webLanguage ? json_decode($webLanguage->value, true) : [];
        $includedLanguages = implode(',', $selectedLanguages ?: []);

        // Master Astrologer for footer
        $aiAstrologer = $flags->get('AiAstrologer');
        $masterAstrologer = null;
        if (!empty($aiAstrologer)) {
            $masterAstrologer = \App\Models\AiAstrologerModel\AiAstrologer::where('type', 'master')->select('image')->first();
        }

        $view->with([
            'systemFlags' => $flags,
            'isLoggedIn' => $isLoggedIn,
            'getProfile' => $getProfile,
            'profileBoostData' => $profileBoostData,
            'getUserNotification' => $getUserNotification,
            'chatRequests' => $chatRequests,
            'includedLanguages' => $includedLanguages,
            'masterAstrologer' => $masterAstrologer,
            'astrotoken' => (new Session())->get('astrotoken'),
            'stories' => $stories,
            'astrologerId' => $astrologerId,
            'astologerliveSectionCheck' => $astologerliveSectionCheck,
            'astologerliveSection' => $astologerliveSection,
            'astrologerCategories' => $astrologerCategories,
            'walletType' => $flags->get('walletType')->value ?? 'coin',
            'coinIcon' => $flags->get('coinIcon')->value ?? null,
            'user' => $user,
            'currency' => $flags->get('currencySymbol'),
        ]);
    }
}
