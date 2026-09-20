@extends('../layout/' . $layout)

@section('subhead')
    <title>Tickets</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10">Tickets</h2>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <!-- Separate Date Range Filter Form -->
      <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
        <form action="{{ route('tickets') }}" method="GET" enctype="multipart/form-data" id="filterForm">
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

     
    @if (count($tickit) > 0)
        <div class="grid grid-cols-12 gap-6">
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">

                <table class="table table-report -mt-2" aria-label="ticket">
                    <thead class="sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">TICKET NO</th>
                            <th class="text-center">SUBJECT</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">CREATED DATE</th>
                            <th class="text-center">DESCRIPTION</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 0;
                        @endphp
                        @foreach ($tickit as $ticket)
                            <tr class="intro-x">
                                <td>{{ ++$no }}</td>
                                <td>
                                    <div class="font-medium text-center">{{ $ticket->ticketNumber }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-center">{{ $ticket->subject }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-center">{{ $ticket->ticketStatus }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-center">{{ $ticket->userName }} - {{ $ticket->contactNo }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-center">{{ date('d-m-Y h:i a', strtotime($ticket->created_at)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-medium text-center">{{ $ticket->description }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center items-center">
                                        <a id="editbtn" href={{ route('chats', $ticket->id) }}
                                            class="flex items-center mr-3 "><i data-lucide="message-square"
                                                class="editbtn w-4 h-4 mr-1"></i>Chat</a>
                                        @if ($ticket->ticketStatus != 'CLOSED')
                                            <a id="editbtn" href="javascript:;" onclick="closeTicket({{ $ticket->id }})"
                                                class="flex items-center text-danger mr-3" data-tw-target="#verified"
                                                data-tw-toggle="modal"><i data-lucide="cross"
                                                    class="editbtn w-4 h-4 mr-1"></i>Close
                                                Ticket</a>
                                            {{-- @if ($ticket->ticketStatus != 'PAUSED')
                                                <a id="pausebtn" href="javascript:;"
                                                    onclick="pauseTicket({{ $ticket->id }})"
                                                    class="flex items-center text-primary" data-tw-target="#paused"
                                                    data-tw-toggle="modal"><i data-lucide="pause"
                                                        class="editbtn w-4 h-4 mr-1"></i>Pause
                                                    Ticket</a>
                                            @endif --}}
                                        @endif
                                        <a id="editbtn" href="javascript:;" class="flex items-center mr-3 "
                                            data-tw-target="#reviewModel" data-tw-toggle="modal"
                                            onclick="getReview({{ $ticket->ticketReview }})"><i data-lucide="eye"
                                                class="editbtn w-4 h-4 mr-1"></i>Review</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
    @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
    @endif
    @if (count($tickit) > 0)
        <div class="d-inline addbtn intro-y col-span-12 ">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('tickets', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link" href="{{ route('tickets', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('tickets', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @endif
    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2">You want Close Ticket!</div>
                    </div>
                    <form method="POST" enctype="multipart/form-data" id="close-form">
                        @csrf
                        <input type="hidden" id="ticket_id" name="ticket_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3">Yes, Close it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- <div id="paused" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2">You want Pause Ticket!</div>
                    </div>
                    <form id="pause-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="ticket_id" name="ticket_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3">Yes, Pause it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div> --}}
    <div id="reviewModel" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Ticket Review</h2>
                </div>
                <div class="modal-body">
                    <h6 id="review"></h6>
                    <div class="px-5 text-right">
                        <a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function closeTicket($id) {
            var id = $id;
            $fid = id;
            $('#ticket_id').val($fid);
        }

        function pauseTicket($id) {
            var id = $id;
            $fid = id;
            $('#ticket_id').val($fid);
        }
        var spinner = $('.loader');
        jQuery(function() {
            jQuery('#close-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('closeTicket') }}",
                    data: new FormData(this),
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.reload();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
            jQuery('#pause-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('pauseTicket') }}",
                    data: new FormData(this),
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.reload();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
        });

        function filterData() {
            user_filter = $('#user_filter').val();
            $ajax({
                url: "{{ url('filter_user') }}?filter_user=" + user_filter,
                success: function(data) {

                }
            })
        }

        function searchData() {
            $("#user_filter").select2({
                placeholder: "Select a Name",
                allowClear: true
            });
        }

        function showEditor() {

        }

        function getReview($review) {
            $rev = $review && $review.length > 0 ? $review[0].review : "No Review Found"
            document.getElementById('review').innerHTML = $rev
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
        document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('tickets') }}"; // Redirect to remove query parameters
    });
    </script>
@endsection
