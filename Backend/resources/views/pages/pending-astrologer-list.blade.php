@extends('../layout/' . $layout)

@section('subhead')
    <title>{{ucfirst($professionTitle)}}</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">{{ucfirst($professionTitle)}}s</h2>
    {{-- @if ($totalRecords > 0)
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>
    @endif --}}
    <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn" href="{{route('addAstrologer')}}">Add {{ucfirst($professionTitle)}}</a>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center">


            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('pending-requests') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('pending-requests') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
           
               <!-- Separate Date Range Filter Form -->
               <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="{{ route('pending-requests') }}" method="GET" enctype="multipart/form-data" id="filterForm">
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
        <!-- BEGIN: Data List -->
    </div>
    @if (count($astrologers) > 0)
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
                <table class="table table-report mt-2" aria-label="astrologer-list">
                    <thead class="sticky-top">
                        <tr>
                            <th class="whitespace-nowrap">#</th>
                            <th class="whitespace-nowrap">NAME</th>
                            <th class="text-center whitespace-nowrap">CONTACT DETAILS</th>
                            <th class="text-center whitespace-nowrap">GENDER</th>
                            <th class="text-center whitespace-nowrap">TOTAL REQUEST</th>
                            <th class="text-center whitespace-nowrap">CALL</th>
                            <th class="text-center whitespace-nowrap">CHAT</th>
                            <th class="text-center whitespace-nowrap">LIVE</th>
                            <th class="text-center whitespace-nowrap">CREATED AT</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="todo-list">
                        @php
        $no = 0;
                        @endphp
                        @foreach ($astrologers as $astro)
                                <tr class="intro-x">
                                    <td>{{ ($page - 1) * 15 + ++$no }}</td>
                                    <td>
                        <div class="flex">
                            <div class="w-10 h-10 image-fit zoom-in">
                                <img class="rounded-full cursor-pointer" 
     src="{{ Str::startsWith($astro->profileImage, ['http://','https://']) ? $astro->profileImage : '/' . $astro->profileImage }}"
     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
     alt="Astrologer image"
     onclick="openImage('{{ Str::startsWith($astro->profileImage, ['http://','https://']) ? $astro->profileImage : '/' . $astro->profileImage }}')"
/>

                            </div>
                        </div>
                    </td>


                                    
                                <td class="text-center">
                                <div>
                                    @if(!empty($astro['contactNo']))
                                        <div class="flex items-center justify-center">
                                            <i data-lucide="phone-call" class="w-4 h-4 mr-2"></i>
                                            <span class="font-medium">{{@$astro['countryCode'] ? @$astro['countryCode'] : '' }} {{ $astro['contactNo'] }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-center mt-2">
                                        <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                        <span class="text-gray-500">{{ $astro['email'] }}</span>
                                    </div>
                                </div>
                               </td>
                                    <td class="text-center">{{ $astro['gender'] }}</td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center">
                                            <i data-lucide="phone-call" class="w-4 h-4 mr-2"></i>
                                            {{ $astro['totalCallRequest'] }} /<i data-lucide="message-square"
                                                class="w-4 h-4 mr-2 ml-2"></i>{{ $astro['totalChatRequest'] }}
                                        </div>
                                    </td>
                                  <!-- Call Section Toggle -->
                            <td>
                                <div class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                                    <input class="toggle-class form-check-input mr-0 ml-3" type="checkbox"
                                        data-id="{{ $astro['id'] }}" data-section="call_sections"
                                        data-onstyle="success" data-offstyle="danger"
                                        data-on="1" data-off="0"
                                        {{ $astro['call_sections'] ? 'checked' : '' }} />
                                </div>
                            </td>

                            <!-- Chat Section Toggle -->
                            <td>
                                <div class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                                    <input class="toggle-class form-check-input mr-0 ml-3" type="checkbox"
                                        data-id="{{ $astro['id'] }}" data-section="chat_sections"
                                        data-onstyle="success" data-offstyle="danger"
                                        data-on="1" data-off="0"
                                        {{ $astro['chat_sections'] ? 'checked' : '' }} />
                                </div>
                            </td>

                            <!-- Live Section Toggle -->
                            <td>
                                <div class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                                    <input class="toggle-class form-check-input mr-0 ml-3" type="checkbox"
                                        data-id="{{ $astro['id'] }}" data-section="live_sections"
                                        data-onstyle="success" data-offstyle="danger"
                                        data-on="1" data-off="0"
                                        {{ $astro['live_sections'] ? 'checked' : '' }} />
                                </div>
                            </td>

                            <td class="text-center">
                                {{ date('d-m-Y h:i a', strtotime($astro['created_at'])) ? date('d-m-Y h:i a', strtotime($astro['created_at'])) : '--' }}
                            </td>


                                    <td class="w-40">
                                        <div
                                            class="flex items-center justify-center {{ $astro['isVerified'] ? 'text-success' : 'text-danger' }}">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                            {{ $astro['isVerified'] ? 'Verified' : 'unverified' }}
                                        </div>
                                    </td>

                                    <td class="table-report__action w-56">

                                        <div class="flex justify-center items-center">
                                            <a onclick="editbtn({{ $astro['id'] }},{{ $astro['isVerified'] }})"
                                                data-tw-target="#verifiedAstrologer"id="editbtn" href="javascript:;"
                                                class="flex items-center mr-3 text-success" data-tw-toggle="modal">
                                                @if ($astro['isVerified'])
                                                    <i style="color:brown"
                                                        data-lucide="{{ $astro['isVerified'] ? 'lock' : 'unlock' }}"
                                                        class="w-4 h-4 mr-1"></i>
                                                @else
                                                    <i data-lucide="{{ $astro['isVerified'] ? 'lock' : 'unlock' }}"
                                                        class="w-4 h-4 mr-1"></i>
                                                @endif
                                                @if ($astro['isVerified'])
                                                    <span style="color:brown">unverified</span>
                                                @else
                                                    Verified
                                                @endif
                                            </a>
                                            <a class="flex items-center mr-3" href="{{strtolower($professionTitle)}}s/edit/{{ $astro['id'] }}">
                                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit
                                            </a>
                                            <a class="flex items-center mr-3 text-success" href="{{strtolower($professionTitle)}}s/{{ $astro['id'] }}">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                            </a>
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


            <!-- END: Data List -->
            <!-- BEGIN: Pagination -->
            @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('pending-requests', ['page' => $page - 1, 'searchString' => $searchString]) }}">
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
                                href="{{ route('pending-requests', ['page' => 1, 'searchString' => $searchString]) }}">1</a>
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
                                href="{{ route('pending-requests', ['page' => $i, 'searchString' => $searchString]) }}">{{ $i }}</a>
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
                                href="{{ route('pending-requests', ['page' => $totalPages, 'searchString' => $searchString]) }}">{{ $totalPages }}</a>
                        </li>
                    @endif

                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('pending-requests', ['page' => $page + 1, 'searchString' => $searchString]) }}">
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
                    <form  class="verifyAstro" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="filed_id" name="filed_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnVerified">Yes,
                                Verified it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"> Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div> <!-- END: Modal Content -->
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')

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


        jQuery(function() {
            jQuery('.verifyAstro').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('verifiedAstrologerApi') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
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
                    url: "{{ route('printastrologerlist') }}",
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
                            link.download = "astrologerList.pdf";
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
                    url: "{{ route('exportAstrologerCSV') }}",
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
                            link.download = "astrologerList.csv";
                            link.click();
                            spinner.hide();
                        } else {
                            spinner.hide();
                        }
                    }
                });
            });
        });

        $(document).ready(function () {
    $('.toggle-class').on('change', function () {
        var id = $(this).data('id');
        var section = $(this).data('section');
        var status = $(this).is(':checked') ? '1' : '0';

        jQuery.ajax({
            url: '{{ route('updateSections') }}',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                astro_id: id,
                section: section,
                status: status,
            }),
            success: function (data) {
                var sectionName = '';
                switch (section) {
                    case 'call_sections':
                        sectionName = 'Call';
                        break;
                    case 'chat_sections':
                        sectionName = 'Chat';
                        break;
                    case 'live_sections':
                        sectionName = 'Live';
                        break;
                }

                var statusMessage = status === '1' ? `${sectionName} section is ON SuccessFully !` : `${sectionName} section is OFF SuccessFully !`;
                toastr.success(statusMessage);
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                toastr.error('Failed to update section status'); 
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
            window.location.href = "{{ route('pending-requests') }}"; // Redirect to remove query parameters
        });
    </script>
@endsection
