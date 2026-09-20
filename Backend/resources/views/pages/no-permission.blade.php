@extends('../layout/' . $layout)

@section('subhead')
    <title>Permission Denied - {{ getAppName() }}</title>
@endsection

@section('subcontent')
    <div class="container">
        <!-- BEGIN: Error Page -->
        <div class="flex flex-col lg:flex-row items-center justify-center h-screen text-center lg:text-left">
            <div class="-intro-x lg:mr-20">
                <img alt="Access Denied" class="h-48 lg:h-80 transition-transform duration-500 hover:scale-105" src="{{ asset('public/access_denied_onlinepuja.png') }}">
            </div>
            <div class="mt-10 lg:mt-0">
                <div class="intro-x text-8xl font-bold text-primary">403</div>
                <div class="intro-x text-2xl lg:text-4xl font-medium mt-5">Access Denied!</div>
                <div class="intro-x text-lg mt-3 opacity-70">You don't have the necessary permissions to access this page. <br>Please contact your administrator if you believe this is an error.</div>
                
                <div class="mt-10">
                    <a href="{{ route('dashboard') }}" class="intro-x btn btn-primary py-3 px-6 rounded-full shadow-lg transition-all hover:px-8 border-none flex items-center justify-center inline-flex">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
        <!-- END: Error Page -->
    </div>
@endsection
