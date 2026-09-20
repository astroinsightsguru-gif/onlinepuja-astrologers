@extends('frontend.astrologers.layout.master')
@section('content')

    <div class="container d-flex justify-content-start gap-4 min-vh-100">
        <div id="allText" class="row flex-column flex-md-row gap-4 w-100">

            <!-- Blog Content -->
            <div class="flex-grow-1 col-md-8 px-0 px-md-4 mt-4 mt-md-5 mb-4 mb-md-5">
                <h1 class="display-5 px-md-0 px-3 fw-bold text-dark">
                    {{ $blog->title }}
                </h1>

                <!-- Optional horizontal scroll container (hidden on desktop) -->
                <div class="overflow-auto d-md-none px-3"></div>

                <!-- Blog Image -->
                <div class="w-100 my-4">
                    @php
                        $extension = pathinfo($blog->blogImage, PATHINFO_EXTENSION);
                        $videoExtensions = ['mp4', 'webm', 'ogg']; // Add other formats if needed
                    @endphp

                    @if(in_array($extension, $videoExtensions))
                        <video class="img-fluid rounded ml-15" controls style="height: 400px;width:100%">
                            <source src="{{ asset($blog->blogImage) }}" type="video/{{ $extension }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img src="{{ asset($blog->blogImage) }}" class="img-fluid rounded ml-15" alt="{{ $blog->title }}">
                    @endif
                </div>

                <!-- Blog Description -->
                <div class="mt-4 px-3 px-md-0 d-flex flex-column gap-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex flex-column gap-4">
                                <div class="fs-5 text-muted" style="line-height: 1.6;">
                                    {!! $blog->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (hidden on mobile) -->
            <div class="d-none d-md-flex flex-column py-5 col-md-4 gap-5 px-lg-4 w-100 ">
                <div class="bg-light shadow-sm rounded p-3">
                    <h2 class="text-gradient text-left" style="background: linear-gradient(to right, #6a1b9a, #00c853); -webkit-background-clip: text; color: transparent; font-size: 1.75rem;">
                        Explore More
                    </h2>
                    @php
                        $total = count($latestBlogs);
                    @endphp

                        @foreach ($latestBlogs as $index => $latest)
                        <div class="d-flex flex-column {{ $index < $total - 1 ? 'border-bottom pb-2 mb-2 border-secondary' : '' }}">
                            <div class="d-flex align-items-center gap-2 text-dark py-3">
                                @php
                                    $extension = pathinfo($latest->blogImage, PATHINFO_EXTENSION);
                                    $videoExtensions = ['mp4', 'webm', 'ogg']; // Add more if needed
                                @endphp

                                @if(in_array($extension, $videoExtensions))
                                    <video class="rounded" width="50" height="50" style="object-fit: cover;" muted>
                                        <source src="{{ asset($latest->blogImage) }}" type="video/{{ $extension }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ asset($latest->blogImage) }}" class="rounded" width="50" height="50" style="object-fit: cover;">
                                @endif

                                <i class="fa-solid fa-arrow-right"></i>
                                <a href="{{ route('front.getBlogDetails', $latest->slug) }}" class="fw-bold text-dark text-decoration-none ml-2 font-20">
                                    {{ $latest->title }}
                                </a>
                            </div>
                        </div>
                        @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
