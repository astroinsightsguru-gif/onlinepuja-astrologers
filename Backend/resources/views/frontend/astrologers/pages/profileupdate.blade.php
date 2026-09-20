@extends('frontend.astrologers.layout.master')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
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
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">{{ucfirst($professionTitle)}} Profile Update</span>
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
                <form action="{{route('front.updateAstrologer')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Step 1 -->
                    <input type="hidden" name="userId" value="{{$getAstrologer['recordList']['0']['userId']}}">
                    <input type="hidden" name="id" value="{{$getAstrologer['recordList']['0']['id']}}">
                    <div id="step1"
                        class="categorycontent step-1 sychics-join-form position-relative border px-4 pb-4 step active">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">Personal Details</small></h2>
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="name">Name<span class="color-red font-weight-bold">*</span></label>
                                <input type="text" id="name" value="{{$getAstrologer['recordList']['0']['name']}}"
                                    name="name" class="form-control rounded" required>

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email Address<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="email" id="email" value="{{$getAstrologer['recordList']['0']['email']}}"
                                    name="email" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contactNo">Phone No<span class="color-red font-weight-bold">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="countryCode1" value="{{$user->countryCode}}"
                                        name="countryCode" class="form-control rounded-left" style="max-width: 60px;" required>
                                    <input type="number" value="{{$getAstrologer['recordList']['0']['contactNo']}}"
                                        id="contactNo" name="contactNo" class="form-control rounded-right" oninput="enforceMaxLength(this, 10)" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatsappNo">Whatsapp No<span class="color-red font-weight-bold">*</span></label>
                                <div class="input-group">

                                    <input type="text" value="{{$getAstrologer['recordList']['0']['whatsappNo']}}" id="whatsappNo" name="whatsappNo" class="form-control rounded-right"
                                        pattern="\d{10}"
                                        inputmode="numeric" title="Whatsapp number should contain only numbers." oninput="enforceMaxLength(this, 10)" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country">Country<span class="color-red font-weight-bold">*</span></label>
                                <select class="form-control  " required name="country" id="country">
                                    <option>Select Country</option>
                                    @foreach($country as $countryName)
                                    <option value="{{$countryName->nicename}}" {{ ($getAstrologer['recordList']['0']['country'] ==$countryName->nicename) ? 'selected' : '' }}>{{$countryName->nicename}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="profileImage">Profile<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="file" class="form-control" id="profileImage" name="profileImage"
                                    style="height: 44px">

                                @if ($getAstrologer['recordList'][0]['profileImage'])
                                <div id="imagePreviewContainer" class="mt-2">
                                    <img id="imagePreview" src="{{$getAstrologer['recordList'][0]['profileImage']}}"
                                        alt="Current Profile Image" style="width:90px;height:90px">
                                    <input type="hidden" id="oldProfileImage" name="oldProfileImage" value="{{ $getAstrologer['recordList'][0]['profileImage'] ?? '' }}">
                                </div>
                                @else
                                <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                    <img id="imagePreview" src="#" alt="Profile Image Preview"
                                        style="width:90px;height:90px">
                                </div>
                                @endif
                            </div>


                            @foreach ($documents as $document)
                            @php
                            $inputName = Str::snake($document->name);
                            $existingImage = $getAstrologer['recordList'][0][$inputName] ?? null;
                            @endphp

                            <div class="col-md-6 mb-3">
                                <label for="{{ $inputName }}">{{ $document->name }}</label>
                                <input type="file" class="form-control" id="{{ $inputName }}" name="{{ $inputName }}" style="height: 44px" accept="image/*">

                                @if ($existingImage)
                                <div id="previewContainer_{{ $inputName }}" class="mt-2">
                                    <img src="{{ $existingImage }}" alt="{{ $document->name }} Image" style="width:90px;height:90px">
                                    <input type="hidden" name="old_{{ $inputName }}" value="{{ $existingImage }}">
                                </div>
                                @else
                                <div id="previewContainer_{{ $inputName }}" class="mt-2" style="display: none;">
                                    <img id="preview_{{ $inputName }}" src="#" alt="{{ $document->name }} Preview" style="width:90px;height:90px">
                                </div>
                                @endif
                            </div>
                            @endforeach





                        </div>
                        <div class="col-12 text-center mt-3">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2" onclick="nextStep('step1', 'step2')">Next</a>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div id="step2"
                        class="categorycontent step-2 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">Skill Details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender">Gender<span class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="gender" id="gender" disabled>
                                    <option value="Male" {{$getAstrologer['recordList'][0]['gender'] === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $getAstrologer['recordList'][0]['gender'] === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birthDate">Birth Date<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="date"
                                    value="{{ date('Y-m-d', strtotime($getAstrologer['recordList'][0]['birthDate'])) }}"
                                    name="birthDate" id="birthDate" class="form-control rounded border-pink " required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="aadharNo">Aadhar No<span class="color-red font-weight-bold">*</span></label>
                                <div class="input-group">

                                    <input type="text" value="{{$getAstrologer['recordList']['0']['aadharNo']}}" id="aadharNo" name="aadharNo" class="form-control rounded-right"
                                       
                                        inputmode="numeric" title="Aadhar number should contain only numbers."
                                        oninput="enforceMaxLength(this, 12)" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pancardNo">Pan No<span class="color-red font-weight-bold">*</span></label>
                                <div class="input-group">

                                    <input type="text" value="{{$getAstrologer['recordList']['0']['pancardNo']}}" id="pancardNo" name="pancardNo" maxlength="10" class="form-control rounded-right"
                                        oninput="enforceMaxLength(this, 10)"
                                        required>
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="astrologerCategoryId">Category<span
                                        class="color-red font-weight-bold">*</span></label>
                                <select class="form-control select2" name="astrologerCategoryId[]"
                                    id="astrologerCategoryId" multiple>
                                    @foreach($categories as $category)
                                    <option value="{{$category['id']}}" {{ in_array($category['id'], array_column($getAstrologer['recordList'][0]['astrologerCategoryId'], 'id')) ? 'selected' : '' }}>{{$category['name']}}</option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="primarySkill">Primary Skills<span class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="primarySkill[]" id="primarySkill">
                                    @foreach($skills as $skill)
                                    <option value="{{ $skill['id'] }}"
                                        @if(in_array($skill['id'], array_column($getAstrologer['recordList'][0]['primarySkill'], 'id' )))
                                        selected
                                        @endif>
                                        {{ $skill['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="allSkill">All Skills<span class="color-red font-weight-bold">*</span></label>
                                <select class="form-control select2" required name="allSkill[]" id="allSkill" multiple>
                                    @foreach($skills as $skill)
                                    <option value="{{ $skill['id'] }}"
                                        @if(in_array($skill['id'], array_column($getAstrologer['recordList'][0]['allSkill'], 'id' )))
                                        selected
                                        @endif>
                                        {{ $skill['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="languageKnown">Language<span
                                        class="color-red font-weight-bold">*</span></label>
                                <select class="form-control  select2" required name="languageKnown[]" id="languageKnown" multiple>
                                    @foreach($languages as $language)
                                    <option value="{{ $language->id }}" {{ in_array($language->id, array_column($getAstrologer['recordList'][0]['languageKnown'], 'id')) ? 'selected' : '' }}>
                                        {{ $language->languageName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="charge">Add your charge(as per min in INR)<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['charge']}}" id="charge"
                                    name="charge" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="videoCallRate">Add your video charge(as per min in INR)<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['videoCallRate']}}"
                                    id="videoCallRate" name="videoCallRate" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reportRate">Add your report charge(in INR)<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['reportRate']}}"
                                    id="reportRate" name="reportRate" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="charge_usd">Add your charge(as per min in USD)<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['charge_usd']}}" id="charge_usd"
                                    name="charge_usd" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="videoCallRate_usd">Add your video charge(as per min in USD)<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['videoCallRate_usd']}}"
                                    id="videoCallRate_usd" name="videoCallRate_usd" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reportRate_usd">Add your report charge (in USD)<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['reportRate_usd']}}"
                                    id="reportRate_usd" name="reportRate_usd" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="experienceInYears">Experience in years<span
                                        class="color-red font-weight-bold">*</span></label>

                                <input type="number" value="{{ $getAstrologer['recordList'][0]['experienceInYears']}}"
                                    id="experienceInYears" name="experienceInYears" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dailyContribution">How many hours you can contribute daily?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="number" value="{{ $getAstrologer['recordList'][0]['dailyContribution']}}"
                                    id="dailyContribution" name="dailyContribution" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hearAboutAstroguru">Where did you hear about {{$appname}}?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['hearAboutAstroguru']}}"
                                    id="hearAboutAstroguru" name="hearAboutAstroguru" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Are you working on any other platform?<span class="color-red font-weight-bold">*</span></label><br>
                                <input type="radio" id="astro-yes" name="isWorkingOnAnotherPlatform" value="1" {{ isset($getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform']) && $getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform'] == '1' ? 'checked' : '' }}> Yes
                                <input type="radio" id="astro-no" name="isWorkingOnAnotherPlatform" value="0" {{ isset($getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform']) && $getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform'] == '0' ? 'checked' : '' }}> No
                            </div>
                            
                            <!-- Container for the two input fields with a dotted border -->
                            <div id="platform-details-container" style="border: 2px dotted gray; padding: 15px; margin-bottom: 20px; {{ isset($getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform']) && $getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform'] == '1' ? '' : 'display: none;' }}">
                                <div class="row">
                                    <!-- Platform Name Input -->
                                    <div class="col-md-6 mb-3">
                                        <label for="platform-name">Name of Platform</label>
                                        <input type="text" id="platform-name" name="nameofplateform" class="form-control" value="{{ isset($getAstrologer['recordList'][0]['nameofplateform']) ? $getAstrologer['recordList'][0]['nameofplateform'] : '' }}" {{ isset($getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform']) && $getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform'] == '1' ? '' : 'disabled' }}>
                                    </div>
                            
                                    <!-- Monthly Earning Input -->
                                    <div class="col-md-6 mb-3">
                                        <label for="monthly-earning">Monthly Earning</label>
                                        <input type="number" id="monthly-earning" name="monthlyEarning" class="form-control" value="{{ isset($getAstrologer['recordList'][0]['monthlyEarning']) ? $getAstrologer['recordList'][0]['monthlyEarning'] : '' }}" {{ isset($getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform']) && $getAstrologer['recordList'][0]['isWorkingOnAnotherPlatform'] == '1' ? '' : 'disabled' }}>
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
                        <h2 class="py-3 text-center"><small class="font-weight-bold">Other Details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="whyOnBoard">Why do you think we should onboard you?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" id="awhyOnBoard" name="whyOnBoard" class="form-control rounded"
                                    value="{{ $getAstrologer['recordList'][0]['whyOnBoard']}}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="currentCity">Which city do you currently live in?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" id="currentCity" name="currentCity" class="form-control rounded"
                                    value="{{ $getAstrologer['recordList'][0]['currentCity']}}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mainSourceOfBusiness">Main Source of business(Other than astrology)?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="mainSourceOfBusiness" id="mainSourceOfBusiness">
                                    @foreach ($mainSourceBusiness as $source)
                                    <option value="{{ $source->jobName }}" {{ $getAstrologer['recordList'][0]['mainSourceOfBusiness'] == $source->jobName ? 'selected' : '' }}>
                                        {{ $source->jobName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="highestQualification">Select your qualification<span
                                        class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="highestQualification" id="highestQualification" disabled>
                                    @foreach ($highestQualification as $highest)
                                    <option value='{{ $highest->qualificationName }}' {{ $getAstrologer['recordList'][0]['highestQualification'] == $highest->qualificationName ? 'selected' : '' }}>
                                        {{ $highest->qualificationName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="degree">Degree / Diploma<span
                                        class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="degree" id="degree" disabled>
                                    @foreach ($qualifications as $qua)
                                    <option value='{{ $qua->degreeName }}' {{ $getAstrologer['recordList'][0]['degree'] == $highest->degree ? 'selected' : '' }}>
                                        {{ $qua->degreeName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="college">College/School/University name<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['college']}}" id="college"
                                    name="college" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="learnAstrology">From where did you learn Astrology?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['learnAstrology']}}"
                                    id="learnAstrology" name="learnAstrology" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instaProfileLink">Instagram profile link<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['instaProfileLink']}}"
                                    id="instaProfileLink" name="instaProfileLink" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="facebookProfileLink">Facebook profile link<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['facebookProfileLink'] }}"
                                    id="facebookProfileLink" name="facebookProfileLink" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="linkedInProfileLink">LinkedIn profile link<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['linkedInProfileLink'] }}"
                                    id="linkedInProfileLink" name="linkedInProfileLink" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="youtubeChannelLink">Youtube profile link<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['youtubeChannelLink'] }}"
                                    id="youtubeChannelLink" name="youtubeChannelLink" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="websiteProfileLink">Website profile link<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['websiteProfileLink'] }}"
                                    id="websiteProfileLink" name="websiteProfileLink" class="form-control rounded">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Did anybody referred you?<span class="color-red font-weight-bold">*</span></label><br>
                                <input type="radio" id="refer-yes" name="isAnyBodyRefer" value="1" {{ isset($getAstrologer['recordList'][0]['isAnyBodyRefer']) && $getAstrologer['recordList'][0]['isAnyBodyRefer'] == '1' ? 'checked' : '' }}> Yes
                                <input type="radio" id="refer-no" name="isAnyBodyRefer" value="0" {{ isset($getAstrologer['recordList'][0]['isAnyBodyRefer']) && $getAstrologer['recordList'][0]['isAnyBodyRefer'] == '0' ? 'checked' : '' }}> No
                            </div>
                            
                            <!-- Container for the referred person's name input with a dotted border -->
                            <div id="refer-details-container" style="border: 2px dotted #000; padding: 15px; margin-bottom: 20px; {{ isset($getAstrologer['recordList'][0]['isAnyBodyRefer']) && $getAstrologer['recordList'][0]['isAnyBodyRefer'] == '1' ? '' : 'display: none;' }}">
                                <div class="row">
                                    <!-- Referred Person Name Input -->
                                    <div class="col-md-12 mb-3">
                                        <label for="referred-person-name">Name of the person who referred you</label>
                                        <input type="text" id="referred-person-name" name="referedPerson" class="form-control" value="{{ isset($getAstrologer['recordList'][0]['referedPerson']) ? $getAstrologer['recordList'][0]['referedPerson'] : '' }}" {{ isset($getAstrologer['recordList'][0]['isAnyBodyRefer']) && $getAstrologer['recordList'][0]['isAnyBodyRefer'] == '1' ? '' : 'disabled' }}>
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
                        <h2 class="py-3 text-center"><small class="font-weight-bold">Other Details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minimumEarning">Minimum Earning Expection<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['minimumEarning'] }}"
                                    id="minimumEarning" name="minimumEarning" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="maximumEarning">Maximum Earning Expection<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['maximumEarning'] }}"
                                    id="maximumEarning" name="maximumEarning" class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="NoofforeignCountriesTravel">Number of the foreign countries you lived/travel
                                    to?<span class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="NoofforeignCountriesTravel"
                                    id="NoofforeignCountriesTravel">
                                    @foreach ($countryTravel as $travel)
                                    <option value="{{ $travel->NoOfCountriesTravell }}" {{ $getAstrologer['recordList'][0]['NoofforeignCountriesTravel'] == $travel->NoOfCountriesTravell ? 'selected' : '' }}>
                                        {{ $travel->NoOfCountriesTravell }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currentlyworkingfulltimejob">Are you currently working a fulltime job?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="currentlyworkingfulltimejob"
                                    id="currentlyworkingfulltimejob">
                                    @foreach ($jobs as $working)
                                    <option value="{{ $working->workName }}" {{ $getAstrologer['recordList'][0]['currentlyworkingfulltimejob'] == $working->workName ? 'selected' : '' }}>
                                        {{ $working->workName }}
                                    </option>
                                    @endforeach
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="loginBio">Long Bio<span class="color-red font-weight-bold">*</span></label>
                                <textarea id="loginBio" oninput="countWords()" name="loginBio"
                                    class="form-control rounded" required>{{ $getAstrologer['recordList'][0]['loginBio'] }}</textarea>
                                    <small id="wordCount">0 words</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="goodQuality">What are some good qualities of perfect {{$professionTitle}}?<span
                                        class="color-red font-weight-bold">*</span></label>
                                <textarea id="goodQuality" name="goodQuality"
                                    class="form-control rounded">{{ $getAstrologer['recordList'][0]['goodQuality'] }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="biggestChallenge">What was the biggest challenge you faced and how did you
                                    overcome it?<span class="color-red font-weight-bold">*</span></label>
                                <textarea id="biggestChallenge" name="biggestChallenge"
                                    class="form-control rounded">{{ $getAstrologer['recordList'][0]['biggestChallenge'] }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatwillDo">A customer is asking the same question repeatedly: what will you
                                    do?<span class="color-red font-weight-bold">*</span></label>
                                <textarea id="whatwillDo" name="whatwillDo"
                                    class="form-control rounded">{{ $getAstrologer['recordList'][0]['whatwillDo'] }}</textarea>
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
                        class="categorycontent step-5 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">{{ucfirst($professionTitle)}} Sign Up - Bank Details</small>
                        </h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ifscCode">IFSC Code<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['ifscCode'] }}" id="ifscCode" name="ifscCode"
                                    class="form-control rounded" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bankName">Bank Name<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['bankName'] }}" id="bankName" name="bankName"
                                    class="form-control rounded" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bankBranch">Bank Branch<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['bankBranch'] }}" id="bankBranch" name="bankBranch"
                                    class="form-control rounded" required>
                            </div>



                            <div class="col-md-6 mb-3">
                                <label for="accountType">Account Type<span class="color-red font-weight-bold">*</span></label>
                                <select class="form-control" name="accountType" id="accountType">
                                    <option value="Saving" {{ old('accountType', $getAstrologer['recordList'][0]['accountType'] ?? '') == 'Saving' ? 'selected' : '' }}>
                                        Saving
                                    </option>
                                    <option value="Current" {{ old('accountType', $getAstrologer['recordList'][0]['accountType'] ?? '') == 'Current' ? 'selected' : '' }}>
                                        Current
                                    </option>
                                </select>
                            </div>



                            <div class="col-md-6 mb-3">
                                <label for="accountNumber">Bank Account No<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['accountNumber'] }}" id="accountNumber" name="accountNumber"
                                    class="form-control rounded" oninput="enforceMaxLength(this, 20)" required>
                            </div>
                            
                             <div class="col-md-6 mb-3">
                                <label for="accountHolderName">Account holder Name<span class="red-color font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['accountHolderName'] }}" id="accountHolderName" name="accountHolderName"
                                       class="form-control rounded" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="upi">Upi Id<span
                                        class="color-red font-weight-bold">*</span></label>
                                <input type="text" value="{{ $getAstrologer['recordList'][0]['upi'] }}" id="upi" name="upi"
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







                    {{-- Step 5 --}}
                    <div id="step6"
                        class="categorycontent step-6 sychics-join-form position-relative border px-4 pb-4 step">
                        <h2 class="py-3 text-center"><small class="font-weight-bold">Your Availability</small>
                        </h2>
                        <div class="row">
                            @php
                            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            @endphp

                            @foreach ($daysOfWeek as $index => $day)
                            <div class="col-12 mb-3">
                                <label>{{ $day }}</label>
                                <input type="hidden" name="astrologerAvailability[{{ $index }}][day]"
                                    value="{{ $day }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="{{ strtolower($day) }}-from">From Time</label>
                                        <input type="time" id="{{ strtolower($day) }}-from"
                                            name="astrologerAvailability[{{ $index }}][time][0][fromTime]"
                                            class="form-control rounded" placeholder="From Time"
                                            value="{{ isset($getAstrologer['recordList'][0]['astrologerAvailability'][$index]['time'][0]['fromTime']) ? date('H:i', strtotime($getAstrologer['recordList'][0]['astrologerAvailability'][$index]['time'][0]['fromTime'])) : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="{{ strtolower($day) }}-to">To Time</label>
                                        <input type="time" id="{{ strtolower($day) }}-to"
                                            name="astrologerAvailability[{{ $index }}][time][0][toTime]"
                                            class="form-control rounded" placeholder="To Time"
                                            value="{{ isset($getAstrologer['recordList'][0]['astrologerAvailability'][$index]['time'][0]['toTime']) ? date('H:i', strtotime($getAstrologer['recordList'][0]['astrologerAvailability'][$index]['time'][0]['toTime'])) : '' }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="col-12 text-center">
                            <a class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2"
                                onclick="previousStep('step6', 'step5')">Previous</a>
                            <button class="btn btn-chat btn-chat-lg font-weight-bold px-5 py-2 mt-2">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 sychics-join-info pt-lg-0 pt-5">
                <h2><small class="font-weight-bold">BECOME "{{strtoupper($appname)}} VERIFIED" {{ucwords($professionTitle)}}</small></h2>
                <p>
                    {{$appname}}, one of the best online astrology portals gives you a chance to be a part of
                    its community
                    of best and top-notch {{ucfirst($professionTitle)}}s. Become a part of the team of {{ucfirst($professionTitle)}}s and offer your
                    consultations to clients from all across the globe, &amp; create an online, personalized brand
                    presence.
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
    @endsection
    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%' // Ensure Select2 dropdown takes full width of the parent
            });
        });
    </script>
    <script>
        function nextStep(currentStepId, nextStepId) {

            var emailValid = validateEmail();
            var mobileValid = validateMobileNumber();
            var countryCodeValid = validateCountryCode();
            var isValid = true;
            var requiredFields = document.querySelectorAll('#' + currentStepId + ' [required]');

            var linkFields = [
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
                    // Validate word count for textarea (Login Bio)
                    var wordCount = field.value.trim().split(/\s+/).length;
                    if (wordCount < 50) {
                        isValid = false;
                        showError(field, 'Long Bio must have at least 50 words.');
                    } else {
                        clearError(field);
                    }
                } else if (field.type === 'checkbox' && field.id === 'tandc') {
                    // Validate checkbox (Terms and Conditions)
                    if (!field.checked) {
                        isValid = false;
                        showError(field, 'You must agree to the Terms of Use and Privacy Policy.');
                    } else {
                        clearError(field);
                    }
                } else if (!field.value.trim()) {
                    // Validate other required fields
                    isValid = false;
                    showError(field, 'This field is required.');
                } else {
                    clearError(field);
                }
            });

            // Validate bank account number and confirm bank account number (only for step 5)
            if (currentStepId === 'step5') {
                var accountNumberValid = validateBankAccountNumber();
               
               
            }

            if (currentStepId === 'step2') {
                // Validate Aadhar No and Pan No for Step 2
                var aadharField = document.getElementById('aadharNo');
                if (aadharField && aadharField.value.trim().length !== 12) {
                    isValid = false;
                    showError(aadharField, 'Aadhar No must be exactly 12 digits.');
                } else {
                    clearError(aadharField);
                }

                var panField = document.getElementById('pancardNo');
                if (panField && panField.value.trim().length !== 10) {
                    isValid = false;
                    showError(panField, 'Pan No must be exactly 10 characters.');
                } else {
                    clearError(panField);
                }
            }

            // Validate social links
            linkFields.forEach(function(id) {
                var field = document.getElementById(id);
                if (field) {
                    if (!validateLink(field.value)) {
                        isValid = false;
                        showError(field, 'Please enter a valid URL.');
                    } else {
                        clearError(field);
                    }
                }
            });

            // Proceed to the next step if all validations pass
            if (isValid && emailValid && mobileValid && countryCodeValid) {
                document.getElementById(currentStepId).classList.remove('active');
                document.getElementById(nextStepId).classList.add('active');
            } else {
                // Handle case where validation fails
            }
        }

        // Function to validate URLs
        function validateLink(value) {
            var urlPattern = /^(https?:\/\/[^\s]+|www\.[^\s]+|#|)$/;
            return urlPattern.test(value.trim());
        }

        // Function to display error messages
        function showError(field, message) {
            // Remove any existing error message
            clearError(field);

            // Create a new error message element
            var errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.style.color = '#dc3545';
            errorMessage.style.fontSize = '0.875rem';
            errorMessage.style.marginTop = '0.25rem';
            errorMessage.innerText = message;

            var container = field.closest('.country-dropdown-container'); // Use the closest container

            // Insert the error message after the field or its container
            if (field.type === 'checkbox') {
                // For checkboxes, insert the error message after the label
                var label = field.closest('label') || field.nextElementSibling;
                if (label) {
                    label.parentNode.insertBefore(errorMessage, label.nextSibling);
                } else {
                    field.parentNode.insertBefore(errorMessage, field.nextSibling);
                }
            } else if ($(field).hasClass('select2-hidden-accessible')) {
                // Insert the error message below the Select2 container
                var select2Container = $(field).data('select2').$container;
                $(select2Container).after(errorMessage);
            } else if (container) {
                container.parentNode.insertBefore(errorMessage, container.nextSibling);

            } else {
                // For other fields, insert the error message after the field itself
                field.parentNode.insertBefore(errorMessage, field.nextSibling);
            }



            // Add invalid class to the field
            field.classList.add('is-invalid');
        }

        // Function to clear error messages
        function clearError(field) {
            // Remove the error message if it exists
            var errorMessage = field.parentNode.querySelector('.error-message');
            if (errorMessage) {
                errorMessage.remove();
            }

            // Remove invalid class from the field
            field.classList.remove('is-invalid');
        }

        // Function to validate country code
        function validateCountryCode() {
            var countryCodeField = document.getElementById('countryCode1');
            var countryCodeValue = countryCodeField.value.trim();
            var countryCodePattern = /^\+?[0-9]{1,4}$/;

            if (!countryCodePattern.test(countryCodeValue)) {
                showError(countryCodeField, 'Please enter a valid country code (e.g., +91 or 9).');
                return false;
            } else {
                clearError(countryCodeField);
                return true;
            }
        }

        // Function to validate email
        function validateEmail() {
            var emailField = document.getElementById('email');
            var emailValue = emailField.value;
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (!emailPattern.test(emailValue)) {
                showError(emailField, 'Please enter a valid email address.');
                return false;
            } else {
                clearError(emailField);
                return true;
            }
        }

        // Function to validate mobile number
        function validateMobileNumber() {
            var mobileField = document.getElementById('contactNo');
            var mobileValue = mobileField.value;
            var mobilePattern = /^[0-9]{10}$/;

            if (!mobilePattern.test(mobileValue)) {
                showError(mobileField, 'Please enter a valid 10-digit mobile number.');
                return false;
            } else {
                clearError(mobileField);
                return true;
            }
        }

        function enforceMaxLength(input, maxLength) {
            input.value = input.value.replace(/[^0-9]/g, '');

            if (input.value.length > maxLength) {
                input.value = input.value.slice(0, maxLength);
            }
        }

        function validateBankAccountNumber() {
            var accountNumberField = document.getElementById('accountNumber');
            var accountNumberValue = accountNumberField.value.trim();

            if (!accountNumberValue) {
                showError(accountNumberField, 'Bank Account No is required.');
                return false;

            } else {
                clearError(accountNumberField);
                return true;
            }
        }



        function countWords() {
            const text = document.getElementById('loginBio').value;
            const words = text.trim().split(/\s+/).filter(word => word.length > 0);
            document.getElementById('wordCount').innerText = `${words.length} word${words.length !== 1 ? 's' : ''}`;
        }

        // Function to go to the previous step without validation
        function previousStep(currentStepId, previousStepId) {
            document.getElementById(currentStepId).classList.remove('active');
            document.getElementById(previousStepId).classList.add('active');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileImageInput = document.getElementById('profileImage');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');

            profileImageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '#';
                    imagePreviewContainer.style.display = 'none';
                }
            });
        });
        
        
         document.getElementById('ifscCode').addEventListener('change', function(e) {
            var ifsccode = e.target.value;
            if (/^[A-Z]{4}0[A-Z0-9]{6}$/.test(ifsccode.toUpperCase())) {
                fetch(`https://ifsc.razorpay.com/${ifsccode.toUpperCase()}`)
                    .then(response => {
                        if (!response.ok) {
                            alert('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('bankName').value = data.BANK;
                        document.getElementById('bankBranch').value = data.BRANCH;
                    })
                    .catch(error => {
                        alert(`There was a problem with the fetch operation: ${error}`);
                    });
            } else {
                alert('Wrong IFSC code, please try again with the correct code');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
    const yesRadio = document.getElementById('astro-yes');
    const noRadio = document.getElementById('astro-no');
    const platformDetailsContainer = document.getElementById('platform-details-container');
    const platformNameInput = document.getElementById('platform-name');
    const monthlyEarningInput = document.getElementById('monthly-earning');

    // Check the initial state of the radio button
    if (yesRadio.checked) {
        platformDetailsContainer.style.display = 'block';
        platformNameInput.disabled = false;
        monthlyEarningInput.disabled = false;
    }

    yesRadio.addEventListener('change', function () {
        if (this.checked) {
            platformDetailsContainer.style.display = 'block';
            platformNameInput.disabled = false;
            monthlyEarningInput.disabled = false;
        }
    });

    noRadio.addEventListener('change', function () {
        if (this.checked) {
            platformDetailsContainer.style.display = 'none';
            platformNameInput.disabled = true;
            monthlyEarningInput.disabled = true;
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const referYesRadio = document.getElementById('refer-yes');
    const referNoRadio = document.getElementById('refer-no');
    const referDetailsContainer = document.getElementById('refer-details-container');
    const referredPersonNameInput = document.getElementById('referred-person-name');

    // Check the initial state of the radio button
    if (referYesRadio.checked) {
        referDetailsContainer.style.display = 'block';
        referredPersonNameInput.disabled = false;
    }

    referYesRadio.addEventListener('change', function () {
        if (this.checked) {
            referDetailsContainer.style.display = 'block';
            referredPersonNameInput.disabled = false;
        }
    });

    referNoRadio.addEventListener('change', function () {
        if (this.checked) {
            referDetailsContainer.style.display = 'none';
            referredPersonNameInput.disabled = true;
        }
    });
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