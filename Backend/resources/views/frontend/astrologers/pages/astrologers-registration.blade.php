@extends('frontend.layout.master')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')

@php
$countries = DB::table('countries')
->orderByRaw("CASE WHEN phonecode = 91 THEN 0 ELSE 1 END")
->get();
@endphp
<style>
    select[name="country"].select2+.select2-container {
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .step {
        display: none;
    }

    .step.active {
        display: block;
    }

    .red-color {
        color: #dc3545;
    }

    /* Hide number arrows */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
        /* Firefox */
    }

    /* Default styles for larger screens */
    #modalImage {
        width: 500px;
        height: 520px;
    }

    /* Mobile responsiveness */
    @media only screen and (max-width: 600px) {
        #modalImage {
            width: 100%;
            height: auto;
        }

        #imageModal div {
            max-width: 95%;
            max-height: 95%;
        }

        button {
            width: 25px;
            height: 25px;
            font-size: 14px;
        }
    }

    .otp-input {
        text-align: center;
        font-size: 1.25rem;
        width: 100%;
        height: 3rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease-in-out;
    }

    .otp-input:focus {
        outline: none;
        border-color: #4c62d1;
        box-shadow: 0 0 0 3px rgba(76, 98, 209, 0.25);
    }
</style>
<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">

                <span style="text-transform: capitalize; ">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">{{ucfirst($professionTitle)}} Registration</span>
                    </span>
                </span>

            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="container py-5">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('success') }}
        </div>
        @endif
        <div class="row pt-3 pb-lg-5">
            <div class="col-lg-6 col-12 order-lg-1">
                <form action="{{route('front.astrologerstore')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Step 1 -->
                    <div id="step1"
                        class="categorycontent step-1 sychics-join-form position-relative border px-4 pb-4 step active">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Personal
                                Details</small></h2>
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="name">Name<span class="red-color font-weight-bold">*</span></label>
                                <input type="text" id="name" value="{{ old('name') }}" name="name" class="form-control rounded" required pattern="^[a-zA-Z\s]{2,50}$" title="Name should contain only letters and be between 2 and 50 characters long."
                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email Address<span class="red-color font-weight-bold">*</span></label>
                                <input type="email" id="email" value="{{ old('email') }}" name="email" class="form-control rounded" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country">Country<span class="red-color font-weight-bold">*</span></label>
                                <select class="form-control select2" name="country" id="country" required>
                                    <option value="">Select Country</option>
                                    @foreach($country as $countryName)
                                    <option value="{{$countryName->nicename}}" {{ (collect(old('country'))->contains($countryName->nicename)) ? 'selected' : '' }}>{{$countryName->nicename}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contactNo">Contact No<span class="red-color font-weight-bold">*</span></label>
                                <div class="d-flex inputform country-dropdown-container" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">

                                    <!-- Country Code Dropdown -->
                                    <select class="form-control select2" id="countryCode1" name="countryCode" style="border: none; border-right: 1px solid #ddd; border-radius: 0; width: 20%;">
                                        @foreach ($countries as $country)
                                        <option data-country="in" value="+{{ $country->phonecode }}" data-ucname="India">
                                            +{{ $country->phonecode }} {{ $country->iso }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <!-- Mobile Number Input -->
                                    <input class="form-control mobilenumber text-box single-line" id="contact" oninput="enforceMaxLength(this, 10)" name="contactNo" type="number" value="{{ old('contactNo') }}" style="border: none; border-radius: 0; width: 130%;" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatsappNo">Whatsapp No<span class="red-color font-weight-bold">*</span></label>
                                <div>
                                    <input type="number" value="{{ old('whatsappNo') }}" id="whatsappNo" name="whatsappNo" class="form-control"

                                        title="Whatsapp number should contain only numbers." required oninput="enforceMaxLength(this, 10)">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="">Profile<span class="red-color font-weight-bold">*</span></label>
                                <input type="file" class="form-control" value="{{ old('profileImage') }}" id="profileImage" name="profileImage" style="height: 44px" required accept="image/*">
                            </div>


                            @foreach ($documents as $document)
                            @php
                            $inputName = Str::snake($document->name);
                            @endphp


                            <div class="col-md-6 mb-3">
                                <label for="{{ $inputName }}">{{ $document->name }}</label>
                                <input type="file" class="form-control" value="{{ old($inputName) }}" id="{{ $inputName }}" name="{{ $inputName }}" style="height: 44px" accept="image/*">
                            </div>


                            @endforeach



                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check">

                                <label class="text-dark">
                                    <small>
                                        <input type="checkbox" id="tandc" class="align-baseline" required>
                                        I Agree To {{ucfirst($appname)}} <a class="text-dark" style="color:#EE4E5E !important"
                                            href="{{route('front.astrologerTermsCondition')}}" target="_blank">Terms Of Use</a>&nbsp;and&nbsp;<a
                                            class="text-dark" style="color:#EE4E5E !important" href="{{route('front.astrologerPrivacyPolicy')}}"
                                            target="_blank">Privacy Policy</a>
                                    </small>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="nextStep('step1', 'step2')">Next</a>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div id="step2"
                        class="categorycontent step-2 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Skill Details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender">Gender<span class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="gender" id="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birthDate">Birth Date<span class="red-color font-weight-bold">*</span></label>
                                <input type="date" value="{{ old('birthDate') }}" name="birthDate" id="birthDate"
                                    class="form-control rounded border-pink " required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="aadharNo">Aadhar No<span class="red-color font-weight-bold">*</span></label>
                                <div>

                                    <input type="number" value="{{ old('aadharNo') }}" id="aadharNo" name="aadharNo" class="form-control rounded-right"
                                        required oninput="enforceMaxLength(this, 12)">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contactNo">Pan No<span class="red-color font-weight-bold">*</span></label>
                                <div>

                                    <input type="text" value="{{ old('pancardNo') }}" id="pancardNo" name="pancardNo" class="form-control rounded-right"

                                        required maxlength="10"
                                        required>
                                </div>
                            </div>



                            <div class="col-md-6 mb-3">
                                <label for="astrologerCategoryId">Category<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control select2" name="astrologerCategoryId[]" id="astrologerCategoryId"
                                    multiple required>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}" {{ (collect(old('astrologerCategoryId'))->contains($category->id)) ? 'selected' : '' }}>{{$category->name}}</option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="primarySkill">Primary Skills<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="primarySkill[]" id="primarySkill" required>
                                    @foreach($skills as $skill)
                                    <option value="{{$skill->id}}" {{ (collect(old('primarySkill'))->contains($skill->id)) ? 'selected' : '' }}>{{$skill->name}}</option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="allSkill">All Skills<span class="red-color font-weight-bold">*</span></label>
                                <select class="form-control select2" name="allSkill[]" id="allSkill" multiple required>
                                    @foreach($skills as $skill)
                                    <option value="{{$skill->id}}" {{ (collect(old('allSkill'))->contains($skill->id)) ? 'selected' : '' }}>{{$skill->name}}</option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="languageKnown">Language<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control select2" name="languageKnown[]" id="languageKnown" multiple required>
                                    @foreach($languages as $language)
                                    <option value="{{$language->id}}" {{ (collect(old('languageKnown'))->contains($language->id)) ? 'selected' : '' }}>{{$language->languageName}}</option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="charge">Add your charge(as per min in INR)<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="number" value="{{ old('charge') }}" id="charge" name="charge" class="form-control rounded"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="videoCallRate">Add your video charge(as per min in INR)<span class="red-color font-weight-bold">*</span></label>
                                <input type="number" value="{{ old('videoCallRate') }}" id="videoCallRate" name="videoCallRate"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reportRate">Add your report charge ( in INR) <span class="red-color font-weight-bold">*</span></label>
                                <input type="number" value="{{ old('reportRate') }}" id="reportRate" name="reportRate" class="form-control rounded"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="charge_usd">Add your charge(as per min in USD)<span class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('charge_usd') }}" id="charge_usd" name="charge_usd" class="form-control rounded"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="videoCallRate_usd">Add your video charge(as per min in USD) <span class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('videoCallRate_usd') }}" id="videoCallRate_usd" name="videoCallRate_usd"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reportRate_usd">Add your report charge ( in USD) <span class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('reportRate_usd') }}" id="reportRate_usd" name="reportRate_usd" class="form-control rounded"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="experienceInYears">Experience in years<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="number" value="{{ old('experienceInYears') }}" id="experienceInYears" name="experienceInYears"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dailyContribution">How many hours you can contribute daily?<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="number" value="{{ old('dailyContribution') }}" id="dailyContribution" name="dailyContribution"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hearAboutAstroguru">Where did you hear about {{ucfirst($appname)}}?</label>
                                <input type="text" value="{{ old('hearAboutAstroguru') }}" id="hearAboutAstroguru" name="hearAboutAstroguru"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Are you working on any other platform?</label><br>
                                <input type="radio" id="astro-yes" name="isWorkingOnAnotherPlatform" value="1"> Yes
                                <input type="radio" id="astro-no" name="isWorkingOnAnotherPlatform" value="0"> No
                            </div>
                            <!-- Container for the two input fields with a dotted border -->
                            <div id="platform-details-container" style="display: none; border: 2px dotted gray; padding: 15px; margin-bottom: 20px;">
                                <div class="row">
                                    <!-- Platform Name Input -->
                                    <div class="col-md-6 mb-3">
                                        <label for="platform-name">Name of Platform</label>
                                        <input type="text" id="platform-name" name="nameofplateform" class="form-control" disabled>
                                    </div>

                                    <!-- Monthly Earning Input -->
                                    <div class="col-md-6 mb-3">
                                        <label for="monthly-earning">Monthly Earning</label>
                                        <input type="number" id="monthly-earning" name="monthlyEarning" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="previousStep('step2', 'step1')">Previous</a>
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="nextStep('step2', 'step3')">Next</a>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div id="step3"
                        class="categorycontent step-3 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Other details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="whyOnBoard">Why do you think we should onboard you?<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" id="awhyOnBoard" name="whyOnBoard" value="{{ old('whyOnBoard') }}" class="form-control rounded"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="interviewSuitableTime">What is suitable time for interview?<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="time" value="{{ old('interviewSuitableTime') }}" id="interviewSuitableTime" name="interviewSuitableTime" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currentCity">Which city do you currently live in?</label>
                                <input type="text" value="{{ old('currentCity') }}" id="currentCity" name="currentCity" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mainSourceOfBusiness">Main Source of business(Other than astrology)?<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="mainSourceOfBusiness" id="mainSourceOfBusiness" required>
                                    @foreach ($mainSourceBusiness as $source)
                                    <option value='{{ $source->jobName }}' {{ (collect(old('mainSourceOfBusiness'))->contains($source->jobName)) ? 'selected' : '' }}>
                                        {{ $source->jobName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="highestQualification">Select your qualification<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="highestQualification" id="highestQualification" required>
                                    @foreach ($highestQualification as $highest)
                                    <option value='{{ $highest->qualificationName }}' {{ (collect(old('highestQualification'))->contains($highest->qualificationName)) ? 'selected' : '' }}>
                                        {{ $highest->qualificationName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="degree">Degree / Diploma<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="degree" id="degree">
                                    @foreach ($qualifications as $qua)
                                    <option value='{{ $qua->degreeName }}' {{ (collect(old('degree'))->contains($qua->degreeName)) ? 'selected' : '' }}>
                                        {{ $qua->degreeName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="college">College/School/University name</label>
                                <input type="text" value="{{ old('college') }}" id="college" name="college" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="learnAstrology">From where did you learn Astrology?</label>
                                <input type="text" value="{{ old('learnAstrology') }}" id="learnAstrology" name="learnAstrology"
                                    class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instaProfileLink">Instagram profile link</label>
                                <input type="text" value="{{ old('instaProfileLink') }}" id="instaProfileLink" name="instaProfileLink"
                                    class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="facebookProfileLink">Facebook profile link</label>
                                <input type="text" value="{{ old('facebookProfileLink') }}" id="facebookProfileLink" name="facebookProfileLink"
                                    class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="linkedInProfileLink">LinkedIn profile link</label>
                                <input type="text" value="{{ old('linkedInProfileLink') }}" id="linkedInProfileLink" name="linkedInProfileLink"
                                    class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="youtubeChannelLink">Youtube profile link</label>
                                <input type="text" value="{{ old('youtubeChannelLink') }}" id="youtubeChannelLink" name="youtubeChannelLink"
                                    class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="websiteProfileLink">Website profile link</label>
                                <input type="text" value="{{ old('websiteProfileLink') }}" id="websiteProfileLink" name="websiteProfileLink"
                                    class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Did anybody referred you?</label><br>
                                <input type="radio" id="refer-yes" name="isAnyBodyRefer" value="1"> Yes
                                <input type="radio" id="refer-no" name="isAnyBodyRefer" value="0"> No
                            </div>

                            <!-- Container for the referred person's name input with a dotted border -->
                            <div id="refer-details-container" style="display: none; border: 2px dotted gray; padding: 15px; margin-bottom: 20px;">
                                <div class="row">
                                    <!-- Referred Person Name Input -->
                                    <div class="col-md-12 mb-3">
                                        <label for="referred-person-name">Referred Person Name</label>
                                        <input type="text" id="referred-person-name" name="referedPerson" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 text-center">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="previousStep('step3', 'step2')">Previous</a>
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="nextStep('step3', 'step4')">Next</a>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div id="step4"
                        class="categorycontent step-4 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Step 4</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minimumEarning">Minimum Earning Expection<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('minimumEarning') }}" id="minimumEarning" name="minimumEarning"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="maximumEarning">Maximum Earning Expection<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('maximumEarning') }}" id="maximumEarning" name="maximumEarning"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="NoofforeignCountriesTravel">Number of the foreign countries you lived/travel
                                    to?</label>
                                <select class="form-control" name="NoofforeignCountriesTravel"
                                    id="NoofforeignCountriesTravel">
                                    @foreach ($countryTravel as $travel)
                                    <option value='{{ $travel->NoOfCountriesTravell }}' {{ (collect(old('NoofforeignCountriesTravel'))->contains($travel->NoOfCountriesTravell)) ? 'selected' : '' }}>
                                        {{ $travel->NoOfCountriesTravell }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currentlyworkingfulltimejob">Are you currently working a fulltime job?<span
                                        class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="currentlyworkingfulltimejob"
                                    id="currentlyworkingfulltimejob" required>
                                    @foreach ($jobs as $working)
                                    <option value='{{ $working->workName }}' {{ (collect(old('currentlyworkingfulltimejob'))->contains($working->workName)) ? 'selected' : '' }}>
                                        {{ $working->workName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="loginBio">Long Bio*</label>
                                <textarea id="loginBio" name="loginBio" class="form-control rounded" oninput="countWords()" required>{{ old('loginBio') }} </textarea>
                                <small id="wordCount">0 words</small>

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="goodQuality">What are some good qualities of perfect {{$professionTitle}}?</label>
                                <textarea id="goodQuality" name="goodQuality" class="form-control rounded">{{ old('goodQuality') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="biggestChallenge">What was the biggest challenge you faced and how did you
                                    overcome it?<span class="red-color font-weight-bold">*</span></label>
                                <textarea id="biggestChallenge" name="biggestChallenge" class="form-control rounded" required>{{ old('biggestChallenge') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatwillDo">A customer is asking the same question repeatedly: what will you
                                    do?</label>
                                <textarea id="whatwillDo" name="whatwillDo" class="form-control rounded">{{ old('whatwillDo') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="previousStep('step4', 'step3')">Previous</a>
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="nextStep('step4', 'step5')">Next</a>
                        </div>
                    </div>

                    <!--Bank Details -->
                    <div id="step5"
                        class="categorycontent step-4 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Bank Details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ifscCode">IFSC Code<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('ifscCode') }}" id="ifscCode" name="ifscCode"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bankName">Bank Name<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('bankName') }}" id="bankName" name="bankName"
                                    class="form-control rounded" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bankBranch">Bank Branch<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('bankBranch') }}" id="bankBranch" name="bankBranch"
                                    class="form-control rounded" required>
                            </div>



                            <div class="col-md-6 mb-3">Account Type<span class="red-color font-weight-bold">*</span></label>
                                <select class="form-control" name="accountType"
                                    id="accountType" required>
                                    <option value="saving">
                                        Saving</option>
                                    <option value="current">
                                        Current</option>
                                </select>
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="accountNumber">Bank Account No<span
                                        class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('accountNumber') }}" id="accountNumber" name="accountNumber"
                                    class="form-control rounded" oninput="enforceMaxLength(this, 20)">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="confirmAccountNumber">Confirm Bank Account No<span class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('confirmAccountNumber') }}" id="confirmAccountNumber" name="confirmAccountNumber"
                                    class="form-control rounded" oninput="enforceMaxLength(this, 20)">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="accountHolderName">Account holder Name<span class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ old('accountHolderName') }}" id="accountHolderName" name="accountHolderName"
                                    class="form-control rounded" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="upi">Upi Id</label>
                                <input type="text" value="{{ old('upi') }}" id="upi" name="upi"
                                    class="form-control rounded">
                            </div>

                        </div>
                        <div class="col-12 text-center">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="previousStep('step5', 'step4')">Previous</a>
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="nextStep('step5', 'step6')">Next</a>
                        </div>
                    </div>

                    {{-- Step 6 --}}
                    <div id="step6"
                        class="categorycontent step-4 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Step 5</small>
                        </h2>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label>Availability<span class="red-color font-weight-bold">*</span></label>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label>Sunday</label>
                                        <input type="hidden" name="astrologerAvailability[0][day]" value="Sunday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="sunday-from">From Time</label>
                                                <input type="time" id="sunday-from" name="astrologerAvailability[0][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sunday-to">To Time</label>
                                                <input type="time" id="sunday-to" name="astrologerAvailability[0][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Monday</label>
                                        <input type="hidden" name="astrologerAvailability[1][day]" value="Monday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="monday-from">From Time</label>
                                                <input type="time" id="monday-from" name="astrologerAvailability[1][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="monday-to">To Time</label>
                                                <input type="time" id="monday-to" name="astrologerAvailability[1][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Tuesday</label>
                                        <input type="hidden" name="astrologerAvailability[2][day]" value="Tuesday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="tuesday-from">From Time</label>
                                                <input type="time" id="tuesday-from" name="astrologerAvailability[2][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="tuesday-to">To Time</label>
                                                <input type="time" id="tuesday-to" name="astrologerAvailability[2][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Wednesday</label>
                                        <input type="hidden" name="astrologerAvailability[3][day]" value="Wednesday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="wednesday-from">From Time</label>
                                                <input type="time" id="wednesday-from" name="astrologerAvailability[3][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="wednesday-to">To Time</label>
                                                <input type="time" id="wednesday-to" name="astrologerAvailability[3][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Thursday</label>
                                        <input type="hidden" name="astrologerAvailability[4][day]" value="Thursday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="thursday-from">From Time</label>
                                                <input type="time" id="thursday-from" name="astrologerAvailability[4][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="thursday-to">To Time</label>
                                                <input type="time" id="thursday-to" name="astrologerAvailability[4][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Friday</label>
                                        <input type="hidden" name="astrologerAvailability[5][day]" value="Friday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="friday-from">From Time</label>
                                                <input type="time" id="friday-from" name="astrologerAvailability[5][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="friday-to">To Time</label>
                                                <input type="time" id="friday-to" name="astrologerAvailability[5][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Saturday</label>
                                        <input type="hidden" name="astrologerAvailability[6][day]" value="Saturday">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="saturday-from">From Time</label>
                                                <input type="time" id="saturday-from" name="astrologerAvailability[6][time][0][fromTime]"
                                                    class="form-control rounded" placeholder="From Time">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="saturday-to">To Time</label>
                                                <input type="time" id="saturday-to" name="astrologerAvailability[6][time][0][toTime]"
                                                    class="form-control rounded" placeholder="To Time">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Repeat similar structure for other days of the week -->
                                </div>
                            </div>

                        </div>
                        <div class="col-12 text-center">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="previousStep('step6', 'step5')">Previous</a>
                            <button class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2">Sign Up</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 sychics-join-info pt-lg-0 pt-5">
                <h2><small class="font-weight-bold">BECOME "{{strtoupper($appname)}} VERIFIED" {{ucwords($professionTitle)}}: <b
                            class="red-color font-weight-bold">JOIN NOW!</b></small></h2>
                <p>
                    {{ucfirst($appname)}}, one of the best online astrology portals gives you a chance to be a part of its community
                    of best and top-notch {{ucfirst($professionTitle)}}s. Become a part of the team of {{ucfirst($professionTitle)}}s and offer your
                    consultations to clients from all across the globe, &amp; create an online, personalized brand presence.
                </p>
                <div class="row py-2">
                    <div class="col-sm-4 col-12 mb-sm-0 mb-3">
                        <div class="border border-danger rounded text-center p-3 h-100">
                            <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/verified-icon.svg') }}"
                                class="mb-1">
                            <span class="d-block font-weight-bold">Verified Expert</span>
                            <p class="mb-0">{{ucfirst($professionTitle)}}s</p>
                        </div>
                    </div>

                    <div class="col-sm-4 col-12 mb-sm-0 mb-3">
                        <div class="border border-danger rounded text-center p-3 h-100">
                            <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/24-availability-icon.svg') }}"
                                class="mb-1">
                            <span class="d-block font-weight-bold">24/7</span>
                            <p class="mb-0">Availability</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header d-flex justify-content-between align-items-center border-0 p-4 pb-0">
                    <h5 class="modal-title fs-4 fw-bold" id="otpModalLabel">Verify Your Account</h5>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4 pt-0">
                    <p class="text-center text-sm text-gray-500 mb-4">Enter the verification codes sent to your email and mobile number.</p>

                    <!-- Email OTP Section -->
                    <div class="mb-4">
                        <label for="emailOtpInput" class="form-label text-sm text-gray-500">Email OTP</label>
                        <input type="text" class="form-control emailInput" disabled value="">
                        <input type="hidden" class="form-control" id="emailResOtp" value="">
                        <input type="text" class="otp-input form-control" id="emailOtpInput" maxlength="6" placeholder="Enter 6-digit OTP">

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span id="emailTimerSpan" class="text-sm text-gray-500">Resend OTP in 00:60</span>
                            <button id="resendEmailOtpBtn" class="btn btn-link p-0 text-sm text-blue-600" disabled>Resend OTP</button>
                        </div>
                        <div id="emailMessageDiv" class="mt-2 text-center custom-message-div"></div>
                    </div>

                    <!-- Mobile OTP Section -->
                    <div>
                        <label for="mobileOtpInput" class="form-label text-sm text-gray-500">Mobile OTP</label>
                        <input type="text" class="form-control mobileInput" disabled value="">
                        <input type="hidden" class="form-control" id="mobileResOtp" value="">
                        <input type="text" class="otp-input form-control" id="mobileOtpInput" maxlength="6" placeholder="Enter 6-digit OTP">

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span id="mobileTimerSpan" class="text-sm text-gray-500">Resend OTP in 00:60</span>
                            <button id="resendMobileOtpBtn" class="btn btn-link p-0 text-sm text-blue-600" disabled>Resend OTP</button>
                        </div>
                        <button id="verifyMobileBtn" class="btn btn-primary w-100 fw-semibold py-2 rounded-lg shadow mt-4">
                            Verify
                        </button>
                        <div id="mobileMessageDiv" class="mt-2 text-center custom-message-div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End OTP Modal -->
    <!-- Modal -->
    <div id="imageModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center; z-index: 1000;">
        <div style="position: relative; max-width: 90%; max-height: 90%;">
            <!-- Full Image -->
            <img id="modalImage" src="" alt="Full View" style=" height: auto; border-radius: 8px;" />
            <!-- Close Button -->
            <button onclick="closeImageModal()" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
        </div>
    </div>
    @endsection
    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Global variables
        let emailVerified = false;
        let mobileVerified = false;

        // DOM elements - declared globally but initialized after DOM loads
        let emailOtpInput, mobileOtpInput, verifyEmailBtn, verifyMobileBtn;
        let resendEmailOtpBtn, resendMobileOtpBtn, emailTimerSpan, mobileTimerSpan;
        let emailMessageDiv, mobileMessageDiv;

        // Country code to phone number length mapping
        const countryNumberLengths = {
            '1': 10, // USA/Canada
            '7': 10, // Russia
            '20': 10, // Egypt
            '27': 9, // South Africa
            '30': 10, // Greece
            '31': 9, // Netherlands
            '32': 9, // Belgium
            '33': 9, // France
            '34': 9, // Spain
            '36': 9, // Hungary
            '39': 10, // Italy
            '40': 9, // Romania
            '41': 9, // Switzerland
            '43': 10, // Austria
            '44': 10, // UK
            '45': 8, // Denmark
            '46': 9, // Sweden
            '47': 8, // Norway
            '48': 9, // Poland
            '49': 10, // Germany
            '51': 9, // Peru
            '52': 10, // Mexico
            '53': 8, // Cuba
            '54': 10, // Argentina
            '55': 11, // Brazil
            '56': 9, // Chile
            '57': 10, // Colombia
            '58': 10, // Venezuela
            '60': 9, // Malaysia
            '61': 9, // Australia
            '62': 11, // Indonesia
            '63': 10, // Philippines
            '64': 9, // New Zealand
            '65': 8, // Singapore
            '66': 9, // Thailand
            '81': 10, // Japan
            '82': 10, // South Korea
            '84': 10, // Vietnam
            '86': 11, // China
            '90': 10, // Turkey
            '91': 10, // India
            '92': 10, // Pakistan
            '93': 9, // Afghanistan
            '94': 9, // Sri Lanka
            '95': 9, // Myanmar
            '98': 10, // Iran
            '212': 9, // Morocco
            '213': 9, // Algeria
            '216': 8, // Tunisia
            '218': 9, // Libya
            '220': 7, // Gambia
            '221': 9, // Senegal
            '234': 10, // Nigeria
            '254': 9, // Kenya
            '255': 9, // Tanzania
            '256': 9, // Uganda
            '351': 9, // Portugal
            '352': 9, // Luxembourg
            '353': 9, // Ireland
            '354': 7, // Iceland
            '355': 9, // Albania
            '356': 8, // Malta
            '357': 8, // Cyprus
            '358': 9, // Finland
            '359': 9, // Bulgaria
            '370': 8, // Lithuania
            '371': 8, // Latvia
            '372': 8, // Estonia
            '373': 8, // Moldova
            '374': 8, // Armenia
            '375': 9, // Belarus
            '376': 6, // Andorra
            '377': 8, // Monaco
            '378': 10, // San Marino
            '380': 9, // Ukraine
            '381': 9, // Serbia
            '382': 8, // Montenegro
            '385': 9, // Croatia
            '386': 8, // Slovenia
            '387': 8, // Bosnia
            '389': 8, // Macedonia
            '420': 9, // Czech Republic
            '421': 9, // Slovakia
            '423': 7, // Liechtenstein
            '850': 10, // North Korea
            '852': 8, // Hong Kong
            '853': 8, // Macau
            '855': 9, // Cambodia
            '856': 10, // Laos
            '880': 10, // Bangladesh
            '886': 9, // Taiwan
            '961': 8, // Lebanon
            '962': 9, // Jordan
            '963': 9, // Syria
            '964': 10, // Iraq
            '965': 8, // Kuwait
            '966': 9, // Saudi Arabia
            '967': 9, // Yemen
            '968': 8, // Oman
            '970': 9, // Palestine
            '971': 9, // UAE
            '972': 9, // Israel
            '973': 8, // Bahrain
            '974': 8, // Qatar
            '975': 8, // Bhutan
            '976': 8, // Mongolia
            '977': 10, // Nepal
            '992': 9, // Tajikistan
            '993': 8, // Turkmenistan
            '994': 9, // Azerbaijan
            '995': 9, // Georgia
            '996': 9, // Kyrgyzstan
            '998': 9 // Uzbekistan
        };

        // ==================== UTILITY FUNCTIONS ====================

        // Function to display error messages
        function showError(field, message) {
            clearError(field);

            const errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.style.color = '#dc3545';
            errorMessage.style.fontSize = '0.875rem';
            errorMessage.style.marginTop = '0.25rem';
            errorMessage.innerText = message;

            const container = field.closest('.country-dropdown-container');

            if (field.type === 'checkbox') {
                const label = field.closest('label') || field.nextElementSibling;
                if (label) {
                    label.parentNode.insertBefore(errorMessage, label.nextSibling);
                } else {
                    field.parentNode.insertBefore(errorMessage, field.nextSibling);
                }
            } else if ($(field).hasClass('select2-hidden-accessible')) {
                const select2Container = $(field).data('select2').$container;
                $(select2Container).after(errorMessage);
            } else if (container) {
                container.parentNode.insertBefore(errorMessage, container.nextSibling);
            } else {
                field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }

            field.classList.add('is-invalid');
        }

        // Function to clear error messages
        function clearError(field) {
            if ($(field).hasClass('select2-hidden-accessible')) {
                const select2Container = $(field).data('select2').$container;
                $(select2Container).next('.error-message').remove();
            } else if (field.type === 'checkbox') {
                const errorMessage = field.closest('.form-check')?.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            } else {
                const container = field.closest('.country-dropdown-container');
                if (container) {
                    const errorMessage = container.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                } else {
                    const errorMessage = field.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            }

            field.classList.remove('is-invalid');
        }

        // Function to validate URLs
        function validateLink(value) {
            const urlPattern = /^(https?:\/\/[^\s]+|www\.[^\s]+|#|)$/;
            return urlPattern.test(value.trim());
        }

        // Function to start a countdown timer
        function startTimer(duration, timerDisplay, resendBtn) {
            let timer = duration;
            resendBtn.disabled = true;
            timerDisplay.classList.remove('hidden');

            const interval = setInterval(() => {
                const minutes = parseInt(timer / 60, 10);
                const seconds = parseInt(timer % 60, 10);
                const displayMinutes = minutes < 10 ? "0" + minutes : minutes;
                const displaySeconds = seconds < 10 ? "0" + seconds : seconds;
                timerDisplay.textContent = `Resend OTP in ${displayMinutes}:${displaySeconds}`;

                if (--timer < 0) {
                    clearInterval(interval);
                    timerDisplay.textContent = '';
                    timerDisplay.classList.add('hidden');
                    resendBtn.disabled = false;
                }
            }, 1000);
        }

        // ==================== VALIDATION FUNCTIONS ====================

        function validateCountryCode() {
            const countryCodeField = document.getElementById('countryCode1');
            const countryCodeValue = countryCodeField.value.trim();
            const countryCodePattern = /^\+?[0-9]{1,4}$/;

            if (!countryCodePattern.test(countryCodeValue)) {
                showError(countryCodeField, 'Please enter a valid country code (e.g., +91 or 9).');
                return false;
            } else {
                clearError(countryCodeField);
                return true;
            }
        }

        function validateEmail() {
            const emailField = document.getElementById('email');
            const emailValue = emailField.value;
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (!emailPattern.test(emailValue)) {
                showError(emailField, 'Please enter a valid email address.');
                return false;
            } else {
                clearError(emailField);
                return true;
            }
        }

        function validateMobileNumber() {
            const mobileField = document.getElementById('contact');
            const mobileValue = mobileField.value;
            const countryCodeField = document.getElementById('countryCode1');
            const countryCode = countryCodeField.value.trim().replace(/^\+/, '');
            const expectedLength = countryNumberLengths[countryCode] || 10;

            const mobilePattern = new RegExp(`^[0-9]{${expectedLength}}$`);
            if (!mobilePattern.test(mobileValue)) {
                showError(mobileField, `Please enter a valid ${expectedLength}-digit mobile number.`);
                return false;
            } else {
                clearError(mobileField);
                return true;
            }
        }

        function validateBankAccountNumber() {
            const accountNumberField = document.getElementById('accountNumber');
            const accountNumberValue = accountNumberField.value.trim();

            if (!accountNumberValue) {
                showError(accountNumberField, 'Bank Account No is required.');
                return false;
            } else {
                clearError(accountNumberField);
                return true;
            }
        }

        function validateConfirmBankAccountNumber() {
            const confirmAccountNumberField = document.getElementById('confirmAccountNumber');
            const confirmAccountNumberValue = confirmAccountNumberField.value.trim();
            const accountNumberValue = document.getElementById('accountNumber').value.trim();

            if (!confirmAccountNumberValue) {
                showError(confirmAccountNumberField, 'Confirm Bank Account No is required.');
                return false;
            } else if (confirmAccountNumberValue !== accountNumberValue) {
                showError(confirmAccountNumberField, 'Bank Account No and Confirm Bank Account No do not match.');
                return false;
            } else {
                clearError(confirmAccountNumberField);
                return true;
            }
        }

        // ==================== API FUNCTIONS ====================

        function checkEmailContactExist() {
            const email = document.getElementById('email').value;
            const contactNo = document.getElementById('contact').value;
            const countryCode = $('#country').val().trim().replace('+', '');

            return fetch('/api/checkemailContactExist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        contactNo: contactNo,
                        countryCode: countryCode
                    })
                })
                .then(response => response.json())
                .catch(error => {
                    console.error('Error:', error);
                    throw error;
                });
        }

        function sendOtpEmailMobile(type) {
            const email = document.getElementById('email').value;
            const contactNo = document.getElementById('contact').value;
            const countryCode = $('#countryCode1').val().trim().replace('+', '');
            const storedEmailOtp = document.getElementById('emailResOtp').value;
            const storedMobileOtp = document.getElementById('mobileResOtp').value;

            return fetch('/api/sendOtpMobileEmail', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        contactNo: contactNo,
                        fromWeb: 1,
                        otptype: type,
                        storedEmailOtp : atob(storedEmailOtp),
                        storedMobileOtp : atob(storedMobileOtp),
                        countryCode: countryCode
                    })
                })
                .then(response => response.json())
                .catch(error => {
                    console.error('Error:', error);
                    throw error;
                });
        }

        // ==================== OTP FUNCTIONS ====================

        function resendEmailOtp() {
            sendOtpEmailMobile('email')
                .then(otpResponse => {
                    if (otpResponse.status === 200) {
                        document.getElementById('emailResOtp').value = otpResponse.email_otp;
                        emailMessageDiv.textContent = 'New Email OTP sent!';
                        emailMessageDiv.className = 'mt-2 text-center font-medium text-blue-500';
                        startTimer(60, emailTimerSpan, resendEmailOtpBtn);
                    }
                })
                .catch(error => {
                    console.error('Error resending email OTP:', error);
                });
        }

        function resendMobileOtp() {
            sendOtpEmailMobile('mobile')
                .then(otpResponse => {
                    if (otpResponse.status === 200) {
                        document.getElementById('mobileResOtp').value = otpResponse.mobile_otp;
                        mobileMessageDiv.textContent = 'New Mobile OTP sent!';
                        mobileMessageDiv.className = 'mt-2 text-center font-medium text-blue-500';
                        startTimer(60, mobileTimerSpan, resendMobileOtpBtn);
                    }
                })
                .catch(error => {
                    console.error('Error resending mobile OTP:', error);
                });
        }

        function verifyOtps() {
            const emailOtp = emailOtpInput.value;
            const mobileOtp = mobileOtpInput.value.trim();
            const storedEmailOtp = document.getElementById('emailResOtp').value;
            const storedMobileOtp = document.getElementById('mobileResOtp').value;

            // Verify Email OTP
            if (emailOtp.length === 6) {
                if (storedEmailOtp && atob(storedEmailOtp) == emailOtp) {
                    emailVerified = true;
                    emailMessageDiv.textContent = 'Email OTP Verified Successfully!';
                    emailMessageDiv.className = 'mt-2 text-center font-medium text-green-500';
                    emailOtpInput.style.borderColor = '#10b981';
                } else {
                    emailVerified = false;
                    emailMessageDiv.textContent = 'Invalid Email OTP!';
                    emailMessageDiv.className = 'mt-2 text-center font-medium text-red-500';
                    emailOtpInput.style.borderColor = '#ef4444';
                }
            } else {
                emailVerified = false;
                emailMessageDiv.textContent = 'Please enter a 6-digit Email OTP.';
                emailMessageDiv.className = 'mt-2 text-center font-medium text-red-500';
                emailOtpInput.style.borderColor = '#ef4444';
            }

            // Verify Mobile OTP
            if (mobileOtp.length === 6) {
                if (storedMobileOtp && atob(storedMobileOtp) == mobileOtp) {
                    mobileVerified = true;
                    mobileMessageDiv.textContent = 'Mobile OTP Verified Successfully!';
                    mobileMessageDiv.className = 'mt-2 text-center font-medium text-green-500';
                    mobileOtpInput.style.borderColor = '#10b981';
                } else {
                    mobileVerified = false;
                    mobileMessageDiv.textContent = 'Invalid Mobile OTP!';
                    mobileMessageDiv.className = 'mt-2 text-center font-medium text-red-500';
                    mobileOtpInput.style.borderColor = '#ef4444';
                }
            } else {
                mobileVerified = false;
                mobileMessageDiv.textContent = 'Please enter a 6-digit Mobile OTP.';
                mobileMessageDiv.className = 'mt-2 text-center font-medium text-red-500';
                mobileOtpInput.style.borderColor = '#ef4444';
            }

            // If both OTPs are verified, close modal and proceed
            if (emailVerified && mobileVerified) {
                setTimeout(() => {
                    $('#otpModal').modal('hide');
                    document.getElementById('step1').classList.remove('active');
                    document.getElementById('step2').classList.add('active');
                }, 1500);
            }
        }

        // ==================== NAVIGATION FUNCTIONS ====================

        function nextStep(currentStepId, nextStepId) {
            const emailValid = validateEmail();
            const mobileValid = validateMobileNumber();
            const countryCodeValid = validateCountryCode();
            let isValid = true;
            const requiredFields = document.querySelectorAll('#' + currentStepId + ' [required]');

            const linkFields = [
                'instaProfileLink',
                'facebookProfileLink',
                'linkedInProfileLink',
                'youtubeChannelLink',
                'websiteProfileLink'
            ];

            // Validate required fields
            requiredFields.forEach(function(field) {
                if (field.tagName.toLowerCase() === 'select') {
                    if (field.value === '' || field.value === 'Select Country') {
                        isValid = false;
                        showError(field, 'This field is required.');
                    } else {
                        clearError(field);
                    }
                } else if (field.tagName.toLowerCase() === 'textarea' && field.id === 'loginBio') {
                    const wordCount = field.value.trim().split(/\s+/).length;
                    if (wordCount < 50) {
                        isValid = false;
                        showError(field, 'Long Bio must have at least 50 words.');
                    } else {
                        clearError(field);
                    }
                } else if (field.type === 'checkbox' && field.id === 'tandc') {
                    if (!field.checked) {
                        isValid = false;
                        showError(field, 'You must agree to the Terms of Use and Privacy Policy.');
                    } else {
                        clearError(field);
                    }
                } else if (!field.value.trim()) {
                    isValid = false;
                    showError(field, 'This field is required.');
                } else {
                    clearError(field);
                }
            });

            // Validate bank account number and confirm bank account number (only for step 5)
            if (currentStepId === 'step5') {
                const accountNumberValid = validateBankAccountNumber();
                const confirmAccountNumberValid = validateConfirmBankAccountNumber();
                if (!accountNumberValid || !confirmAccountNumberValid) {
                    isValid = false;
                }
            }

            // Step 2 specific validations
            if (currentStepId === 'step2') {
                const aadharField = document.getElementById('aadharNo');
                if (aadharField && aadharField.value.trim().length !== 12) {
                    isValid = false;
                    showError(aadharField, 'Aadhar No must be exactly 12 digits.');
                } else if (aadharField) {
                    clearError(aadharField);
                }

                const panField = document.getElementById('pancardNo');
                if (panField && panField.value.trim().length !== 10) {
                    isValid = false;
                    showError(panField, 'Pan No must be exactly 10 characters.');
                } else if (panField) {
                    clearError(panField);
                }
            }

            // Validate social links
            linkFields.forEach(function(id) {
                const field = document.getElementById(id);
                if (field && field.value.trim()) {
                    if (!validateLink(field.value)) {
                        isValid = false;
                        showError(field, 'Please enter a valid URL.');
                    } else {
                        clearError(field);
                    }
                }
            });

            // Handle step1 with OTP verification
            if (isValid && emailValid && mobileValid && countryCodeValid && currentStepId === 'step1') {
                checkEmailContactExist()
                    .then(result => {
                        if (result.status === 400) {
                            if (result.messages.email) {
                                showError(document.getElementById('email'), result.messages.email);
                            }
                            if (result.messages.contact) {
                                showError(document.getElementById('contact'), result.messages.contact);
                            }
                        } else {
                            if (emailVerified && mobileVerified) {
                                document.getElementById(currentStepId).classList.remove('active');
                                document.getElementById(nextStepId).classList.add('active');
                            } else {
                                $('#otpModal').modal('show');
                                const email = document.getElementById('email').value;
                                const contactNo = document.getElementById('contact').value;
                                $('.emailInput').val(email);
                                $('.mobileInput').val(contactNo);

                                sendOtpEmailMobile('both')
                                    .then(otpResponse => {
                                        if (otpResponse.status === 200) {
                                            document.getElementById('emailResOtp').value =otpResponse.email_otp;
                                            document.getElementById('mobileResOtp').value = otpResponse.mobile_otp;

                                            emailMessageDiv.textContent = 'OTP sent to your email!';
                                            emailMessageDiv.className = 'mt-2 text-center font-medium text-blue-500';
                                            mobileMessageDiv.textContent = 'OTP sent to your mobile!';
                                            mobileMessageDiv.className = 'mt-2 text-center font-medium text-blue-500';

                                            // Start timers
                                            startTimer(60, emailTimerSpan, resendEmailOtpBtn);
                                            startTimer(60, mobileTimerSpan, resendMobileOtpBtn);
                                        } else {
                                            alert('Failed to send OTP. Please try again.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error sending OTP:', error);
                                        alert('Error sending OTP. Please try again.');
                                    });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking email/contact:', error);
                    });
            } else if (isValid && currentStepId !== 'step1') {
                document.getElementById(currentStepId).classList.remove('active');
                document.getElementById(nextStepId).classList.add('active');
            }
        }

        function previousStep(currentStepId, previousStepId) {
            document.getElementById(currentStepId).classList.remove('active');
            document.getElementById(previousStepId).classList.add('active');
        }

        // ==================== UTILITY FUNCTIONS ====================

        function enforceMaxLength(input, maxLength) {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value.length > maxLength) {
                input.value = input.value.slice(0, maxLength);
            }
        }

        function countWords() {
            const text = document.getElementById('loginBio').value;
            const words = text.trim().split(/\s+/).filter(word => word.length > 0);
            document.getElementById('wordCount').innerText = `${words.length} word${words.length !== 1 ? 's' : ''}`;
        }

        function showImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.style.display = 'flex';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }

        // ==================== DOM READY EVENT HANDLERS ====================

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize DOM element references
            emailOtpInput = document.getElementById('emailOtpInput');
            mobileOtpInput = document.getElementById('mobileOtpInput');
            verifyEmailBtn = document.getElementById('verifyEmailBtn');
            verifyMobileBtn = document.getElementById('verifyMobileBtn');
            resendEmailOtpBtn = document.getElementById('resendEmailOtpBtn');
            resendMobileOtpBtn = document.getElementById('resendMobileOtpBtn');
            emailTimerSpan = document.getElementById('emailTimerSpan');
            mobileTimerSpan = document.getElementById('mobileTimerSpan');
            emailMessageDiv = document.getElementById('emailMessageDiv');
            mobileMessageDiv = document.getElementById('mobileMessageDiv');

            // Event listeners for OTP verification
            if (verifyMobileBtn) {
                verifyMobileBtn.addEventListener('click', verifyOtps);
            }

            if (resendEmailOtpBtn) {
                resendEmailOtpBtn.addEventListener('click', resendEmailOtp);
            }

            if (resendMobileOtpBtn) {
                resendMobileOtpBtn.addEventListener('click', resendMobileOtp);
            }

            // IFSC code validation
            const ifscCodeField = document.getElementById('ifscCode');
            if (ifscCodeField) {
                ifscCodeField.addEventListener('change', function(e) {
                    const ifsccode = e.target.value;
                    if (/^[A-Z]{4}0[A-Z0-9]{6}$/.test(ifsccode.toUpperCase())) {
                        fetch(`https://ifsc.razorpay.com/${ifsccode.toUpperCase()}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                const bankNameField = document.getElementById('bankName');
                                const bankBranchField = document.getElementById('bankBranch');
                                if (bankNameField) bankNameField.value = data.BANK;
                                if (bankBranchField) bankBranchField.value = data.BRANCH;
                            })
                            .catch(error => {
                                alert(`There was a problem with the fetch operation: ${error}`);
                            });
                    } else {
                        alert('Wrong IFSC code, please try again with the correct code');
                    }
                });
            }

            // Astrologer platform details toggle
            const yesRadio = document.getElementById('astro-yes');
            const noRadio = document.getElementById('astro-no');
            const platformDetailsContainer = document.getElementById('platform-details-container');
            const platformNameInput = document.getElementById('platform-name');
            const monthlyEarningInput = document.getElementById('monthly-earning');

            if (yesRadio && noRadio && platformDetailsContainer) {
                yesRadio.addEventListener('change', function() {
                    if (this.checked) {
                        platformDetailsContainer.style.display = 'block';
                        if (platformNameInput) platformNameInput.disabled = false;
                        if (monthlyEarningInput) monthlyEarningInput.disabled = false;
                    }
                });

                noRadio.addEventListener('change', function() {
                    if (this.checked) {
                        platformDetailsContainer.style.display = 'none';
                        if (platformNameInput) platformNameInput.disabled = true;
                        if (monthlyEarningInput) monthlyEarningInput.disabled = true;
                    }
                });
            }

            // Referral details toggle
            const referYesRadio = document.getElementById('refer-yes');
            const referNoRadio = document.getElementById('refer-no');
            const referDetailsContainer = document.getElementById('refer-details-container');
            const referredPersonNameInput = document.getElementById('referred-person-name');

            if (referYesRadio && referNoRadio && referDetailsContainer) {
                referYesRadio.addEventListener('change', function() {
                    if (this.checked) {
                        referDetailsContainer.style.display = 'block';
                        if (referredPersonNameInput) referredPersonNameInput.disabled = false;
                    }
                });

                referNoRadio.addEventListener('change', function() {
                    if (this.checked) {
                        referDetailsContainer.style.display = 'none';
                        if (referredPersonNameInput) referredPersonNameInput.disabled = true;
                    }
                });
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