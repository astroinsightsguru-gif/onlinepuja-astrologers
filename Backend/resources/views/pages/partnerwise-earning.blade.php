@extends('../layout/' . $layout)

@section('subhead')
    <title>Partner Wise Earning</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')->where('name', 'currencySymbol')->select('value')->first();
    @endphp
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Partner Wise Earning</h2>
    @if ($totalRecords > 0)
        <a class="btn btn-primary shadow-md mr-2 d-inline mt-10 addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 d-inline mt-10 addbtn downloadcsv">CSV</a>
    @endif

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form method="GET" action="{{ route('partnerWiseEarning') }}" id="searchForm">
                    <div class="flex justify-end items-center">
                        <!-- Month Dropdown -->
                        <div class="intro-y col-span-6 md:col-span-6">
                        <select name="astrologerId" id="astrologerId" class="select2">
                            <option value="">Select {{ucfirst($professionTitle)}}</option>
                            @foreach($astrologers as $astrologer)
                                <option value="{{ $astrologer->id }}" {{ request('astrologerId') == $astrologer->id ? 'selected' : '' }}>
                                    {{ $astrologer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <!-- Filter Button -->
                        <button style="display:inline-flex;top: 4px; position: relative;"
                        class="btn btn-primary mr-2 mb-2 ml-2"><i data-lucide="filter" class="deletebtn w-4 h-4 mr-2"></i>Apply</button>

                        <button type="button" id="clearButton" class="btn btn-secondary">
                            <i data-lucide="x"  class="w-4 h-4 mr-1"></i> Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($totalRecords > 0)
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2" aria-label="customer-list">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="text-center whitespace-nowrap">{{ ucwords($professionTitle) }}</th>
                        <th class="text-center whitespace-nowrap">Total Earning</th>
                        <th class="text-center whitespace-nowrap">Chat Earning</th>
                        <th class="text-center whitespace-nowrap">Call Earning</th>
                        <th class="text-center whitespace-nowrap">Report Earning</th>
                        <th class="text-center whitespace-nowrap">Gift Earning</th>
                        <th class="text-center whitespace-nowrap">Puja Earning</th>
                        <th class="text-center whitespace-nowrap">Course Earning</th>
                        <th class="text-center whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                   @foreach ($partnerWiseEarning as $earning)
                   <tr class="intro-x">
                       <td>{{ ($page - 1) * 15 + ++$no }}</td>

                       <td class="text-center">
                           {{ $earning->astrologerName }}
                       </td>
                       <td class="text-center">{{ $currency->value }}
                           @if ($earning->totalEarning)
                               {{ number_format($earning->chatEarning + $earning->callEarning + $earning->reportEarning + $earning->giftEarning + $earning->pujaEarning + $earning->courseEarning,2) }}
                           @else
                               0
                           @endif

                       </td>
                       <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->chatEarning)
                               {{ number_format($earning->chatEarning,2) }}
                           @else
                               0
                           @endif
                       </td>
                       {{-- <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->aichatearning)
                               {{ $earning->aichatearning }}
                           @else
                               0
                           @endif
                       </td> --}}
                       <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->callEarning)
                               {{ number_format($earning->callEarning,2) }}
                           @else
                               0
                           @endif
                       </td>
                       <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->reportEarning)
                               {{ number_format($earning->reportEarning,2) }}
                           @else
                               0
                           @endif
                       </td>

                       <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->giftEarning)
                               {{ number_format($earning->giftEarning,2) }}
                           @else
                               0
                           @endif
                       </td>

                       <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->pujaEarning)
                               {{ number_format($earning->pujaEarning,2) }}
                           @else
                               0
                           @endif
                       </td>
                       <td class="text-center">
                           {{ $currency->value }}
                           @if ($earning->courseEarning)
                               {{ number_format($earning->courseEarning,2) }}
                           @else
                               0
                           @endif
                       </td>


                       <td class="table-report__action w-56">
                           <div class="flex justify-center items-center text-success">
                               <a id="editbtn"
                                   href="{{ route('earning-report', ['id' => $earning->astrologerId]) }}"
                                   class="flex items-center mr-3 " data-tw-target="#edit-modal"
                                   data-tw-toggle="modal"><i data-lucide="eye"
                                       class="editbtn w-4 h-4 mr-1"></i>View</a>
                           </div>
                       </td>
                   </tr>
               @endforeach
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
        <!-- BEGIN: Pagination -->
        @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
    @endif
    <div class="d-inline intro-y col-span-12 addbtn">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination" id="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link"
                        href="{{ route('partnerWiseEarning', ['page' => $page - 1]) }}">
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
                            href="{{ route('partnerWiseEarning', ['page' => $page - 1]) }}">1</a>
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
                            href="{{ route('partnerWiseEarning', ['page' => $page - 1]) }}">{{ $i }}</a>
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
                            href="{{ route('partnerWiseEarning', ['page' => $page - 1]) }}">{{ $totalPages }}</a>
                    </li>
                @endif

                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link"
                        href="{{ route('partnerWiseEarning', ['page' => $page - 1]) }}">
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
    <!-- END: Pagination -->
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  ></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> --}}

    <script type="text/javascript">
        var spinner = $('.loader');
        jQuery(function() {
            jQuery('.printpdf').click(function(e) {
                e.preventDefault();
                spinner.show();
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ route('printPartnerWisePdf') }}",
                    data: "",
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "partnerWiseEarning.pdf";
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
                    url: "{{ route('exportPartnerWiseCSV') }}",
                    data: "",
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            var blob = new Blob([data]);
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "partnerWiseEarning.csv";
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
        })
    </script>

<script>
    document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('searchForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('partnerWiseEarning') }}"; // Redirect to remove query parameters
    });

    $(document).ready(function() {
        jQuery('.select2').select2({
            allowClear: true,
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

</script>


@endsection
