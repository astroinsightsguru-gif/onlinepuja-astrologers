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
<h2 class="intro-y text-lg font-medium mt-10 d-inline">Recharge History
</h2>
            <form class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv"
                  id="downloadReportForm"
                  action="{{ route('downloadwallethistorycsv') }}"
                  method="GET">
                <input type="hidden" name="searchString" value="{{ request('searchString') }}">
                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                <input type="hidden" name="paymentMethod" value="{{ request('paymentMethod') }}">
                <button type="submit" class="">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    CSV Report
                    @if(request('searchString'))
                        for "{{ request('searchString') }}"
                    @endif
                </button>
            </form>



            <form class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv"
                  id="downloadReportPdfForm"
                  action="{{ route('downloadwallethistorypdf') }}"
                  method="GET">
            
                <input type="hidden" name="searchString" value="{{ request('searchString') }}">
                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                
                <button type="submit">
                    <i class="fa fa-file-pdf-o"></i>&nbsp; PDF Report
                    @if(request('searchString'))
                        for "{{ request('searchString') }}"
                    @endif
                </button>
            </form>



<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0" style="display: ruby;">
            <form action="{{ route('walletHistory') }}" method="GET" enctype="multipart/form-data">
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
            <form action="{{ route('walletHistory') }}" method="GET" id="dropdownForm">
                <select name="paymentMethod" id="paymentMethod" class="form-control box mr-2 ml-5">
                    <option value="">Payment Method</option>
                    <option value="admin" {{ request('paymentMethod') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="razorpay" {{ request('paymentMethod') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                    <option value="refund" {{ request('paymentMethod') == 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </form>
        </div>
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
            <form action="{{ route('walletHistory') }}" method="GET" enctype="multipart/form-data" id="filterForm">
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
                        <th class="whitespace-nowrap">NAME</th>
						<th class="whitespace-nowrap">ContactNo</th>
                        <th class="whitespace-nowrap">Amount</th>
                        <th class="whitespace-nowrap">GST</th>
                        <th class="whitespace-nowrap">Total Amount</th>
                        <th class="whitespace-nowrap">Date</th>
                        <th class="whitespace-nowrap">Payment Method</th>
                        <th class="whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                
                    @foreach ($wallet as $request)
                        @php
                            $normalAmount = $request->amount;
                            $gstAmount = ($normalAmount * $gst) / 100;
                            $totalAmount = $normalAmount + $gstAmount;
                        @endphp
                
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                
                            <td>
                                <div class="w-10 h-10 image-fit zoom-in">
                                    <img class="rounded-full cursor-pointer"
                                         src="{{ Str::startsWith($request->userProfile, ['http://','https://']) ? $request->userProfile : '/' . $request->userProfile }}"
                                         onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                         alt="Customer image"
                                         onclick="openImage('{{ $request->userProfile }}')" />
                                </div>
                            </td>
                
                            <td><div class="font-medium whitespace-nowrap">{{ $request->userName }}</div></td>
                
                            <td><div class="font-medium whitespace-nowrap">{{ $request->userContact ?? $request->userEmail }}</div></td>
                
                            <td><div class="font-medium whitespace-nowrap">(+)
                                {{ $currency->value }}{{ number_format($normalAmount, 2) }}
                            </div></td>
                
                            <td><div class="font-medium whitespace-nowrap">(+)
                                {{ $currency->value }}{{ number_format($gstAmount, 2) }}
                            </div></td>
                
                            <td><div class="font-medium whitespace-nowrap">(+)
                                {{ $currency->value }}{{ number_format($totalAmount, 2) }}
                            </div></td>
                
                            <td>{{ date('d-m-Y h:i a', strtotime($request->created_at)) }}</td>
                
                            <td>{{ ucwords($request->paymentMode) }}</td>
                
                            <td><div class="font-medium whitespace-nowrap">{{ ucfirst($request->paymentStatus) }}</div></td>
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
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
    @endif
    <div class="d-inline addbtn intro-y col-span-12">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination" id="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link"
                        href="{{ route('walletHistory', ['page' => $page - 1, 'searchString' => $searchString]) }}">
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
                            href="{{ route('walletHistory', ['page' => 1, 'searchString' => $searchString]) }}">1</a>
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
                            href="{{ route('walletHistory', ['page' => $i, 'searchString' => $searchString]) }}">{{ $i }}</a>
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
                            href="{{ route('walletHistory', ['page' => $totalPages, 'searchString' => $searchString]) }}">{{ $totalPages }}</a>
                    </li>
                @endif

                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link"
                        href="{{ route('walletHistory', ['page' => $page + 1, 'searchString' => $searchString]) }}">
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
    document.getElementById('paymentMethod').addEventListener('change', function() {
        document.getElementById('dropdownForm').submit();
    });
    document.getElementById('clearButton').addEventListener('click', function() {
        window.location.href = "{{ route('walletHistory') }}";
    });
</script>
    @endsection
