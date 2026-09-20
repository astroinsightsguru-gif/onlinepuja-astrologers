<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\AdminGetCommission;
use App\Models\UserModel\UserOrder;
use App\Models\Astrologer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Exception;
if (!defined('MONTHGROUP')) define('MONTHGROUP', 'month(created_at)');

class DashboardController extends Controller
{
    public $path;

    // function for return faq Data -----
    public function faqList(Request $req)
    {
        $faq = DB::table('web_home_faqs');
        $faqCount = $faq->count();

        if ($req->startIndex >= 0 && $req->fetchRecord) {
            $faq->skip($req->startIndex);
            $faq->take($req->fetchRecord);
        }
        $faq = $faq->get();

        return response()->json([
            'message' => count($faq) > 0 ? 'FAQ data found' : 'FAQ data not found',
            'status' => 200,
            'recordList' => $faq,
            'totalCount' => $faqCount
        ], 200);
    }

    // -----

    public function termscond(Request $request)
    {
        try {
            return response()->json([
                'response' => url('terms-condition'),
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

    public function privacyPolicy(Request $request)
    {
        try {
            return response()->json([
                'response' => url('privacyPolicy'),
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

    public function getDashboard(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $totalCallRequest = DB::table('callrequest')
                    ->count();
                $totalChatRequest = DB::table('chatrequest')
                    ->count();
                $totalaiChatRequest = DB::table('ai_chat_histories')
                    ->count();
                $totalReportRequest = DB::table('user_reports')
                    ->count();

                $totalCustomer = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('user_roles.roleId', '=', '3')
                    ->where('users.isActive', '=', true)
                    ->where('users.isDelete', '=', false)
                    ->count();
                $totalAstrologer = DB::table('astrologers')
                    ->count();

                $totalOrders = DB::table('order_request')
                    ->where('orderType', '=', 'astromall')
                    ->count();

                $totalStories = DB::table('astrologer_stories')
                    ->count();

                $totalExotelReport = DB::table('exotel_reports')
                    ->count();

                $totalPujaOrders = DB::table('puja_orders')
                    ->count();

                $totalCourseOrders = DB::table('course_orders')
                    ->count();

                // Get all user orders of type 'aiChat'
                $userOrder = UserOrder::where('orderType', 'aiChat')->get();
                $aichatearning = $userOrder->sum('totalPayable');
                $totalEarning = AdminGetCommission::get()->sum('amount');

                $topAstrologers = DB::table('astrologers')
                    ->where('isVerified', '=', true)
                    ->orderBy('totalOrder', 'desc')
                    ->limit(10)
                    ->get();
                if ($topAstrologers && count($topAstrologers) > 0) {
                    foreach ($topAstrologers as $astrologer) {
                        $allSkill = array_map('intval', explode(',', $astrologer->allSkill));
                        $languages = array_map('intval', explode(',', $astrologer->languageKnown));
                        $allSkill = DB::table('skills')
                            ->whereIn('id', $allSkill)
                            ->select('name')
                            ->get();
                        $skill = $allSkill->pluck('name')->all();
                        $astrologer->allSkill = implode(',', $skill);
                        $languageKnown = DB::table('languages')
                            ->whereIn('id', $languages)
                            ->select('languageName')
                            ->get();
                        $languageKnown = $languageKnown->pluck('languageName')->all();
                        $astrologer->languageKnown = implode(',', $languageKnown);
                        $totalCall = DB::table('callrequest')
                            ->where('astrologerId', '=', $astrologer->id)
                            ->count();
                        $astrologer->totalCallRequest = $totalCall;
                        $totalChat = DB::table('chatrequest')
                            ->where('astrologerId', '=', $astrologer->id)
                            ->count();
                        $astrologer->totalChatRequest = $totalChat;
                    }
                }
                $currentDate = Carbon::now();
                $last12Months = [];
                $last12Months[] = $currentDate->format('Y-m');
                for ($i = 1; $i <= 11; $i++) {
                    $lastMonth = $currentDate->subMonth();
                    $last12Months[] = $lastMonth->format('Y-m');
                }
                $last12Months = array_reverse($last12Months);
                $call = [];
                $chat = [];
                $report = [];
                $ti = [];
                for ($i = 0; $i < count($last12Months); $i++) {
                    $last12monthyear = array_map('intval', explode('-', $last12Months[$i]))[0];
                    $last12monthofmonth = array_map('intval', explode('-', $last12Months[$i]))[1];
                    $callRequest = DB::table('callrequest')
                        ->selectRaw('month(created_at) as callMonth')
                        ->selectRaw('count(id) as totalCall')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $chatRequest = DB::table('chatrequest')
                        ->selectRaw('month(created_at) as chatMonth')
                        ->selectRaw('count(id) as totalChat')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $reportRequest = DB::table('user_reports')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('count(id) as totalReport')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $monthyCommission = DB::table('admin_get_commissions')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('sum(amount) as totalEarning')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $dateObj = DateTime::createFromFormat('!m', $last12monthofmonth);
                    $data = array(
                        'callMonth' => $dateObj->format('M'),
                        'callYear' => $last12monthyear,
                        'totalCall' => $callRequest && count($callRequest) > 0 ? $callRequest[0]->totalCall : 0,
                    );
                    $chatData = array(
                        'chatMonth' => $dateObj->format('M'),
                        'chatYear' => $last12monthyear,
                        'totalChat' => $chatRequest && count($chatRequest) > 0 ? $chatRequest[0]->totalChat : 0,
                    );
                    $reportData = array(
                        'month' => $dateObj->format('M'),
                        'reportYear' => $last12monthyear,
                        'totalReport' => $reportRequest && count($reportRequest) > 0 ? $reportRequest[0]->totalReport : 0,
                    );
                    $monthCommission = array(
                        'month' => $dateObj->format('M'),
                        'commissionYear' => $last12monthyear,
                        'totalEarning' => $monthyCommission && count($monthyCommission) > 0 ? $monthyCommission[0]->totalEarning : 0,
                    );
                    array_push($call, $data);
                    array_push($chat, $chatData);
                    array_push($report, $reportData);
                    array_push($ti, $monthCommission);
                }
                $unverifiedAstrologer = DB::table('astrologers')
                    ->where('isVerified', '=', 'false')
                    ->get();
                foreach ($unverifiedAstrologer as $astrologers) {
                    $allSkill = array_map('intval', explode(',', $astrologers->allSkill));
                    $languages = array_map('intval', explode(',', $astrologers->languageKnown));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $allSkill)
                        ->select('name')
                        ->get();
                    $skill = $allSkill->pluck('name')->all();
                    $astrologers->allSkill = implode(',', $skill);
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $languages)
                        ->select('languageName')
                        ->get();
                    $languageKnown = $languageKnown->pluck('languageName')->all();
                    $astrologers->languageKnown = implode(',', $languageKnown);
                }
                $dashboardData = ([
                    'totalCallRequest' => $totalCallRequest,
                    'totalChatRequest' => $totalChatRequest,
                    'totalaiChatRequest' => $totalaiChatRequest,
                    'totalReportRequest' => $totalReportRequest,
                    'topAstrologer' => $topAstrologers,
                    'totalEarning' => $totalEarning,
                    'totalCustomer' => $totalCustomer,
                    'totalAstrologer' => $totalAstrologer,
                    'monthlyCommission' => $ti,
                    'monthlyCallRequest' => $call,
                    'monthlyChatRequest' => $chat,
                    'monthlyReportRequest' => $report,
                    'unverifiedAstrologer' => $unverifiedAstrologer,
                    'totalOrders' => $totalOrders,
                    'totalStories' => $totalStories,
                    'totalExotelReport' => $totalExotelReport,
                    'totalPujaOrders' => $totalPujaOrders,
                    'totalCourseOrders' => $totalCourseOrders,
                ]);
                $labels = [];
                $data = [];
                $callData = [];
                $chatData = [];
                $reportData = [];
                $dashboardData = [$dashboardData];
                foreach ($dashboardData[0]['monthlyCommission'] as $label) {
                    $la = $label['month'] . ' ' . $label['commissionYear'];
                    array_push($labels, $la);
                    array_push($data, $label['totalEarning']);
                }

                foreach ($dashboardData[0]['monthlyCallRequest'] as $call) {
                    array_push($callData, $call['totalCall']);
                }
                foreach ($dashboardData[0]['monthlyChatRequest'] as $chat) {
                    array_push($chatData, $chat['totalChat']);
                }
                foreach ($dashboardData[0]['monthlyReportRequest'] as $report) {
                    array_push($reportData, $report['totalReport']);
                }
                $result = $dashboardData;

                if (!in_array(Auth::guard('web')->user()->userRoleName, ['SUPERADMIN', 'ADMIN'])) {
                    $accessRoutes = DB::table('adminpages')
                        ->join('rolepages', 'rolepages.adminPageId', '=', 'adminpages.id')
                        ->join('teammember', 'teammember.teamRoleId', '=', 'rolepages.teamRoleId')
                        ->where('teammember.userId', '=', Auth::guard('web')->user()->id)
                        ->select('adminpages.id')
                        ->get()
                        ->toArray();
                } else {
                    $accessRoutes = DB::table('adminpages')->select('id')->get()->toArray();
                }
                $accessRoutes = count($accessRoutes) > 0 ? array_column($accessRoutes, 'id') : [];
                return view('pages.dashboard-overview-1', compact('result', 'labels', 'data', 'callData', 'chatData', 'reportData', 'accessRoutes'));
            } else {
                return redirect('admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifiedAstrologer(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $eid = $request->filed_id;
                $astrologer = Astrologer::find($eid);
                $astrologer->isVerified = !$astrologer->isVerified;
                $astrologer->update();
                return redirect()->route('getDashboard');
            } else {
                return redirect('admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
