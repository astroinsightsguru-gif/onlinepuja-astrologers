@extends('frontend.astrologers.layout.master')
<style>
    .profile-card {
        display: flex;
        align-items: center;
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .profile-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
        border-color: #65a9fd;
        background: #e7f1ff;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        margin-left: 15px;
    }

    .name {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .follower-tag {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .inpage {
        background: white !important;
        min-height: 450px;
    }

    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
</style>
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
                        <i class="fa fa-chevron-right"></i> <a href="{{route('front.followerslist')}}"
                            style="color:white;text-decoration:none">Followers</a>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="inpage">
                <div class="text-left pb-md-4 pb-2">
                    <h1 class="h2 font-weight-bold colorblack">My Followers</h1>
                    <p class="text-muted">Check your complete Followers here.</p>
                </div>
                @if (isset($getastrologerfollower) && $getastrologerfollower['totalCount'] > 0)
                <div class="row">
                    @foreach ($getastrologerfollower['recordList'] as $astrologerFollowers)
                    <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
                        <div class="profile-card">
                            @php
                            $profileImage = !empty($astrologerFollowers['profile']) ? asset($astrologerFollowers['profile']) : asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/blank-profile.png');
                            @endphp
                            <img class="avatar" src="{{ $profileImage }}" alt="{{ $astrologerFollowers['name'] }}" />
                            <div class="profile-info">
                                <span class="name">{{ $astrologerFollowers['name'] }}</span>
                                <span class="follower-tag"><i class="fa fa-user-circle"></i> Follower</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-5 text-center">
                    <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/blank-profile.png') }}" class="mb-3" style="width: 80px; opacity: 0.5;" alt="No followers" />
                    <h3 class="font-weight-bold colorblack">You have no followers yet</h3>
                    <p class="text-muted">When users start following you, they will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection
