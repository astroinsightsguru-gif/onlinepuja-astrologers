@extends('frontend.layout.master')
@section('content')

    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getMyWallet') }}"
                                style="color:white;text-decoration:none">My Wallet</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <div class="ds-head-body">

        <div class="container">
            <div class="row">
                <div class="col-sm-12">

                    <div id="dashaspeaksplanpoup">
                        <div class="">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="page-body">
                                        <p class="txt1" id="planTitle">Add Money To Your Wallet</p>
                                        <p class="txt2" id="planDesc"></p>
                                        <div class="bg-pink mb-2">
                                            <div
                                                class="d-none align-items-center justify-content-center special-offer-ribbon-outer">
                                                <img src="https://cdn.anytimeastro.com/dashaspeaks/web/content/anytimeastro/images/wallet-plan-offer.gif"
                                                    width="43" height="43">
                                                <span class="ribbon-content pl-2 pt-1 font-weight-semi-bold"
                                                    id="UserOfrText"></span>
                                            </div>
                                            <p
                                                class="bg-pink pt-1 pb-1 mb-0 text-center color-red font-16 font-weight-bold mb-0 normal-offer-ribbon-outer">
                                                Available Balance : <span class="gWalletbalance colorblack">
                                                @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                {{number_format($getProfile['data']['totalWalletAmount'],2)}}</span></p>
                                        </div>
                                        <div id="specialplan-item"></div>
                                        <p class="specialplan-otherplan d-none my-3 py-2 font-weight-bold text-center px-3">
                                            Other Recharge Plans Available For You.</p>


                                        <div id="plan-item">
                                            @foreach ($getRechargeAmount['recordList'] as $amount)
                                                <div id="pln-d301" class="pprice ">
                                                    <div class="pdiscount ribbon-top-left"><span> {{ $amount['cashback'] }}%
                                                            Extra</span></div><button class="btn add-plan w-100"
                                                        id="add-301">
                                                        @if($walletType == 'Coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                    {{ $amount['amount'] }}</button>
                                                    <button class="btn  add-plan chatbrown r-btn w-100"
                                                        id="AddPlanloader-301" type="button" style="display: none; "
                                                        disabled=""><span class="spinner-border spinner-border-sm"
                                                            role="status" aria-hidden="true"></span> Loading...</button>
                                                </div>
                                            @endforeach
                                        </div>


                                        <hr class="p-2 mt-3">
                                        <div class="row">

                                            <div class="col-lg-6 ml-auto">
                                                <div class="row">
                                                    <div class="col-sm-12 pb-2 text-right">
                                                        <img
                                                            src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/pay-safely.png') }}">
                                                    </div>
                                                </div>
                                                <div
                                                    class="onlinepuja-astro-plan-calc justify-content-between align-items-center">
                                                    <div class="row">
                                                        <div class="col-sm-12">

                                                            <div class="onlinepuja-astro-plan-calc-left">
                                                                <form id="prcdtopayform">


                                                                    <div class="onlinepuja-calc-section">
                                                                        <p class="font-weight-bold border-bottom">Your
                                                                            Order</p>
                                                                        <p
                                                                            class="d-flex justify-content-between align-items-center pt-2">
                                                                            <span id="planText" class="w-50 d-flex">Main
                                                                                Amount</span><span id="packageText"
                                                                                class="w-50 d-none">Package
                                                                                Value</span><span>:</span><span
                                                                                class="w-50 text-right"
                                                                                id="total_plan_amount">
                                                                                @if($walletType == 'Coin')
                                                                                     <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                                                 @else
                                                                                     {{ $currency['value'] }}
                                                                                @endif
                                                                            {{ number_format($selectedamount['amount'], 2) }}</span>
                                                                        </p>
                                                                         <input type="hidden" id="subtotal" name="subtotal" value="{{ number_format($selectedamount['amount'], 2) }}">
                                                                        <p class="justify-content-between align-items-center d-none"
                                                                            id="discount-div"><span class="w-50"><span
                                                                                    id="total_plan_discount_percent"></span>%
                                                                                Discount</span><span>:</span><span
                                                                                class="w-50 text-right"
                                                                                id="total_plan_discount_amount"></span></p>
                                                                        <p id="gst_details"
                                                                            class="justify-content-between align-items-center d-flex">
                                                                            <span class="w-50">
                                                                                <span id="gstval">
                                                                                    {{ $gstvalue['value'] }}</span>% GST(+)
                                                                            </span><span>:</span><span
                                                                                class="w-50 text-right"
                                                                                id="total_plan_gst_amount">
                                                                                @if($walletType == 'Coin')
                                                                                    <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                                                @else
                                                                                    {{ $currency['value'] }}
                                                                                @endif
                                                                            {{ number_format($selectedamount['amount'] * ($gstvalue['value'] / 100), 2) }}</span>
                                                                        </p>
                                                                        <input type="hidden" id="gst_amount" name="gst_amount" value="{{ number_format($selectedamount['amount'] * ($gstvalue['value'] / 100), 2) }}">
                                                                        <p
                                                                            class="d-flex justify-content-between align-items-center font-weight-bold border-top">
                                                                            <span
                                                                                class="w-50 ">Total</span><span>:</span><span
                                                                                class="w-50 text-right total_plan_amount_with_gst">
                                                                                @if($walletType == 'Coin')
                                                                                    <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                                                @else
                                                                                    {{ $currency['value'] }}
                                                                                @endif
                                                                            {{ number_format($selectedamount['amount'] + $selectedamount['amount'] * ($gstvalue['value'] / 100), 2) }}</span>
                                                                        </p>

                                                                        <div class="p-2 align-items-center total-cashback-amount position-relative d-flex"
                                                                            id="total-cashback-amount">
                                                                            <img
                                                                                src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/cashback-icon.svg') }}">
                                                                            <span class="ml-3">
                                                                                <span class="d-block font-14">
                                                                                    <span id="extracashback"
                                                                                        class="extracashback"> <span
                                                                                            class="font-weight-semi-bold"><span
                                                                                                id="total_plan_cashback_percent">{{ $selectedamount['cashback'] }}%
                                                                                                Extra</span></span></span>
                                                                                </span>
                                                                                <span class="d-block font-12">
                                                                                    <span
                                                                                        id="total_plan_cashback_amount">
                                                                                       @if($walletType == 'Coin')
                                                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                                                        @else
                                                                                        {{ $currency['value'] }}
                                                                                        @endif
                                                                                        {{ number_format(($selectedamount['amount'] * $selectedamount['cashback']) / 100, 2) }}
                                                                                        cashback</span>

                                                                                        <input type="hidden" id="paycashback" name="cashback_amount" value="{{ number_format(($selectedamount['amount'] * $selectedamount['cashback']) / 100, 2) }}">
                                                                                </span>
                                                                            </span>

                                                                        </div>

                                                                        <input type="hidden" id="payamount" name="amount" value="{{ number_format($selectedamount['amount'] + $selectedamount['amount'] * ($gstvalue['value'] / 100), 2) }}">
                                                                        {{-- <span id="gatewayPrice"
                                                                            class="total_plan_amount_with_gst ml-2">
                                                                          @if($walletType == 'Coin')
                                                                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                                            @else
                                                                                {{ $currency['value'] }}
                                                                            @endif
                                                                        {{ number_format($selectedamount['amount'] + $selectedamount['amount'] * ($gstvalue['value'] / 100), 2) }}</span> --}}

                                                                        <div class="onlinepuja-astro-plan-calc-right">
                                                                            <div
                                                                                class="onlinepuja-astro-plan-calc-right-mob-container mt-3">
                                                                                <div class="mb-2">
                                                                                <span type="button" style="height:30px!important" class="btn btn-block" id="proceedbtn">Proceed To Pay &nbsp;&nbsp;
                                                                                    <span>
                                                                                       @if($walletType == 'Coin')
                                                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                                                        @else
                                                                                        {{ $currency['value'] }}
                                                                                        @endif
                                                                                    </span>
                                                                                    <span>&nbsp;{{ number_format($selectedamount['amount'] + $selectedamount['amount'] * ($gstvalue['value'] / 100), 2) }}</span>
                                                                                </span>
                                                                                 </div>



                                                                                <p class="text-center font-12">
                                                                                    By confirming this payment, you agree to
                                                                                    our <a class="text-primary"
                                                                                        href="{{route('front.privacyPolicy')}}"
                                                                                        target="_blank">Privacy Policy</a>
                                                                                    and <a class="text-primary"
                                                                                        href="{{route('front.termscondition')}}"
                                                                                        target="_blank">Terms Of Use</a>.
                                                                                </p>
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
                            </div>
                        </div>
                        <input type="hidden" id="flag">
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-pink-light py-5 payment-icons-section">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12 text-center">
                        <h2 class="heading">100% SECURE &amp; SAFE PAYMENT</h2>
                        <p class="mb-1">Your details are secure with 3rd party payment</p>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        var currencySymbol = `@if(systemflag('walletType') == 'Coin')
                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                              @else
                                ₹
                              @endif`;
        var gstPercent = {{ $gstvalue['value'] }};

            function updateOrderValues(planAmount, gstPercent, currencySymbol, cashbackPercent, cashbackAmount) {
                var gstAmount = (parseFloat(planAmount) * parseFloat(gstPercent)) / 100;
                var totalAmount = parseFloat(planAmount) + gstAmount;
                var cashback = (parseFloat(planAmount) * parseFloat(cashbackPercent)) / 100;

                $('#total_plan_amount').text(currencySymbol + planAmount.toFixed(2));
                $('#total_plan_gst_amount').text(currencySymbol + gstAmount.toFixed(2));
                $('.total_plan_amount_with_gst').text(currencySymbol + totalAmount.toFixed(2));

                $('#total_plan_cashback_percent').text(cashbackPercent + "% Extra");
                $('#total_plan_cashback_amount').text(currencySymbol + (cashbackAmount).toFixed(2) + " cashback");

                   // Set values for hidden input fields
                // console.log(totalAmount, cashbackAmount); // Debugging line
                $('#payamount').val(totalAmount.toFixed(2));
                $('#paycashback').val(cashbackAmount.toFixed(2));
                $('#subtotal').val(planAmount.toFixed(2));
                $('#gst_amount').val(gstAmount.toFixed(2));
                
                // Update proceed button text with new amount
                $('#proceedbtn').html('Proceed To Pay &nbsp;&nbsp;<span>' + currencySymbol + '</span>&nbsp;<span>' + totalAmount.toFixed(2) + '</span>');

            }

            $('.add-plan').click(function() {
                var planAmount = parseFloat($(this).text().replace(currencySymbol, ''));
                var cashbackPercent = parseFloat($(this).parent().find('.pdiscount span').text().replace(
                    "% Extra", ""));
                var cashbackAmount = (parseFloat(planAmount) * parseFloat(cashbackPercent)) / 100;

                updateOrderValues(planAmount, gstPercent, currencySymbol, cashbackPercent, cashbackAmount);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#proceedbtn').click(function(e) {
                e.preventDefault();

                @php
                    use Symfony\Component\HttpFoundation\Session\Session;
                    $session = new Session();
                    $token = $session->get('token');

                @endphp
                var formData = $('#prcdtopayform').serialize();
                // console.log(formData);
                // return false;

                $.ajax({
                    url: '{{ route('user.addpayment', ['token' => $token]) }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        window.location.href = response.url;
                        // console.log(response);
                    },

                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
