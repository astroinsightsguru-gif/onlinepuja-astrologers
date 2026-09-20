<?php

namespace App\Http\View\Composers;

use App\Models\AdminModel\SystemFlag;
use App\Models\AiAstrologerModel\AiAstrologer;
use App\Models\AstrologerModel\AstrologerCategory;
use App\Models\UserModel\UserWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Session\Session;

class FrontendLayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = authcheck();
        $isLoggedIn = (bool) $user;

        // Fetch all system flags once
        $systemFlags = SystemFlag::all();

        // Map system flags for easy access
        $flags = $systemFlags->keyBy('name');

        // Fetch astrologer categories

        // Fetch astrologer categories
        $astrologerCategories = AstrologerCategory::where('isActive', 1)
            ->orderBy('id', 'DESC')
            ->get();

        // Fetch countries
        $countries = DB::table('countries')
            ->orderByRaw('CASE WHEN phonecode = 91 THEN 0 ELSE 1 END')
            ->get();

        // Fetch user specific data
        $userNotifications = [];
        $chatRequests = [];
        $userWalletAmount = 0;
        $isProfileComplete = false;

        if ($isLoggedIn) {
            $userNotifications = DB::table('user_notifications')
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

            $chatRequests = DB::table('chatrequest')->where('userId', $user->id)->get();
            $userWalletAmount = UserWallet::where('userId', $user->id)->value('amount') ?? 0;

            $isProfileComplete = ($user->name && $user->birthDate && $user->birthPlace);
        }

        // Language settings
        $webLanguage = $systemFlags->where('name', 'WebLanguage')->first();
        $selectedLanguages = $webLanguage ? json_decode($webLanguage->value, true) : [];
        $includedLanguages = implode(',', $selectedLanguages ?: []);

        // Master Astrologer for footer
        $aiAstrologer = $flags->get('AiAstrologer');
        $masterAstrologer = null;
        if (!empty($aiAstrologer)) {
            $masterAstrologer = AiAstrologer::where('type', 'master')->select('image')->first();
        }

        $view->with([
            'systemFlags' => $flags,
            'astrologerCategories' => $astrologerCategories,
            'countries' => $countries,
            'userNotifications' => $userNotifications,
            'chatRequests' => $chatRequests,
            'userWalletAmount' => $userWalletAmount,
            'isProfileComplete' => $isProfileComplete,
            'isLoggedIn' => $isLoggedIn,
            'includedLanguages' => $includedLanguages,
            'masterAstrologer' => $masterAstrologer,
        ]);
    }
}
