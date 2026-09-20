@extends('frontend.layout.master')

@section('content')

<div class="container py-5 min-vh-100">
    <div class="row">

        <!-- Main Blog Section -->
        <div class="col-lg-8 col-12 mb-4">
            <div class="blog-main-content pr-lg-4">

                <!-- Breadcrumbs (Manual implementation) -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent p-0" style="font-size: 14px;">
                        <li class="breadcrumb-item"><a href="/" class="blog-breadcrumb-link">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('front.getBlog') }}" class="blog-breadcrumb-link">Blog</a></li>
                        <li class="breadcrumb-item active text-muted-custom" aria-current="page">{{ Str::limit($blog->title, 20) }}</li>
                    </ol>
                </nav>

                <!-- Blog Title -->
                <h1 class="blog-main-title mb-3">
                    {{ $blog->title }}
                </h1>

                <!-- Blog Meta -->
                <div class="blog-main-meta mb-4">
                    <span><i class="far fa-calendar-alt mr-1"></i> {{ date('F d, Y', strtotime($blog->created_at ?? now())) }}</span>
                    <span><i class="far fa-user mr-1"></i> Admin</span>
                    <span><i class="far fa-eye mr-1"></i> {{ number_format($blog->viewer ?? 0) }} views</span>
                </div>

                <!-- Blog Image/Video -->
                <div class="blog-image-wrapper mb-4 shadow-sm">
                    @php
                        $extension = pathinfo($blog->blogImage, PATHINFO_EXTENSION);
                        $videoExtensions = ['mp4', 'webm', 'ogg'];
                    @endphp

                    @if(in_array($extension, $videoExtensions))
                        <video class="w-100" controls style="display: block; max-height: 450px; background: #000;">
                            <source src="{{ asset($blog->blogImage) }}" type="video/{{ $extension }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img src="{{ Str::startsWith($blog->blogImage, ['http://','https://']) ? $blog->blogImage : '/' . $blog->blogImage }}"
                             onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                             class="img-fluid w-100"
                             style="cursor: pointer; max-height: 500px; object-fit: cover;"
                             onclick="openImage('{{ $blog->blogImage }}')" alt="{{ $blog->title }}">
                    @endif
                </div>

                <!-- Blog Description -->
                <div class="blog-content">
                    {!! $blog->description !!}
                </div>
            </div>
        </div>


        <!-- Sidebar Section -->
        <div class="col-lg-4 col-12 mt-4 mt-lg-0">

            <div class="sticky-top" style="top: 100px; z-index: 10;">
                <div class="blog-sidebar-card shadow-sm border rounded p-4 mb-4">
                    <h4 class="blog-sidebar-title mb-4">
                        Explore More
                        <span class="blog-sidebar-title-bar"></span>
                    </h4>

                    @foreach ($latestBlogs as $index => $latest)
                        @php
                            $extension = pathinfo($latest->blogImage, PATHINFO_EXTENSION);
                            $videoExtensions = ['mp4', 'webm', 'ogg'];
                        @endphp

                        <div class="recent-post-card d-flex align-items-center mb-4 pb-3 border-bottom-custom">
                            <div class="recent-post-media">
                                @if(in_array($extension, $videoExtensions))
                                    <video class="recent-post-img" muted>
                                        <source src="{{ asset($latest->blogImage) }}" type="video/{{ $extension }}">
                                    </video>
                                @else
                                    <img src="{{ Str::startsWith($latest->blogImage, ['http://','https://']) ? $latest->blogImage : '/' . $latest->blogImage }}"
                                         onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                         class="recent-post-img"
                                         onclick="openImage('{{ $latest->blogImage }}')" alt="{{ $latest->title }}">
                                @endif
                            </div>

                            <div class="recent-post-info">
                                <a href="{{ route('front.getBlogDetails', $latest->slug) }}"
                                   class="recent-post-title-link">
                                   {{ Str::limit($latest->title, 45) }}
                                </a>
                                <small class="recent-post-date">{{ date('M d, Y', strtotime($latest->created_at ?? now())) }}</small>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>


    </div>
</div>

<style>
    :root {
        --blog-primary-color: #400e2f;
        --blog-accent-color: #f59e0b;
        --blog-accent-hover: #d97706;

        /* Light Mode Defaults */
        --blog-bg-card: #ffffff;
        --blog-border: rgba(0, 0, 0, 0.08);
        --blog-text-main: #334155;
        --blog-text-title: #1e293b;
        --blog-text-muted: #64748b;
        --blog-link: #400e2f;
        --blog-link-hover: #f59e0b;
        --blog-sidebar-title-accent: #400e2f;
        --blog-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        --blog-card-hover-bg: #fafafa;
    }

    body.home-dark-mode {
        /* Dark Mode Overrides */
        --blog-bg-card: #1a1611;
        --blog-border: rgba(255, 255, 255, 0.08);
        --blog-text-main: #cbd5e1;
        --blog-text-title: #ffffff;
        --blog-text-muted: #94a3b8;
        --blog-link: #fbbf24;
        --blog-link-hover: #f59e0b;
        --blog-sidebar-title-accent: #fbbf24;
        --blog-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        --blog-card-hover-bg: #25201a;
    }

    /* General Typography & Details */
    .blog-main-content {
        font-family: var(--primary-font, 'Outfit', 'Poppins', sans-serif);
    }

    .blog-breadcrumb-link {
        color: var(--blog-link) !important;
        font-weight: 500;
        text-decoration: none !important;
        transition: color 0.3s ease;
    }

    .blog-breadcrumb-link:hover {
        color: var(--blog-link-hover) !important;
    }

    .text-muted-custom {
        color: var(--blog-text-muted) !important;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: var(--blog-text-muted);
    }

    .blog-main-title {
        font-family: 'Poppins', sans-serif;
        color: var(--blog-text-title) !important;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    @media (max-width: 768px) {
        .blog-main-title {
            font-size: 1.85rem;
        }
    }

    .blog-main-meta {
        color: var(--blog-text-muted) !important;
        font-size: 0.88rem;
        font-family: var(--primary-font, 'Outfit', sans-serif);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .blog-main-meta i {
        color: var(--blog-accent-color);
    }

    .blog-image-wrapper {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--blog-border) !important;
        box-shadow: var(--blog-shadow) !important;
        background: #000;
    }

    .blog-image-wrapper img {
        transition: transform 0.5s ease;
    }

    .blog-image-wrapper img:hover {
        transform: scale(1.02);
    }

    /* Rich Content Styles */
    .blog-content {
        line-height: 1.85;
        color: var(--blog-text-main) !important;
        font-family: var(--primary-font, 'Outfit', sans-serif);
        font-size: 1.08rem;
    }

    .blog-content p {
        margin-bottom: 1.5rem;
    }

    .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4 {
        font-family: 'Poppins', sans-serif;
        margin-top: 2.25rem;
        margin-bottom: 1rem;
        font-weight: 700;
        color: var(--blog-text-title) !important;
        line-height: 1.3;
    }

    .blog-content h1 { font-size: 2rem; }
    .blog-content h2 { font-size: 1.65rem; }
    .blog-content h3 { font-size: 1.4rem; }
    .blog-content h4 { font-size: 1.2rem; }

    .blog-content blockquote {
        border-left: 4px solid var(--blog-accent-color) !important;
        background: rgba(245, 158, 11, 0.05) !important;
        padding: 16px 24px;
        margin: 28px 0;
        border-radius: 4px 16px 16px 4px;
        font-style: italic;
        color: var(--blog-text-main);
    }

    body.home-dark-mode .blog-content blockquote {
        background: rgba(251, 191, 36, 0.04) !important;
    }

    .blog-content blockquote p {
        margin-bottom: 0;
    }

    .blog-content ul, .blog-content ol {
        margin-bottom: 1.5rem;
        padding-left: 24px;
    }

    .blog-content li {
        margin-bottom: 0.5rem;
    }

    /* Sidebar Styles */
    .blog-sidebar-card {
        background: var(--blog-bg-card) !important;
        border: 1px solid var(--blog-border) !important;
        box-shadow: var(--blog-shadow) !important;
        border-radius: 16px !important;
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .blog-sidebar-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: var(--blog-text-title) !important;
        position: relative;
        padding-bottom: 10px;
    }

    .blog-sidebar-title-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 45px;
        height: 3px;
        background-color: var(--blog-sidebar-title-accent) !important;
        border-radius: 2px;
    }

    .recent-post-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 8px;
    }

    .recent-post-card:hover {
        background-color: var(--blog-card-hover-bg) !important;
    }

    .border-bottom-custom {
        border-bottom: 1px solid var(--blog-border) !important;
    }

    .recent-post-card:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .recent-post-media {
        width: 70px;
        height: 70px;
        overflow: hidden;
        border-radius: 10px;
        flex-shrink: 0;
        margin-right: 14px;
        background: #000;
    }

    .recent-post-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .recent-post-card:hover .recent-post-img {
        transform: scale(1.08);
    }

    .recent-post-info {
        flex-grow: 1;
        overflow: hidden;
    }

    .recent-post-title-link {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: var(--blog-text-title) !important;
        display: block;
        margin-bottom: 4px;
        text-decoration: none !important;
        font-size: 0.92rem;
        line-height: 1.35;
        transition: color 0.2s ease;

        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 2.5rem;
    }

    .recent-post-title-link:hover {
        color: var(--blog-accent-hover) !important;
    }

    .recent-post-date {
        color: var(--blog-text-muted) !important;
        font-size: 0.78rem;
    }

    @media (max-width: 576px) {
        .blog-main-content {
            padding-right: 0 !important;
        }
    }
</style>

@endsection
