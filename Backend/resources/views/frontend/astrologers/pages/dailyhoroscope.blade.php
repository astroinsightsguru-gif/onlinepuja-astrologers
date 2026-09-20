@extends('frontend.astrologers.layout.master')
@section('content')
    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
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




    <div class="onlinepuja-menu pt-3 bg-pink">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="list-unstyled d-flex mb-3 mt-20">


                        @foreach ($gethoroscopesign['recordList'] as $horoscopesign)
                            <li class="taurus">
                                <a href="{{ route('front.astrologers.dailyHoroscope', ['slug' => $horoscopesign['slug']]) }}"
                                    title="Taurus Daily Horoscope" class="text-decoration-none ">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon border-0 bg-pink">
                                            <img src="{{ $horoscopesign['image'] }}" alt="{{ $horoscopesign['name'] }}">
                                        </div>
                                        <span class="d-block icon-desc pt-0">{{ $horoscopesign['name'] }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="ds-head-populararticle bg-white cat-pages">
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
                        <div class="tab-content">
                            
                            <div class="col-12 pt-4 mt-3 tab-pane fade show active" id="dailyData">
                                @if (!empty($horoscope['vedicList']['todayHoroscope'][0]))
                                <h2 class="cat-heading mb-4">Free <span
                                        class="color-red">{{ $horoscope['vedicList']['todayHoroscope'][0]['zodiac'] }}</span>
                                    Daily
                                    Horoscope, {{ date("d-m-Y", strtotime($horoscope['vedicList']['todayHoroscope'][0]['date'])) }}
                                </h2>
                                <div class="d-md-flex align-items-start">
                                    <img src="/{{ $signRcd[0]->image }}" style="max-height: 140px"
                                        class="mr-md-3 mx-auto d-block" alt="Aries">
                                    <div class="media-body">
                                        <div class="dailyhoroscope-content mt-3">
                                            <p>Free {{ $horoscope['vedicList']['todayHoroscope'][0]['zodiac'] }} Daily
                                                Horoscope
                                            </p>
                                            <p> {{ $horoscope['vedicList']['todayHoroscope'][0]['bot_response'] }}</p>
                                        </div>

                                        <div class="shadow-pink rounded-10">
                                            <div class="bg-pink">
                                                <h3 class="text-center font-weight-bold color-red py-2">Today’s Lucky Color
                                                    and
                                                    Number For {{ $horoscope['vedicList']['todayHoroscope'][0]['zodiac'] }}
                                                </h3>
                                            </div>
                                            <div class="px-3">
                                                <div class="row">
                                                    <div class="col-md-6 text-center">
                                                        <div class="my-3"><svg data-name="Group 31019"
                                                                xmlns="http://www.w3.org/2000/svg" width="60"
                                                                height="60" viewBox="0 0 60 60">
                                                                <defs>
                                                                    <clipPath id="a">
                                                                        <path data-name="Rectangle 2947" fill="#130708"
                                                                            d="M0 0h60v60H0z"></path>
                                                                    </clipPath>
                                                                </defs>
                                                                <g data-name="Group 31018" clip-path="url(#a)">
                                                                    <path data-name="Path 55421"
                                                                        d="M28.119-.095h3.762c.151.033.3.073.453.1 1.545.24 3.111.383 4.632.73a30.041 30.041 0 0 1 22.673 24.08c.184 1.1.306 2.2.456 3.307v3.762c-.033.151-.076.3-.1.454-.2 1.392-.334 2.8-.619 4.176a30.1 30.1 0 0 1-24.133 23.117c-1.116.181-2.241.31-3.362.464h-3.762c-.131-.032-.26-.075-.393-.094-1.373-.2-2.76-.333-4.119-.605A30.1 30.1 0 0 1 .376 35.305c-.184-1.138-.315-2.283-.47-3.424v-3.762c.117-.908.217-1.818.353-2.724a28.562 28.562 0 0 1 2.807-8.79 1.169 1.169 0 0 1 .657-.581.923.923 0 0 1 .813.253 1.323 1.323 0 0 1 .114 1.014c-.413 1.117-.921 2.2-1.374 3.3-.158.387-.257.8-.393 1.23l11.923 3.19a16.459 16.459 0 0 1 3.229-5.614l-8.792-8.792c-.9 1.172-1.818 2.363-2.731 3.558-.392.512-.874.643-1.311.346a.894.894 0 0 1-.139-1.344A29.817 29.817 0 0 1 24.699.384c1.13-.214 2.28-.321 3.42-.478m-12.343 30.07a14.224 14.224 0 1 0 14.276-14.2 14.25 14.25 0 0 0-14.276 14.2m6.043-27.1a28.064 28.064 0 0 0-11.143 6.412l8.744 8.743a16.262 16.262 0 0 1 5.594-3.226L21.819 2.878m-7.55 30.427c-.089-1.146-.238-2.215-.238-3.284 0-1.087.146-2.175.233-3.325L2.413 23.521a28.44 28.44 0 0 0 .008 12.956l11.848-3.172m12.429-19.043c1.182-.089 2.253-.233 3.324-.231s2.137.149 3.279.238L36.477 2.42a28.191 28.191 0 0 0-12.953.005l3.174 11.837m9.78 43.319-3.202-11.936a16.259 16.259 0 0 1-6.557.01l-3.2 11.924a28.379 28.379 0 0 0 12.954 0m14.261-46.86-8.739 8.726a16.507 16.507 0 0 1 3.209 5.57l11.915-3.193a28.232 28.232 0 0 0-6.38-11.1m-.038 38.6a28.233 28.233 0 0 0 6.416-11.142l-11.92-3.192a16.53 16.53 0 0 1-3.227 5.606l8.736 8.725M49.323 9.305a28.153 28.153 0 0 0-11.144-6.422l-3.191 11.922a16.536 16.536 0 0 1 5.605 3.229l8.73-8.729M38.179 57.12a28.194 28.194 0 0 0 11.107-6.39l-8.73-8.734a16.556 16.556 0 0 1-5.568 3.209l3.191 11.915m7.471-23.842 11.93 3.2a28.191 28.191 0 0 0-.007-12.947l-11.923 3.2a16.258 16.258 0 0 1 0 6.555M9.306 49.321l8.729-8.73a16.607 16.607 0 0 1-3.233-5.6l-11.92 3.192a28.245 28.245 0 0 0 6.424 11.138m12.519 7.8 3.192-11.927a16.451 16.451 0 0 1-5.611-3.22l-8.731 8.72a28.235 28.235 0 0 0 11.147 6.422"
                                                                        fill="#130708"></path>
                                                                    <path data-name="Path 55422"
                                                                        d="M38.198 37.534a2.43 2.43 0 0 1-2-1.363.656.656 0 0 0-.685-.393q-5.521.014-11.043 0a.666.666 0 0 0-.679.4 2.607 2.607 0 0 1-3.223 1.206 2.662 2.662 0 0 1-1.689-2.852 2.617 2.617 0 0 1 2.658-2.259.694.694 0 0 0 .717-.431q2.73-4.759 5.491-9.5a.7.7 0 0 0-.02-.834 2.634 2.634 0 1 1 4.536.008.7.7 0 0 0-.009.834q2.763 4.74 5.49 9.5a.7.7 0 0 0 .722.422 2.643 2.643 0 0 1 2.682 2.679 2.708 2.708 0 0 1-2.953 2.582m-8.258-3.514h5.581a.814.814 0 0 0 .716-1.266q-2.742-4.753-5.487-9.5a.828.828 0 0 0-1.5 0l-5.487 9.5a.817.817 0 0 0 .768 1.269h5.4m-8.439 1.755a.854.854 0 0 0 .866-.841.88.88 0 0 0-.871-.907.916.916 0 0 0-.878.855.88.88 0 0 0 .884.893m8.511-14.712a.877.877 0 1 0-.876-.836.874.874 0 0 0 .876.836m9.371 13.872a.91.91 0 0 0-.883-.908.881.881 0 0 0-.866.854.855.855 0 0 0 .813.892.88.88 0 0 0 .936-.839"
                                                                        fill="#ee4e5e"></path>
                                                                </g>
                                                            </svg></div>
                                                        <p>{{ $horoscope['vedicList']['todayHoroscope'][0]['zodiac'] }}
                                                            Lucky
                                                            Color For Today
                                                            <br><strong>{{ $horoscope['vedicList']['todayHoroscope'][0]['lucky_color'] }}</strong>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <div class="my-3"><svg data-name="Group 31029"
                                                                xmlns="http://www.w3.org/2000/svg" width="58.271"
                                                                height="59.841" viewBox="0 0 58.271 59.841">
                                                                <defs>
                                                                    <clipPath id="nu">
                                                                        <path data-name="Rectangle 2949" fill="none"
                                                                            d="M0 0h58.271v59.841H0z"></path>
                                                                    </clipPath>
                                                                </defs>
                                                                <g data-name="Group 31028" clip-path="url(#nu)">
                                                                    <path data-name="Path 55445"
                                                                        d="M7.971 59.842a20.766 20.766 0 0 1-2.633-1.036 5.6 5.6 0 0 1-2.837-3.832c-.419-1.957-.688-3.948-1-5.927a.845.845 0 0 1 .688-1.048.868.868 0 0 1 1.012.741 2.8 2.8 0 0 1 .065.285c.246 1.537.5 3.073.733 4.612a4.723 4.723 0 0 0 3.484 4.232 5.006 5.006 0 0 0 2.07.126q7.039-1.076 14.063-2.255a4.778 4.778 0 0 0 3.954-5.763c-.741-4.512-1.482-9.024-2.187-13.542a4.915 4.915 0 0 0-5.993-4.172c-4.466.777-8.948 1.467-13.421 2.2a4.773 4.773 0 0 0-4.118 5.839c.233 1.381.444 2.766.662 4.15.1.661-.164 1.119-.7 1.207-.5.083-.9-.247-1.007-.894-.272-1.592-.587-3.18-.768-4.783a6.433 6.433 0 0 1 5.473-7.229q7.109-1.194 14.236-2.279a6.4 6.4 0 0 1 7.262 5.262c.7 4.064 1.346 8.139 1.987 12.214a16.612 16.612 0 0 1 .367 3.527 6.4 6.4 0 0 1-5.661 6.032c-4.69.734-9.375 1.5-14.062 2.26a.679.679 0 0 0-.158.07Z">
                                                                    </path>
                                                                    <path data-name="Path 55446"
                                                                        d="M58.262 44.675c0 2.357.025 4.715-.006 7.071a6.426 6.426 0 0 1-5.065 6.377 6.192 6.192 0 0 1-1.387.171c-4.792.012-9.585.039-14.376 0a6.433 6.433 0 0 1-6.357-5.245 10.122 10.122 0 0 1-.138-1.86c-.019-1.441-.007-2.883 0-4.325a4 4 0 0 1 .026-.582.847.847 0 0 1 .9-.808.827.827 0 0 1 .813.834c.019.232.015.467.017.7.013 1.675 0 3.35.043 5.024a4.724 4.724 0 0 0 4.864 4.518c4.675.011 9.351.028 14.026-.006a4.7 4.7 0 0 0 4.888-4.858q.062-7.071 0-14.143a4.7 4.7 0 0 0-4.971-4.826q-6.9-.015-13.792 0a4.768 4.768 0 0 0-5.052 5c-.021 1.363 0 2.727-.006 4.091 0 .213.052.487-.057.627-.207.266-.506.62-.779.632s-.582-.33-.834-.562c-.089-.082-.078-.3-.077-.449.009-1.714-.048-3.432.055-5.141a6.365 6.365 0 0 1 6.192-5.932q7.421-.073 14.844 0a6.42 6.42 0 0 1 6.227 6.156c0 .117.013.234.013.35v7.188">
                                                                    </path>
                                                                    <path data-name="Path 55447"
                                                                        d="M32.342 30.822c-.666-.126-1.347-.2-2-.386q-6.617-1.894-13.223-3.829a6.512 6.512 0 0 1-4.645-8.516l1.336-4.593a2.662 2.662 0 0 1 .176-.493.8.8 0 0 1 .968-.465.768.768 0 0 1 .637.911 16.263 16.263 0 0 1-.417 1.637c-.375 1.328-.825 2.639-1.127 3.983a4.678 4.678 0 0 0 3.4 5.8q6.713 1.983 13.446 3.9a4.7 4.7 0 0 0 6.064-3.271q2.033-6.759 3.945-13.553a4.713 4.713 0 0 0-3.374-6.062Q30.846 3.9 24.14 2a4.743 4.743 0 0 0-6.143 3.4c-.389 1.284-.753 2.575-1.134 3.862-.06.2-.092.484-.236.58-.279.187-.674.424-.936.348s-.454-.492-.612-.793c-.065-.124.024-.342.072-.509.4-1.4.792-2.807 1.23-4.2A6.444 6.444 0 0 1 24.437.265q6.878 1.937 13.724 3.986a6.437 6.437 0 0 1 4.433 8.143q-1.921 6.761-3.911 13.5a6.651 6.651 0 0 1-6.341 4.926">
                                                                    </path>
                                                                    <path data-name="Path 55448"
                                                                        d="M13.17 49.69c1.539-.253 2.9-.478 4.257-.7.735-.119 1.057.035 1.21.567a.824.824 0 0 1-.678 1.132c-1.765.3-3.531.584-5.3.847-.815.121-1-.109-1.223-.856a4 4 0 0 1 1.253-4.225 38.657 38.657 0 0 0 2.128-2.325 2.079 2.079 0 0 0 .561-1.991 1.5 1.5 0 0 0-1.739-1.117 1.359 1.359 0 0 0-1.32 1.537.981.981 0 0 1-.966 1.081c-.534.061-.859-.272-.955-.943a3.132 3.132 0 0 1 2.9-3.44 3.343 3.343 0 0 1 3.528 1.408 3.222 3.222 0 0 1-.046 3.788 29.812 29.812 0 0 1-2.192 2.565 10.578 10.578 0 0 0-1.142 1.394 4.341 4.341 0 0 0-.276 1.278"
                                                                        fill="#ee4e5e"></path>
                                                                    <path data-name="Path 55449"
                                                                        d="M46.893 44.246c2.208 1.066 1.655 5.1-.148 5.962a5.065 5.065 0 0 1-4.823-.346 2.707 2.707 0 0 1-1.051-2c-.044-.6.159-.869.693-.952q1.015-.157 1.2.687a1.6 1.6 0 0 0 1.911 1.349 1.519 1.519 0 0 0 1.691-1.48c.014-.1.022-.194.028-.291.083-1.372-.338-1.881-1.7-2.051-.665-.083-1.016-.555-.741-1.121a1.113 1.113 0 0 1 .744-.5c.552-.085 1.06-.184 1.25-.776a2.074 2.074 0 0 0-.159-1.96c-.465-.613-1.182-.507-1.837-.39a1.111 1.111 0 0 0-.917 1.068c-.1.7-.437.933-1.176.852-.582-.064-.77-.312-.725-1.027a2.575 2.575 0 0 1 1.71-2.368 4.694 4.694 0 0 1 3.587-.008 2.265 2.265 0 0 1 1.4 1.607 3.8 3.8 0 0 1-.944 3.746"
                                                                        fill="#ee4e5e"></path>
                                                                    <path data-name="Path 55450"
                                                                        d="M28.18 12.208c-.51.3-.914.582-1.276.046-.284-.42-.1-.947.452-1.261a17.841 17.841 0 0 1 2.021-1.039 1.331 1.331 0 0 1 .96.094c.359.173.348.548.238.923q-1.426 4.868-2.835 9.741a.853.853 0 0 1-1.241.651c-.608-.187-.808-.563-.619-1.22q1.05-3.641 2.111-7.279c.053-.182.1-.364.189-.655"
                                                                        fill="#ee4e5e"></path>
                                                                </g>
                                                            </svg></div>
                                                        <p>{{ $horoscope['vedicList']['todayHoroscope'][0]['zodiac'] }}
                                                            Lucky
                                                            Number For Today
                                                            <br><strong>{{ $horoscope['vedicList']['todayHoroscope'][0]['lucky_number'] }}</strong>
                                                        </p>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div>
                                    <p>No Daily Horoscope Found</p>
                                </div>
                                @endif
                            </div>
                          
                            {{-- End --}}

                            {{-- For weekly --}}
                           
                            <div class="col-12 pt-4 mt-3 tab-pane fade" id="weeklyData">
                                @if (!empty($horoscope['vedicList']['weeklyHoroScope'][0]))
                                <h2 class="cat-heading mb-4">Free <span
                                        class="color-red">{{ $horoscope['vedicList']['weeklyHoroScope'][0]['zodiac'] }}</span>
                                    Weekly
                                    Horoscope, {{ date("d-m-Y" , strtotime($horoscope['vedicList']['weeklyHoroScope'][0]['start_date'])) }} To
                                    {{ date("d-m-Y" , strtotime($horoscope['vedicList']['weeklyHoroScope'][0]['end_date'])) }}
                                </h2>
                                <div class="d-md-flex align-items-start ">
                                    <img src="/{{ $signRcd[0]->image }}" style="max-height: 140px"
                                        class="mr-md-3 mx-auto d-block" alt="Aries">
                                    <div class="media-body">
                                        <div class="dailyhoroscope-content mt-3">
                                            <p>Free {{ $horoscope['vedicList']['weeklyHoroScope'][0]['zodiac'] }} Weekly
                                                Horoscope
                                            </p>
                                            <p> {{ $horoscope['vedicList']['weeklyHoroScope'][0]['bot_response'] }}</p>
                                        </div>

                                        <div class="shadow-pink rounded-10">
                                            <div class="bg-pink">
                                                <h3 class="text-center font-weight-bold color-red py-2">Weekly's Lucky
                                                    Color
                                                    and
                                                    Number For
                                                    {{ $horoscope['vedicList']['weeklyHoroScope'][0]['zodiac'] }}
                                                </h3>
                                            </div>
                                            <div class="px-3">
                                                <div class="row">
                                                    <div class="col-md-6 text-center">
                                                        <div class="my-3"><svg data-name="Group 31019"
                                                                xmlns="http://www.w3.org/2000/svg" width="60"
                                                                height="60" viewBox="0 0 60 60">
                                                                <defs>
                                                                    <clipPath id="a">
                                                                        <path data-name="Rectangle 2947" fill="#130708"
                                                                            d="M0 0h60v60H0z"></path>
                                                                    </clipPath>
                                                                </defs>
                                                                <g data-name="Group 31018" clip-path="url(#a)">
                                                                    <path data-name="Path 55421"
                                                                        d="M28.119-.095h3.762c.151.033.3.073.453.1 1.545.24 3.111.383 4.632.73a30.041 30.041 0 0 1 22.673 24.08c.184 1.1.306 2.2.456 3.307v3.762c-.033.151-.076.3-.1.454-.2 1.392-.334 2.8-.619 4.176a30.1 30.1 0 0 1-24.133 23.117c-1.116.181-2.241.31-3.362.464h-3.762c-.131-.032-.26-.075-.393-.094-1.373-.2-2.76-.333-4.119-.605A30.1 30.1 0 0 1 .376 35.305c-.184-1.138-.315-2.283-.47-3.424v-3.762c.117-.908.217-1.818.353-2.724a28.562 28.562 0 0 1 2.807-8.79 1.169 1.169 0 0 1 .657-.581.923.923 0 0 1 .813.253 1.323 1.323 0 0 1 .114 1.014c-.413 1.117-.921 2.2-1.374 3.3-.158.387-.257.8-.393 1.23l11.923 3.19a16.459 16.459 0 0 1 3.229-5.614l-8.792-8.792c-.9 1.172-1.818 2.363-2.731 3.558-.392.512-.874.643-1.311.346a.894.894 0 0 1-.139-1.344A29.817 29.817 0 0 1 24.699.384c1.13-.214 2.28-.321 3.42-.478m-12.343 30.07a14.224 14.224 0 1 0 14.276-14.2 14.25 14.25 0 0 0-14.276 14.2m6.043-27.1a28.064 28.064 0 0 0-11.143 6.412l8.744 8.743a16.262 16.262 0 0 1 5.594-3.226L21.819 2.878m-7.55 30.427c-.089-1.146-.238-2.215-.238-3.284 0-1.087.146-2.175.233-3.325L2.413 23.521a28.44 28.44 0 0 0 .008 12.956l11.848-3.172m12.429-19.043c1.182-.089 2.253-.233 3.324-.231s2.137.149 3.279.238L36.477 2.42a28.191 28.191 0 0 0-12.953.005l3.174 11.837m9.78 43.319-3.202-11.936a16.259 16.259 0 0 1-6.557.01l-3.2 11.924a28.379 28.379 0 0 0 12.954 0m14.261-46.86-8.739 8.726a16.507 16.507 0 0 1 3.209 5.57l11.915-3.193a28.232 28.232 0 0 0-6.38-11.1m-.038 38.6a28.233 28.233 0 0 0 6.416-11.142l-11.92-3.192a16.53 16.53 0 0 1-3.227 5.606l8.736 8.725M49.323 9.305a28.153 28.153 0 0 0-11.144-6.422l-3.191 11.922a16.536 16.536 0 0 1 5.605 3.229l8.73-8.729M38.179 57.12a28.194 28.194 0 0 0 11.107-6.39l-8.73-8.734a16.556 16.556 0 0 1-5.568 3.209l3.191 11.915m7.471-23.842 11.93 3.2a28.191 28.191 0 0 0-.007-12.947l-11.923 3.2a16.258 16.258 0 0 1 0 6.555M9.306 49.321l8.729-8.73a16.607 16.607 0 0 1-3.233-5.6l-11.92 3.192a28.245 28.245 0 0 0 6.424 11.138m12.519 7.8 3.192-11.927a16.451 16.451 0 0 1-5.611-3.22l-8.731 8.72a28.235 28.235 0 0 0 11.147 6.422"
                                                                        fill="#130708"></path>
                                                                    <path data-name="Path 55422"
                                                                        d="M38.198 37.534a2.43 2.43 0 0 1-2-1.363.656.656 0 0 0-.685-.393q-5.521.014-11.043 0a.666.666 0 0 0-.679.4 2.607 2.607 0 0 1-3.223 1.206 2.662 2.662 0 0 1-1.689-2.852 2.617 2.617 0 0 1 2.658-2.259.694.694 0 0 0 .717-.431q2.73-4.759 5.491-9.5a.7.7 0 0 0-.02-.834 2.634 2.634 0 1 1 4.536.008.7.7 0 0 0-.009.834q2.763 4.74 5.49 9.5a.7.7 0 0 0 .722.422 2.643 2.643 0 0 1 2.682 2.679 2.708 2.708 0 0 1-2.953 2.582m-8.258-3.514h5.581a.814.814 0 0 0 .716-1.266q-2.742-4.753-5.487-9.5a.828.828 0 0 0-1.5 0l-5.487 9.5a.817.817 0 0 0 .768 1.269h5.4m-8.439 1.755a.854.854 0 0 0 .866-.841.88.88 0 0 0-.871-.907.916.916 0 0 0-.878.855.88.88 0 0 0 .884.893m8.511-14.712a.877.877 0 1 0-.876-.836.874.874 0 0 0 .876.836m9.371 13.872a.91.91 0 0 0-.883-.908.881.881 0 0 0-.866.854.855.855 0 0 0 .813.892.88.88 0 0 0 .936-.839"
                                                                        fill="#ee4e5e"></path>
                                                                </g>
                                                            </svg></div>
                                                        <p>{{ $horoscope['vedicList']['weeklyHoroScope'][0]['zodiac'] }}
                                                            Lucky
                                                            Color For week
                                                            <br><strong>{{ $horoscope['vedicList']['weeklyHoroScope'][0]['lucky_color'] }}</strong>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <div class="my-3"><svg data-name="Group 31029"
                                                                xmlns="http://www.w3.org/2000/svg" width="58.271"
                                                                height="59.841" viewBox="0 0 58.271 59.841">
                                                                <defs>
                                                                    <clipPath id="nu">
                                                                        <path data-name="Rectangle 2949" fill="none"
                                                                            d="M0 0h58.271v59.841H0z"></path>
                                                                    </clipPath>
                                                                </defs>
                                                                <g data-name="Group 31028" clip-path="url(#nu)">
                                                                    <path data-name="Path 55445"
                                                                        d="M7.971 59.842a20.766 20.766 0 0 1-2.633-1.036 5.6 5.6 0 0 1-2.837-3.832c-.419-1.957-.688-3.948-1-5.927a.845.845 0 0 1 .688-1.048.868.868 0 0 1 1.012.741 2.8 2.8 0 0 1 .065.285c.246 1.537.5 3.073.733 4.612a4.723 4.723 0 0 0 3.484 4.232 5.006 5.006 0 0 0 2.07.126q7.039-1.076 14.063-2.255a4.778 4.778 0 0 0 3.954-5.763c-.741-4.512-1.482-9.024-2.187-13.542a4.915 4.915 0 0 0-5.993-4.172c-4.466.777-8.948 1.467-13.421 2.2a4.773 4.773 0 0 0-4.118 5.839c.233 1.381.444 2.766.662 4.15.1.661-.164 1.119-.7 1.207-.5.083-.9-.247-1.007-.894-.272-1.592-.587-3.18-.768-4.783a6.433 6.433 0 0 1 5.473-7.229q7.109-1.194 14.236-2.279a6.4 6.4 0 0 1 7.262 5.262c.7 4.064 1.346 8.139 1.987 12.214a16.612 16.612 0 0 1 .367 3.527 6.4 6.4 0 0 1-5.661 6.032c-4.69.734-9.375 1.5-14.062 2.26a.679.679 0 0 0-.158.07Z">
                                                                    </path>
                                                                    <path data-name="Path 55446"
                                                                        d="M58.262 44.675c0 2.357.025 4.715-.006 7.071a6.426 6.426 0 0 1-5.065 6.377 6.192 6.192 0 0 1-1.387.171c-4.792.012-9.585.039-14.376 0a6.433 6.433 0 0 1-6.357-5.245 10.122 10.122 0 0 1-.138-1.86c-.019-1.441-.007-2.883 0-4.325a4 4 0 0 1 .026-.582.847.847 0 0 1 .9-.808.827.827 0 0 1 .813.834c.019.232.015.467.017.7.013 1.675 0 3.35.043 5.024a4.724 4.724 0 0 0 4.864 4.518c4.675.011 9.351.028 14.026-.006a4.7 4.7 0 0 0 4.888-4.858q.062-7.071 0-14.143a4.7 4.7 0 0 0-4.971-4.826q-6.9-.015-13.792 0a4.768 4.768 0 0 0-5.052 5c-.021 1.363 0 2.727-.006 4.091 0 .213.052.487-.057.627-.207.266-.506.62-.779.632s-.582-.33-.834-.562c-.089-.082-.078-.3-.077-.449.009-1.714-.048-3.432.055-5.141a6.365 6.365 0 0 1 6.192-5.932q7.421-.073 14.844 0a6.42 6.42 0 0 1 6.227 6.156c0 .117.013.234.013.35v7.188">
                                                                    </path>
                                                                    <path data-name="Path 55447"
                                                                        d="M32.342 30.822c-.666-.126-1.347-.2-2-.386q-6.617-1.894-13.223-3.829a6.512 6.512 0 0 1-4.645-8.516l1.336-4.593a2.662 2.662 0 0 1 .176-.493.8.8 0 0 1 .968-.465.768.768 0 0 1 .637.911 16.263 16.263 0 0 1-.417 1.637c-.375 1.328-.825 2.639-1.127 3.983a4.678 4.678 0 0 0 3.4 5.8q6.713 1.983 13.446 3.9a4.7 4.7 0 0 0 6.064-3.271q2.033-6.759 3.945-13.553a4.713 4.713 0 0 0-3.374-6.062Q30.846 3.9 24.14 2a4.743 4.743 0 0 0-6.143 3.4c-.389 1.284-.753 2.575-1.134 3.862-.06.2-.092.484-.236.58-.279.187-.674.424-.936.348s-.454-.492-.612-.793c-.065-.124.024-.342.072-.509.4-1.4.792-2.807 1.23-4.2A6.444 6.444 0 0 1 24.437.265q6.878 1.937 13.724 3.986a6.437 6.437 0 0 1 4.433 8.143q-1.921 6.761-3.911 13.5a6.651 6.651 0 0 1-6.341 4.926">
                                                                    </path>
                                                                    <path data-name="Path 55448"
                                                                        d="M13.17 49.69c1.539-.253 2.9-.478 4.257-.7.735-.119 1.057.035 1.21.567a.824.824 0 0 1-.678 1.132c-1.765.3-3.531.584-5.3.847-.815.121-1-.109-1.223-.856a4 4 0 0 1 1.253-4.225 38.657 38.657 0 0 0 2.128-2.325 2.079 2.079 0 0 0 .561-1.991 1.5 1.5 0 0 0-1.739-1.117 1.359 1.359 0 0 0-1.32 1.537.981.981 0 0 1-.966 1.081c-.534.061-.859-.272-.955-.943a3.132 3.132 0 0 1 2.9-3.44 3.343 3.343 0 0 1 3.528 1.408 3.222 3.222 0 0 1-.046 3.788 29.812 29.812 0 0 1-2.192 2.565 10.578 10.578 0 0 0-1.142 1.394 4.341 4.341 0 0 0-.276 1.278"
                                                                        fill="#ee4e5e"></path>
                                                                    <path data-name="Path 55449"
                                                                        d="M46.893 44.246c2.208 1.066 1.655 5.1-.148 5.962a5.065 5.065 0 0 1-4.823-.346 2.707 2.707 0 0 1-1.051-2c-.044-.6.159-.869.693-.952q1.015-.157 1.2.687a1.6 1.6 0 0 0 1.911 1.349 1.519 1.519 0 0 0 1.691-1.48c.014-.1.022-.194.028-.291.083-1.372-.338-1.881-1.7-2.051-.665-.083-1.016-.555-.741-1.121a1.113 1.113 0 0 1 .744-.5c.552-.085 1.06-.184 1.25-.776a2.074 2.074 0 0 0-.159-1.96c-.465-.613-1.182-.507-1.837-.39a1.111 1.111 0 0 0-.917 1.068c-.1.7-.437.933-1.176.852-.582-.064-.77-.312-.725-1.027a2.575 2.575 0 0 1 1.71-2.368 4.694 4.694 0 0 1 3.587-.008 2.265 2.265 0 0 1 1.4 1.607 3.8 3.8 0 0 1-.944 3.746"
                                                                        fill="#ee4e5e"></path>
                                                                    <path data-name="Path 55450"
                                                                        d="M28.18 12.208c-.51.3-.914.582-1.276.046-.284-.42-.1-.947.452-1.261a17.841 17.841 0 0 1 2.021-1.039 1.331 1.331 0 0 1 .96.094c.359.173.348.548.238.923q-1.426 4.868-2.835 9.741a.853.853 0 0 1-1.241.651c-.608-.187-.808-.563-.619-1.22q1.05-3.641 2.111-7.279c.053-.182.1-.364.189-.655"
                                                                        fill="#ee4e5e"></path>
                                                                </g>
                                                            </svg></div>
                                                        <p>{{ $horoscope['vedicList']['weeklyHoroScope'][0]['zodiac'] }}
                                                            Lucky
                                                            Number For Week
                                                            <br><strong>{{ $horoscope['vedicList']['weeklyHoroScope'][0]['lucky_number'] }}</strong>
                                                        </p>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div>
                                    <p>No Weekly Horoscope Found</p>
                                </div>
                                
                                @endif
                            </div>
                         
                            {{-- End --}}
                            {{-- For Yearly  --}}
                            
                            <div class="col-12 pt-4 mt-3 tab-pane fade " id="yearlyData">
                                @if (!empty($horoscope['vedicList']['yearlyHoroScope'][0]))
                                <h2 class="cat-heading mb-4">Free <span
                                        class="color-red">{{ $horoscope['vedicList']['yearlyHoroScope'][0]['zodiac'] }}</span>
                                    Yearly
                                    Horoscope, {{ date('Y') }}
                                </h2>
                                <div class="d-md-flex align-items-start ">
                                    <img src="/{{ $signRcd[0]->image }}" style="max-height: 140px"
                                        class="mr-md-3 mx-auto d-block" alt="Aries">
                                    <div class="media-body">
                                        <div class="dailyhoroscope-content mt-3">
                                            <p>Free {{ $horoscope['vedicList']['yearlyHoroScope'][0]['zodiac'] }} Yearly
                                                Horoscope
                                            </p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['bot_response'] }}</p>
                                            <p>Health Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['health_remark'] }}</p>
                                            <p>Relationship Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['relationship_remark'] }}
                                            </p>
                                            <p>Travel Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['travel_remark'] }}</p>
                                            <p>Family Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['family_remark'] }}</p>
                                            <p>Friends Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['friends_remark'] }}</p>
                                            <p>Finances Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['finances_remark'] }}</p>
                                            <p>Status Remark :</p>
                                            <p> {{ $horoscope['vedicList']['yearlyHoroScope'][0]['status_remark'] }}</p>
                                        </div>

                                    </div>
                                </div>
                                @else
                                <div>
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
