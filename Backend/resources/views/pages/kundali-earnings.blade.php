@extends('../layout/' . $layout)

@section('subhead')
    <title>Kundali Report</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Kundali Report</h2>


    {{-- Replace the existing "Tabs UI" block with this --}}

<!-- Tabs UI (Improved) -->
<style>
    .kundali-tabs-wrapper {
        margin-top: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0;
        background: #1a1a2e;
        border-radius: 12px;
        padding: 5px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        width: fit-content;
        position: relative;
    }

    .kundali-tab-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 28px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none !important;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .kundali-tab-link:hover {
        color: rgba(255, 255, 255, 0.9);
        background: rgba(255, 255, 255, 0.06);
    }

    .kundali-tab-link.active {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(109, 40, 217, 0.4);
    }

    .kundali-tab-link.active::after {
        content: '✦';
        font-size: 7px;
        position: absolute;
        top: 6px;
        right: 9px;
        opacity: 0.55;
        color: #d8b4fe;
    }

    .kundali-tab-link {
        position: relative;
    }

    .tab-icon {
        width: 15px;
        height: 15px;
        opacity: 0.8;
        flex-shrink: 0;
    }

    .kundali-tab-link.active .tab-icon {
        opacity: 1;
    }
</style>

<div class="intro-y">
    <div class="kundali-tabs-wrapper">
        <a href="{{ route('kundaliearning', ['forMatch' => 0]) }}"
           class="kundali-tab-link {{ $forMatch == 0 ? 'active' : '' }}">
            <svg class="tab-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Kundali
        </a>
        <a href="{{ route('kundaliearning', ['forMatch' => 1]) }}"
           class="kundali-tab-link {{ $forMatch == 1 ? 'active' : '' }}">
            <svg class="tab-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Kundali Match
        </a>
    </div>
</div>
   

    <!-- Filter UI (Restored) -->
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('kundaliearning') }}" method="GET" enctype="multipart/form-data">
                    <input type="hidden" name="forMatch" value="{{ $forMatch }}">
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('kundaliearning', ['forMatch' => $forMatch]) }}"><i
                                    class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="x"></i></a>
                        @endif
                    </div>

                    <!-- From Date -->
                    <label for="from_date" class="ml-2 font-bold">From :</label>
                    <input type="date" name="from_date" value="{{ $from_date ?? '' }}" class="form-control w-48 box mr-2" style="display:inline-block">

                    <!-- To Date -->
                    <label for="to_date" class="font-bold">To :</label>
                    <input type="date" name="to_date" value="{{ $to_date ?? '' }}" class="form-control w-48 box mr-2" style="display:inline-block">

                    <button class="btn btn-primary shadow-md mr-2">Filter</button>
                    <a href="{{ route('kundaliearning', ['forMatch' => $forMatch]) }}" class="btn btn-secondary">
                        <i data-lucide="refresh-ccw" class="w-4 h-4 mr-1"></i> Clear
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Data List (Restored) -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report -mt-2" aria-label="call-history">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="text-center whitespace-nowrap">USER TYPE</th>
                        <th class="text-center whitespace-nowrap">USER</th>
                        <th class="text-center whitespace-nowrap">DATE</th>
                        <th class="text-center whitespace-nowrap">KUNDALI TYPE</th>
                        <th class="text-center whitespace-nowrap">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($kundaliEarnings as $req)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td class="text-center">{{$req->user_type}}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $req->userName }}</div>
                                <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $req->userContactNo }}</div>
                                <div class="mt-2 pt-2 border-t border-slate-200 dark:border-darkmode-400">
                                    <div class="text-primary font-medium">Generation For: {{ $req->kundaliName }}</div>
                                    <div class="text-slate-500 text-xs mt-1">
                                        <i data-lucide="calendar" class="w-3 h-3 inline-block"></i> 
                                        {{ date('d M, Y', strtotime($req->birthDate)) }} ({{ $req->birthTime }})
                                    </div>
                                    <div class="text-slate-500 text-xs mt-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 inline-block"></i> 
                                        {{ $req->birthPlace }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                {{ date('d-m-Y', strtotime($req->created_at)) ? date('d-m-Y h:i a', strtotime($req->created_at)) : '--' }}
                            </td>
                            <td class="text-center">{{ $req->kundaliType }}</td>
                            @if($req->pdf_link!=null)
                            <td class="text-center">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center mr-3 text-success" href="{{asset('public/'.$req->pdf_link)}}" target="_blank">
                                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>DownloadFile
                                    </a>
                                </div>
                            </td>
                            @else
                            <td class="text-center">No Pdf Available</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
        
        <div class="d-inline intro-y col-span-12 addbtn">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('kundaliearning', array_merge(request()->query(), ['page' => $page - 1, 'forMatch' => $forMatch])) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
    
                    @php
                        $showPages = 15;
                        $halfShowPages = floor($showPages / 2);
                        $startPage = max(1, $page - $halfShowPages);
                        $endPage = min($startPage + $showPages - 1, $totalPages);
                    @endphp
    
                    @if ($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ route('kundaliearning', array_merge(request()->query(), ['page' => 1, 'forMatch' => $forMatch])) }}">1</a></li>
                        @if ($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif
    
                    @for ($i = $startPage; $i <= $endPage; $i++)
                        <li class="page-item {{ $page == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ route('kundaliearning', array_merge(request()->query(), ['page' => $i, 'forMatch' => $forMatch])) }}">{{ $i }}</a>
                        </li>
                    @endfor
    
                    @if ($endPage < $totalPages)
                        @if ($endPage < $totalPages - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ route('kundaliearning', array_merge(request()->query(), ['page' => $totalPages, 'forMatch' => $forMatch])) }}">{{ $totalPages }}</a></li>
                    @endif
    
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('kundaliearning', array_merge(request()->query(), ['page' => $page + 1, 'forMatch' => $forMatch])) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
    </script>
@endsection
