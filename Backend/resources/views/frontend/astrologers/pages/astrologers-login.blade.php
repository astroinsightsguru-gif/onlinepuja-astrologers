<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($professionTitle) }} Login</title>
    <link rel="icon" href="/{{ $logo['value'] }}" type="image/x-icon">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta
        content="Ask an online {{ $professionTitle }} and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best {{ $professionTitle }}s' team."
        name="description" />
    <meta property="Keywords" content="" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="" />
    <meta name="twitter:description"
        content="Ask an online {{ $professionTitle }} and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best {{ $professionTitle }}s' team." />
    <meta name="twitter:title"
        content="Online Astrology Consultation, Ask an {{ ucfirst($professionTitle) }} - {{ ucfirst($appname) }}" />
    <meta name="twitter:image" content="/public/storage/images/AdminLogo1707194841.png" />

    <meta property="og:type" content="website" />
    <meta property="og:title"
        content="Online Astrology Consultation, Ask an {{ ucfirst($professionTitle) }} - {{ ucfirst($appname) }}" />
    <meta property="og:description"
        content="Ask an online {{ $professionTitle }} and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best {{ $professionTitle }}s' team." />
    <meta property="og:image" content="/public/storage/images/AdminLogo1707194841.png" />
    <meta property="og:url" content="index.html" />
    <meta property="og:site_name" content="{{ ucfirst($appname) }}" />

    <title>Online Astrology Consultation, Ask an {{ ucfirst($professionTitle) }} - {{ ucfirst($appname) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link href="index.html" rel="canonical" />


    {{-- <link href="{{asset($logo->value)}}" rel="shortcut icon" type="image/x-icon" /> --}}

    <link rel="preconnect" as="font"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0"
        type="font/woff2" crossorigin />

    <link rel="stylesheet" href="{{ asset('public/frontend/css/newcss.css') }}">

    <link rel="stylesheet" href="{{ asset('public/frontend/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link href="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/css/font/stylesheet.css') }}"
        rel="stylesheet" />




    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/fontawesome.min.css" />
    </noscript>

    <link rel="stylesheet"
        href="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/css/carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/css/carousel/owl.carousel.css') }}">
    <link href="{{ asset('public/frontend/select2/npm/select2@4.1.0-rc.0/dist/css/select2.min.css') }}"
        rel="stylesheet" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.min.css"
        integrity="sha512-OWGg8FcHstyYFwtjfkiCoYHW2hG3PDWwdtczPAPUcETobBJOVCouKig8rqED0NMLcT9GtE4jw6IT1CSrwY87uw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="{{ asset('public/build/assets/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="preload" href="https://translate.google.com/translate_a/element.js?cb=googleTranslateInit"
        as="script">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    @php
        $logo = DB::table('systemflag')->where('name', 'partnerLogo')->select('value')->first();
        $appName = DB::table('systemflag')->where('name', 'AppName')->select('value')->first();
        $countries = DB::table('countries')->orderByRaw('CASE WHEN phonecode = 91 THEN 0 ELSE 1 END')->get();
    @endphp

    <style>
        .select2-selection__rendered {
            margin-top: 5px !important;
        }

        .login-offer-bg {
            background: #EE4E5E;
            padding: 0 !important;
            padding-top: 20px !important;
            padding-bottom: 0 !important;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            padding-bottom: 10px !important;
        }

        /* Hide number arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
            /* Firefox */
        }
    </style>
</head>

<body>



    <div class="login-offer mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <!-- Box Container -->
                    <div class="login-offer-bg">
                        <p class="text-white font-22 text-center font-weight-bold p-0 m-0 offertxt1"> <img
                                src="{{ asset($logo->value) }}" alt="{{ $appName->value }}" class="img-fluid mb-2 mr-3"
                                width="60" height="60"> Give Consultation to Users</p>
                    </div>
                    <div class="bg-white p-4 shadow-sm" style="border: 1px solid #ddd;border-bottom-left-radius:10px;border-bottom-right-radius:10px">
                        <!-- Sign In Header -->
                        <div class="text-center font-22 d-flex align-items-center justify-content-center">

                            <h3 class="font-weight-bold mb-0 mt-2 mb-3">{{ ucfirst($professionTitle) }} Sign In</h3>
                        </div>
                        <div>
                            <p class="colorblack text-center pb-md-0 pb-2 mb-0">Enter your mobile number to continue</p>
                        </div>

                        <!-- Mobile Number Input Section -->
                        <div class="pt-4 p-2">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="d-flex inputform country-dropdown-container p-1" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;" id="header-country-dropdown-container">
                                        <!-- Country Code Dropdown -->
                                        <select class="form-control select2" id="countryCode" name="countryCode" style="border: none; border-right: 1px solid #ddd; border-radius: 0; width: 20%;">
                                            @foreach ($countries as $country)
                                                <option data-country="in" value="+{{ $country->phonecode }}" data-ucname="India">+{{ $country->phonecode }} {{ $country->iso }}</option>
                                            @endforeach
                                        </select>
                                        <!-- Mobile Number Input -->
                                        <input class="form-control mobilenumber text-box single-line" id="contactNo" maxlength="12" name="contactNo" placeholder="Enter Mobile Number" type="number" value="" style="border: none; border-radius: 0; width: 90%;">
                                        <input type="hidden" id="validOtp" value="" />
                                    </div>

                                    <!--<span class="text-danger field-validation-error ContactMobile-error" style="display: none">Please Enter Your Mobile Number</span>-->
                                    <span class="text-danger field-validation-error otp-error" id="mobileMessage"></span>
                                </div>
                            </div>

                            <!-- Get OTP Button -->
                            <div class="form-group text-center">
                                <button class="font-weight-bold ml-0 w-100 btn btn-chat" id="loaderOtpLogin" type="button" style="display:none;" disabled="">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </button>
                                <!--<input type="button" id="getOtp" value="Get OTP" class="font-weight-bold ml-0 w-100 btn btn-chat valid" aria-invalid="false" onclick="phoneAuth()">-->
                                <input type="button" id="sendOtpBtn" value="Send OTP" class="font-weight-bold ml-0 w-100 btn btn-chat valid" aria-invalid="false">
                            </div>
                        </div>

                        <!-- Continue With Gmail Button -->
                        <div class="container mt-4 mb-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <button
                                        class="btn btn-danger w-100 d-flex align-items-center justify-content-center"
                                        id="googleLoginBtn">
                                        <i class="fa-solid fa-envelope mr-2"></i>
                                        <span>Continue With Gmail</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-group">
                            <div class="col-md-11 list-inline-item ml-md-3 ml-sm-0">
                                <p class="text-dark font-13 text-center pb-md-0 pb-2 mb-0">
                                    By signing in, you agree to our&nbsp;<a class="text-dark font-13"
                                        style="color:#EE4E5E !important"
                                        href="{{ route('front.astrologerTermsCondition') }}" target="_blank">Terms Of
                                        Use</a>&nbsp;and&nbsp;<a class="text-dark font-13"
                                        style="color:#EE4E5E !important"
                                        href="{{ route('front.astrologerPrivacyPolicy') }}" target="_blank">Privacy
                                        Policy</a>
                                </p>
                            </div>
                        </div>
                          <!-- Register Link -->
                        <div class="text-center mt-3 mb-2">
                            <p class="font-14 text-dark mb-0">
                                Don't have an account?
                                <a href="{{ route('front.astrologerregister') }}" class="font-weight-bold" style="color:#EE4E5E !important; text-decoration: none;">
                                    Register as {{ ucfirst($professionTitle) }}
                                </a>
                            </p>
                        </div>

                        <!-- OTP Input Section (Initially Hidden) -->
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <!--<div id="otpInputGroup" style="display: none;">-->
                                <div id="otpInputGroup" class="d-none">
                                    <div class="col-md-12 text-center pb-2 pb-md-4">
                                        <h3 class="font-22 font-weight-bold">OTP Verification</h3>
                                    </div>
                                    <div class="otpheader pb-2 align-items-center">
                                        <!--Enter 6 digit code sent to Your Number.<a href="#" onclick="editMobile()" class="pl-1 font-14 text-danger">Edit</a>-->
                                        Enter 6 digit code sent to Your Number.<a href="#" onclick="editMobileNumber()" class="pl-1  font-14 text-danger">Edit</a>
                                    </div>
                                    <div class="form-group">
                                        <!--<input class="form-control" id="otp" name="otp" placeholder="Enter OTP" type="text" maxlength="6" oninput="validateOTPInput(this)">-->
                                        <input class="form-control" id="otpCode" name="otp" placeholder="Enter OTP" type="text" maxlength="6">
                                        <!--<span class="text-danger field-validation-error otp-error" style="display: none" id="otpError"></span>-->
                                        <span class="text-danger field-validation-error otp-error" id="otpLoginMessage"></span>
                                    </div>
                                        <input id="contactNo" name="contactNo" type="hidden" value="" />
                                        <input id="countryCode" name="countryCode" type="hidden" value="" />
                                    <div class="form-group text-center">
                                        <div class="my-0 w-100">
                                            <button class="font-weight-bold w-100 btn btn-chat ml-0" id="loaderVerifyLogin" type="button" style="display:none" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Loading...
                                            </button>

                                            <input type="button" value="Submit" class="btn btn-chat font-weight-bold w-100 ml-0 mt-3" id="verifyOtpBtn">
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

</body>

</html>


<script
    src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/carousel/owl.carousel.min.js') }}">
</script>
<script src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/carousel/owl.carousel.js') }}">
</script>
<script src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/popper.min.js') }}"></script>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
<script async src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/bootstrap.min.js') }}">
</script>
<script async src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/popper.min.js') }}">
</script>
<script async src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/js/momentum.js') }}"></script>
<script async src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/js/bootstrap-datepicker.min.js') }}">
</script>
<script async src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/js/js.cookie.min.js') }}"></script>
<script async src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/bundle/js/AfterLoginJs.js') }}">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.min.js"
    integrity="sha512-FbWDiO6LEOsPMMxeEvwrJPNzc0cinzzC0cB/+I2NFlfBPFlZJ3JHSYJBtdK7PhMn0VQlCY1qxflEG+rplMwGUg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/duDatepicker.js') }}"></script>

<script>

$(document).ready(function () {
    $('#sendOtpBtn').click(function () {
        const mobile = $('#contactNo').val();
         const countryCode = $('#countryCode').val().trim().replace('+', '');
        $('#otpLoginMessage').text('');
        $('#mobileMessage').text('')
        $("#validOtp").val("");
        $("#otpCode").val("")


        if (mobile?.length <= 0) {
            $('#mobileMessage').text('Please Enter Your Mobile Number');
            return
        }

        $.ajax({
            url: '{{ route("api.checkContactAndSendOTP") }}',
            method: 'POST',
            data: {
                contactNo: mobile,
                fromApp: "astrologer",
                countryCode: countryCode,
                type: "login",
                fromWeb: 1
            },
            success: function (res) {
                console.log("ddd :: ", res)
                makeAction(res.status == 200)
                if (res.status == 200) {
                    $('#mobileMessage').text('');
                    $("#validOtp").val(res.otp)
                    $('#otpInputGroup').removeClass('d-none');
                    $('#sendOtpBtn').addClass('d-none');
                } else {
                    toastr.error(res.message);
                    $('#mobileMessage').html(res.message);
                    $("#validOtp").val("")
                    $('#otpInputGroup').addClass('d-none');
                    $('#sendOtpBtn').removeClass('d-none');
                }
            },
            error: function (e) {
                console.log("mobile error :: ", mobile, e)
                toastr.error(e?.responseJSON?.message);
                $('#mobileMessage').html(e?.responseJSON?.message);
            }
        });
    });


    $('#verifyOtpBtn').click(function () {
        const mobile = $('#contactNo').val();
        const otpCode = $('#otpCode').val();
        const code = $('#validOtp').val();
        const countryCode = $('#countryCode').val().trim();

        if (otpCode?.length <= 0) {
            $("#otpLoginMessage").html("Enter OTP")
            return
        }


        if (atob(code) != otpCode) {
            $("#otpLoginMessage").html("Invalid OTP")
            return
        }

        $("#otpLoginMessage").html("")


        $.ajax({
            url: '{{ route("front.verifyOTLAstro") }}',
            method: 'POST',
            data: {
                contactNo: mobile,
                otp: otpCode,
                validOtp: code,
                countryCode: countryCode,
                country: '',
                fromWeb: 1
            },
            success: function (res) {
                console.log(" res :: ", res)
                if (res.status == 200) {
                    location.reload();
                } else {
                    toastr.error(res.message);
                    $('#mobileMessage').text(res.message);
                    console.log("Invalid OTP.")
                }
            },
            error: function (e) {
                console.log("Error verifying OTP.")
                toastr.error(e?.responseJSON?.message);
                $('#mobileMessage').html(e?.responseJSON?.message);
            }
        });
    });

});

function makeAction(action = false) {
    if (action) {
        $("#contactNo").attr("readonly", true)
        $("#contactNo").attr("disabled", true)
        $("#countryCode").attr("readonly", true)
        $("#countryCode").attr("disabled", true)
        $("#header-country-dropdown-container").css('background-color', '#e9ecef')
    }
    if (!action) {
        $("#contactNo").removeAttr("readonly");
        $("#contactNo").removeAttr("disabled");
        $("#countryCode").removeAttr("readonly");
        $("#countryCode").removeAttr("disabled");
        $("#header-country-dropdown-container").css('background-color', '')
    }
}

function editMobileNumber() {
    $("#validOtp").val("")
    $('#otpInputGroup').addClass('d-none');
    $('#sendOtpBtn').removeClass('d-none');
    makeAction(false)
}
</script>
<script>
    $(document).ready(function() {

        $('#countryCode').select2({
            dropdownAutoWidth: true,
            width: 'resolve',
            minimumResultsForSearch: 0
        });

    });
</script>

@if (request('error'))
    <script>
        toastr.error("{{ request('error') }}");

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>
@endif
@php
 $appId = DB::table('systemflag')->where('name', 'firebaseappId')->first();
    $measurementId = DB::table('systemflag')->where('name', 'firebasemeasurementId')->first();
    $messagingSenderId = DB::table('systemflag')->where('name', 'firebasemessagingSenderId')->first();
    $storageBucket = DB::table('systemflag')->where('name', 'firebasestorageBucket')->first();
    $projectId = DB::table('systemflag')->where('name', 'firebaseprojectId')->first();
    $authDomain = DB::table('systemflag')->where('name', 'firebaseauthDomain')->first();
    $databaseURL = DB::table('systemflag')->where('name', 'firebasedatabaseURL')->first();
    $apiKey = DB::table('systemflag')->where('name', 'firebaseapiKey')->first();
@endphp

<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-storage.js"></script>



<script>
    var firebaseConfig = {
        apiKey: "{{ $apiKey->value }}",
        databaseURL: "{{ $databaseURL->value }}",
        authDomain: "{{ $authDomain->value }}",
        projectId: "{{ $projectId->value }}",
        storageBucket: "{{ $storageBucket->value }}",
        messagingSenderId: "{{ $messagingSenderId->value }}",
        appId: "{{ $appId->value }}",
        measurementId: "{{ $measurementId->value }}"
    };

    firebase.initializeApp(firebaseConfig);
</script>

  <script>
    document.getElementById('googleLoginBtn').addEventListener('click', async function () {
        const provider = new firebase.auth.GoogleAuthProvider();
        firebase.auth().signInWithPopup(provider)
        .then(async (result) => {
            const idToken = await result.user.getIdToken();

            // Send token to backend
            console.log(" response 1539 :: ", result.user)

            $.ajax({
                url: '{{ route("front.verifyOTLAstro") }}',
                method: 'POST',
                data: {
                    fromWeb: 1,
                    isGoogleLogin: 1,
                    email: result.user?.email,
                    name: result.user?.displayName
                },
                success: function (res) {
                    console.log(" res :: ", res)
                    if (res.status == 200) {
                        location.reload();
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function (e) {
                    toastr.error(e?.responseJSON?.message);
                }
            });
        })
        .catch((error) => {
            toastr.error(error?.responseJSON?.message);
        });
    });
</script>
