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

<div class="onlinepuja-menu pt-2 pb-md-2">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-8 col-md-6 d-flex align-items-center text-left">
                        <h1 class="">
                            <span class="dd-block cat-heading font-weight-semi-bold font-24 mb-0">Free Horoscope</span>
                        </h1>

                    </div>
                    <div class="col-4 col-md-6 text-right compatibility py-2 py-md-0">


                        <svg xmlns="http://www.w3.org/2000/svg" width="69.828" height="69.828"
                            viewBox="0 0 69.828 69.828">
                            <g data-name="calendar (3)">
                                <path data-name="Path 81075"
                                    d="M60.518 10.474a4.655 4.655 0 0 0-4.655-4.655H54.7V9.31a2.328 2.328 0 0 1-2.328 2.328h-2.329a2.328 2.328 0 0 0 2.328-2.328V2.328a2.328 2.328 0 1 0-4.655 0v3.491H41.9V9.31a2.328 2.328 0 0 1-2.328 2.328h-2.33A2.328 2.328 0 0 0 39.57 9.31V2.328a2.328 2.328 0 1 0-4.655 0v3.491h-6.984V9.31a2.328 2.328 0 0 1-2.331 2.328h-2.324A2.328 2.328 0 0 0 25.6 9.31V2.328a2.328 2.328 0 0 0-4.655 0v3.491h-5.816V9.31a2.328 2.328 0 0 1-2.329 2.328h-2.326A2.328 2.328 0 0 0 12.8 9.31V2.328a2.328 2.328 0 1 0-4.655 0v3.491h-3.49A4.655 4.655 0 0 0 0 10.474v6.983h60.518Z"
                                    fill="#ee4e5e"></path>
                                <path data-name="Path 81076"
                                    d="M57.024 44.224a12.8 12.8 0 1 0 12.8 12.8 12.8 12.8 0 0 0-12.8-12.8Zm0 15.129a2.312 2.312 0 0 1-.842-.163l-4.249 3.4-1.455-1.818 4.253-3.4a2.223 2.223 0 0 1 1.131-2.348v-7.309h2.328v7.309a2.321 2.321 0 0 1-1.166 4.329Z"
                                    fill="#ee4e5e"></path>
                                <path data-name="Path 81077"
                                    d="M4.655 62.846H43.06a15.131 15.131 0 0 1 8.146-19.785h-5.818v-6.983h6.983v6.557a14.9 14.9 0 0 1 8.147-.316V19.785H0v38.406a4.654 4.654 0 0 0 4.655 4.655Zm40.733-39.57h6.983v6.983h-6.983Zm-12.8 0h6.983v6.983h-6.985Zm0 12.8h6.983v6.983h-6.985Zm0 12.8h6.983v6.983h-6.985Zm-12.8-25.6h6.983v6.983h-6.986Zm0 12.8h6.983v6.983h-6.986Zm0 12.8h6.983v6.983h-6.986Zm-12.8-25.6h6.983v6.983H6.983Zm0 12.8h6.983v6.983H6.983Zm0 12.8h6.983v6.983H6.983Z">
                                </path>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="ds-head-populararticle cat-pages">
    <div class="container">
        <div class="row py-3">
            <div class="col-12 col-md-12 mt-4 cat-pages-hide">
                <h2 class="cat-heading mb-4">Today’s <span class="color-red">Horoscope</span></h2>
                <p class="text-center">Confused about how your day would turn out to be? Find out if today is the day to
                    make big decisions. Read your Daily Horoscope forecast and get insights regarding different aspects
                    of your life to plan your day better.</p>

                <div class="row pt-4">
                    @foreach ($gethoroscopesign['recordList'] as $horoscopesign)
                    <div class="col-4 col-md-4 col-lg-3 col-xl-2 mb-4">
                        <div class="horo-sign-card text-center">
                            <a href="{{route('front.dailyHoroscope',['slug' => $horoscopesign['slug']])}}"
                                title="{{$horoscopesign['name']}}" class="text-decoration-none text-dark">
                                <div>
                                    <img style="height: 110px;width:110px" src="{{ Str::startsWith($horoscopesign['image'], ['http://','https://']) ? $horoscopesign['image'] : '/' . $horoscopesign['image'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $horoscopesign['image'] }}')" />
                                </div>
                                <div class="">
                                    <h3 class="horo-sign-name">{{$horoscopesign['name']}}</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach

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
                    class="looks-1 d-flex p-2 py-3 p-sm-3 overflow-hidden position-relative flex-xlwrap flex-sm-wrap flex-md-nowrap">
                    <div class="text-center d-flex font-weight-medium align-items-center w-100 px-sm-4 pr-2 pr-sm-5">
                        <div class="px-2 w-100 px-lg-5 mx-lg-5">
                            <span class="d-block font-30 heading-line">WILL YOU BE <span class="color-red">RICH</span>
                                AND <span class="color-red">SUCCESSFUL</span> IN FUTURE?</span>
                            <span class="d-none d-md-block font-22 mt-3 pt-1">Know what’s written in your stars!</span>
                            <a href="{{route('front.chatList')}}"
                                class="btn btn-chat px-3 px-sm-4 font-20 font-weight-semi-bold font-small-ms mt-3">Ask
                                An {{ucfirst($professionTitle)}} Now</a>
                        </div>
                    </div>
                    <div
                        class="looks-image ilook2 text-center position-relative align-items-center mr-md-3 mr-lg-4  w-100 justify-content-center">
                        <div class="looks-img-box position-relative">
                            <img
                                src="{{asset('public/frontend/onlinepujacdn/onlinepuja/web/content/images/ads/success-future.png')}}">
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
