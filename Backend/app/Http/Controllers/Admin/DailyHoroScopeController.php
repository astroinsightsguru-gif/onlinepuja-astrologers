<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\HororscopeSign;
use App\Models\Horoscope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');
if (!defined('DATEFORMAT')) define('DATEFORMAT', "(DATE_FORMAT(date,'%Y-%m-%d'))");

class DailyHoroScopeController extends Controller
{
    public $limit = 15;
    public $paginationStart;

    public function getDailyHoroscope(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $dt = Carbon::now()->format('Y-m-d');

                $filterDate = $request->filterDate ? Carbon::parse($request->filterDate)->format('Y-m-d') : Carbon::Now()->format('Y-m-d');

                $Horoscope = Horoscope::selectRaw('horoscopes.*, REPLACE(lucky_color_code, "#", "0xff") AS color_code')
                    ->where('type', config('constants.DAILY_HORSCOPE'))
                    ->where(function ($query) use ($request) {
                        if ($request->filterDate) {
                            $filterDate = Carbon::parse($request->filterDate)->format('Y-m-d');
                            $query->where('date', $filterDate);
                        } else {
                            $query->where('date', Carbon::now()->format('Y-m-d'));
                        }
                    })
                    ->orderBy('created_at', 'DESC');

                $dailyHoroscopecount = $Horoscope->count();
                $totalRecords = $dailyHoroscopecount;

                // Rest of your code for pagination
                $totalPages = ceil($dailyHoroscopecount / $this->limit);
                $searchString = $request->searchString ? $request->searchString : null;

                $dailyHoroscope = $Horoscope->skip($paginationStart)->take($this->limit)->get();

                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;

                return view('pages.daily-horoscope', compact('dailyHoroscope', 'filterDate', 'start', 'end', 'page', 'totalRecords', 'searchString', 'totalPages'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addDailyHoroscope(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $state = DB::table('dailyhoroscopestatics')->where('horoscopeSignId', $req->horoscopeSignId)->where('horoscopeDate', $req->horoscopeDate)->get();
                $statics = array(
                    'luckyTime' => $req->luckyTime,
                    'luckyColor' => $req->luckyColour,
                    'luckyNumber' => $req->luckyNumber,
                    'moodday' => $req->moodday,
                    'horoscopeSignId' => $req->horoscopeSignId,
                    'horoscopeDate' => $req->horoscopeDate,
                );
                if ($state && count($state) > 0) {
                    DB::table('dailyhoroscopestatics')->where('id', '=', $state[0]->id)->update($statics);
                } else {
                    DB::table('dailyhoroscopestatics')->insert($statics);
                }
                $this->addDaily('Love', $req->horoscopeSignId, $req->horoscopeDate, $req->lovepercent, $req->lovedesc, null, null);
                $this->addDaily('Career', $req->horoscopeSignId, $req->horoscopeDate, $req->careerpercent, $req->careerdesc, null, null);
                $this->addDaily('Health', $req->horoscopeSignId, $req->horoscopeDate, $req->healthpercent, $req->healthdesc, null, null);
                $this->addDaily('Money', $req->horoscopeSignId, $req->horoscopeDate, $req->moneypercent, $req->moneydesc, null, null);
                $this->addDaily('Travel', $req->horoscopeSignId, $req->horoscopeDate, $req->travelpercent, $req->traveldesc, null, null);
                return response()->json([
                    'success' => 'Add Horoscope Successfully',
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addDaily($category, $horoscopeSignId, $horoscopeDate, $percent, $description, $oldSignId, $oldHoroDate)
    {
        $data = DB::table('dailyhoroscope')->where('category', $category)->where('horoscopeSignId', $horoscopeSignId)->where('horoscopeDate', $horoscopeDate)->get();
        $daily = array(
            'category' => $category,
            'description' => $description,
            'percentage' => $percent,
            'horoscopeSignId' => $horoscopeSignId,
            'horoscopeDate' => $horoscopeDate,
        );
        if ($data && count($data) > 0) {
            DB::table('dailyhoroscope')->where('id', $data[0]->id)->update($daily);
        } else {
            if ($oldSignId && $oldHoroDate) {
                DB::table('dailyhoroscope')->where('category', $category)->where('horoscopeSignId', $oldSignId)->where('horoscopeDate', $oldHoroDate)->delete();
            }
            DB::table('dailyhoroscope')
                ->insert($daily);
        }
    }

    public function editDailyHoroscope(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $state = DB::table('dailyhoroscopestatics')->where('horoscopeSignId', $req->horoscopeSignId)->where('horoscopeDate', $req->horoscopeDate)->get();
                $statics = array(
                    'luckyTime' => $req->luckyTime,
                    'luckyColor' => $req->luckyColour,
                    'luckyNumber' => $req->luckyNumber,
                    'moodday' => $req->moodday,
                    'horoscopeSignId' => $req->horoscopeSignId,
                    'horoscopeDate' => $req->horoscopeDate,
                );
                if ($state && count($state) > 0) {
                    DB::table('dailyhoroscopestatics')->where('id', '=', $state[0]->id)->update($statics);
                } else {
                    DB::table('dailyhoroscopestatics')->where('horoscopeSignId', $req->oldSignId)->where('horoscopeDate', $req->oldHoroDate)->delete();
                    DB::table('dailyhoroscopestatics')->insert($statics);
                }
                $this->addDaily('Love', $req->horoscopeSignId, $req->horoscopeDate, $req->lovepercent, $req->lovedesc, $req->oldSignId, $req->oldHoroDate);
                $this->addDaily('Career', $req->horoscopeSignId, $req->horoscopeDate, $req->careerpercent, $req->careerdesc, $req->oldSignId, $req->oldHoroDate);
                $this->addDaily('Health', $req->horoscopeSignId, $req->horoscopeDate, $req->healthpercent, $req->healthdesc, $req->oldSignId, $req->oldHoroDate);
                $this->addDaily('Money', $req->horoscopeSignId, $req->horoscopeDate, $req->moneypercent, $req->moneydesc, $req->oldSignId, $req->oldHoroDate);
                $this->addDaily('Travel', $req->horoscopeSignId, $req->horoscopeDate, $req->travelpercent, $req->traveldesc, $req->oldSignId, $req->oldHoroDate);
                return response()->json([
                    'success' => 'Edit Horoscope Successfully',
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteHoroscope(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('dailyhoroscope')->where('horoscopeSignId', '=', $req->del_id)->where('horoscopeDate', $req->horoscope_date)->delete();
                DB::table('dailyhoroscopestatics')->where('horoscopeSignId', '=', $req->del_id)->where('horoscopeDate', $req->horoscope_date)->delete();
                return redirect()->route('dailyHoroscope');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function redirectAddDailyHoroscope()
    {
        $hororscopeSign = HororscopeSign::query();
        $signs = $hororscopeSign->where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.add-daily-horoscope', compact('signs'));
    }

    public function redirectEditDailyHoroscope(Request $req)
    {
        $horoscopeSignId = $req->horoscopeSignId;
        $horoscopeDate = $req->horoscopeDate;
        $dailyHoroscope = DB::table('dailyhoroscope')->where('horoscopeSignId', $req->horoscopeSignId)->where('horoscopeDate', $req->horoscopeDate)->get();
        $loveDesc = '';
        $lovePercent = '';
        $healthDesc = '';
        $healthPercent = '';
        $careerDesc = '';
        $careerPercent = '';
        $travelDesc = '';
        $travelPercent = '';
        $moneyDesc = '';
        $moneyPercent = '';
        if ($dailyHoroscope && count($dailyHoroscope) > 0) {
            for ($i = 0; $i < count($dailyHoroscope); $i++) {
                if ($dailyHoroscope[$i]->category == 'Love') {
                    $loveDesc = $dailyHoroscope[$i]->description;
                    $lovePercent = $dailyHoroscope[$i]->percentage;
                }
                if ($dailyHoroscope[$i]->category == 'Health') {
                    $healthDesc = $dailyHoroscope[$i]->description;
                    $healthPercent = $dailyHoroscope[$i]->percentage;
                }
                if ($dailyHoroscope[$i]->category == 'Career') {
                    $careerDesc = $dailyHoroscope[$i]->description;
                    $careerPercent = $dailyHoroscope[$i]->percentage;
                }
                if ($dailyHoroscope[$i]->category == 'Travel') {
                    $travelDesc = $dailyHoroscope[$i]->description;
                    $travelPercent = $dailyHoroscope[$i]->percentage;
                }
                if ($dailyHoroscope[$i]->category == 'Money') {
                    $moneyDesc = $dailyHoroscope[$i]->description;
                    $moneyPercent = $dailyHoroscope[$i]->percentage;
                }
            }
        }
        $data = array(
            'loveDesc' => $loveDesc ? $loveDesc : '',
            'lovePercent' => $lovePercent ? $lovePercent : '',
            'careerDesc' => $careerDesc ? $careerDesc : '',
            'careerPercent' => $careerPercent ? $careerPercent : '',
            'healthDesc' => $healthDesc ? $healthDesc : '',
            'healthPercent' => $healthPercent ? $healthPercent : '',
            'moneyDesc' => $moneyDesc ? $moneyDesc : '',
            'moneyPercent' => $moneyPercent ? $moneyPercent : '',
            'travelDesc' => $travelDesc ? $travelDesc : '',
            'travelPercent' => $travelPercent ? $travelPercent : '',
        );
        $dailyHoroscopeStatics = DB::table('dailyhoroscopestatics')->where('horoscopeSignId', $req->horoscopeSignId)->where('horoscopeDate', $req->horoscopeDate)->get();
        $hororscopeSign = HororscopeSign::query();
        $signs = $hororscopeSign->where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.edit-daily-horoscope', compact('signs', 'dailyHoroscopeStatics', 'data', 'horoscopeSignId', 'horoscopeDate'));
    }

    public function getHoroscopeFeedback(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $feedback = DB::table('horoscopefeedback')->join('users', 'users.id', '=', 'horoscopefeedback.userId')->select('horoscopefeedback.*', 'users.name', 'users.contactNo', 'users.profile')->orderBy('horoscopefeedback.id', 'DESC');
                $feedbackCount = $feedback->count();
                $feedback->orderBy('id', 'DESC');
                $feedback->skip($paginationStart);
                $feedback->take($this->limit);
                $feedback = $feedback->get();
                $totalPages = ceil($feedbackCount / $this->limit);
                $totalRecords = $feedbackCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view(
                    'pages.horoscope-feedback',
                    compact('feedback', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
