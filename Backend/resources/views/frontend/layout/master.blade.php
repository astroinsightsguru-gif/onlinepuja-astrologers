<!DOCTYPE html>
<html lang="en">

<head>
@php
    $logo = $systemFlags->get('AdminLogo');
    $walletTypeFlag = $systemFlags->get('walletType');
@endphp



    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- Primary Meta Tags -->
    <title>@hasSection('title')@yield('title') - {{ ucfirst($appname) }}@else Online Puja & Pandit Booking in Varanasi | Authentic Vedic Pujas - {{ ucfirst($appname) }}@endif</title>
    <meta name="description" content="@yield('meta_description', 'Book authentic Vedic pujas in Varanasi (Kashi Vishwanath, Ganga Ghats) performed by experienced Vedic Pandits. Receive live sankalp video and sanctum prasad at your doorstep.')" />
    <meta name="keywords" content="@yield('meta_keywords', 'online puja varanasi, kashi vishwanath rudrabhishek booking, pandit in varanasi, online pandit booking, dev deepawali deepdaan, vedic puja service')" />

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', request()->url())" />
    <link href="{{ asset($logo->value ?? 'build/assets/images/logo.png') }}" rel="shortcut icon" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:title" content="@hasSection('title')@yield('title') - {{ ucfirst($appname) }}@else Online Puja & Pandit Booking in Varanasi | Authentic Vedic Pujas - {{ ucfirst($appname) }}@endif" />
    <meta property="og:description" content="@yield('meta_description', 'Book authentic Vedic pujas in Varanasi performed by experienced Vedic Pandits. Receive live sankalp video and sanctum prasad.')" />
    <meta property="og:image" content="@yield('og_image', asset($logo->value ?? 'build/assets/images/logo.png'))" />
    <meta property="og:url" content="{{ request()->url() }}" />
    <meta property="og:site_name" content="{{ ucfirst($appname) }}" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@hasSection('title')@yield('title') - {{ ucfirst($appname) }}@else Online Puja & Pandit Booking in Varanasi | Authentic Vedic Pujas - {{ ucfirst($appname) }}@endif" />
    <meta name="twitter:description" content="@yield('meta_description', 'Book authentic Vedic pujas in Varanasi performed by experienced Vedic Pandits.')" />
    <meta name="twitter:image" content="@yield('og_image', asset($logo->value ?? 'build/assets/images/logo.png'))" />

    <!-- Dynamic Schema Markup Container -->
    @yield('schema_markup')

    <link rel="preconnect" as="font"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0"
        type="font/woff2" crossorigin />

        <!-- Google Fonts: Outfit and Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('public/frontend/css/newcss.css?v=1.1.0') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('public/frontend/css/app.min.css?v=1.1.0') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/css/app.css?v=1.1.0') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link href="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/css/font/stylesheet.css') }}"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/fontawesome.min.css" />

    <link rel="stylesheet"
        href="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/css/carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/css/carousel/owl.carousel.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.min.css"
        integrity="sha512-OWGg8FcHstyYFwtjfkiCoYHW2hG3PDWwdtczPAPUcETobBJOVCouKig8rqED0NMLcT9GtE4jw6IT1CSrwY87uw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="{{ asset('public/build/assets/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="preload" href="https://translate.google.com/translate_a/element.js?cb=googleTranslateInit" as="script">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        body.Is-Offer{
            top: 0px !important;
        }
    </style>


</head>

<body class="english country-in Is-Offer ">
    @include('frontend.layout.header')

    <div class="wrapper">

        @yield('content')

    </div>

    @include('frontend.layout.footer')

    @yield('scripts')


<script
    src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/carousel/owl.carousel.min.js') }}">
</script>
<script src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/carousel/owl.carousel.js') }}">
</script>
<script src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/js/popper.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>


@php
    $webLanguage=$systemFlags->get('WebLanguage');
    $selectedLanguages = json_decode($webLanguage->value, true) ?: [];
    $includedLanguages = implode(',', $selectedLanguages);
@endphp


<script>
     AOS.init();

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
</body>
</html>
