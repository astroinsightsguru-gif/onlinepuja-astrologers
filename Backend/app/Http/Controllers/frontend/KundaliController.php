<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Kundali;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Session\Session;

class KundaliController extends Controller
{
   public function getPanchang(Request $request)
    {
        // ── 1. Resolve & validate the requested date ──────────────────────────────
        $panchangDate = $request->input('panchangDate');

        if ($panchangDate) {
            try {
                $dateCarbon = Carbon::createFromFormat('Y-m-d', $panchangDate)->startOfDay();
            } catch (\Exception $e) {
                $dateCarbon = Carbon::today();
                $panchangDate = null;
            }
        } else {
            $dateCarbon = Carbon::today();
        }

        $dateForApi = $dateCarbon->format('d/m/Y');
        $dateForCache = $dateCarbon->format('Y-m-d');
        $year = $dateCarbon->year;
        $month = $dateCarbon->month;

        // ── 2. Geo-location ───────────────────────────────────────────────────────
        $reqLat = $request->input('lat') ?? $request->input('latitude');
        $reqLon = $request->input('lon') ?? $request->input('longitude');
        $reqCity = $request->input('city') ?? $request->input('cityName') ?? $request->input('city_name');
        $reqTimezone = $request->input('timezone') ?? $request->input('tz');
        $reqRegion = $request->input('region') ?? $request->input('regionName') ?? $request->input('region_name');
        $reqCountry = $request->input('country') ?? $request->input('countryName') ?? $request->input('country_name');

        $session = null;
        if (class_exists(Session::class)) {
            try {
                $session = new Session();
            } catch (\Exception $e) {
                // Ignore
            }
        }

        if ($reqLat && $reqLon) {
            $geoData = [
                'ip' => $request->ip(),
                'lat' => $reqLat,
                'lon' => $reqLon,
                'city' => $reqCity ?? 'Custom Location',
                'region' => $reqRegion ?? '',
                'country' => $reqCountry ?? '',
                'timezone' => $reqTimezone ?? 'Asia/Kolkata',
            ];
            if ($session) {
                $session->set('panchang_location', $geoData);
            }
        } elseif ($session && $session->has('panchang_location')) {
            $geoData = $session->get('panchang_location');
        } else {
            $ip = $request->server('HTTP_CF_CONNECTING_IP')
                ?? $request->server('HTTP_X_FORWARDED_FOR')
                ?? $request->ip();

            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);

            // Replace loopback / private IPs with a fallback public IP
            if (
                in_array($ip, ['127.0.0.1', '::1', ''], true) ||
                filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
            ) {
                $ip = '103.238.108.209';  // New Delhi fallback
            }

            $defaultGeo = [
                'ip' => $ip,
                'lat' => '28.6139',
                'lon' => '77.2090',
                'city' => 'New Delhi',
                'region' => 'Delhi',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
            ];

            $geoData = cache()->remember("geo_{$ip}", now()->addHours(24), function () use ($ip, $defaultGeo) {
                try {
                    $position = Location::get($ip);
                    if ($position) {
                        return [
                            'ip' => $ip,
                            'lat' => $position->latitude,
                            'lon' => $position->longitude,
                            'city' => $position->cityName,
                            'region' => $position->regionName,
                            'country' => $position->countryName,
                            'timezone' => $position->timezone,
                        ];
                    }
                } catch (\Exception $e) {
                    \Log::warning("Panchang geo lookup failed for IP {$ip}: " . $e->getMessage());
                }
                return $defaultGeo;
            });
        }

        $latitude = $geoData['lat'];
        $longitude = $geoData['lon'];
        $timezone = $geoData['timezone'];
        $tzOffset = $this->getTimezoneOffset($timezone);
        $time = Carbon::now($timezone)->format('H:i');

        // ── 3. API key (cached 6 h) ───────────────────────────────────────────────
        $apiKey = cache()->remember('vedic_astro_api_key_new', now()->addHours(6), function () {
            $row = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
            return $row ? $row->value : null;
        });

        $googleMapApiKey = cache()->remember('google_map_api_key', now()->addHours(6), function () {
            $row = DB::table('systemflag')->where('name', 'googleMapApiKey')->first();
            return $row ? $row->value : null;
        });

        if (!$apiKey) {
            \Log::error('Panchang: vedicAstroAPI key not found in systemflag table.');
            return view('frontend.pages.panchang', [
                'getPanchang' => [],
                'geoData' => $geoData,
                'dateCarbon' => $dateCarbon,
                'professionTitle' => $this->getProfessionTitle(),
                'error' => 'API configuration missing. Please contact support.',
                'googleMapApiKey' => $googleMapApiKey,
            ]);
        }

        // ── 4. Shared param groups ────────────────────────────────────────────────
        $locationParams = [
            'lat' => $latitude,
            'lon' => $longitude,
            'tz' => $tzOffset,
            'api_key' => $apiKey,
            'lang' => 'en',
        ];

        $dateTimeParams = array_merge($locationParams, [
            'date' => $dateForApi,
            'time' => $time,
        ]);

        // Round coordinate keys to 2 decimal places (~1.1km accuracy)
        // This dramatically increases cache hit rate across different users in the same region, preventing slow loads
        $latKey = round($latitude, 2);
        $lonKey = round($longitude, 2);

        // ── 5. All API calls — each cached at its own TTL ─────────────────────────

        // Main Panchang — 12 h per date + location
        $panchangCacheKey = "panchang_{$dateForCache}_{$latKey}_{$lonKey}";
        $getPanchang = cache()->remember($panchangCacheKey, now()->addHours(12), function () use ($dateTimeParams) {
            $data = $this->fetchVedicApi('panchang/panchang', $dateTimeParams);
            if (empty($data) || empty($data['response'])) {
                return [];
            }
            return $data;
        });

        // If cache contains an empty response, clear it so next load tries again
        if (empty($getPanchang) || empty($getPanchang['response'])) {
            cache()->forget($panchangCacheKey);
            $getPanchang = [];
        }

        // Debug: log the keys available in the panchang response for troubleshooting
        $panchangRes = $getPanchang['response'] ?? [];
        $panchangAdv = $panchangRes['advanced_details'] ?? [];

        // ── 6. Return view ────────────────────────────────────────────────────────
        return view('frontend.pages.panchang', [
            'getPanchang' => $getPanchang,
            'geoData' => $geoData,
            'dateCarbon' => $dateCarbon,
            'professionTitle' => getProfessionTitle(),
            'googleMapApiKey' => $googleMapApiKey,
        ]);
    }


    private function getTimezoneOffset($timezone)
    {
        $time = new \DateTime('now', new \DateTimeZone($timezone));
        return $time->getOffset() / 3600;  // Convert seconds to hours
    }

    public function getkundali(Request $request)
    {
        Artisan::call('cache:clear');

        $session = new Session();
        $token = $session->get('token');

        $getkundaliprice = Http::withoutVerifying()->post(url('/') . '/api/pdf/price', [
            'token' => $token,
        ])->json();

        $getkundali = Http::withoutVerifying()->post(url('/') . '/api/getkundali', [
            'token' => $token,
        ])->json();

        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag', [
            'token' => $token,
        ])->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
        // dd( $getkundaliprice);

        return view('frontend.pages.kundali', [
            'getkundali' => $getkundali,
            'getkundaliprice' => $getkundaliprice,
            'currency' => $currency,
        ]);
    }

    public function kundaliMatch(Request $request)
    {
        return view('frontend.pages.kundali-matching', []);
    }

    public function kundaliMatchReport(Request $request)
    {
        $KundaliMatching = Http::withoutVerifying()->post(url('/') . '/api/KundaliMatching/report', [
            'male_kundli_id' => $request->male_kundli_id,
            'female_kundli_id' => $request->female_kundli_id,
        ])->json();

        $kundalimale = Kundali::where('id', $request->male_kundli_id)->first();
        $kundalifemale = Kundali::where('id', $request->female_kundli_id)->first();

        return view('frontend.pages.kundali-match-report', [
            'KundaliMatching' => $KundaliMatching,
            'kundalimale' => $kundalimale,
            'kundalifemale' => $kundalifemale,
        ]);
    }

    public function kundaliReport(Request $request)
    {
        // Initialize session
        $session = new Session();

        // Create unique session key based on request parameters
        $sessionKey = 'kundali_report_' . $request->kundali_id . '_' . ($request->lang ?? 'en');
        if ($session->has($sessionKey)) {
            $KundaliReport = $session->get($sessionKey);
        } else {
            // Make API call if not in session
            $KundaliReport = Http::withoutVerifying()->post(url('/') . '/api/kundali/getKundaliReport', [
                'kundali_id' => $request->kundali_id,
                'lang' => $request->lang
            ])->json();

            // Store in session for subsequent requests
            $session->set($sessionKey, $KundaliReport);
        }

        return view('frontend.pages.kundali-report', compact('KundaliReport'));
    }
}
