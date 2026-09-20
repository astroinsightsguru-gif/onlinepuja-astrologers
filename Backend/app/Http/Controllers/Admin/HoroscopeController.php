<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDailyHoroscopeJob;
use App\Jobs\GenerateWeeklyHoroscopeJob;
use App\Jobs\GenerateYearlyHoroscopeJob;
use App\Models\UserModel\HororscopeSign;
use App\Models\Horoscope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;

class HoroscopeController extends Controller
{
    const LOGINPATH = '/admin/login';
    const DATEFORMAT = "(DATE_FORMAT(date,'%Y-%m-%d'))";

    public int $limit = 15;

    // ──────────────────────────────────────────────────────────
    //  DAILY HOROSCOPE GENERATION
    // ──────────────────────────────────────────────────────────

    public function generateDailyHorscope()
    {
        GenerateDailyHoroscopeJob::dispatch();

        return response()->json(['message' => 'Daily horoscope generated successfully']);
    }

    // ──────────────────────────────────────────────────────────
    //  WEEKLY HOROSCOPE GENERATION
    // ──────────────────────────────────────────────────────────

    public function generateWeeklyHorscope()
    {
        GenerateWeeklyHoroscopeJob::dispatch();

        return response()->json(['message' => 'Weekly horoscope generated successfully']);
    }

    // ──────────────────────────────────────────────────────────
    //  YEARLY HOROSCOPE GENERATION
    // ──────────────────────────────────────────────────────────

    public function generateYearlyHorscope()
    {
        GenerateYearlyHoroscopeJob::dispatch();

        return response()->json(['message' => 'Yearly horoscope generated successfully']);
    }

    // ──────────────────────────────────────────────────────────
    //  ADMIN LISTING & CRUD (unchanged logic, minor cleanup)
    // ──────────────────────────────────────────────────────────

    public function getHoroscope(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return redirect(self::LOGINPATH);
        }

        $page = max(1, (int) $request->page);
        $paginationStart = ($page - 1) * $this->limit;
        $filterDate = $request->filterDate
            ? Carbon::parse($request->filterDate)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        [$startOfWeek, $endOfWeek] = $this->currentWeekRange();

        $query = Horoscope::selectRaw('horoscopes.*, REPLACE(lucky_color_code, "#", "0xff") AS color_code')
            ->whereBetween('start_date', [$startOfWeek, $endOfWeek])
            ->where('type', config('constants.WEEKLY_HORSCOPE'))
            ->orderBy('created_at', 'DESC');

        if ($request->filterDate) {
            $query->whereDate('date', $filterDate);
        }

        $totalRecords = $query->count();
        $totalPages = ceil($totalRecords / $this->limit);
        $dailyHoroscope = $query->skip($paginationStart)->take($this->limit)->get();
        $searchString = $request->searchString;

        [$start, $end] = $this->paginationRange($page, $totalRecords);

        return view('pages.horoscope', compact(
            'dailyHoroscope', 'filterDate', 'start', 'end',
            'page', 'totalRecords', 'searchString', 'totalPages'
        ));
    }

    public function getyearlyHoroscope(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return redirect(self::LOGINPATH);
        }

        $page = max(1, (int) $request->page);
        $paginationStart = ($page - 1) * $this->limit;
        $filterDate = $request->filterDate
            ? Carbon::parse($request->filterDate)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        $year = now()->year;

        $query = DB::table('horoscopes')
            ->selectRaw('horoscopes.*, REPLACE(lucky_color_code, "#", "0xff") AS color_code')
            ->where('start_date', '>=', "{$year}-01-01")
            ->where('end_date', '<=', "{$year}-12-31")
            ->where('type', config('constants.YEARLY_HORSCOPE'))
            ->orderBy('created_at', 'DESC');

        if ($request->filterDate) {
            $query->whereDate('date', $filterDate);
        }

        $totalRecords = $query->count();
        $totalPages = ceil($totalRecords / $this->limit);
        $dailyHoroscope = $query->skip($paginationStart)->take($this->limit)->get();
        $searchString = $request->searchString;

        [$start, $end] = $this->paginationRange($page, $totalRecords);

        return view('pages.yearlyhoroscope', compact(
            'dailyHoroscope', 'filterDate', 'start', 'end',
            'page', 'totalRecords', 'searchString', 'totalPages'
        ));
    }

    public function addHoroscope(Request $req)
    {
        if (!Auth::guard('web')->check())
            return redirect(self::LOGINPATH);

        $validator = Validator::make($req->all(), ['horoscopeSignId' => 'required']);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }

        foreach (['Weekly' => [$req->title, $req->weeklydesc],
                'Monthly' => [$req->monthlytitle, $req->monthlydesc],
                'Yearly' => [$req->yearlytitle, $req->yearlydesc]] as $type => [$title, $desc]) {
            $this->addHoro($type, $title, $req->horoscopeSignId, $desc, null);
        }

        return response()->json(['success' => 'Add Horoscope Successfully']);
    }

    public function editHoroscope(Request $req)
    {
        if (!Auth::guard('web')->check())
            return redirect(self::LOGINPATH);

        $validator = Validator::make($req->all(), ['horoscopeSignId' => 'required']);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }

        foreach (['Weekly' => [$req->title, $req->weeklydesc],
                'Monthly' => [$req->monthlytitle, $req->monthlydesc],
                'Yearly' => [$req->yearlytitle, $req->yearlydesc]] as $type => [$title, $desc]) {
            $this->addHoro($type, $title, $req->horoscopeSignId, $desc, $req->oldSignId);
        }

        return response()->json(['success' => 'Edit Horoscope Successfully']);
    }

    public function deleteHoroscope(Request $request)
    {
        if (!Auth::guard('web')->check())
            return redirect(self::LOGINPATH);

        DB::table('horoscope')->where('horoscopeSignId', $request->del_id)->delete();
        return redirect()->route('horoscope');
    }

    public function redirectAddHoroscope()
    {
        $signs = HororscopeSign::where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.add-horoscope', compact('signs'));
    }

    public function redirectEditHoroscope(Request $req)
    {
        $horoscopes = DB::table('horoscope')
            ->where('horoscopeSignId', $req->horoscopeSignId)
            ->get()
            ->keyBy('horoscopeType');

        $horo = [
            'weeklytitle' => $horoscopes['Weekly']->title ?? '',
            'weeklydesc' => $horoscopes['Weekly']->description ?? '',
            'monthlytitle' => $horoscopes['Monthly']->title ?? '',
            'monthlydesc' => $horoscopes['Monthly']->description ?? '',
            'yearlytitle' => $horoscopes['Yearly']->title ?? '',
            'yearlydesc' => $horoscopes['Yearly']->description ?? '',
        ];

        $signs = HororscopeSign::where('isActive', true)->orderBy('id', 'DESC')->get();
        $horoscopeSignId = $req->horoscopeSignId;

        return view('pages.edit-horoscope', compact('signs', 'horo', 'horoscopeSignId'));
    }

    public function addHoro($hroscopeType, $horoTitle, $horoscopeSignId, $description, $oldSignId): void
    {
        $data = [
            'horoscopeType' => $hroscopeType,
            'title' => $horoTitle,
            'description' => $description,
            'horoscopeSignId' => $horoscopeSignId,
        ];

        $existing = DB::table('horoscope')
            ->where('horoscopeSignId', $horoscopeSignId)
            ->where('horoscopeType', $hroscopeType)
            ->first();

        if ($existing) {
            DB::table('horoscope')->where('id', $existing->id)->update($data);
        } else {
            if ($oldSignId) {
                DB::table('horoscope')->where('horoscopeSignId', $oldSignId)->delete();
            }
            DB::table('horoscope')->insert($data);
        }
    }

    // ──────────────────────────────────────────────────────────
    //  SMALL HELPERS
    // ──────────────────────────────────────────────────────────

    private function currentWeekRange(): array
    {
        $currentDate = new DateTime();
        $currentDate->setISODate(
            (int) $currentDate->format('o'),
            (int) $currentDate->format('W'),
            1
        );
        $start = $currentDate->format('Y-m-d');
        $currentDate->modify('+6 days');
        return [$start, $currentDate->format('Y-m-d')];
    }

    private function paginationRange(int $page, int $total): array
    {
        $start = ($this->limit * ($page - 1)) + 1;
        $end = min($this->limit * $page, $total);
        return [$start, $end];
    }
}
