@extends('frontend.layout.master')
@section('content')
@php
     $countries = DB::table('countries')
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
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getLiveAstro') }}"
                                style="color:white;text-decoration:none">My Account</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid container-xl mt-3 email-prefrences" data-select2-id="select2-data-9-7lve">
        <div class="inpage" data-select2-id="select2-data-8-zrys">
            <div class="tab-content py-3" data-select2-id="select2-data-7-dgci">
                <div data-select2-id="select2-data-6-lle8">
                    <div class="text-left">
                        <h1 class="h2 font-weight-bold colorblack">My Account</h1>

                        {{-- <a class="btn btn-danger rounded-pill text-white" href="#" onclick="confirmDelete(event)" style="float: inline-end;">
                            <span class="mr-2">
                                <i class="fa-solid fa-trash"></i>
                            </span>
                            <span>Delete Account</span>
                        </a> --}}


                        <p>
                            View and update your profile, change password in your Online Puja account.
                        </p>
                    </div>

                    <div class="form-group  mb-o mt-0 mt-lg-4 p-0">
                        <div class="d-flex flex-nowrap">
                            <a
                                class="text-decoration-none weight500 mb-4 mt-1 py-2 py-sm-3 px-2 px-sm-3 d-inline-block border-bottom borderbrown">
                                Update Profile</a>

                        </div>

                        <form id="frmUpdateProfile" enctype="multipart/form-data">
                            @csrf
                            <div class="container" data-select2-id="select2-data-5-q5pn">

                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Name <b class="req">*</b></label>
                                            <input autocomplete="off" class="form-control inputtext" data-val="true"
                                                id="FirstName" maxlength="30" name="name" placeholder="Enter First Name"
                                                type="text" value="{{ $getuserdetails['userDetails']['name'] }}"
                                                pattern="^[a-zA-Z\s]{2,50}$"
                                                title="Name should contain only letters and be between 2 and 30 characters long."
                                                required oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">

                                            <span class="field-validation-valid text-danger" data-valmsg-for="FirstName"
                                                data-valmsg-replace="true"></span>
                                        </div>


                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Email <b class="req">*</b></label>
                                            <input autocomplete="off" class="form-control inputtext" id="EmailAddress"
                                                maxlength="50" name="email" placeholder="Enter Email" type="email"
                                                value="{{ $getuserdetails['userDetails']['email'] }}" required>

                                        </div>
                                    </div>



                                </div>
                                <div class="row">

                                    <div class="col-sm-6 col-12">
                                        <!--<div class="form-group">-->
                                        <label class="pb-1 pb-md-0 form-label">Mobile <b class="req">*</b></label>
                                        <div class="input-group">
                                                <!-- Country Code Dropdown -->
                                                <div class="input-group">
                                                    <!-- Country Code Dropdown -->
                                                    <select class="form-control select2 rounded-left" id="countryCode1" name="countryCode" style="max-width: 100px;">
                                                        @foreach ($countries as $country)
                                                            <option data-country="in" value="{{ $getuserdetails['userDetails']['countryCode'] ?? $country->phonecode}}" data-ucname="India">
                                                                +{{ $country->phonecode }} {{ $country->iso }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <!-- Mobile Number Input -->
                                                    <input autocomplete="off" class="form-control rounded-right" id="ContactMobile" name="contactNo" type="tel"
                                                           value="{{ $getuserdetails['userDetails']['contactNo'] }}" pattern="\d{10}" inputmode="numeric"
                                                           title="Phone number should contain only numbers." required maxlength="10"
                                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                </div>
                                                <span class="field-validation-valid text-danger" data-valmsg-for="ContactMobile" data-valmsg-replace="true"></span>
                                        </div>
                                        <!--</div>-->
                                    </div>


                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Gender<b class="req">*</b></label>
                                            <select class="form-control" data-val="true" id="Gender" name="gender"
                                                required>
                                                <option value=""
                                                    {{ $getuserdetails['userDetails']['gender'] == 0 ? 'selected' : '' }}>
                                                    --Select--</option>
                                                <option value="Male"
                                                    {{ $getuserdetails['userDetails']['gender'] == 'Male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="Female"
                                                    {{ $getuserdetails['userDetails']['gender'] == 'Female' ? 'selected' : '' }}>
                                                    Female</option>
                                                <option value="Other"
                                                    {{ $getuserdetails['userDetails']['gender'] == 'Other' ? 'selected' : '' }}>
                                                    Other</option>
                                            </select>
                                            <span class="field-validation-valid text-danger" data-valmsg-for="Gender"
                                                data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <label class="pb-1 pb-md-0 form-label">Birth Date<b class="req">*</b></label>
                                        <input type="date" name="birthDate" class="form-control"
                                            value="{{ date('Y-m-d', strtotime($getuserdetails['userDetails']['birthDate'])) }}" required>
                                        <span class="field-validation-valid text-danger" data-valmsg-for="POB"
                                            data-valmsg-replace="true"></span>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Birth Time</label>

                                            <div class="md-form md-outline input-with-post-icon timepicker position-relative"
                                                default="now">
                                                <input type="time" name="birthTime" class="form-control"
                                                    value="{{ $getuserdetails['userDetails']['birthTime'] }}">
                                                <span class="field-validation-valid text-danger" data-valmsg-for="POB"
                                                    data-valmsg-replace="true"></span>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Place Of Birth<b class="req">*</b></label>
                                            <input autocomplete="off" name="birthPlace"
                                                class="form-control inputtext ui-autocomplete-input" id="address"
                                                placeholder="Enter Place Of Birth" type="text"
                                                value="{{ $getuserdetails['userDetails']['birthPlace'] }}" required>
                                            <span class="field-validation-valid text-danger" data-valmsg-for="POB"
                                                data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Current Address</label>
                                            <input class="form-control inputtext" id="CurrentAddress"
                                                value="{{ $getuserdetails['userDetails']['addressLine1'] }}"
                                                maxlength="300" name="addressLine1" placeholder="Enter Current Address"
                                                type="text" value="">
                                            <span class="field-validation-valid text-danger"
                                                data-valmsg-for="CurrentAddress" data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Location (City/State/Country)</label>
                                            <input autocomplete="off" class="form-control inputtext ui-autocomplete-input"
                                                id="CurrentPlace" name="location" placeholder="Enter Current City"
                                                type="text" value="{{ $getuserdetails['userDetails']['location'] }}">
                                            <span class="field-validation-valid text-danger"
                                                data-valmsg-for="CurrentPlace" data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Pin Code</label>
                                            <input autocomplete="off" class="form-control inputtext" id="PinCode"
                                                name="pincode" pattern="\d{6}" inputmode="numeric"
                                                title="Pincode should be a 6 digit number." required
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="6"
                                                placeholder="Enter Pin Code" type="tel"
                                                value="{{ $getuserdetails['userDetails']['pincode'] }}">
                                            <span class="field-validation-valid text-danger" data-valmsg-for="PinCode"
                                                data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="pb-1 pb-md-0 form-label">Profile </label>
                                            <input class="form-control" id="profilepic" name="profilepic"
                                                style="height:44px;" type="file" value="" accept="image/*">
                                            <span class="field-validation-valid text-danger" data-valmsg-for="FirstName"
                                                data-valmsg-replace="true"></span>
                                            @if($getuserdetails['userDetails']['profile'])
                                            <img class="rounded-m" src="{{ Str::startsWith($getuserdetails['userDetails']['profile'], ['http://','https://']) ? $getuserdetails['userDetails']['profile'] : '/' . $getuserdetails['userDetails']['profile'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $getuserdetails['userDetails']['profile'] }}')" />
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-12 text-center pt-2">
                                        <div class="form-group text-right">
                                            <input type="button" id="btnSave" value="Update Profile"
                                                class="btn btn-chat font-weight-semi">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class=" card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <b> Share Link</b>
                                    </div>
                                    <p class="font-16 mb-5">
                                        Invite your friends by simply copying &amp; sharing the referral link and earn
                                        referral bonus for you as well as your friends.
                                    </p>
                                    <p>
                                    </p>
                                    <div class="input-group">
                                        <input class="form-control" type="url" readonly="" id="referralLink"
                                            value="{{ env('APP_URL') . '?ref=' . $getuserdetails['userDetails']['referral_token'] }}">
                                        <div class="input-group-append" >
                                            <span class="input-group-text" style="border: 1px solid #495057" onclick="copyToClipboard('referralLink')"
                                                style="cursor: pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="24"
                                                    viewBox="0 0 21 26">
                                                    <path id="Icon_material-content-copy"
                                                        data-name="Icon material-content-copy"
                                                        d="M8.526,1.5H21.789A2.3,2.3,0,0,1,24,3.864V20.409H21.789V3.864H8.526ZM5.211,6.227H17.368a2.3,2.3,0,0,1,2.211,2.364V25.136A2.3,2.3,0,0,1,17.368,27.5H5.211A2.3,2.3,0,0,1,3,25.136V8.591A2.3,2.3,0,0,1,5.211,6.227Zm0,18.909H17.368V8.591H5.211Z"
                                                        transform="translate(-3 -1.5)"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class=" card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <p class="font-weight-bold">How It Works :</p>
                                    </div>
                                    <p>Your friend must sign up on {{ $appname }} using your referral code.</p>
                                    <p>Your friend must be a first time user of {{ $appname }}</p>
                                    <p>You will get {{ $currency->value }}{{ $referral_settings->amount }} when your
                                        friend sign up on {{ $appname }} using your referral code.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')

    @php
$apikey = DB::table('systemflag')->where('name', 'googleMapApiKey')->first();
@endphp
<script src="https://maps.googleapis.com/maps/api/js?key={{ $apikey->value }}&libraries=places">
</script>
<script>
        // $(document).ready(function() {
        //     $('.select2').select2({
        //         width: '100%' // Ensure Select2 dropdown takes full width of the parent
        //     });
        // });
    function initializeAutocomplete(inputId) {
        var input = document.getElementById(inputId);
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function(event) {
            var place = autocomplete.getPlace();
            if (place.hasOwnProperty('place_id')) {
                if (!place.geometry) {
                    return;
                }
                latitude.value = place.geometry.location.lat();
                longitude.value = place.geometry.location.lng();
            } else {
                var service = new google.maps.places.PlacesService(document.createElement('div'));
                service.textSearch({
                    query: place.name
                }, function(results, status) {
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        latitude.value = results[0].geometry.location.lat();
                        longitude.value = results[0].geometry.location.lng();
                    }
                });
            }
        });
    }
    initializeAutocomplete('address');
    initializeAutocomplete('CurrentAddress');

</script>
        <script>
            function isNumberKey(evt) {
                var e = event || evt;
                var CharCode = e.which || e.keyCode;
                if (CharCode == 13) {
                    $("#btnVerify").click();
                    return false;
                }
                if (CharCode > 31 && (CharCode < 48 || CharCode > 57))
                    return false;
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#btnSave').click(function(e) {
                    var form = document.getElementById('frmUpdateProfile');
                    if (form.checkValidity() === false) {
                        form.reportValidity();
                        return;
                    }
                    e.preventDefault();

                    @php
                        $id = authcheck()['id'];
                    @endphp
                    var formData = new FormData($('#frmUpdateProfile')[0]);
                    formData.append('profilepic', $('#profilepic')[0].files[0]);

                    $.ajax({
                        url: '{{ route('user.update', ['id' => $id]) }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            toastr.success('Profile Updated Successfully');
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        },
                        error: function(xhr, status, error) {
                            const response = typeof xhr.responseJSON === 'object' ? xhr.responseJSON : JSON.parse(xhr.responseText);
                            $.each(response.error, function(key, value) {
                                // Check if the value is an array (multiple errors per field)
                                if (Array.isArray(value)) {
                                    // Display all error messages for this field
                                    $.each(value, function(i, errorMsg) {
                                        toastr.error(errorMsg);
                                    });
                                } else {
                                    // Single error message for this field
                                    toastr.error(value);
                                }
                            });
                            // toastr.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>
        <script>
            function confirmDelete(event) {
                event.preventDefault(); // Prevent the default link behavior

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the delete account route
                        window.location.href = "{{ route('front.deleteAccount') }}";
                    }
                });
            }

            function copyToClipboard(elementId) {
                const input = document.getElementById(elementId);
                navigator.clipboard.writeText(input.value).then(() => {
                    toastr.success('Copied Successfully');
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            }
        </script>
    @endsection
