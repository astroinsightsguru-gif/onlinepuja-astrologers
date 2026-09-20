@extends('frontend.layout.master')
@section('content')
@php
    $currencySymbol = systemflag('currencySymbol') ?? '₹';
@endphp

<main role="main" class="margin-top-header">
    <!-- Breadcrumb Header -->
    <div class="pt-1 pb-1 bg-red d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-12 d-flex align-items-center">
                    <span style="text-transform: capitalize;">
                        <span class="text-white breadcrumbs">
                            <a href="/" class="text-white text-decoration-none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i>
                            <a href="{{ route('front.pujaList',$puja->category_id) }}" class="text-white text-decoration-none">Puja</a>
                            <i class="fa fa-chevron-right"></i>
                            Puja Details - {{$puja->puja_title}}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Detail Area -->
    <div class="product-detail py-4">
        <div class="container py-5 px-3">
            <!-- Product Card -->
            <div class="puja-detail-card p-4 d-flex flex-column flex-md-row gap-4">

                <!-- Left: Main Image -->
                <div class="col-md-6 d-flex flex-column align-items-center">
                    <div class="w-100 main-image-wrapper">
                        @php
                            $firstImage = !empty($puja->puja_images) ? $puja->puja_images[0] : 'build/assets/images/person.png';
                            $mainImageSrc = Str::startsWith($firstImage, ['http://','https://']) ? $firstImage : '/' . $firstImage;
                        @endphp
                        <img id="mainImage" class="w-100 h-100 object-fit-cover transition-all"
                             src="{{ $mainImageSrc }}"
                             onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                             alt="{{ $puja->puja_title }}" />
                    </div>

                    <!-- Thumbnails -->
                    @if(count($puja->puja_images) > 1)
                        <div class="d-flex gap-3 mt-3 overflow-auto w-100 justify-content-center py-2">
                            @foreach ($puja->puja_images as $index => $image)
                                <img class="thumb-img"
                                     src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}"
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                     alt="Puja image thumbnail"
                                     onclick="changeImage(this)" />
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right: Product Details -->
                <div class="col-md-6 d-flex flex-column justify-content-between">
                    <div>
                        <h2 class="puja-category-title fs-5 text-uppercase mb-2">{{ $puja->category->name }}</h2>
                        <h1 class="puja-main-title h3 fw-bold mb-2">{{ $puja->puja_title }}</h1>
                        <h5 class="puja-sub-title mb-3">{{ $puja->puja_subtitle }}</h5>

                        <div class="puja-place-text d-flex align-items-center gap-2 mb-4">
                            <i class="fa-solid fa-place-of-worship text-warning"></i>
                            <strong>{{ $puja->puja_place }}</strong>
                        </div>

                        @php
                            $startDatetime = \Carbon\Carbon::parse($puja->puja_start_datetime);
                            $endDatetime = \Carbon\Carbon::parse($puja->puja_end_datetime);

                            $startDateDisplay = $startDatetime->format('j M, D');
                            $endDateDisplay = $endDatetime->format('j M, D');
                            $startTimeDisplay = $startDatetime->format('H:i');
                            $endTimeDisplay = $endDatetime->format('H:i');
                            $sameDate = $startDatetime->isSameDay($endDatetime);

                            $now = \Carbon\Carbon::now();
                            $isFutureEvent = $now->lt($startDatetime);
                        @endphp

                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-date py-2 px-3 fs-6">
                                <i class="fa-regular fa-calendar-days me-2"></i>
                                @if($sameDate)
                                    {{ $startDateDisplay }} {{ $startTimeDisplay }} - {{ $endTimeDisplay }}
                                @else
                                    {{ $startDateDisplay }} {{ $startTimeDisplay }} to {{ $endDateDisplay }} {{ $endTimeDisplay }}
                                @endif
                            </span>
                        </div>

                        @if($isFutureEvent)
                            <div class="countdown-timer mt-2"
                                 data-start-datetime="{{ $startDatetime->toIso8601String() }}">
                                <div class="badge-countdown d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3">
                                    <i class="fa-regular fa-clock text-warning"></i>
                                    <span class="fw-semibold">Puja starts in:</span>
                                    <span class="fw-bold">
                                        <span class="days">0</span>d
                                        <span class="hours">0</span>h
                                        <span class="minutes">0</span>m
                                        <span class="seconds">0</span>s
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="mt-3">
                                <div class="status-ongoing-capsule d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 text-success">
                                    <span class="status-dot-blink bg-success"></span>
                                    <span class="fw-bold">Puja is ongoing</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <a href="#packages" id="selectPackageBtn" class="btn btn-select-package w-100 mt-4 fw-semibold d-flex justify-content-center align-items-center">
                        Select Puja Package <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Custom Styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fonts and General Premium Aesthetics */
        .product-detail {
          background-color: #f8fafc;
          transition: background-color 0.3s ease;
          font-family: 'Outfit', sans-serif;
        }

        .puja-detail-card {
          background: #ffffff;
          border-radius: 20px;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
          border: 1px solid rgba(0, 0, 0, 0.05);
          transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .main-image-wrapper {
          border: 1px solid #e2e8f0;
          border-radius: 16px;
          overflow: hidden;
          height: 400px;
          background: #f8fafc;
          transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        .thumb-img {
          border: 2px solid #e2e8f0;
          border-radius: 12px;
          width: 80px;
          height: 80px;
          object-fit: cover;
          cursor: pointer;
          transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .thumb-img:hover {
          transform: translateY(-2px);
          border-color: #ff6600;
        }

        /* Product Details Text */
        .puja-category-title {
          color: #ff6600;
          font-weight: 700;
          letter-spacing: 1px;
          font-size: 0.85rem !important;
          font-family: 'Outfit', sans-serif;
        }

        .puja-main-title {
          color: #1e293b;
          font-family: 'Poppins', sans-serif;
          font-weight: 800;
          font-size: 1.8rem;
        }

        .puja-sub-title {
          color: #475569;
          font-size: 1.05rem;
          font-weight: 500;
          line-height: 1.5;
        }

        .puja-place-text {
          color: #4b5563;
          font-size: 0.95rem;
        }

        .puja-place-text i {
          font-size: 1.1rem;
        }

        /* Badges & Counters */
        .badge-date {
          background-color: rgba(255, 102, 0, 0.08);
          color: #ff6600;
          font-weight: 600;
          border: 1px solid rgba(255, 102, 0, 0.2);
          border-radius: 8px;
        }

        .badge-countdown {
          background-color: #fffbeb;
          color: #b45309;
          border: 1px solid rgba(252, 211, 77, 0.4);
          display: inline-flex;
          font-size: 0.92rem;
        }

        .status-ongoing-capsule {
          background-color: rgba(25, 135, 84, 0.08);
          border: 1px solid rgba(25, 135, 84, 0.2);
          display: inline-flex;
          font-size: 0.92rem;
        }

        .status-dot-blink {
          width: 10px;
          height: 10px;
          border-radius: 50%;
          display: inline-block;
          animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
          50% { opacity: 0; }
        }

        .btn-select-package {
          border: 2px solid #ff6600 !important;
          color: #ff6600 !important;
          background: transparent;
          padding: 14px 28px;
          border-radius: 50px;
          font-weight: 700;
          font-size: 0.95rem;
          transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .btn-select-package:hover {
          background: #ff6600 !important;
          color: #ffffff !important;
          box-shadow: 0 8px 20px rgba(255, 102, 0, 0.25);
          transform: translateY(-2px);
        }

        /* ====== NAVIGATION TABS ====== */
        .product-info {
          border-top: 1px solid #e2e8f0;
          border-bottom: 1px solid #e2e8f0;
          background: #ffffff;
          transition: all 0.3s ease;
        }

        .product-info .nav-link {
          font-weight: 600;
          color: #475569;
          font-size: 15px;
          padding: 10px 24px;
          transition: all 0.3s ease;
          border-radius: 50px;
          cursor: pointer;
          margin: 4px;
        }

        .product-info .nav-link:hover {
          color: #ff6600;
          background: rgba(255, 102, 0, 0.05);
        }

        .product-info .nav-link.active {
          color: #ffffff !important;
          background-color: #ff6600 !important;
          box-shadow: 0 4px 12px rgba(255, 102, 0, 0.2);
        }

        /* ====== TAB CONTENT ====== */
        .section {
          display: none;
          animation: fadeIn 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .section.active {
          display: block;
        }

        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(12px); }
          to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
          color: #ff6600;
          font-family: 'Poppins', sans-serif;
          font-weight: 700;
          font-size: 1.5rem;
          margin-bottom: 20px;
          position: relative;
          display: inline-block;
        }

        .section-title::after {
          content: '';
          position: absolute;
          bottom: -6px;
          left: 0;
          width: 40px;
          height: 3px;
          background: #ff6600;
          border-radius: 2px;
        }

        .section-text {
          color: #334155;
          font-size: 1.05rem;
          line-height: 1.7;
        }

        /* ====== BENEFITS & PROCESS ====== */
        .benefit-card, .process-card {
          background: #ffffff;
          border: 1px solid #e2e8f0;
          border-radius: 16px;
          height: 100%;
          transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .benefit-card:hover, .process-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
          border-color: rgba(255, 102, 0, 0.2);
        }

        .benefit-icon-wrapper {
          background-color: rgba(255, 102, 0, 0.06);
          border-radius: 50%;
          height: 52px;
          width: 52px;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
        }

        .benefit-title, .process-title {
          color: #1e293b;
          font-family: 'Poppins', sans-serif;
          font-weight: 600;
          font-size: 1rem;
        }

        .benefit-desc, .process-desc {
          color: #64748b;
          font-size: 0.9rem;
          line-height: 1.5;
        }

        .process-card {
          position: relative;
          overflow: hidden;
        }

        .process-step {
          background: #ff6600;
          color: #ffffff;
          width: 32px;
          height: 32px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 700;
          font-size: 0.95rem;
          box-shadow: 0 3px 8px rgba(255, 102, 0, 0.25);
        }

        /* ====== PACKAGE CARDS ====== */
        .package-card {
          background: #ffffff;
          border: 1px solid #fcd34d;
          border-radius: 20px;
          padding: 28px;
          display: flex;
          flex-direction: column;
          box-shadow: 0 8px 30px rgba(251, 191, 36, 0.03);
          transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
          position: relative;
          height: 100%;
        }

        .package-card:hover {
          transform: translateY(-6px);
          box-shadow: 0 16px 35px rgba(251, 191, 36, 0.12);
          border-color: #f59e0b;
        }

        .package-title {
          color: #ff6600;
          font-family: 'Poppins', sans-serif;
          font-weight: 700;
          font-size: 1.35rem;
          margin-bottom: 4px;
        }

        .package-audience {
          color: #64748b;
          font-size: 0.85rem;
          font-weight: 600;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .package-price-box {
          background: rgba(255, 102, 0, 0.03);
          border-radius: 14px;
          border: 1px dashed rgba(255, 102, 0, 0.12);
        }

        .price-value {
          font-size: 2rem;
          font-weight: 800;
          color: #dc2626;
          font-family: 'Poppins', sans-serif;
          display: inline-flex;
          align-items: center;
          justify-content: center;
        }

        .package-features ul li {
          font-size: 0.92rem;
          color: #334155;
          margin-bottom: 12px;
          line-height: 1.45;
          display: flex;
          align-items: start;
        }

        .package-features ul li i {
          margin-top: 3px;
          font-size: 0.95rem;
        }

        .btn-participate {
          background: #ff6600;
          color: #ffffff;
          font-weight: 700;
          border-radius: 50px;
          padding: 12px 24px;
          border: none;
          transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
          box-shadow: 0 6px 18px rgba(255, 102, 0, 0.2);
        }

        .btn-participate:hover {
          background: #e05500;
          color: #ffffff;
          box-shadow: 0 8px 24px rgba(255, 102, 0, 0.35);
          transform: translateY(-2px);
        }

        /* ====== ACCORDION (FAQ) ====== */
        .accordion-item {
          border: 1px solid #e2e8f0;
          border-radius: 14px !important;
          margin-bottom: 14px;
          overflow: hidden;
          background: #ffffff;
          box-shadow: 0 4px 15px rgba(0,0,0,0.01);
          transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .accordion-item:hover {
          border-color: rgba(255, 102, 0, 0.2);
        }

        .accordion-button {
          font-family: 'Poppins', sans-serif;
          font-weight: 600;
          font-size: 1rem;
          color: #1e293b;
          background-color: #ffffff;
          padding: 20px 24px;
          border: none !important;
          box-shadow: none !important;
          transition: all 0.3s ease;
        }

        .accordion-button:not(.collapsed) {
          color: #ff6600;
          background-color: rgba(255, 102, 0, 0.02);
        }

        .accordion-body {
          color: #475569;
          font-size: 0.95rem;
          line-height: 1.6;
          padding: 20px 24px;
          background: #f8fafc;
          border-top: 1px solid #e2e8f0;
        }

        /* ==========================================
           DARK MODE OVERRIDES (body.home-dark-mode)
           ========================================== */
        body.home-dark-mode .product-detail {
          background-color: #120f0b;
        }

        body.home-dark-mode .puja-detail-card {
          background: #1a1611;
          border-color: rgba(255, 255, 255, 0.08);
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        body.home-dark-mode .main-image-wrapper {
          border-color: rgba(255, 255, 255, 0.08);
          background: #15120e;
        }

        body.home-dark-mode .thumb-img {
          border-color: rgba(255, 255, 255, 0.08);
        }

        body.home-dark-mode .thumb-img:hover {
          border-color: #f1e135;
        }

        body.home-dark-mode .puja-category-title {
          color: #f1e135;
        }

        body.home-dark-mode .puja-main-title {
          color: #ffffff;
        }

        body.home-dark-mode .puja-sub-title {
          color: #94a3b8;
        }

        body.home-dark-mode .puja-place-text {
          color: #cbd5e1;
        }

        body.home-dark-mode .badge-date {
          background-color: rgba(241, 225, 53, 0.08);
          color: #f1e135;
          border-color: rgba(241, 225, 53, 0.2);
        }

        body.home-dark-mode .badge-countdown {
          background-color: #1c1812;
          color: #f1e135;
          border-color: rgba(241, 225, 53, 0.25);
        }

        body.home-dark-mode .status-ongoing-capsule {
          background-color: rgba(25, 135, 84, 0.12);
          border-color: rgba(25, 135, 84, 0.3);
        }

        body.home-dark-mode .btn-select-package {
          border-color: #f1e135 !important;
          color: #f1e135 !important;
        }

        body.home-dark-mode .btn-select-package:hover {
          background: #f1e135 !important;
          color: #0c0a08 !important;
          box-shadow: 0 8px 20px rgba(241, 225, 53, 0.35);
        }

        body.home-dark-mode .product-info {
          border-top-color: rgba(255, 255, 255, 0.08);
          border-bottom-color: rgba(255, 255, 255, 0.08);
          background: #1a1611;
        }

        body.home-dark-mode .product-info .nav-link {
          color: #cbd5e1;
        }

        body.home-dark-mode .product-info .nav-link:hover {
          color: #f1e135;
          background: rgba(241, 225, 53, 0.05);
        }

        body.home-dark-mode .product-info .nav-link.active {
          color: #0c0a08 !important;
          background-color: #f1e135 !important;
          box-shadow: 0 4px 12px rgba(241, 225, 53, 0.35);
        }

        body.home-dark-mode .section-title {
          color: #f1e135;
        }

        body.home-dark-mode .section-title::after {
          background: #f1e135;
        }

        body.home-dark-mode .section-text {
          color: #cbd5e1;
        }

        body.home-dark-mode .benefit-card,
        body.home-dark-mode .process-card {
          background: #1a1611;
          border-color: rgba(255, 255, 255, 0.08);
        }

        body.home-dark-mode .benefit-card:hover,
        body.home-dark-mode .process-card:hover {
          border-color: rgba(241, 225, 53, 0.25);
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        body.home-dark-mode .benefit-icon-wrapper {
          background-color: rgba(241, 225, 53, 0.08);
        }

        body.home-dark-mode .benefit-title,
        body.home-dark-mode .process-title {
          color: #ffffff;
        }

        body.home-dark-mode .benefit-desc,
        body.home-dark-mode .process-desc {
          color: #94a3b8;
        }

        body.home-dark-mode .process-step {
          background: #f1e135;
          color: #0c0a08;
          box-shadow: 0 3px 8px rgba(241, 225, 53, 0.35);
        }

        body.home-dark-mode .package-card {
          background: #1a1611;
          border-color: rgba(241, 225, 53, 0.3);
          box-shadow: 0 8px 30px rgba(0,0,0,0.4);
        }

        body.home-dark-mode .package-card:hover {
          border-color: #f1e135;
          box-shadow: 0 16px 35px rgba(241, 225, 53, 0.15);
        }

        body.home-dark-mode .package-title {
          color: #f1e135;
        }

        body.home-dark-mode .package-audience {
          color: #94a3b8;
        }

        body.home-dark-mode .package-price-box {
          background: rgba(241, 225, 53, 0.04);
          border-color: rgba(241, 225, 53, 0.15);
        }

        body.home-dark-mode .price-value {
          color: #f1e135;
        }

        body.home-dark-mode .package-features ul li {
          color: #cbd5e1;
        }

        body.home-dark-mode .btn-participate {
          background: #f1e135;
          color: #0c0a08;
          box-shadow: 0 6px 18px rgba(241, 225, 53, 0.2);
        }

        body.home-dark-mode .btn-participate:hover {
          background: #d8c92a;
          color: #0c0a08;
          box-shadow: 0 8px 24px rgba(241, 225, 53, 0.35);
        }

        body.home-dark-mode .accordion-item {
          background: #1a1611;
          border-color: rgba(255, 255, 255, 0.08);
        }

        body.home-dark-mode .accordion-item:hover {
          border-color: rgba(241, 225, 53, 0.25);
        }

        body.home-dark-mode .accordion-button {
          background-color: #1a1611;
          color: #ffffff;
        }

        body.home-dark-mode .accordion-button:not(.collapsed) {
          color: #f1e135;
          background-color: rgba(241, 225, 53, 0.04);
        }

        body.home-dark-mode .accordion-body {
          color: #cbd5e1;
          background: #15120e;
          border-top-color: rgba(255, 255, 255, 0.08);
        }

        body.home-dark-mode .accordion-button::after {
          filter: invert(1) brightness(2);
        }
    </style>

    <!-- Tabbed Information Container -->
    <div class="container py-4">

        <!-- ===== TAB MENU ===== -->
        <div class="py-2 product-info justify-content-center d-flex w-100 mt-3 rounded-3">
            <ul class="nav flex-wrap justify-content-center" id="pujaTabs">
                <li class="nav-item px-2">
                    <a class="nav-link active" data-target="about">About Puja</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" data-target="benefits">Benefits</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" data-target="process">Process</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" data-target="packages">Packages</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" data-target="faqs">FAQs</a>
                </li>
            </ul>
        </div>

        <!-- ===== TAB CONTENT ===== -->
        <div class="tab-content mt-4">

            <!-- About -->
            <div id="about" class="section active">
                <h2 class="section-title">About Puja</h2>
                <p class="section-text text-justify mt-3">
                    {{ $puja->long_description ?? 'Detailed description about the Puja will appear here.' }}
                </p>
            </div>

            <!-- Benefits -->
            <div id="benefits" class="section">
                <h2 class="section-title">Puja Benefits</h2>
                <div class="row mt-4">
                    @foreach ($puja->puja_benefits as $benefit)
                        <div class="col-md-4 mb-3">
                            <div class="benefit-card d-flex p-3 align-items-start">
                                <div class="benefit-icon-wrapper me-3">
                                    <i class="fa fa-star text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="benefit-title fw-bold mb-1">{{ $benefit['title'] }}</h6>
                                    <p class="benefit-desc mb-0">{{ $benefit['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Process -->
            <div id="process" class="section">
                <h2 class="section-title">Puja Process</h2>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="process-card p-4">
                            <div class="process-step">1</div>
                            <h5 class="process-title fw-bold mt-3">Select Puja</h5>
                            <p class="process-desc text-muted mb-0">Choose from the puja packages listed below.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="process-card p-4">
                            <div class="process-step">2</div>
                            <h5 class="process-title fw-bold mt-3">Add Offerings</h5>
                            <p class="process-desc text-muted mb-0">Enhance your experience with optional offerings like Deep Daan or Anna Daan.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="process-card p-4">
                            <div class="process-step">3</div>
                            <h5 class="process-title fw-bold mt-3">Sankalp Details</h5>
                            <p class="process-desc text-muted mb-0">Provide Name and Gotra for Sankalp.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Packages -->
            <div id="packages" class="section">
                <h2 class="section-title">Select Puja Package</h2>
                <div class="row mt-4">
                    @foreach ($package as $packageDetail)
                        <div class="col-md-4 mb-4">
                            <div class="package-card">
                                <div class="package-header text-center">
                                    <h3 class="package-title">{{ $packageDetail['title'] }}</h3>
                                    <span class="package-audience">For {{ $packageDetail['person'] }} Person</span>
                                </div>

                                <div class="package-price-box text-center py-3 my-3">
                                    <span class="price-value">
                                        @if(systemflag('walletType') == 'Coin')
                                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="18" class="me-1">
                                        @else
                                            <span class="currency-symbol me-1">{{ $currencySymbol ?? '₹' }}</span>
                                        @endif
                                        {{ $packageDetail['package_price'] }}
                                    </span>
                                </div>

                                <div class="package-features my-2">
                                    <ul class="text-start list-unstyled">
                                        @foreach ($packageDetail['description'] as $point)
                                            <li>
                                                <i class="fa-solid fa-circle-check text-success me-2 mt-1"></i>
                                                <span>{{ $point }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="package-footer text-center mt-auto pt-3">
                                    <a @if(!authcheck()) data-toggle="modal" data-target="#loginSignUp" @else href="{{route('front.pujaAstrologerList',['slug'=>$puja->slug,'package_id'=>$packageDetail['id']])}}" @endif class="btn btn-participate w-100">Participate Now</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- FAQs -->
            <div id="faqs" class="section">
                <h2 class="section-title">FAQs</h2>
                <div class="accordion mt-4" id="faqAccordion">
                    @foreach ($FAQ as $index => $faqItem)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false">
                                    {{ $faqItem->title }}
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {{ $faqItem->description }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div><!-- End tab-content -->
    </div><!-- End container -->

    <!-- ===== JS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      function changeImage(el) {
          document.getElementById('mainImage').src = el.src;
      }

      // Handle tab switching
      const tabs = document.querySelectorAll('#pujaTabs .nav-link');
      const sections = document.querySelectorAll('.section');

      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          // Remove active class from tabs
          tabs.forEach(t => t.classList.remove('active'));
          this.classList.add('active');

          // Hide all sections
          sections.forEach(s => s.classList.remove('active'));

          // Show selected section
          const targetId = this.getAttribute('data-target');
          document.getElementById(targetId).classList.add('active');

          // Scroll to section smoothly
          window.scrollTo({ top: document.querySelector('.product-info').offsetTop - 100, behavior: 'smooth' });
        });
      });

      // Handle Select Puja Package button click
      const selectPackageBtn = document.getElementById('selectPackageBtn');
      if (selectPackageBtn) {
        selectPackageBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const packageTab = document.querySelector('#pujaTabs .nav-link[data-target="packages"]');
          if (packageTab) {
            packageTab.click();
          }
        });
      }
    </script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const readMoreElements = document.querySelectorAll('.read-more');

        readMoreElements.forEach(element => {
            element.addEventListener('click', function () {
                const fullDescription = this.nextElementSibling;
                const readLess = this.nextElementSibling.nextElementSibling;
                fullDescription.style.display = 'inline';
                this.style.display = 'none';
                readLess.style.display = 'inline';
            });
        });

        const readLessElements = document.querySelectorAll('.read-less');

        readLessElements.forEach(element => {
            element.addEventListener('click', function () {
                const fullDescription = this.previousElementSibling;
                const readMore = this.previousElementSibling.previousElementSibling;
                fullDescription.style.display = 'none';
                this.style.display = 'none';
                readMore.style.display = 'inline';
            });
        });
    });

    $(document).ready(function () {
        $('.owl-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: false,
            dots: true,
            center: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    });

    document.querySelectorAll('.scrollable-container').forEach(container => {
        container.addEventListener('wheel', (event) => {
            const atTop = container.scrollTop === 0;
            const atBottom = container.scrollHeight - container.scrollTop === container.clientHeight;

            if ((event.deltaY < 0 && !atTop) || (event.deltaY > 0 && !atBottom)) {
                event.preventDefault();
                container.scrollBy({
                    top: event.deltaY,
                    behavior: 'auto'
                });
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElements = document.querySelectorAll('.countdown-timer');

        function updateCountdown() {
            countdownElements.forEach(element => {
                const startDatetime = new Date(element.dataset.startDatetime);
                const now = new Date();
                const diff = startDatetime - now;

                if (diff <= 0) {
                    element.innerHTML = '<div class="status-ongoing-capsule d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 text-success"><span class="status-dot-blink bg-success"></span><span class="fw-bold">Puja is ongoing</span></div>';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                const daysEl = element.querySelector('.days');
                const hoursEl = element.querySelector('.hours');
                const minutesEl = element.querySelector('.minutes');
                const secondsEl = element.querySelector('.seconds');

                if(daysEl) daysEl.textContent = days;
                if(hoursEl) hoursEl.textContent = hours;
                if(minutesEl) minutesEl.textContent = minutes;
                if(secondsEl) secondsEl.textContent = seconds;
            });
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>

@endsection
