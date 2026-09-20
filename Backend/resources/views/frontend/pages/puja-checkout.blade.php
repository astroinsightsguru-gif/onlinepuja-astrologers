@extends('frontend.layout.master')
@section('content')
<style>
    select[name="country"].select2 + .select2-container,
    select[name="state"].select2 + .select2-container,
    select[name="city"].select2 + .select2-container {
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    /* ====== checkout styling ====== */
    .cat-pages {
        background-color: #f8fafc;
        transition: background-color 0.3s ease;
        font-family: 'Outfit', sans-serif;
    }

    .cat-heading {
        color: #1e293b;
        font-family: 'Poppins', sans-serif;
    }

    .checkout-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .checkout-card-header {
        background: #ff6600;
        color: #ffffff;
        font-weight: 600;
        text-align: center;
        padding: 12px;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.5px;
        font-size: 1.05rem;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-add-address {
        background: transparent;
        border: 2px solid #ff6600 !important;
        color: #ff6600 !important;
        border-radius: 50px;
        padding: 8px 22px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all 0.3s ease;
        text-decoration: none !important;
        display: inline-block;
    }

    .btn-add-address:hover {
        background: #ff6600 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(255, 102, 0, 0.25);
    }

    /* Address Table Styling */
    .checkout-table {
        width: 100%;
        margin-bottom: 0;
        color: #334155;
        border-collapse: collapse;
    }

    .checkout-table thead tr th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
        font-family: 'Poppins', sans-serif;
    }

    .checkout-table tbody tr td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #475569;
    }

    .checkout-table input[type="radio"] {
        accent-color: #ff6600;
        transform: scale(1.2);
        cursor: pointer;
    }

    /* Detail Card Right column */
    .detail-card {
        background: transparent !important;
        border: none !important;
    }

    .detail-title {
        color: #1e293b;
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
    }

    .detail-row-label {
        color: #475569;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .detail-row-value {
        color: #1e293b;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .detail-price-total {
        color: #dc2626;
        font-weight: 700;
        font-size: 1.3rem;
        font-family: 'Poppins', sans-serif;
    }

    .btn-buy-now {
        background: #ff6600 !important;
        color: #ffffff !important;
        font-weight: 700;
        border-radius: 50px;
        padding: 14px 28px;
        font-size: 1.05rem;
        border: none !important;
        transition: all 0.3s ease;
        box-shadow: 0 6px 18px rgba(255, 102, 0, 0.2);
        width: 100%;
    }

    .btn-buy-now:hover {
        background: #e05500 !important;
        box-shadow: 0 8px 24px rgba(255, 102, 0, 0.35);
        transform: translateY(-2px);
    }

    /* Address Modal */
    #addressBtn {
        background: #ff6600 !important;
        color: #ffffff !important;
        font-weight: 700;
        border-radius: 50px;
        padding: 12px 24px;
        border: none !important;
        transition: all 0.3s ease;
    }

    #addressBtn:hover {
        background: #e05500 !important;
        box-shadow: 0 4px 12px rgba(255, 102, 0, 0.25);
    }

    /* ==========================================
       DARK MODE OVERRIDES (body.home-dark-mode)
       ========================================== */
    body.home-dark-mode .cat-pages {
        background-color: #120f0b !important;
    }

    body.home-dark-mode .cat-heading {
        color: #ffffff !important;
    }

    body.home-dark-mode .checkout-card {
        background: #1a1611;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    body.home-dark-mode .checkout-card-header {
        background: #f1e135;
        color: #0c0a08;
    }

    body.home-dark-mode .btn-add-address {
        border-color: #f1e135 !important;
        color: #f1e135 !important;
    }

    body.home-dark-mode .btn-add-address:hover {
        background: #f1e135 !important;
        color: #0c0a08 !important;
        box-shadow: 0 4px 12px rgba(241, 225, 53, 0.35);
    }

    body.home-dark-mode .checkout-table {
        color: #cbd5e1;
    }

    body.home-dark-mode .checkout-table thead tr th {
        background-color: #15120e;
        color: #ffffff;
        border-bottom-color: rgba(255, 255, 255, 0.08);
    }

    body.home-dark-mode .checkout-table tbody tr td {
        border-bottom-color: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
    }

    body.home-dark-mode .checkout-table input[type="radio"] {
        accent-color: #f1e135;
    }

    body.home-dark-mode .detail-title {
        color: #ffffff;
    }

    body.home-dark-mode .detail-row-label {
        color: #94a3b8;
    }

    body.home-dark-mode .detail-row-value {
        color: #cbd5e1;
    }

    body.home-dark-mode .detail-price-total {
        color: #f1e135;
    }

    body.home-dark-mode .btn-buy-now {
        background: #f1e135 !important;
        color: #0c0a08 !important;
        box-shadow: 0 6px 18px rgba(241, 225, 53, 0.2);
    }

    body.home-dark-mode .btn-buy-now:hover {
        background: #d8c92a !important;
        box-shadow: 0 8px 24px rgba(241, 225, 53, 0.35);
    }

    body.home-dark-mode .modal-content {
        background-color: #1a1611 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }

    body.home-dark-mode .modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    body.home-dark-mode .modal-body {
        background-color: #1a1611 !important;
    }

    body.home-dark-mode .modal-body .bg-white.body {
        background-color: #1a1611 !important;
    }

    body.home-dark-mode .modal-body label {
        color: #cbd5e1 !important;
    }

    body.home-dark-mode .modal-body input,
    body.home-dark-mode .modal-body select {
        background-color: #15120e !important;
        color: #ffffff !important;
        border-color: rgba(255,255,255,0.08) !important;
    }

    body.home-dark-mode .modal-body input:focus,
    body.home-dark-mode .modal-body select:focus {
        border-color: #f1e135 !important;
        box-shadow: 0 0 0 0.25rem rgba(241, 225, 53, 0.15) !important;
    }

    body.home-dark-mode #addressBtn {
        background: #f1e135 !important;
        color: #0c0a08 !important;
    }

    body.home-dark-mode #addressBtn:hover {
        background: #d8c92a !important;
    }
</style>

@php
    $countries = DB::table('countries2')->get();

    $countries2 = DB::table('countries')
        ->orderByRaw("CASE WHEN phonecode = 91 THEN 0 ELSE 1 END")
        ->get();
@endphp

    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{route('front.pujacheckout',['slug'=>$astrologer->id,'id'=>$PujaDetails->id,'package_id'=>$PujaDetails->packages->id ?? 0])}}"
                                style="color:white;text-decoration:none">Puja Checkout </a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- SHIPPING DETAILS Modal -->
    <div class="modal fade rounded mt-2 mt-md-5 login-offer" id="checkout" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        SHIPPING DETAILS
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" style="background:none; border:none; font-size:1.5rem;">&times;</button>
                </div>
                <div class="modal-body pt-0 pb-0">
                    <div class="bg-white body">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <div class="mb-3">
                                    <form class="px-3 font-14" method="post" id="orderAddress" autocomplete="off">
                                        <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                        <div class="row">
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"></span>
                                                    <label for="Name" class="mb-1">Name&nbsp;<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="name" placeholder="Enter Name"
                                                        type="text" value="" pattern="^[a-zA-Z\s]{2,50}$" title="Name should contain only letters and be between 2 and 50 characters long." required
                                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                     <span class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="phoneNumber" data-valmsg-replace="false"></span>
                                                    <label for="contact" class="mb-1">Phone No<span class="color-red font-weight-bold">*</span></label>
                                                    <div class="input-group">
                                                        <div class="d-flex inputform country-dropdown-container w-100" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                                            <select class="form-control select2" id="countryCode" name="countryCode" style="border: none; border-right: 1px solid #ddd; border-radius: 0; width: 30%;">
                                                                @foreach ($countries2 as $country)
                                                                <option data-country="in" value="{{$country->phonecode}}" data-ucname="India">
                                                                    +{{ $country->phonecode }} {{ $country->iso }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            <!-- Mobile Number Input -->
                                                            <input class="form-control mobilenumber text-box single-line" id="contact" maxlength="12" name="phoneNumber" type="number" style="border: none; border-radius: 0; width: 70%;" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="flatNo" class="mb-1">Flat No&nbsp;<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="flatNo" name="flatNo" placeholder="Enter Flat"
                                                        type="text" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="locality" class="mb-1">Locality&nbsp;<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="locality" name="locality" placeholder="Enter Locality"
                                                        type="text" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="landmark" class="mb-1">Landmark&nbsp;<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="landmark" name="landmark" placeholder="Enter Landmark"
                                                        type="text" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="country" class="mb-1">Country <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="country" id="country" required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="state" class="mb-1">State <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="state" id="state" required>
                                                        <option value="">Select State</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="city" class="mb-1">City <span class="color-red">*</span></label>
                                                    <select class="form-control select2" name="city" id="city" required>
                                                        <option value="">Select City</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <label for="pincode" class="mb-1">Pincode&nbsp;<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="pincode" name="pincode" placeholder="Enter Pincode"
                                                        type="text" value="" pattern="\d{6}"
                                                        inputmode="numeric" title="Pincode should be a 6 digit number." required
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-block px-4 px-md-5 mb-2 w-100" id="addressBtn">Add Address</button>
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

    <!-- Main Checkout Section -->
    <div class="ds-head-populararticle cat-pages">
        <div class="container py-5">
            <div class="row">
                <div class="col-12 mb-4">
                    <h2 class="cat-heading font-24 font-weight-bold">
                        Puja Checkout <span class="">Form</span>
                    </h2>
                </div>

                <!-- Left: Address Selection -->
                <div class="col-lg-8 col-12 mb-4">
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            SELECT ADDRESS
                        </div>
                        <div class="p-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto ms-auto">
                                    <a role="button" data-toggle="modal" data-target="#checkout" class="btn btn-add-address">
                                        Add Address
                                    </a>
                                </div>
                            </div>

                            <form method="post" id="orderForm" autocomplete="off">
                                <input type="hidden" name="astrologer_id" value="{{ $astrologer->id }}">
                                <div class="table-responsive rounded-3 border">
                                    <table class="table checkout-table text-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getOrderAddressed as $getOrderAddress)
                                                <tr>
                                                    <td>
                                                        <input type="radio" name="orderAddressId" id="orderAddressId_{{ $getOrderAddress['id'] }}" value="{{ $getOrderAddress['id'] }}">
                                                    </td>
                                                    <td class="fw-semibold">{{ $getOrderAddress['name'] }}</td>
                                                    <td>{{ $getOrderAddress['countryCode'] }} {{ $getOrderAddress['phoneNumber'] }}</td>
                                                    <td class="text-start" style="font-size:0.88rem;">
                                                        {{ $getOrderAddress['flatNo'] }}, {{ $getOrderAddress['locality'] }}, {{ $getOrderAddress['landmark'] }}, {{ $getOrderAddress['city'] }}, {{ $getOrderAddress['state'] }}, {{ $getOrderAddress['country'] }}, {{ $getOrderAddress['pincode'] }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-muted py-4">No shipping addresses found. Please add one.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Puja Detail -->
                <div class="col-lg-4 col-12">
                    <div class="checkout-card p-3">
                        <div class="checkout-card-header rounded-3 mb-3">
                            Puja Detail
                        </div>
                        <div class="card detail-card mt-2">
                            <div class="card-body pt-0 px-2">
                                <div class="text-center mb-4">
                                    @if (!empty($PujaDetails->puja_images) && is_array($PujaDetails->puja_images))
                                        <div id="pujaImageSlider" class="carousel slide border rounded-3 overflow-hidden mx-auto shadow-sm" data-ride="carousel" style="width: 100%; max-width: 240px; height: 160px; position:relative;">
                                            <div class="carousel-inner w-100 h-100">
                                                @foreach($PujaDetails->puja_images as $key => $image)
                                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }} w-100 h-100">
                                                        <img class="w-100 h-100 object-fit-cover" src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="{{ $PujaDetails->puja_title }}" />
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if(count($PujaDetails->puja_images) > 1)
                                                <a class="carousel-control-prev" href="#pujaImageSlider" role="button" data-slide="prev" style="width: 30px; height: 30px; top: 50%; left: 5px; transform: translateY(-50%); background: rgba(0,0,0,0.3); border-radius:50%;">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="width:14px; height:14px;"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#pujaImageSlider" role="button" data-slide="next" style="width: 30px; height: 30px; top: 50%; right: 5px; transform: translateY(-50%); background: rgba(0,0,0,0.3); border-radius:50%;">
                                                    <span class="carousel-control-next-icon" aria-hidden="true" style="width:14px; height:14px;"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <img class="img-fluid border rounded-3 mx-auto shadow-sm" src="{{ asset('public/frontend/homeimage/360.png') }}" style="width: 100%; max-width: 240px; height: 160px; object-fit: cover;">
                                    @endif
                                    <div class="mt-3">
                                        <p class="mb-0 detail-title"><b>{{ $PujaDetails->puja_title}}</b></p>
                                    </div>
                                </div>

                                <input type="hidden" name="pujaId" value="{{ $PujaDetails->id }}">
                                @if(isset($PujaDetails->packages->id))
                                    <input type="hidden" name="packageId" value="{{ $PujaDetails->packages->id }}">
                                @endif

                                <hr class="my-3 opacity-10">

                                @if(isset($PujaDetails->packages->id))
                                    <div class="mb-3">
                                        <span class="detail-row-label d-block mb-1">Package:</span>
                                        <span class="detail-row-value fw-semibold">{{ $PujaDetails->packages->title }} ({{$PujaDetails->packages->person}} Person)</span>
                                    </div>
                                @endif

                                <div class="row justify-content-between mb-3 align-items-center">
                                    <div class="col-auto">
                                        <span class="detail-row-label">Price:</span>
                                    </div>
                                    <div class="col-auto text-end">
                                        @if(isset($PujaDetails->packages->id))
                                            <span class="detail-row-value fw-bold">{{ $currency->value }}{{ number_format($PujaDetails->packages->package_price, 2) }}</span>
                                            <div class="small text-muted" style="font-size:0.75rem;">(incl. of all taxes)</div>
                                            <input type="hidden" value="{{ number_format($PujaDetails->packages->package_price, 2) }}" name="payableAmount">
                                        @else
                                            <span class="detail-row-value fw-bold">{{ $currency->value }}{{ number_format($PujaDetails->puja_price, 2) }}</span>
                                            <div class="small text-muted" style="font-size:0.75rem;">(incl. of all taxes)</div>
                                            <input type="hidden" value="{{ number_format($PujaDetails->puja_price, 2) }}" name="payableAmount">
                                        @endif
                                    </div>
                                </div>

                                <hr class="my-3 opacity-10">

                                <div class="row justify-content-between mb-4 align-items-center">
                                    <div class="col-auto">
                                        <span class="detail-row-label fw-bold h6 mb-0">Total Price:</span>
                                    </div>
                                    <div class="col-auto text-end">
                                        @if(isset($PujaDetails->packages->id))
                                            <span class="detail-price-total">{{ $currency->value }}{{ number_format($PujaDetails->packages->package_price , 2) }}</span>
                                            <input type="hidden" value="{{ number_format($PujaDetails->packages->package_price , 2) }}" name="totalPayable" id="totalPayable">
                                        @else
                                            <span class="detail-price-total">{{ $currency->value }}{{ number_format($PujaDetails->puja_price , 2) }}</span>
                                            <input type="hidden" value="{{ number_format($PujaDetails->puja_price , 2) }}" name="totalPayable" id="totalPayable">
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-buy-now" id="orderBtn">Buy Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                $session = new \Symfony\Component\HttpFoundation\Session\Session();
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
            var radioButton = document.querySelector('input[name="orderAddressId"]:checked');

            if (!radioButton) {
                toastr.error('Please select an address.', 'Validation Error', {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true
                });
                e.preventDefault();
                return;
            }
            e.preventDefault();

            var token = "{{ session('token') }}";

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to proceed with this Puja order?",
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
                        url: '{{ route('front.addUserPujaOrder', ['token' => '']) }}' + token,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if(response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                toastr.success('Puja Ordered Successfully');
                                setTimeout(function() {
                                    window.location.href = '{{ route('front.home') }}';
                                }, 2000);
                            }
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
