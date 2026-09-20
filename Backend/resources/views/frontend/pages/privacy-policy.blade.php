@extends('frontend.layout.master')
@section('content')
    <div class="pt-1 pb-1 bg-red d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> Privacy Policy
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="ds-head-body">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mt-5">
                    <h2 class="h2 font-weight-bold color-off-black mb-4 cat-heading">{{$privacy->title}}</h2>
                    {!!$privacy->description!!}

            </div>
        </div>
    </div>
@endsection
