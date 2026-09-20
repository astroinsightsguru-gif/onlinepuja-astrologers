@extends('frontend.layout.master')
@section('content')

@php
    $countries = DB::table('countries2')->get();
    $countries2 = DB::table('countries')->orderByRaw("CASE WHEN phonecode = 91 THEN 0 ELSE 1 END")->get();
@endphp

<!-- Breadcrumbs Header -->
<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize; ">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i>
                        <a href="{{ route('front.getproducts') }}" style="color:white;text-decoration:none">Products</a>
                        <i class="fa fa-chevron-right"></i>
                        <a href="{{ route('front.checkout',['id' => $getproductdetails->id]) }}" style="color:white;text-decoration:none">Checkout</a>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Address Modal -->
<div class="modal fade rounded mt-2 mt-md-5 login-offer" id="checkout" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">
                    SHIPPING DETAILS
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-0 pb-0">
                <div class="body">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <div class="mb-3">
                                <form class="px-3 font-14" method="post" id="orderAddress" autocomplete="off">
                                    <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                    <div class="row">
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="BoyName" class="form-label-custom">Name&nbsp;<span class="color-red">*</span></label>
                                                <input class="form-control form-control-custom matchInTxt shadow-none"
                                                    id="Name" name="name" placeholder="Enter Name"
                                                    type="text" value="" pattern="^[a-zA-Z\s]{2,50}$" title="Name should contain only letters and be between 2 and 50 characters long." required
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="BoyName" class="form-label-custom">Phone No<span class="color-red font-weight-bold">*</span></label>
                                                <div class="country-dropdown-container">
                                                    <select class="form-control select-country-code" id="checkoutCountryCode" name="countryCode">
                                                        @foreach ($countries2 as $country)
                                                            <option data-country="in" value="{{$country->phonecode}}" data-ucname="India">
                                                                +{{ $country->phonecode }} {{ $country->iso }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input class="form-control mobilenumber-input text-box single-line" id="contact" maxlength="12" name="phoneNumber" type="number" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="BoyName" class="form-label-custom">Flat No&nbsp;<span class="color-red">*</span></label>
                                                <input class="form-control form-control-custom matchInTxt shadow-none"
                                                    id="flatNo" name="flatNo" placeholder="Enter Flat"
                                                    type="text" value="" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="BoyName" class="form-label-custom">Locality&nbsp;<span class="color-red">*</span></label>
                                                <input class="form-control form-control-custom matchInTxt shadow-none"
                                                    id="locality" name="locality" placeholder="Enter Locality"
                                                    type="text" value="" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="BoyName" class="form-label-custom">Landmark&nbsp;<span class="color-red">*</span></label>
                                                <input class="form-control form-control-custom matchInTxt shadow-none"
                                                    id="landmark" name="landmark" placeholder="Enter Landmark"
                                                    type="text" value="" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="country" class="form-label-custom">Country <span class="color-red">*</span></label>
                                                <select class="form-control select2 form-control-custom" name="country" required>
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="state" class="form-label-custom">State <span class="color-red">*</span></label>
                                                <select class="form-control select2 form-control-custom" name="state" required>
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="city" class="form-label-custom">City <span class="color-red">*</span></label>
                                                <select class="form-control select2 form-control-custom" name="city" required>
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-3">
                                            <div class="form-group mb-0">
                                                <label for="BoyName" class="form-label-custom">Pincode&nbsp;<span class="color-red">*</span></label>
                                                <input class="form-control form-control-custom matchInTxt shadow-none"
                                                    id="pincode" name="pincode" placeholder="Enter Pincode"
                                                    type="text" value="" pattern="\d{6}"
                                                    inputmode="numeric" title="Pincode should be a 6 digit number." required
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 py-3">
                                        <div class="row">
                                            <div class="col-12 pt-md-3 text-center mt-2">
                                                <button type="submit" class="btn btn-block btn-submit-custom px-4 px-md-5 mb-2 w-100" id="addressBtn">Add Address</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Checkout Form Section -->
<div class="ds-head-populararticle cat-pages py-4">
    <div class="container">
        <div class="row py-3">
            <div class="col-sm-12 mt-4">
                <div class="row">
                    <div class="col-12 text-center text-md-left mb-4">
                        <h2 class="position-relative d-inline-block pb-3 fw-bold text-dark checkout-section-title">
                            Checkout Form
                            <span class="position-absolute bottom-0 start-0 bg-warning d-block rounded" style="width: 60px; height: 3px;"></span>
                        </h2>
                    </div>

                    <!-- Left: Select Address -->
                    <div class="col-lg-8 col-12 mb-4">
                        <div class="checkout-card-custom mb-3">
                            <div class="card-header-custom py-2 px-3 text-center">
                                SELECT ADDRESS
                            </div>

                            <div class="p-3">
                                <div class="row justify-content-end mb-3">
                                    <div class="col-auto">
                                        <a role="button" data-toggle="modal" data-target="#checkout" class="btn btn-outline-custom">
                                            <i class="fa fa-plus me-1"></i> Add Address
                                        </a>
                                    </div>
                                </div>

                                <form method="post" id="orderForm" autocomplete="off">
                                    <div class="table-responsive">
                                        <table class="table table-custom font-14 mb-0 text-center">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Address</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($getOrderAddress['recordList'] as $address)
                                                    <tr>
                                                        <td>
                                                            <input type="radio" name="orderAddressId" value="{{ $address['id'] }}">
                                                        </td>
                                                        <td class="fw-semibold">{{ $address['name'] }}</td>
                                                        <td>{{ $address['phoneNumber'] }}</td>
                                                        <td class="text-left font-13">{{ $address['flatNo'] }}, {{ $address['locality'] }}, {{ $address['landmark'] }}, {{ $address['city'] }}, {{ $address['state'] }}, {{ $address['country'] }}, {{ $address['pincode'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Product Detail & Pricing -->
                    <div class="col-lg-4 col-12 mb-4">
                        <div class="checkout-card-custom p-0">
                            <div class="card-header-custom py-2 px-3 text-center">
                                PRODUCT DETAIL
                            </div>
                            <div class="p-4">
                                <div class="border-0">
                                    <div class="card-body p-0">
                                        <div class="mb-3 text-center">
                                            <div class="product-summary-image mb-3">
                                                <img class="img-fluid rounded shadow-sm" src="{{ Str::startsWith($getproductdetails->productImage, ['http://','https://']) ? $getproductdetails->productImage : '/' . $getproductdetails->productImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $getproductdetails->name }}" onclick="openImage('{{ $getproductdetails->productImage }}')" style="width: 100%; height: 160px; object-fit: cover; cursor: pointer;"/>
                                            </div>
                                            <h5 class="fw-bold text-dark-custom mb-1">{{ $getproductdetails->name }}</h5>
                                            <small class="text-muted-custom d-block mt-2 text-left">{!! \Illuminate\Support\Str::limit($getproductdetails->features, 140) !!}</small>
                                        </div>

                                        <input type="hidden" name="productCategoryId" value="{{ $getproductdetails->productCategoryId }}">
                                        <input type="hidden" name="productId" value="{{ $getproductdetails->id }}">

                                        <hr class="my-3 border-custom">

                                        <!-- Price -->
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <p class="text-muted-custom mb-0">Price:</p>
                                            </div>
                                            <div class="col-auto text-dark-custom fw-semibold">
                                                <span class="d-flex align-items-center">
                                                    @if(strtolower($walletType) == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15" class="me-1">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                    {{ number_format($getproductdetails->amount, 2) }}
                                                </span>
                                                <small class="text-muted d-block text-right" style="font-size: 10px;">(incl. of all taxes)</small>
                                                <input type="hidden" value="{{ number_format($getproductdetails->amount, 2, '.', '') }}" name="payableAmount">
                                            </div>
                                        </div>

                                        <hr class="my-3 border-custom">

                                        <!-- Total Payable -->
                                        <div class="row justify-content-between align-items-center mb-2">
                                            <div class="col-auto">
                                                <p class="text-dark-custom fw-bold mb-0">Total Price:</p>
                                            </div>
                                            <div class="col-auto text-danger-custom fw-bold fs-5">
                                                <span class="d-flex align-items-center">
                                                    @if(strtolower($walletType) == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="18" class="me-1">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                    {{ number_format($getproductdetails->amount, 2) }}
                                                </span>
                                                <input type="hidden" value="{{ number_format($getproductdetails->amount, 2, '.', '') }}" name="totalPayable" id="totalPayable">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="py-3">
                            <button type="submit" class="btn btn-submit-custom w-100" id="orderBtn">
                                <i class="fa fa-shopping-bag me-1"></i> Pay & Order Now
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
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
    }

    /* Checkout Card Layout */
    .checkout-card-custom {
        background: var(--astro-card-bg-light);
        border: 1px solid var(--astro-border-light);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01) !important;
        transition: all 0.3s ease;
    }

    body.home-dark-mode .checkout-card-custom {
        background: #1a1611 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-family: 'Poppins', sans-serif;
        font-size: 13.5px;
        letter-spacing: 0.8px;
    }

    body.home-dark-mode .card-header-custom {
        background: linear-gradient(135deg, #2a2015, #1a140d) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #fbbf24 !important;
    }

    /* Form Fields */
    .form-control-custom {
        background-color: #ffffff !important;
        border: 1px solid #ced4da !important;
        color: #1e293b !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        height: auto !important;
        font-size: 14px !important;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus {
        border-color: var(--astro-accent, #f59e0b) !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15) !important;
    }

    body.home-dark-mode .form-control-custom {
        background-color: #15120e !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }

    body.home-dark-mode .form-control-custom:focus {
        border-color: #fbbf24 !important;
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.15) !important;
    }

    .form-label-custom {
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
        font-size: 13.5px;
    }

    body.home-dark-mode .form-label-custom {
        color: #cbd5e1 !important;
    }

    /* Dropdown container */
    .country-dropdown-container {
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        overflow: hidden;
        display: flex;
        width: 100%;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .country-dropdown-container:focus-within {
        border-color: var(--astro-accent, #f59e0b) !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15) !important;
    }

    body.home-dark-mode .country-dropdown-container {
        border-color: rgba(255, 255, 255, 0.08) !important;
        background-color: #15120e !important;
    }

    body.home-dark-mode .country-dropdown-container:focus-within {
        border-color: #fbbf24 !important;
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.15) !important;
    }

    .select-country-code {
        background-color: transparent !important;
        color: inherit !important;
        border: none !important;
        border-right: 1px solid #ced4da !important;
        width: 95px !important;
        height: 44px !important;
        padding: 0 8px !important;
        cursor: pointer;
        flex-shrink: 0;
        font-size: 13.5px;
    }

    body.home-dark-mode .select-country-code {
        border-right-color: rgba(255, 255, 255, 0.08) !important;
    }

    .select-country-code option {
        background-color: #ffffff;
        color: #1e293b;
    }

    body.home-dark-mode .select-country-code option {
        background-color: #1a1611;
        color: #ffffff;
    }

    .mobilenumber-input {
        background-color: transparent !important;
        color: inherit !important;
        border: none !important;
        padding: 10px 14px !important;
        height: 44px !important;
        flex-grow: 1 !important;
        width: 100% !important;
        font-size: 14px;
    }

    .mobilenumber-input:focus, .select-country-code:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    /* Modal Overrides */
    body.home-dark-mode .modal-content {
        background-color: #1a1611 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }
    body.home-dark-mode .modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    body.home-dark-mode .modal-header h4 {
        color: #ffffff !important;
    }
    body.home-dark-mode .modal-header .close {
        color: #ffffff !important;
        opacity: 0.8;
    }

    /* Section Title */
    .checkout-section-title {
        color: var(--astro-text-dark) !important;
        font-family: 'Poppins', sans-serif;
    }

    body.home-dark-mode .checkout-section-title {
        color: #ffffff !important;
    }

    /* Custom Tables */
    .table-custom {
        color: var(--astro-text-dark);
        font-family: 'Outfit', sans-serif;
    }

    body.home-dark-mode .table-custom {
        color: #cbd5e1 !important;
    }

    .table-custom th {
        background: rgba(245, 158, 11, 0.04) !important;
        color: var(--astro-accent-hover, #d97706) !important;
        font-weight: 700;
        border-bottom: 2px solid rgba(245, 158, 11, 0.1) !important;
    }

    body.home-dark-mode .table-custom th {
        background: rgba(251, 191, 36, 0.04) !important;
        color: #fbbf24 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    .table-custom td {
        border-color: rgba(245, 158, 11, 0.06) !important;
        vertical-align: middle;
    }

    body.home-dark-mode .table-custom td {
        border-color: rgba(255, 255, 255, 0.05) !important;
    }

    /* Primary Submit Button */
    .btn-submit-custom {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 50px !important;
        padding: 12px 28px !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-submit-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4) !important;
        color: #ffffff !important;
    }

    body.home-dark-mode .btn-submit-custom {
        background: linear-gradient(135deg, #fbbf24, #f1e135) !important;
        color: #0c0a08 !important;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3) !important;
    }

    body.home-dark-mode .btn-submit-custom:hover {
        box-shadow: 0 8px 24px rgba(251, 191, 36, 0.45) !important;
        color: #0c0a08 !important;
    }

    /* Outline Button */
    .btn-outline-custom {
        border: 2px solid var(--astro-accent, #f59e0b) !important;
        color: var(--astro-accent, #f59e0b) !important;
        background: transparent;
        font-weight: 700;
        padding: 6px 18px;
        border-radius: 50px;
        transition: all 0.3s ease;
        font-size: 13px;
        display: inline-block;
        text-decoration: none !important;
    }

    .btn-outline-custom:hover {
        background: var(--astro-accent, #f59e0b) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    body.home-dark-mode .btn-outline-custom {
        border-color: #fbbf24 !important;
        color: #fbbf24 !important;
    }

    body.home-dark-mode .btn-outline-custom:hover {
        background: #fbbf24 !important;
        color: #0c0a08 !important;
    }

    /* Text Customizations */
    .text-dark-custom {
        color: var(--astro-text-dark, #1e293b);
    }

    body.home-dark-mode .text-dark-custom {
        color: #ffffff !important;
    }

    .text-muted-custom {
        color: var(--astro-text-muted-light) !important;
    }

    body.home-dark-mode .text-muted-custom {
        color: #94a3b8 !important;
    }

    .text-danger-custom {
        color: #dc2626 !important;
    }

    body.home-dark-mode .text-danger-custom {
        color: #f87171 !important;
    }

    .border-custom {
        border-color: var(--astro-border-light) !important;
    }

    body.home-dark-mode .border-custom {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    /* Select2 Dark Mode Adaptations */
    body.home-dark-mode .select2-container--default .select2-selection--single {
        background-color: #15120e !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }
    body.home-dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #ffffff !important;
    }
    body.home-dark-mode .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #ffffff transparent transparent transparent !important;
    }
    .select2-container--default .select2-selection--single {
        height: 44px !important;
        padding: 8px 12px !important;
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        right: 8px !important;
    }
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
    $(document).ready(function () {
        const $countryDropdown = $('select[name="country"]');
        const $stateDropdown = $('select[name="state"]');
        const $cityDropdown = $('select[name="city"]');

        $countryDropdown.on('change', function () {
            const countryId = $(this).val();

            $stateDropdown.html('<option value="">Select State</option>');
            $cityDropdown.html('<option value="">Select City</option>');

            if (countryId) {
                $.ajax({
                    url: `/get-states/${countryId}`,
                    type: 'GET',
                    success: function (data) {
                        $stateDropdown.html('<option value="">Select State</option>');
                        $.each(data, function (key, value) {
                            $stateDropdown.append(`<option value="${value.id}">${value.name}</option>`);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching states:', error);
                    }
                });
            }
        });

        $stateDropdown.on('change', function () {
            const stateId = $(this).val();

            $cityDropdown.html('<option value="">Select City</option>');

            if (stateId) {
                $.ajax({
                    url: `/get-cities/${stateId}`,
                    type: 'GET',
                    success: function (data) {
                        $cityDropdown.html('<option value="">Select City</option>');
                        $.each(data, function (key, value) {
                            $cityDropdown.append(`<option value="${value.id}">${value.name}</option>`);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching cities:', error);
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#addressBtn').click(function(e) {
            e.preventDefault();

            var form = document.getElementById('orderAddress');
            if (form.checkValidity() === false) {
                form.reportValidity();
                return;
            }

            @php
                use Symfony\Component\HttpFoundation\Session\Session;
                $session = new Session();
                $token = $session->get('token');
            @endphp

            var formData = $('#orderAddress').serialize();

            $.ajax({
                url: '{{ route('api.addOrderAddress', ['token' => $token]) }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Address Added Successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#orderBtn').click(function(e) {
            e.preventDefault();

            @php
                $token = $session->get('token');

                $wallet = DB::table('user_wallets')
                ->where('userId', '=', authcheck()['id'])
                ->first();

                if (!$wallet) {
                    $wallet = (object) ['amount' => 0];
                }
            @endphp

            var paymentMethod = 'wallet';

            if (!$("input[name='orderAddressId']:checked").val()) {
                toastr.error('Please select an address.');
                return;
            }

            var payableAmount=$("#totalPayable").val();
            var walletamount="{{$wallet->amount}}";

            newpayableAmount=(parseFloat(payableAmount.replace(/,/g, '')));

            if(walletamount < newpayableAmount){
                toastr.error('Insufficient Balance in wallet');
                $.ajax({
                    url: '{{ route('user.addpayment', ['token' => $token]) }}',
                    type: 'POST',
                    data: {
                        'amount':newpayableAmount,
                        'cashback_amount':0
                    },
                    success: function(response) {
                        window.location.href = response.url;
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseText);
                    }
                 });
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to proceed with the order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, order now!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var paymentMethod = 'wallet';
                    var formData = $('#orderForm').serialize();
                    formData += '&paymentMethod=' + encodeURIComponent(paymentMethod);

                    $.ajax({
                        url: '{{ route('api.addUserOrder', ['token' => $token]) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            toastr.success('Product Ordered Successfully');
                            setTimeout(function() {
                                window.location.href = '{{ route('front.home') }}';
                            }, 2000);
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = JSON.parse(xhr.responseText).error.paymentMethod[0];
                            toastr.error(errorMessage);
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
