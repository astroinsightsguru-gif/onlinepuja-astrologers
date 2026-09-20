@extends('../layout/' . $layout)

@section('subhead')
    <title>Order</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'currencySymbol')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Orders</h2>
    @if ($totalRecords > 0)
    <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
    <a class="downloadcsv btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn">CSV</a>
    @endif
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('orders') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('orders') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            </div>

             <!-- Separate Date Range Filter Form -->
             <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="{{ route('orders') }}" method="GET" enctype="multipart/form-data" id="filterForm">
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
            <table class="table table-report -mt-2" aria-label="order-request">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="text-center whitespace-nowrap">User</th>
                        <th class="text-center whitespace-nowrap">Product</th>
                        <th class="text-center whitespace-nowrap">Amount</th>
                        {{-- <th class="text-center whitespace-nowrap">GST</th> --}}
                        <th class="text-center whitespace-nowrap">Order Date</th>
                        <th class="text-center whitespace-nowrap">Order Status</th>
                        <th class="text-center whitespace-nowrap">Order Address</th>
                        <th class="text-center whitespace-nowrap">Change Status</th>
                        <th class="text-center whitespace-nowrap">Invoice Download</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($orderRequest as $request)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>

                            <td class="text-center">
                                {{ $request->userName }}
                            </td>
                            <td>
                        <div class="flex">
                            <div class="w-10 h-10 image-fit zoom-in">
                                <img class="rounded-full cursor-pointer" 
                                     src="{{ Str::startsWith($request->productImage, ['http://','https://']) ? $request->productImage : '/' . $request->productImage }}"
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                     alt="Customer image"
                                     onclick="openImage('{{ Str::startsWith($request->productImage, ['http://','https://']) ? $request->productImage : '/' . $request->productImage }}')" />
                            </div>
                        </div>
                    </td>

                            <td class="text-center">
                                {{ $currency->value }}{{ $request->payableAmount }}
                            </td>
                            {{-- <td class="text-center">
                                {{ $currency->value }}{{ number_format($request->gstAmount, 2, '.', ',') }}
                            </td> --}}
                            <td class="text-center">
                                {{ date('d-m-Y', strtotime($request->created_at)) ? date('d-m-Y h:i a', strtotime($request->created_at)) : '--' }}
                            </td>
                            <td class="text-center">
                                <span @class([
                                    'text-green' => $request->orderStatus == 'Confirmed',
                                    'text-red' => $request->orderStatus == 'Pending',
                                    'text-red' => $request->orderStatus == 'Cancelled',
                                ])>{{ $request->orderStatus }}</span>
                            </td>
                            <td class="text-center">
                                {{ $request->flatNo }},{{ $request->landmark }},{{ $request->city }},{{ $request->state }},{{ $request->country }}-{{ $request->pincode }}
                            </td>
                            <td>
                                @if ($request->orderStatus && $request->orderStatus != 'Cancelled' && $request->orderStatus != 'Delivered')
                                    <div class="dropdown ml-3">
                                        <div class="changeorder">
                                            <a class="dropdown-toggle flex items-center rounded-full  justify-center"
                                                href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                                                <span @class([
                                                    'text-green' => $request->orderStatus == 'Confirmed',
                                                    'text-red' => $request->orderStatus == 'Pending',
                                                    'text-red' => $request->orderStatus == 'Cancelled',
                                                ])>{{ $request->orderStatus }}</span> <i
                                                    data-lucide="chevron-down" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                        <div class="dropdown-menu w-40">
                                            <ul class="dropdown-content">
                                                <li>
                                                    <a href="javascript:;" class="text-green dropdown-item"
                                                        onclick="changeStatus({{ $request->id }},'Confirmed',{{ $request->userId }})"
                                                        data-tw-target="#status-change" data-tw-toggle="modal">
                                                        Confirmed
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="changeStatus({{ $request->id }},'Packed',{{ $request->userId }})"
                                                        data-tw-target="#status-change" data-tw-toggle="modal">
                                                        Packed
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="changeStatus({{ $request->id }},'Dispatched',{{ $request->userId }})"
                                                        data-tw-target="#status-change" data-tw-toggle="modal">
                                                        Dispatched
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="changeStatus({{ $request->id }},'Delivered',{{ $request->userId }})"
                                                        data-tw-target="#status-change" data-tw-toggle="modal">
                                                        Delivered
                                                    </a>
                                                </li>

                                                 @if ($request->orderStatus != 'Dispatched' && $request->orderStatus != 'Delivered')
                                                <li>
                                                    <a href="javascript:;" class="dropdown-item "
                                                       onclick="changeStatus({{ $request->id }},'Cancelled',{{ $request->userId }})"
                                                       data-tw-target="#status-change" data-tw-toggle="modal">
                                                       Cancelled
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a target="_blank" href="{{ route('order.invoice', ['id' => $request->id]) }}" class="btn btn-primary"><i data-lucide="download" style="width: 20px; height: 20px;"></i>
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Fullscreen Image Viewer -->
<div class="image-overlay" id="imageOverlay">
                <img src="your-image.jpg" id="popupImage" alt="Full Screen Image">
                <span class="closebtn" id="closeBtn">&times;</span>
            </div>
            <script>
            const overlay = document.getElementById('imageOverlay');
            const closeBtn = document.getElementById('closeBtn');
            function openImage(src) {
                document.getElementById('popupImage').src = src;
                overlay.classList.add('active');
            }
            closeBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
            });
            overlay.addEventListener('click', (e) => {
                if(e.target === overlay) {
                    overlay.classList.remove('active');
                }
            });
            </script>
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('orders', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('orders', ['page' => $i + 1, 'searchString' => $searchString]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('orders', ['page' => $page + 1, 'searchString' => $searchString]) }}">
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
    <div id="status-change" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want change Status!</div>
                    </div>
                    <form action="{{ route('changeOrder') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="userId" name="userId" >
                        <input type="hidden" id="status" name="status">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnActive">Yes,
                                Change it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function changeStatus($orderId, $status,$userId) {
            $("#id").val($orderId);
            $("#status").val($status);
             $("#userId").val($userId);

            document.getElementById('active').innerHTML = "You want to change status to " + $status;
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
        document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('orders') }}"; // Redirect to remove query parameters
    });
    </script>

<script type="text/javascript">
    var spinner = $('.loader');

    jQuery(function() {
        jQuery('.printpdf').click(function(e) {
            e.preventDefault();
            spinner.show();
            var searchString = $("#searchString").val();
            jQuery.ajax({
                type: 'GET',
                url: "{{ route('printOrder') }}",
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
                        link.download = "orderRequest.pdf";
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
            var searchString = $("#searchString").val();
            jQuery.ajax({
                type: 'GET',
                url: "{{ route('exportOrderRequestCSV') }}",
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
                        link.download = "orderRequest.csv";
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
@endsection
