@extends('../layout/' . $layout)

@section('subhead')
    <title>ContactUs List</title>
@endsection

<style>
    .expandable-cell {
        position: relative;
        max-width: 200px;
        overflow: hidden;
        white-space: nowrap;
    }

    .full-text {
        display: none;
        position: absolute;
        left: 0;
        top: 0;
        background: white;
        padding: 5px;
        border: 1px solid #ddd;
        z-index: 10;
        white-space: normal;
        word-wrap: break-word;
    }

    .expandable-cell:hover .truncated-text {
        display: none;
    }

    .expandable-cell:hover .full-text {
        display: block;
    }

    /* Modal styles */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background: rgba(0, 0, 0, 0.5);
    }

    .custom-modal-content {
        background: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 8px;
        width: 50%;
        max-width: 600px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .custom-modal-close {
        float: right;
        font-size: 22px;
        font-weight: bold;
        cursor: pointer;
    }

    .modal-row {
        margin-bottom: 12px;
    }

    .modal-label {
        font-weight: bold;
        display: inline-block;
        width: 120px;
    }
</style>

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Contact Us</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
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
                        <th class="whitespace-nowrap">Name</th>
                        <th class="whitespace-nowrap">Email</th>
                        <th class="whitespace-nowrap">Description</th>
                        <th class="text-center whitespace-nowrap">Entry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php $no = 0; @endphp
                    @foreach ($contacts as $clist)
                        <tr class="intro-x"
                            data-id="{{ $clist->id }}"
                            data-name="{{ $clist->contact_name }}"
                            data-email="{{ $clist->contact_email }}"
                            data-message="{{ $clist->contact_message }}"
                            data-date="{{ date('d-m-Y h:i a', strtotime($clist->created_at)) }}">
                            
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->contact_name ? $clist->contact_name : '--' }}
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->contact_email ? $clist->contact_email : '--' }}
                                </div>
                            </td>
                            <td class="expandable-cell">
                                <span class="truncated-text">
                                    {{ (!empty($clist->contact_message) ? (strlen($clist->contact_message) > 50 ? ucwords(substr($clist->contact_message, 0, 50)) . ' ...' : ucwords($clist->contact_message)) : '- -') }}
                                </span>
                                <span class="full-text">
                                    {{ ucwords($clist->contact_message) }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ date('d-m-Y h:i a', strtotime($clist->created_at)) ?: '--' }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary viewBtn">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->

        <!-- BEGIN: Pagination -->
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline intro-y col-span-12 addbtn ">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('contactlist', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('contactlist', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('contactlist', ['page' => $page + 1]) }}">
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

    <!-- Custom Modal -->
    <div id="contactModal" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-modal-close">&times;</span>
            <h3 class="mb-3 font-bold text-lg">Contact Details</h3>
            <div class="modal-row"><span class="modal-label">Name:</span> <span id="modalName"></span></div>
            <div class="modal-row"><span class="modal-label">Email:</span> <span id="modalEmail"></span></div>
            <div class="modal-row"><span class="modal-label">Message:</span> <span id="modalMessage"></span></div>
            <div class="modal-row"><span class="modal-label">Entry Date:</span> <span id="modalDate"></span></div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });

        $(document).ready(function() {
            // Open modal on view click
            $('.viewBtn').on('click', function() {
                let row = $(this).closest('tr');
                $('#modalName').text(row.data('name') || '--');
                $('#modalEmail').text(row.data('email') || '--');
                $('#modalMessage').text(row.data('message') || '--');
                $('#modalDate').text(row.data('date') || '--');
                $('#contactModal').fadeIn();
            });

            // Close modal
            $('.custom-modal-close').on('click', function() {
                $('#contactModal').fadeOut();
            });

            // Close when clicking outside
            $(window).on('click', function(e) {
                if ($(e.target).is('#contactModal')) {
                    $('#contactModal').fadeOut();
                }
            });
        });
    </script>
@endsection
