@extends('../layout/' . $layout)

@section('subhead')
    <title>Edit Customer</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">

            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Edit Customer</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="edit-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" name="field_id" id="field_id" class="form-control"
                                                placeholder="Customer Name" value="{{ $customer['id'] }}">
                                            <label for="regular-form-1" class="form-label">Name</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="Customer Name" value="{{ $customer['name'] }}"
                                                onkeypress="return Validate(event);" required>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        {{-- <div> --}}
                                            <label for="regular-form-1" class="form-label">Contact No</label>

                                            <div class="input-group">
                                                <input type="text" id="countryCode1" value="{{ $customer['countryCode'] }}" placeholder="+91" name="countryCode"
                                                class="form-control rounded-left" style="max-width: 60px;" required oninput="this.value = this.value.replace(/[^0-9\+]/g, '').replace(/^(\+?)(\d{1,4})$/, '$1$2')"
                                                title="Country code should be a number, optionally prefixed with '+'." maxlength="5" onkeypress="return validateJavascript(event);">

                                            <input id="regular-form-1" type="text" class="form-control" name="contactNo"
                                                placeholder="Contact Number" value="{{ $customer['contactNo'] }}" required
                                                onKeyDown="numbersOnly(event)" maxlength=10>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <div>
                                            <label for="validation-form-2"
                                                class="form-label w-full flex flex-col sm:flex-row">
                                                Email
                                            </label>
                                            <input id="validation-form-2" type="email" name="email" class="form-control"
                                                placeholder="example@gmail.com"
                                                onkeypress="return validateJavascript(event);" required
                                                value="{{ $customer['email'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input">
                                        <div>
                                            <label id="input-group" class="form-label">Birth Date</label>
                                            <input type="date" class="form-control" placeholder="Unit"
                                                aria-describedby="input-group-3" name="birthDate"
                                                value="{{ date('Y-m-d', strtotime($customer['birthDate'])) }}" required>
                                        </div>
                                    </div>

                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Birth Time</label>
                                        <input type="time" class="form-control" placeholder="Wholesale" name="birthTime"
                                            aria-describedby="input-group-4" value="{{ $customer['birthTime'] }}" required>

                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Birth Place</label>
                                        <input type="text" class="form-control" placeholder="Birth Place"
                                            name="birthPlace" id="birthPlace" aria-describedby="input-group-5"
                                            value="{{ $customer['birthPlace'] }}" onkeypress="return Validate(event);"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div>
                                    <div class="input">
                                        <div>
                                            <label for="validation-form-6"
                                                class="form-label w-full flex flex-col sm:flex-row">
                                                Current Address
                                            </label>
                                            <textarea id="validation-form-6" class="form-control" name="addressLine1" placeholder="Current Address" minlength="10"
                                                onkeypress="return validateJavascript(event);" required>{{ $customer['addressLine1'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">City</label>
                                        <input type="text" class="form-control" placeholder="City" name="location"
                                            aria-describedby="input-group-4" id="addressLine2" value="{{ $customer['location'] }}"
                                            onkeypress="return Validate(event);" required>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Pin Code</label>
                                        <input type="text" class="form-control" placeholder="Pin Code" name="pincode"
                                            aria-describedby="input-group-5" value="{{ $customer['pincode'] }}"
                                            onKeyDown="checkPincode(event)" required>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="gender" class="form-label">Select Gender</label>
                                        <select class="form-control" id="gender" name="gender" value="gender">
                                            <option disabled selected>--Select Gender--</option>
                                            <option id="gender" Value="Female"
                                                {{ $customer->gender == 'Female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option id="gender" Value="Male"
                                                {{ $customer->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-6">
                                <div class="intro-y col-span-12">
                                    <div>
                                        <label for="profile" class="form-label">Profile Image</label>
                                        <img id="thumb" width="150px" src="/{{ $customer['profile'] }}"
                                            alt="Customer image" onerror="this.style.display='none';" />
                                        <input type="file" class="mt-2" name="profile" id="profile"
                                            onchange="preview()" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit"class="btn btn-primary shadow-md mr-2">Save
                                </button>
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

        function checkPincode(e) {
            let pincode = document.getElementById("pincode").value;
            if (pincode.length >= 6 && (keycode < 48 || keycode > 57) && keycode != 9 && keycode != 8) {
                e.preventDefault();
            }
        }

        function numbersOnly(e) {
            var keycode = e.keyCode;
            let contact = document.getElementById("contactNo").value;
            if (contact.length >= 10 && (keycode < 48 || keycode > 57) && keycode != 9 && keycode != 8) {
                e.preventDefault();
            }
        }

        jQuery(function() {
            jQuery('#edit-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('editUserApi') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
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
                    }
                });
            });

              // Optional: Clear error when user starts typing or changing input
            jQuery('#edit-form').on('input change', 'input, select, textarea', function () {
                jQuery(this).removeClass('is-invalid').next('.text-danger').remove();
            });

            jQuery('#edit-form').on('input', '[name="countryCode"], [name="contactNo"] , [name="country"]', function() {
                    var inputGroup = jQuery(this).closest('.input-group');
                    inputGroup.find('input').removeClass('is-invalid');
                    inputGroup.next('.text-danger').remove();
                });

            });
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
