

<style>
    .text-gray{
        color: #D1D5DB !important;
    }
    .text-gray-dark{
        color: #9CA3AF !important;
    }
</style>
@php
$getAstrologerCategory = $astrologerCategories;
$facebook = $systemFlags->get('Facebook');
$apple = $systemFlags->get('Apple');
$website = $systemFlags->get('Website');
$youtube = $systemFlags->get('Youtube');
$linkedIn = $systemFlags->get('LinkedIn');
$pintrest = $systemFlags->get('Pintrest');
$instagram = $systemFlags->get('Instagram');
$whatsapp = $systemFlags->get('Whatsapp');
$telegram = $systemFlags->get('Telegram');
$twitter = $systemFlags->get('Twitter');
$playstore = $systemFlags->get('PartnerPlayStore');
$appstore = $systemFlags->get('PartnerAppStore');
@endphp
<div id="footer" style="background: linear-gradient(180deg, #3b3b3b 0%, #202020 100%); color: #cfcfcf;">
    <section class="pt-5 pb-4">
        <div class="container">
            <div class="row text-center text-md-start gy-4">
                <!-- MENU -->
                <div class="col-6 col-md-3">
                    <h5 class="text-white border-bottom border-secondary pb-2 mb-3 font-16">MENU</h5>
                    <ul class="list-unstyled" style="font-size: 14px;">
                        <li class="p-1"><a class="footer-link" href="{{ route('front.astrologers.getkundali') }}">Kundli</a></li>
                        <li class="p-1"><a class="footer-link" href="{{ route('front.astrologers.kundaliMatch') }}">Kundli Matching</a></li>
                        <li class="p-1"><a class="footer-link" href="{{ route('front.astrologers.getPanchang') }}">Today's Panchang</a></li>
                    </ul>
                </div>

                <!-- HOROSCOPE -->
                <div class="col-6 col-md-3">
                    <h5 class="text-white border-bottom border-secondary pb-2 mb-3 font-16">Horoscope</h5>
                    <ul class="list-unstyled" style="font-size: 14px;">
                        <li class="p-1"><a class="footer-link" href="{{ route('front.astrologers.horoScope') }}">Daily Horoscope</a></li>
                        <li class="p-1"><a class="footer-link" href="{{ route('front.astrologers.horoScope') }}">Weekly Horoscope</a></li>
                        <li class="p-1"><a class="footer-link" href="{{ route('front.astrologers.horoScope') }}">Yearly Horoscope</a></li>
                    </ul>
                </div>

                <!-- LINKS -->
                <div class="col-6 col-md-3">
                    <h5 class="text-white border-bottom border-secondary pb-2 mb-3 font-16">LINKS</h5>
                    <ul class="list-unstyled" style="font-size: 14px;">
                        <li class="p-1"><a class="footer-link" href="{{route('front.astrologers.getBlog')}}">Go to Blog</a></li>
                        <li class="p-1"><a class="footer-link" href="{{route('front.astrologers.privacyPolicy')}}">Privacy Policy</a></li>
                        <li class="p-1"><a class="footer-link" href="{{route('front.astrologers.contact')}}">Contact Us</a></li>
                    </ul>
                </div>

                <!-- DOWNLOAD APP -->
                <div class="col-6 col-md-3">
                    <h5 class="text-white border-bottom border-secondary pb-2 mb-3 font-16">Download Our Apps</h5>
                    <a href="{{$playstore->value}}" class="d-block mt-3">
                        <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/google-play.png')}}" alt="Google Play" class="img-fluid" width="180" height="54" loading="lazy">
                    </a>
                    <a href="{{$appstore->value}}" class="d-block mt-3">
                        <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/app-store.png')}}" alt="App Store" class="img-fluid" width="180" height="54" loading="lazy">
                    </a>

                    <!-- SOCIAL ICONS -->
                    <div class="d-flex flex-wrap mt-3 justify-content-center justify-content-md-start">
                        @if(!empty($facebook->value))
                        <a class="social-icon" target="_blank" href="{{$facebook->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/fb.svg')}}" width="30" height="30" alt="Facebook">
                        </a>
                        @endif
                        @if(!empty($twitter->value))
                        <a class="social-icon" target="_blank" href="{{$twitter->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/twitter.svg')}}" width="30" height="30" alt="Twitter">
                        </a>
                        @endif
                        @if(!empty($linkedIn->value))
                        <a class="social-icon" target="_blank" href="{{$linkedIn->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/linkedin.svg')}}" width="30" height="30" alt="LinkedIn">
                        </a>
                        @endif
                        @if(!empty($instagram->value))
                        <a class="social-icon" target="_blank" href="{{$instagram->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/insta.svg')}}" width="30" height="30" alt="Instagram">
                        </a>
                        @endif
                        @if(!empty($youtube->value))
                        <a class="social-icon" target="_blank" href="{{$youtube->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/youtube.svg')}}" width="30" height="30" alt="YouTube">
                        </a>
                        @endif
                        @if(!empty($pintrest->value))
                        <a class="social-icon" target="_blank" href="{{$pintrest->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/pinterest.svg')}}" width="30" height="30" alt="Pinterest">
                        </a>
                        @endif
                        @if(!empty($whatsapp->value))
                        <a class="social-icon" target="_blank" href="{{$whatsapp->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/whatsapp.svg')}}" width="30" height="30" alt="WhatsApp">
                        </a>
                        @endif
                        @if(!empty($telegram->value))
                        <a class="social-icon" target="_blank" href="{{$telegram->value}}" rel="nofollow">
                            <img src="{{asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/telegram.svg')}}" width="30" height="30" alt="Telegram">
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- COPYRIGHT -->
    <div class="text-center py-3" style="background: #2b2b2b;">
        <small>
            Copyright © 2020-{{date('Y')}} {{ucfirst($appname)}}. All Rights Reserved |
            <a class="footer-link text-decoration-none" href="{{route('front.astrologers.privacyPolicy')}}">Privacy Policy</a> |
            <a class="footer-link text-decoration-none" href="{{route('front.astrologers.refundPolicy')}}">Refund Policy</a> |
            <a class="footer-link text-decoration-none" href="{{route('front.astrologers.termscondition')}}">Terms of Service</a> |
            <a class="footer-link text-decoration-none" href="{{route('front.astrologers.contact')}}">Contact Us</a>
        </small>
    </div>
</div>

<!-- Footer Styles -->
<style>
    .footer-link {
        color: #bfbfbf;
        transition: color 0.3s ease, transform 0.3s ease;
    }
    .footer-link:hover {
        color: #f7b731;
        transform: translateX(3px);
        text-decoration: underline;
    }
    .social-icon {
        margin: 4px;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .social-icon:hover {
        transform: scale(1.15);
        opacity: 0.8;
    }
    @media (max-width: 768px) {
        #footer h5 {
            font-size: 15px;
        }
        .footer-link {
            font-size: 13px;
        }
        .social-icon img {
            width: 26px;
            height: 26px;
        }
    }
</style>
