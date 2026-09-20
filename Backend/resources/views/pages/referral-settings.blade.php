@extends('../layout/' . $layout)

@section('subhead')
    <title>Update Referral</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">

            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Edit Referral</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" name="id" id="id" class="form-control" value="{{ $referral->id }}">

                                            <label for="regular-form-1" class="form-label">Amount (INR)<span
                                                    class="color-red font-weight-bold">*</span></label>
                                            <input id="amount" name="amount" type="number" class="form-control inputs"
                                                placeholder="Amount in Inr" required value="{{ $referral->amount }}">
                                            <div class="text-danger print-amount-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="input mt-2 sm:mt-0">
                                        <div>
                                            <label for="regular-form-1" class="form-label">Amount (USD)<span
                                                    class="color-red font-weight-bold">*</span></label>
                                            <div class="input-group">


                                                <input id="amount_usd" name="amount_usd" type="text"
                                                    class="form-control inputs rounded-right" placeholder="Amount in Usd"
                                                    required onKeyDown="numbersOnly(event)"  value="{{ $referral->amount_usd }}">
                                                <div class="text-danger print-amount_usd-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <div>
                                            <label for="validation-form-2"
                                                class="form-label w-full flex flex-col sm:flex-row">
                                                Max User Limit<span class="color-red font-weight-bold">*</span>
                                            </label>
                                            <input id="max_user_limit" type="number" name="max_user_limit"
                                                class="form-control inputs"
                                                placeholder="Max User Limit" required value="{{ $referral->max_user_limit }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Update</button>
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



        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
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




        jQuery(function() {
            jQuery('#add-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('update-referral-settings') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.href = "{{route('referral-settings')}}";
                        } else {
                            printErrorMsg(data.error);
                            spinner.hide();
                        }
                    }
                });
            });
        });


    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
