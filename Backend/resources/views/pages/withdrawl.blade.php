@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
<style>
/* Small modal styles - adjust as needed */
.simple-modal { display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; }
.simple-modal.open { display: flex; }
.simple-modal .overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.45); }
.simple-modal .dialog { position: relative; background: #fff; max-width: 520px; width: 100%; border-radius: 8px; box-shadow: 0 6px 30px rgba(0,0,0,0.2); z-index: 2; padding: 0; overflow: hidden; }
.simple-modal .header, .simple-modal .footer { padding: 12px 16px; border-bottom: 1px solid #eee; }
.simple-modal .header { display:flex; justify-content:space-between; align-items:center; }
.simple-modal .body { padding: 16px; }
.simple-modal .footer { border-top: 1px solid #eee; border-bottom: none; text-align: right; }
.simple-modal .close-btn { background: transparent; border: none; font-size: 20px; cursor: pointer; }
.simple-modal textarea.form-control { width:100%; min-height:100px; padding:8px; border:1px solid #ccc; border-radius:4px; resize:vertical; }
.simple-modal .btn { padding:6px 12px; border-radius:4px; cursor:pointer; border: none; }
.simple-modal .btn-secondary { background:#6c757d; color:#fff; }
.simple-modal .btn-danger { background:#dc3545; color:#fff; }
</style>

<div class="loader"></div>
<h2 class="intro-y text-lg font-medium mt-10 d-inline">withdrawal - Requests
</h2>

<!-- <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a> -->

            <form class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv" id="downloadReportForm" action="{{ route('downloadTDSReport') }}" method="GET" >
                <input type="hidden" name="searchString" value="{{ request('searchString') }}">
                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                <button type="submit" class="">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    CSV Report
                    @if(request('searchString'))
                        for "{{ request('searchString') }}"
                    @endif
                </button>
            </form>

            <form class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv" id="downloadReportPdfForm" action="{{ route('downloadTDSReportPDF') }}" method="GET" style="display:inline;">
                <input type="hidden" name="searchString" value="{{ request('searchString') }}">
                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                <button type="submit" class="">
                    <i class="fa fa-file-pdf-o"></i>&nbsp; 
                    PDF Report
                    @if(request('searchString'))
                        for "{{ request('searchString') }}"
                    @endif
                </button>
            </form>


<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0" style="display: ruby;">
            <form action="{{ route('withdrawalRequests') }}" method="GET" enctype="multipart/form-data">
                <div class="w-56 relative text-slate-500" style="display:inline-block">
                    <input value="{{ request('searchString') }}" type="text" class="form-control w-56 box pr-10"
                           placeholder="Search by user name..." id="searchString" name="searchString">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
                <button class="btn btn-primary shadow-md mr-2">Search</button>

                <a id="editbtn" href="javascript:;" onclick="" value="" class="btn btn-primary shadow-md mr-2" data-tw-target="#report-confirmation-modal" data-tw-toggle="modal">Report</a>
            </form>
        </div>
        <div style="width: 170px" class="w-50 sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            <form action="{{ route('withdrawalRequests') }}" method="GET" id="dropdownForm">
                <select name="orderType" id="orderType" class="form-control box mr-2 ml-5">
                    <option value="">Withdraw Type</option>
                    <option value="pending" {{ request('orderType') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approve" {{ request('orderType') == 'approve' ? 'selected' : '' }}>Approved</option>
                    <option value="reject" {{ request('orderType') == 'reject' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
            <form action="{{ route('withdrawalRequests') }}" method="GET" enctype="multipart/form-data" id="filterForm">
                <label for="to_date" class="font-bold">From :</label>
                <input style="width: 150px" type="date" name="from_date" value="{{ request('from_date') }}" class="form-control w-56 box mr-2">
                <label for="to_date" class="font-bold">To :</label>
                <input style="width: 150px" type="date" name="to_date" value="{{ request('to_date') }}" class="form-control w-56 box mr-2">

                <button class="btn btn-primary shadow-md mr-2">Filter</button>
                <button type="button" id="clearButton" class="btn btn-secondary">
                    <i data-lucide="x" class="w-4 h-4 mr-1"></i> Clear
                </button>
            </form>
        </div>
    </div>
</div>
    @if ($totalRecords > 0)
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2 " aria-label="withdraw">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">Profile</th>
                        <th class="whitespace-nowrap">Pancard</th>
                        <th class="whitespace-nowrap">NAME</th>
                        <th class="whitespace-nowrap">Amount</th>
                        <th class="whitespace-nowrap">TDS</th>
                        <th class="whitespace-nowrap">Payable</th>
                        <th class="whitespace-nowrap">Request Date</th>
                        <th class="whitespace-nowrap">Payment Method</th>
                        <th class="whitespace-nowrap">Detail</th>
                        <th class="whitespace-nowrap">Status</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($withdrawlRequest as $request)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="w-10 h-10 image-fit zoom-in">
                                    <img class="rounded-full cursor-pointer" src="{{ Str::startsWith($request->profileImage, ['http://','https://']) ? $request->profileImage : '/' . $request->profileImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $request->profileImage }}')" />
                                </div>
                            </td>
                            <td>
                                <div class="w-10 h-10 image-fit zoom-in">
                                    <img class="rounded-full cursor-pointer" src="{{ Str::startsWith($request->pan_card, ['http://','https://']) ? $request->pan_card : '/' . $request->pan_card }}" onerror="this.onerror=null;this.src='/public/storage/images/pancard.jpg';" alt="Customer image" onclick="openImage('{{ $request->pan_card }}')" />
                                </div>
                                    </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $request->name }} -
                                    {{ $request->contactNo }}</div>
                            </td>
                             <td>
                                <div class="font-medium whitespace-nowrap">@if($request->country=='India') (-) ₹@else$@endif{{ $request->withdrawAmount }}</div>
                            </td>
                            <td>₹{{ $request->tds_pay_amount }}</td>
                            <td>₹{{ $request->pay_amount }}</td>
                            <td>
                                {{ $request->created_at ? date('d-m-Y h:i a', strtotime($request->created_at)) : '--' }}
                            </td>
                            <td>
                                {{$request->method_name}}
                            </td>
                            <td>
                                @if ($request->method_id == 2)
                                    UPI:{{ $request->upiId }}
                                @elseif($request->method_id == 3)
                                    --
                                @else
                                    A/C NO:{{ $request->accountNumber }}<br>
                                    IFSC:{{ $request->ifscCode }}<br>
                                    A/C Holder:{{ $request->accountHolderName }}
                                @endif

                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap @if($request->status =='Cancelled') text-danger @endif">{{ $request->status }}</div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    @if ($request->status != 'Released' && $request->status != 'Cancelled')
                                        <a id="editbtn" href="javascript:;" onclick="delbtn({{ $request->id }})"
                                            value="{{ $request->id }}" class="flex items-center"
                                            data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal"><i
                                                data-lucide="share" class="editbtn w-4 h-4 mr-1"></i>Release</a>

                                    <a id="editbtn" href="javascript:;" onclick="delbtn({{ $request->id }})"
                                        value="{{ $request->id }}" class="flex items-center text-danger ml-2"
                                        data-tw-target="#cancel-confirmation-modal" data-tw-toggle="modal"><i
                                            data-lucide="trash" class="editbtn w-4 h-4 mr-1"></i>Cancel</a>
                                    @endif

                                </div>
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
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>

        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('withdrawalRequests', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('withdrawalRequests', ['page' => $i + 1, 'searchString' => $searchString]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('withdrawalRequests', ['page' => $page + 1, 'searchString' => $searchString]) }}">
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
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i class="w-16 h-16 text-danger mx-auto mt-3"></i>

                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 py-4">Do you really want to Release This Amount?
                        </div>

                        <form action="{{ route('releaseAmount') }} " method="POST">
                            @csrf
                            <input type="hidden" id="del_id" name="del_id">
                            <div class="sm:grid grid-cols gap-2 mb-3">
                                <div class="input mt-2">
                                    <div>
                                        <textarea  id="note" required class="form-control" name="note" placeholder="Note (such as refernece number etc)" minlength="10" required></textarea>
                                        <div class="text-danger print-note-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 pb-8 text-center">
                                <button type="button" data-tw-dismiss="modal"
                                    class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                <button data-tw-dismiss="modal" class="btn btn-primary w-24">Release</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div id="cancel-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i class="w-16 h-16 text-danger mx-auto mt-3"></i>

                            <div class="text-3xl mt-5">Are you sure?</div>
                            <div class="text-slate-500 py-4">Do you really want to Cancel This Amount?
                            </div>

                            <form action="{{ route('cancelWithdrawAmount') }} " method="POST">
                                @csrf
                                <input type="hidden" id="del_id" name="del_id">
                                <div class="px-5 pb-8 text-center">
                                    <button type="button" data-tw-dismiss="modal"
                                        class="btn btn-outline-secondary w-24 mr-1">No</button>
                                    <button data-tw-dismiss="modal" class="btn btn-primary w-24">Yes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Report Modal -->
<div id="report-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <h3 class="text-2xl font-bold mb-4">
                        @if($searchString)
                            TDS Report for: "{{ $searchString }}"
                        @else
                            Overall TDS Report (All Astrologers)
                        @endif
                    </h3>

                    <table class="table table-bordered text-left w-full">
                        <tr>
                            <th>Total Withdraw Amount</th>
                            <td>
                                @if($tdsReport && $tdsReport->total_withdraw)
                                    ₹{{ number_format($tdsReport->total_withdraw, 2) }}
                                @else
                                    ₹0.00
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total TDS Deducted</th>
                            <td>
                                @if($tdsReport && $tdsReport->total_tds)
                                    ₹{{ number_format($tdsReport->total_tds, 2) }}
                                @else
                                    ₹0.00
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Payable Amount</th>
                            <td>
                                @if($tdsReport && $tdsReport->total_payable)
                                    ₹{{ number_format($tdsReport->total_payable, 2) }}
                                @else
                                    ₹0.00
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="mt-5 text-slate-500">
                        @if($searchString)
                            Showing TDS summary for astrologer "{{ $searchString }}"
                        @else
                            Showing combined TDS summary for all astrologers
                        @endif
                    </div>

                    <div class="px-5 pb-8 text-center mt-5">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-primary w-24">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




    @endsection

    @section('script')
        <script type="text/javascript">
            function delbtn($id, $name) {
                var id = $id;
                $did = id;

                $('#del_id').val($did);
                $('#id').val($id);
            }
        </script>
        <script>
            $(window).on('load', function() {
                $('.loader').hide();
            })
        </script>
        <script>
document.getElementById('orderType').addEventListener('change', function() {
    document.getElementById('dropdownForm').submit();
});
document.getElementById('clearButton').addEventListener('click', function() {
    window.location.href = "{{ route('withdrawalRequests') }}";
});
</script>
    @endsection
