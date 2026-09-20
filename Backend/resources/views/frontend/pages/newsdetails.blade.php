@extends('frontend.layout.master')

@section('content')
<div class="container-fluid mt-5">
    <div class="row">
        <!-- Left Main Blog Content -->
        <div class="col-lg-8 mb-4">
            <!-- Blog Image -->
            <img class="rounded-m" src="{{ Str::startsWith($news->bannerImage, ['http://','https://']) ? $news->bannerImage : '/' . $news->bannerImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $news->bannerImage }}')" style="width: inherit; height: 25rem;" />
            <!-- Blog Title & Date -->
            <h4 style="background: aliceblue;padding: 10px;">{{ $news->channel }}</h4>
            <div style="background: aliceblue; padding: 10px;">
    {!! $news->description !!} 
    <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
        <a class="btn btn-primary btn-sm" href="{{ asset($news->link) }}">View More</a>
    </div>
</div>

            <p class="text-muted" style="background: aliceblue;padding: 10px;">Published on {{ \Carbon\Carbon::parse($news->created_at)->format('d M Y') }}</p>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <h3>Our Astrologers</h3>
            <!-- Astrologer List -->
            <div class="mb-4" style="max-height: 450px; overflow-y:auto;">
                <ul class="list-group">
                    @foreach($astrologers as $astrologer)
                        <li class="list-group-item p-0 mb-1" style="border: 1px solid deepskyblue;">
                            <a href="{{ $astrologer->slug ? url('/astrologer-details/' . $astrologer->slug) : '#' }}" class="d-flex align-items-center text-decoration-none text-dark">
                                <img class="rrounded-circle me-3" style="width:40px; height:40px; object-fit:cover;" src="{{ Str::startsWith($astrologer->profileImage, ['http://','https://']) ? $astrologer->profileImage : '/' . $astrologer->profileImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $astrologer->profileImage }}')" />

                                <!-- <img src="{{ asset($astrologer->profileImage) }}" class="rrounded-circle me-3" style="width:40px; height:40px; object-fit:cover;" alt="{{ $astrologer->name }}"> -->
                                <span style="font-size: 20px;padding: 0px 10px;">{{ $astrologer->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

                <h3>Recent Blogs</h3>
            <!-- Astrologer List -->
            <div class="mb-1" style="max-height: 51rem; overflow-y:auto;">
                <ul class="list-group">
                     @foreach($recentBlogs as $recent)
                        <li class="mb-2" style="border: 1px solid deepskyblue;">
                            <div class="">
                                <img class="card-img-top" style="height:120px; object-fit:cover;" src="{{ Str::startsWith($recent->blogImage, ['http://','https://']) ? $recent->blogImage : '/' . $recent->blogImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $recent->blogImage }}')" />
                        <div class="p-3">
                            <h6 class="card-title mb-1">{{ $recent->videoTitle }}</h6>
                            <p class="card-text text-muted mb-1" style="font-size:12px;">Posted on: {{ \Carbon\Carbon::parse($recent->created_at)->format('Y-m-d') }}</p>
                            <p class="card-text mb-1" style="font-size:13px;">{!! \Illuminate\Support\Str::words($recent->description, 15) !!}</p>
                            <a href="{{ route('front.getBlogDetails', $recent->slug) }}" class="btn btn-primary btn-sm">Read More</a>
                        </div>
                    </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            </div>
        </div>
    </div>

@if (isset($astrologyVideo) && count($astrologyVideo) > 0)
        <section class="py-5 bg-white" id="calculator"
            style="background: url('{{ asset('public/frontend/homeimage/videobackground.jpeg') }}');">
            <div class="container-fluid">
                <h2 class="text-center text-black py-3 font-28">Astrology Videos</h2>
        
                <!-- Marquee Container -->
                <div class="marquee-wrapper overflow-hidden position-relative">
                    <div class="marquee d-flex">
                        @foreach ($astrologyVideo as $video)
                            <a href="javascript:;" 
                               class="video-link mx-2" 
                               data-video="{{ $video->youtubeLink }}" 
                               data-description="{{ \Illuminate\Support\Str::words($video->description, 30, '...') }}"
                               data-toggle="modal" 
                               data-target="#videoModal">
                                <div class="video-card position-relative">
                                    <img class="video-thumbnail img-fluid" style="height:160px" src="{{ Str::startsWith($video->coverImage, ['http://','https://']) ? $video->coverImage : '/' . $video->coverImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $video->coverImage }}')" />

                                    <img style="cursor: pointer;" class="position-absolute youtube-icon"
                                        src="{{ asset('public/frontend/homeimage/youtube.svg') }}" alt="">
                                    <div class="video-title text-center mt-2">{{ $video->videoTitle }}</div>
                                </div>
                            </a>
                        @endforeach
        
                        <!-- Duplicate for infinite loop -->
                        @foreach ($astrologyVideo as $video)
                            <a href="javascript:;" 
                               class="video-link mx-2" 
                               data-video="{{ $video->youtubeLink }}" 
                               data-description="{{ \Illuminate\Support\Str::words($video->description, 30, '...') }}"
                               data-toggle="modal" 
                               data-target="#videoModal">
                                <div class="video-card position-relative">
                                    <img class="video-thumbnail img-fluid" style="height:160px"  src="{{ Str::startsWith($video->coverImage, ['http://','https://']) ? $video->coverImage : '/' . $video->coverImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $video->coverImage }}')" />

                                    <img style="cursor: pointer;" class="position-absolute youtube-icon"
                                        src="{{ asset('public/frontend/homeimage/youtube.svg') }}" alt="">
                                    <div class="video-title text-center mt-2">{{ $video->videoTitle }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Modal -->
        <div class="modal fade mt-5" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 30px;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" id="videoIframe" src="" allowfullscreen></iframe>
                        </div>
                        <h3 class="p-3 bg-success text-white">Video Description</h3>
                        <div class="video-description mt-2 p-3" id="videoDescription"></div>
                    </div>
                </div>
            </div>
        </div>

<style>
.marquee-wrapper {
    width: 100%;
    overflow: hidden;
    position: relative;
}

.marquee {
    display: flex;
    width: max-content;
    animation: marquee 40s linear infinite;
}

.marquee:hover {
    animation-play-state: paused; /* hover par slide ruk jayega */
}

.video-card {
    flex: 0 0 auto;
    width: 250px;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl {
    width: 100%;
    padding-right: 50px !important;
    padding-left: 50px !important;
    margin-right: auto;
    margin-left: auto;
}
.list-group-item:first-child {
    border-top-left-radius: inherit;
    border-top-right-radius: inherit;
    background: #eeeef7;
}
.list-group-item + .list-group-item {
    border-top-left-radius: inherit;
    border-top-right-radius: inherit;
    background: #eeeef7;
}
</style>

<script>
$(document).ready(function () {
    // Modal open -> play video
    $('.video-link').click(function () {
        let videoUrl = $(this).data('video');  
        let description = $(this).data('description');

        // Convert normal YouTube link into embed format
        if(videoUrl.includes("watch?v=")) {
            videoUrl = videoUrl.replace("watch?v=", "embed/"); 
        }

        $("#videoIframe").attr("src", videoUrl + "?autoplay=1");
        $("#videoDescription").text(description);
    });

    // Modal close -> stop video
    $('#videoModal').on('hidden.bs.modal', function () {
        $("#videoIframe").attr("src", "");
    });
});
</script>


@endsection

@push('styles')

<style>
.card h6 {
    font-weight: 600;
}
.list-group-item {
    font-size: 14px;
}
</style>
@endpush
