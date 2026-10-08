@extends('frontend.layout.master')

@push('styles')
<style>
    .loader_full__LR0ml {
        transition: opacity .4s ease;
    }
    .loader_image__D6P69 {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .object-cover {
        -o-object-fit: cover;
        object-fit: cover;
    }

    /* Redesigned Blog Card */
    .blog-card-custom {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.015);
    }

    .blog-card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(64, 14, 47, 0.08);
    }

    .blog-card-media {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: #000;
    }

    .blog-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .blog-card-custom:hover .blog-card-img {
        transform: scale(1.05);
    }

    /* Floating badge on image */
    .blog-card-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(64, 14, 47, 0.85);
        backdrop-filter: blur(4px);
        color: #ffffff;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        z-index: 5;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Card Content */
    .blog-card-content {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        justify-content: space-between;
        text-align: left;
    }

    .blog-card-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 6px; /* reduced from 10px */
        font-family: 'Outfit', sans-serif;
    }

    .blog-card-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .blog-card-meta-item i {
        color: #fbbf24;
    }

    .blog-card-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.05rem; /* reduced from 1.1rem */
        color: #1e293b;
        line-height: 1.35;
        margin-bottom: 4px; /* reduced from 8px */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8rem; /* reduced from 3.1rem */
    }

    .blog-card-desc {
        font-family: 'Outfit', sans-serif;
        font-size: 0.88rem; /* reduced from 0.9rem */
        color: #64748b;
        line-height: 1.45;
        margin-bottom: 8px; /* reduced from 15px */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.5rem; /* reduced from 2.7rem */
    }

    .blog-card-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        color: #400e2f;
        text-decoration: none !important;
        transition: gap 0.3s;
    }

    .blog-card-action i {
        transition: transform 0.2s;
    }

    .blog-card-custom:hover .blog-card-action i {
        transform: translateX(3px);
    }

    /* ==========================================================================
       Dark Mode Overrides
       ========================================================================== */
    body.home-dark-mode .blog-card-custom {
        background: #1a1611;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    body.home-dark-mode .blog-card-custom:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
    }

    body.home-dark-mode .blog-card-title {
        color: #ffffff !important;
    }

    body.home-dark-mode .blog-card-desc {
        color: #94a3b8 !important;
    }

    body.home-dark-mode .blog-card-meta {
        color: #94a3b8 !important;
    }

    body.home-dark-mode .blog-card-meta-item i {
        color: #f1e135 !important;
    }

    body.home-dark-mode .blog-card-action {
        color: #f1e135 !important;
    }

    body.home-dark-mode .blog-card-badge {
        background: rgba(241, 225, 53, 0.95) !important;
        color: #0c0a08 !important;
    }

    body.home-dark-mode .text-dark {
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container d-flex flex-column gap-5">
        <h2 class="position-relative border-bottom pb-2 text-dark">
            Ours Blogs
            <span class="position-absolute bottom-0 start-50 translate-middle-x bg-warning d-block rounded"
                  style="width: 110px; height: 3px; margin-top: -1px;">
            </span>
        </h2>
        @if (isset($bloglist) && count($bloglist)>0)
        <div class="row justify-content-start">

            @foreach ($bloglist as $blog)
            <div class="col-md-4 mt-4">
                <a href="{{ route('front.getBlogDetails', ['slug' => $blog->slug]) }}" class="text-decoration-none">
                    <div class="blog-card-custom">
                        <div class="blog-card-media">
                            @php
                                $extension = pathinfo($blog->blogImage, PATHINFO_EXTENSION);
                                $videoExtensions = ['mp4', 'webm', 'ogg'];
                                $isVideo = in_array($extension, $videoExtensions);
                                $imageSrc = Str::startsWith($blog->blogImage, ['http://','https://']) ? $blog->blogImage : asset($blog->blogImage);
                                $cleanDesc = strip_tags($blog->description);
                                $wordsCount = str_word_count($cleanDesc);
                                $readTime = max(2, ceil($wordsCount / 180));
                            @endphp


                            @if($isVideo)
                                <video class="blog-card-img" controls>
                                    <source src="{{ $imageSrc }}" type="video/{{ $extension }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <img src="{{ $imageSrc }}"
                                    class="blog-card-img"
                                    onerror="this.onerror=null; this.src='{{ asset('public/frontend/homeimage/home-analyze.png') }}';">
                            @endif
                        </div>
                        <div class="blog-card-content">
                            <div>
                                <!-- Metadata -->
                                <div class="blog-card-meta">
                                    <span class="blog-card-meta-item">
                                        <i class="fa-regular fa-calendar-days"></i>
                                        {{ date('M d, Y', strtotime($blog->created_at ?? now())) }}
                                    </span>
                                    <span class="blog-card-meta-item">
                                        <i class="fa-regular fa-eye"></i>
                                        {{ number_format($blog->viewer ?? 0) }} views
                                    </span>
                                </div>

                                <h3 class="blog-card-title">{{ $blog->title }}</h3>
                                <p class="blog-card-desc">
                                    {{ Str::words($cleanDesc, 18) }}
                                </p>
                            </div>

                            <span class="blog-card-action">
                                Read More <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach

        </div>
        @else
        <h3 class="mt-5 mb-5 text-center">No Blog Available</h3>
        @endif
        <!-- Pagination Controls -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $bloglist->links() }}
        </div>

    </div>
</div>

@endsection
