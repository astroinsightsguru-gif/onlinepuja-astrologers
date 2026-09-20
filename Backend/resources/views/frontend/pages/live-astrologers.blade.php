@extends('frontend.layout.master')
@section('content')
    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getLiveAstro') }}"
                                style="color:white;text-decoration:none">Live {{ucfirst($professionTitle)}}s</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-pink">
        <div class="container">
            <div class="d-flex py-3 align-items-center justify-content-between">
                <div>
                    <h2 class="cat-heading live-session text-left text-capitalize">INTERACTIVE LIVE SESSIONS</h2>
                    <p class="cat-description pt-2 mb-0 text-left">Talk to premium {{ucfirst($professionTitle)}}s through Free Live Sessions
                    </p>
                </div>
                <div>
                    <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/livestream/live-event.png') }}"
                        class="img-fluid" width="74" height="70" alt="live-event">
                </div>
            </div>
        </div>
    </div>


    <div>
        <div class="container">
            <div class="row py-3 py-md-5">
                <div class="col-sm-12">
                    <h2 class="cat-heading live-home pb-2">LIVE SESSIONS</h2>
                </div>
            </div>

            @if($liveastro['recordList'])
            <div class="d-flex flex-wrap pb-5">

                @foreach ($liveastro['recordList'] as $live)

                <div class="live-astrologer gif-animation-enable position-relative m-2"
                    style="background:url('{{asset('public/frontend/onlinepujacdn/onlinepuja/category/banner/643714c2-9893-4183-85fc-ed89513a2a0d.jpg')}}')">
                    <div class="position-absolute top-part">
                        <span class="bg-red px-2 text-white d-inline-flex align-items-center rounded font-12"><i
                                class="fa fa-circle font-11 mr-1"></i>Live</span>
                    </div>
                    <div class="d-flex h-100 align-items-center">
                        <div class="position-relative profile-pic">
                            @if($live['profileImage'])
                            <img width="100" height="145" loading="lazy" class="expert-profile-pic" src="{{ Str::startsWith($live['profileImage'], ['http://','https://']) ? $live['profileImage'] : '/' . $live['profileImage'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Astro image" />
                            @else
                            <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                            class="expert-profile-pic"  width="100" height="145" loading="lazy">
                            @endif
                        </div>
                        <div class=" ml-2">
                            <p class="mb-0 pb-0 text-white font-16 text-capitalize"><a
                                    href="#"  class="text-white"> {{ $live['name'] }}</a></p>


                        </div>
                    </div>
                    <div class="position-absolute bottom-part">
                        <a href="{{route('front.LiveAstroDetails',['astrologerId'=>$live['astrologerId']])}}"
                            class="btn join-now-btn font-12 text-white d-flex align-items-center justify-content-center">Join
                            Now</a>
                    </div>
                </div>

                @endforeach

            </div>
            @else

                <h3 class="d-flex justify-content-center mb-5">No Live {{ucfirst($professionTitle)}} Found</3>

                @endif
        </div>
    </div>
@endsection
