@extends('../layout/' . $layout)

@section('subhead')
    <title>Call History</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Call History</h2>
    @if ($totalRecords > 0)
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>
    @endif
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('callHistory') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('callHistory') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>

              <!-- Separate Date Range Filter Form -->
              <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="{{ route('callHistory') }}" method="GET" enctype="multipart/form-data" id="filterForm">
                    <!-- From Date -->
                    <label for="from_date" class="font-bold">From :</label>
                    <input type="date" name="from_date" value="{{ $from_date ?? '' }}" class="form-control w-56 box mr-2">

                    <!-- To Date -->
                    <label for="to_date" class="font-bold">To :</label>
                    <input type="date" name="to_date" value="{{ $to_date ?? '' }}" class="form-control w-56 box mr-2">

                    <button class="btn btn-primary shadow-md mr-2">Filter</button>
                    <button type="button" id="clearButton" class="btn btn-secondary">
                        <i data-lucide="x"  class="w-4 h-4 mr-1"></i> Clear
                    </button>
                </form>
              </div>
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report -mt-2" aria-label="call-history">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class=" whitespace-nowrap">User</th>
                        <th class=" whitespace-nowrap">Type</th>
                        <th class="text-center whitespace-nowrap">{{ucwords($professionTitle)}}</th>
                        <th class="text-center whitespace-nowrap">Call Rate</th>
                        <th class="text-center whitespace-nowrap">Call Time</th>
                        <th class="text-center whitespace-nowrap">Total Min</th>
                        <th class="text-center whitespace-nowrap">Deduction</th>
                        <th class="text-center whitespace-nowrap">Call Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($callHistory as $call)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>

                            <td>
                                {{ $call->userName }}
                            </td>
                            <td>
                                @if($call->call_type == config('constants.CALL_TYPE.AUDIO_CALL_TYPE'))
                                Audio
                                @else
                                Video
                                @endif
                            </td>
                            <td class="text-center">{{ $call->astrologerName }}</td>
                            <td class="text-center">
                                {{ number_format($call->callRate,2) }}
                            </td>
                            <td class="text-center">
                                {{ date('d-m-Y h:i a', strtotime($call->updated_at)) ? date('d-m-Y h:i a', strtotime($call->updated_at)) : '--' }}
                            </td>
                            <td class="text-center">
                                {{ $call->totalMin }}
                            </td>
                            <td class="text-center">
                                @if ($call->deduction)
                                    {{ $currency->value }} {{ number_format($call->deduction,2) }}
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $call->callStatus }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-inline text-slate-500 pagecount">
            Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries
        </div>
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('callHistory', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>

                    @php
                        $showPages = 15; // Number of pages to show at a time
                        $halfShowPages = floor($showPages / 2);
                        $startPage = max(1, $page - $halfShowPages);
                        $endPage = min($startPage + $showPages - 1, $totalPages);
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ route('callHistory', ['page' => 1, 'searchString' => $searchString]) }}">1</a>
                        </li>
                        @if ($startPage > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    @for ($i = $startPage; $i <= $endPage; $i++)
                        <li class="page-item {{ $page == $i ? 'active' : '' }}">
                            <a class="page-link"
                                href="{{ route('callHistory', ['page' => $i, 'searchString' => $searchString]) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($endPage < $totalPages)
                        @if ($endPage < $totalPages - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link"
                                href="{{ route('callHistory', ['page' => $totalPages, 'searchString' => $searchString]) }}">{{ $totalPages }}</a>
                        </li>
                    @endif

                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('callHistory', ['page' => $page + 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

    @else
        <div class="intro-y mt-5" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <!-- BEGIN: Delete Confirmation Modal -->
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        var spinner = $('.loader');
        jQuery(function() {
            jQuery('.printpdf').click(function(e) {
                e.preventDefault();
                spinner.show();
                var searchString = $("#searchString").val();
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ route('printPdf') }}",
                    data: {
                        "searchString": searchString,
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "callHistory.pdf";
                            link.click();
                            spinner.hide();
                            // location.reload();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
            jQuery('.downloadcsv').click(function(e) {
                e.preventDefault();
                spinner.show();
                var searchString = $("#searchString").val();
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ route('exportCSV') }}",
                    data: {
                        "searchString": searchString,
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "callHistory.csv";
                            link.click();
                            spinner.hide();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
        document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('callHistory') }}"; // Redirect to remove query parameters
    });
    </script>
@endsection
