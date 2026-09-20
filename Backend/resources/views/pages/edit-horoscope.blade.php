@extends('../layout/' . $layout)

@section('subhead')
    <title>Edit Horoscope</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6 mt-5" id="result">
        <div class="intro-y col-span-12 mt-2">

            <div class="intro-y box">

                <form method="POST" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Edit Horoscope</h2>
                        <button type="submit" class="btn btn-primary shadow-md mr-2">Save</button>
                    </div>
                    <div id="input" class="p-5"
                        style="    height: calc(100vh - 200px);
                    overflow: auto;">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-1 gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Horoscope Sign</label>
                                        <select class="form-control" id="horoscopeSignId" name="horoscopeSignId"
                                            value="horoscopeSignId">
                                            <option disabled selected>--Select Sign--</option>
                                            @foreach ($signs as $sign)
                                                <option {{ $horoscopeSignId == $sign->id ? 'selected' : '' }}
                                                    id="productCategoryId" value={{ $sign->id }}>
                                                    {{ $sign->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="mt-3">
                                <div>
                                    <h4 class="category">Weekly</h4>
                                </div>
                                <div class="input mt-2">
                                    <div>
                                        <input type="hidden" name="oldSignId" value={{$horoscopeSignId}}>
                                        <input type="text" id="title" name="title" class="form-control"
                                            placeholder="Title" aria-describedby="input-group-3" required
                                            onkeypress="return Validate(event);" value="{{ $horo['weeklytitle'] }}">
                                    </div>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:10px">
                                    <textarea class="form-control ml-3" id="weeklydesc" name="weeklydesc">{{ $horo['weeklydesc'] }}</textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div>
                                    <h4 class="category">Monthly</h4>
                                </div>
                                <div class="input mt-2">
                                    <div>
                                        <input type="text" id="monthlytitle" name="monthlytitle" class="form-control"
                                            placeholder="Title" aria-describedby="input-group-3" required
                                            onkeypress="return Validate(event);" value={{ $horo['monthlytitle'] }}>
                                    </div>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:10px">
                                    <textarea class="form-control ml-3" id="monthlydesc" name="monthlydesc">{{ $horo['monthlydesc'] }}</textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div>
                                    <h4 class="category">Yearly</h4>
                                </div>
                                <div class="input mt-2">
                                    <div>
                                        <input type="text" id="yearlytitle" name="yearlytitle" class="form-control"
                                            placeholder="Title" aria-describedby="input-group-3" required
                                            onkeypress="return Validate(event);" value="{{ $horo['yearlytitle'] }}">
                                    </div>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:10px">
                                    <textarea class="form-control ml-3" id="yearlydesc" name="yearlydesc">{{ $horo['yearlydesc'] }}</textarea>
                                </div>
                            </div>
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
         @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ session('error') }}");
        @endif
        var spinner = $('.loader');

        jQuery(function() {
            jQuery('#add-form').submit(function(e) {
                debugger
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                data.append('weeklydesc', CKEDITOR.instances['weeklydesc'].getData());
                data.append('monthlydesc', CKEDITOR.instances['monthlydesc'].getData());
                data.append('yearlydesc', CKEDITOR.instances['yearlydesc'].getData());
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('editHoroscope') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.href = "/admin/horoscope"
                        } else {
                            printErrorMsg(data.error);
                            spinner.hide();
                        }
                    }
                });
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

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            CKEDITOR.replace('monthlydesc', {
                toolbar: 'simple'
            });
            CKEDITOR.replace('yearlydesc', {
                toolbar: 'simple'
            });
            CKEDITOR.replace('weeklydesc', {
                toolbar: 'simple'
            });
        });
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
