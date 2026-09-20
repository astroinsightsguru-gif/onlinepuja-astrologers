<!DOCTYPE html>
<html lang="en">

<head>
    @php
    $logo = $systemFlags->get('PartnerLogo');
@endphp
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta
        content="Ask an online {{ucfirst($professionTitle)}} and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best {{ucfirst($professionTitle)}}s' team."
        name="description" />
    <meta property="Keywords" content="" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="" />
    <meta name="twitter:description"
        content="Ask an online {{$professionTitle}} and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best {{ucfirst($professionTitle)}}s' team." />
    <meta name="twitter:title" content="Online Astrology Consultation, Ask an {{ucfirst($professionTitle)}} - {{ucfirst($appname)}}" />
    <meta name="twitter:image" content="{{asset($logo->value)}}" />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="Online Astrology Consultation, Ask an {{ucfirst($professionTitle)}} - {{ucfirst($appname)}}" />
    <meta property="og:description"
        content="Ask an online {{$professionTitle}} and get instant consultation on top Astrology portal. Accurate astrology predictions and solutions by India's best {{ucfirst($professionTitle)}}s' team." />
    <meta property="og:image" content="{{asset($logo->value)}}" />
    <meta property="og:url" content="index.html" />
    <meta property="og:site_name" content="{{ucfirst($appname)}}" />

    <title>Online Astrology Consultation, Ask an {{ucfirst($professionTitle)}} - {{ucfirst($appname)}}</title>

    <link href="{{request()->url()}}" rel="canonical" />

    <link href="{{asset($logo->value)}}" rel="shortcut icon" type="image/x-icon" />


    <link rel="preconnect" as="font"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0"
        type="font/woff2" crossorigin />

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
    <link rel="stylesheet" href="{{ asset('public/frontend/css/newcss.css') }}">

    <link rel="preload" href="https://translate.google.com/translate_a/element.js?cb=googleTranslateInit" as="script">
	<style>
        body.Is-Offer{
            top: 0px !important;
        }
    </style>
	<script src="https://cdn.jsdelivr.net/npm/@100mslive/hms-video-store@latest/dist/index.min.js"></script>


</head>
@include('frontend.astrologers.layout.header')

<body class="english country-in Is-Offer ">
    <div class="wrapper">

        @yield('content')

    </div>
</body>

@include('frontend.astrologers.layout.footer')

@yield('scripts')


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



<script src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/duDatepicker.js') }}"></script>


@php
    $webLanguage = $systemFlags->get('WebLanguage');
    $selectedLanguages = json_decode($webLanguage->value ?? '[]', true) ?: [];
    $includedLanguages = implode(',', $selectedLanguages);
@endphp

{{-- <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateInit"></script> --}}


<script>
              (function() {
    // Check if the script is already loaded
    if (typeof google === 'undefined' || !google.translate || !google.translate.TranslateElement) {
        var gtScript = document.createElement('script');
        gtScript.type = 'text/javascript';
        gtScript.async = true;
        gtScript.defer = true;
        gtScript.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateInit';
        document.head.appendChild(gtScript);
    } else {
        console.log('Google Translate script is already loaded.');
    }
})();

var translateInitialized = false;

function googleTranslateInit() {
    if (translateInitialized) {
        console.log('Google Translate already initialized.');
        return; // Prevent multiple initializations
    }

    try {
        new google.translate.TranslateElement(
            {
                pageLanguage: 'en',
                includedLanguages: '{{ $includedLanguages }}',
            },
            'google_translate_button'
        );
        translateInitialized = true; // Mark as initialized

        // Use MutationObserver to detect when the dropdown is rendered
        const observer = new MutationObserver((mutationsList, observer) => {
            const selectLanguageElement = document.querySelector('.goog-te-combo');
            if (selectLanguageElement && selectLanguageElement.options.length > 0) {
                // Change the default text from "Select Language" to "English"
                selectLanguageElement.options[0].text = 'English';
                observer.disconnect(); // Stop observing once the change is made
            }
        });

        // Start observing the document for changes
        observer.observe(document.body, { childList: true, subtree: true });

    } catch (error) {
        console.error('Google Translate failed to initialize:', error);
    }
}
    </script>


@if (astroauthcheck())
    @php
        $astrologerId = astroauthcheck()['astrologerId'];
        $assetBase = asset('');
        $blankProfile = asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/blank-profile.png');
    @endphp
    <!-- Global Notification Sound -->
    <audio id="global-notification-sound" src="{{ asset('public/sound/ringtone-126505.mp3') }}" loop></audio>

 <script>
    $(document).ready(function() {
        var chatCount = 0;
        var callCount = 0;
        var reportCount = 0;

        function manageGlobalSound() {
            var sound = document.getElementById('global-notification-sound');
            if (!sound) return;

            // Check if there's any manual override (e.g., from Test Sounds button)
            if (window.manualSoundPlaying) return;

            if (chatCount + callCount > 0) {
                if (sound.paused) {
                    sound.play().catch(function(e) {
                        console.log("Autoplay blocked. User interaction required.");
                    });
                }
            } else {
                if (!sound.paused) {
                    sound.pause();
                    sound.currentTime = 0;
                }
            }
        }

        function fetchGlobalRequests() {
            // Fetch Chat
            $.ajax({
                url: "{{ route('astrologer.chat.requests') }}",
                method: 'POST',
                data: {
                    astrologerId: "{{ $astrologerId }}"
                },
                success: function(data) {
                    chatCount = (data.recordList && data.recordList.chatRequest) ? data.recordList.chatRequest.length : 0;
                    if (typeof window.updateChatRequests === 'function') {
                        window.updateChatRequests(data.recordList.chatRequest);
                    }
                    manageGlobalSound();
                }
            });

            // Fetch Call
            $.ajax({
                url: "{{ route('astrologer.call.requests') }}",
                method: 'POST',
                data: {
                    astrologerId: "{{ $astrologerId }}"
                },
                success: function(data) {
                    callCount = 0;
                    if (data.recordList && data.recordList.length > 0) {
                        data.recordList.forEach(function(request) {
                            if (request.IsSchedule != 1) {
                                callCount++;
                            } else {
                                // It's a scheduled appointment request
                                // Check if we already showed SweetAlert for this specific callId
                                let shownAppointments = JSON.parse(localStorage.getItem('shown_appointments') || '{}');
                                if (!shownAppointments[request.callId]) {
                                    shownAppointments[request.callId] = true;
                                    localStorage.setItem('shown_appointments', JSON.stringify(shownAppointments));

                                    Swal.fire({
                                        title: '📅 Appointment Request',
                                        html: `You got an appointment request from <strong>${request.name || 'User'}</strong>.<br>Scheduled for: <strong>${request.schedule_datetime || ''}</strong>`,
                                        icon: 'info',
                                        showCancelButton: true,
                                        confirmButtonColor: '#ff8c00',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'View Appointments',
                                        cancelButtonText: 'Dismiss'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "{{ route('astro-appointment') }}";
                                        }
                                    });
                                }

                                if (request.schedule_datetime) {
                                    var scheduleTimeStr = request.schedule_datetime.replace(/-/g, '/');
                                    var scheduleTime = new Date(scheduleTimeStr).getTime();
                                    var currentTime = new Date().getTime();
                                    if (currentTime >= scheduleTime) {
                                        callCount++;
                                    }
                                }
                            }
                        });
                    }
                    if (typeof window.updateCallRequests === 'function') {
                        window.updateCallRequests(data.recordList);
                    }
                    manageGlobalSound();
                }
            });

            // Fetch Report
            $.ajax({
                url: "{{ route('astrologer.report.requests') }}",
                method: 'POST',
                data: {
                    astrologerId: "{{ $astrologerId }}"
                },
                success: function(data) {
                    reportCount = data.recordList ? data.recordList.length : 0;
                    if (typeof window.updateReportRequests === 'function') {
                        window.updateReportRequests(data.recordList);
                    }
                    manageGlobalSound();
                }
            });
        }

        // Start polling if not on chat or call page (or even if we are, to hear other requests)
        fetchGlobalRequests();
        setInterval(fetchGlobalRequests, 6000);
    });

    // Global function to toggle test sound from any page
    window.toggleTestSound = function() {
        var sound = document.getElementById('global-notification-sound');
        if (sound.paused) {
            sound.play();
            window.manualSoundPlaying = true;
            return true;
        } else {
            sound.pause();
            sound.currentTime = 0;
            window.manualSoundPlaying = false;
            return false;
        }
    };
</script>
@endif
</html>
