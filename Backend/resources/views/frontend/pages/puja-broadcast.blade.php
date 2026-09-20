@extends('frontend.layout.master')


@section('content')
<style>
@media (max-width: 768px) {
    .product-detail .row {
        display: flex;
        flex-direction: column-reverse; /* Reverse the order for mobile */
    }

    .product-large-image {
        margin-top: 20px;
    }
    
    .carousel-inner img {
    height: 300px !important;
   }
   .carousel-control-next, .carousel-control-prev {
       
       top:20% !important;
   }
}

</style>

<main role="main" class="margin-top-header">
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
                            <a href="{{ route('front.pujaList') }}" class="text-white text-decoration-none">Puja</a>
                            <i class="fa fa-chevron-right"></i>
                            Puja Details - {{$puja->puja_title}}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
<div class="container">
    <div class="product-detail bg-white py-4 mb-5">
        <div class="row py-4">
            <div class="col-12 col-md-7 d-flex align-items-center">
                <div id="productCarousel" class="carousel slide product-large-image position-relative" data-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($puja->puja_images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img id="PujaImg{{ $index + 1 }}" class="rounded-m" src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $image }}')" />
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-5 px-4 mt-3 mt-md-0">
                <div>
                    <span class="font-weight-semi-bold border-bottom border-gray">{{ $puja->category->name }}</span>
                </div>
                <div class="mt-3">
                    <span class="puja-title font-weight-bold font-26">{{ $puja->puja_title }}</span>
                </div>
                <div class="mt-3">
                    <span class="mt-2 puja-subTitle text-secondary font-20 font-weight-semi-bold">{{ $puja->puja_subtitle }}</span>
                </div>
                <div class="mt-3 d-flex align-items-start">
                    <i class="fa-solid fa-place-of-worship me-2 mr-2"></i>
                    <div>
                        <span class="d-block" style="font-size: 15px;">{{ $puja->puja_place }}</span>
                    </div>
                </div>
                <?php
                $startDatetime = \Carbon\Carbon::parse($puja->puja_start_datetime);
                $endDatetime = \Carbon\Carbon::parse($puja->puja_end_datetime);
                $startDateDisplay = $startDatetime->format('j M, D');
                $endDateDisplay = $endDatetime->format('j M, D');
                $startTimeDisplay = $startDatetime->format('H:i');
                $endTimeDisplay = $endDatetime->format('H:i');
                $sameDate = $startDatetime->isSameDay($endDatetime);
                ?>
                <div class="mt-3 d-flex align-items-start">
                    <i class="fa fa-calendar me-2 mr-2" aria-hidden="true"></i>
                    <div>
                        <span class="d-block" style="font-size: 15px;">{{ $sameDate ? $startDateDisplay . ' ' . $startTimeDisplay . ' to ' . $endTimeDisplay : $startDateDisplay . ' ' . $startTimeDisplay . ' to ' . $endDateDisplay . ' ' . $endTimeDisplay }}</span>
                    </div>
                </div>

                <div class="footer mt-5 text-center">
                    <!-- Image above the text -->
                    <img src="{{ asset('public/frontend/homeimage/360.png') }}" alt="Completion Image" class="mb-3" style="max-width: 150px;">
                    <div class="message font-30 text-success font-weight-bold">Puja has been finished !</div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            margin: 20, // Adds space between items
            nav: false,
            dots: true,
            center: true, // Ensures the nav buttons are centered
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
            event.preventDefault();
            container.scrollBy({
                top: event.deltaY,
                behavior: 'smooth'
            });
        });
    });

</script>


@endsection