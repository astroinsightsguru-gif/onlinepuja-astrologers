<!-- BEGIN: Top Bar -->
@if(Auth()->user())
<?php
if (!defined('EXPRESSION')) define('EXPRESSION', '/(?=[A-Z])/');
?>

<style>
    .top-bar-boxed:after{
        margin-top: 0px !important;
    }

    .side-nav {
        padding: 6rem 1.25rem 4rem !important;
    }

    .content{
        padding-top:3rem;
    }
</style>
<div
    class="top-bar-boxed {{ isset($class) ? $class : '' }} h-[70px] md:h-[65px] z-[51] border-b border-white/[0.08] mt-12 md:mt-0 -mx-3 sm:-mx-8 md:-mx-0 px-3 md:border-b-0 relative md:fixed md:inset-x-0 md:top-0 sm:px-8 md:px-10 md:bg-gradient-to-b md:from-slate-100 md:to-transparent dark:md:from-darkmode-700">
    <div class="h-full flex items-center">
        @php
            $dashboardPage = \App\Main\SideMenu::hasDashboardAccess();
            
            $flags = DB::table('systemflag')
                ->whereIn('name', ['AdminLogo', 'AppName'])
                ->pluck('value', 'name');
            
            // Assign to variables for template compatibility (using objects to match previous structure if needed, or just strings)
            // The original code accessed $logo->value and $appName->value.
            // Let's create simple objects to minimize template changes or just update usage.
            // Updating usage is cleaner but might break if I miss one. 
            // Let's assume I update usage below or recreate structure.
            $logo = (object)['value' => $flags['AdminLogo'] ?? ''];
            $appName = (object)['value' => $flags['AppName'] ?? ''];
        @endphp
        @if($dashboardPage)
        <a href="/admin/dashboard" class="logo -intro-x hidden md:flex xl:w-[180px] block">
            <img alt="AstroGuru image" class="logo__image w-6" src="/{{ $logo->value }}"
                style="height: 50px;width: 100%; max-width: 50px;border-radius:50%">
            <span class="logo__text text-white text-lg ml-5 mt-2.5" style="vertical-align: center">
                {{ $appName->value }}
            </span>
        </a>
        @else
        <a class="logo -intro-x hidden md:flex xl:w-[180px] block">
            <img alt="AstroGuru image" class="logo__image w-6" src="/{{ $logo->value }}"
                style="height: 50px;width: 100%; max-width: 50px;border-radius:50%">
            <span class="logo__text text-white text-lg ml-5 mt-2.5" style="vertical-align: center">
                {{ $appName->value }}
            </span>
        </a>
        @endif
        <!-- END: Logo -->
        <!-- BEGIN: Breadcrumb -->
        <nav aria-label="breadcrumb" class="-intro-x h-[45px] mr-auto">
            <ol class="breadcrumb breadcrumb-light">

                <li class="breadcrumb-item">
                    @if ($dashboardPage)
                        <a href="{{ route('dashboard') }}">Home</a>
                    @else
                        <a>Home</a>
                    @endif
                </li>
                {{-- <li class="breadcrumb-item active" aria-current="page" style="text-transform: capitalize;">
                    <a href="{{ route(Request::segment(2)) }}">
                        @if (preg_match('/[A-Z]/', Request::segment(1)))
                            <?php
                            $capital = preg_split(EXPRESSION, Request::segment(1));
                            $header = implode(' - ', $capital);
                            ?>
                            {{ $header }}
                        @else
                        @endif
                    </a>
                </li> --}}
                @if (Request::segment(2))
                    <li class="breadcrumb-item active" aria-current="page" style="text-transform: capitalize;">
                        @if (Request::segment(3))
                            @if (preg_match('/[A-Z]/', Request::segment(2)))
                                <?php
                                $capital = preg_split(EXPRESSION, Request::segment(2));
                                $header = implode(' - ', $capital);
                                ?>
                                <a href="/proxxample/{{ (Request::segment(2) == 'user-chat-monitoring') ? 'chat-monitoring' : Request::segment(2) }}"> {{ $header }}</a>
                            @else
                                <a href="/proxxample/{{ (Request::segment(2) == 'user-chat-monitoring') ? 'chat-monitoring' : Request::segment(2) }}"> {{ Request::segment(2) }}</a>
                            @endif
                    <li class="breadcrumb-item active" aria-current="page" style="text-transform: capitalize;">
                        @if (preg_match('/[A-Z]/', Request::segment(3)))
                            <?php
                            $capital = preg_split(EXPRESSION, Request::segment(3));
                            $header = implode(' - ', $capital);
                            ?>
                            {{ $header }}
                        @else
                            {{ Request::segment(3) }}
                        @endif
                    </li>
                @else
                    @if (preg_match('/[A-Z]/', Request::segment(2)))
                        <?php
                        $capital = preg_split(EXPRESSION, Request::segment(2));
                        $header = implode(' - ', $capital);
                        ?>
                        {{ $header }}
                    @else
                        {{ Request::segment(2) }}
                    @endif
                @endif
                </li>
                @endif
            </ol>
        </nav>
        <!-- END: Notifications -->
        <!-- BEGIN: Account Menu -->
        <div class="intro-x dropdown w-8 h-8">
            <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110"
                role="button" aria-expanded="false" data-tw-toggle="dropdown">
                @if (auth()->user())
                    <img class="rounded-full" src="{{ asset(auth()->user()->profile) }}"
                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Admin Profile" />
                @else
                    <img class="rounded-full" src='/build/assets/images/person.png' alt="Admin Profile" />
                @endif
                {{-- <img alt="AstroGuru image" class="logo__image w-6" src="/build/assets/images/person.png"
                    style="height: 100%;width: 100%; max-width: 50px;"> --}}
                {{-- <img alt="Profile image" src="../build/assets/images/person.png"> --}}
            </div>
            <div class="dropdown-menu w-56">
                <ul
                    class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                    <li class="p-2">
                        @if (auth()->user())
                            <div class="font-medium">{{ auth()->user()->name }}</div>
                        @endif
                        {{-- <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">{{ $fakers[0]['jobs'][0] }}
                        </div> --}}
                    </li>

                    <li>
                        <hr class="dropdown-divider border-white/[0.08]">
                    </li>
                    <li>
                        <a href="{{ route('editProfile') }}" class="dropdown-item hover:bg-white/5">
                            <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Edit Profile
                        </a>
                    </li>
                    <li>
                        <a onclick="document.getElementById('change-modal').querySelector('form').reset();" class="dropdown-item hover:bg-white/5 changepassword"
                            href="javascript:;" data-tw-toggle="modal" data-tw-target="#change-modal">
                            <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Change Password
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" class="dropdown-item hover:bg-white/5">
                            <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- END: Account Menu -->
    </div>
</div>

<div id="change-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Change Password</h2>
            </div>
            <form  method="POST" enctype="multipart/form-data" id="change-password">
                @csrf
                <div id="input" class="p-5">
                    <div class="input">
                        <div>
                            <label for="name" class="form-label">Old Password</label>
                            <input id="old" name="old" type="text" class="form-control"
                                placeholder="Old Password" required>
                            <div class="text-danger print-oldPassword-error-msg mb-2" style="display:none">
                                <ul></ul>
                            </div>
                        </div>
                    </div>
                    <div class="input mt-3">
                        <div>
                            <label for="name" class="form-label">New Password</label>
                            <input id="new" name="new" type="password" class="form-control"
                                placeholder="New Password" required>
                            <div class="text-danger print-newpassword-error-msg mb-2" style="display:none">
                                <ul></ul>
                            </div>
                        </div>

                    </div>
                    <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Change
                            Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        jQuery("#change-password").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('changePassword') }}",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.success(data.success);
                        const modal = tailwind.Modal.getOrCreateInstance(document.getElementById('change-modal'));
                        modal.hide();
                        document.getElementById('change-password').reset();
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });

        });



        function printErrorMsg(msg) {
            jQuery(".print-oldPassword-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (!key) {
                    jQuery(".print-oldPassword-error-msg").css('display', 'block');
                    jQuery(".print-oldPassword-error-msg").find("ul").append('<li>' + value + '</li>');
                }
            });
        }

        jQuery('.changepassword').on('click',function(){
            jQuery(".print-oldPassword-error-msg").css('display', 'none');
        })
    </script>

@endif
<!-- END: Top Bar -->
