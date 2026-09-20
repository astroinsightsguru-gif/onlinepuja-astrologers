@extends('../layout/' . $layout)

@section('subhead')
<title>AI Astrologer</title>
@endsection

<!-- Include SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<style>
    .about1 {
        text-overflow: ellipsis;
        overflow: hidden;
        width: 100px;
        white-space: nowrap;
        display: block;
        position: relative;
        transition: width 0.3s ease;
    }

    .about1:hover {
        overflow: visible;
        white-space: normal;
        width: auto;
        z-index: 10;
    }
</style>
@section('subcontent')
<div class="loader"></div>
<h2 class="intro-y text-lg font-medium mt-10 d-inline">AI Astrologers</h2>
{{-- @if (@$totalRecords > 0)
<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>
@endif --}}
<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn" href="{{route('create.ai.astrologer')}}">Add AI Astrologer</a>
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center">

        @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    willClose: () => {
                    }
                });
            });
        </script>
        @endif

        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            <form action="{{ route('ai.astrologers') }}" method="GET">
                @csrf
                <div class="w-56 relative text-slate-500" style="display:inline-block">
                    <input value="{{ $searchString ?? '' }}" type="text" class="form-control w-56 box pr-10"
                    placeholder="Search..." id="searchString" name="searchString">
                    @if (!empty($searchString))
                    <a href="{{ route('ai.astrologers') }}" class="text-slate-500" onclick="clearSearch()">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="x"></i>
                    </a>
                    @else
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    @endif
                </div>
                <button class="btn btn-primary shadow-md mr-2">Search</button>
            </form>

        </div>
    </div>
    <!-- BEGIN: Data List -->
</div>
@if ($aiAstrologers->isNotEmpty())
<div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
    <table class="table table-report mt-2" aria-label="astrologer-list">
        <thead class="sticky-top">
            <tr>
                <th class="whitespace-nowrap">#</th>
                <th class="whitespace-nowrap">NAME</th>
                <th class="text-center whitespace-nowrap">About</th>
                <th class="text-center whitespace-nowrap">Category</th>
                <th class="text-center whitespace-nowrap">PRIMARY SKILL</th>
                <th class="text-center whitespace-nowrap">ALL SKILL</th>
                <th class="text-center whitespace-nowrap">CHAT CHARGE (INR)</th>
                <th class="text-center whitespace-nowrap">CHAT CHARGE (USD)</th>
                <th class="text-center whitespace-nowrap">EXPERIENCE</th>
                <th class="text-center whitespace-nowrap">System Intruction</th>
                {{-- <th class="text-center whitespace-nowrap">STATUS</th> --}}
                <th class="text-center whitespace-nowrap">ACTIONS</th>
            </tr>
        </thead>
        <tbody id="todo-list">
            @php
            $no = 0;
            @endphp

            @foreach ($aiAstrologers as $astro)
            <tr class="intro-x">
                <td>{{ ($page - 1) * 15 + ++$no }}</td>
                <td>
                    <div class="flex items-center">
                        <div class="image-fit zoom-in" style="height:2.3rem;width:2.3rem;">
                            <img class="rounded-full" src="{{asset($astro->image)}}"
                            alt="{{$astro->image}} image" />
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">{{ @$astro->name }}</div>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium about1">{{ $astro->about }}</span>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">

                            <span class="font-medium">{{  implode(',', $astro->categories_names->toArray()) }}</span>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium">{{ implode(',', $astro->primary_skills_names->toArray()) }}</span>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium">{{ implode(', ', $astro->all_skills_names->toArray()) }}</span>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium">{{ $astro->chat_charge }}</span>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium">{{ $astro->chat_charge_usd }}</span>
                        </div>
                    </div>
                </td>

                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium">{{ $astro->experience }} Years</span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <div>
                        <div class="flex items-center justify-center">
                            <span class="font-medium about1">{{ $astro->system_intruction }}</span>
                        </div>
                    </div>
                </td>
                {{-- <td>
                    <div class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <input class="toggle-class form-check-input mr-0 ml-3" type="checkbox"
                        data-id="{{ @$astro['id'] }}" data-section="chat_sections"
                        data-onstyle="success" data-offstyle="danger"
                        data-on="1" data-off="0"
                        {{ @$astro['chat_sections'] ? 'checked' : '' }} />
                    </div>
                </td> --}}

                <td class="table-report__action w-56">

                    <div class="flex justify-center items-center">

                        <a class="flex items-center mr-3" href="{{ route('edit.ai.astrologer', ['slug' => $astro->slug]) }}">
                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit
                        </a>

                        <a class="flex items-center mr-3 text-danger" href="javascript:void(0);" onclick="deleteJob({{@$astro->id}})">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>Delete
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- BEGIN: Pagination -->
@if ($totalRecords > 0)
<div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
@endif
<div class="d-inline addbtn intro-y col-span-12">
    <nav class="w-full sm:w-auto sm:mr-auto">
        <ul class="pagination">
            <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ route('ai.astrologers', ['page' => $page - 1, 'searchString' => $searchString]) }}">
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
                href="{{ route('ai.astrologers', ['page' => 1, 'searchString' => $searchString]) }}">1</a>
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
                href="{{ route('ai.astrologers', ['page' => $i, 'searchString' => $searchString]) }}">{{ $i }}</a>
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
                href="{{ route('ai.astrologers', ['page' => $totalPages, 'searchString' => $searchString]) }}">{{ $totalPages }}</a>
            </li>
            @endif

            <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                <a class="page-link"                href="{{ route('ai.astrologers', ['page' => $page + 1, 'searchString' => $searchString]) }}">
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

<!-- END: Pagination -->

<!-- BEGIN: Modal Content -->
<div id="verifiedAstrologer" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <div class="text-3xl mt-5">Are You Sure?</div>
                    <div class="text-slate-500 mt-2" id="verified">You want Verified!</div>
                </div>
                <form action="{{ route('verifiedAstrologerApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="filed_id" name="filed_id">
                    <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnVerified">Yes,
                        Verified it!
                    </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                    onclick="location.reload();"> Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div> <!-- END: Modal Content -->
@endsection
@section('script')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.7.0/dist/js/bootstrap.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    function clearSearch() {
        const url = new URL(window.location.href);
        url.searchParams.delete('searchString');  // Remove the searchString from URL
        window.location.href = url.toString();  // Reload the page with the updated URL
    }
</script>


<script>
    function deleteJob(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                axios.delete("{{ route('delete.ai.astrologer', '') }}" + '/' + id)
                .then(response => {
                    swal({
                        title: "Your data has been deleted!",
                        icon: "success",
                    })
                    .then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    swal({
                        title: "Error!",
                        text: error.response.data.error || 'An error occurred while trying to delete the subject.',
                        icon: "error",
                    });
                });
            } else {
                swal({
                    icon: "success",
                    text: "Your data is safe!",
                });
            }
        });
    }
</script>






<script type="text/javascript">
    @if (Session::has('error'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.warning("{{ session('error') }}");
    @endif
    function editbtn($id, $isVerified) {
        var id = $id;
        $cid = id;

        $('#filed_id').val($cid);
        var verified = $isVerified ? 'unverified' : 'Verified';
        document.getElementById('verified').innerHTML = "You want to " + verified;
        document.getElementById('btnVerified').innerHTML = "Yes, " +
        verified + " it";
    }
</script>
<script type="text/javascript">
    // var spinner = $('.loader');
    // jQuery(function() {
    //     jQuery('.printpdf').click(function(e) {
    //         e.preventDefault();
    //         spinner.show();
    //         var searchString = $("#searchString").val();
    //         jQuery.ajax({
    //             type: 'GET',
    //             url: "{{ route('printastrologerlist') }}",
    //             data: {
    //                 "searchString": searchString,
    //             },
    //             xhrFields: {
    //                 responseType: 'blob'
    //             },
    //             success: function(data) {
    //                 if (jQuery.isEmptyObject(data.error)) {
    //                     var blob = new Blob([data]);
    //                     var link = document.createElement('a');
    //                     link.href = window.URL.createObjectURL(blob);
    //                     link.download = "astrologerList.pdf";
    //                     link.click();
    //                     spinner.hide();
    //                 } else {
    //                     spinner.hide();
    //                 }
    //             }
    //         });
    //     });
    //     jQuery('.downloadcsv').click(function(e) {
    //         e.preventDefault();
    //         spinner.show();
    //         var searchString = $("#searchString").val();
    //         jQuery.ajax({
    //             type: 'GET',
    //             url: "{{ route('exportAstrologerCSV') }}",
    //             data: {
    //                 "searchString": searchString,
    //             },
    //             xhrFields: {
    //                 responseType: 'blob'
    //             },
    //             success: function(data) {
    //                 if (jQuery.isEmptyObject(data.error)) {
    //                     var blob = new Blob([data]);
    //                     var link = document.createElement('a');
    //                     link.href = window.URL.createObjectURL(blob);
    //                     link.download = "astrologerList.csv";
    //                     link.click();
    //                     spinner.hide();
    //                 } else {
    //                     spinner.hide();
    //                 }
    //             }
    //         });
    //     });
    // });

    // $(document).ready(function () {
    //     $('.toggle-class').on('change', function () {
    //         var id = $(this).data('id');
    //         var section = $(this).data('section');
    //         var status = $(this).is(':checked') ? '1' : '0';

    //         jQuery.ajax({
    //             url: '{{ route('updateSections') }}',
    //             type: 'POST',
    //             contentType: 'application/json',
    //             data: JSON.stringify({
    //                 astro_id: id,
    //                 section: section,
    //                 status: status,
    //             }),
    //             success: function (data) {
    //                 var sectionName = '';
    //                 switch (section) {
    //                     case 'call_sections':
    //                     sectionName = 'Call';
    //                     break;
    //                     case 'chat_sections':
    //                     sectionName = 'Chat';
    //                     break;
    //                     case 'live_sections':
    //                     sectionName = 'Live';
    //                     break;
    //                 }

    //                 var statusMessage = status === '1' ? `${sectionName} section is ON SuccessFully !` : `${sectionName} section is OFF SuccessFully !`;
    //                 toastr.success(statusMessage);
    //             },
    //             error: function (xhr, status, error) {
    //                 console.error('Error:', error);
    //                 toastr.error('Failed to update section status');
    //             }
    //         });
    //     });
    // });




</script>

<script>
    $(window).on('load', function() {
        $('.loader').hide();
    })
</script>
@endsection
