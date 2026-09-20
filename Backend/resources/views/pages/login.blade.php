@extends('../layout/' . $layout)

@section('head')
<title>Login</title>
@endsection
@section('content')
<div class="loader"></div>
<div class="container sm:px-10">
    <div class="block xl:grid grid-cols-2 gap-4">
        <div class="hidden xl:flex flex-col min-h-screen">

            <div class="my-auto">
                @php
                $logo = DB::table('systemflag')
                ->where('name', 'AdminLogo')
                ->select('value')
                ->first();
                $appName = DB::table('systemflag')
                ->where('name', 'AppName')
                ->select('value')
                ->first();
                @endphp
                <img alt="AstroGuru image" class="-intro-x w-1/2 -mt-16" src="/{{ $logo->value }}"
                    style="height: 200px;width: 200px;border-radius:50%">
                <div class="-intro-x text-white font-medium text-4xl leading-tight mt-3">{{ $appName->value }}</div>
                <div class="-intro-x mt-5 text-lg text-white text-opacity-70 dark:text-slate-400">Astrology Prediction
                    by {{ucfirst($professionTitle)}}s</div>
            </div>
        </div>
        <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
            <div
                class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                <img alt="AstroGuru image" class="-intro-x w-1/2 -mt-16 xl:hidden" src="/{{ $logo->value }}"
                    style="height: 140px;width: 140px;margin:auto">
                <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Sign In</h2>
                <div class="intro-x mt-2 xl:hidden text-center">Astrology Prediction
                    by {{ucfirst($professionTitle)}}s</div>

                <div class="intro-x mt-8">
                    <form>
                        <div class="alert alert-danger print-error-msg mb-2" style="display:none">
                            <ul></ul>
                        </div>
                        <input id="email" type="text" class="intro-x login__input form-control py-3 px-4 block"
                            placeholder="Email" name="email">
                        <div class="text-danger print-email-error-msg mb-2" style="display:none">
                            <ul></ul>
                        </div>
                        <input id="password" type="password" name="password"
                            class="intro-x login__input form-control py-3 px-4 block mt-4" placeholder="Password">
                        <div class="text-danger print-password-error-msg mb-2" style="display:none">
                            <ul></ul>
                        </div>
                </div>
                <button
                    class="mt-4 btn btn-primary btn-submit py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Login</button>
                </form>

            </div>
        </div>

    </div>

</div>
<div id="superlarge-modal-size-preview" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="col-span-12 lg:col-span-8 2xl:col-span-9">
                <div class="intro-y box lg:mt-5">
                    <div class="flex items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Forgot Password</h2>
                    </div>
                    <div class="p-5">
                        <form method="POST" enctype="multipart/form-data" id="sendmailform">
                            @csrf
                            <div>
                                <label for="change-password-form-1" class="form-label">Email</label>
                                <input class="form-control" id="resendemail" name="resendemail" type="email"
                                    class="form-control" placeholder="Input text">
                                <div class="text-danger print-resendmail-error-msg mb-2" style="display:none">
                                    <ul></ul>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-4">Send Mail</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="module">
    var spinner = $('.loader');
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    jQuery(".btn-submit").click(function(e) {

        e.preventDefault();

        var email = $("#email").val();
        var password = $("#password").val();

        jQuery.ajax({

            type: 'POST',
            url: "{{ route('loginApi') }}",
            data: {
                email: email,
                password: password
            },
            success: function(data) {
                if (jQuery.isEmptyObject(data.error)) {
                    location.href = data.first;
                } else {
                    printErrorMsg(data.error);
                }
            }
        });

    });


    function printErrorMsg(msg) {
        jQuery(".print-error-msg").find("ul").html('');
        jQuery(".print-email-error-msg").find("ul").html('');
        jQuery(".print-password-error-msg").find("ul").html('');
        jQuery.each(msg, function(key, value) {
            if (key == 'email') {
                jQuery(".print-email-error-msg").css('display', 'block');
                jQuery(".print-email-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            if (key == 'password') {
                jQuery(".print-password-error-msg").css('display', 'block');
                jQuery(".print-password-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            if (!key) {
                jQuery(".print-error-msg").css('display', 'block');
                jQuery(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            }
        });
    }
</script>
<script>
    $(window).on('load', function() {
        $('.loader').hide();
    })
</script>
@endsection
