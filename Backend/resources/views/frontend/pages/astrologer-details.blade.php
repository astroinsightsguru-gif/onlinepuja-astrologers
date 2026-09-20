@extends('frontend.layout.master')

@section('content')
@php
$countries = DB::table('countries')
->orderByRaw("CASE WHEN phonecode = 91 THEN 0 ELSE 1 END")
->get();
@endphp

<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">

                <span style="text-transform: capitalize; ">


                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span
                            class="breadcrumbtext">{{ $getAstrologer['recordList'][0]['name'] }}</span>
                    </span>

                </span>

            </div>
        </div>
    </div>
</div>


<!--Report and block modal-->
<div id="reportBlockModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm h-100 d-flex align-items-center">

        <!-- Modal content-->
        <div class="modal-content p-3">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">
                    Reason
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="reportBlockForm">
                    @if (authcheck())
                    <input type="hidden" name="userId" id="userId" value="{{ authcheck()['id'] }}">
                    @endif
                    <input type="hidden" id="astrologerId" name="astrologerId" value="{{ $getAstrologer['recordList'][0]['id'] }}">
                    <div class="text-center">
                        <div class="form-group mt-1">

                            <textarea class="form-control" id="review" name="reason" rows="3" placeholder="Enter your reason">{{ isset($getUserHistoryReview['recordList'][0]['review']) ? $getUserHistoryReview['recordList'][0]['review'] : '' }}</textarea>
                        </div>
                        <button class="btn btn-chat" id="reportBlockBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--End-->




{{-- Intake Form  chat --}}
<div class="modal fade rounded mt-2 mt-md-5 " id="intake" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title font-weight-bold">
                    Birth Details
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-0 pb-0">
                <div class="bg-white body">
                    <div class="row ">

                        <div class="col-lg-12 col-12 ">
                            <div class="mb-3 ">

                                <form class="px-3 font-14" method="post" id="intakeForm">

                                    @if (authcheck())
                                    <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                    <input type="hidden" name="countryCode"
                                        value="{{ authcheck()['countryCode'] }}">
                                    @endif
                                    <input type="hidden" name="astrologerId"
                                        value="{{ $getAstrologer['recordList'][0]['id'] }}">
                                    <div class="row">
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="Name">Name<span class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="Name" name="name" placeholder="Enter Name"
                                                    type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['name'] ?? '' }}" pattern="^[a-zA-Z\s]{2,50}$" title="Name should contain only letters and be between 2 and 50 characters long." required
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <label for="profileImage">Contact No*</label>
                                            <div class="d-flex inputform country-dropdown-container" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">

                                                <!-- Country Code Dropdown -->
                                                <select class="form-control select2" id="countryCode" name="countryCode" style="border: none; border-right: 1px solid #ddd; border-radius: 0; width: 20%;">
                                                    @foreach ($countries as $country)
                                                    <option data-country="in" value="{{ $getIntakeForm['recordList'][0]['countryCode'] ?? $country->phonecode}}" data-ucname="India">
                                                        +{{ $country->phonecode }} {{ $country->iso }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <!-- Mobile Number Input -->
                                                <input class="form-control mobilenumber text-box single-line" id="contact" maxlength="12" name="phoneNumber" type="number" value="{{ $getIntakeForm['recordList'][0]['phoneNumber'] ?? ''}}" style="border: none; border-radius: 0; width: 130%;" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group">
                                                <label for="Gender">Gender <span class="color-red">*</span></label>
                                                <select class="form-control" id="Gender" name="gender" required>
                                                    <option value="Male"
                                                        {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Male' ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="Female"
                                                        {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Female' ? 'selected' : '' }}>
                                                        Female</option>
                                                    <option value="Other"
                                                        {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" id="latitude" name="latitude" value="{{ $getIntakeForm['recordList'][0]['latitude'] ?? '' }}">
                                        <input type="hidden" id="longitude" name="longitude" value="{{ $getIntakeForm['recordList'][0]['longitude'] ?? '' }}">
                                        <input type="hidden" id="timezone" name="timezone" value="{{ $getIntakeForm['recordList'][0]['timezone'] ?? '5.5' }}">
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="BirthDate">Birthdate<span class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="BirthDate" name="birthDate" placeholder="Enter Birthdate"
                                                    type="date" required
                                                    value="{{ isset($getIntakeForm['recordList'][0]['birthDate']) ? date('Y-m-d', strtotime($getIntakeForm['recordList'][0]['birthDate'])) : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="BirthTime">Birthtime<span class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="BirthTime" name="birthTime" placeholder="Enter Birthtime"
                                                    type="time"
                                                    value="{{ $getIntakeForm['recordList'][0]['birthTime'] ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="BirthPlace">Birthplace<span
                                                        class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="BirthPlace" name="birthPlace" placeholder="Enter Birthplace"
                                                    type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['birthPlace'] ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="MaritalStatus">Marital Status<span
                                                        class="color-red">*</span></label>
                                                <select class="form-control" id="MaritalStatus" name="maritalStatus" required>
                                                    <option value="Single"
                                                        {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Single' ? 'selected' : '' }}>
                                                        Single</option>
                                                    <option value="Married"
                                                        {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Married' ? 'selected' : '' }}>
                                                        Married</option>
                                                    <option value="Divorced"
                                                        {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Divorced' ? 'selected' : '' }}>
                                                        Divorced</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="Occupation">Occupation</label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="Occupation" name="occupation" placeholder="Enter Occupation"
                                                    type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['occupation'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="TopicOfConcern">Topic Of Concern *</label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="TopicOfConcern" name="topicOfConcern"
                                                    placeholder="Enter Topic Of Concern" type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['topicOfConcern'] ?? '' }}" required>
                                            </div>
                                        </div>

                                        @if (authcheck())
                                        @if ($getAstrologer['recordList'][0]['isFreeAvailable'] != true)
                                        <input type="hidden" name="isFreeSession"
                                            value="0">
                                        <div class="col-12 py-3">
                                            <div class="form-group mb-0">
                                                <label>Select Time You want to chat<span
                                                        class="color-red">*</span></label><br>
                                                <div class="btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-info btn-sm mt-1">
                                                        <input type="radio" name="chat_duration"
                                                            id="chat_duration300" value="300" required> 5 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-1">
                                                        <input type="radio" name="chat_duration"
                                                            id="chat_duration600" value="600" required> 10 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-1">
                                                        <input type="radio" name="chat_duration"
                                                            id="chat_duration900" value="900" required> 15 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-1">
                                                        <input type="radio" name="chat_duration"
                                                            id="chat_duration1200" value="1200" required> 20 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-1">
                                                        <input type="radio" name="chat_duration"
                                                            id="chat_duration1500" value="1500" required> 25 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-1">
                                                        <input type="radio" name="chat_duration"
                                                            id="chat_duration1800" value="1800" required> 30 mins
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <input type="hidden" name="chat_duration"
                                            value="{{ $getIntakeForm['default_time'] }}">
                                        <input type="hidden" name="isFreeSession"
                                            value="1">
                                        @endif
                                        @endif



                                    </div>

                                    <div class="col-12 col-md-12 py-3">
                                        <div class="row">

                                            <div class="col-12 pt-md-3 text-center mt-2">
                                                <button class="font-weight-bold ml-0 w-100 btn btn-chat"
                                                    id="loaderintakeBtn" type="button" style="display:none;"
                                                    disabled>
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span> Loading...
                                                </button>
                                                <button type="submit"
                                                    class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                                    id="intakeBtn">Start Chat</button>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- End  chat Intake form --}}

{{-- Call Intake --}}

<div class="modal fade rounded mt-2 mt-md-5 " id="callintake" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title font-weight-bold">
                    Birth Details
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-0 pb-0">
                <div class="bg-white body">
                    <div class="row ">

                        <div class="col-lg-12 col-12 ">
                            <div class="mb-3 ">

                                <form class="px-3 font-14" method="post" id="callintakeForm">

                                    @if (authcheck())
                                    <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                    <input type="hidden" name="countryCode"
                                        value="{{ authcheck()['countryCode'] }}">
                                    @endif
                                    <input type="hidden" name="astrologerId"
                                        value="{{ $getAstrologer['recordList'][0]['id'] }}">

                                    <input type="hidden" name="call_type" id="call_type" value="">
                                    <input type="hidden" name="astrocharge" id="astrocharge" value="">
                                    <div class="row">
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="Name">Name<span class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="Name" name="name" placeholder="Enter Name"
                                                    type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['name'] ?? '' }}" pattern="^[a-zA-Z\s]{2,50}$" title="Name should contain only letters and be between 2 and 50 characters long." required
                                                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <label for="profileImage">Contact No*</label>
                                            <div class="d-flex inputform country-dropdown-container" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">

                                                <!-- Country Code Dropdown -->
                                                <select class="form-control select2" id="countryCode1" name="countryCode" style="border: none; border-right: 1px solid #ddd; border-radius: 0; width: 20%;">
                                                    @foreach ($countries as $country)
                                                    <option data-country="in" value="{{ $getIntakeForm['recordList'][0]['countryCode'] ?? $country->phonecode}}" data-ucname="India">
                                                        +{{ $country->phonecode }} {{ $country->iso }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <!-- Mobile Number Input -->
                                                <input class="form-control mobilenumber text-box single-line" id="contact" maxlength="12" name="phoneNumber" type="number" value="{{ $getIntakeForm['recordList'][0]['phoneNumber'] ?? ''}}" style="border: none; border-radius: 0; width: 130%;" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group">
                                                <label for="Gender">Gender <span class="color-red">*</span></label>
                                                <select class="form-control" id="Gender" name="gender" required>
                                                    <option value="Male"
                                                        {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Male' ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="Female"
                                                        {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Female' ? 'selected' : '' }}>
                                                        Female</option>
                                                    <option value="Other"
                                                        {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="BirthDate">Birthdate<span
                                                        class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="BirthDate" name="birthDate" placeholder="Enter Birthdate"
                                                    type="date"
                                                    value="{{ isset($getIntakeForm['recordList'][0]['birthDate']) ? date('Y-m-d', strtotime($getIntakeForm['recordList'][0]['birthDate'])) : '' }}" required>
                                            </div>
                                        </div>

                                        <input type="hidden" id="latitude1" name="latitude" value="{{ $getIntakeForm['recordList'][0]['latitude'] ?? '' }}">
                                        <input type="hidden" id="longitude1" name="longitude" value="{{ $getIntakeForm['recordList'][0]['longitude'] ?? '' }}">
                                        <input type="hidden" id="timezone1" name="timezone" value="{{ $getIntakeForm['recordList'][0]['timezone'] ?? '5.5' }}">

                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="BirthTime">Birthtime<span
                                                        class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="BirthTime" name="birthTime" placeholder="Enter Birthtime"
                                                    type="time"
                                                    value="{{ $getIntakeForm['recordList'][0]['birthTime'] ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="BirthPlace">Birthplace<span
                                                        class="color-red">*</span></label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="BirthPlace1" name="birthPlace" placeholder="Enter Birthplace"
                                                    type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['birthPlace'] ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="MaritalStatus">Marital Status<span
                                                        class="color-red">*</span></label>
                                                <select class="form-control" id="MaritalStatus" name="maritalStatus" required>
                                                    <option value="Single"
                                                        {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Single' ? 'selected' : '' }}>
                                                        Single</option>
                                                    <option value="Married"
                                                        {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Married' ? 'selected' : '' }}>
                                                        Married</option>
                                                    <option value="Divorced"
                                                        {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Divorced' ? 'selected' : '' }}>
                                                        Divorced</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="Occupation">Occupation</label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="Occupation" name="occupation" placeholder="Enter Occupation"
                                                    type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['occupation'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 py-2">
                                            <div class="form-group mb-0">
                                                <label for="TopicOfConcern">Topic Of Concern</label>
                                                <input class="form-control border-pink matchInTxt shadow-none"
                                                    id="TopicOfConcern" name="topicOfConcern"
                                                    placeholder="Enter Topic Of Concern" type="text"
                                                    value="{{ $getIntakeForm['recordList'][0]['topicOfConcern'] ?? '' }}">
                                            </div>
                                        </div>

                                        @if (authcheck())
                                        @if ($getAstrologer['recordList'][0]['isFreeAvailable'] != true)
                                        <input type="hidden" name="isFreeSession"
                                            value="0">
                                        <div class="col-12 py-3">
                                            <div class="form-group mb-0">
                                                <label>Select Time You want to call<span
                                                        class="color-red">*</span></label><br>
                                                <div class="btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-info btn-sm mt-2">
                                                        <input type="radio" name="call_duration"
                                                            id="call_duration300" value="300" required> 5 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-2">
                                                        <input type="radio" name="call_duration"
                                                            id="call_duration600" value="600" required> 10 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-2">
                                                        <input type="radio" name="call_duration"
                                                            id="call_duration900" value="900" required> 15 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-2">
                                                        <input type="radio" name="call_duration"
                                                            id="call_duration1200" value="1200" required> 20 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-2">
                                                        <input type="radio" name="call_duration"
                                                            id="call_duration1500" value="1500" required> 25 mins
                                                    </label>
                                                    <label class="btn btn-info btn-sm mt-2">
                                                        <input type="radio" name="call_duration"
                                                            id="call_duration1800" value="1800" required> 30 mins
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <input type="hidden" name="call_duration"
                                            value="{{ $getIntakeForm['default_time'] }}">
                                        <input type="hidden" name="isFreeSession"
                                            value="1">
                                        @endif
                                        @endif



                                    </div>

                                    <div class="col-12 col-md-12 py-3">
                                        <div class="row">

                                            <div class="col-12 pt-md-3 text-center mt-2">
                                                <button class="font-weight-bold ml-0 w-100 btn btn-chat"
                                                    id="callloaderintakeBtn" type="button" style="display:none;"
                                                    disabled>
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span> Loading...
                                                </button>
                                                <button type="submit"
                                                    class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                                    id="callintakeBtn">Start Call</button>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- End  Call --}}




<div class="bg-pink py-3 py-md-4 expert-profile-page-new">
    <div class="container">

        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-block d-md-flex">
                    <!--Expert profile image and badge -->
                    <div class="profile-image position-relative pb-5 border">
                        @if ($getAstrologer['recordList'][0]['profileImage'])
                        <img class="psychicpic img-fluid" src="{{ Str::startsWith($getAstrologer['recordList'][0]['profileImage'], ['http://','https://']) ? $getAstrologer['recordList'][0]['profileImage'] : '/' . $getAstrologer['recordList'][0]['profileImage'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $getAstrologer['recordList'][0]['profileImage'] }}')" width="143" height="143" loading="lazy" />

                        <!-- <img src="/{{ $getAstrologer['recordList'][0]['profileImage'] }}"
                                    class="psychicpic img-fluid" alt="{{ $getAstrologer['recordList'][0]['name'] }}"
                                    width="143" height="143" /> -->
                        @else
                        <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                            class="psychicpic img-fluid" alt="{{ $getAstrologer['recordList'][0]['name'] }}"
                            width="143" height="143" />
                        @endif
                        <div id="psychic-21599-status" class="status-badge specific-Clr-Online hidden"></div>
                        <div class="position-absolute profile-badge">
                            <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/onlinepuja/images/deals/seal.png') }}"
                                width="52" height="52" />
                        </div>

                    </div>

                    <!-- Expert Information -->
                    <div class="ml-md-4 mt-2 mt-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                            <p class="font-weight-bold text-capitalize mb-0 font-24">
                                {{ $getAstrologer['recordList'][0]['name'] }}
                            </p>
                            <div>

                                @if(authcheck())
                                @if(!$getfollower)
                                <form id="followastro" class="ml-5">
                                    <input type="hidden" name="astrologerId"
                                        value="{{ $getAstrologer['recordList'][0]['id'] }}">
                                    <a class="btn btn-lg bg-white rounded text-dark font-weight-bold buttonshowmoreprofile" role="button" id="btnFollow">
                                        <span class="show-more-btn-txt">Follow</span>
                                    </a>
                                </form>
                                @else
                                <form id="unfollowfollowastro" class="ml-5">
                                    <input type="hidden" name="astrologerId"
                                        value="{{ $getAstrologer['recordList'][0]['id'] }}">
                                    <a class="btn btn-lg bg-white rounded text-dark font-weight-bold buttonshowmoreprofile" role="button" id="btnUnFollow">
                                        <span class="show-more-btn-txt">Unfollow</span>
                                    </a>
                                </form>
                                @endif
                                @endif

                                @if($getAstrologer['recordList'][0]['isBlock']==true)

                                <form id="unblockastrologer" class="ml-5 mt-2">
                                    <input type="hidden" name="astrologerId" value="{{ $getAstrologer['recordList'][0]['id'] }}">
                                    <a class="btn btn-lg bg-white rounded text-dark font-weight-bold buttonshowmoreprofile" role="button" id="btnunBlock" style="height:50%">
                                        <span class="show-more-btn-txt">Unblock</span>
                                    </a>
                                </form>
                                @endif
                            </div>

                        </div>

                        <!--report and block-->

                        @if(authcheck())

                        @if($getAstrologer['recordList'][0]['isBlock']==false)

                        <span class="dropdown d-flex justify-content-end">
                            <a href="#" class="colorblack" id="optionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="optionsDropdown" style="">
                                <a class="dropdown-item" id="reportBlock" href="#" data-toggle="modal" data-target="#reportBlockModal">Report and Block</a>

                            </div>
                        </span>

                        @endif

                        @endif

                        <!--end-->


                        <div class="text-center text-md-left order-2 order-md-5">
                            <p class="mb-0 font-16 color-darkgray">
                                @foreach ($getAstrologer['recordList'][0]['primarySkill'] as $primarySkill)
                                <span id="exp_CatName"
                                    title="{{ $primarySkill['name'] }}">{{ $primarySkill['name'] }}</span>
                                @endforeach
                            </p>
                            <p class="font-16 m-0 profileCatName color-darkgray pb-1">
                                @foreach ($getAstrologer['recordList'][0]['languageKnown'] as $language)
                                <span class="colorblack lang">{{ $language['languageName'] }},</span>
                                @endforeach

                            </p>




                        </div>
                        <div class="order-3 order-md-3"><span class="border-top d-block m-2"></span></div>
                        <div
                            class="d-flex align-items-center justify-content-center justify-content-md-start order-4 order-md-2 flex-wrap">
                            <p class="text-left font-16  p-0 m-0 font-weight-normal color-darkgray">
                                <span> Reviews : </span> <span
                                    class="reviews-count"><text>{{ $getAstrologer['recordList'][0]['ratingcount'] }}</text></span>
                            </p>
                            <span class="font-16 px-3">|</span>
                            <p class="font-16 text-left p-0 m-0 text-nowrap">Rating:
                                @php
                                $totalReviews = count($getAstrologer['recordList'][0]['review']);
                                $totalRating = 0; // Total sum of ratings
                                foreach ($getAstrologer['recordList'][0]['review'] as $review) {
                                $totalRating += $review['rating'];
                                }
                                if ($totalReviews > 0) {
                                $averageRating = $totalRating / $totalReviews;
                                } else {
                                $averageRating = 0;
                                }
                                @endphp
                                <span>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <=$averageRating)
                                        <i class="fas fa-star filled-star"></i>
                                        @else
                                        <i class="far fa-star empty-star"></i>
                                        @endif
                                        @endfor
                                </span>
                            </p>


                            <span class="font-16 px-3">|</span>
                            <p class="font-16 m-0">Exp :<span
                                    class="colorblack ml-1">{{ $getAstrologer['recordList'][0]['experienceInYears'] }}
                                    Years</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 mt-3 mt-md-0">
                <!--Expert Call Chat Buttons -->
                <ul class="list-inline psychic-badge text-center text-md-right mb-0">
                    <li class="list-inline-item mt-sm-2 mt-md-0">
                        <div class="profile-buttons d-block align-items-center justify-content-center ">
                            @if($getAstrologer['recordList'][0]['chatStatus']=='Busy' || $getAstrologer['recordList'][0]['chatStatus']=='Offline' || empty($getAstrologer['recordList'][0]['chatStatus']))
                            <div class="my-2 position-relative">
                                <a class="btn-block  colorblack  btn-chat-profile @if($getAstrologer['recordList'][0]['chatStatus']=='Busy') expert-busy @else expert-offline @endif ">
                                    <span class="d-flex w-100 justify-content-between">
                                        <span class="position-relative">
                                            <span class="d-block mb-3 font-weight-bold"> Chat </span>
                                            <span class="d-block font-12 position-absolute bsy-txtded text-left font-16"> {{$getAstrologer['recordList'][0]['chatStatus'] ?? 'Offline'}} </span>
                                        </span>

                                        <span class="separator d-block">
                                            <span class="d-block text-center p-0">
                                                <span class="d-block font-12"></span>
                                                <span class="d-block font-16">
                                                    @if($walletType == 'coin')
                                                    <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                    {{ $currency['value'] }}
                                                    @endif
                                                    {{ $getAstrologer['recordList'][0]['charge'] }} /Min</span>
                                            </span>
                                        </span>

                                    </span>
                                </a>
                            </div>
                            {{-- @elseif($getAstrologer['recordList'][0]['chat_sections']==0 || $chatsection['value']==0)

                                <div class="my-2 position-relative">
                                    <a class="btn-block  colorblack  btn-chat-profile expert-busy btn-opacity disabled" style="border: 2px solid #53535a !important;">
                                        <span class="d-flex w-100 justify-content-between">
                                            <span class="font-weight-bold"> Chat </span>
                                            @if ($getAstrologer['recordList'][0]['isFreeAvailable'] == true)
                                                <span class="separator d-block">
                                                    <span class="d-block text-center p-0 font-12"><del>
                                                            @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                            @else
                            {{ $currency['value'] }}
                            @endif
                            {{ $getAstrologer['recordList'][0]['charge'] }}
                            /Min</del></span>
                            <span class="d-block text-center p-0">Free</span>
                            </span>
                            @else
                            <span class="d-block font-16">
                                @if($walletType == 'coin')
                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                @else
                                {{ $currency['value'] }}
                                @endif
                                {{ $getAstrologer['recordList'][0]['charge'] }} /Min</span>
                            @endif
                            </span>
                            </a>
                        </div> --}}
                        @else
                        <div class="my-2 position-relative">
                            <a class="btn-block btn-chat-profile colorblack" data-toggle="modal" role="button"
                                id="chat-btn"
                                @if (!authcheck()) data-target="#loginSignUp" @else data-target="#intake" @endif>
                                <span class="d-flex w-100 justify-content-between">
                                    <span class="font-weight-bold"> Chat </span>
                                    @if ($getAstrologer['recordList'][0]['isFreeAvailable'] == true)
                                    <span class="separator d-block">
                                        <span class="d-block text-center p-0 font-12"><del>
                                                @if($walletType == 'coin')
                                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                @else
                                                {{ $currency['value'] }}
                                                @endif
                                                {{ $getAstrologer['recordList'][0]['charge'] }}
                                                /Min</del></span>
                                        <span class="d-block text-center p-0">Free</span>
                                    </span>
                                    @else
                                    <span class="d-block font-16">
                                        @if($walletType == 'coin')
                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                        @else
                                        {{ $currency['value'] }}
                                        @endif
                                        {{ $getAstrologer['recordList'][0]['charge'] }} /Min</span>
                                    @endif
                                </span>
                            </a>
                        </div>
                        @endif

                        @if($getAstrologer['recordList'][0]['callStatus']=='Busy' || $getAstrologer['recordList'][0]['callStatus']=='Offline' || empty($getAstrologer['recordList'][0]['callStatus']))

                        <div class="my-2 position-relative">
                            <a class="btn-block  colorblack  btn-chat-profile @if($getAstrologer['recordList'][0]['callStatus']=='Busy') expert-busy @else expert-offline @endif ">
                                <span class="d-flex w-100 justify-content-between">
                                    <span class="position-relative">
                                        <span class="d-block mb-3 font-weight-bold">Audio Call </span>
                                        <span class="d-block font-12 position-absolute bsy-txtded text-left font-16"> {{$getAstrologer['recordList'][0]['callStatus'] ?? 'Offline'}} </span>
                                    </span>

                                    <span class="separator d-block">
                                        <span class="d-block text-center p-0">
                                            <span class="d-block font-12"></span>
                                            <span class="d-block font-16">
                                                @if($walletType == 'coin')
                                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                @else
                                                {{ $currency['value'] }}
                                                @endif
                                                {{ $getAstrologer['recordList'][0]['charge'] }} /Min</span>
                                        </span>
                                    </span>

                                </span>
                            </a>
                        </div>
                        {{-- @elseif($getAstrologer['recordList'][0]['call_sections']==0 || $callsection['value']==0)

                                <div class="my-2 position-relative">
                                    <a class="btn-block  colorblack  btn-chat-profile expert-busy btn-opacity disabled" style="border: 2px solid #53535a !important;">
                                        <span class="d-flex w-100 justify-content-between">
                                            <span class="font-weight-bold">Audio Call </span>
                                            @if ($getAstrologer['recordList'][0]['isFreeAvailable'] == true)
                                                <span class="separator d-block">
                                                    <span class="d-block text-center p-0 font-12"><del>
                                                      @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                        @else
                        {{ $currency['value'] }}
                        @endif
                        {{ $getAstrologer['recordList'][0]['charge'] }}
                        /Min</del></span>
                        <span class="d-block text-center p-0">Free</span>
                        </span>
                        @else
                        <span class="d-block font-16">
                            @if($walletType == 'coin')
                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                            @else
                            {{ $currency['value'] }}
                            @endif
                            {{ $getAstrologer['recordList'][0]['charge'] }} /Min</span>
                        @endif
                        </span>
                        </a>
            </div> --}}

            @else
            <div class="my-2 position-relative">
                <a class="other-country btn-block btn btn-chat-profile colorblack" role="button"
                    data-toggle="modal"
                    @if (!authcheck()) data-target="#loginSignUp" @else data-target="#callintake" @endif
                    id="audio-call-btn">
                    <span class="d-flex w-100 justify-content-between">
                        <span class="font-weight-bold">Audio Call </span>
                        @if ($getAstrologer['recordList'][0]['isFreeAvailable'] == true)
                        <span class="separator d-block">
                            <span class="d-block text-center p-0 font-12"><del>
                                    @if($walletType == 'coin')
                                    <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                    @else
                                    {{ $currency['value'] }}
                                    @endif
                                    {{ $getAstrologer['recordList'][0]['charge'] }}
                                    /Min</del></span>
                            <span class="d-block text-center p-0">Free</span>
                        </span>
                        @else
                        <span class="d-block font-16">
                            @if($walletType == 'coin')
                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                            @else
                            {{ $currency['value'] }}
                            @endif
                            {{ $getAstrologer['recordList'][0]['charge'] }} /Min</span>
                        @endif
                    </span>
                </a>
            </div>

            @endif

        </div>
        </li>
        </ul>
    </div>
</div>
</div>
</div>
<div class="container profile-page">
    <div class="row my-3 profile-desc">
        <div class="col-sm-12" id="order2">
            <div class="bg-white div_Shadow p-4">
                {{--
                    @if($getAstrologer['recordList'][0]['courseBadges'])
                    <div class="psychic-specialization">
                        <h3 class="font-18 weight500 colorblack m-0 font-weight-bold">Special Badges</h3>
                        <p class="font15 colorblack m-0 p-0 pt-3" id="profile-specialization">
                            <ul>
                                @foreach ($getAstrologer['recordList'][0]['courseBadges'] as $badgeData)
                                    @php
                                        // Decode the JSON string into a PHP array
                                        $badges = json_decode($badgeData['course_badge']);
                                    @endphp

                                    @foreach ($badges as $badge)
                                        <li>{{ $badge }}</li>
                @endforeach
                @endforeach
                </ul>
                </p>
            </div>
            @endif
            --}}
            <div class="psychic-specialization">
                <h3 class="font-18 weight500 colorblack m-0 font-weight-bold">Specialization</h3>
                <p class="font15 colorblack m-0 p-0 pt-3" id="profile-specialization">
                <ul>
                    @foreach ($getAstrologer['recordList'][0]['astrologerCategoryId'] as $category)
                    <li>{{ $category['name'] }}</li>
                    @endforeach
                </ul>
                </p>
            </div>
            <h3 class="font-18 weight500 colorblack m-0 pt-4 font-weight-bold">About My Services</h3>
            <p class="font15 colorblack m-0 p-0 pt-2">
                {{ $getAstrologer['recordList'][0]['loginBio'] }}
            </p>
            <h3 class="font-18 weight500 colorblack m-0  pt-4 font-weight-bold">Experience &amp; Qualification</h3>
            <p class="font15 colorblack m-0 p-0 pt-2">
                I am a practicing Astrology in
                @if (!empty($getAstrologer['recordList'][0]['primarySkill']))
                @php
                $skills = collect($getAstrologer['recordList'][0]['primarySkill'])->pluck('name')->implode(', ');
                @endphp
                {{ $skills }}
                @endif
                with an experience of more than {{ $getAstrologer['recordList'][0]['experienceInYears'] }} years now. I obtained my
                {{ $getAstrologer['recordList'][0]['degree'] }} degree from
                {{ $getAstrologer['recordList'][0]['college'] }} college.
            </p>

            <div class="rounded border mt-4">
                <div class="d-block d-sm-flex align-items-center justify-content-between p-2">
                    <h3 class="font-18 weight500 colorblack m-0 font-weight-bold text-center text-md-left my-1">
                        Send Gift to Expert
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#giftInfoModal">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16.764" height="16.764"
                                    viewBox="0 0 16.764 16.764">
                                    <g id="Icon_feather-info" data-name="Icon feather-info"
                                        transform="translate(0.5 0.5)">
                                        <path id="Path_195175" data-name="Path 195175"
                                            d="M18.764,10.882A7.882,7.882,0,1,1,10.882,3,7.882,7.882,0,0,1,18.764,10.882Z"
                                            transform="translate(-3 -3)" fill="none" stroke="#848484"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1" />
                                        <path id="Path_195176" data-name="Path 195176" d="M18,23.369V18"
                                            transform="translate(-10.118 -11.461)" fill="none"
                                            stroke="#848484" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="1.5" />
                                        <path id="Path_195177" data-name="Path 195177" d="M18,12h0"
                                            transform="translate(-10.118 -8.146)" fill="none" stroke="#848484"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                                    </g>
                                </svg>
                            </span>
                        </a>
                    </h3>

                </div>
                <form id="giftForm">
                    <div id="loadGiftItems" class="loadGiftItems d-flex align-items-center flex-wrap py-2">
                        @foreach ($getGift['recordList'] as $gift)
                        <a class="d-flex align-items-center justify-content-center loadGiftItem"
                            data-gift-id="{{ $gift['id'] }}" data-gift-amount="{{ $gift['amount'] }}">
                            <div>
                                <img src="{{ Str::startsWith($gift['image'], ['http://','https://']) ? $gift['image'] : '/' . $gift['image'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $gift['image'] }}')" style="width: 60px;height:60px;" loading="lazy" />

                                <!-- <img src="/{{ $gift['image'] }}" style="width: 60px;height:60px;"> -->
                                <p style="margin-bottom: 0;"
                                    class="gift-name text-nowrap font-weight-bold py-2">
                                    {{ $gift['name'] }}
                                </p>
                                <span
                                    class="font-weight-semi-bold">
                                    @if($walletType == 'coin')
                                    <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                    @else
                                    {{ $currency['value'] }}
                                    @endif
                                    {{ $gift['amount'] }}</span>
                            </div>
                        </a>
                        @endforeach
                        <input type="hidden" name="astrologerId"
                            value="{{ $getAstrologer['recordList'][0]['id'] }}">
                        <input type="hidden" name="giftId" value="">
                        <input type="hidden" name="giftamount" id="giftamount" value="">

                    </div>
                    <div class="d-flex align-items-center justify-content-center mt-2">
                        @if (authcheck())
                        <a class="btn btn-Waitlist send-gift active" id="send-gift" role="button"
                            data-toggle="modal">
                            Send
                        </a>
                        <button class="btn btn-Waitlist send-gift active" id="send-giftBtn" type="button"
                            style="display:none;" disabled>
                            <span class="spinner-border spinner-border-sm" role="status"
                                aria-hidden="true"></span> Loading...
                        </button>
                        @else
                        <a class="btn btn-Waitlist send-gift" id="send-gift" role="button"
                            data-toggle="modal" data-target="#loginSignUp">
                            Send
                        </a>
                        @endif
                    </div>
                </form>
            </div>
            <!-- Modal -->
            <div id="giftInfoModal" class="modal fade" role="dialog">
                <div class="modal-dialog h-100 d-flex align-items-center">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">

                            <h4 class="modal-title font-weight-bold">
                                How does it work?
                            </h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <ol>
                                <li>
                                    <p>Users can send virtual gifts to the {{ucfirst($professionTitle)}}s.</p>
                                </li>
                                <li>
                                    <p>Users will send these gifts voluntarily and the company does not guarantee
                                        any service in exchange of these gifts.</p>
                                </li>
                                <li>
                                    <p> These gifts are non-refundable.</p>
                                </li>
                                <li>
                                    <p> As per the Company&#39;s policies, gifts can be en-cashed by the {{ucfirst($professionTitle)}}s
                                        in monetary terms.</p>
                                </li>
                            </ol>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="schedule-list-containter">
                <h3 class="font-18 weight500 colorblack m-0  pt-4 font-weight-bold">Check Online Availability</h3>

                <ul id="scheduleContainer"
                    class="bg-pink d-flex py-3 list-unstyled mt-3 justify-content-between px-3 schedule-progressbar">
                    @if (!empty($getAstrologer['recordList']) && !empty($getAstrologer['recordList'][0]['astrologerAvailability']))
                    @foreach ($getAstrologer['recordList'][0]['astrologerAvailability'] as $astrologerAvailability)
                    <li class="active">
                        <div class="schedule-range pb-3">
                            <div class="d-block text-left">
                                <p class="font-weight-bold font-16 mb-2 text-left text-md-center">
                                    {{ \Carbon\Carbon::parse($astrologerAvailability['day'])->format('l') }}
                                </p>
                                <p class="color-red font-12 font-weight-semi-bold text-left text-md-center mb-2">
                                    ({{ \Carbon\Carbon::parse($astrologerAvailability['day'])->format('F d') }})
                                </p>
                            </div>
                        </div>
                        <ul>
                            @if (!empty($astrologerAvailability['time']) && !empty($astrologerAvailability['time'][0]))
                            <li style="color: black !important;">{{ !empty($astrologerAvailability['time'][0]['fromTime']) ? date('h:i A', strtotime($astrologerAvailability['time'][0]['fromTime'])) : '-' }}</li>
                            <li style="color: black !important;">{{ !empty($astrologerAvailability['time'][0]['toTime']) ? date('h:i A', strtotime($astrologerAvailability['time'][0]['toTime'])) : '-' }}</li>
                            @else
                            <li style="color: black !important;">-</li>
                            <li style="color: black !important;">-</li>
                            @endif
                        </ul>
                    </li>
                    @endforeach
                    @endif
                </ul>


            </div>
        </div>
    </div>
</div>
</div>
<div class="bg-pink">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
            </div>
        </div>
    </div>
</div>
<div class="container py-3 py-md-5">
    <div class="row">
        <div class="col-sm-12">
            <div class="bg-white" id="review">
                <ul class="list-unstyled border-bottom pb-2">
                    <li class="font-20 colorblack pb-0 font-weight-bold">Reviews <span
                            class="color-red">{{ $getAstrologer['recordList'][0]['ratingcount'] }}</span>
                    </li>
                    @if ($getAstrologer['recordList'][0]['rating'] > 0)
                    <li class="font18 weight600 coloryellow d-flex align-items-center">
                        <p class="mb-0 ml-1">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <=$averageRating)
                                <i class="fas fa-star filled-star"></i>
                                @else
                                <i class="far fa-star empty-star"></i>
                                @endif
                                @endfor
                        </p>
                    </li>

                </ul>
                <div class="reviewrapper list row">
                    @foreach ($getAstrologer['recordList'][0]['review'] as $index => $review)
                    <div class="reviewslist col-sm-12 col-md-6 {{ $index % 2 == 0 ? 'even' : 'odd' }}">
                        <div class="border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-between pt-2">
                                    <div
                                        class="review-profile-pic d-flex align-items-center justify-content-center bg-pink">
                                        <p class="mb-0 font-20 font-weight-bold">
                                            @if ($review['profile'])
                                            <img src="{{ $review['profile'] }}" class="review-profile-pic"
                                                alt="">
                                            @else
                                            <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img.png') }}"
                                                class="review-profile-pic" alt="">
                                            @endif
                                        </p>
                                    </div>
                                    <div class="ml-2">
                                        <p class="font-16 weight500 m-0 font-weight-bold">
                                            {{ $review['userName'] ? $review['userName'] : 'Anonymous' }}
                                        </p>
                                        <p> <i class="font-18" data-star="{{ $review['rating'] }}"></i></p>
                                    </div>
                                </div>
                            </div>
                            <p class="font-14 mt-1">
                                {{ $review['review'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p>No Review Found</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
@php
$apikey = DB::table('systemflag')->where('name', 'googleMapApiKey')->first();
@endphp
<script src="https://maps.googleapis.com/maps/api/js?key={{ $apikey->value }}&libraries=places">
</script>
<script>
    @if(authcheck())
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%' // Ensure Select2 dropdown takes full width of the parent
        });
    });
    @endif

    function initializeAutocomplete(inputId, latitudeId, longitudeId) {
        var input = document.getElementById(inputId);
        var autocomplete = new google.maps.places.Autocomplete(input);
        var latitude = document.getElementById(latitudeId);
        var longitude = document.getElementById(longitudeId);

        autocomplete.addListener('place_changed', function() {
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

    // Initialize when the page loads
    initializeAutocomplete('BirthPlace', 'latitude', 'longitude');
    initializeAutocomplete('BirthPlace1', 'latitude1', 'longitude1');
</script>

<script>
    $(document).ready(function() {
        $('.loadGiftItem').on('click', function() {
            $('.loadGiftItem').css({
                'box-shadow': '',
                'background': ''
            });
            $(this).css({
                'box-shadow': '0px 3px 6px #E7F1FF',
                'background': '#E7F1FF'
            });

            var selectedGiftId = $(this).data('gift-id');
            var giftamount = $(this).data('gift-amount');

            $('input[name="giftId"]').val(selectedGiftId);
            $('input[name="giftamount"]').val(giftamount);
        });



        $('#send-gift').click(function(e) {
            e.preventDefault();

            var textarea = document.getElementById("giftamount").value;
            // Check if textarea is empty and prevent form submission
            if (textarea.trim() === "") {
                toastr.error('Please select a gift🎁');
                event.preventDefault(); // Prevent form submission if empty
                return;
            }
            var giftamount = $("#giftamount").val();
            // console.log(giftamount);return false;
            @php
            use Symfony\Component\HttpFoundation\Session\Session;
            $session = new Session();
            $token = $session->get('token');

            $wallet_amount = '';
            if (authcheck()) {
                $wallet_amount = $walletAmount;
            }

            @endphp

            var wallet_amount = "{{ $wallet_amount }}";

            $('#send-gift').hide();
            $('#send-giftBtn').show();
            setTimeout(function() {
                $('#send-gift').show();
                $('#send-giftBtn').hide();
            }, 7000);


            var formData = $('#giftForm').serialize();

            // console.log(parseInt(giftamount),'giftamnt');
            // console.log(parseInt(wallet_amount), 'waltamnt');
            if (parseInt(giftamount) > parseInt(wallet_amount)) {
                toastr.error('Insufficient Balance');
                window.location.href = "{{ route('front.walletRecharge') }}"
                //   console.log("hhh");
                return false;

            }

            $.ajax({
                url: "{{ route('api.sendGifts',['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Gift Sent Successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });

        // Follow
        $('#btnFollow').click(function(e) {
            e.preventDefault();

            @php
            $token = $session->get('token');
            @endphp

            var formData = $('#followastro').serialize();
            $.ajax({
                url: "{{ route('api.addFollowing', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Followed Successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });

        // Unfollow
        $('#btnUnFollow').click(function(e) {
            e.preventDefault();
            @php
            $token = $session->get('token');
            @endphp

            var formData = $('#unfollowfollowastro').serialize();
            $.ajax({
                url: "{{ route('api.updateFollowing', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('UnFollowed Successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });


    });
</script>

{{-- for chat --}}
<script>
    const firestore = firebase.firestore();
    // Function to send a message
    function sendMessage(senderId, receiverId, message, isEndMessage, attachementPath) {
        const chatRef = firestore.collection('chats').doc(`${receiverId}_${senderId}`).collection('userschat').doc(
            receiverId).collection('messages');
        const timestamp = new Date();
        // Generate a unique ID for the message
        const messageId = chatRef.doc().id;

        chatRef.doc(messageId).set({
                id: null,
                createdAt: timestamp,
                invitationAcceptDecline: null,
                isDelete: false,
                isEndMessage: isEndMessage,
                isRead: false,
                messageId: messageId,
                reqAcceptDecline: null,
                status: null,
                updatedAt: timestamp,
                url: null,
                userId1: senderId,
                userId2: receiverId,
                message: message,
                attachementPath: attachementPath, // Pass attachementPath to the message object
            })
            .then(() => {
                // console.log("Message sent with ID: ", messageId);
            })
            .catch((error) => {
                console.error("Error sending message: ", error);
            });
    }




    $(document).ready(function() {
        $('#intakeBtn').click(function(e) {
            e.preventDefault();

            var form = document.getElementById('intakeForm');
            if (form.checkValidity() === false) {
                form.reportValidity();
                return;
            }

            @if(authcheck())
            var sessionAvailable = "{{$isChatSessionavailable}}";
            @endif


            if (sessionAvailable == false) {
                toastr.error('Your request is already there');
                return false;
            }

            $('#intakeBtn').hide();
            $('#loaderintakeBtn').show();
            setTimeout(function() {
                $('#intakeBtn').show();
                $('#loaderintakeBtn').hide();
            }, 3000);


            var astrocharge = "{{$getAstrologer['recordList'][0]['charge']}}";


            <?php
            $wallet_amount = '';
            if (authcheck()) {
                $wallet_amount = authcheck()['totalWalletAmount'];
            }
            ?>

            var formData = $('#intakeForm').serialize();


            // Parse form data as URL parameters
            var urlParams = new URLSearchParams(formData);
            var chat_duration = parseInt(urlParams.get('chat_duration'));

            var chat_duration_minutes = Math.ceil(chat_duration / 60);

            var total_charge = astrocharge * chat_duration_minutes;
            var isFreeAvailable = "{{ $getAstrologer['recordList'][0]['isFreeAvailable'] }}";

            var wallet_amount = "{{ $wallet_amount }}";

            // for message send
            var astrologerId = "{{$getAstrologer['recordList'][0]['id']}}";
            @if(authcheck())
            var userId = "{{authcheck()['id']}}";
            @endif
            var formDatas = $('#intakeForm').serializeArray();
            var name = formDatas.find(item => item.name === 'name').value;
            var gender = formDatas.find(item => item.name === 'gender').value;
            var birthDate = formDatas.find(item => item.name === 'birthDate').value;
            var birthTime = formDatas.find(item => item.name === 'birthTime').value;
            var birthPlace = formDatas.find(item => item.name === 'birthPlace').value;
            var maritalStatus = formDatas.find(item => item.name === 'maritalStatus').value;
            var topicOfConcern = formDatas.find(item => item.name === 'topicOfConcern').value;

            var message = `Hi {{$getAstrologer['recordList'][0]['name']}}
                Below are my details:

                Name: ${name},
                Gender: ${gender},
                DOB: ${birthDate},
                TOB: ${birthTime},
                POB: ${birthPlace},
                Marital status: ${maritalStatus},
                TOPIC: ${topicOfConcern}

                This is an automated message to confirm that chat has started.`;


            // Check if free chat is available and wallet has sufficient balance
            if (isFreeAvailable != true) {
                if (total_charge <= wallet_amount) {
                    $.ajax({
                        url: "{{ route('api.addChatRequest', ['token' => $token]) }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            $.ajax({
                                url: "{{ route('api.intakeForm', ['token' => $token]) }}",
                                type: 'POST',
                                data: formData,
                                success: function(response) {
                                    sendMessage(userId, astrologerId, message, false, '');
                                    setTimeout(function() {
                                        toastr.success(
                                            'Chat Request Sent ! you will be notified if {{strtolower($professionTitle)}} accept your request.'
                                        );
                                        window.location.reload();

                                    }, 2000);
                                },
                                error: function(xhr, status, error) {
                                    if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                                        toastr.error(xhr.responseJSON.recordList.message);
                                    } else {
                                        toastr.error(xhr.responseText);
                                    }
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                                toastr.error(xhr.responseJSON.recordList.message);
                            } else {
                                toastr.error(xhr.responseText);
                            }
                        }
                    });

                } else {
                    toastr.error('Insufficient balance. Please recharge your wallet.');
                    window.location.href = "{{ route('front.walletRecharge') }}"
                }
            } else {

                $.ajax({
                    url: "{{ route('api.addChatRequest', ['token' => $token]) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $.ajax({
                            url: "{{ route('api.intakeForm', ['token' => $token]) }}",
                            type: 'POST',
                            data: formData,
                            success: function(response) {

                                setTimeout(function() {
                                    sendMessage(userId, astrologerId, message, false, '');
                                    toastr.success(
                                        'Chat Request Sent ! you will be notified if {{strtolower($professionTitle)}} accept your request.'
                                    );
                                    window.location.reload();

                                }, 2000);
                            },
                            error: function(xhr, status, error) {
                                if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                                    toastr.error(xhr.responseJSON.recordList.message);
                                } else {
                                    toastr.error(xhr.responseText);
                                }
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                            toastr.error(xhr.responseJSON.recordList.message);
                        } else {
                            toastr.error(xhr.responseText);
                        }
                    }
                });


            }
        });
    });
</script>

<script>
    $(document).ready(function() {


        $('#audio-call-btn').click(function() {
            $("#call_type").val("10");
            $("#astrocharge").val("{{ $getAstrologer['recordList'][0]['charge'] }}");

        });




        $('#callintakeBtn').click(function(e) {
            e.preventDefault();

            var form = document.getElementById('callintakeForm');
            if (form.checkValidity() === false) {
                form.reportValidity();
                return;
            }

            @if(authcheck())
            var sessionAvailable = "{{$isCallSessionavailable}}";
            @endif


            if (sessionAvailable == false) {
                toastr.error('Your request is already there');
                return false;
            }

            $('#callintakeBtn').hide();
            $('#callloaderintakeBtn').show();
            setTimeout(function() {
                $('#callintakeBtn').show();
                $('#callloaderintakeBtn').hide();
            }, 3000);

            astrocharge = $("#astrocharge").val();



            <?php
            $wallet_amount = '';
            if (authcheck()) {
                $wallet_amount = authcheck()['totalWalletAmount'];
            }
            ?>

            var formData = $('#callintakeForm').serialize();

            // Parse form data as URL parameters
            var urlParams = new URLSearchParams(formData);
            var call_duration = parseInt(urlParams.get('call_duration'));
            var call_duration_minutes = Math.ceil(call_duration / 60);

            var total_charge = astrocharge * call_duration_minutes;

            var isFreeAvailable = "{{ $getAstrologer['recordList'][0]['isFreeAvailable'] }}";

            var wallet_amount = "{{$wallet_amount }}";



            // Check if free chat is available and wallet has sufficient balance
            if (isFreeAvailable != true) {
                if (total_charge <= wallet_amount) {
                    $.ajax({
                        url: "{{ route('api.addCallRequest', ['token' => $token]) }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            $.ajax({
                                url: "{{ route('api.intakeForm', ['token' => $token]) }}",
                                type: 'POST',
                                data: formData,
                                success: function(response) {

                                    setTimeout(function() {
                                        toastr.success(
                                            'Call Request Sent ! you will be notified if {{strtolower($professionTitle)}} accept your request.'
                                        );
                                        window.location.href = "{{ route('front.home') }}";

                                    }, 2000);
                                },
                                error: function(xhr, status, error) {
                                    toastr.error(xhr.responseText);
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                                toastr.error(xhr.responseJSON.recordList.message);
                            } else {
                                toastr.error(xhr.responseText);
                            }
                        }
                    });

                } else {
                    toastr.error('Insufficient balance. Please recharge your wallet.');
                    window.location.href = "{{ route('front.walletRecharge') }}"
                }
            } else {

                $.ajax({
                    url: "{{ route('api.addCallRequest', ['token' => $token]) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $.ajax({
                            url: "{{ route('api.intakeForm', ['token' => $token]) }}",
                            type: 'POST',
                            data: formData,
                            success: function(response) {

                                setTimeout(function() {
                                    toastr.success(
                                        'Call Request Sent ! you will be notified if {{strtolower($professionTitle)}} accept your request.'
                                    );
                                    window.location.href = "{{ route('front.home') }}";

                                }, 2000);
                            },
                            error: function(xhr, status, error) {
                                toastr.error(xhr.responseText);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.recordList && xhr.responseJSON.recordList.message) {
                            toastr.error(xhr.responseJSON.recordList.message);
                        } else {
                            toastr.error(xhr.responseText);
                        }
                    }
                });


            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#reportBlockBtn').click(function(e) {
            e.preventDefault();

            var textarea = document.getElementById("review");
            if (textarea.value.trim() === "") {
                toastr.error('Please enter your reason.');
                event.preventDefault(); // Prevent form submission if empty
            }

            @php
            $token = $session->get('token');

            @endphp

            var formData = $('#reportBlockForm').serialize();
            $.ajax({
                url: "{{ route('api.reportBlockAstrologer', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Reported Successfully');
                    setTimeout(function() {
                        window.location.reload()
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    var errorMessage = JSON.parse(xhr.responseText).error.paymentMethod[0];
                    toastr.error(errorMessage);
                }
            });

        });
    });


    $(document).ready(function() {
        $('#btnunBlock').click(function(e) {
            e.preventDefault();

            @php
            $token = $session->get('token');
            @endphp

            var formData = $('#unblockastrologer').serialize();
            $.ajax({
                url: "{{ route('api.unblockAstrologer', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Unblocked Successfully ');
                    setTimeout(function() {
                        window.location.reload()
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    var errorMessage = JSON.parse(xhr.responseText).error.paymentMethod[0];
                    toastr.error(errorMessage);
                }
            });

        });
    });
</script>

@endsection
