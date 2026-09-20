@extends('frontend.astrologers.layout.master')
@section('content')
@php
$todayActive = !request()->has('panchangDate');
$tomorrowActive = request('panchangDate') === date('Y-m-d', strtotime('+1 day'));
@endphp
    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{route('front.astrologerindex')}}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="#"
                                style="color:white;text-decoration:none">Panchang </a>
                            <i class="fa fa-chevron-right"></i> Today's Panchang
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <div class="ds-head-populararticle bg-white cat-pages">
        <div class="container">

            <div class="row py-3 mt-4">
                <div class="col-12">
                    <h2 class="cat-heading font-24"> @if($tomorrowActive)
                        Tomorrow’s Panchang <span class="color-red">(Kal Ka Panchang)</span>
                    @else
                        Today’s Panchang <span class="color-red">(Aaj Ka Panchang)</span>
                    @endif</h2>
                    <p class="pt-3 text-center">Panchang is the Hindu calendar followed by Vedic astrology, which provides
                        complete detail on each day's Tithis and auspicious and inauspicious timings. Today’s Panchang on
                        {{getAppName()}} is based on Vijay Vishwa Panchang, which is the rarest of Panchang, used by
                        {{ucfirst($professionTitle)}}s for hundreds of years. Through Daily Panchang, you can get all the information about the
                        time, date, and day to determine the Muhurat for everything. {{ucfirst($professionTitle)}}s suggest people should
                        follow the Day Panchang while doing new work or performing any auspicious event.</p>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 text-center mt-3 mb-0">
                            <div id="cardholder" class="rounded-lg">
                                <div style="position: initial !important; width: 100% !important;">
                                    <div class="pt-0 mb-1">
                                        <a class="card-link btn m-1 bg-white text-dark border-red font-14 font-weight-semi-bold titlecase rounded-25 mt-2"
                                            data-toggle="collapse" data-parent="#cardholder"
                                            href="#card-element-1">Calendar&nbsp;<i class="fa fa-caret-down"
                                                aria-hidden="true"></i><i class="fa fa-caret-up" aria-hidden="true"
                                                style="display:none"></i></a>
                                              
                                       <a class="btn m-1 {{ $todayActive ? 'bg-red text-white' : 'bg-white text-dark' }} border-red font-14 font-weight-semi-bold titlecase rounded-25 mt-2" href="{{ route('front.astrologers.getPanchang')}}">TODAY&nbsp;
                                        </a>
                                            <a class="btn m-1 {{ $tomorrowActive ? 'bg-red text-white' : 'bg-white text-dark' }} border-red font-14 font-weight-semi-bold titlecase rounded-25 mt-2" href="{{ route('front.astrologers.getPanchang', ['panchangDate' => date('Y-m-d', strtotime('+1 day'))]) }}">TOMORROW&nbsp;
                                          </a>
                                    </div>
                                    <div id="card-element-1" class="collapse row">
                                        <div class="col-12 pt-2">
                                            <div class="px-3 pb-1 rounded shadow-pink">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center pt-2 pb-3">

                                                            <a href="{{ route('front.astrologers.getPanchang')}}" class="monthbox high">{{date('M')}}</a>

                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                                        @php
                                                        $year = date('Y');
                                                        $month = date('m');
                                                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                                    @endphp
                                                    @for ($day = 1; $day <= $daysInMonth; $day++)
                                                            @php
                                                                $date = sprintf("%02d", $day);
                                                                $year = date('Y');
                                                                $month = date('m');
                                                                $fullDate = "$year-$month-$date";
                                                            @endphp
                                                            <a href="{{ route('front.astrologers.getPanchang', ['panchangDate' => $fullDate]) }}" class="daybox p-1 m-1{{ request('panchangDate', date('Y-m-d')) == $fullDate ? ' high' : '' }}">{{ $day }}</a>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    @if(!empty($getPanchang['response']['tithi']))

                    <div class="row mt-2 pt-1">
                        <div class="col-12 Choghadiya_section my-3">
                            <div class="shadow-pink rounded-10 p-3">
                                <div class="row">
                                    <div class="col-md-6 col-12 CGH_Ssection0">
                                        <div class="shadow-pink rounded h-100">
                                            <div class="bg-pink p-1 rounded-top text-center">
                                                <p class="font-14 color-red font-weight-bold m-0">Panchang</p>
                                            </div>
                                            <div class="Choghadiya_img_day px-3 pt-3 font-14">

                                                <div class="row border-bottom">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Tithi</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['tithi']['name']}}</p>
                                                    </div>
                                                </div>

                                                <div class="row border-bottom">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Nakshatra</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['nakshatra']['name']}}</p>
                                                    </div>
                                                </div>

                                                <div class="row border-bottom">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Yoga</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['yoga']['name']}}</p>
                                                    </div>
                                                </div>

                                                <div class="row border-bottom">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Karana</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['karana']['name']}}</p>
                                                    </div>
                                                </div>

                                                <div class="row border-bottom">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Rasi</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['rasi']['name']}}</p>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="shadow-pink rounded h-100">
                                            <div class="bg-pink p-1 rounded-top text-center">
                                                <p class="font-14 color-red font-weight-bold m-0">Additional Info</p>
                                            </div>
                                            <div class="Choghadiya_img_day px-3 pt-3 font-14">
                                                <div class="row border-bottom">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Sunrise</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['sun_rise']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Sunset</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['sun_set']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Moonrise</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['moon_rise']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Moonset</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['moon_set']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Next Full Moon</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['next_full_moon']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Next New Moon</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['next_new_moon']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Amanta Month</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['masa']['amanta_name']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Paksha</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['masa']['paksha']}}</p>
                                                    </div>
                                                </div>
                                                <div class="row border-bottom pt-2">
                                                    <div class="col-6">
                                                        <p class="font-weight-semi-bold mb-2">Purnimanta</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-2">{{$getPanchang['response']['advanced_details']['masa']['purnimanta_name']}}</p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    @else

                    <h3 class="mt-5 mb-5 text-center">Oops! No Panchang Found</h3>

                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
