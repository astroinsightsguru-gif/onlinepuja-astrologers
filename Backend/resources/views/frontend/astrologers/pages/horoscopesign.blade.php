@extends('frontend.astrologers.layout.master')
@section('content')
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
                                style="color:white;text-decoration:none">Horoscope </a>
                            <i class="fa fa-chevron-right"></i> Daily Horoscope
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="onlinepuja-menu pt-2 pb-md-2 bg-pink border-bottom border-pink">
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



    <div class="ds-head-populararticle bg-white cat-pages">
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
                            <div class="shadow-pink-down text-center p-3 hover-border-red rounded-10">
                                <a href="{{route('front.astrologers.dailyHoroscope',['slug' => $horoscopesign['slug']])}}"
                                    title="{{$horoscopesign['name']}}" class="text-decoration-none text-dark">
                                    <div>
                                        <img style="height: 110px;width:110px" src="{{$horoscopesign['image']}}"
                                            alt="{{$horoscopesign['name']}}">

                                    </div>
                                    <div class="">
                                        <p class="font-weight-bold mb-0 mt-2 color-red">{{$horoscopesign['name']}}</p>
                                        {{-- <p class="mb-0">Mar 21 - Apr 20</p> --}}
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
    </div>
@endsection
