@extends('frontend.layout.master')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
use Symfony\Component\HttpFoundation\Session\Session;
$session = new Session();
$token = $session->get('token');
@endphp
<style>
    #loader {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999; /* Ensure it's on top of other content */
    }

    .sf_chat_button {
        display: none;
    }
    .sf_chat_button1 {
        position: fixed;
        bottom: 12px;
        right: 12px;
        z-index: 99;
        font-family: Gilroy, Inter, sans-serif;
    }
    .sf_chat_button1 button {
        border-radius: 50%;
        padding: 0;
        border: none;
        background: none;
    }
    .sf_chat_button1 svg {
        display: inline-block;
    }
</style>
@section('content')


<div id="sf_chat_button1" role="button" class="sf_chat_button1" >
    <button data-bs-toggle="tooltip" title="Chat with master Astrologer" data-bs-placement="top">
        <a class="shadow-md mr-2 mt-10 d-inline checkBalance" id="checkBalance">
            <img src="https://cdn-icons-png.flaticon.com/128/6819/6819661.png" width="50" height="53" alt="">
        </a>
    </button>
  </div>

<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize; ">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Chat With AI Astrologer</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="py-md-3 expert-search-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12" id="experts" style="overflow:hidden;">
                <div id="expert-search" class="my-3 my-md-0">
                    <div class="expert-search-form">
                        <div class="row mx-auto px-2 px-md-0 flex-md-nowrap align-items-center round">
                            <div  class="col-12 col-md-3 col-sm-auto text-left d-flex justify-content-between align-items-center w-100 bg-white px-0">
                                <h1 class="font-22 font-weight-bold">Chat With AI Astrologer</h1>
                                <img src="#" alt="Filter Experts based on Status" width="18" height="18"
                                class="img-fluid filterIcon float-right d-md-none" onClick="fnSearch()" />
                            </div>
                            {{-- <div class="d-flex align-items-center justify-content-end w-100">
                                <a class="btn btn-chat shadow-md mr-2 mt-10 d-inline checkBalance" id="checkBalance">
                                    <img src="http://localhost/onlinepuja-pro-backend/public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/ai.png" width="20" height="20" alt="">
                                    Master AI Astro Chat
                                </a>
                            </div> --}}

                            {{-- <div class="col-ms-12 col-md-3 d-none d-md-block" id="searchExpert">
                                <form action="{{ route('front.chatList') }}" method="GET">
                                    <div class="search-box">
                                        <input value="{{ isset($searchTerm) ? $searchTerm : '' }}"
                                        class="form-control rounded" name="s" placeholder="Search AI Astrologer"
                                        type="search" autocomplete="off">
                                        <button type="submit" class="btn btn-link search-btn" id="search-button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div> --}}
                            {{-- <div class="col-ms-12 col-md-3 d-none d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2 " id="sortExpert">
                                <select class="form-control font13 rounded" name="sortBy" onchange="onSortExpertList()" id="psychicOrderBy">
                                    <option value="1" {{ $sortBy == '1' ? 'selected' : '' }}>Online</option>
                                    <option value="experienceLowToHigh" {{ $sortBy == 'experienceLowToHigh' ? 'selected' : '' }}>Low Experience</option>
                                    <option value="experienceHighToLow" {{ $sortBy == 'experienceHighToLow' ? 'selected' : '' }}>High Experience</option>
                                    <option value="priceLowToHigh" {{ $sortBy == 'priceLowToHigh' ? 'selected' : '' }}> Lowest Price</option>
                                    <option value="priceHighToLow" {{ $sortBy == 'priceHighToLow' ? 'selected' : '' }}> Highest Price</option>
                                </select>
                            </div> --}}
                            {{-- <div class="col-ms-12 col-md-3 d-none d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2" id="filterExpertCategory">
                                <select name="astrologerCategoryId" onchange="onFilterExpertCategoryList()" class="form-control font13 rounded" id="psychicCategories">
                                    <option value="0" {{ $astrologerCategoryId == '0' ? 'selected' : '' }}>All</option>
                                    @foreach ($getAstrologerCategory['recordList'] as $category)
                                    <option value="{{ $category['id'] }}"
                                    {{ $astrologerCategoryId == $category['id'] ? 'selected' : '' }}>
                                    {{ $category['name'] }} </option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container">
    <div class="row">

        <div class="col-sm-12 expert-search-section-height">
            <div id="expert-list" class="py-4 ">

                @foreach ($aiAstrologers as $astro)
                <div id="ATAAIOfferTile" class="psychic-card overflow-hidden expertOnline ask-guruji" data-astro-id="{{ @$astro->id }}">
                    <span class=" must-try-badge font-10 position-absolute font-weight-semi text-center align-items-center justify-content-center text-white">Must Try</span>
                    <ul class="list-unstyled d-flex mb-0">
                        <li class="mr-3 position-relative psychic-presence status-online" data-status="online">
                            <a href="javascript:void(0);">
                                <div class="psyich-img position-relative">
                                    @if ($astro->image)
                                    <img src="{{ asset($astro->image) }}"width="80" height="80"
                                    style="border-radius:50%;" loading="lazy">
                                    @else
                                    <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                                    width="80" height="80" style="border-radius:50%;">
                                    @endif
                                </div>
                            </a>
                            <div class="status-badge specific-Clr-Busy" title="Online"></div>
                            <div class="status-badge specific-Clr-Online" title="Online"></div>
                            <div class="status-badge-txt text-center specific-Clr-Online">
                                <span id=""title="Online" class="status-badge-txt specific-Clr-Online tooltipex">Online</span>
                            </div>
                        </li>

                        <li class="w-100 overflow-hidden">
                            <a href="" class="colorblack font-weight-semi font16 mt-0 ml-0 mr-0 mb-0 p-0 text-capitalize d-block"
                            data-toggle="tooltip" title="">{{ $astro->name }}</a><span
                            class="font-12 d-block color-red">{{ implode(',', $astro->all_skills_names->toArray()) }}</span>
                            <span class="font-12 d-block exp-language">Hindi, English</span>
                            <span class="font-12 d-block"> Exp :{{ $astro->experience }} Years</span>

                            @if ($isFreeAvailable == 1)
                            <span class="font-12 font-weight-semi-bold d-flex">
                                <span class="exprt-price">
                                    <del>{{ $currency['value'] }}{{ $astro->chat_charge }}</del>
                                    /Min
                                </span>
                                <span class="free-badge text-uppercase color-red ml-2">Free</span>
                            </span>
                            @else
                            <span class="font-12 font-weight-semi-bold d-flex">
                                <span class="exprt-price">
                                    {{ $currency['value'] }}{{ $astro->chat_charge }}
                                    /Min
                                </span>
                            </span>
                            @endif
                        </li>
                    </ul>


                    <div class="d-flex align-items-end position-relative">
                        <div class="d-block">
                            <div class="row">
                                <div class="psy-review-section col-6">
                                    <a href="javascript:void(0);">
                                        <span class="colorblack font-12 m-0 p-0 d-block">
                                            Category:

                                            <span class="font-12 font-weight-bold m-0 p-0 color-brown">
                                                {{ @$astro->about }}
                                                {{-- {{  implode(',', $astro->categories_names->toArray()) }} --}}
                                            </span>
                                        </span>
                                    </a>
                                </div>
                                <div class="col-3 ml-5">
                                    <a class="btn-block btn btn-call  align-items-center " role="button" data-toggle="modal"
                                    @if (authcheck()) data-target="#intake" @else data-target="#loginSignUp" @endif>
                                    <i class="fa-regular fa-comment"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
 @php
    $isProfileComplete=false;
    if(authcheck()){
        $user = authcheck()['name'];
        $dob = authcheck()['birthDate'];
        $place_of_birth= authcheck()['birthPlace'];
        $isProfileComplete = $user && $dob && $place_of_birth;
    }
    @endphp

{{-- Intake Form --}}
<div class="modal fade rounded mt-2 mt-md-5 " id="intake" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">
                    Intake Form
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-0 pb-0">
                <div class="bg-white body">
                    <div class="row ">
                        <div class="col-lg-12 col-12 ">
                            <div class="mb-3 ">
                                <form class="px-3 font-14" method="post" id="intakeForm">
                                    @csrf
                                    @if (authcheck())
                                    <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                    @endif
                                    <input type="hidden" name="astrologerId" id="astroId" value="">
                                    <input type="hidden" name="charge" id="astroCharge" value="">
                                    <div class="row">

                                        @if (authcheck())
                                        @if ($isFreeAvailable == false)
                                        <input type="hidden" name="isFreeSession" value="0">
                                        <div class="col-12 py-3">
                                            <div class="form-group mb-0">
                                                <label>Select Time You want to chat<span
                                                    class="color-red">*</span>
                                                </label><br>
                                                <div class="btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-info btn-sm">
                                                        <input type="radio" name="chat_duration"
                                                        id="chat_duration300" required value="300"> 5 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm">
                                                        <input type="radio" name="chat_duration"
                                                        id="chat_duration600" required value="600"> 10 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm">
                                                        <input type="radio" name="chat_duration"
                                                        id="chat_duration900" required value="900"> 15 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm">
                                                        <input type="radio" name="chat_duration"
                                                        id="chat_duration1200" required value="1200"> 20 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm">
                                                        <input type="radio" name="chat_duration"
                                                        id="chat_duration1500" required value="1500"> 25 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm">
                                                        <input type="radio" name="chat_duration"
                                                        id="chat_duration1800" required value="1800"> 30 mins
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <input type="hidden" name="chat_duration" value="300">
                                        <h5 class="text-center pl-5 pt-2">Your free chat is available!</h5>
                                        @endif
                                        @endif

                                    </div>

                                    <div class="col-12 col-md-12 py-3">
                                        <div class="row">
                                            <div class="col-12 pt-md-3 text-center mt-2">
                                                <button class="font-weight-bold ml-0 w-100 btn btn-chat"  id="loaderintakeBtn" type="button" style="display:none;"  disabled>
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span> Loading...
                                                </button>
                                                <button type="submit" class="btn btn-block btn-chat px-4 px-md-5 mb-2" id="intakeBtn">Start Chat
                                                </button>
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
    <div id="loader" style="display: none;" class="text-center">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    
   

</div>



{{-- End Intake form --}}

@endsection


@section('scripts')

<script>
    $('.checkBalance').on('click', function(e) {
        e.preventDefault();
        
        var isProfileComplete = @json($isProfileComplete);

        if (!isProfileComplete) {
            // Profile incomplete, show SweetAlert
            Swal.fire({
                title: 'Profile Incomplete',
                text: 'Your profile is incomplete. Please provide your Date of Birth and Place of Birth.',
                icon: 'warning',
                confirmButtonText: 'Update Profile',
                showCancelButton: true,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to profile update page
                    window.location.href = '/my-account'; // Adjust to your profile update route
                }
            });
          
        }else{

        $.ajax({
            url: '{{ route("check.user.balance") }}',
            method: 'GET',
            success: function(response) {

                localStorage.removeItem('masterSubmitting');
                localStorage.removeItem('refreshRedirectMaster');
                localStorage.removeItem('timer');
                localStorage.removeItem('balance');
                localStorage.removeItem('reloadAftSubmit');

                if (response.status === 'success') {

                    console.log(response.balance)
                    if(response.balance !== null){
                        Swal.fire({
                            icon: 'question',
                            title: 'Confirm Action',
                            text: response.message,
                            showCancelButton: true,
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Hold on!',
                                    text: 'Please do not refresh the page.',
                                    showCancelButton: true,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('master.chat.page') }}";
                                    }
                                });
                            }
                        });
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hold on!',
                            text: 'Please do not refresh the page.',
                            showCancelButton: true,
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('master.chat.page') }}";
                            }
                        });
                    }
                } else if (response.status === 'warning') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: response.message
                    });
                } else if (response.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: response.message,
                        confirmButtonText: 'Log In',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#loginSignUp').modal('show');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: 'Please try again later.'
                });
            }
        });
        }
    });

</script>

<script>
    $('.btn-call').on('click', function() {
        var astrologerCard = $(this).closest('.psychic-card');
        var astrologerId = astrologerCard.data('astro-id');
        var astroChargeText = astrologerCard.find('.exprt-price').text().trim();

        // Extract numerical value from the charge text
        var astroCharge = parseFloat(astroChargeText.match(/[\d.]+/));

        // Set values to hidden fields
        $('#astroId').val(astrologerId);
        $('#astroCharge').val(astroCharge);

    });


</script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        $('#intakeBtn').click(function(e) {
            e.preventDefault();

            var form = document.getElementById('intakeForm');
            if (form.checkValidity() === false) {
                form.reportValidity();
                return;
            }
            

            

            $('#intakeBtn').hide();
            $('#loaderintakeBtn').show();
            setTimeout(function() {
                $('#intakeBtn').show();
                $('#loaderintakeBtn').hide();
            }, 500);

            var astrocharge = $("#astroCharge").val();
            var formData = $('#intakeForm').serialize();

            // Parse form data as URL parameters
            var urlParams = new URLSearchParams(formData);
            var chat_duration = parseInt(urlParams.get('chat_duration'));
            var chat_duration_minutes = Math.ceil(chat_duration / 60);
            var total_charge = astrocharge * chat_duration_minutes;
            var isFreeAvailable = "{{ $isFreeAvailable }}";
            var wallet_amount = "{{ $wallet_amount }}";

                        // Check profile completion from the Blade variable
            var isProfileComplete = @json($isProfileComplete);

            if (!isProfileComplete) {
                // Profile incomplete, show SweetAlert
                Swal.fire({
                    title: 'Profile Incomplete',
                    text: 'Your profile is incomplete. Please provide your Date of Birth and Place of Birth.',
                    icon: 'warning',
                    confirmButtonText: 'Update Profile',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to profile update page
                        window.location.href = '/my-account'; // Adjust to your profile update route
                    }
                });
           
            }else{
            
            $.ajax({
                url: "{{ route('ai.chat.page') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.warning) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: response.warning
                        });
                        // toastr.warning(response.warning);
                        $('#intakeBtn').show();
                        $('#loaderintakeBtn').hide();
                    } else if (response.success) {
                        toastr.warning(response.success);
                        $('#intakeBtn').hide();
                        $('#loaderintakeBtn').show();

                        setTimeout(function() {
                            location.href = "{{ route('ai.chatting.page') }}?&astrologerId=" + response.astrologerId + "&chat_duration=" + response.chat_duration;
                        }, 500);
                        // location.href = "{{ route('ai.chatting.page') }}?userId=" + response.userId + "&astrologerId=" + response.astrologerId + "&charge=" + response.charge + "&chat_duration=" + response.chat_duration + "&userName=" + response.userName;
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
            }
        });

    });
</script>

@endsection
