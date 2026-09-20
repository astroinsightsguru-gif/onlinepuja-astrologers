@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
<style>
    .form-check-input[type="radio"] {
        border: 1px solid #cbd5e1;
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
    }

    .form-check-input[type="radio"]:checked {
        border-color: #3b82f6 !important;
        background-color: #3b82f6 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='2' fill='%23fff'/%3e%3c/svg%3e") !important;
        background-size: 60% 60%;
        background-repeat: no-repeat;
        background-position: center;
    }

    .form-check-label {
        cursor: pointer;
        margin-left: 0.25rem;
        vertical-align: middle;
    }

    .form-check {
        display: flex;
        align-items: center;
    }
</style>
    <div class="loader"></div>
    <form id="editastrologer" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible ">
            </div>
        </div>
        <!-- BEGIN: Profile Info -->
        <div class="intro-y box  pt-5 mt-5">
            <div id="link-tab" class="p-3">
                <button type="submit"class="btn btn-primary shadow-md mr-2 d-inline addbtn">Save
                </button>
                <ul class="nav nav-link-tabs" role="tablist">
                    <li id="example-1-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#example-tab-1"
                            type="button" role="tab" aria-controls="example-tab-1" aria-selected="true">
                            Personal Detail
                        </button>
                    </li>
                    <li id="example-2-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-2"
                            type="button" role="tab" aria-controls="example-tab-2" aria-selected="false">
                            Skill Detail
                        </button>
                    </li>
                    <li id="example-5-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-5"
                            type="button" role="tab" aria-controls="example-tab-5" aria-selected="false">
                            Bank Details
                        </button>
                    </li>
                    <li id="example-3-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-3"
                            type="button" role="tab" aria-controls="example-tab-3" aria-selected="false">
                            Other Details
                        </button>
                    </li>
                    <li id="example-4-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-4"
                            type="button" role="tab" aria-controls="example-tab-4" aria-selected="false">
                            Availability
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-5 editastrologertab">
                     <div id="example-tab-1" class="tab-pane leading-relaxed active" role="tabpanel"
                        aria-labelledby="example-1-tab">
                        <div class="input">
                            <div>
                                <input type="hidden" name="id" id="id" class="form-control"
                                    placeholder="Customer Name" value="{{ $astrologer['id'] }}">
                                <label for="regular-form-1" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="{{ucfirst($professionTitle)}} Name" value="{{ $astrologer['name'] }}"
                                    onkeypress="return Validate(event);" >
                            </div>
                        </div>
                        <div class="input mt-3">
                            <div>
                                <label for="regular-form-1" class="form-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="{{ucfirst($professionTitle)}} Email" onkeypress="return validateJavascript(event);" value="{{ $astrologer['email'] }}" >
                            </div>
                        </div>
                        <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    {{-- <div> --}}
                                        <label for="regular-form-1" class="form-label">Mobile Number</label>

                                        <div class="input-group">
                                            <input type="text" id="countryCode1" value="{{ $astrologer['countryCode'] }}" placeholder="+91" name="countryCode"
                                            class="form-control rounded-left" style="max-width: 60px;"  oninput="this.value = this.value.replace(/[^0-9\+]/g, '').replace(/^(\+?)(\d{1,4})$/, '$1$2')"
                                            title="Country code should be a number, optionally prefixed with '+'." maxlength="5" onkeypress="return validateJavascript(event);">

                                        <input type="text" name="contactNo" id="contactNo" class="form-control"
                                            placeholder="ContactNo" value="{{ $astrologer['contactNo'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="title" class="form-label">Country</label>
                                        <select name="country" class="form-control select2 country"
                                            data-placeholder="Choose Your country">
                                            <option>Select Country</option>
                                            @foreach ($country as $countryName)
                                                <option value={{ $countryName->nicename }} {{ $astrologer['country'] == $countryName->nicename ? 'selected' : '' }}>
                                                    {{ $countryName->nicename }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>



                         <div class="intro-y col-span-6 md:col-span-6">
    <div class="input mt-3">
        <div>
            <label for="regular-form-1" class="form-label">Whatsapp Number</label>
            <input type="text"
                name="whatsappNo"
                id="whatsappNo"
                class="form-control"
                title="Phone number should contain only 10 digits numbers."
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                maxlength="10"
                onkeypress="return validateJavascript(event);"
                placeholder="Whatsapp Number"
                value="{{ $astrologer['whatsappNo'] ?? '' }}">
        </div>
    </div>
</div>

<div class="intro-y col-span-6 md:col-span-6">
    <div class="input mt-3">
        <div>
            <label for="regular-form-1" class="form-label">Aadhar Number</label>
            <input type="text"
                name="aadharNo"
                id="aadharNo"
                class="form-control"
                title="Aadhar number should contain only 12 digits numbers."
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                maxlength="12"
                onkeypress="return validateJavascript(event);"
                placeholder="Aadhar Number"
                value="{{ $astrologer['aadharNo'] ?? '' }}">
        </div>
    </div>
</div>

<div class="intro-y col-span-6 md:col-span-6">
    <div class="input mt-3">
        <div>
            <label for="regular-form-1" class="form-label">Pan Number</label>
            <input type="text"
                name="pancardNo"
                id="pancardNo"
                class="form-control"
                title="Pan number should contain only 10 characters."
                maxlength="10"
                onkeypress="return validateJavascript(event);"
                placeholder="Pan Card Number"
                value="{{ $astrologer['pancardNo'] ?? '' }}">
        </div>
    </div>
</div>


                            <div class="intro-y col-span-6 md:col-span-6">
                                <div>
                                    <label for="profile" class="form-label">Profile Image</label>
                                    <img id="thumb" width="150px" src="/{{ $astrologer['profileImage'] }}"
                                        alt="Customer image" onerror="this.style.display='none';" />
                                    <input type="file" class="mt-2" name="profileImage" id="profileImage"
                                        onchange="preview()" accept="image/*">
                                </div>
                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">
                                <div>
                                    <label for="astro_video" class="form-label">Astrologer Video</label>
                                    @if (!empty($astrologer['astro_video']))
                                        <video id="videoPreview" width="250" controls class="mt-2">
                                            <source id="videoSource" src="/{{ $astrologer['astro_video'] }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif

                                    <input type="file" class="mt-2" name="astro_video" id="astro_video" onchange="previewVideo()" accept="video/*">
                                </div>
                            </div>

                            @foreach ($documents as $document)
                            @php
                                $inputName = Str::snake($document->name);
                                $existingImage = $astrologer[$inputName] ?? null; // Existing image if available
                            @endphp

                                <div class="intro-y col-span-6 md:col-span-6">
                                    <div>
                                        <label for="{{ $inputName }}" class="form-label">{{ $document->name }}</label>
                                        @if($existingImage)
                                            <img id="thumb_{{ $inputName }}" width="150px" src="/{{ $existingImage }}"
                                                alt="{{ $document->name }} image" onerror="this.style.display='none';" />
                                        @endif
                                        <input type="file" class="mt-2" name="{{ $inputName }}" id="{{ $inputName }}"
                                            onchange="preview2('{{ $inputName }}')" accept="image/*">
                                    </div>
                                </div>
                            @endforeach




                        </div>


                    </div>

                    <div id="example-tab-2" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-2-tab">
                        <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="title" class="form-label">Select Gender</label>
                                        <select data-minimum-results-for-search="Infinity" name="gender"
                                            class="form-control select2" data-placeholder="Gender">
                                            <option Value="Female"
                                                {{ $astrologer['gender'] == 'Female' ? 'selected' : '' }}>
                                                Female
                                            </option>
                                            <option Value="Male" {{ $astrologer['gender'] == 'Male' ? 'selected' : '' }}>
                                                Male
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label id="input-group" class="form-label">Birth Date</label>
                                        <input type="date" class="form-control" placeholder="Unit"
                                            aria-describedby="input-group-3" name="birthDate"
                                            value="{{ date('Y-m-d', strtotime($astrologer['birthDate'])) }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">{{ucfirst($professionTitle)}} Category</label>
                                    <select name="astrologerCategoryId[]" class="form-control select2 category" multiple
                                        data-placeholder="Choose Your Category">
                                        @foreach ($astrologerCategory as $categroy)
                                            <option value={{ $categroy->id }}>
                                                {{ $categroy->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Primary Skills</label>
                                    <select name="primarySkill[]" class="form-control select2 primary" multiple
                                        data-placeholder="Choose Your Primary Skills">
                                        @foreach ($skills as $skill)
                                            <option value={{ $skill->id }}>
                                                {{ $skill->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">All Skills</label>
                                    <select name="allSkill[]" class="form-control select2 all" multiple
                                        data-placeholder="Choose Your Primary Skills">
                                        @foreach ($skills as $skill)
                                            <option value={{ $skill->id }}>
                                                {{ $skill->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Language</label>
                                    <select name="languageKnown[]" class="form-control select2 language" multiple
                                        data-placeholder="Choose Language">
                                        @foreach ($language as $lang)
                                            <option value={{ $lang->id }}>
                                                {{ $lang->languageName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                             <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Add Your Charge(As per
                                            Minute In INR)</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="charge" id="charge" class="form-control"
                                            placeholder="Charge" value="{{ $astrologer['charge'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Add Your video charge(As per
                                            Minute In INR)</label>
                                        <input type="text" onkeypress="return validateJavascript(event);" name="videoCallRate" id="videoCallRate"
                                            class="form-control" placeholder="VideoCall Rate"
                                            value="{{ $astrologer['videoCallRate'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Add Your report charge(As per
                                            Minute In INR)</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="reportRate" id="reportRate" class="form-control"
                                            placeholder="Reprot Rate" value="{{ $astrologer['reportRate'] }}" >
                                    </div>
                                </div>
                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Add Your Charge(As per
                                            Minute In USD)</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="charge_usd" id="charge_usd" class="form-control"
                                            placeholder="Charge" value="{{ $astrologer['charge_usd'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Add Your video charge(As per
                                            Minute In USD)</label>
                                        <input type="text" onkeypress="return validateJavascript(event);" name="videoCallRate_usd" id="videoCallRate_usd"
                                            class="form-control" placeholder="VideoCall Rate"
                                            value="{{ $astrologer['videoCallRate_usd'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Add Your report charge(As per
                                            Minute In USD)</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="reportRate_usd" id="reportRate_usd" class="form-control"
                                            placeholder="Reprot Rate" value="{{ $astrologer['reportRate_usd'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Experience In Years</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="experienceInYears" id="experienceInYears"
                                            class="form-control" placeholder="Experience In Years"
                                            value="{{ $astrologer['experienceInYears'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">How many hours you can contribute
                                            daily?</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="dailyContribution" id="dailyContribution"
                                            class="form-control" placeholder="Daily Contribution"
                                            value="{{ $astrologer['dailyContribution'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Where did you hear about
                                        {{ucfirst($appname)}}?</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="hearAboutAstroguru" id="hearAboutAstroguru"
                                            class="form-control" placeholder="Youtube,Facebook"
                                            value="{{ $astrologer['hearAboutAstroguru'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="flex flex-col sm:flex-row mt-2">
                                    <label for="regular-form-1" class="form-label">Are you working on any other
                                        platform?</label>
                                    <div class="flex flex-col sm:flex-row mt-2">
                                        <div class="form-check mr-2">
                                            <input class="form-check-input" type="radio"
                                                name="isWorkingOnAnotherPlatform" value=1
                                                {{ $astrologer['isWorkingOnAnotherPlatform'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="radio-switch-4">Yes</label>
                                        </div>
                                        <div class="form-check mr-2 mt-2 sm:mt-0">
                                            <input class="form-check-input" type="radio"
                                                name="isWorkingOnAnotherPlatform" value=0
                                                {{ !$astrologer['isWorkingOnAnotherPlatform'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="radio-switch-5">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="example-tab-5" class="tab-pane leading-relaxed" role="tabpanel"
                    aria-labelledby="example-5-tab">
                    <div class="intro-y grid grid-cols-12 gap-6 mt-5">

                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">IFSC Code</label>
                                    <input
                                          onkeypress="return validateJavascript(event);" type="text" name="ifscCode" id="ifscCode" class="form-control"
                                        placeholder="IFSC Code"  value="{{ $astrologer['ifscCode'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">Bank Name</label>
                                    <input
                                        onkeypress="return validateJavascript(event);" type="text" name="bankName" id="bankName" class="form-control"
                                        placeholder="Bank Name" value="{{ $astrologer['bankName'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">Bank Branch</label>
                                    <input
                                        onkeypress="return validateJavascript(event);" type="text" name="bankBranch" id="bankBranch" class="form-control"
                                        placeholder="Bank Branch" value="{{ $astrologer['bankBranch'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">Account Type</label>
                                    <select data-minimum-results-for-search="Infinity" id="accountType"
                                    name="accountType" class="form-control select"
                                    data-placeholder="Account Type">

                                        <option value='Saving' {{ old('accountType',  $astrologer['accountType'] ?? '') == 'Saving' ? 'selected' : '' }}>
                                            Saving</option>
                                        <option value='Current' {{ old('accountType',  $astrologer['accountType'] ?? '') == 'Current' ? 'selected' : '' }}>
                                            Current</option>
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">Bank Account Number</label>
                                    <input inputmode="numeric"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="20" onkeypress="return validateJavascript(event);" type="text" name="accountNumber" id="accountNumber" class="form-control"
                                        placeholder="Bank Account Number"  value="{{ $astrologer['accountNumber'] }}">
                                </div>
                            </div>
                        </div>




                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">Account Holder Name</label>
                                    <input
                                        onkeypress="return validateJavascript(event);" type="text" name="accountHolderName" id="accountHolderName" class="form-control"
                                        placeholder="Account Holder Name" value="{{ $astrologer['accountHolderName'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="intro-y col-span-6 md:col-span-6">
                            <div class="input mt-3">
                                <div>
                                    <label for="regular-form-1" class="form-label">Upi Id</label>
                                    <input
                                        onkeypress="return validateJavascript(event);" type="text" name="upi" id="upi" class="form-control"
                                        placeholder="Upi Id" value="{{ $astrologer['upi'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                    <div id="example-tab-3" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-3-tab">
                        <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Why do you think we should onboard
                                            you?</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="whyOnBoard" id="whyOnBoard" class="form-control"
                                            placeholder="Why we should on board you?"
                                            value="{{ $astrologer['whyOnBoard'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">What is suitable time for
                                            interview?</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="interviewSuitableTime" id="interviewSuitableTime"
                                            class="form-control" placeholder="Enter Suitable Time For Interview"
                                            value="{{ $astrologer['interviewSuitableTime'] }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Which city do you currently live
                                            in?</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="currentCity" id="currentCity" class="form-control"
                                            placeholder="City" value="{{ $astrologer['currentCity'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Main Source of business(Other than
                                        astrology)?</label>
                                    <select data-minimum-results-for-search="Infinity" id="mainSourceOfBusiness"
                                        name="mainSourceOfBusiness" class="form-control select2"
                                        data-placeholder="Main Source of business">
                                        @foreach ($mainSourceBusiness as $source)
                                            <option value='{{ $source->jobName }}'>
                                                {{ $source->jobName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Select your highest qualification</label>
                                    <select name="highestQualification" id="highestQualification"
                                        class="form-control select2" data-placeholder="Highest Qualification">
                                        @foreach ($highestQualification as $highest)
                                            <option value='{{ $highest->qualificationName }}'>
                                                {{ $highest->qualificationName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Degree / Diploma</label>
                                    <select data-minimum-results-for-search="Infinity" id="degree" name="degree"
                                        class="form-control select2" data-placeholder="Degree">
                                        @foreach ($qualifications as $qua)
                                            <option value='{{ $qua->degreeName }}'>
                                                {{ $qua->degreeName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">College/School/University</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="college" id="college" class="form-control"
                                            placeholder="Enter your College/School/University"
                                            value="{{ $astrologer['college'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">From where did you learn
                                            Astrology?</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="learnAstrology" id="learnAstrology"
                                            class="form-control" placeholder="From where did you learn Astrology"
                                            value="{{ $astrologer['learnAstrology'] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Instagram profile link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="instaProfileLink" id="instaProfileLink"
                                            class="form-control" placeholder="Please let us know your Instagram profile"
                                            value="{{ $astrologer['instaProfileLink'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Facebook profile link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="facebookProfileLink" id="facebookProfileLink"
                                            class="form-control" placeholder="Please let us know your Facebook profile"
                                            value="{{ $astrologer['facebookProfileLink'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">LinkedIn profile link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="linkedInProfileLink" id="linkedInProfileLink"
                                            class="form-control" placeholder="Please let us know your LinkedIn profile"
                                            value="{{ $astrologer['linkedInProfileLink'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Youtube profile link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="youtubeChannelLink" id="youtubeChannelLink"
                                            class="form-control" placeholder="Please let us know your Youtube profile"
                                            value="{{ $astrologer['youtubeChannelLink'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Website profile link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="websiteProfileLink" id="websiteProfileLink"
                                            class="form-control" placeholder="Please let us know your Website profile"
                                            value="{{ $astrologer['websiteProfileLink'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="flex flex-col sm:flex-row mt-2">
                                    <label for="regular-form-1" class="form-label">Did anybody refer you to
                                    {{ucfirst($appname)}}?</label>
                                    <div class="form-check mr-2">
                                        <input class="form-check-input" type="radio" name="isAnyBodyRefer" value=1
                                            {{ $astrologer['isAnyBodyRefer'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radio-switch-4">Yes</label>
                                    </div>
                                    <div class="form-check mr-2 mt-2 sm:mt-0">
                                        <input class="form-check-input" type="radio" name="isAnyBodyRefer" value=0
                                            {{ !$astrologer['isAnyBodyRefer'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radio-switch-5">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                @if ($astrologer['isAnyBodyRefer'])
                                    <div class="input mt-3">
                                        <div>
                                            <label for="regular-form-1" class="form-label">Name of the person who referred
                                                you?</label>
                                            <input onkeypress="return validateJavascript(event);" type="text" name="referedPerson" id="referedPerson"
                                                class="form-control" placeholder="Please let us know your Website profile"
                                                value="{{ $astrologer['referedPerson'] }}">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Minimum Earning Expection from
                                        {{ucfirst($appname)}}</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="minimumEarning" id="minimumEarning"
                                            class="form-control" placeholder="Minimum Earning"
                                            value="{{ $astrologer['minimumEarning'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Maximum Earning Expection from
                                        {{ucfirst($appname)}}</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="maximumEarning" id="maximumEarning"
                                            class="form-control" placeholder="Maximum Earning"
                                            value="{{ $astrologer['maximumEarning'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">Long bio</label>
                                        <textarea onkeypress="return validateJavascript(event);" name="loginBio" id="loginBio" class="form-control" placeholder="Describe bio">{{ $astrologer['loginBio'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Number of the foreign countries you
                                        lived/travelled to?</label>
                                    <select data-minimum-results-for-search="Infinity" name="NoofforeignCountriesTravel"
                                        class="form-control select2" data-placeholder="Travel Countries">
                                        @foreach ($countryTravel as $travel)
                                            <option value={{ $travel->NoOfCountriesTravell }}>
                                                {{ $travel->NoOfCountriesTravell }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="preview mt-3">
                                    <label for="title" class="form-label">Are you currently working a fulltime
                                        job?</label>
                                    <select data-minimum-results-for-search="Infinity" id="currentlyworkingfulltimejob" name="currentlyworkingfulltimejob"
                                        class="form-control select2" data-placeholder="Currently Working">
                                        @foreach ($jobs as $working)
                                            <option value='{{ $working->workName }}'>
                                                {{ $working->workName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">What are some good qualities of
                                            perfect
                                            {{ucfirst($professionTitle)}}?</label>
                                        <textarea onkeypress="return validateJavascript(event);" name="goodQuality" id="goodQuality" class="form-control" placeholder="Describe Here">{{ $astrologer['goodQuality'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">What was the biggest challenge you
                                            faced and how did you overcome it?</label>
                                        <textarea onkeypress="return validateJavascript(event);" name="biggestChallenge" id="biggestChallenge" class="form-control" placeholder="Describe Here">{{ $astrologer['biggestChallenge'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-6 md:col-span-6">
                                <div class="input mt-3">
                                    <div>
                                        <label for="regular-form-1" class="form-label">A customer is asking the same
                                            question
                                            repeatedly: what will you do?</label>
                                        <textarea onkeypress="return validateJavascript(event);" name="whatwillDo" id="whatwillDo" class="form-control" placeholder="Describe Here">{{ $astrologer['whatwillDo'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="example-tab-4" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-4-tab">
                        @foreach ($astrologer['astrologerAvailability'] as $availability)
                            <div class="input mt-2 sm:mt-0">
                                <h4 class="font-medium text-lg mt-3 d-inline">{{ $availability['day'] }}</h4>
                                <button style="padding: 3px 6px;"class="btn btn-sm btn-primary add-field d-inline"
                                    type="button" onclick="addField('{{ $availability['day'] }}')">+</button>
                                <div class="intro-y grid grid-cols-12 gap-6" id="astrologerfield">
                                    @foreach ($availability['time'] as $timeIndex => $time)
                                        <div
                                            class="{{ $availability['day'] }}_fromTime{{ $timeIndex }} intro-y col-span-6 md:col-span-6">
                                            <label id="input-group"
                                                class="astrologerAvailability_{{ $availability['day'] }}_time{{ $timeIndex }}_fromTime form-label">FromTime
                                                <button
                                                    style="padding: 2px 7px;
                                                border-radius: 50%"
                                                    class="btn btn-sm btn-primary add-field d-inline" type="button"
                                                    onclick="removeField('{{ $availability['day'] }}',{{ $timeIndex }})">-</button></label>
                                            <input type="hidden" class="form-control"
                                                id="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][day]"
                                                placeholder="FromTime"
                                                name="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][day]"
                                                aria-describedby="input-group-4" value="{{ $availability['day'] }}">
                                            <input type="time" class="form-control" placeholder="FromTime"
                                                name="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][time][{{ $timeIndex }}][fromTime]"
                                                id="astrologerAvailability_{{ $availability['day'] }}_time{{ $timeIndex }}_fromTime"
                                                aria-describedby="input-group-4" value="{{ $time['fromTime'] }}">
                                        </div>
                                        <div
                                            class="{{ $availability['day'] }}_toTime{{ $loop->index }} intro-y col-span-6 md:col-span-6">
                                            <label id="input-group"
                                                class="astrologerAvailability_{{ $availability['day'] }}_time{{ $loop->index }}_toTime form-label">ToTime</label>
                                            <input type="time" class="form-control" placeholder="FromTime"
                                                name="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][time][{{ $loop->index }}][toTime]"
                                                id="astrologerAvailability_{{ $availability['day'] }}_time{{ $loop->index }}_toTime"
                                                aria-describedby="input-group-4" value="{{ $time['toTime'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        </div>
    </form>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  ></script>
    <script type="text/javascript"></script>
    <script>
        var spinner = $('.loader');
        $(window).on('load', function() {
            $('.loader').hide();
        });
        var category = {{ Js::from($astrologer['astrologerCategoryId']) }};
        var primarySkill = {{ Js::from($astrologer['primarySkill']) }};
        var allSkill = {{ Js::from($astrologer['allSkill']) }};
        var language = {{ Js::from($astrologer['languageKnown']) }};
        var mainSourceOfBusiness = {{ Js::from($astrologer['mainSourceOfBusiness']) }};
        var degree = {{ Js::from($astrologer['degree']) }};
        var highestQualification = {{ Js::from($astrologer['highestQualification']) }};
        var currentlyworkingfulltimejob = {{ Js::from($astrologer['currentlyworkingfulltimejob']) }};
        category = category.split(',')
        primarySkill = primarySkill.split(',')
        allSkill = allSkill.split(',')
        languageKnown = language.split(',')
        $('.category').val(category).trigger('change');
        $('.primary').val(primarySkill).trigger('change');
        $('.all').val(allSkill).trigger('change');
        $('.language').val(languageKnown).trigger('change');
        $('#mainSourceOfBusiness').val(mainSourceOfBusiness).trigger('change');
        $('#degree').val(degree).trigger('change');
        $('#highestQualification').val(highestQualification).trigger('change');
        $('#currentlyworkingfulltimejob').val(currentlyworkingfulltimejob).trigger('change');
    </script>
    <script>
        $(document).ready(function() {
            jQuery('.select2').select2({
                allowClear: true,
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });
        jQuery(function() {
    // Edit form submission
    jQuery('#editastrologer').submit(function(e) {
        e.preventDefault();
        spinner.show();

        // Clear previous errors
        jQuery('.is-invalid').removeClass('is-invalid');
        jQuery('.text-danger').remove();

        var formData = new FormData(this);

        // Validate UPI ID
        var upiId = jQuery('#upi').val();
        var upiRegex = /^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/;
        if (upiId.trim().length > 0 && !upiRegex.test(upiId)) {
            toastr.error('Please enter a valid UPI ID.');
            spinner.hide();
            return false;
        }

        jQuery.ajax({
            type: 'POST',
            url: "{{ route('editAstrologerApi') }}",
            data: formData,
            dataType: 'JSON',
            processData: false,
            contentType: false,
            success: function(data) {
                if (jQuery.isEmptyObject(data.error)) {
                    spinner.hide();
                    location.href = "{{route('astrologers')}}";
                } else {
                    // Process errors and find first error field
                    var firstErrorTab = null;

                    jQuery.each(data.error, function(key, value) {
                        var field = jQuery('[name="' + key + '"], [name="' + key + '[]"]');
                            field.addClass('is-invalid');

                            var errorElement = jQuery('<span class="text-danger">' + value[0] + '</span>');

                            if (key === 'astrologerCategoryId' || key === 'primarySkill' ||
                                key === 'allSkill' || key === 'languageKnown') {
                                var selectContainer = field.closest('.preview');
                                if (!selectContainer.find('.text-danger').length) {
                                    selectContainer.append(errorElement);
                                }
                            } else if (field.is(':radio') || field.is(':checkbox')) {
                                field.closest('.form-check').append(errorElement);
                            } else if (field.hasClass('select2-hidden-accessible')) {
                                field.next('.select2-container').after(errorElement);
                            } else if (key === 'countryCode' || key === 'contactNo') {
                                var inputGroup = field.closest('.input-group');
                                if (!inputGroup.next('.text-danger').length) {
                                    inputGroup.after('<div class="text-danger w-full mt-1">' + value[0] + '</div>');
                                }
                            } else {
                                field.after(errorElement);
                            }

                        // Find the tab containing this field
                        if (!firstErrorTab) {
                            var tabPane = field.closest('.tab-pane');
                            if (tabPane.length) {
                                firstErrorTab = tabPane.attr('id');
                            }
                        }
                    });

                    // Switch to tab with first error
                    if (firstErrorTab) {
                        var tabButton = jQuery('[aria-controls="' + firstErrorTab + '"]');
                        if (tabButton.length) {
                            tabButton.click();

                            // Scroll to the error field
                            var errorField = jQuery('[name="' + Object.keys(data.error)[0] + '"]');
                            if (errorField.length) {
                                jQuery('html, body').animate({
                                    scrollTop: errorField.offset().top - 100
                                }, 500);
                            }
                        }
                    }

                    spinner.hide();
                }
            },
            error: function(xhr) {
                spinner.hide();
                console.error(xhr.responseText);
                toastr.error('An unexpected error occurred. Please try again.');
            }
        });
    });

    // Clear errors when typing in edit form
    jQuery('#editastrologer').on('input', '[name="countryCode"], [name="contactNo"]', function() {
        var inputGroup = jQuery(this).closest('.input-group');
        inputGroup.find('input').removeClass('is-invalid');
        inputGroup.next('.text-danger').remove();
    });

    jQuery('#editastrologer').on('change', 'select.country', function() {
        var $select = jQuery(this);
        $select.removeClass('is-invalid');
        $select.next('.select2-container').removeClass('is-invalid');
        $select.closest('.input').find('.text-danger').remove();
    });

    jQuery('#editastrologer').on('input change', 'input:not([name="countryCode"], [name="contactNo"]), select, textarea', function() {
        jQuery(this).removeClass('is-invalid').next('.text-danger').remove();
    });
});

         function printErrorMsgs(msg) {
        toastr.clear(); // Clear previous toasts if any
        jQuery.each(msg, function(key, value) {
            toastr.warning(value[0]); // Display each validation error
        });
    }

        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
        }

        function preview2(inputId) {
            const fileInput = document.getElementById(inputId);
            const thumb = document.getElementById(`thumb_${inputId}`);

            if (fileInput.files && fileInput.files[0]) {
                if (thumb) { // <- Check if thumb element exists
                    thumb.style.display = "block";
                    thumb.src = URL.createObjectURL(fileInput.files[0]);
                } else {
                    // If thumb doesn't exist, create and insert it
                    const previewContainer = fileInput.parentNode;
                    const newThumb = document.createElement("img");
                    newThumb.id = `thumb_${inputId}`;
                    newThumb.width = 150;
                    newThumb.style.display = "block";
                    newThumb.src = URL.createObjectURL(fileInput.files[0]);
                    previewContainer.insertBefore(newThumb, fileInput);
                }
            }
        }

        function previewVideo() {
            const fileInput = document.getElementById('astro_video');

            if (fileInput.files && fileInput.files[0]) {
                const videoFile = fileInput.files[0];
                const videoPreview = document.getElementById('videoPreview');

                if (videoPreview) {
                    // If video element exists, update it
                    const videoSource = document.getElementById('videoSource');
                    videoSource.src = URL.createObjectURL(videoFile);
                    videoPreview.style.display = "block";
                    videoPreview.load(); // Reload video with new source
                } else {
                    // If video element doesn't exist, create it
                    const previewContainer = fileInput.parentNode;
                    const newVideo = document.createElement("video");
                    newVideo.id = "videoPreview";
                    newVideo.width = 250;
                    newVideo.controls = true;
                    newVideo.className = "mt-2";

                    const newSource = document.createElement("source");
                    newSource.id = "videoSource";
                    newSource.src = URL.createObjectURL(videoFile);
                    newSource.type = "video/mp4";

                    newVideo.appendChild(newSource);
                    previewContainer.insertBefore(newVideo, fileInput);
                }
            }
        }
        var times = {{ Js::from($astrologer['astrologerAvailability']) }};
        var dayTime = [];


        function addField($day) {
            if (times && times.length > 0) {
                dayTime = times.find(c => c.day == $day)['time'];
                dayTime.push({
                    fromTime: null,
                    toTime: null
                })
            }
            html = '';
            htmlto = '';
            html +=
                " <div class=" + $day + "_fromTime" + (dayTime.length - 1) +
                " intro-y col-span-6 md:col-span-6 mt-5'> <label id='input-group' class='mt-5 astrologerAvailability_" +
                $day +
                "_time" + (dayTime.length - 1) +
                "_fromTime form-label'>FromTime<button style='padding: 2px 7px;border-radius: 50%'class='btn btn-sm btn-primary add-field d-inline' type='button' onclick=removeField('" +
                $day + "'," + (dayTime.length - 1) +
                ")>-</button></label> <input id='astrologerAvailability[" + $day + "_" + (dayTime.length - 1) +
                "][day]' type='hidden' class='form-control' placeholder='FromTime' name='astrologerAvailability[" +
                $day + "_" + (dayTime.length - 1) + "][day]' aria-describedby='input-group-4' value=" + $day +
                "><input type = 'time' class = 'form-control' placeholder = 'FromTime' id='astrologerAvailability_" +
                $day + "_time" + (dayTime.length - 1) + "_fromTime' name = 'astrologerAvailability[" +
                $day + "_" + (dayTime.length - 1) + "][time][" + (dayTime.length - 1) +
                "][fromTime]' aria-describedby = 'input-group-4'></div>";
            htmlto +=
                ' <div class=' + $day + '_toTime' + (dayTime.length - 1) +
                ' intro-y col-span-6 md:col-span-6 mt-5"><label id="input-group" class="mt-5 form-label astrologerAvailability_' +
                $day + '_time' + (dayTime.length - 1) +
                '_toTime">ToTime</label><input type = "time" class = "form-control"  placeholder = "ToTime" name = "astrologerAvailability[' +
                $day + '_' + (dayTime.length - 1) + '][time][' + (dayTime.length - 1) +
                '][toTime]" id="astrologerAvailability_' +
                $day + '_time' + (dayTime.length - 1) + '_toTime"></div>'
            $('.' + $day + '_fromTime' + (dayTime.length - 2)).append(
                html
            );
            $('.' + $day + '_toTime' + (dayTime.length - 2)).append(
                htmlto
            );
        }

        function removeField($day, $index) {
            if (dayTime.length == 0)
                dayTime = times.filter(c => c.day == $day)[0]['time'];
            dayTime.splice($index, 1);

            $('#astrologerAvailability_' + $day + '_time' + $index + '_fromTime').remove();
            $('#astrologerAvailability_' + $day + '_time' + $index + '_toTime').remove();

            $('.astrologerAvailability_' + $day + '_time' + $index + '_fromTime').remove();
            $('.astrologerAvailability_' + $day + '_time' + $index + '_toTime').remove();
            $('#astrologerAvailability[' + $day + '_' + $index + '][day]').remove();
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


        document.getElementById('ifscCode').addEventListener('change', function(e) {
            var ifsccode = e.target.value;
            if (/^[A-Z]{4}0[A-Z0-9]{6}$/.test(ifsccode.toUpperCase())) {
                fetch(`https://ifsc.razorpay.com/${ifsccode.toUpperCase()}`)
                    .then(response => {
                        if (!response.ok) {
                            toastr.error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('bankName').value = data.BANK;
                        document.getElementById('bankBranch').value = data.BRANCH;
                    })
                    .catch(error => {
                        toastr.error(`There was a problem with the fetch operation: ${error}`);
                    });
            } else {
                toastr.error('Wrong IFSC code, please try again with the correct code');
            }
        });
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
    initializeAutocomplete('currentCity');

</script>
@endsection
