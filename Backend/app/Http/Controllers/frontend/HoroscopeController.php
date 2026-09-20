<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Horoscope;
use App\Models\MstControl;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HoroscopeController extends Controller
{
    /**
     * Get all horoscope signs (cached for 30 minutes).
     */
    private function getHoroscopeSignsCached()
    {
        return Cache::remember('horoscope_signs_list', 1800, function () {
            $signs = DB::table('hororscope_signs')->orderBy('id', 'DESC')->get();
            // Convert to array of arrays to match the old Http::json() format
            // Views use array syntax: $horoscopesign['slug'], $horoscopesign['name'], etc.
            $signsArray = $signs->map(function ($sign) {
                $arr = (array) $sign;
                if (!empty($arr['image']) && !preg_match('/^https?:\/\//', $arr['image'])) {
                    $arr['image'] = asset($arr['image']);
                }
                return $arr;
            })->toArray();
            return [
                'recordList' => $signsArray,
                'status' => 200,
                'totalRecords' => count($signsArray),
            ];
        });
    }

    /**
     * Get daily/weekly/yearly horoscope data for a sign (cached for 15 minutes).
     */
    private function getDailyHoroscopeCached(int $signId, string $langcode = 'en')
    {
        $cacheKey = "daily_horoscope_{$signId}_{$langcode}_" . now()->format('Y-m-d');

        return Cache::remember($cacheKey, 900, function () use ($signId, $langcode) {
            $dt = Carbon::now()->format('Y-m-d');
            $mstData = Cache::remember('mst_control_data', 3600, function () {
                return MstControl::first();
            });
            $astroApiCallType = $mstData->astro_api_call_type ?? null;

            $signRcd = DB::table('hororscope_signs')->where('id', $signId)->first();
            $signName = $signRcd->name;

            $currentDate = new DateTime();
            $currentDate->setISODate((int) $currentDate->format('o'), (int) $currentDate->format('W'), 1);
            $startOfWeekFormatted = $currentDate->format('Y-m-d');
            $currentDate->modify('+6 days');
            $endOfWeekFormatted = $currentDate->format('Y-m-d');

            $currentYear = date('Y');

            $selectRaw = 'horoscopes.*, REPLACE(lucky_color_code, "#", "0xff") AS color_code';

            // --- Today ---
            $todayHoroscope = Horoscope::selectRaw($selectRaw)
                ->where('zodiac', $signName)
                ->where('date', $dt)
                ->where('type', config('constants.DAILY_HORSCOPE'))
                ->where('langcode', $langcode)
                ->get();

            if ($todayHoroscope->isEmpty() && $langcode !== 'en') {
                $todayHoroscope = Horoscope::selectRaw($selectRaw)
                    ->where('zodiac', $signName)
                    ->where('date', $dt)
                    ->where('type', config('constants.DAILY_HORSCOPE'))
                    ->where('langcode', 'en')
                    ->get();
            }

            // --- Weekly ---
            $weeklyHoroScope = Horoscope::selectRaw($selectRaw)
                ->where('zodiac', $signName)
                ->where('start_date', '>=', $startOfWeekFormatted)
                ->where('end_date', '<=', $endOfWeekFormatted)
                ->where('type', config('constants.WEEKLY_HORSCOPE'))
                ->where('langcode', $langcode)
                ->get();

            if ($weeklyHoroScope->isEmpty() && $langcode !== 'en') {
                $weeklyHoroScope = Horoscope::selectRaw($selectRaw)
                    ->where('zodiac', $signName)
                    ->where('start_date', '>=', $startOfWeekFormatted)
                    ->where('end_date', '<=', $endOfWeekFormatted)
                    ->where('type', config('constants.WEEKLY_HORSCOPE'))
                    ->where('langcode', 'en')
                    ->get();
            }

            // --- Yearly ---
            $yearlyHoroScope = Horoscope::selectRaw($selectRaw)
                ->where('zodiac', $signName)
                ->whereYear('date', $currentYear)
                ->whereNotNull('month_range')
                ->where('type', config('constants.YEARLY_HORSCOPE'))
                ->where('langcode', $langcode)
                ->get();

            if ($yearlyHoroScope->isEmpty() && $langcode !== 'en') {
                $startOfYearFormatted = "$currentYear-01-01";
                $endOfYearFormatted = "$currentYear-12-31";
                $yearlyHoroScope = Horoscope::selectRaw($selectRaw)
                    ->where('zodiac', $signName)
                    ->where('start_date', '>=', $startOfYearFormatted)
                    ->where('end_date', '<=', $endOfYearFormatted)
                    ->where('type', config('constants.YEARLY_HORSCOPE'))
                    ->where('langcode', 'en')
                    ->get();
            }

            // Convert to nested arrays to match the old Http::json() format
            // Views use array syntax: $horoscope['vedicList']['todayHoroscope'][0]['zodiac'], etc.
            return [
                'message' => 'get daily Horoscope',
                'astroApiCallType' => $astroApiCallType,
                'vedicList' => [
                    'todayHoroscope' => $todayHoroscope->toArray(),
                    'weeklyHoroScope' => $weeklyHoroScope->toArray(),
                    'yearlyHoroScope' => $yearlyHoroScope->toArray(),
                ],
                'status' => 200,
            ];
        });
    }

    public function horoScope(Request $request)
    {
        try {
            $gethoroscopesign = $this->getHoroscopeSignsCached();
            return view('frontend.pages.horoscopesign', [
                'gethoroscopesign' => $gethoroscopesign,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dailyHoroscope(Request $request, $slug)
    {
        try {
            // Single query to get the sign record by slug
            $horoscopeSign = DB::table('hororscope_signs')->where('slug', $slug)->first();

            if (!$horoscopeSign) {
                return back()->with('error', 'Horoscope sign not found.');
            }

            // Direct DB queries with caching instead of self-HTTP calls
            $gethoroscopesign = $this->getHoroscopeSignsCached();
            $horoscope = $this->getDailyHoroscopeCached($horoscopeSign->id);

            // Reuse the record we already fetched — no redundant second query
            $signRcd = collect([$horoscopeSign]);

            return view('frontend.pages.dailyhoroscope', [
                'horoscope' => $horoscope,
                'gethoroscopesign' => $gethoroscopesign,
                'signRcd' => $signRcd,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
       public function ajaxDailyHoroscope(Request $request, $id)
    {
        try {
            $horoscope = $this->getDailyHoroscopeCached((int) $id);
            return response()->json($horoscope);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
