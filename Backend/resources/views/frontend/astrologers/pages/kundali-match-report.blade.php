@extends('frontend.astrologers.layout.master')
@section('content')

@if($KundaliMatching['recordList']['status']==400 || $KundaliMatching['recordList']['status']==402)
<div class="container">
    <div class="row justify-content-center py-3 mt-5 mb-5">
        <h3 class=" mt-3">No Kundali Match Found</h3>
    </div>
</div>
@else
<div class="ds-head-populararticle bg-white cat-pages mb-2">
    <div class="container">
        <div class="row pt-3">
            <div class="col-sm-12 mt-4">
                <div class="row">
                    <div class="col-12 mb-4">
                        <h2 class="cat-heading font-26 font-weight-bold">Free Kundali Matching Report</h2>
                    </div>
                    <div class="col-12">
                        <div class="shadow-pink rounded-10 p-3">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="table-theme mb-3 shadow-pink">
                                        <table class="table table-bordered border-pink font-14 mb-0">
                                            <thead>
                                                <tr class="bg-pink color-red text-center">
                                                    <th colspan="2">Basic Details for Boy</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Name
                                                    </td>
                                                    <td>
                                                        {{$kundalimale->name}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Birth Date
                                                    </td>
                                                    <td>
                                                        {{ date('d-m-Y', strtotime($kundalimale->birthDate)) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Birth Time
                                                    </td>
                                                    <td>
                                                        <div>{{$kundalimale->birthTime}}</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Birth Place
                                                    </td>
                                                    <td>
                                                        {{$kundalimale->birthPlace}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Janam Rashi
                                                    </td>
                                                    <td>
                                                        @if(isset($KundaliMatching['recordList']['response']['bhakoot']))
                                                        {{$KundaliMatching['recordList']['response']['bhakoot']['boy_rasi_name']}}
                                                        @else
                                                        {{$KundaliMatching['recordList']['response']['vasya']['boy_rasi']}}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="table-theme mb-3 shadow-pink">
                                        <table class="table table-bordered border-pink font-14 mb-0">
                                            <thead>
                                                <tr class="bg-pink color-red text-center">
                                                    <th colspan="2">Basic Details for Girl</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Name
                                                    </td>
                                                    <td>
                                                        {{$kundalifemale->name}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Birth Date
                                                    </td>
                                                    <td>
                                                        {{ date('d-m-Y', strtotime($kundalifemale->birthDate)) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Birth Time
                                                    </td>
                                                    <td>
                                                        <div> {{$kundalifemale->birthTime}}</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Birth Place
                                                    </td>
                                                    <td>
                                                        {{$kundalifemale->birthPlace}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-semi-bold">
                                                        Janam Rashi
                                                    </td>
                                                    <td>
                                                        @if(isset($KundaliMatching['recordList']['response']['bhakoot']))
                                                        {{$KundaliMatching['recordList']['response']['bhakoot']['girl_rasi_name']}}
                                                        @else
                                                        {{$KundaliMatching['recordList']['response']['vasya']['girl_rasi']}}
                                                        @endif
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>



                            <div class="container py-0 mb-4 shadow-pink rounded-10-top px-0 gun-milan-table">
                                <div class="col-12 bg-pink color-red text-center font-weight-semi-bold py-1 px-3 rounded-10-top">
                                    Guna Milan Details and Matched Points
                                </div>
                                <div class="table-theme table-tiles rounded-0 mb-3 overflow-auto">
                                    <table class="table table-bordered border-pink font-14 mb-0" role="grid">
                                        <thead>
                                            <tr class="">
                                                <th width="15%">Guna</th>
                                                <th width="15%">Boy</th>
                                                <th width="15%">Girl</th>
                                                <th width="15%">Max Points</th>
                                                <th width="18%">Matched Points</th>
                                                <th width="22%">Area of Interest</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($KundaliMatching['recordList']['response']['varna']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Vasya" class="clickable matchtablealt1"  style="cursor:pointer;">
                                                <td>
                                                    <span>Varna</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['varna']['boy_varna']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['varna']['girl_varna']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['varna']['full_score']}}

                                                </td>
                                                <td>
                                                    <span> {{$KundaliMatching['recordList']['response']['varna']['varna']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace">{{$KundaliMatching['recordList']['response']['varna']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif


                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Vasya" class="clickable matchtablealt1"  style="cursor:pointer;">
                                                <td>
                                                    <span>Vasya</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    @if(isset($KundaliMatching['recordList']['response']['vasya']['boy_vasya']))
                                                    {{$KundaliMatching['recordList']['response']['vasya']['boy_vasya']}}
                                                    @else
                                                    {{$KundaliMatching['recordList']['response']['vasya']['boy_rasi']}}
                                                    @endif
                                                </td>
                                                <td class="Area_of_List">
                                                    @if(isset($KundaliMatching['recordList']['response']['vasya']['girl_vasya']))
                                                    {{$KundaliMatching['recordList']['response']['vasya']['girl_vasya']}}
                                                    @else
                                                    {{$KundaliMatching['recordList']['response']['vasya']['girl_rasi']}}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['vasya']['full_score']}}

                                                </td>
                                                <td>
                                                    <span> {{$KundaliMatching['recordList']['response']['vasya']['vasya']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace">  {{$KundaliMatching['recordList']['response']['vasya']['description']}}</span>

                                                </td>
                                            </tr>

                                            @if(isset($KundaliMatching['recordList']['response']['tara']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Tara" class=" clickable matchtablealt" style="cursor:pointer;">
                                                <td>
                                                    <span>Tara</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['tara']['boy_tara']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['tara']['girl_tara']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['tara']['full_score']}}


                                                </td>
                                                <td>
                                                    <span> {{$KundaliMatching['recordList']['response']['tara']['tara']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace">{{$KundaliMatching['recordList']['response']['tara']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Yoni" class="clickable matchtablealt1"  style="cursor:pointer;">
                                                <td>
                                                    <span>Yoni</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['yoni']['boy_yoni']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['yoni']['girl_yoni']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['yoni']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>{{$KundaliMatching['recordList']['response']['yoni']['yoni']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace">{{$KundaliMatching['recordList']['response']['yoni']['description']}}</span>

                                                </td>
                                            </tr>
                                            @if(isset($KundaliMatching['recordList']['response']['grahamaitri']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Maitri" class=" clickable matchtablealt" style="cursor:pointer;">
                                                <td>
                                                    <span>Maitri</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['grahamaitri']['boy_lord']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['grahamaitri']['girl_lord']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['grahamaitri']['full_score']}}

                                                </td>
                                                <td>
                                                    <span> {{$KundaliMatching['recordList']['response']['grahamaitri']['grahamaitri']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['grahamaitri']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Gana" class="clickable matchtablealt1"  style="cursor:pointer;">
                                                <td>
                                                    <span>Gana</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['gana']['boy_gana']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['gana']['girl_gana']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['gana']['full_score']}}

                                                </td>
                                                <td>
                                                    <span> {{$KundaliMatching['recordList']['response']['gana']['gana']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace">Nature</span>

                                                </td>
                                            </tr>
                                            @if(isset($KundaliMatching['recordList']['response']['bhakoot']))
                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Bhakuta" class=" clickable matchtablealt"  style="cursor:pointer;">
                                                <td>
                                                    <span>Bhakuta</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['bhakoot']['boy_rasi_name']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['bhakoot']['girl_rasi_name']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['bhakoot']['full_score']}}


                                                </td>
                                                <td>
                                                    <span>{{$KundaliMatching['recordList']['response']['bhakoot']['bhakoot']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['bhakoot']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            @if(isset($KundaliMatching['recordList']['response']['nadi']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Nadi" class="clickable matchtablealt1" style="cursor:pointer;">
                                                <td>
                                                    <span>Nadi</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['nadi']['boy_nadi']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['nadi']['girl_nadi']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['nadi']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['nadi']['nadi']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace">  {{$KundaliMatching['recordList']['response']['nadi']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            @if(isset($KundaliMatching['recordList']['response']['dina']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Nadi" class="clickable matchtablealt1"  style="cursor:pointer;">
                                                <td>
                                                    <span>Dina</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['dina']['boy_star']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['dina']['girl_star']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['dina']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['dina']['dina']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['dina']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            @if(isset($KundaliMatching['recordList']['response']['mahendra']))
                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Nadi" class="clickable matchtablealt1" style="cursor:pointer;">
                                                <td>
                                                    <span>Mahendra</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['mahendra']['boy_star']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['mahendra']['girl_star']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['mahendra']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['mahendra']['mahendra']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['mahendra']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            @if(isset($KundaliMatching['recordList']['response']['sthree']))
                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Nadi" class="clickable matchtablealt1"  style="cursor:pointer;">
                                                <td>
                                                    <span>Sthree</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['sthree']['boy_star']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['sthree']['girl_star']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['sthree']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['sthree']['sthree']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['sthree']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif


                                            @if(isset($KundaliMatching['recordList']['response']['rasi']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Nadi" class="clickable matchtablealt1" style="cursor:pointer;">
                                                <td>
                                                    <span>Rasi</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['rasi']['boy_rasi']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['rasi']['girl_rasi']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['rasi']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['rasi']['rasi']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['rasi']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif


                                            @if(isset($KundaliMatching['recordList']['response']['rasiathi']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Nadi" class="clickable matchtablealt1" style="cursor:pointer;">
                                                <td>
                                                    <span>Rasiathi</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['rasiathi']['boy_lord']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['rasiathi']['girl_lord']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['rasiathi']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['rasiathi']['rasiathi']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['rasiathi']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            @if(isset($KundaliMatching['recordList']['response']['rajju']))
                                            <tr title="Click to see details" data-toggle="collapse"  data-target="#Nadi" class="clickable matchtablealt1" style="cursor:pointer;">
                                                <td>
                                                    <span>Rajju</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['rajju']['boy_rajju']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['rajju']['girl_rajju']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['rajju']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['rajju']['rajju']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['rasiathi']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif

                                            @if(isset($KundaliMatching['recordList']['response']['vedha']))
                                            <tr title="Click to see details" data-toggle="collapse" data-target="#Nadi" class="clickable matchtablealt1" style="cursor:pointer;">
                                                <td>
                                                    <span>Vedha</span>
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['vedha']['boy_star']}}
                                                </td>
                                                <td class="Area_of_List">
                                                    {{$KundaliMatching['recordList']['response']['vedha']['boy_star']}}
                                                </td>
                                                <td>
                                                    {{$KundaliMatching['recordList']['response']['vedha']['full_score']}}

                                                </td>
                                                <td>
                                                    <span>  {{$KundaliMatching['recordList']['response']['vedha']['vedha']}}</span>

                                                </td>
                                                <td>

                                                    <span class="whitespace"> {{$KundaliMatching['recordList']['response']['vedha']['description']}}</span>

                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 col-lg-3 text-center mb-3">
                                    <div class="border border-pink shadow-pink-down rounded-10 p-3 h-100">
                                        <span>
                                            <img src="{{asset('public/frontend/kundaliimages/mars.svg')}}" alt="">
                                        </span>
                                        <p class="mb-0 mt-2">Boy Manglik Report</p>
                                        <p class="color-red mb-0"><strong>{{$KundaliMatching['boyManaglikRpt']['response']['score']}} out of 100</strong></p>

                                    </div>
                                </div>
                                <div class="col-6 col-lg-3 text-center mb-3">
                                    <div class="border border-pink shadow-pink-down rounded-10 p-3 h-100">
                                        <span>
                                            <img src="{{asset('public/frontend/kundaliimages/mars.svg')}}" alt="">
                                        </span>
                                        <p class="mb-0 mt-2">Kundali Match Points</p>
                                        <p class="color-red mb-0"><strong>{{$KundaliMatching['recordList']['response']['score']}} out of 36</strong></p>

                                    </div>
                                </div>
                                <div class="col-6 col-lg-3 text-center mb-3">
                                    <div class="border border-pink shadow-pink-down rounded-10 p-3 h-100">
                                        <span>
                                            <img src="{{asset('public/frontend/kundaliimages/mars.svg')}}" alt="">
                                        </span>
                                        <p class="mb-0 mt-2">Girl Manglik Report</p>
                                        <p class="color-red mb-0"><strong>{{$KundaliMatching['girlMangalikRpt']['response']['score']}} out of 100</strong></p>

                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-12 mt-3">
                                    <h2 class="font-16 font-weight-bold">Boy Manglik Report</h2>
                                    <div class="mangaldosh">
                                        <p>Factors : {{ implode(', ', $KundaliMatching['boyManaglikRpt']['response']['factors']) }}</p>
                                        <p>Aspects : {{ implode(', ', $KundaliMatching['boyManaglikRpt']['response']['aspects']) }}</p>
                                        <p>Response : {{$KundaliMatching['boyManaglikRpt']['response']['bot_response']}}</p>
                                    </div>

                                    <h2 class="font-16 font-weight-bold">Girl Manglik Report</h2>
                                    <div class="mangaldosh">
                                        <p>Factors : {{ implode(', ', $KundaliMatching['girlMangalikRpt']['response']['factors']) }}</p>
                                        <p>Aspects : {{ implode(', ', $KundaliMatching['girlMangalikRpt']['response']['aspects']) }}</p>
                                        <p>Response : {{$KundaliMatching['girlMangalikRpt']['response']['bot_response']}}</p>
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
@endif
@endsection
