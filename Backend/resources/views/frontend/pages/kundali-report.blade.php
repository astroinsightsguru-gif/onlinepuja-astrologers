@extends('frontend.layout.master')
<style>
    .tab-headbox {
        overflow-x: hidden;
    }

    #kundaliTab {
        float: left;
        display: flex;
        flex-wrap: nowrap;
        cursor: pointer;
    }

    #kundaliTab {
        padding: 0 15px;
        border-bottom: 1px solid #c2185b !important;
        width: 100%;
    }

    .nav-tabs {
        margin-bottom: 22px !important;
        border-bottom: none !important;
    }

    .nav {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }

    .nav-tabs .nav-item {
        margin-bottom: -1px;
    }

    #kundaliTab li a.active {
        border-color: #c2185b #c2185b #FFFFFF !important;
        color: #5E5E5E;
    }

    #kundaliTab li a {
        font-weight: 600;
        color: #000000;
        white-space: nowrap;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }

    .nav-link {
        display: block;
        padding: .5rem 1rem !important;
    }

    .nav-tabs>li>a {
        background: white !important;
        border-bottom: 1px solid #c2185b !important;
    }
</style>
@section('content')

@if($KundaliReport['status']==400 || $KundaliReport['status']==402)
<div class="container">
    <div class="row justify-content-center py-3 mt-5 mb-5">
        <h3 class=" mt-3">No Kundali Found</h3>
    </div>
</div>
@else
<div class="container">
    <div class="row py-3">
        <div class="col-sm-12 mt-4">

            <div class="tab-headbox">
                <ul class="nav nav-tabs" id="kundaliTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-toggle="tab" href="#basic">Basic Details</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#planetaryposition">Planetary Position</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#predictions">Predictions</a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#shodashvarga">Shodashvarga</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#ashtakvarga">Ashtakvarga</a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#mahadasha">Mahadasha</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#yogini">Yogini Dasha</a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#dosha">Dosha</a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" href="#report">Report</a>
                    </li>

                </ul>
            </div>
            <div class="tab-content" id="kundaliTabContent">
                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4 active show" id="basic" role="tabpanel"
                    aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="table-theme overflow-visible mb-3 shadow-pink rounded-10-top">
                                <div
                                    class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3 rounded-10-top">
                                    Birth Details
                                </div>
                                <table class="table table-bordered border-pink font-14 mb-1 w-100">
                                    <tbody>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Name
                                            </td>
                                            <td>
                                                {{ $KundaliReport['recordList']['name'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Birth Date
                                            </td>
                                            <td>
                                                {{ date("d-m-Y" ,strtotime($KundaliReport['recordList']['birthDate'])) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Birth Time
                                            </td>
                                            <td>
                                                {{ $KundaliReport['recordList']['birthTime'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Birth Place
                                            </td>
                                            <td>
                                                {{ $KundaliReport['recordList']['birthPlace'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Latitude
                                            </td>
                                            <td>
                                                {{ $KundaliReport['recordList']['latitude'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Longitude
                                            </td>
                                            <td>
                                                {{ $KundaliReport['recordList']['longitude'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Timezone
                                            </td>
                                            <td>
                                                {{ $KundaliReport['recordList']['timezone'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Rasi
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['rasi'] }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Pdf Link
                                            </td>
                                            <td>
                                                @if (isset($KundaliReport['recordList']['pdf_type']) && $KundaliReport['recordList']['pdf_type'] != 'basic')
                                                <a class="text-black"
                                                    href="{{ $KundaliReport['recordList']['pdf_link'] }}">
                                                    <i class="fa-solid fa-file-pdf"></i> Pdf
                                                </a>
                                                <span class="text-danger mt-2"> &nbsp;(note : pdf generation will take some time so wait for it.)</span>
                                                @else
                                                No PDF found
                                                @endif
                                            </td>

                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="table-theme overflow-visible mb-3 shadow-pink rounded-10-top">
                                <div
                                    class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3 rounded-10-top">
                                    Panchang Details
                                </div>
                                <table class="table table-bordered border-pink font-14 mb-1 w-100">
                                    <tbody>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Tithi
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['tithi'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Yoga
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['yoga'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Karana
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['karana'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Sun Rise
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['sunrise_at_birth'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Sun Set
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['sunset_at_birth'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Ayanamsa
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['ayanamsa_name'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Hora
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['hora_lord'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Day Lord
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['day_lord'] }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="font-weight-semi-bold">
                                                Day of Birth
                                            </td>
                                            <td>
                                                {{ $KundaliReport['planet']['response']['panchang']['day_of_birth'] }}
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="planetaryposition" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive table-theme shadow-pink">
                                <table class="table table-bordered border-pink font-14 mb-0">
                                    <thead class="matchV_thead bg-pink color-red">
                                        <tr>
                                            <th class="cellhead">Planet</th>
                                            <th class="cellhead">C</th>
                                            <th class="cellhead">R</th>
                                            <th class="cellhead">Rashi</th>
                                            <th class="cellhead">Local Degree</th>
                                            <th class="cellhead">Global Degree</th>
                                            <th class="cellhead">Nakshatra</th>
                                            <th class="cellhead">Pada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        // Ensure $KundaliReport['planet']['response'] is an array
                                        $response = $KundaliReport['planet']['response'] ?? [];

                                        // Filter numerical keys (0 to 9)
                                        $filteredData = array_filter(
                                        $response,
                                        function ($key) {
                                        return is_int($key);
                                        },
                                        ARRAY_FILTER_USE_KEY,
                                        );
                                        @endphp

                                        @foreach ($filteredData as $planetdata)
                                        <tr>
                                            <td>{{ $planetdata['full_name'] ?? 'N/A' }}</td>
                                            <td>{{ isset($planetdata['is_combust']) && $planetdata['is_combust'] ? 'C' : '' }}
                                            </td>
                                            <td>{{ isset($planetdata['retro']) && $planetdata['retro'] ? 'R' : '' }}
                                            </td>
                                            <td>{{ $planetdata['zodiac'] ?? 'N/A' }}</td>
                                            <td>{{ $planetdata['local_degree'] ?? 'N/A' }}</td>
                                            <td>{{ $planetdata['global_degree'] ?? 'N/A' }}</td>
                                            <td>{{ $planetdata['nakshatra'] ?? 'N/A' }}</td>
                                            <td>{{ $planetdata['nakshatra_pada'] ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="shodashvarga" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <h2>Horoscope Chart</h2>
                    <div class="row">

                        @php
                        $chartNames = [
                        'D1' => 'Rasi',
                        'D2' => 'Hora',
                        'D3' => 'Drekkana',
                        'D4' => 'Chaturthamsa',
                        'D5' => 'Panchamamsa',
                        'D6' => 'Shastamsa',
                        'D7' => 'Saptamsa',
                        'D8' => 'Astamsa',
                        'D9' => 'Navamsa',
                        'D10' => 'Dasamsa',
                        'D11' => 'Rudramsa',
                        'D12' => 'Dwadasamsa',
                        'D16' => 'Shodasamsa',
                        'D20' => 'Vimsamsa',
                        'D24' => 'Siddhamsa',
                        'D27' => 'Nakshatramsa',
                        'D30' => 'Trimsamsa',
                        'D40' => 'Khavedamsa',
                        'D45' => 'Akshavedamsa',
                        'D60' => 'Shastyamsa',
                        'chalit' => 'Chalit',
                        'sun' => 'Sun',
                        'moon' => 'Moon',
                        'kp_chalit' => 'Kp Chalit',
                        ];
                        @endphp
                        @foreach ($KundaliReport['charts'] as $key => $chart)
                        <div class="col-md-4 mt-3">
                            <p class="font-16 mb-1"> <strong>{{ $chartNames[$key] ?? $key }}</strong></p>
                            {!! $chart !!}
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="ashtakvarga" role="tabpanel"
                    aria-labelledby="contact-tab">


                    <div class="ds-head-populararticle bg-white">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">

                                    <div class="row">
                                        <div class="col-12">
                                            <h2 class="font-24">Ashtakvarga</h2>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-users table-theme shadow-pink mb-3">
                                                <table class="table table-bordered border-pink font-14 mb-0"
                                                    cellpadding="0" cellspacing="0" border="0">
                                                    <thead class="font-13">
                                                        <tr class="bg-pink color-red font-weight-normal">
                                                            <th class="cellhead">&nbsp;</th>
                                                            <th>Ar</th>
                                                            <th>Ta</th>
                                                            <th>Ge</th>
                                                            <th>Ca</th>
                                                            <th>Le</th>
                                                            <th>Vi</th>
                                                            <th>Li</th>
                                                            <th>Sc</th>
                                                            <th>Sa</th>
                                                            <th>Ca</th>
                                                            <th>Aq</th>
                                                            <th>Pi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($KundaliReport['ashtakvarga']['response']['ashtakvarga_order'] as $index => $name)
                                                        @if ($name !== 'Ascendant')
                                                        <tr>
                                                            <td>{{ $name }}</td>
                                                            @foreach ($KundaliReport['ashtakvarga']['response']['ashtakvarga_points'][$index] as $points)
                                                            <td>{{ $points }}</td>
                                                            @endforeach
                                                        </tr>
                                                        @endif
                                                        @endforeach
                                                        <tr>
                                                            <td>Total</td>
                                                            @foreach ($KundaliReport['ashtakvarga']['response']['ashtakvarga_total'] as $total)
                                                            <td>{{ $total }}</td>
                                                            @endforeach
                                                        </tr>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <h2 class="font-24">Binnashtakvarga</h2>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-users table-theme shadow-pink mb-3">
                                                <table class="table table-bordered border-pink font-14 mb-0"
                                                    cellpadding="0" cellspacing="0" border="0">
                                                    <thead class="font-13">
                                                        <tr class="bg-pink color-red font-weight-normal">

                                                            @foreach ($KundaliReport['binnashtakvarga']['response'] as $name => $points)
                                                            <th>{{ $name }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @for ($i = 0; $i < 12; $i++)
                                                            <tr>
                                                            @foreach ($KundaliReport['binnashtakvarga']['response'] as $points)
                                                            <td>{{ $points[$i] }}</td>
                                                            @endforeach
                                                            </tr>
                                                            @endfor
                                                    </tbody>


                                                </table>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="predictions" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="font-24">Predictions</h2>
                        </div>
                        <div class="col-12">
                            <div class="panel-group my-1" id="accordion">

                                @php
                                $ordinalWords = [
                                1 => 'First',
                                2 => 'Second',
                                3 => 'Third',
                                4 => 'Fourth',
                                5 => 'Fifth',
                                6 => 'Sixth',
                                7 => 'Seventh',
                                8 => 'Eighth',
                                9 => 'Ninth',
                                10 => 'Tenth',
                                11 => 'Eleventh',
                                12 => 'Twelth',
                                ];
                                @endphp

                                @foreach ($KundaliReport['personal']['response'] as $index => $prediction)
                                @php
                                $houseNumber = $index + 1; // Adjust for 1-based index
                                $houseWord = $ordinalWords[$houseNumber] ?? $houseNumber; // Fallback to number if not in array
                                @endphp

                                <div class="panel panel-default mb-3">
                                    <div class="panel-heading">
                                        <h3 class="panel-title mb-0">
                                            <a class="accordion-toggle font-weight-semi d-block py-2 colorblack font-16"
                                                data-toggle="collapse" data-parent="#accordion"
                                                href="#accAbount_{{ $index }}">
                                                {{ $houseWord }} House
                                            </a>
                                        </h3>
                                    </div>
                                    <div id="accAbount_{{ $index }}"
                                        class="panel-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                        data-parent="#accordion">
                                        <div class="panel-body px-0 px-md-3 py-4 border-top">
                                            <p>{{ $prediction['personalised_prediction'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>



                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="mahadasha" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="font-24 mb-0" id="currentTitle">Mahadasha</h2>
                            <div class="d-flex align-items-center mb-3">
                                <!-- Improved Breadcrumb Navigation -->
                                <nav aria-label="Dasha navigation" class="flex-grow-1">
                                    <ol class="breadcrumb mb-0 d-flex align-items-center" id="dashaBreadcrumb">
                                        <li class="breadcrumb-item d-flex align-items-center">
                                            <span class="font-16 dasha-breadcrumb-item"
                                                data-type="maha"
                                                onclick="navigateToDasha('maha')"
                                                style="cursor:pointer">
                                                <i class="fas fa-layer-group mr-1"></i> Mahadasha
                                            </span>
                                        </li>
                                    </ol>
                                </nav>

                            </div>
                        </div>

                        <div class="col-12">
                            <!-- Main Table Container -->
                            <div id="dashaTableContainer">
                                <!-- Mahadasha Table (Initial) -->
                                <div class="table-users table-theme shadow-pink mb-3" id="mahaDashaTable">
                                    <table class="table table-bordered border-pink font-14 mb-0">
                                        <thead class="font-13">
                                            <tr class="bg-pink color-red font-weight-normal">
                                                <th class="cellhead">MahaDasha</th>
                                                <th class="cellhead">MahaDasha Order</th>
                                                <th class="cellhead" style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($KundaliReport['mahaDasha']['response']['mahadasha'] as $index => $mahadasha)
                                            <tr class="dasha-row maha-row" data-type="maha" data-index="{{ $index }}" style="cursor:pointer">
                                                <td>{{ $mahadasha }}</td>
                                                <td>{{ $KundaliReport['mahaDasha']['response']['mahadasha_order'][$index] }}</td>
                                                <td class="text-center">
                                                    <i class="fas fa-chevron-right text-muted"></i>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Mahadasha Predictions -->
                        <div class="col-12">
                            <h3 class="font-20 mb-2">Mahadasha Predictions</h3>
                        </div>
                        <div class="col-12">
                            @foreach ($KundaliReport['mahaDashaPrediction']['response']['dashas'] as $prediction)
                            <div class="prediction-block mb-4">
                                <h4 class="font-18">{{ $prediction['dasha'] }}
                                    ({{ $prediction['dasha_start_year'] }} - {{ $prediction['dasha_end_year'] }})
                                </h4>
                                <p class="font-14"><strong>Prediction:</strong> {{ $prediction['prediction'] }}
                                </p>
                                <p class="font-14"><strong>Planet in Zodiac:</strong>
                                    {{ $prediction['planet_in_zodiac'] }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                <!--Yogini Dasha-->

                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="yogini" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="font-24 mb-0" id="yoginiCurrentTitle">Yogini Dasha</h2>
                            <div class="d-flex align-items-center mb-3">
                                <!-- Yogini Dasha Breadcrumb Navigation -->

                                <nav aria-label="Yogini Dasha navigation" class="flex-grow-1">
                                    <ol class="breadcrumb mb-0 d-flex align-items-center" id="yoginiDashaBreadcrumb">
                                        <li class="breadcrumb-item d-flex align-items-center">
                                            <span class="font-16 yogini-breadcrumb-item active"
                                                data-type="main"
                                                onclick="navigateToYoginiDasha('main')"
                                                style="cursor:pointer">
                                                <i class="fas fa-layer-group mr-1"></i> Yogini Dasha
                                            </span>
                                        </li>
                                    </ol>
                                </nav>

                            </div>
                        </div>

                        <div class="col-12">
                            <!-- Main Table Container -->
                            <div id="yoginiDashaTableContainer">
                                <!-- Main Yogini Dasha Table (Initial) -->
                                <div class="table-users table-theme shadow-pink mb-3" id="yoginiMainDashaTable">
                                    <table class="table table-bordered border-pink font-14 mb-0">
                                        <thead class="font-13">
                                            <tr class="bg-pink color-red font-weight-normal">
                                                <th class="cellhead">Dasha</th>
                                                <th class="cellhead">Dasha Lord</th>
                                                <th class="cellhead">End Date</th>
                                                <th class="cellhead" style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($KundaliReport['yoginiDashaMain']['response']['dasha_list'] as $index => $dasha)
                                            <tr class="yogini-row main-row"
                                                data-type="main"
                                                data-index="{{ $index }}"
                                                style="cursor:pointer">
                                                <td>{{ $dasha }}</td>
                                                <td>{{ $KundaliReport['yoginiDashaMain']['response']['dasha_lord_list'][$index] }}</td>
                                                <td>{{ $KundaliReport['yoginiDashaMain']['response']['dasha_end_dates'][$index] }}</td>
                                                <td class="text-center">
                                                    <i class="fas fa-chevron-right text-muted"></i>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Dosha-->

                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="dosha" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="font-24">Doshas</h2>
                        </div>

                        <!-- Mangal Dosh Table -->
                        <div class="col-12 mb-3">
                            <div class="table-users table-theme shadow-pink">
                                <table class="table table-bordered border-pink font-14 mb-0" cellpadding="0"
                                    cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead" colspan="2">Mangal Dosh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <p>{{ $KundaliReport['mangalDosh']['response']['bot_response'] }}</p>
                                                <div class="dosha-factors">
                                                    @foreach ($KundaliReport['mangalDosh']['response']['factors'] as $factor)
                                                    <p>{{ $factor }}</p>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Kaal Sarp Dosh Table -->
                        <div class="col-12 mb-3">
                            <div class="table-users table-theme shadow-pink">
                                <table class="table table-bordered border-pink font-14 mb-0" cellpadding="0"
                                    cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead" colspan="2">Kaal Sarp Dosh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <p>{{ $KundaliReport['kaalsarpDosh']['response']['bot_response'] }}</p>
                                                @if (!empty($KundaliReport['kaalsarpDosh']['response']['remedies']) && $KundaliReport['kaalsarpDosh']['response']['is_dosha_present'] == true)
                                                <h5 class="font-16">Remedies</h5>
                                                <div class="dosha-remedies">
                                                    @foreach ($KundaliReport['kaalsarpDosh']['response']['remedies'] as $remedy)
                                                    <p>{{ $remedy }}</p>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Manglik Dosh Table -->
                        <div class="col-12 mb-3">
                            <div class="table-users table-theme shadow-pink">
                                <table class="table table-bordered border-pink font-14 mb-0" cellpadding="0"
                                    cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead" colspan="2">Manglik Dosh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <p>{{ $KundaliReport['manglikDosh']['response']['bot_response'] }}</p>
                                                <div class="dosha-aspects">
                                                    @foreach ($KundaliReport['manglikDosh']['response']['aspects'] as $aspect)
                                                    <p>{{ $aspect }}</p>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pitra Dosh Table -->
                        <div class="col-12 mb-3">
                            <div class="table-users table-theme shadow-pink">
                                <table class="table table-bordered border-pink font-14 mb-0" cellpadding="0"
                                    cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead" colspan="2">Pitra Dosh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <p>{{ $KundaliReport['pitraDosh']['response']['bot_response'] }}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Papasamaya Dosh Table -->
                        <div class="col-12 mb-3">
                            <div class="table-users table-theme shadow-pink">
                                <table class="table table-bordered border-pink font-14 mb-0" cellpadding="0"
                                    cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead" colspan="2">Papasamaya Dosh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <table class="table table-bordered border-pink font-14 mb-0"
                                                    cellpadding="0" cellspacing="0" border="0">
                                                    <thead class="font-13">
                                                        <tr class="bg-pink color-red font-weight-normal">
                                                            <th class="cellhead">Planet</th>
                                                            <th class="cellhead">Papa Score</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Rahu</td>
                                                            <td>{{ $KundaliReport['papasamayaDosh']['response']['rahu_papa'] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sun</td>
                                                            <td>{{ $KundaliReport['papasamayaDosh']['response']['sun_papa'] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Saturn</td>
                                                            <td>{{ $KundaliReport['papasamayaDosh']['response']['saturn_papa'] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Mars</td>
                                                            <td>{{ $KundaliReport['papasamayaDosh']['response']['mars_papa'] }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <!--Report-->

                <div class="tab-pane fade shadow-pink rounded-10 font-14 p-4" id="report" role="tabpanel"
                    aria-labelledby="contact-tab">
                    <div class="row">
                        <!-- Ascendant Report -->
                        <div class="col-12">
                            <h2 class="font-24">Ascendant Report</h2>
                            <div class="table-users table-theme shadow-pink mb-3">
                                <table class="table table-bordered border-pink font-14 mb-0" cellpadding="0"
                                    cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead">Aspect</th>
                                            <th class="cellhead">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($KundaliReport['ascendantReport']['response'] as $ascendant)
                                        <tr>
                                            <td><strong>Ascendant</strong></td>
                                            <td>{{ $ascendant['ascendant'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ascendant Lord</strong></td>
                                            <td>{{ $ascendant['ascendant_lord'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ascendant Lord Location</strong></td>
                                            <td>{{ $ascendant['ascendant_lord_location'] }}
                                                ({{ $ascendant['ascendant_lord_house_location'] }}th house)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>General Prediction</strong></td>
                                            <td>{{ $ascendant['general_prediction'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Personalized Prediction</strong></td>
                                            <td>{{ $ascendant['personalised_prediction'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Verbal Location</strong></td>
                                            <td>{{ $ascendant['verbal_location'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ascendant Lord Strength</strong></td>
                                            <td>{{ $ascendant['ascendant_lord_strength'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Symbol</strong></td>
                                            <td>{{ $ascendant['symbol'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Zodiac Characteristics</strong></td>
                                            <td>{{ $ascendant['zodiac_characteristics'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lucky Gem</strong></td>
                                            <td>{{ $ascendant['lucky_gem'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Day for Fasting</strong></td>
                                            <td>{{ $ascendant['day_for_fasting'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gayatri Mantra</strong></td>
                                            <td>{{ $ascendant['gayatri_mantra'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Flagship Qualities</strong></td>
                                            <td>{{ $ascendant['flagship_qualities'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Spirituality Advice</strong></td>
                                            <td>{{ $ascendant['spirituality_advice'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Good Qualities</strong></td>
                                            <td>{{ $ascendant['good_qualities'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bad Qualities</strong></td>
                                            <td>{{ $ascendant['bad_qualities'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Planet Report -->
                        <div class="col-12">
                            <h2 class="font-24">Planet Report</h2>
                            @foreach ($KundaliReport['planetReport'] as $planet => $data)
                            @if ($data['status'] === 200)
                            @foreach ($data['response'] as $planetDetails)
                            <div class="table-users table-theme shadow-pink mb-3">
                                <table class="table table-bordered border-pink font-14 mb-0"
                                    cellpadding="0" cellspacing="0" border="0">
                                    <thead class="font-13">
                                        <tr class="bg-pink color-red font-weight-normal">
                                            <th class="cellhead">{{ $planet }} Report</th>
                                            <th class="cellhead">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Planet Location</strong></td>
                                            <td>{{ $planetDetails['planet_location'] }}
                                                ({{ $planetDetails['planet_native_location'] }}th house)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Planet Zodiac</strong></td>
                                            <td>{{ $planetDetails['planet_zodiac'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Zodiac Lord</strong></td>
                                            <td>{{ $planetDetails['zodiac_lord'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Zodiac Lord Location</strong></td>
                                            <td>{{ $planetDetails['zodiac_lord_location'] }}
                                                ({{ $planetDetails['zodiac_lord_house_location'] }}th
                                                house)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>General Prediction</strong></td>
                                            <td>{{ $planetDetails['general_prediction'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Planet Definitions</strong></td>
                                            <td>{{ $planetDetails['planet_definitions'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gayatri Mantra</strong></td>
                                            <td>{{ $planetDetails['gayatri_mantra'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Qualities Long</strong></td>
                                            <td>{{ $planetDetails['qualities_long'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Qualities Short</strong></td>
                                            <td>{{ $planetDetails['qualities_short'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Affliction</strong></td>
                                            <td>{{ $planetDetails['affliction'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Personalized Prediction</strong></td>
                                            <td>{{ $planetDetails['personalised_prediction'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Verbal Location</strong></td>
                                            <td>{{ $planetDetails['verbal_location'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Planet Zodiac Prediction</strong></td>
                                            <td>{{ $planetDetails['planet_zodiac_prediction'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Character Keywords Positive</strong></td>
                                            <td>{{ implode(', ', $planetDetails['character_keywords_positive']) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Character Keywords Negative</strong></td>
                                            <td>{{ implode(', ', $planetDetails['character_keywords_negative']) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endforeach
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endsection
    <style>
        .dasha-breadcrumb-item {
            padding: 4px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #dee2e6;
            color: #495057;
            font-weight: 500;
        }

        .dasha-breadcrumb-item:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-color: #adb5bd;
            color: #212529;
        }

        .dasha-breadcrumb-item.active {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white !important;
            font-weight: 600;
            border: 1px solid #495057;
            box-shadow: 0 3px 8px rgba(73, 80, 87, 0.3);
        }

        .dasha-breadcrumb-item.active:hover {
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            border-color: #343a40;
        }

        .dasha-breadcrumb-item i {
            font-size: 14px;
            color: #6c757d;
        }

        .dasha-breadcrumb-item.active i {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-size: 18px;
            color: #6c757d;
            padding: 0 8px;
            font-weight: 600;
        }

        /* Table row hover effects */
        .dasha-row:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .dasha-row:hover td:last-child i {
            color: #495057;
            transform: translateX(3px);
            transition: all 0.2s ease;
        }

        /* Yogini Dasha specific styles */
        .yogini-breadcrumb-item {
            padding: 4px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #dee2e6;
            color: #495057;
            font-weight: 500;
        }

        .yogini-breadcrumb-item:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-color: #adb5bd;
            color: #212529;
        }

        .yogini-breadcrumb-item.active {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white !important;
            font-weight: 600;
            border: 1px solid #495057;
            box-shadow: 0 3px 8px rgba(73, 80, 87, 0.3);
        }

        .yogini-breadcrumb-item.active:hover {
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            border-color: #343a40;
        }

        .yogini-breadcrumb-item i {
            font-size: 14px;
            color: #6c757d;
        }

        .yogini-breadcrumb-item.active i {
            color: white;
        }

        /* Use the same breadcrumb separator */
        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-size: 18px;
            color: #6c757d;
            padding: 0 8px;
            font-weight: 600;
        }
    </style>
    @section('scripts')
    <script>
        const tabHeadbox = document.querySelector('.tab-headbox');
        let isDown = false,
            startX, scrollLeft;

        tabHeadbox.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - tabHeadbox.offsetLeft;
            scrollLeft = tabHeadbox.scrollLeft;
        });

        tabHeadbox.addEventListener('mouseleave mouseup', () => isDown = false);

        tabHeadbox.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            tabHeadbox.scrollLeft = scrollLeft - (e.pageX - tabHeadbox.offsetLeft - startX) * 2;
        });

        tabHeadbox.addEventListener('wheel', (e) => {
            e.preventDefault();
            tabHeadbox.scrollLeft += e.deltaY;
        });
    </script>
    <script>
        const antarDashas = @json($KundaliReport['antarDasha']['response']['antardashas']);
        const antarOrders = @json($KundaliReport['antarDasha']['response']['antardasha_order']);

        const paryDashas = @json($KundaliReport['paryantarDasha']['response']['paryantardasha']);
        const paryOrders = @json($KundaliReport['paryantarDasha']['response']['paryantardasha_order']);

        let currentMahaIndex = null;
        let currentMahaName = null;
        let currentAntarIndex = null;
        let currentAntarName = null;

        /* ================== CREATE ANTAR DASHA TABLE ================== */
        function showAntarDasha(mahaIndex, mahaName) {
            currentMahaIndex = mahaIndex;
            currentMahaName = mahaName;
            currentAntarIndex = null;
            currentAntarName = null;

            // Update breadcrumb
            updateBreadcrumb('antar', mahaName);

            // Update title
            document.getElementById('currentTitle').textContent = 'Antar Dasha';

            // Replace table content
            const container = document.getElementById('dashaTableContainer');
            container.innerHTML = `
        <div class="table-users table-theme shadow-pink mb-3" id="antarDashaTable">
            <table class="table table-bordered border-pink font-14 mb-0">
                <thead class="font-13">
                    <tr class="bg-pink color-red font-weight-normal">
                        <th class="cellhead">AntarDasha</th>
                        <th class="cellhead">AntarDasha Order</th>
                        <th class="cellhead" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="antarDashaBody">
                    ${antarDashas[mahaIndex].map((antar, i) => `
                        <tr class="dasha-row antar-row" data-type="antar" data-index="${i}" style="cursor:pointer">
                            <td>${antar}</td>
                            <td>${antarOrders[mahaIndex][i]}</td>
                            <td class="text-center">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        /* ================== CREATE PARYANTAR DASHA TABLE ================== */
        function showParyantarDasha(antarIndex, antarName) {
            currentAntarIndex = antarIndex;
            currentAntarName = antarName;

            // Update breadcrumb
            updateBreadcrumb('paryantar', antarName);

            // Update title
            document.getElementById('currentTitle').textContent = 'Paryantar Dasha';

            // Replace table content
            const container = document.getElementById('dashaTableContainer');
            container.innerHTML = `
        <div class="table-users table-theme shadow-pink mb-3" id="paryantarDashaTable">
            <table class="table table-bordered border-pink font-14 mb-0">
                <thead class="font-13">
                    <tr class="bg-pink color-red font-weight-normal">
                        <th class="cellhead">Paryantar Dasha</th>
                        <th class="cellhead">Paryantar Dasha Order</th>
                        <th class="cellhead" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    ${paryDashas[currentMahaIndex][antarIndex].map((pary, i) => `
                        <tr class="dasha-row">
                            <td>${pary}</td>
                            <td>${paryOrders[currentMahaIndex][antarIndex][i]}</td>
                            <td class="text-center">
                                <i class="fas fa-minus text-muted"></i>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        /* ================== SHOW MAHADASHA TABLE ================== */
        function showMahaDasha() {
            currentMahaIndex = null;
            currentMahaName = null;
            currentAntarIndex = null;
            currentAntarName = null;

            // Update breadcrumb
            updateBreadcrumb('maha', null);

            // Update title
            document.getElementById('currentTitle').textContent = 'Mahadasha';

            // Restore original mahadasha table
            const container = document.getElementById('dashaTableContainer');
            container.innerHTML = `
        <div class="table-users table-theme shadow-pink mb-3" id="mahaDashaTable">
            <table class="table table-bordered border-pink font-14 mb-0">
                <thead class="font-13">
                    <tr class="bg-pink color-red font-weight-normal">
                        <th class="cellhead">MahaDasha</th>
                        <th class="cellhead">MahaDasha Order</th>
                        <th class="cellhead" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    ${@json($KundaliReport['mahaDasha']['response']['mahadasha']).map((mahadasha, index) => `
                        <tr class="dasha-row maha-row" data-type="maha" data-index="${index}" style="cursor:pointer">
                            <td>${mahadasha}</td>
                            <td>${@json($KundaliReport['mahaDasha']['response']['mahadasha_order'])[index]}</td>
                            <td class="text-center">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

            // Reattach event listeners
            setTimeout(() => {
                attachRowClickListeners();
            }, 10);
        }

        /* ================== UPDATE BREADCRUMB ================== */
        function updateBreadcrumb(currentType, parentName) {
            const breadcrumb = document.getElementById('dashaBreadcrumb');

            switch (currentType) {
                case 'maha':
                    breadcrumb.innerHTML = `
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 dasha-breadcrumb-item active"
                          data-type="maha"
                          onclick="navigateToDasha('maha')"
                          style="cursor:pointer">
                        <i class="fas fa-layer-group mr-1"></i> Mahadasha
                    </span>
                </li>
            `;
                    break;

                case 'antar':
                    breadcrumb.innerHTML = `
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 dasha-breadcrumb-item"
                          data-type="maha"
                          onclick="navigateToDasha('maha')"
                          style="cursor:pointer">
                        <i class="fas fa-layer-group mr-1"></i> Mahadasha
                    </span>
                </li>
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 dasha-breadcrumb-item active"
                          style="cursor:default">
                        <i class="fas fa-star mr-1"></i> ${parentName}
                    </span>
                </li>
            `;
                    break;

                case 'paryantar':
                    breadcrumb.innerHTML = `
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 dasha-breadcrumb-item"
                          data-type="maha"
                          onclick="navigateToDasha('maha')"
                          style="cursor:pointer">
                        <i class="fas fa-layer-group mr-1"></i> Mahadasha
                    </span>
                </li>
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 dasha-breadcrumb-item"
                          data-type="antar"
                          onclick="navigateToDasha('antar')"
                          style="cursor:pointer">
                        <i class="fas fa-star mr-1"></i> ${currentMahaName}
                    </span>
                </li>
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 dasha-breadcrumb-item active"
                          style="cursor:default">
                        <i class="fas fa-asterisk mr-1"></i> ${parentName}
                    </span>
                </li>
            `;
                    break;
            }
        }

        /* ================== NAVIGATE VIA BREADCRUMB ================== */
        function navigateToDasha(type) {
            switch (type) {
                case 'maha':
                    showMahaDasha();
                    break;
                case 'antar':
                    if (currentMahaIndex !== null && currentMahaName !== null) {
                        showAntarDasha(currentMahaIndex, currentMahaName);
                    }
                    break;
            }
        }

        /* ================== ATTACH ROW CLICK LISTENERS ================== */
        function attachRowClickListeners() {
            // Use event delegation on the container
            const container = document.getElementById('dashaTableContainer');

            // Remove any existing listener and add new one
            container.onclick = null;
            container.onclick = function(event) {
                const row = event.target.closest('.dasha-row');
                if (!row) return;

                const type = row.dataset.type;
                const index = parseInt(row.dataset.index);

                if (type === 'maha') {
                    const mahaName = row.cells[0].textContent;
                    showAntarDasha(index, mahaName);
                } else if (type === 'antar') {
                    const antarName = row.cells[0].textContent;
                    showParyantarDasha(index, antarName);
                }
            };
        }

        /* ================== INITIAL SETUP ================== */
        document.addEventListener('DOMContentLoaded', function() {
            // Initial attachment of listeners
            attachRowClickListeners();

            // Initialize breadcrumb
            updateBreadcrumb('maha', null);
        });
    </script>
    <script>
        const yoginiSubDashas = @json($KundaliReport['yoginiDashaSub']['response']);

        let currentYoginiMainIndex = null;
        let currentYoginiMainName = null;
        let currentYoginiMainLord = null;

        /* ================== CREATE YOGINI SUB DASHA TABLE ================== */
        function showYoginiSubDasha(mainIndex, mainName, mainLord) {
            currentYoginiMainIndex = mainIndex;
            currentYoginiMainName = mainName;
            currentYoginiMainLord = mainLord;

            // Update breadcrumb
            updateYoginiBreadcrumb('sub', mainName);

            // Update title
            document.getElementById('yoginiCurrentTitle').textContent = `${mainName} Sub Dasha`;

            const subDashaData = yoginiSubDashas[mainIndex];

            // Replace table content
            const container = document.getElementById('yoginiDashaTableContainer');
            container.innerHTML = `
        <div class="table-users table-theme shadow-pink mb-3" id="yoginiSubDashaTable">
            <table class="table table-bordered border-pink font-14 mb-0">
                <thead class="font-13">
                    <tr class="bg-pink color-red font-weight-normal">
                        <th class="cellhead">Main Dasha</th>
                        <th class="cellhead">Main Dasha Lord</th>
                        <th class="cellhead">Start Date</th>
                        <th class="cellhead" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${subDashaData.main_dasha}</td>
                        <td>${subDashaData.main_dasha_lord}</td>
                        <td>${formatYoginiDate(subDashaData.sub_dasha_start_date)}</td>
                        <td class="text-center">
                            <i class="fas fa-minus text-muted"></i>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <h5 class="font-16 mb-2 color-red">Sub Dashas</h5>
                <table class="table table-bordered border-pink font-14 mb-0 mt-2">
                    <thead class="font-13">
                        <tr class="bg-light-pink color-red font-weight-normal">
                            <th class="cellhead">Sub Dasha</th>
                            <th class="cellhead">End Date</th>
                            <th class="cellhead" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        ${subDashaData.sub_dasha_list.map((subDasha, i) => `
                            <tr>
                                <td>${subDasha}</td>
                                <td>${formatYoginiDate(subDashaData.sub_dasha_end_dates[i])}</td>
                                <td class="text-center">
                                    <i class="fas fa-minus text-muted"></i>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
        }

        /* ================== SHOW YOGINI MAIN DASHA TABLE ================== */
        function showYoginiMainDasha() {
            currentYoginiMainIndex = null;
            currentYoginiMainName = null;
            currentYoginiMainLord = null;

            // Update breadcrumb
            updateYoginiBreadcrumb('main', null);

            // Update title
            document.getElementById('yoginiCurrentTitle').textContent = 'Yogini Dasha';

            // Restore original Yogini Dasha table
            const container = document.getElementById('yoginiDashaTableContainer');
            container.innerHTML = `
        <div class="table-users table-theme shadow-pink mb-3" id="yoginiMainDashaTable">
            <table class="table table-bordered border-pink font-14 mb-0">
                <thead class="font-13">
                    <tr class="bg-pink color-red font-weight-normal">
                        <th class="cellhead">Dasha</th>
                        <th class="cellhead">Dasha Lord</th>
                        <th class="cellhead">End Date</th>
                        <th class="cellhead" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    ${@json($KundaliReport['yoginiDashaMain']['response']['dasha_list']).map((dasha, index) => `
                        <tr class="yogini-row main-row"
                            data-type="main"
                            data-index="${index}"
                            style="cursor:pointer">
                            <td>${dasha}</td>
                            <td>${@json($KundaliReport['yoginiDashaMain']['response']['dasha_lord_list'])[index]}</td>
                            <td>${formatYoginiDate(@json($KundaliReport['yoginiDashaMain']['response']['dasha_end_dates'])[index])}</td>
                            <td class="text-center">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

            // Reattach event listeners
            setTimeout(() => {
                attachYoginiRowClickListeners();
            }, 10);
        }

        /* ================== UPDATE YOGINI BREADCRUMB ================== */
        function updateYoginiBreadcrumb(currentType, parentName) {
            const breadcrumb = document.getElementById('yoginiDashaBreadcrumb');

            switch (currentType) {
                case 'main':
                    breadcrumb.innerHTML = `
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 yogini-breadcrumb-item active"
                          data-type="main"
                          onclick="navigateToYoginiDasha('main')"
                          style="cursor:pointer">
                        <i class="fas fa-layer-group mr-1"></i> Yogini Dasha
                    </span>
                </li>
            `;
                    break;

                case 'sub':
                    breadcrumb.innerHTML = `
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 yogini-breadcrumb-item"
                          data-type="main"
                          onclick="navigateToYoginiDasha('main')"
                          style="cursor:pointer">
                        <i class="fas fa-layer-group mr-1"></i> Yogini Dasha
                    </span>
                </li>
                <li class="breadcrumb-item d-flex align-items-center">
                    <span class="font-16 yogini-breadcrumb-item active"
                          style="cursor:default">
                        <i class="fas fa-star mr-1"></i> ${parentName}
                    </span>
                </li>
            `;
                    break;
            }
        }

        /* ================== NAVIGATE YOGINI VIA BREADCRUMB ================== */
        function navigateToYoginiDasha(type) {
            switch (type) {
                case 'main':
                    showYoginiMainDasha();
                    break;
                case 'sub':
                    if (currentYoginiMainIndex !== null && currentYoginiMainName !== null) {
                        showYoginiSubDasha(currentYoginiMainIndex, currentYoginiMainName, currentYoginiMainLord);
                    }
                    break;
            }
        }

        /* ================== ATTACH YOGINI ROW CLICK LISTENERS ================== */
        function attachYoginiRowClickListeners() {
            // Use event delegation on the container
            const container = document.getElementById('yoginiDashaTableContainer');

            // Remove any existing listener and add new one
            container.onclick = null;
            container.onclick = function(event) {
                const row = event.target.closest('.yogini-row');
                if (!row || row.dataset.type !== 'main') return;

                const index = parseInt(row.dataset.index);
                const mainName = row.cells[0].textContent;
                const mainLord = row.cells[1].textContent;

                showYoginiSubDasha(index, mainName, mainLord);
            };
        }

        /* ================== FORMAT YOGINI DATE ================== */
        function formatYoginiDate(dateString) {
            if (!dateString) return '';

            try {
                // Remove timezone info for parsing
                const cleanDateString = dateString.replace(/\(.*\)/, '').trim();
                const date = new Date(cleanDateString);

                // Format: Fri Jul 30 1987
                return date.toDateString();
            } catch (e) {
                return dateString;
            }
        }

        /* ================== INITIAL SETUP ================== */
        document.addEventListener('DOMContentLoaded', function() {
            // Initial attachment of listeners for Yogini Dasha
            attachYoginiRowClickListeners();

            // Initialize breadcrumb
            updateYoginiBreadcrumb('main', null);

            // Pre-format dates in the initial table
            document.querySelectorAll('#yoginiMainDashaTable td:nth-child(3)').forEach(td => {
                const originalDate = td.textContent;
                td.textContent = formatYoginiDate(originalDate);
            });
        });
    </script>

    @endsection
