@extends('frontend.layout.master')
@section('content')

<div class="max-w-7xl mx-auto pb-5">
  <div class="container my-5">

    <!-- Section Title -->
    <div class="text-center mb-5">
      <h2 class="position-relative d-inline-block pb-3 fw-bold text-dark product-section-title">
        Astrologer Products
        <span class="position-absolute bottom-0 start-50 translate-middle-x bg-warning d-block rounded"
              style="width: 80px; height: 3px;">
        </span>
      </h2>
      <p class="mt-3 text-muted-custom">
        See new products and how {{ ucfirst($appname) }} helped them find their path to happiness!
      </p>
    </div>

    <!-- Category Filter -->
    <div class="col-ms-12 col-md-3 d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2 ml-auto mb-3" id="filterproductCategory">
      <select name="productCategoryId" onchange="onFilterProductCategoryList()" class="form-control font13 rounded shadow-sm border-0 product-select-custom" id="psychicCategories">
        <option value="0" {{ $productCategoryId == '0' ? 'selected' : '' }}>Select Category</option>
        @foreach ($getproductCategory['recordList'] as $category)
          <option value="{{ $category['id'] }}" {{ $productCategoryId == $category['id'] ? 'selected' : '' }}>
            {{ $category['name'] }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- Products Grid -->
    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-2">
      @if (count($productlist) > 0)
        @foreach ($productlist as $key => $products)
          <div class="col" data-aos="fade-up">
            <div class="product-card d-flex flex-column overflow-hidden transition-all">
              <a href="{{ route('front.getproductDetails', ['slug' => $products->slug]) }}" class="text-decoration-none">
                <div class="product-image-wrapper position-relative overflow-hidden">
                  <img
                    class="product-image w-100 h-100"
                    src="{{ Str::startsWith($products->productImage, ['http://','https://']) ? $products->productImage : '/' . $products->productImage }}"
                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                    alt="{{ $products->name }}"
                  />
                </div>
              </a>
              <div class="d-flex flex-column justify-content-between flex-grow-1 p-3">
                <!-- Product Name -->
                <h5 class="product-title mb-3">{{ $products->name }}</h5>

                <!-- Price and Buy Button Row -->
                <div class="d-flex justify-content-between align-items-center mt-auto">
                  <span class="product-price fw-bold fs-6 mb-0">
                    @if($walletType == 'coin')
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

  /* Section Title */
  .product-section-title {
    color: var(--astro-text-dark) !important;
    font-family: 'Poppins', sans-serif;
  }

  body.home-dark-mode .product-section-title {
    color: #ffffff !important;
  }

  .text-muted-custom {
    color: var(--astro-text-muted-light) !important;
    font-family: 'Outfit', sans-serif;
  }

  body.home-dark-mode .text-muted-custom {
    color: #94a3b8 !important;
  }

  /* Dropdown Filter */
  .product-select-custom {
    background-color: var(--astro-card-bg-light) !important;
    color: var(--astro-text-dark) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    font-size: 13.5px;
    height: 42px;
    padding: 6px 12px;
    transition: all 0.3s ease;
  }

  body.home-dark-mode .product-select-custom {
    background-color: #1a1611 !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
  }

  /* Product Card */
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

  /* Product Image */
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

  /* Product Info */
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

    /* Clamp title to 2 lines */
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

  /* Buy Button */
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
  }
</style>

<!-- Pagination Controls -->
<div class="mt-8 d-flex justify-content-center pt-5 pb-5">
    {{ $productlist->appends(request()->query())->links() }}
</div>
@endsection

@section('scripts')
    <script>
    function onFilterProductCategoryList() {
        var productCategoryId = $('#psychicCategories').val();
        var url = new URL(window.location.href);
        url.searchParams.set('productCategoryId', productCategoryId);
        window.location.href = url.toString();
    }
    </script>
@endsection
