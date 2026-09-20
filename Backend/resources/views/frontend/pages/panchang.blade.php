@extends('frontend.layout.master')
@push('styles')
<link rel="stylesheet" href="{{ asset('public/frontend/css/panchang-custom.css') }}">
@endpush
@section('content')
@php
$selectedDate = request('panchangDate', date('Y-m-d'));
$todayActive = $selectedDate === date('Y-m-d');
$tomorrowActive = $selectedDate === date('Y-m-d', strtotime('+1 day'));
$displayDate = date('l, d F Y', strtotime($selectedDate));
$res = $getPanchang['response'] ?? [];
$adv = $res['advanced_details'] ?? $res;
$masa = $adv['masa'] ?? [];
$years = $adv['years'] ?? [];
$currentYear = isset($dateCarbon) ? $dateCarbon->year : date('Y');
$currentMonth = isset($dateCarbon) ? $dateCarbon->month : date('m');
$currentMonthName = isset($dateCarbon) ? $dateCarbon->format('F') : date('F');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

// Safe string output helper — avoids htmlspecialchars() TypeError on arrays/objects
function pv($val) {
if (is_array($val) || is_object($val)) return null;
return $val !== null && $val !== '' ? (string) $val : null;
}
// Format time range from array or string
function pvTime($val) {
if (is_array($val)) {
$s = $val['start'] ?? '';
$e = $val['end'] ?? '';
return ($s || $e) ? "$s – $e" : null;
}
return pv($val);
}
@endphp


{{-- ─── Breadcrumb ─── --}}
<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize;">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none"><i class="fa fa-home font-18"></i></a>
                        <i class="fa fa-chevron-right"></i>
                        <a href="#" style="color:white;text-decoration:none">Panchang</a>
                        <i class="fa fa-chevron-right"></i>
                        @if($tomorrowActive) Tomorrow's Panchang @else Today's Panchang @endif
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ─── Hero ─── --}}
<div class="panchang-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>
                    @if($tomorrowActive) <i class="fas fa-moon"></i> Tomorrow's Panchang
                    @elseif($todayActive) <i class="fas fa-sun"></i> Today's Panchang
                    @else <i class="fas fa-calendar"></i> Panchang for {{ date('d M Y', strtotime($selectedDate)) }}
                    @endif
                </h1>
                <p class="hero-subtitle">@if($tomorrowActive) Kal Ka Panchang @elseif($todayActive) Aaj Ka Panchang @else Dainik Panchang @endif — Hindu Calendar & Auspicious Timings</p>
                <div class="hero-date"><i class="fas fa-calendar-alt"></i> {{ $displayDate }}</div>
                <div class="hero-location d-flex flex-wrap align-items-center mt-2" style="gap: 8px; font-size: 14px; color: #333;">
                    <span><i class="fas fa-map-marker-alt" style="color: #EE4E5E;"></i> <span id="current-location-display" style="font-weight: 500;">{{ $geoData['city'] ?? 'New Delhi' }}{{ !empty($geoData['region']) ? ', ' . $geoData['region'] : '' }} — {{ $geoData['country'] ?? 'India' }}</span></span>
                    <button id="btn-detect-location" class="btn btn-xs rounded-pill" style="background: #fff; border: 1px solid #EE4E5E; color: #EE4E5E; font-size: 11px; padding: 4px 12px; cursor: pointer; transition: all 0.2s ease; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fas fa-crosshairs"></i> Detect Location
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-right d-none d-md-block">
                @if(!empty($res['tithi']))
                <div class="hero-details-card">
                    <div style="text-align: center;">
                        <div class="hero-details-label">Tithi</div>
                        <div class="hero-details-value-tithi">{{ pv($res['tithi']['name'] ?? null) ?? '—' }}</div>
                        <hr class="hero-details-divider">
                        <div class="hero-details-label">Nakshatra</div>
                        <div class="hero-details-value-nakshatra">{{ pv($res['nakshatra']['name'] ?? null) ?? '—' }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ─── Date Nav ─── --}}
<div class="date-nav-bar">
    <div class="container">
        <div class="date-nav-pills">
            <a class="date-nav-btn {{ $todayActive ? 'active' : '' }}" href="{{ route('front.getPanchang') }}"><i class="fas fa-sun"></i> Today</a>
            <a class="date-nav-btn {{ $tomorrowActive ? 'active' : '' }}" href="{{ route('front.getPanchang', ['panchangDate' => date('Y-m-d', strtotime('+1 day'))]) }}"><i class="fas fa-moon"></i> Tomorrow</a>
            <a class="date-nav-btn calendar-toggle-btn" data-toggle="collapse" href="#panchangCalendar" role="button"><i class="fas fa-calendar-day"></i> Pick a Date</a>
        </div>
        <div class="collapse" id="panchangCalendar">
            <div class="calendar-dropdown">
                <div class="text-center"><span class="cal-month-label">{{ $currentMonthName }} {{ $currentYear }}</span></div>
                <div class="cal-day-grid">
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php $date=sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $day); @endphp
                        <a href="{{ route('front.getPanchang', ['panchangDate' => $date]) }}" class="cal-day {{ request('panchangDate', date('Y-m-d')) == $date ? 'selected' : '' }}">{{ $day }}</a>
                        @endfor
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Main Content ─── --}}
<div class="panchang-content">
    <div class="container">
        @if(isset($error))
        <div class="alert alert-danger text-center mt-4"><i class="fas fa-exclamation-triangle"></i> {{ $error }}</div>
        @endif

        @if(!empty($res['tithi']))

        {{-- ════════ BIG FIVE ELEMENTS ════════ --}}
        <div class="text-center mb-2"><small class="text-uppercase" style="letter-spacing: 3px; color: #EE4E5E; font-weight: 700; font-size: 11px;">Pancha Angas</small></div>
        <h2 class="text-center font-24 mb-3" style="font-weight: 700; color: #333;">The <span style="color: #EE4E5E;">Five Elements</span> of Panchang</h2>

        <div class="panch-five">
            {{-- Tithi --}}
            <div class="panch-element">
                <span class="el-symbol">🌙</span>
                <span class="el-name">Tithi</span>
                <span class="el-value">{{ pv($res['tithi']['name'] ?? null) ?? '—' }}</span>
                @if(!empty($res['tithi']['end']))
                <span class="el-sub">Until {{ date('h:i A', strtotime($res['tithi']['end'])) }}</span>
                @elseif(!empty($res['tithi']['end_time_ms']))
                <span class="el-sub">Until {{ date('h:i A', $res['tithi']['end_time_ms']/1000) }}</span>
                @endif
                @if(!empty($res['tithi']['diety']) || !empty($res['tithi']['deity']))
                <span class="el-sub" style="font-size:11px;">Deity: {{ pv($res['tithi']['diety'] ?? $res['tithi']['deity'] ?? null) }}</span>
                @endif
            </div>
            {{-- Nakshatra --}}
            <div class="panch-element">
                <span class="el-symbol">⭐</span>
                <span class="el-name">Nakshatra</span>
                <span class="el-value">{{ pv($res['nakshatra']['name'] ?? null) ?? '—' }}</span>
                <span class="el-sub">
                    @if(!empty($res['nakshatra']['lord'])) Lord: {{ pv($res['nakshatra']['lord']) }} @endif
                    @if(!empty($res['nakshatra']['pada'])) | Pada: {{ pv($res['nakshatra']['pada']) }} @endif
                </span>
                @if(!empty($res['nakshatra']['end']))
                <span class="el-sub">Until {{ date('h:i A', strtotime($res['nakshatra']['end'])) }}</span>
                @elseif(!empty($res['nakshatra']['end_time_ms']))
                <span class="el-sub">Until {{ date('h:i A', $res['nakshatra']['end_time_ms']/1000) }}</span>
                @endif
            </div>
            {{-- Yoga --}}
            <div class="panch-element">
                <span class="el-symbol">🔱</span>
                <span class="el-name">Yoga</span>
                <span class="el-value">{{ pv($res['yoga']['name'] ?? null) ?? '—' }}</span>
                @if(!empty($res['yoga']['end']))
                <span class="el-sub">Until {{ date('h:i A', strtotime($res['yoga']['end'])) }}</span>
                @elseif(!empty($res['yoga']['end_time_ms']))
                <span class="el-sub">Until {{ date('h:i A', $res['yoga']['end_time_ms']/1000) }}</span>
                @endif
            </div>
            {{-- Karana --}}
            <div class="panch-element">
                <span class="el-symbol">🪷</span>
                <span class="el-name">Karana</span>
                <span class="el-value">{{ pv($res['karana']['name'] ?? null) ?? '—' }}</span>
                @if(!empty($res['karana']['end']))
                <span class="el-sub">Until {{ date('h:i A', strtotime($res['karana']['end'])) }}</span>
                @elseif(!empty($res['karana']['end_time_ms']))
                <span class="el-sub">Until {{ date('h:i A', $res['karana']['end_time_ms']/1000) }}</span>
                @endif
            </div>
            {{-- Rasi --}}
            <div class="panch-element">
                <span class="el-symbol">♈</span>
                <span class="el-name">Rasi</span>
                <span class="el-value">{{ pv($res['rasi']['name'] ?? $res['moon_sign']['name'] ?? $res['moon_sign'] ?? null) ?? '—' }}</span>
                @if(!empty($res['rasi']['lord']))
                <span class="el-sub">Lord: {{ pv($res['rasi']['lord']) }}</span>
                @endif
            </div>
        </div>

        {{-- ════════ ROW 1: Celestial Timings + Hindu Calendar ════════ --}}
        <div class="row mb-4">
            {{-- Celestial Timings --}}
            <div class="col-md-6 mb-4">
                <div class="panchang-card">
                    <div class="panchang-card-header" style="background: linear-gradient(135deg, #1A6896, #0D3D5C);">
                        <i class="fas fa-sun"></i>
                        <h3>Celestial Timings</h3>
                    </div>
                    <div class="panchang-card-body">
                        @if(pv($adv['sun_rise'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-arrow-up" style="color:#FF9800;"></i> Sunrise</div>
                            <div class="panchang-row-value"><strong>{{ pv($adv['sun_rise']) }}</strong></div>
                        </div>
                        @endif
                        @if(pv($adv['sun_set'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-arrow-down" style="color:#E65100;"></i> Sunset</div>
                            <div class="panchang-row-value"><strong>{{ pv($adv['sun_set']) }}</strong></div>
                        </div>
                        @endif
                        @if(pv($adv['moon_rise'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-moon" style="color:#5C6BC0;"></i> Moonrise</div>
                            <div class="panchang-row-value"><strong>{{ pv($adv['moon_rise']) }}</strong></div>
                        </div>
                        @endif
                        @if(pv($adv['moon_set'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-moon" style="color:#283593;"></i> Moonset</div>
                            <div class="panchang-row-value"><strong>{{ pv($adv['moon_set']) }}</strong></div>
                        </div>
                        @endif
                        @php
                        $dayDuration = '';
                        if (!empty($adv['sun_rise']) && !empty($adv['sun_set'])) {
                        $rise = strtotime($adv['sun_rise']); $set = strtotime($adv['sun_set']);
                        if ($set > $rise) { $diff = $set - $rise; $dayDuration = floor($diff/3600).' hrs '.floor(($diff%3600)/60).' mins'; }
                        }
                        if (empty($dayDuration) && !empty($adv['day_duration'])) $dayDuration = $adv['day_duration'];
                        @endphp
                        @if(!empty($dayDuration))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-hourglass-start"></i> Day Duration</div>
                            <div class="panchang-row-value">{{ $dayDuration }}</div>
                        </div>
                        @endif
                        @if(pv($adv['next_full_moon'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-circle" style="color:#FFD700;"></i> Next Full Moon</div>
                            <div class="panchang-row-value">{{ pv($adv['next_full_moon']) }}</div>
                        </div>
                        @endif
                        @if(pv($adv['next_new_moon'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="far fa-circle" style="color:#37474F;"></i> Next New Moon</div>
                            <div class="panchang-row-value">{{ pv($adv['next_new_moon']) }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Hindu Calendar --}}
            <div class="col-md-6 mb-4">
                <div class="panchang-card">
                    <div class="panchang-card-header" style="background: linear-gradient(135deg, #C9963C, #A0720A);">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Hindu Calendar Details</h3>
                    </div>
                    <div class="panchang-card-body">
                        {{-- Vedic Day --}}
                        @php
                        $varDay = $res['day'] ?? date('l', strtotime($selectedDate));
                        $varDayName = is_array($varDay) ? ($varDay['name'] ?? '—') : $varDay;
                        @endphp
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-sun"></i> Vedic Day (Vara)</div>
                            <div class="panchang-row-value">{{ $varDayName }} @if(!empty($adv['vaara'])) ({{ pv($adv['vaara']) }}) @endif</div>
                        </div>

                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-bookmark"></i> Amanta Month</div>
                            <div class="panchang-row-value">{{ pv($masa['amanta_name'] ?? null) ?? '—' }} @if(!empty($masa['alternate_amanta_name'])) ({{ pv($masa['alternate_amanta_name']) }}) @endif</div>
                        </div>

                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="far fa-bookmark"></i> Purnimanta</div>
                            <div class="panchang-row-value">{{ pv($masa['purnimanta_name'] ?? null) ?? '—' }} @if(!empty($masa['alternate_purnimanta_name'])) ({{ pv($masa['alternate_purnimanta_name']) }}) @endif</div>
                        </div>

                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-adjust"></i> Paksha</div>
                            <div class="panchang-row-value">{{ pv($masa['paksha'] ?? null) ?? '—' }}</div>
                        </div>

                        @if(!empty($res['ritu']) || !empty($masa['ritu']))
                        @php $rituVal = $res['ritu'] ?? $masa['ritu'] ?? '—'; $rituName = is_array($rituVal) ? ($rituVal['name'] ?? '—') : $rituVal; @endphp
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-leaf"></i> Ritu (Season)</div>
                            <div class="panchang-row-value">{{ $rituName }}</div>
                        </div>
                        @endif

                        @if(!empty($res['ayana']) || !empty($masa['ayana']))
                        @php $ayanaVal = $res['ayana'] ?? $masa['ayana'] ?? '—'; $ayanaName = is_array($ayanaVal) ? ($ayanaVal['name'] ?? '—') : $ayanaVal; @endphp
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-sun"></i> Ayana</div>
                            <div class="panchang-row-value">{{ $ayanaName }}</div>
                        </div>
                        @endif

                        @if(!empty($years['vikram_samvaat']) || !empty($adv['vikram_samvat']))
                        @php $vikram = $years['vikram_samvaat'] ?? $adv['vikram_samvat'] ?? '—'; $vikramName = is_array($vikram) ? ($vikram['name'] ?? '—') : $vikram; @endphp
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-history"></i> Vikram Samvat</div>
                            <div class="panchang-row-value">{{ $vikramName }} @if(!empty($years['vikram_samvaat_name'])) ({{ pv($years['vikram_samvaat_name']) }}) @endif</div>
                        </div>
                        @endif

                        @if(!empty($years['saka']) || !empty($adv['shaka_samvat']))
                        @php $shaka = $years['saka'] ?? $adv['shaka_samvat'] ?? '—'; $shakaName = is_array($shaka) ? ($shaka['name'] ?? '—') : $shaka; @endphp
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-history"></i> Shaka Samvat</div>
                            <div class="panchang-row-value">{{ $shakaName }}</div>
                        </div>
                        @endif

                        @if(!empty($years['kali']))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-history"></i> Kali Samvat</div>
                            <div class="panchang-row-value">{{ pv($years['kali']) }}</div>
                        </div>
                        @endif

                        @if(pv($adv['disha_shool'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-compass"></i> Disha Shool</div>
                            <div class="panchang-row-value">{{ pv($adv['disha_shool']) }}</div>
                        </div>
                        @endif

                        @if(pv($adv['moon_yogini_nivas'] ?? null))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-compass"></i> Yogini Nivas</div>
                            <div class="panchang-row-value"><strong>{{ pv($adv['moon_yogini_nivas']) }}</strong></div>
                        </div>
                        @endif

                        @if(!empty($adv['ahargana']))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-clock"></i> Ahargana</div>
                            <div class="panchang-row-value">{{ round($adv['ahargana'], 2) }} days</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════ ROW 2: Auspicious + Inauspicious ════════ --}}
        @php
        $brahma = $res['brahma_muhurta'] ?? $adv['brahma_muhurta'] ?? '';
        $abhijit = $res['abhijit_muhurta'] ?? $adv['abhijit_muhurta'] ?? '';
        $amrit = $res['amrit_kalam'] ?? $adv['amrit_kalam'] ?? '';
        $godhuli = $res['godhuli_muhurta'] ?? $adv['godhuli_muhurta'] ?? '';
        $vijay = $res['vijay_muhurta'] ?? $adv['vijay_muhurta'] ?? '';
        $nishita = $res['nishita_muhurta'] ?? $adv['nishita_muhurta'] ?? '';
        $rahukaal_val = $res['rahukaal'] ?? $adv['rahukaal'] ?? '';
        $gulika_val = $res['gulika'] ?? $adv['gulika'] ?? '';
        $yamaganda_val = $res['yamakanta'] ?? $res['yamaganda'] ?? $adv['yamaganda'] ?? $adv['yamakanta'] ?? '';
        $durs = $res['durmuhurta'] ?? $res['dur_muhurtam'] ?? $adv['durmuhurta'] ?? $adv['dur_muhurtam'] ?? [];
        $hasAuspicious = !empty($brahma) || !empty($abhijit) || !empty($amrit) || !empty($godhuli) || !empty($vijay) || !empty($nishita);
        $hasInauspicious = !empty($rahukaal_val) || !empty($gulika_val) || !empty($yamaganda_val) || !empty($durs);
        @endphp

        @if($hasAuspicious || $hasInauspicious)
        <div class="row mb-4">
            {{-- Auspicious --}}
            <div class="col-md-6 mb-4">
                <div class="panchang-card">
                    <div class="panchang-card-header" style="background: linear-gradient(135deg, #2E7D32, #1B5E20);">
                        <i class="fas fa-check-circle"></i>
                        <h3>Auspicious Timings</h3>
                    </div>
                    <div class="panchang-card-body">
                        @if(!empty($brahma))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-star" style="color:#2E7D32;"></i> Brahma Muhurta</div>
                            <div class="panchang-row-value" style="color:#2E7D32; font-weight:600;">{{ pvTime($brahma) }}</div>
                        </div>
                        @endif
                        @if(!empty($abhijit))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-star" style="color:#2E7D32;"></i> Abhijit Muhurta</div>
                            <div class="panchang-row-value" style="color:#2E7D32; font-weight:600;">{{ pvTime($abhijit) }}</div>
                        </div>
                        @endif
                        @if(!empty($amrit))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-heart" style="color:#1B5E20;"></i> Amrit Kalam</div>
                            <div class="panchang-row-value" style="color:#1B5E20; font-weight:600;">{{ pvTime($amrit) }}</div>
                        </div>
                        @endif
                        @if(!empty($godhuli))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-sun" style="color:#FF8F00;"></i> Godhuli Muhurta</div>
                            <div class="panchang-row-value" style="color:#FF8F00; font-weight:600;">{{ pvTime($godhuli) }}</div>
                        </div>
                        @endif
                        @if(!empty($vijay))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-trophy" style="color:#2E7D32;"></i> Vijay Muhurta</div>
                            <div class="panchang-row-value" style="color:#2E7D32; font-weight:600;">{{ pvTime($vijay) }}</div>
                        </div>
                        @endif
                        @if(!empty($nishita))
                        <div class="panchang-row">
                            <div class="panchang-row-label"><i class="fas fa-moon" style="color:#283593;"></i> Nishita Muhurta</div>
                            <div class="panchang-row-value" style="color:#283593; font-weight:600;">{{ pvTime($nishita) }}</div>
                        </div>
                        @endif
                        @if(!$hasAuspicious)
                        <div class="text-center p-4 text-muted"><em>No auspicious timings data available.</em></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Inauspicious --}}
            <div class="col-md-6 mb-4">
                <div class="panchang-card">
                    <div class="panchang-card-header" style="background: linear-gradient(135deg, #8B1A1A, #C0392B);">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Inauspicious Periods</h3>
                    </div>
                    <div class="panchang-card-body" style="padding: 16px 20px;">
                        @if(!empty($rahukaal_val))
                        <div class="inauspicious-badge">
                            <div class="i-name">⚠ Rahu Kaal</div>
                            <div class="i-time">{{ pvTime($rahukaal_val) }}</div>
                        </div>
                        @endif
                        @if(!empty($gulika_val))
                        <div class="inauspicious-badge">
                            <div class="i-name">⚠ Gulika Kaal</div>
                            <div class="i-time">{{ pvTime($gulika_val) }}</div>
                        </div>
                        @endif
                        @if(!empty($yamaganda_val))
                        <div class="inauspicious-badge">
                            <div class="i-name">⚠ Yamaganda / Yamakanta</div>
                            <div class="i-time">{{ pvTime($yamaganda_val) }}</div>
                        </div>
                        @endif
                        @if(!empty($durs) && is_array($durs))
                        @foreach($durs as $dur)
                        <div class="inauspicious-badge">
                            <div class="i-name">⚠ Dur Muhurtam</div>
                            <div class="i-time">{{ pvTime($dur) }}</div>
                        </div>
                        @endforeach
                        @endif
                        @if(!$hasInauspicious)
                        <div class="text-center p-4 text-muted"><em>No inauspicious periods data available.</em></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ════════ Festivals & Vrats ════════ --}}
        @if(!empty($adv['festivals']) && is_array($adv['festivals']) && count($adv['festivals']) > 0)
        <div class="mb-4">
            <div class="panchang-card">
                <div class="panchang-card-header" style="background: linear-gradient(135deg, #C9963C, #A0720A);">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Festivals & Vrats Today</h3>
                </div>
                <div class="panchang-card-body" style="padding: 20px;">
                    @foreach($adv['festivals'] as $festival)
                    <span class="festival-pill"><span class="star">✦</span> {{ is_array($festival) ? ($festival['name'] ?? '') : $festival }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ════════ Choghadiya (from advanced_details) ════════ --}}
        @php
        $chog_day = $adv['choghadiya_day'] ?? $adv['choghadiya'] ?? [];
        $chog_night = $adv['choghadiya_night'] ?? [];
        @endphp
        @if(!empty($chog_day) || !empty($chog_night))
        <div class="mb-4">
            <div class="panchang-card">
                <div class="panchang-card-header" style="background: linear-gradient(135deg, #2A1A08, #5C3A1A);">
                    <i class="fas fa-clock"></i>
                    <h3>Choghadiya (Day & Night)</h3>
                </div>
                <div class="panchang-card-body">
                    @if(!empty($chog_day) && is_array($chog_day))
                    <div style="padding: 14px 20px 6px;">
                        <p style="font-size:12px; letter-spacing:2px; color:#EE4E5E; text-transform:uppercase; margin:0 0 10px; font-weight:700;">☀ Day Choghadiya</p>
                        <div class="chog-grid" style="padding:0;">
                            @foreach($chog_day as $chog)
                            @php $goodTypes = ['Shubh','Amrit','Labh','Char']; $isGood = in_array($chog['type'] ?? '', $goodTypes); @endphp
                            <div class="chog-item {{ $isGood ? 'good-type' : '' }}">
                                <div class="time-label" style="{{ $isGood ? 'color:#2E7D32;' : '' }}">{{ pv($chog['type'] ?? null) ?? '' }}</div>
                                <div class="time-name">{{ pv($chog['name'] ?? $chog['start'] ?? null) ?? '' }}</div>
                                @if(!empty($chog['start']) && !empty($chog['end']))
                                <div class="time-range">{{ $chog['start'] }} – {{ $chog['end'] }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(!empty($chog_night) && is_array($chog_night))
                    <div style="padding: 14px 20px 16px;">
                        <p style="font-size:12px; letter-spacing:2px; color:#888; text-transform:uppercase; margin:0 0 10px; font-weight:700;">🌙 Night Choghadiya</p>
                        <div class="chog-grid" style="padding:0;">
                            @foreach($chog_night as $chog)
                            <div class="chog-item" style="border-color:#666; background:rgba(0,0,0,0.02);">
                                <div class="time-label" style="color:#666;">{{ pv($chog['type'] ?? null) ?? '' }}</div>
                                <div class="time-name">{{ pv($chog['name'] ?? $chog['start'] ?? null) ?? '' }}</div>
                                @if(!empty($chog['start']) && !empty($chog['end']))
                                <div class="time-range">{{ $chog['start'] }} – {{ $chog['end'] }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ════════ Vedic Significations ════════ --}}
        @if(!empty($res['tithi']['meaning']) || !empty($res['nakshatra']['meaning']) || !empty($res['yoga']['meaning']) || !empty($res['karana']['special']))
        <div class="text-center mb-2 mt-4"><small class="text-uppercase" style="letter-spacing: 3px; color: #EE4E5E; font-weight: 700; font-size: 11px;">Astro-Guide</small></div>
        <h2 class="text-center font-24 mb-3" style="font-weight: 700; color: #333;">Vedic Significations & <span style="color: #EE4E5E;">Daily Interpretations</span></h2>

        <div class="row mb-5">
            @if(!empty($res['tithi']['meaning']) || !empty($res['tithi']['special']))
            <div class="col-lg-6 mb-4">
                <div class="panchang-card signif-card" style="border-left-color:#EE4E5E;">
                    <div style="padding: 24px;">
                        <div class="d-flex align-items-center mb-3">
                            <span style="font-size: 32px; margin-right: 12px;">🌙</span>
                            <div>
                                <h4 style="font-size:16px; font-weight:700; margin:0; color:#D4323F;">Tithi: {{ pv($res['tithi']['name'] ?? null) }}</h4>
                                @if(!empty($res['tithi']['type']))<span class="badge" style="background:#FFF0F1; color:#EE4E5E; font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">{{ pv($res['tithi']['type']) }} Paksha</span>@endif
                            </div>
                        </div>
                        @if(!empty($res['tithi']['meaning']))<p class="meaning-text">"{{ $res['tithi']['meaning'] }}"</p>@endif
                        @if(!empty($res['tithi']['special']))<div class="special-box">
                            <p><i class="fas fa-info-circle mr-1" style="color:#EE4E5E;"></i><strong>Auspicious Guidelines:</strong> {{ $res['tithi']['special'] }}</p>
                        </div>@endif
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($res['nakshatra']['meaning']) || !empty($res['nakshatra']['special']))
            <div class="col-lg-6 mb-4">
                <div class="panchang-card signif-card" style="border-left-color:#C9963C;">
                    <div style="padding: 24px;">
                        <div class="d-flex align-items-center mb-3">
                            <span style="font-size: 32px; margin-right: 12px;">⭐</span>
                            <div>
                                <h4 style="font-size:16px; font-weight:700; margin:0; color:#A0720A;">Nakshatra: {{ pv($res['nakshatra']['name'] ?? null) }}</h4>
                                <span class="badge" style="color:#A0720A; border:1px solid rgba(201,150,60,0.3); font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">Pada: {{ pv($res['nakshatra']['pada'] ?? null) ?? '—' }} (Lord: {{ pv($res['nakshatra']['lord'] ?? null) ?? '—' }})</span>
                            </div>
                        </div>
                        @if(!empty($res['nakshatra']['meaning']))<p class="meaning-text">"{{ $res['nakshatra']['meaning'] }}"</p>@endif
                        @if(!empty($res['nakshatra']['special']))<div class="special-box" style="border-color:#C9963C;">
                            <p><i class="fas fa-info-circle mr-1" style="color:#C9963C;"></i><strong>Auspicious Guidelines:</strong> {{ $res['nakshatra']['special'] }}</p>
                        </div>@endif
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($res['yoga']['meaning']) || !empty($res['yoga']['special']))
            <div class="col-lg-6 mb-4">
                <div class="panchang-card signif-card" style="border-left-color:#1A6896;">
                    <div style="padding: 24px;">
                        <div class="d-flex align-items-center mb-3">
                            <span style="font-size: 32px; margin-right: 12px;">🔱</span>
                            <div>
                                <h4 style="font-size:16px; font-weight:700; margin:0; color:#1A6896;">Yoga: {{ pv($res['yoga']['name'] ?? null) }}</h4>
                                @if(!empty($res['yoga']['number']))<span class="badge" style="background:rgba(214,238,247,0.4); color:#1A6896; font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">No. {{ pv($res['yoga']['number']) }}</span>@endif
                            </div>
                        </div>
                        @if(!empty($res['yoga']['meaning']))<p class="meaning-text">"{{ $res['yoga']['meaning'] }}"</p>@endif
                        @if(!empty($res['yoga']['special']))<div class="special-box" style="border-color:#1A6896;">
                            <p><i class="fas fa-info-circle mr-1" style="color:#1A6896;"></i><strong>Daily Influence:</strong> {{ $res['yoga']['special'] }}</p>
                        </div>@endif
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($res['karana']['meaning']) || !empty($res['karana']['special']))
            <div class="col-lg-6 mb-4">
                <div class="panchang-card signif-card" style="border-left-color:#2E7D32;">
                    <div style="padding: 24px;">
                        <div class="d-flex align-items-center mb-3">
                            <span style="font-size: 32px; margin-right: 12px;">🪷</span>
                            <div>
                                <h4 style="font-size:16px; font-weight:700; margin:0; color:#2E7D32;">Karana: {{ pv($res['karana']['name'] ?? null) }}</h4>
                                <span class="badge" style="background:rgba(46,94,58,0.06); color:#2E7D32; font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">{{ pv($res['karana']['type'] ?? null) }} (Lord: {{ pv($res['karana']['lord'] ?? null) ?? '—' }})</span>
                            </div>
                        </div>
                        @if(!empty($res['karana']['meaning']))<p class="meaning-text">"{{ $res['karana']['meaning'] }}"</p>@endif
                        @if(!empty($res['karana']['special']))<div class="special-box" style="border-color:#2E7D32;">
                            <p><i class="fas fa-info-circle mr-1" style="color:#2E7D32;"></i><strong>Influence & Action:</strong> {{ $res['karana']['special'] }}</p>
                        </div>@endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ════════ Planetary Positions ════════ --}}
        @if(!empty($res['sun_position']) || !empty($res['moon_position']) || !empty($res['planets']))
        <div class="row mb-4">
            <div class="col-12">
                <div class="panchang-card">
                    <div class="panchang-card-header" style="background: linear-gradient(135deg, #1A6896, #0D3D5C);">
                        <i class="fas fa-globe"></i>
                        <h3>Planetary Positions</h3>
                    </div>
                    <div class="panchang-card-body" style="padding: 20px;">
                        {{-- Major Signs Row --}}
                        <div class="row mb-3">
                            <div class="col-md-3 col-6 border-right">
                                <div class="text-center p-2">
                                    <div style="font-size:24px;">☀️</div>
                                    <div class="small text-uppercase mt-2" style="color:#888;">Sun Sign</div>
                                    <div class="font-weight-bold" style="color:#EE4E5E;">{{ pv($res['sun_position']['zodiac'] ?? $res['sun_sign']['name'] ?? $res['sun_sign'] ?? null) ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 border-right">
                                <div class="text-center p-2">
                                    <div style="font-size:24px;">🌙</div>
                                    <div class="small text-uppercase mt-2" style="color:#888;">Moon Sign</div>
                                    <div class="font-weight-bold" style="color:#EE4E5E;">{{ pv($res['rasi']['name'] ?? $res['moon_sign']['name'] ?? $res['moon_sign'] ?? null) ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 border-right">
                                <div class="text-center p-2">
                                    <div style="font-size:24px;">📅</div>
                                    <div class="small text-uppercase mt-2" style="color:#888;">Lunar Month</div>
                                    <div class="font-weight-bold" style="color:#EE4E5E;">{{ pv($masa['amanta_name'] ?? $adv['lunar_month'] ?? null) ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-2">
                                    <div style="font-size:24px;">📆</div>
                                    <div class="small text-uppercase mt-2" style="color:#888;">Lunar Day</div>
                                    <div class="font-weight-bold" style="color:#EE4E5E;">{{ pv($res['tithi']['name'] ?? $adv['lunar_day'] ?? null) ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Planet Degrees --}}
                        <hr>
                        <div class="row mt-3">
                            @if(!empty($res['sun_position']))
                            <div class="col-md-3 col-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:22px; width:28px; text-align:center;">☀️</span>
                                    <div>
                                        <div class="small text-uppercase" style="font-size:9px; color:#888;">Sun</div>
                                        <div style="font-size:13px; font-weight:600;">{{ pv($res['sun_position']['zodiac'] ?? null) }} ({{ round($res['sun_position']['sun_degree_at_rise'] ?? 0, 1) }}°)</div>
                                        @if(!empty($res['sun_position']['nakshatra']))<div class="small" style="font-size:10px; color:#888;">Nakshatra: {{ pv($res['sun_position']['nakshatra']) }}</div>@endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($res['moon_position']))
                            <div class="col-md-3 col-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:22px; width:28px; text-align:center;">🌙</span>
                                    <div>
                                        <div class="small text-uppercase" style="font-size:9px; color:#888;">Moon</div>
                                        <div style="font-size:13px; font-weight:600;">{{ pv($res['rasi']['name'] ?? null) }} ({{ round(($res['moon_position']['moon_degree'] ?? 0) % 30, 1) }}°)</div>
                                        <div class="small" style="font-size:10px; color:#888;">Total: {{ round($res['moon_position']['moon_degree'] ?? 0, 1) }}°</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @php $planets = ['mercury'=>'☿','venus'=>'♀','mars'=>'♂','jupiter'=>'♃','saturn'=>'♄','rahu'=>'☊','ketu'=>'☋']; @endphp
                            @foreach($planets as $code => $sym)
                            @php $pData = $res['planets'][$code] ?? []; @endphp
                            @if(!empty($pData))
                            <div class="col-md-3 col-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:22px; width:28px; text-align:center;">{{ $sym }}</span>
                                    <div>
                                        <div class="small text-uppercase" style="font-size:9px; color:#888;">{{ ucfirst($code) }}</div>
                                        <div style="font-size:13px; font-weight:600;">{{ pv($pData['sign'] ?? null) }} ({{ round($pData['degree'] ?? 0, 1) }}°)</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ════════ About Panchang ════════ --}}
        <div class="row mt-2">
            <div class="col-12">
                <div class="panchang-info-section">
                    <h2 class="font-24 mb-3" style="color: #333; font-weight: 700;">What is Panchang? <span style="color: #EE4E5E;">(पंचांग)</span></h2>
                    <p>Panchang is the Hindu calendar followed by Vedic astrology, which provides complete detail on each day's Tithis and auspicious and inauspicious timings. Today's Panchang on {{ getAppName() }} is based on Vijay Vishwa Panchang, which is the rarest of Panchang, used by {{ ucfirst($professionTitle) }}s for hundreds of years. Through Daily Panchang, you can get all the information about the time, date, and day to determine the Muhurat for everything.</p>
                    <p class="mt-2">The five elements of Panchang are <strong>Tithi</strong> (lunar day), <strong>Nakshatra</strong> (lunar mansion), <strong>Yoga</strong> (a luni-solar day), <strong>Karana</strong> (half of a Tithi), and <strong>Vaara</strong> (weekday). Together these five elements help determine the most auspicious timings for rituals, ceremonies, and new beginnings.</p>
                </div>
            </div>
        </div>

        @else
        <div class="panchang-empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No Panchang Data Available</h3>
            <p style="color:#999; font-size:14px;">Please try a different date or check back later.</p>
            <a href="{{ route('front.getPanchang') }}" class="date-nav-btn active mt-3" style="display: inline-flex;"><i class="fas fa-redo"></i> View Today's Panchang</a>
        </div>
        @endif
    </div>
</div>


@endsection

@section('scripts')
@if(!empty($googleMapApiKey))
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapApiKey }}&libraries=places"></script>
<script>
    $(document).ready(function() {
        var input = document.getElementById('panchang_address');
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                document.getElementById('btn-save-location').disabled = true;
                return;
            }

            var lat = place.geometry.location.lat();
            var lon = place.geometry.location.lng();
            document.getElementById('panchang_lat').value = lat;
            document.getElementById('panchang_lon').value = lon;

            // Parse address components
            var city = '';
            var region = '';
            var country = '';

            if (place.address_components) {
                for (var i = 0; i < place.address_components.length; i++) {
                    var component = place.address_components[i];
                    var types = component.types;

                    if (types.includes('locality')) {
                        city = component.long_name;
                    } else if (types.includes('administrative_area_level_1')) {
                        region = component.long_name;
                    } else if (types.includes('country')) {
                        country = component.long_name;
                    }
                }
                // Fallback for city if locality is not present
                if (!city) {
                    for (var i = 0; i < place.address_components.length; i++) {
                        var component = place.address_components[i];
                        if (component.types.includes('sublocality') || component.types.includes('postal_town') || component.types.includes('administrative_area_level_2')) {
                            city = component.long_name;
                            break;
                        }
                    }
                }
            }

            document.getElementById('panchang_city').value = city || place.name || '';
            document.getElementById('panchang_region').value = region;
            document.getElementById('panchang_country').value = country;

            // Set static timezone offset to 5.5
            document.getElementById('panchang_timezone').value = "5.5";
            document.getElementById('btn-save-location').disabled = false;
        });

        // Save Button Handler
        $('#btn-save-location').click(function() {
            var lat = document.getElementById('panchang_lat').value;
            var lon = document.getElementById('panchang_lon').value;
            var city = document.getElementById('panchang_city').value;
            var region = document.getElementById('panchang_region').value;
            var country = document.getElementById('panchang_country').value;
            var timezone = document.getElementById('panchang_timezone').value;

            // Build redirect URL
            var url = new URL(window.location.href);
            url.searchParams.set('lat', lat);
            url.searchParams.set('lon', lon);
            url.searchParams.set('city', city);
            url.searchParams.set('region', region);
            url.searchParams.set('country', country);
            url.searchParams.set('timezone', timezone);

            window.location.href = url.toString();
        });

        // Device Geolocation Handler
        $('#btn-detect-location').click(function() {
            var btn = $(this);
            var originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Detecting...');
            btn.prop('disabled', true);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;
                    var timezone = "5.5";

                    // Attempt reverse geocoding via Google Maps API
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ location: { lat: lat, lng: lon } }, function(results, status) {
                        var city = '';
                        var region = '';
                        var country = '';

                        if (status === 'OK' && results[0]) {
                            var addressComponents = results[0].address_components;
                            for (var i = 0; i < addressComponents.length; i++) {
                                var component = addressComponents[i];
                                var types = component.types;

                                if (types.includes('locality')) {
                                    city = component.long_name;
                                } else if (types.includes('administrative_area_level_1')) {
                                    region = component.long_name;
                                } else if (types.includes('country')) {
                                    country = component.long_name;
                                }
                            }
                            if (!city) {
                                for (var i = 0; i < addressComponents.length; i++) {
                                    var component = addressComponents[i];
                                    if (component.types.includes('sublocality') || component.types.includes('postal_town') || component.types.includes('administrative_area_level_2')) {
                                        city = component.long_name;
                                        break;
                                    }
                                }
                            }
                        }

                        // Build redirect URL
                        var url = new URL(window.location.href);
                        url.searchParams.set('lat', lat);
                        url.searchParams.set('lon', lon);
                        url.searchParams.set('city', city || 'Detected Location');
                        url.searchParams.set('region', region);
                        url.searchParams.set('country', country);
                        url.searchParams.set('timezone', timezone);

                        window.location.href = url.toString();
                    });
                }, function(error) {
                    console.error("Geolocation error:", error);
                    alert("Unable to retrieve your location. Please check your browser permissions or search manually.");
                    btn.html(originalHtml);
                    btn.prop('disabled', false);
                });
            } else {
                alert("Geolocation is not supported by your browser.");
                btn.html(originalHtml);
                btn.prop('disabled', false);
            }
        });
    });
</script>
@endif
@endsection
