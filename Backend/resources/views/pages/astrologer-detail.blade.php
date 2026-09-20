@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">{{ucfirst($professionTitle)}} Details</h2>
    </div>
    <!-- BEGIN: Profile Info -->
    @foreach ($result as $astrologerDetail)
        <div class="intro-y box  pt-5 mt-5">

            <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5 px-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                        @if (Request::segment(2))
                        <img class="rounded-full"
                                     src="{{ Str::startsWith($astrologerDetail->profileImage, ['http://','https://']) ? $astrologerDetail->profileImage : '/' . $astrologerDetail->profileImage }}"
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                     alt="Customer profileImage"
                                     onclick="openImage('{{ $astrologerDetail->profileImage }}')" />
                        @else
                            <img class="rounded-full"
                                     src="{{ Str::startsWith($astrologerDetail->profileImage, ['http://','https://']) ? $astrologerDetail->profileImage : '/' . $astrologerDetail->profileImage }}"
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                     alt="Customer profileImage"
                                     onclick="openImage('{{ $astrologerDetail->profileImage }}')" />
                        @endif
                    </div>
                    <div class="ml-5">
                        <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                            {{ $astrologerDetail->name ? $astrologerDetail->name : '--' }}</div>
                        <div class="text-slate-500">
                            {{ @$astrologerDetail->countryCode ? @$astrologerDetail->countryCode : '--' }} {{ $astrologerDetail->contactNo ? $astrologerDetail->contactNo : '--' }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Details</div>
                    <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            {{ $astrologerDetail->email ? $astrologerDetail->email : '--' }}
                        </div>

                        <div class="truncate sm:whitespace-normal flex items-center mt-1">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp mr-2" viewBox="0 0 16 16">
                          <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg>
                            {{ $astrologerDetail->whatsappNo ? $astrologerDetail->whatsappNo : '--' }}
                        </div>

                         <div class="truncate sm:whitespace-normal flex items-center mt-1">
                           Aadhar :
                            {{ $astrologerDetail->aadharNo ? $astrologerDetail->aadharNo : '--' }}
                        </div>

                        <div class="truncate sm:whitespace-normal flex items-center mt-1">
                           Pan :
                            {{ $astrologerDetail->pancardNo ? $astrologerDetail->pancardNo : '--' }}
                        </div>

                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Details</div>
                    <div class="flex items-center justify-center lg:justify-start mt-2">
                        <div class="flex">
                            Total Order:
                            <span class="ml-3 font-medium text-success">
                                {{ $astrologerDetail->totalOrder ? $astrologerDetail->totalOrder : '--' }} Order
                            </span>
                            <!-- Edit Button -->
                            <button class="ml-4 px-2 py-1 text-sm bg-blue-500  rounded btn btn-danger" onclick="document.getElementById('editModal').style.display='block'">
                                Edit
                            </button>
                        </div>

                                <!-- Modal for Editing -->
                                <div id="editModal" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center" style="display:none;">
                                    <div class="bg-white p-5 rounded-lg">
                                        <h3 class="text-lg font-bold mb-4">Edit Total Order</h3>
                                        <form action="{{route('editTotalOrder')}}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="totalOrder" class="block font-medium">Total Order:</label>
                                                <input type="number" id="totalOrder" name="totalOrder" class="border border-gray-300 p-2 rounded w-full" value="{{ $astrologerDetail->totalOrder }}">
                                                 <input type="hidden" id="astroId" name="id" value="{{ $astrologerDetail->id }}">
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="button" class="px-4 py-2 mr-2 bg-gray-300 rounded" onclick="document.getElementById('editModal').style.display='none'">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="px-4 py-2 bg-green-500  rounded">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Followers : <span
                                class="ml-3 font-medium text-danger">{{ $astrologerDetail->totalFollower ? $astrologerDetail->totalFollower : '--' }}
                                Follower</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Total Chat Min: <span
                                class="ml-3 font-medium text-warning">{{ $astrologerDetail->chatMin ? $astrologerDetail->chatMin : '--' }}
                                Minutes</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Total Call Min: <span
                                class="ml-3 font-medium text-warning">{{ $astrologerDetail->callMin ? $astrologerDetail->callMin : '--' }}
                                Minutes</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="link-tab" class="p-3">

                <ul class="nav nav-link-tabs" role="tablist">
                    <li id="example-1-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#example-tab-1"
                            type="button" role="tab" aria-controls="example-tab-1" aria-selected="true">
                            Basic Detail
                        </button>
                    </li>
                    <li id="example-2-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-2"
                            type="button" role="tab" aria-controls="example-tab-2" aria-selected="false">
                            Wallet
                        </button>
                    </li>
                    <li id="example-3-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-3"
                            type="button" role="tab" aria-controls="example-tab-3" aria-selected="false">
                            Chat History
                        </button>
                    </li>
                    <li id="example-4-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-4"
                            type="button" role="tab" aria-controls="example-tab-4" aria-selected="false">
                            Call History
                        </button>
                    </li>
                    <li id="example-5-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-5"
                            type="button" role="tab" aria-controls="example-tab-5" aria-selected="false">
                            Report
                        </button>
                    </li>
                    <li id="example-11-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-11"
                            type="button" role="tab" aria-controls="example-tab-11" aria-selected="false">
                            Puja
                        </button>
                    </li>
                    <li id="example-6-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-6"
                            type="button" role="tab" aria-controls="example-tab-6" aria-selected="false">
                            Followers List
                        </button>
                    </li>
                    <li id="example-7-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-7"
                            type="button" role="tab" aria-controls="example-tab-7" aria-selected="false">
                            Notification List
                        </button>
                    </li>
                    <li id="example-8-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-8"
                            type="button" role="tab" aria-controls="example-tab-8" aria-selected="false">
                            Gift List
                        </button>
                    </li>
                    <li id="example-9-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-9"
                            type="button" role="tab" aria-controls="example-tab-9" aria-selected="false">
                            Documents
                        </button>
                    </li>

                     <li id="example-10-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-10"
                            type="button" role="tab" aria-controls="example-tab-10" aria-selected="false">
                            Bank Details
                        </button>
                    </li>
                </ul>
                <div class="tab-content astrologer-tab-content mt-5">
                    <div id="example-tab-1" class="tab-pane leading-relaxed active" role="tabpanel"
                        aria-labelledby="example-1-tab">


                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 2xl:col-span-12">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="col-span-12 md:col-span-6">
                                        <div class="intro-y box p-5 mt-12 sm:mt-5" style="height:100%">
                                            <div
                                                class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                                    Skill Details</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Gender</div>
                                                    <div class="text-danger flex text-xs font-medium tooltip cursor-pointer ml-2"
                                                        title="49% Higher than last month">
                                                    </div>
                                                </div>
                                                <div class="ml-auto">{{ $astrologerDetail->gender }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Date of Birth</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ date('d-m-Y', strtotime($astrologerDetail->birthDate)) ? date('d-m-Y', strtotime($astrologerDetail->birthDate)) : '--' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>{{ucfirst($professionTitle)}} Category</div>
                                                    <div class="text-success flex text-xs font-medium tooltip cursor-pointer ml-2"
                                                        title="49% Higher than last month">
                                                    </div>
                                                </div>

                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->astrologerCategoryId as $astroCat)
                                                        <span> {{ $astroCat->name }},</span>
                                                    @endforeach
                                                </div>

                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Primary Skill</div>
                                                </div>
                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->primarySkill as $primarySkill)
                                                        <span> {{ $primarySkill->name }},</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>All Skill</div>
                                                    <div class="text-danger flex text-xs font-medium tooltip cursor-pointer ml-2"
                                                        title="49% Higher than last month">

                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->allSkill as $allSkill)
                                                        <span> {{ $allSkill->name }},</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Language</div>
                                                </div>
                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->languageKnown as $language)
                                                        <span> {{ $language->languageName }},</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Your charges(per min)</div>
                                                </div>

                                                <div class="ml-auto">
                                                    {{ $currency->value }}{{ $astrologerDetail->charge }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Video charges</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $currency->value }}{{ $astrologerDetail->videoCallRate ? $astrologerDetail->videoCallRate : '0' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Report charges</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $currency->value }}{{ $astrologerDetail->reportRate ? $astrologerDetail->reportRate : '0' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Expirence in year</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->experienceInYears ? $astrologerDetail->experienceInYears : '0' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>How many hours you can contribute daily</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->dailyContribution ? $astrologerDetail->dailyContribution : '0' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Where did you hear about {{ucfirst($appname)}}</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->hearAboutAstroguru }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-12 md:col-span-6 ">
                                        <div class="intro-y box p-5 mt-12 sm:mt-5" style="height:100%">
                                            <div
                                                class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                                    Other Details</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Why do you think we should onboard you?</div>
                                                <div class="ml-auto">{{ $astrologerDetail->whyOnBoard }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Suitable time for interview</div>
                                                <div class="ml-auto">{{ $astrologerDetail->interviewSuitableTime }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Currently Live City</div>
                                                <div class="ml-auto">{{ $astrologerDetail->currentCity }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Main source of bussiness</div>
                                                <div class="ml-auto">{{ $astrologerDetail->mainSourceOfBusiness }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Highest Qualification</div>
                                                <div class="ml-auto">{{ $astrologerDetail->highestQualification }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Degree/Diploma</div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->degree ? $astrologerDetail->degree : '--' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Expected Minimum Earning</div>
                                                <div class="ml-auto">{{ $astrologerDetail->minimumEarning }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Expected Maximum Earning</div>
                                                <div class="ml-auto">{{ $astrologerDetail->maximumEarning }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Login Bio</div>
                                                <div class="ml-auto">{{ $astrologerDetail->loginBio }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Number of Foreign Country you Lived</div>
                                                <div class="ml-auto">{{ $astrologerDetail->NoofforeignCountriesTravel }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Currently Working</div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->currentlyworkingfulltimejob }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Good Quality</div>
                                                <div class="ml-auto">{{ $astrologerDetail->goodQuality }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Biggest challenge you faced</div>
                                                <div class="ml-auto">Biggest challenge you faced
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>A customer asking same question repatedly: What will you do</div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->whatwillDo }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-6 mt-5">
                            <div class="col-span-12 2xl:col-span-12">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="col-span-12 md:col-span-6">
                                        <div class="intro-y box p-5 mt-12 sm:mt-5" style="height:100%">
                                            <div
                                                class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                                    Availability</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="items-center">
                                                    @foreach ($astrologerDetail->astrologerAvailability as $availability)
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div class="row"> {{ $availability->day }}</div>
                                                            @foreach ($availability->time as $time)
                                                                <div class="text-xs font-medium tooltip cursor-pointer mb-4"
                                                                    style="display: inline-block">
                                                                    @if ($time->fromTime != null)
                                                                        <div class="box p-2"
                                                                            style="background-color: #e0e8f1">
                                                                            {{ $time->fromTime }} -
                                                                            {{ $time->toTime }} </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                    <div class="flex items-center mb-5">
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div>Chat Availability</div>
                                                        </div>
                                                        <div class="ml-auto">
                                                            {{ $astrologerDetail->chatStatus ? $astrologerDetail->chatStatus : '--' }}
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center mb-5">
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div>Call Availability</div>
                                                        </div>
                                                        <div class="ml-auto">
                                                            {{ $astrologerDetail->callStatus ? $astrologerDetail->callStatus : '--' }}
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center mb-5">
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div>Wait Time</div>
                                                        </div>
                                                        <div class="ml-auto">
                                                            {{ $astrologerDetail->chatWaitTime ? $astrologerDetail->chatWaitTime : '--' }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="example-tab-2" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-2-tab">

                        <div class="intro-y">
                            @if (count($astrologerDetail->wallet) > 0)
                                @foreach ($astrologerDetail->wallet as $wallet)
                                    <div class="intro-y">
                                        <div class="box px-4 py-4 mb-3 flex items-center">
                                            <div class="ml-4 mr-auto">
                                                    <div class="font-medium">
                                                @if ($wallet->transactionType != 'Gift' && $wallet->transactionType != 'ProductRefCommission' && $wallet->transactionType != 'courseOrder')
                                                    {{ $wallet->transactionType }} with {{ $wallet->name }} for {{ $wallet->totalMin }} minutes
                                                @elseif($wallet->transactionType == 'ProductRefCommission')
                                                    Received Product Commission Referred to {{$wallet->productRefName ?? 'User'}}
                                                @elseif($wallet->transactionType == 'courseOrder')
                                                        Course Purchased
                                                @else
                                                    Received Gift From {{ $wallet->name }}
                                                @endif

                                                </div>
                                                <div class="text-slate-500 text-xs mt-0.5">
                                                    {{ date('d-m-Y h:i a', strtotime($wallet->created_at)) }}
                                                </div>
                                            </div>
                                            <div class="flex items-center">

                                                <div
                                                    class="ml-4 mr-2 {{ $wallet->transactionType == 'courseOrder' ? 'text-danger' : 'text-success' }}">
                                                    <div class="font-medium">
                                                        @if($wallet->transactionType == 'courseOrder') (-) @else <span>(+)</span> @endif  {{ $currency->value }}{{ $wallet->amount }}</div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center w-30">
                                    <h5>No Wallet Transaction Found</h5>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div id="example-tab-3" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-3-tab">
                        @if (count($astrologerDetail->chatHistory) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">
                                @foreach ($astrologerDetail->chatHistory as $chatHistory)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="image-fit" style="height:150px;width:150px">
                                                    <img class="rounded-full" style="width: 100%; height: 100%;"
                                                        src="/{{ $chatHistory->profile }}"
                                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                        alt="{{$professionTitle}} image" />
                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3">
                                                    {{ $chatHistory->name }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-2">
                                                    <div class="flex items-center">
                                                        {{ date('d-m-Y h:i a', strtotime($chatHistory->created_at)) }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                                        {{ $chatHistory->contactNo }}
                                                    </div>
                                                    <div
                                                        class="flex items-center mt-2 {{ $chatHistory->chatStatus == 'Pending' ? 'text-success' : 'text-danger' }}">
                                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                                        {{ $chatHistory->chatStatus }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Chat Request Found</h5>
                            </div>
                        @endif
                    </div>
                    <div id="example-tab-4" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-4-tab">
                        @if (count($astrologerDetail->callHistory) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">

                                @foreach ($astrologerDetail->callHistory as $callHistory)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="h-20 2xl:h-56 image-fit" style="height:150px;width:150px">
                                                    <img class="rounded-full" style="width: 100%; height: 100%;"
                                                        src="/{{ $callHistory->profile }}"
                                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                        alt="{{$professionTitle}} image" />
                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3">
                                                    {{ $callHistory->name }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-2">
                                                    <div class="flex items-center">
                                                        {{ date('d-m-Y h:i a', strtotime($callHistory->created_at)) }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                                        {{ $callHistory->contactNo }}
                                                    </div>
                                                    <div
                                                        class="flex items-center mt-2 {{ $callHistory->callStatus == 'Accepted' ? 'text-success' : 'text-danger' }}">
                                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                                        {{ $callHistory->callStatus }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Call Request Found</h5>
                            </div>
                        @endif

                    </div>
                    <div id="example-tab-5" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-5-tab">
                        <div class="col-span-12 xl:col-span-4 mt-6">
                            <div class="mt-5">
                                @if (count($astrologerDetail->report) > 0)
                                    @foreach ($astrologerDetail->report as $report)
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center">
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium text-success">
                                                        {{ $report->firstName }} {{ $report->lastName }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.5">
                                                        {{ date('d-m-Y h:i a', strtotime($report->created_at)) }}
                                                    </div>
                                                    <div class="text-slate-900 text-x mt-0.9">
                                                        {{ $report->reportType }}
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Report Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="example-tab-11" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-9-tab">
                        <div class="col-span-12 xl:col-span-4 mt-6">
                            <div class="mt-5">
                                @if (count($astrologerDetail->pujaorders) > 0)
                                    @foreach ($astrologerDetail->pujaorders as $orders)
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center">
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium text-success">
                                                        {{ $orders->puja_name }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.5">
                                                        {{ date('d-m-Y h:i a', strtotime($orders->created_at)) }}
                                                    </div>
                                                    <div class="text-slate-900 text-x mt-0.9">
                                                        {{ $orders->package_name }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.9">
                                                        Puja Start : {{ date('d-m-Y h:i a', strtotime($orders->puja_start_datetime)) }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.9">
                                                        Puja End : {{ date('d-m-Y h:i a', strtotime($orders->puja_end_datetime)) }}
                                                    </div>
                                                    <div class="text text mt-0.9">
                                                        <span class="text-danger mr-2">Order Amount:</span>
                                                        {{ $currency->value }}{{ $orders->order_total_price }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Puja Order Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="example-tab-6" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-6-tab">
                        <div class="grid grid-cols-12 gap-6 mt-5">

                            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                                @if (count($astrologerDetail->follower) > 0)
                                    <table class="table table-report -mt-2" aria-label="follower">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap">#</th>
                                                <th class="whitespace-nowrap">PROFILE</th>
                                                <th class="whitespace-nowrap">NAME</th>
                                                <th class="text-center whitespace-nowrap">CONTACT NO</th>
                                                <th class="text-center whitespace-nowrap">DATE</th>
                                                <th class="text-center whitespace-nowrap">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp

                                            @foreach ($astrologerDetail->follower as $follower)
                                                <tr class="intro-x">
                                                    <td>{{ ++$no }} </td>
                                                    <td>
                                                        <div class="flex">
                                                            <div class="w-10 h-10 image-fit zoom-in">
                                                                <img class="rounded-full" src="/{{ $follower->profile }}"
                                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                                    alt="{{$professionTitle}} image" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="font-medium whitespace-nowrap">
                                                            {{ $follower->userName }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $follower->contactNo }}</td>
                                                    <td class="text-center whitespace-nowrap">
                                                        {{ date('d-m-Y', strtotime($follower->created_at)) }}
                                                    </td>
                                                    <td class="table-report__action w-56">
                                                        <div class="flex justify-center items-center">
                                                            <a class="flex items-center mr-3 text-success" href="/admin/customers/{{$follower->userId}}">
                                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Followers Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="example-tab-7" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-7-tab">
                        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                            @if (count($astrologerDetail->notification) > 0)
                                <table class="table table-report mt-2" aria-label="notification">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap" style="text-align: center">TITLE</th>
                                            <th class="whitespace-nowrap" style="text-align: center">DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                        @endphp

                                        @foreach ($astrologerDetail->notification as $notification)
                                            <tr class="intro-x">
                                                <td>{{ ++$no }} </td>
                                                <td>
                                                    <div class="font-medium" style="text-align: center">
                                                        {{ $notification->title }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $notification->description }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            @else
                                <div class="text-center w-30">
                                    <h5>No Notification List Found</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="example-tab-8" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-8-tab">
                        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                            @if (count($astrologerDetail->gifts) > 0)
                                <table class="table table-report mt-2" aria-label="gift">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap" style="text-align: center">Name</th>
                                            <th class="whitespace-nowrap" style="text-align: center">GIFT NAME</th>
                                            <th class="whitespace-nowrap" style="text-align: center">AMOUNT</th>
                                            <th class="whitespace-nowrap" style="text-align: center">DATE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                        @endphp

                                        @foreach ($astrologerDetail->gifts as $gift)
                                            <tr class="intro-x">
                                                <td>{{ ++$no }} </td>
                                                <td>
                                                    <div class="font-medium" style="text-align: center">
                                                        {{ $gift->userName }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $gift->giftName }}</td>
                                                <td class="text-center">
                                                  (+) {{ $currency->value }}{{ $gift->giftAmount ? $gift->giftAmount : 0 }}
                                                </td>
                                                <td class="text-center">
                                                    {{ date('d-m-Y', strtotime($gift->created_at)) }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            @else
                                <div class="text-center w-30">
                                    <h5>No Gift List Found</h5>
                                </div>
                            @endif
                        </div>
                    </div>



                    <div id="example-tab-9" class="tab-pane leading-relaxed" role="tabpanel"
                    aria-labelledby="example-9-tab">

                    @php
                        $documents = \App\Models\AstrologerDocument::all();
                    @endphp

                    <div class="grid grid-cols-12 gap-6 mt-5">
                        @foreach ($documents as $document)
                            @php
                                $inputName = Str::snake($document->name);
                            @endphp

                            @if (!empty($astrologerDetail->$inputName))
                                <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                    <div class="box">
                                        <div class="p-5">
                                            <div class="h-20 2xl:h-56 image-fit" style="height:150px;width:150px">
                                                <img class="class="rounded-full"
                                     src="{{ Str::startsWith($astrologerDetail->$inputName, ['http://','https://']) ? $astrologerDetail->$inputName : '/' . $astrologerDetail->$inputName }}"
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                     alt="Customer $inputName"
                                     onclick="openImage('{{ $astrologerDetail->$inputName }}')" />
                                            </div>
                                            <div class="text-center mt-2">{{ ucfirst(str_replace('_', ' ', $document->name)) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>


                <div id="example-tab-10" class="tab-pane leading-relaxed" role="tabpanel"
                    aria-labelledby="example-10-tab">



                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 md:col-span-6">
                        <div class="intro-y box p-5" style="height:100%">
                            <div class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                    Bank Details</div>
                            </div>
                            <div class="flex items-center mb-5">
                                <div class="flex items-center">
                                    <div>IFSC Code</div>
                                    <div class="text-danger flex text-xs font-medium tooltip cursor-pointer ml-2"
                                        >
                                    </div>
                                </div>
                                <div class="ml-auto">{{ $astrologerDetail->ifscCode }}</div>
                            </div>
                            <div class="flex items-center mb-5">
                                <div class="flex items-center">
                                    <div>Bank Name</div>
                                </div>
                                <div class="ml-auto">
                                    {{ $astrologerDetail->bankName }}
                                </div>
                            </div>

                             <div class="flex items-center mb-5">
                                <div class="flex items-center">
                                    <div>Bank Branch</div>
                                </div>
                                <div class="ml-auto">
                                    {{ $astrologerDetail->bankBranch }}
                                </div>
                            </div>

                            <div class="flex items-center mb-5">
                                <div class="flex items-center">
                                    <div>Account Type</div>
                                </div>
                                <div class="ml-auto">
                                    {{ $astrologerDetail->accountType }}
                                </div>
                            </div>

                             <div class="flex items-center mb-5">
                                <div class="flex items-center">
                                    <div>Account Number</div>
                                </div>
                                <div class="ml-auto">
                                    {{ $astrologerDetail->accountNumber }}
                                </div>
                            </div>

                             <div class="flex items-center mb-5">
                                <div class="flex items-center">
                                    <div>Upi</div>
                                </div>
                                <div class="ml-auto">
                                    {{ $astrologerDetail->upi }}
                                </div>
                            </div>

                        </div>
                    </div>

                    </div>
                </div>

                </div>

            </div>

        </div>
    @endforeach

<!-- Modal -->
<div id="imageModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center; z-index: 1000;">
    <div style="position: relative; max-width: 90%; max-height: 90%; overflow: auto;">
        <!-- Full Image -->
        <img id="modalImage" src="" alt="Full View" style="max-width: 100%; height: auto; object-fit: contain; border-radius: 8px;" />
        <!-- Close Button -->
        <button onclick="closeImageModal()" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
    </div>
</div>
@endsection
@section('script')
    <script type="text/javascript"></script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>

<script>
    function showImageModal(imageSrc) {
     const modal = document.getElementById('imageModal');
     const modalImage = document.getElementById('modalImage');

     // Set the modal image source
     modalImage.src = imageSrc;
     // Display the modal
     modal.style.display = 'flex';
 }

 function closeImageModal() {
     const modal = document.getElementById('imageModal');
     // Hide the modal
     modal.style.display = 'none';
 }

 </script>
@endsection
