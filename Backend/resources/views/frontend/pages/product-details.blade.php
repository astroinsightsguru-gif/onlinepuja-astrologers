@extends('frontend.layout.master')
@section('content')

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
                        <a href="{{ route('front.getproducts') }}" class="text-white text-decoration-none">Products</a>
                        <i class="fa fa-chevron-right"></i>
                        Product Details - {{ $getproductdetails->name }}
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Product Detail Main Card -->
    <div class="product-detail-card mb-5">
        <div class="row align-items-center g-4">
            <!-- Product Image Section -->
            <div class="col-md-6">
                <div class="product-details-img shadow-sm">
                    <img src="{{ Str::startsWith($getproductdetails->productImage, ['http://','https://']) ? $getproductdetails->productImage : '/' . $getproductdetails->productImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $getproductdetails->name }}" onclick="openImage('{{ $getproductdetails->productImage }}')" />
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="col-md-6 d-flex align-items-center p-3">
                <div class="w-100">
                    <div class="mb-3">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-2 px-3 rounded-pill text-uppercase fw-bold mb-3 fs-7" style="letter-spacing: 0.5px;">
                            Category: {{ $getproductdetails->productCategory }}
                        </span>
                        <h1 class="fw-bold product-main-title mt-2">{{ $getproductdetails->name }}</h1>
                    </div>
                    <div class="mb-4">
                        <h2 class="detail-price">
                            @if(strtolower($walletType) == 'coin')
                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="22" class="me-1">
                            @else
                                {{ $currency['value'] }}
                            @endif
                            {{ $getproductdetails->amount }}
                        </h2>
                    </div>
                    <a @if(authcheck()) href="{{ route('front.checkout', ['id' => $getproductdetails->id]) }}" @else data-toggle="modal"  data-target="#loginSignUp" @endif class="btn-buy-now-detail text-center w-100">
                        <i class="fa fa-shopping-cart me-2"></i> Buy Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="row pt-2">
        <div class="col">
            <h3 class="detail-section-title">Product Details</h3>
            <p class="detail-section-text">{{ $getproductdetails->features }}</p>
        </div>
    </div>

    <!-- Product FAQs -->
    @if(count($productfaq)>0)
    <div id="faqs" class="section mt-5">
        <h3 class="detail-section-title">Frequently Asked Questions</h3>
        <div class="accordion mt-3" id="faqAccordion">
            @foreach ($productfaq as $index => $faqItem)
                <div class="accordion-item-custom">
                    <h3 class="mb-0">
                        <a href="#" class="accordion-button-custom collapsed" data-toggle="collapse"
                           data-target="#faq{{ $index + 1 }}" aria-expanded="false"
                           aria-controls="faq{{ $index + 1 }}">
                            {{ $faqItem->question }}
                            <i class="fas fa-chevron-down font-12"></i>
                        </a>
                    </h3>

                    <div id="faq{{ $index + 1 }}" class="collapse" data-parent="#faqAccordion">
                        <div class="accordion-body-custom">
                            {{ $faqItem->answer }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Products Section -->
    <div class="container my-5 px-0">
        <div class="text-center mb-5">
            <h2 class="position-relative d-inline-block pb-3 fw-bold text-dark recent-section-title">
                Recent Products
                <span class="position-absolute bottom-0 start-50 translate-middle-x bg-warning d-block rounded"
                      style="width: 80px; height: 3px;">
                </span>
            </h2>
            <p class="mt-3 text-muted-custom">
                See new products and how {{ ucfirst($appname) }} helped them find their path to happiness!
            </p>
        </div>

        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-2">
            @if (count($productlist) > 0)
                @foreach ($productlist as $key => $products)
                    <div class="col" data-aos="fade-up">
                        <div class="product-card d-flex flex-column overflow-hidden transition-all">
                            <a href="{{ route('front.getproductDetails', ['slug' => $products->slug]) }}" class="text-decoration-none">
                                <div class="product-image-wrapper position-relative overflow-hidden">
                                    <img class="product-image w-100 h-100"
                                        src="{{ Str::startsWith($products->productImage, ['http://','https://']) ? $products->productImage : '/' . $products->productImage }}"
                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                        alt="{{ $products->name }}" />
                                </div>
                            </a>
                            <div class="d-flex flex-column justify-content-between flex-grow-1 p-3">
                                <!-- Product Name -->
                                <h5 class="product-title mb-3">{{ $products->name }}</h5>

                                <!-- Price and Buy Button Row -->
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="product-price fw-bold fs-6 mb-0">
                                        @if(strtolower($walletType) == 'coin')
                                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                        @else
                                            {{ $currency['value'] }}
                                        @endif
                                        {{ $products->amount }}
                                    </span>
                                    <a href="{{ route('front.getproductDetails', ['slug' => $products->slug]) }}" class="btn-buy-now">
                                        Buy Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5 w-100">
                    <h5 class="text-muted">No products found.</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    :root {
        --astro-accent: #f59e0b;
        --astro-accent-hover: #d97706;
        --astro-border-light: rgba(0, 0, 0, 0.06);
        --astro-card-bg-light: #ffffff;
        --astro-text-dark: #1e293b;
        --astro-text-muted-light: #64748b;
        --astro-img-bg-light: #f8fafc;
    }

    /* Product Detail Card */
    .product-detail-card {
        background: var(--astro-card-bg-light);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--astro-border-light);
        padding: 28px;
        transition: all 0.3s ease;
    }

    body.home-dark-mode .product-detail-card {
        background: #1a1611 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .product-details-img {
        border: 1px solid var(--astro-border-light);
        border-radius: 16px;
        overflow: hidden;
        height: 400px;
        background: var(--astro-img-bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    body.home-dark-mode .product-details-img {
        border-color: rgba(255, 255, 255, 0.08) !important;
        background: #15120e !important;
    }

    .product-details-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        cursor: pointer;
        transition: transform 0.5s ease;
        padding: 15px;
    }

    .product-details-img img:hover {
        transform: scale(1.03);
    }

    .product-main-title {
        color: var(--astro-text-dark);
        font-family: 'Poppins', sans-serif;
    }

    body.home-dark-mode .product-main-title {
        color: #ffffff !important;
    }

    .detail-price {
        color: #dc2626 !important;
        font-weight: 800;
        font-family: 'Poppins', sans-serif;
        display: flex;
        align-items: center;
    }

    body.home-dark-mode .detail-price {
        color: #f87171 !important;
    }

    /* Buy Now Button - Main CTA */
    .btn-buy-now-detail {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 1.05rem !important;
        border-radius: 50px !important;
        padding: 14px 28px !important;
        border: none !important;
        box-shadow: 0 6px 18px rgba(245, 158, 11, 0.25) !important;
        transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
    }

    .btn-buy-now-detail:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.45) !important;
    }

    body.home-dark-mode .btn-buy-now-detail {
        background: linear-gradient(135deg, #fbbf24, #f1e135) !important;
        color: #0c0a08 !important;
        box-shadow: 0 6px 18px rgba(251, 191, 36, 0.35) !important;
    }

    body.home-dark-mode .btn-buy-now-detail:hover {
        box-shadow: 0 10px 25px rgba(251, 191, 36, 0.5) !important;
    }

    /* Detail Sections styling */
    .detail-section-title {
        color: var(--astro-text-dark) !important;
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        position: relative;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .detail-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 45px;
        height: 3px;
        background: var(--astro-accent, #f59e0b);
        border-radius: 2px;
    }

    body.home-dark-mode .detail-section-title {
        color: #ffffff !important;
    }

    .detail-section-text {
        color: #475569 !important;
        font-family: 'Outfit', sans-serif;
        font-size: 1.05rem;
        line-height: 1.75;
    }

    body.home-dark-mode .detail-section-text {
        color: #cbd5e1 !important;
    }

    /* Accordion - FAQs */
    .accordion-item-custom {
        border: 1px solid var(--astro-border-light);
        border-radius: 14px !important;
        margin-bottom: 14px;
        overflow: hidden;
        background: var(--astro-card-bg-light);
        box-shadow: 0 4px 15px rgba(0,0,0,0.005);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    body.home-dark-mode .accordion-item-custom {
        background: #1a1611 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    .accordion-item-custom:hover {
        border-color: rgba(245, 158, 11, 0.25);
    }

    .accordion-button-custom {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        color: var(--astro-text-dark);
        background-color: var(--astro-card-bg-light);
        padding: 20px 24px;
        border: none !important;
        box-shadow: none !important;
        transition: all 0.3s ease;
        text-align: left;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none !important;
    }

    .accordion-button-custom i {
        transition: transform 0.3s ease;
    }

    .accordion-button-custom:not(.collapsed) i {
        transform: rotate(180deg);
    }

    .accordion-button-custom:hover {
        color: var(--astro-accent-hover);
    }

    body.home-dark-mode .accordion-button-custom {
        background-color: #1a1611 !important;
        color: #ffffff !important;
    }

    body.home-dark-mode .accordion-button-custom:hover {
        color: #fbbf24 !important;
    }

    .accordion-button-custom:not(.collapsed) {
        color: var(--astro-accent-hover);
        background-color: rgba(245, 158, 11, 0.02);
    }

    body.home-dark-mode .accordion-button-custom:not(.collapsed) {
        color: #fbbf24 !important;
        background-color: rgba(251, 191, 36, 0.04) !important;
    }

    .accordion-body-custom {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.6;
        padding: 20px 24px;
        background: #f8fafc;
        border-top: 1px solid var(--astro-border-light);
    }

    body.home-dark-mode .accordion-body-custom {
        color: #cbd5e1;
        background: #15120e;
        border-top-color: rgba(255, 255, 255, 0.08);
    }

    /* Recent Section Title */
    .recent-section-title {
        color: var(--astro-text-dark) !important;
        font-family: 'Poppins', sans-serif;
    }

    body.home-dark-mode .recent-section-title {
        color: #ffffff !important;
    }

    .text-muted-custom {
        color: var(--astro-text-muted-light) !important;
        font-family: 'Outfit', sans-serif;
    }

    body.home-dark-mode .text-muted-custom {
        color: #94a3b8 !important;
    }

    /* Product Grid Cards */
    .product-card {
        background: var(--astro-card-bg-light);
        border: 1px solid var(--astro-border-light) !important;
        border-radius: 16px;
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.015) !important;
    }

    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(245, 158, 11, 0.08) !important;
        border-color: rgba(245, 158, 11, 0.25) !important;
    }

    body.home-dark-mode .product-card {
        background: #1a1611 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
    }

    body.home-dark-mode .product-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4) !important;
        border-color: rgba(251, 191, 36, 0.25) !important;
    }

    .product-image-wrapper {
        width: 100%;
        height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--astro-img-bg-light);
        transition: background-color 0.3s ease;
    }

    body.home-dark-mode .product-image-wrapper {
        background-color: #15120e !important;
    }

    .product-image {
        object-fit: cover;
        width: 100%;
        height: 100%;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.04);
    }

    .product-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--astro-text-dark);
        margin: 0;
        padding: 0;
        text-align: left;
        line-height: 1.4;
        transition: color 0.3s ease;

        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8rem;
    }

    body.home-dark-mode .product-title {
        color: #ffffff !important;
    }

    .product-price {
        color: var(--astro-text-dark);
        font-family: 'Outfit', sans-serif;
    }

    body.home-dark-mode .product-price {
        color: #ffffff !important;
    }

    .btn-buy-now {
        border: 2px solid var(--astro-accent, #f59e0b) !important;
        color: var(--astro-accent, #f59e0b) !important;
        background: transparent;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 6px 16px;
        border-radius: 50px;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        text-decoration: none !important;
        display: inline-block;
    }

    .btn-buy-now:hover {
        background: var(--astro-accent, #f59e0b) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        transform: translateY(-1px);
    }

    body.home-dark-mode .btn-buy-now {
        border-color: #fbbf24 !important;
        color: #fbbf24 !important;
    }

    body.home-dark-mode .btn-buy-now:hover {
        background: #fbbf24 !important;
        color: #0c0a08 !important;
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.35);
    }

    @media (max-width: 576px) {
        .product-image-wrapper {
            height: 180px;
        }
        .product-title {
            font-size: 0.95rem;
            height: 2.6rem;
        }
        .product-details-img {
            height: 300px;
        }
    }
</style>

@endsection
