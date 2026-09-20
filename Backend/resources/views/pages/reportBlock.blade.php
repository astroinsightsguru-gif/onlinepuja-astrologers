@extends('../layout/' . $layout)

@section('subhead')
    <title>Report & Block List</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10">{{ucfirst($professionTitle)}} Review</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-review"class="btn btn-primary shadow-md  d-inline flex ml-auto"
    onclick="document.getElementById('add-data').reset();document.getElementById('thumb').style.display = 'none'" style="width: 10%" >Add
    Review</a>

    <!-- BEGIN: Data List -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report - mt-7" aria-label="reportBlock">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">USERPROFILE</th>
                        <th class="whitespace-nowrap">USERNAME</th>
                        <th class="text-center whitespace-nowrap">{{ucwords($professionTitle)}}</th>
                        <th class="text-center whitespace-nowrap">DATE</th>
                        <th class="text-center whitespace-nowrap">Review</th>
                        <th class=" whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($reportBlocks as $reportBlock)
                        <tr class="intro-x">
                            <td>{{ ++$no }}</td>
                            <td>
                        <div class="flex">
                            <div class="w-10 h-10 image-fit zoom-in">
                                <img class="rounded-full cursor-pointer" 
                                     src="{{ Str::startsWith($reportBlock->profile, ['http://','https://']) ? $reportBlock->profile : '/' . $reportBlock->profile }}"
                                     onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                     alt="Customer image"
                                     onclick="openImage('{{ $reportBlock->profile }}')" />
                            </div>
                        </div>
                    </td>

                            @if(!empty($reportBlock->user_name))
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ $reportBlock->user_name ? $reportBlock->user_name : 'user' }}</div>

                            </td>
                            @else
                            <td>
                                <div class=" whitespace-nowrap">
                                    {{ $reportBlock->userName ? $reportBlock->userName : 'user' }}</div>

                            </td>
                            @endif
                            <td class="text-center">{{ $reportBlock->astrologerName }}</td>
                            <td class="text-center">{{ date('d-m-Y', strtotime($reportBlock->created_at)) }}</td>
                            <td class="text-center">{{ $reportBlock->review }}</td>
                            <td class="text-center"> <a id="editbtn" href="javascript:;"
                                    onclick="delbtn({{ $reportBlock->id }})" class="flex items-center text-danger"
                                    data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal"><i
                                        data-lucide="trash-2" class="editbtn w-4 h-4 mr-1"></i>Delete</a></td>

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
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('astrologerReview', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('astrologerReview', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('astrologerReview', ['page' => $page + 1]) }}">
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
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noImage">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <!-- END: Pagination -->
    </div>
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>

                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process
                            cannot be undone.</div>
                    </div>

                    <form action="{{ route('deleteReview') }} " method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="del_id" name="del_id">
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button class="btn btn-danger w-24">@method('DELETE')Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


     <!--Add Review-->
     <div id="add-review" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Review</h2>
                </div>
                <form id="add-data" method="POST" action="{{route('addReviewfromAdmin')}}" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="review" class="form-label">Review Message</label>
                                            <input type="text" name="review" id="review" class="form-control"
                                                placeholder="review" onkeypress="return Validate(event);" required>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="input">
                                        <div>
                                            <label for="rating" class="form-label">Review Star (Out of 5)</label>
                                            <input type="number" name="rating" id="rating" class="form-control"
                                                placeholder="Rating" onkeypress="return Validate(event);" required>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input mt-2 sm:mt-0">
                                        <label id="userId" class="form-label">User</label>
                                        <input type="text" name="user_name" id="user_name" class="form-control"
                                        placeholder="User Name" onkeypress="return Validate(event);" required>
                                        <div class="text-danger print-astrologerId-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>

                                      <div class="input mt-2 sm:mt-0">
                                        <label id="astrologerId" class="form-label">{{ucwords($professionTitle)}}</label>
                                        <select data-placeholder="Search User" class="form-control" id="astrologerId"
                                            name="astrologerId" value="astrologerId" required>
                                            @foreach ($astrologer as $astro)
                                                <option id="astrologerId" value={{ $astro->id }}>
                                                    {{ $astro->name }}-{{ $astro->contactNo }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger print-astrologerId-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="mt-5"><button class="btn btn-submit btn-primary shadow-md mr-2">Add Review</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });

        function delbtn($id) {
            $('#del_id').val($id);
        }
    </script>
@endsection
