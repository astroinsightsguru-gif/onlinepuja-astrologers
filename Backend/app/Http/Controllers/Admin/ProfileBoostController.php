<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\ProfileBoost;
use App\Models\ProfileBoosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class ProfileBoostController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addProfileBoost()
    {
        return view('pages.profile-boost');
    }

    // ---------------------------------------------------------------------------------------------------------
    public function getProfileList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $query = ProfileBoost::query();

                $userCount = $query->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                $profilelist = $query
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();
                return view('pages.profile-boost-list', compact('profilelist', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_commission' => 'required',
                'call_commission' => 'required',
                'profile_boost' => 'nullable|string|max:255',
                'profile_boost_benefits.*' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Prepare profile benefits as JSON
            $benefits = [];
            if ($request->has('profile_boost_benefits')) {
                foreach ($request->profile_boost_benefits as $benefit) {
                    if ($benefit) {
                        $benefits[] = $benefit;
                    }
                }
            }

            // dd($benefits);

            if (Auth::guard('web')->check()) {
                // Save to database
                ProfileBoost::create([
                    'chat_commission' => $request->chat_commission,
                    'call_commission' => $request->call_commission,
                    'profile_boost_benefits' => $benefits,
                    'profile_boost' => $request->profile_boost,
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
        // Redirect or return response
        return redirect()->route('pages.profile-boost-list')->with('success', 'Profile boost successfully!');
    }

    // ----------------------------------------------------------------------------------------------------------------------------
    public function editprofileboost($id)
    {
        $profileBoost = ProfileBoost::findOrFail($id);
        return view('pages.profile-boost', compact('profileBoost'));
    }

    // --------------------------------------------------------------------------------------------------------------------
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_commission' => 'required',
                'call_commission' => 'required',
                'profile_boost' => 'nullable|string|max:255',
                'profile_boost_benefits.*' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Find the existing puja
            $profileBoost = ProfileBoost::findOrFail($id);

            // Prepare puja_benefits as JSON
            $benefits = [];
            if ($request->has('profile_boost_benefits')) {
                foreach ($request->profile_boost_benefits as $benefit) {
                    if ($benefit) {
                        $benefits[] = $benefit;
                    }
                }
            }

            if (Auth::guard('web')->check()) {
                // Update existing puja record
                $profileBoost->update([
                    'chat_commission' => $request->chat_commission,
                    'call_commission' => $request->call_commission,
                    'profile_boost_benefits' => $benefits,
                    'profile_boost' => $request->profile_boost,
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }

        // Redirect or return response
        return redirect()->route('profile-list')->with('success', 'Profile boost update successfully!');
    }

    // ------------------------------------------------------------------------------------------------------------------------------

    // Profile Boost History

    // -------------------------------------------------------------------------------------------------------------------------------------------------------
    public function profileBoostHistory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $month = request('month');
                $year = request('year');

                $month = request('month');
                $year = request('year');

                $month = request('month', date('m'));  // Default to current month if no month is selected
                $year = request('year', date('Y'));  // Default to current year if no year is selected

                $boosted = ProfileBoosted::orderBy('id', 'DESC')
                    ->join('astrologers', 'astrologers.id', '=', 'astrologer_boosted_profiles.astrologer_id')
                    ->leftJoin(DB::raw('(
                             SELECT astrologer_id,
                                    YEAR(boosted_datetime) AS boost_year,
                                    MONTH(boosted_datetime) AS boost_month,
                                    COUNT(*) as monthly_boost_count
                             FROM astrologer_boosted_profiles
                             GROUP BY astrologer_id, boost_year, boost_month
                     ) as monthly_boosts'),
                        function ($join) use ($month, $year) {
                            $join
                                ->on('monthly_boosts.astrologer_id', '=', 'astrologers.id')
                                ->whereRaw('YEAR(astrologer_boosted_profiles.boosted_datetime) = ?', [$year])
                                ->whereRaw('MONTH(astrologer_boosted_profiles.boosted_datetime) = ?', [$month]);
                        })
                    ->select(
                        'astrologer_boosted_profiles.*',
                        'astrologers.name',
                        'astrologers.profileImage',
                        'astrologers.id as astrologerId',
                        DB::raw('DATE_ADD(astrologer_boosted_profiles.boosted_datetime, INTERVAL 24 HOUR) as enddate_time'),
                        DB::raw('COALESCE(monthly_boosts.monthly_boost_count, 0) as monthly_boost_count'),
                        DB::raw('MONTHNAME(astrologer_boosted_profiles.boosted_datetime) as monthname'),
                        DB::raw('YEAR(astrologer_boosted_profiles.boosted_datetime) as yearname')
                    );

                // Apply filters if the month and year are selected
                if ($month) {
                    $boosted = $boosted->whereMonth('boosted_datetime', $month);
                }
                if ($year) {
                    $boosted = $boosted->whereYear('boosted_datetime', $year);
                }

                // Pagination
                $boosted = $boosted->skip($paginationStart)->take($this->limit)->groupBy('astrologer_boosted_profiles.id')->get();
                $totalRecords = $boosted->count();
                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.astrologer-profile-boostedList', compact('boosted', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
