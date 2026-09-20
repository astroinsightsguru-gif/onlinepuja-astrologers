@extends('frontend.layout.master')
@push('styles')
<link rel="stylesheet" href="{{ asset('public/frontend/css/horoscope-custom.css?v=1.0.0') }}">
@endpush
@section('content')
<div class="pt-1 pb-1 d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize; ">
                    <span class="text-white breadcrumbs">
                        <a href="/" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <a href="#"
                            style="color:white;text-decoration:none">Horoscope </a>
                        <i class="fa fa-chevron-right"></i> Daily Horoscope
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>




@php
$currentZodiac = $horoscope['vedicList']['todayHoroscope'][0]['zodiac'] ?? $horoscope['vedicList']['weeklyHoroScope'][0]['zodiac'] ?? $horoscope['vedicList']['yearlyHoroScope'][0]['zodiac'] ?? '';
@endphp
<div class="onlinepuja-menu pt-3">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ul class="list-unstyled d-flex mb-3 mt-20" style="overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach ($gethoroscopesign['recordList'] as $horoscopesign)
                    @php
                    $isActive = (strtolower($horoscopesign['name']) == strtolower($currentZodiac));
                    @endphp
                    <li class="px-2 zodiac-carousel-item {{ $isActive ? 'active' : '' }}" style="flex: 0 0 auto;">
                        <a href="{{ route('front.dailyHoroscope', ['slug' => $horoscopesign['slug']]) }}"
                            title="{{ $horoscopesign['name'] }} Daily Horoscope" class="text-decoration-none">
                            <div class="text-center mb-2 mb-md-0">
                                <div class="zodiac-icon-container">
                                    <img class="cursor-pointer" src="{{ Str::startsWith($horoscopesign['image'], ['http://','https://']) ? $horoscopesign['image'] : '/' . $horoscopesign['image'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $horoscopesign['name'] }}" />
                                </div>
                                <span class="d-block icon-desc pt-2" style="font-size: 12px;">{{ $horoscopesign['name'] }}</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>

            </div>
        </div>
    </div>
</div>

<div class="ds-head-populararticle cat-pages">
    <div class="container">
        <div class="row py-3">
            <div class="col-12 col-md-12 mt-4">


                <div class="row pt-2">
                    <div class="col-12 text-center">
                        <div id="cardholder" class="rounded-lg">
                            <div class="w-100">
                                <div class="pt-0 mb-1">
                                    <a class="card-link  btn m-1 bg-white color-red border-red font-14 font-weight-semi-bold titlecase rounded-25 px-md-4 hover-border-red"
                                        data-toggle="tab" id="weeklypanel" href="#weeklyData">Weekly</a>
                                    <a class="btn m-1 bg-red text-white border-red font-14 font-weight-semi-bold titlecase rounded-25 px-md-4 hover-border-red"
                                        data-toggle="tab" href="#dailyData" id="dailypanel">Daily
                                        <span class="d-none d-md-inline-block"></span></a>
                                    <a class="btn m-1 bg-white color-red border-red font-14 font-weight-semi-bold titlecase rounded-25 px-md-4 hover-border-red"
                                        data-toggle="tab" href="#yearlyData " id="yearlypanel">Yearly
                                        <span class="d-none d-md-inline-block">{{date('Y')}}</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- For Daily --}}
                    @php
                    $getPercent = function($val) {
                    if (empty($val)) return 0;
                    $val = floatval($val);
                    if ($val <= 5) return $val * 20;
                        return $val;
                        };
                        @endphp
                        <div class="tab-content">

                        {{-- For Daily --}}
                        <div class="col-12 pt-4 mt-3 tab-pane fade show active" id="dailyData">
                            @if (!empty($horoscope['vedicList']['todayHoroscope'][0]))
                            @php
                            $h = $horoscope['vedicList']['todayHoroscope'][0];
                            $zodiac = $h['zodiac'];
                            $date = date("d-m-Y", strtotime($h['date']));
                            @endphp
                            <h2 class="cat-heading mb-4">Free <span class="color-red">{{ $zodiac }}</span> Daily Horoscope</h2>

                            <div class="row horo-dashboard-row">
                                <div class="col-lg-4 mb-4">
                                    <div class="horoscope-profile-card">
                                        <img class="cursor-pointer zodiac-avatar-img" src="{{ Str::startsWith($signRcd[0]->image, ['http://','https://']) ? $signRcd[0]->image : '/' . $signRcd[0]->image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $zodiac }}" onclick="openImage('{{ $signRcd[0]->image }}')" />
                                        <h3 class="zodiac-name">Daily {{ $zodiac }}</h3>
                                        <p class="zodiac-date-text"><i class="far fa-calendar-alt"></i> {{ $date }}</p>

                                        <div class="lucky-pill-box">
                                            <div class="lucky-pill-item">
                                                <span class="lucky-icon">🎨</span>
                                                <div class="lucky-text">
                                                    <span class="label">Lucky Color</span>
                                                    <span class="val">{{ $h['lucky_color'] }}</span>
                                                </div>
                                            </div>
                                            <div class="lucky-pill-item">
                                                <span class="lucky-icon">🔢</span>
                                                <div class="lucky-text">
                                                    <span class="label">Lucky Number</span>
                                                    <span class="val">{{ implode(', ', (array)json_decode($h['lucky_number'], true)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="horoscope-details-main-card mb-4">
                                        <h4 class="section-title"><i class="fas fa-star-of-david"></i> Today's Forecast</h4>
                                        <p class="forecast-desc">{{ $h['bot_response'] }}</p>
                                    </div>

                                    <div class="horoscope-insights-card">
                                        <h4 class="section-title"><i class="fas fa-chart-line"></i> Daily Ratings & Energy</h4>
                                        <div class="row">
                                            @foreach(['health' => 'Health', 'career' => 'Career', 'finances' => 'Finances', 'relationship' => 'Relationship', 'travel' => 'Travel', 'family' => 'Family', 'friends' => 'Friends', 'status' => 'Status', 'physique' => 'Physique'] as $key => $label)
                                            @if(isset($h[$key]))
                                            @php $percent = $getPercent($h[$key]); @endphp
                                            <div class="col-md-6 insight-rating-item">
                                                <div class="insight-rating-header">
                                                    <span class="insight-rating-label">
                                                        @if($key=='health') <i class="fas fa-heartbeat text-danger"></i>
                                                        @elseif($key=='career') <i class="fas fa-briefcase text-primary"></i>
                                                        @elseif($key=='finances') <i class="fas fa-wallet text-success"></i>
                                                        @elseif($key=='relationship') <i class="fas fa-heart text-pink"></i>
                                                        @elseif($key=='travel') <i class="fas fa-plane text-info"></i>
                                                        @elseif($key=='family') <i class="fas fa-home text-warning"></i>
                                                        @elseif($key=='friends') <i class="fas fa-users text-secondary"></i>
                                                        @elseif($key=='status') <i class="fas fa-award text-purple"></i>
                                                        @else <i class="fas fa-child text-dark"></i>
                                                        @endif
                                                        {{ $label }}
                                                    </span>
                                                    <span class="insight-rating-val">{{ $percent }}%</span>
                                                </div>
                                                <div class="insight-progress-container">
                                                    <div class="insight-progress-bar" style="width: {{ $percent }}%"></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <p>No Daily Horoscope Found</p>
                            </div>
                            @endif
                        </div>

                        {{-- For Weekly ─── --}}
                        <div class="col-12 pt-4 mt-3 tab-pane fade" id="weeklyData">
                            @if (!empty($horoscope['vedicList']['weeklyHoroScope'][0]))
                            @php
                            $w = $horoscope['vedicList']['weeklyHoroScope'][0];
                            $zodiac = $w['zodiac'];
                            $startDate = date("d-m-Y", strtotime($w['start_date']));
                            $endDate = date("d-m-Y", strtotime($w['end_date']));
                            @endphp
                            <h2 class="cat-heading mb-4">Free <span class="color-red">{{ $zodiac }}</span> Weekly Horoscope</h2>

                            <div class="row horo-dashboard-row">
                                <div class="col-lg-4 mb-4">
                                    <div class="horoscope-profile-card">
                                        <img class="cursor-pointer zodiac-avatar-img" src="{{ Str::startsWith($signRcd[0]->image, ['http://','https://']) ? $signRcd[0]->image : '/' . $signRcd[0]->image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $zodiac }}" onclick="openImage('{{ $signRcd[0]->image }}')" />
                                        <h3 class="zodiac-name">Weekly {{ $zodiac }}</h3>
                                        <p class="zodiac-date-text"><i class="far fa-calendar-alt"></i> {{ $startDate }} to {{ $endDate }}</p>

                                        <div class="lucky-pill-box">
                                            <div class="lucky-pill-item">
                                                <span class="lucky-icon">🎨</span>
                                                <div class="lucky-text">
                                                    <span class="label">Lucky Color</span>
                                                    <span class="val">{{ $w['lucky_color'] }}</span>
                                                </div>
                                            </div>
                                            <div class="lucky-pill-item">
                                                <span class="lucky-icon">🔢</span>
                                                <div class="lucky-text">
                                                    <span class="label">Lucky Number</span>
                                                    <span class="val">{{ implode(', ', (array)json_decode($w['lucky_number'], true)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="horoscope-details-main-card mb-4">
                                        <h4 class="section-title"><i class="fas fa-star-of-david"></i> Weekly Forecast</h4>
                                        <p class="forecast-desc">{{ $w['bot_response'] }}</p>
                                    </div>

                                    <div class="horoscope-insights-card">
                                        <h4 class="section-title"><i class="fas fa-chart-line"></i> Weekly Ratings & Energy</h4>
                                        <div class="row">
                                            @foreach(['health' => 'Health', 'career' => 'Career', 'finances' => 'Finances', 'relationship' => 'Relationship', 'travel' => 'Travel', 'family' => 'Family', 'friends' => 'Friends', 'status' => 'Status', 'physique' => 'Physique'] as $key => $label)
                                            @if(isset($w[$key]))
                                            @php $percent = $getPercent($w[$key]); @endphp
                                            <div class="col-md-6 insight-rating-item">
                                                <div class="insight-rating-header">
                                                    <span class="insight-rating-label">
                                                        @if($key=='health') <i class="fas fa-heartbeat text-danger"></i>
                                                        @elseif($key=='career') <i class="fas fa-briefcase text-primary"></i>
                                                        @elseif($key=='finances') <i class="fas fa-wallet text-success"></i>
                                                        @elseif($key=='relationship') <i class="fas fa-heart text-pink"></i>
                                                        @elseif($key=='travel') <i class="fas fa-plane text-info"></i>
                                                        @elseif($key=='family') <i class="fas fa-home text-warning"></i>
                                                        @elseif($key=='friends') <i class="fas fa-users text-secondary"></i>
                                                        @elseif($key=='status') <i class="fas fa-award text-purple"></i>
                                                        @else <i class="fas fa-child text-dark"></i>
                                                        @endif
                                                        {{ $label }}
                                                    </span>
                                                    <span class="insight-rating-val">{{ $percent }}%</span>
                                                </div>
                                                <div class="insight-progress-container">
                                                    <div class="insight-progress-bar" style="width: {{ $percent }}%"></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <p>No Weekly Horoscope Found</p>
                            </div>
                            @endif
                        </div>

                        {{-- For Yearly ─── --}}
                        <div class="col-12 pt-4 mt-3 tab-pane fade" id="yearlyData">
                            @if (!empty($horoscope['vedicList']['yearlyHoroScope'][0]))
                            @php
                            $y = $horoscope['vedicList']['yearlyHoroScope'][0];
                            $zodiac = $y['zodiac'];
                            $year = date('Y');
                            @endphp
                            <h2 class="cat-heading mb-4">Free <span class="color-red">{{ $zodiac }}</span> Yearly Horoscope, {{ $year }}</h2>

                            <div class="row horo-dashboard-row">
                                <div class="col-lg-4 mb-4">
                                    <div class="horoscope-profile-card">
                                        <img class="cursor-pointer zodiac-avatar-img" src="{{ Str::startsWith($signRcd[0]->image, ['http://','https://']) ? $signRcd[0]->image : '/' . $signRcd[0]->image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $zodiac }}" onclick="openImage('{{ $signRcd[0]->image }}')" />
                                        <h3 class="zodiac-name">Yearly {{ $zodiac }}</h3>
                                        <p class="zodiac-date-text"><i class="far fa-star"></i> Overview for Year {{ $year }}</p>

                                        <div class="lucky-pill-box">
                                            <div class="lucky-pill-item">
                                                <span class="lucky-icon">🎨</span>
                                                <div class="lucky-text">
                                                    <span class="label">Lucky Color</span>
                                                    <span class="val">{{ !empty($y['lucky_color']) ? $y['lucky_color'] : 'Gold' }}</span>
                                                </div>
                                            </div>
                                            <div class="lucky-pill-item">
                                                <span class="lucky-icon">🔢</span>
                                                <div class="lucky-text">
                                                    <span class="label">Lucky Number</span>
                                                    <span class="val">{{ !empty($y['lucky_number']) ? implode(', ', (array)json_decode($y['lucky_number'], true)) : '9' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="horoscope-details-main-card mb-4">
                                        <h4 class="section-title"><i class="fas fa-star-of-david"></i> Year's General Forecast</h4>
                                        <p class="forecast-desc">{{ $y['bot_response'] }}</p>
                                    </div>

                                    <div class="horoscope-insights-card">
                                        <h4 class="section-title"><i class="fas fa-book-open"></i> Annual Life Remarks</h4>

                                        @foreach([
                                        'health_remark' => ['Health', 'fas fa-heartbeat text-danger'],
                                        'career_remark' => ['Career', 'fas fa-briefcase text-primary'],
                                        'finances_remark' => ['Finances', 'fas fa-wallet text-success'],
                                        'relationship_remark' => ['Relationship', 'fas fa-heart text-pink'],
                                        'travel_remark' => ['Travel', 'fas fa-plane text-info'],
                                        'family_remark' => ['Family', 'fas fa-home text-warning'],
                                        'friends_remark' => ['Friends', 'fas fa-users text-secondary'],
                                        'status_remark' => ['Status', 'fas fa-award text-purple']
                                        ] as $field => $meta)
                                        @if(!empty($y[$field]))
                                        <div class="remark-box-item">
                                            <h5 class="remark-box-title">
                                                <i class="{{ $meta[1] }}"></i> {{ $meta[0] }}
                                            </h5>
                                            <p class="remark-box-desc">{{ $y[$field] }}</p>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <p>No Yearly Horoscope Found</p>
                            </div>
                            @endif
                        </div>

                </div>
                {{-- End --}}
            </div>
        </div>
    </div>
</div>
</div>


<div class="container py-5">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="heading text-center">Why Should You Check Your Horoscope Daily? </h2>
            <p>If today is the right day for new beginnings? Or if this day will have opportunities or challenges in
                store?</p>
            <p>Every day is like a new page in the book of our life. While some days are for hustle, on some days all
                you need to do is take a back seat and let situations reveal their outcome. What if there is a way from
                which you can get clarity about your day ahead and know what needs to be done. The daily Horoscope of an
                individual is a prediction about what different situations in your life such as regarding career,
                health, relationship, etc. are going to be like.</p>
            <p>The position of celestial bodies like the Sun, the Moon, and planets change frequently and they often
                enter into new Houses and Zodiac signs leaving the former ones. With this movement, the life situations
                of an individual also get affected.</p>
            <p>Daily Horoscope is created by deeply analyzing the position and effect of the celestial bodies on a
                particular day and how it affects different aspects of the life of an individual.</p>
            <p>Your Daily Horoscope can help you decipher upcoming challenges and reveal opportunities coming towards
                you. You get better clarity about the roadblocks that are restricting you to get peace of mind and
                success. These predictions give you greater confidence about your day ahead and help you steer your life
                in the right direction by making the right decisions.</p>
        </div>
    </div>
    <div class="mb-3">

        <div class="row pt-3">
            <div class="col-12">
                <div
                    class="bg-pink looks-1 d-flex p-2 py-3 p-sm-3 overflow-hidden position-relative flex-xlwrap flex-sm-wrap flex-md-nowrap">
                    <div class="text-center d-flex font-weight-medium align-items-center w-100 px-sm-4 pr-2 pr-sm-5">
                        <div class="px-2 w-100 px-lg-5 mx-lg-5">
                            <span class="d-block font-30 heading-line">WILL YOU BE <span class="color-red">RICH</span>
                                AND <span class="color-red">SUCCESSFUL</span> IN FUTURE?</span>
                            <span class="d-none d-md-block font-22 mt-3 pt-1">Know what’s written in your stars!</span>
                            <a href="{{ route('front.chatList') }}"
                                class="btn btn-chat px-3 px-sm-4 font-20 font-weight-semi-bold font-small-ms mt-3">Ask
                                An {{ ucfirst($professionTitle) }} Now</a>
                        </div>
                    </div>
                    <div
                        class="looks-image ilook2 text-center position-relative align-items-center mr-md-3 mr-lg-4  w-100 justify-content-center">
                        <div class="looks-img-box position-relative">
                            <img
                                src="{{ asset('public/frontend/onlinepujacdn/onlinepuja/web/content/images/ads/success-future.png') }}">
                        </div>
                    </div>
                    <span class="looks-circle size-2 tops position-absolute"></span>
                    <span class="looks-circle size-3 filled rights position-absolute" style="margin-top:9%"></span>
                    <span class="looks-circle size-1 filled bottom-0 position-absolute d-none d-sm-block"
                        style="margin-left:11%"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $("#weeklypanel").on('click', function() {
            $('#weeklypanel').addClass("bg-red text-white");
            $('#weeklypanel').removeClass("bg-white color-red");
            $('#dailypanel').removeClass("bg-red text-white");
            $('#dailypanel').addClass("bg-white color-red");
            $('#yearlypanel').removeClass("bg-red text-white");
            $('#yearlypanel').addClass("bg-white color-red");
            $('#dailyData').removeClass("show active");
            $('#yearlyData').removeClass("show active");
            $('#weeklyData').addClass("show active");
        });

        $("#dailypanel").on('click', function() {
            $('#dailypanel').addClass("bg-red text-white");
            $('#dailypanel').removeClass("bg-white color-red");
            $('#weeklypanel').removeClass("bg-red text-white");
            $('#weeklypanel').addClass("bg-white color-red");
            $('#yearlypanel').removeClass("bg-red text-white");
            $('#yearlypanel').addClass("bg-white color-red");
            $('#weeklyData').removeClass("show active");
            $('#yearlyData').removeClass("show active");
            $('#dailyData').addClass("show active");
        });

        $("#yearlypanel").on('click', function() {
            $('#yearlypanel').addClass("bg-red text-white");
            $('#yearlypanel').removeClass("bg-white color-red");
            $('#weeklypanel').removeClass("bg-red text-white");
            $('#weeklypanel').addClass("bg-white color-red");
            $('#dailypanel').removeClass("bg-red text-white");
            $('#dailypanel').addClass("bg-white color-red");
            $('#weeklyData').removeClass("show active");
            $('#dailyData').removeClass("show active");
            $('#yearlyData').addClass("show active");
        });
    });
</script>
@endsection
