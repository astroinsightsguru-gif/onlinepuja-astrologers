@extends('../layout/' . $layout)

@section('subhead')
    <title>Earning Report</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp

    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Earning Report</h2>
    @if ($totalRecords > 0)
        <a class="btn btn-primary shadow-md mr-2 d-inline mt-10 addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 d-inline mt-10 addbtn downloadcsv">CSV</a>
    @endif

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('earning-report',['id' => request()->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input type="hidden" name="id" value="{{ request()->id }}">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('earning-report',['id' => request()->id]) }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>

            <div class="w-50 sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('earning-report' ,['id' => request()->id]) }}" method="GET" id="dropdownForm">
                    <input type="hidden" name="id" value="{{ request()->id }}">
                <select name="orderType" id="orderType" class="form-control  box mr-2 ml-5">
                    <option value="">Order Type</option>
                    <option value="chat" {{ request('orderType') == 'chat' ? 'selected' : '' }}>Chat</option>
                    <option value="call" {{ request('orderType') == 'call' ? 'selected' : '' }}>Call</option>
                    <option value="puja" {{ request('orderType') == 'puja' ? 'selected' : '' }}>Puja</option>
                    <option value="course" {{ request('orderType') == 'course' ? 'selected' : '' }}>Course</option>
                    <option value="report" {{ request('orderType') == 'report' ? 'selected' : '' }}>Report</option>
                    <option value="gift" {{ request('orderType') == 'gift' ? 'selected' : '' }}>Gift</option>
                </select>
            </form>
        </div>

            <!-- Separate Date Range Filter Form -->
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="{{ route('earning-report',['id' => request()->id]) }}" method="GET" enctype="multipart/form-data" id="filterForm">
                    <!-- From Date -->
                    <input type="hidden" name="id" value="{{ request()->id }}">

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


    <div class="grid grid-cols-12 gap-6">

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            </div>
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report -mt-2" aria-label="earning">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="text-center whitespace-nowrap">User</th>
                        <th class="text-center whitespace-nowrap">Order Type</th>
                        <th class="text-center whitespace-nowrap">Order Amount</th>
                        <th class="text-center whitespace-nowrap">Total Min</th>
                        <th class="text-center whitespace-nowrap">Charge</th>
                        <th class="text-center whitespace-nowrap">Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($astrologerEarning as $earning)

                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>

                            <td class="text-center">
                                {{ $earning->userName }}
                            </td>
                            <td class="text-center">{{ $earning->orderType }}</td>

                            @if($earning->orderType=='course')
                            <td class="text-center">
                               (-) {{ $currency->value }}{{ number_format($earning->totalPayable,2) }}
                            </td>
                            @else
                            <td class="text-center">
                              (+) {{ $currency->value }}{{ number_format($earning->totalPayable,2) }}
                            </td>
                            @endif


                            <td class="text-center">
                                {{ $earning->totalMin??'--' }}
                            </td>
                            @if($earning->orderType=='report' || $earning->orderType=='puja' || $earning->orderType=='course')
                            <td class="text-center">
                                {{ $currency->value }}{{ number_format($earning->totalPayable,2)??'--' }}
                            </td>
                            @else
                            <td class="text-center">
                                {{ $currency->value }}{{ number_format($earning->charge,2)??'--' }}
                            </td>
                            @endif

                            <td class="text-center">
                                {{ date('d-m-Y', strtotime($earning->created_at)) ? date('d-m-Y h:i a', strtotime($earning->created_at)) : '--' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('earning-report', ['page' => $page - 1, 'id' => request()->id]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('earning-report', ['page' => $i + 1, 'id' => request()->id]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('earning-report', ['page' => $page + 1, 'id' => request()->id]) }}">
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
        var id = "{{ Js::from($astrologerId) }}";
        jQuery(function() {
            jQuery('.printpdf').click(function(e) {
                e.preventDefault();
                spinner.show();
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ route('printAstrologerEarning') }}",
                    data: {
                        "id": id
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "astrologerEarning.pdf";
                            link.click();
                            spinner.hide();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
            jQuery('.downloadcsv').click(function(e) {
                e.preventDefault();
                spinner.show();
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ route('exportAstrologerEarningCSV') }}",
                    data: {
                        "id": id
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "astrologerEarning.csv";
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
        window.location.href = "{{ route('earning-report',['id' => request()->id]) }}"; // Redirect to remove query parameters
    });

    document.getElementById('orderType').addEventListener('change', function() {
        document.getElementById('dropdownForm').submit();

    });
    </script>
@endsection
