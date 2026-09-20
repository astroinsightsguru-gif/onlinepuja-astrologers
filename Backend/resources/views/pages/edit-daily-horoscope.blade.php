@extends('../layout/' . $layout)

@section('subhead')
    <title>Add Daily Horoscope</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">

            <div class="intro-y box">

                <form method="POST" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Edit Daily Horoscope</h2>
                        <button type="submit" class="btn btn-primary shadow-md mr-2">Save</button>
                    </div>
                    <div id="input" class="p-5"
                        style="    height: calc(100vh - 200px);
                    overflow: auto;">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-2 gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Horoscope Sign</label>
                                        <select class="form-control" id="horoscopeSignId" name="horoscopeSignId"
                                            value="{{ $horoscopeSignId }}" required>
                                            <option disabled selected>--Select Sign--</option>
                                            @foreach ($signs as $sign)
                                                <option {{ $horoscopeSignId == $sign->id ? 'selected' : '' }}
                                                    id="productCategoryId" value={{ $sign->id }}>
                                                    {{ $sign->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <input type="hidden" value={{ $horoscopeSignId }} name="oldSignId">
                                            <input type="hidden" value={{ $horoscopeDate }} name="oldHoroDate">
                                            <label id="input-group" class="form-label">HoroScope Date</label>
                                            <input type="date" id="horoscopeDate" name="horoscopeDate"
                                                class="form-control" placeholder="horoscopeDate"
                                                aria-describedby="input-group-3"
                                                value="{{ date('Y-m-d', strtotime($horoscopeDate)) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-4 gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Lucky Time</label>
                                        <input type="time" id="luckyTime" name="luckyTime" class="form-control inputs"
                                            placeholder="luckyTime" aria-describedby="input-group-4"
                                            value="{{ $dailyHoroscopeStatics[0]->luckyTime }}" required>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Lucky Number</label>
                                        <input type="number" id="luckyNumber" name="luckyNumber"
                                            class="form-control inputs" placeholder="luckyNumber"
                                            aria-describedby="input-group-4"
                                            value="{{ $dailyHoroscopeStatics[0]->luckyNumber }}" required>
                                    </div>

                                    <div class="input-row">
                                        <label id="input-group" class="form-label">Mood of the day</label>
                                        <select id="moodday" name="moodday"class="form-control inputs" required>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😁' ? 'selected' : '' }}
                                                value="😁">😁
                                            </option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😋' ? 'selected' : '' }}
                                                value="😋">😋</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😶' ? 'selected' : '' }}
                                                value="😶">😶</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🌝' ? 'selected' : '' }}
                                                value="🌝">🌝</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😃' ? 'selected' : '' }}
                                                value="😃">😃</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😏' ? 'selected' : '' }}
                                                value="😏">😏</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😀' ? 'selected' : '' }}
                                                value="😀">😀</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😊' ? 'selected' : '' }}
                                                value="😊">😊</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😬' ? 'selected' : '' }}
                                                value="😬">😬</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🙂' ? 'selected' : '' }}
                                                value="🙂">🙂</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😌' ? 'selected' : '' }}
                                                value="😌">😌</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😆' ? 'selected' : '' }}
                                                value="😆">😆</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😉' ? 'selected' : '' }}
                                                value="😉">😉</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😮' ? 'selected' : '' }}
                                                value="😮">😮</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🤩' ? 'selected' : '' }}
                                                value="🤩">🤩</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😅' ? 'selected' : '' }}
                                                value="😅">😅</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😜' ? 'selected' : '' }}
                                                value="😜">😜</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🥲' ? 'selected' : '' }}
                                                value="🥲">🥲</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😂' ? 'selected' : '' }}
                                                value="😂">😂</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🤗' ? 'selected' : '' }}
                                                value="🤗">🤗</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🥱' ? 'selected' : '' }}
                                                value="🥱">🥱</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😲' ? 'selected' : '' }}
                                                value="😲">😲</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🤐' ? 'selected' : '' }}
                                                value="🤐">🤐</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🤧' ? 'selected' : '' }}
                                                value="🤧">🤧</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🥰' ? 'selected' : '' }}
                                                value="🥰">🥰</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😔' ? 'selected' : '' }}
                                                value="😔">😔</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🙁' ? 'selected' : '' }}
                                                value="🙁">🙁</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🥺' ? 'selected' : '' }}
                                                value="🥺">🥺</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😟' ? 'selected' : '' }}
                                                value="😟">😟</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😩' ? 'selected' : '' }}
                                                value="😩">😩</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '😖' ? 'selected' : '' }}
                                                value="😖">😖</option>
                                            <option {{ $dailyHoroscopeStatics[0]->moodday == '🥴' ? 'selected' : '' }}
                                                value="🥴">🥴</option>
                                        </select>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Lucky Colours</label>
                                        <input type="color" id="luckyColor" name="luckyColour"
                                            class="form-control inputs" placeholder="luckyColor"
                                            aria-describedby="input-group-4"
                                            value="{{ $dailyHoroscopeStatics[0]->luckyColor }}" required>
                                    </div>

                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-inline">
                                    <h4 class="category">Love</h4>
                                </div>
                                <div class="input d-inline" style="float:right">
                                    <input type="text" id="lovepercent" name="lovepercent" class="form-control"
                                        placeholder="Percentage(%)" aria-describedby="input-group-3"
                                        onKeyDown="numbersOnly(event)" value={{ $data['lovePercent'] }} required>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:20px">
                                    <textarea class="form-control ml-3" id="lovedesc" name="lovedesc">{{ $data['loveDesc'] }}</textarea required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-inline">
                                    <h4 class="category">Career</h4>
                                </div>
                                <div class="input d-inline" style="float:right">
                                    <input type="text" id="careerpercent" name="careerpercent" class="form-control"
                                        placeholder="Percentage(%)" aria-describedby="input-group-3"
                                        onKeyDown="numbersOnly(event)" value={{ $data['careerPercent'] }} required>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:20px">
                                    <textarea class="form-control ml-3" id="careerdesc" name="careerdesc">{{ $data['careerDesc'] }}</textarea required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-inline">
                                    <h4 class="category">Travel</h4>
                                </div>
                                <div class="input d-inline" style="float:right">
                                    <input type="text" id="travelpercent" name="travelpercent" class="form-control"
                                        placeholder="Percentage(%)" aria-describedby="input-group-3"
                                        onKeyDown="numbersOnly(event)" value={{ $data['travelPercent'] }} required>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:20px">
                                    <textarea class="form-control ml-3" id="traveldesc" name="traveldesc" required>{{ $data['travelDesc'] }}</textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-inline">
                                    <h4 class="category">Health</h4>
                                </div>
                                <div class="input d-inline" style="float:right">
                                    <input type="text" id="healthpercent" name="healthpercent" class="form-control"
                                        placeholder="Percentage(%)" aria-describedby="input-group-3"
                                        onKeyDown="numbersOnly(event)" value={{ $data['healthPercent'] }} required>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:20px">
                                    <textarea class="form-control ml-3" id="healthdesc" name="healthdesc" required>{{ $data['healthDesc'] }}</textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-inline">
                                    <h4 class="category">Money</h4>
                                </div>
                                <div class="input d-inline" style="float:right">
                                    <input type="text" id="moneypercent" name="moneypercent" class="form-control"
                                        placeholder="Percentage(%)" aria-describedby="input-group-3"
                                        onKeyDown="numbersOnly(event)" value={{ $data['moneyPercent'] }} required>
                                </div>
                                <div class="input" id="classic-editor" style="margin-top:20px">
                                    <textarea required class="form-control ml-3" id="moneydesc" name="moneydesc">{{ $data['moneyDesc'] }}</textarea>
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
            if ((keycode < 48 || keycode > 57) && (keycode < 96 || keycode > 105) && keycode !=
                9 && keycode != 8 && keycode != 37 && keycode != 38 && keycode != 39 && keycode != 40 && keycode != 46) {
                e.preventDefault();
            }
        }

        function printErrorMsg(msg) {
            debugger
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (key == 'name') {
                    jQuery(".print-name-error-msg").css('display', 'block');
                    jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                else {
                    toastr.warning(value);
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            CKEDITOR.replace('lovedesc', {
                toolbar: 'simple'
            });
            CKEDITOR.replace('careerdesc', {
                toolbar: 'simple'
            });
            CKEDITOR.replace('moneydesc', {
                toolbar: 'simple'
            });
            CKEDITOR.replace('healthdesc', {
                toolbar: 'simple'
            });
            CKEDITOR.replace('traveldesc', {
                toolbar: 'simple'
            });
        });
        jQuery(function() {
            jQuery('#add-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);

                data.append('lovedesc', CKEDITOR.instances['lovedesc'].getData());
                data.append('traveldesc', CKEDITOR.instances['traveldesc'].getData());
                data.append('moneydesc', CKEDITOR.instances['moneydesc'].getData());
                data.append('careerdesc', CKEDITOR.instances['careerdesc'].getData());
                data.append('healthdesc', CKEDITOR.instances['healthdesc'].getData());
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('editDailyHoroscope') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        debugger
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.href = "/admin/dailyHoroscope"
                        } else {
                            printErrorMsg(data.error);
                            spinner.hide();
                        }
                    }
                });
            });
        });
        $(window).on('load', function() {
            $('.loader').hide();
        });
    </script>
@endsection
