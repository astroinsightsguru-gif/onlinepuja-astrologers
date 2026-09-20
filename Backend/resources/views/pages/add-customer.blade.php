@extends('../layout/' . $layout)

@section('subhead')
<title>Add Customer</title>
@endsection


@section('subcontent')

<div class="loader"></div>
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 mt-2">

        <div class="intro-y box">
            <div
            class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
            <h2 class="font-medium text-base mr-auto">Add Customer</h2>
        </div>
        <form method="POST" enctype="multipart/form-data" id="add-form">
            @csrf
            <div id="input" class="p-5">
                <div class="preview">
                    <div class="mt-3">
                        <div class="sm:grid grid-cols-3 gap-2">
                            <div class="input">
                                <div>
                                    <label for="regular-form-1" class="form-label">Name<span class="color-red font-weight-bold">*</span></label>
                                    <input id="name" name="name" type="text" class="form-control inputs"
                                    placeholder="Customer Name" onkeypress="return Validate(event);" required>
                                    <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                        <ul></ul>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="input mt-2 sm:mt-0">
                                <label for="contactNo">Phone No<span class="color-red font-weight-bold">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="countryCode1" value="{{ old('countryCode') }}" placeholder="+91" name="countryCode"
                                    class="form-control rounded-left" style="max-width: 60px;" required oninput="this.value = this.value.replace(/[^0-9\+]/g, '').replace(/^(\+?)(\d{1,4})$/, '$1$2')"
                                    title="Country code should be a number, optionally prefixed with '+'." maxlength="5">

                                    <input type="text" value="{{ old('contactNo') }}" id="contactNo" name="contactNo" class="form-control rounded-right"
                                    pattern="\d{10}"
                                    inputmode="numeric" title="Phone number should contain only numbers." required maxlength="10" minlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                </div>
                            </div> --}}
                            <div class="input mt-2 sm:mt-0">
                                <div>
                                    <label for="regular-form-1" class="form-label">Contact No<span class="color-red font-weight-bold">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="countryCode1" value="" placeholder="+91" name="countryCode"
                                        class="form-control rounded-left" style="max-width: 60px;" required oninput="this.value = this.value.replace(/[^0-9\+]/g, '').replace(/^(\+?)(\d{1,4})$/, '$1$2')"
                                        title="Country code should be a number, optionally prefixed with '+'." maxlength="5">

                                        <input id="contactNo" name="contactNo" type="text"
                                        class="form-control inputs rounded-right" placeholder="Contact Number" required
                                        onKeyDown="numbersOnly(event)" maxlength="10">
                                        <div class="text-danger print-number-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input mt-2 sm:mt-0">
                                <div>
                                    <label for="validation-form-2"
                                    class="form-label w-full flex flex-col sm:flex-row">
                                    Email<span class="color-red font-weight-bold">*</span>
                                </label>
                                <input id="email" type="email" name="email" onkeypress="return validateJavascript(event);" class="form-control inputs"
                                placeholder="example@gmail.com" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="sm:grid grid-cols-3 gap-2">
                        <div class="input">
                            <div>
                                <label id="input-group" class="form-label">Birth Date<span class="color-red font-weight-bold">*</span></label>
                                <input type="date" id="birthDate" name="birthDate"
                                class="form-control inputs" placeholder="Unit"
                                aria-describedby="input-group-3" required>
                            </div>
                        </div>

                        <div class="input mt-2 sm:mt-0">
                            <label id="input-group" class="form-label">Birth Time<span class="color-red font-weight-bold">*</span></label>
                            <input type="time" id="birthTime" name="birthTime" class="form-control inputs"
                            placeholder="Wholesale" aria-describedby="input-group-4" required>

                        </div>
                        <div class="input mt-2 sm:mt-0">
                            <label id="input-group" class="form-label">Birth Place<span class="color-red font-weight-bold">*</span></label>
                            <input type="text" class="form-control inputs" id="birthPlace" name="birthPlace"
                            placeholder="Birth Place" aria-describedby="input-group-5"
                            onkeypress="return Validate(event);" required>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div>
                        <div class="input">
                            <div>
                                <label for="validation-form-6"
                                class="form-label w-full flex flex-col sm:flex-row">
                                Current Address<span class="color-red font-weight-bold">*</span>
                            </label>
                            <textarea id="addressLine1" class="form-control inputs" name="addressLine1" placeholder="Current Address" onkeypress="return validateJavascript(event);" minlength="10"
                            required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div class="sm:grid grid-cols-3 gap-2">
                    <div class="input mt-2 sm:mt-0">
                        <label id="input-group" class="form-label">City<span class="color-red font-weight-bold">*</span></label>
                        <input type="text" id="addressLine2" name="location" class="form-control"
                        placeholder="City" aria-describedby="input-group-4"
                        onkeypress="return Validate(event);" required>
                    </div>
                    <div class="input mt-2 sm:mt-0">
                        <label id="input-group" class="form-label">Pin Code<span class="color-red font-weight-bold">*</span></label>
                        <input type="text" id="pincode" name="pincode" class="form-control"
                        placeholder="Pin Code" aria-describedby="input-group-5"
                        onKeyDown="checkPincode(event)" required pattern="\d{6}"
                        inputmode="numeric" title="Pincode should be a 6 digit number." required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="6">
                    </div>
                    <div class="input mt-2 sm:mt-0">
                        <label id="gender" class="form-label">Select Gender<span class="color-red font-weight-bold">*</span></label>
                        <select class="form-control" id="gender" name="gender" value="gender"
                        required>
                        <option disabled selected>--Select Gender--</option>
                        <option id="gender">Female</option>
                        <option id="gender">Male</option>
                    </select>
                    <div class="text-danger print-gender-error-msg mb-2" style="display:none">
                        <ul></ul>
                    </div>
                </div>

            </div>
        </div>

                            <div class="mt-3">

                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-4">
                                            <div>
                                                <label for="profile" class="form-label">Profile Image</label>
                                                <img id="thumb" width="150px" alt="profileImage"
                                                    style="display:none" />
                                                <input type="file" class="mt-2" name="profile" id="profile"
                                                    onchange="preview()" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="intro-y col-span-6 md:col-span-4">
                                            <div class="input mt-3">
                                                <div>
                                                    <label for="title" class="form-label">Country</label>
                                                    <select name="country" class="form-control select2 country"
                                                        data-placeholder="Choose Your country">
                                                        <option>Select Country</option>
                                                        @foreach ($country as $countryName)
                                                            <option value={{ $countryName->nicename }}>
                                                                {{ $countryName->nicename }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



        <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add
            Customer</button>
        </div>
    </div>
</div>
</form>
</div>


</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
    var spinner = $('.loader');

    function preview() {
        document.getElementById("thumb").style.display = "block";
        thumb.src = URL.createObjectURL(event.target.files[0]);
    }

    function Validate(event) {
        var regex = new RegExp("^[0-9-!@#$%&<>*?]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }

    function validateJavascript(event) {
        var regex = new RegExp("^[<>]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }

    function numbersOnly(e) {
        var keycode = e.keyCode;
        let contact = document.getElementById("contactNo").value;
        if ((keycode < 48 || keycode > 57) && (keycode < 96 || keycode > 105) && keycode !=
        9 && keycode != 8 && keycode != 37 && keycode != 38 && keycode != 39 && keycode != 40 && keycode != 46) {
            e.preventDefault();
        }
    }


    function checkPincode(e) {
        let pincode = document.getElementById("pincode").value;
        var keycode = e.keyCode;
        if (pincode.length >= 6 && (keycode < 48 || keycode > 57) && keycode != 9 && keycode != 8) {
            e.preventDefault();
        }
    }

    jQuery(function () {
        jQuery('#add-form').submit(function (e) {
            e.preventDefault();
            spinner.show();

            // Clear previous errors
            jQuery('.is-invalid').removeClass('is-invalid');
            jQuery('.text-danger').remove();

            var data = new FormData(this);

            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addUserApi') }}",
                data: data,
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function (data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        spinner.hide();
                        location.href = "/admin/customers";
                    } else {
                        jQuery.each(data.error, function (key, value) {
                            var field = jQuery('[name="' + key + '"], [name="' + key + '[]"]');
                            field.addClass('is-invalid');

                            var errorElement = jQuery('<span class="text-danger">' + value[0] + '</span>');

                            // Append error appropriately
                            if (field.is(':radio') || field.is(':checkbox')) {
                                field.closest('.form-check').append(errorElement);
                            } else if (field.hasClass('select2-hidden-accessible')) {
                                field.next('.select2-container').after(errorElement);
                            } else if (field.closest('.input-group').length) {
                                field.closest('.input-group').after(errorElement);
                            } else {
                                field.after(errorElement);
                            }
                        });
                        spinner.hide();
                    }
                },
                error: function (xhr) {
                    spinner.hide();
                    toastr.error('An unexpected error occurred. Please try again.');
                    console.error(xhr.responseText);
                }
            });
        });

        // Optional: Clear error when user starts typing or changing input
        jQuery('#add-form').on('input change', 'input, select, textarea', function () {
            jQuery(this).removeClass('is-invalid').next('.text-danger').remove();
        });

        jQuery('#add-form').on('input', '[name="countryCode"], [name="contactNo"] , [name="country"]', function() {
                var inputGroup = jQuery(this).closest('.input-group');
                inputGroup.find('input').removeClass('is-invalid');
                inputGroup.next('.text-danger').remove();
            });

    });


    function printErrorMsg(msg) {
        jQuery(".print-name-error-msg").find("ul").html('');
        jQuery.each(msg, function(key, value) {
            if (key == 'name') {
                jQuery(".print-name-error-msg").css('display', 'block');
                jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            if (key == 'contactNo') {
                jQuery(".print-number-error-msg").css('display', 'block');
                jQuery(".print-number-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            if (key == 'gender') {
                jQuery(".print-gender-error-msg").css('display', 'block');
                jQuery(".print-gender-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            else {
                toastr.warning(value)
            }
        });
    }
</script>
<script>
    $(window).on('load', function() {
        $('.loader').hide();
    })
</script>

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
    initializeAutocomplete('birthPlace');
    initializeAutocomplete('addressLine2');

</script>
@endsection
