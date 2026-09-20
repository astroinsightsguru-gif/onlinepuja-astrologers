@extends('../layout/' . $layout)

@section('subhead')
    <title>Chapters</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Chapters</h2>
    <a href="{{route('viewCourseChapter')}}"
        class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn"
        onclick="document.getElementById('add-data').reset();document.getElementById('thumb').style.display = 'none'">Add
        Chapters</a>
    <div class="grid grid-cols-12 gap-6 ">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if (count($courseschapter) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report -mt-2" aria-label="astrologer-category">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">COURSE</th>
                        <th class="whitespace-nowrap">CHAPTER NAME</th>
                        <th class="whitespace-nowrap">YOUTUBE LINK</th>
                        <th class="whitespace-nowrap">DOCUMENT</th>
                        <th class="text-center whitespace-nowrap">STATUS</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($courseschapter as $chapter)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            {{-- <td>
                                <div class="flex">
                                    <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="rounded-full" src="/{{ $chapter['image'] }}"
                                            onerror="this.onerror=null;this.src='/build/assets/images/default.jpg';"
                                            alt="{{ucfirst($professionTitle)}} image" />
                                    </div>
                                </div>
                            </td> --}}
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $chapter->course->name}}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $chapter['chapter_name'] }}</div>
                            </td>

                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $chapter['youtube_link'] }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    @if(!empty($chapter['chapter_document']))
                                        <a href="{{ asset($chapter['chapter_document']) }}" target="_blank" class="text-red-600">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        --
                                    @endif
                                </div>
                            </td>


                            <td class="w-40">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0">
                                    <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $chapter['isActive'] ? 'checked' : '' }}
                                        onclick="editAstrologyCategory({{ $chapter['id'] }},{{ $chapter['isActive'] }})"
                                        href="$chapter['id']" data-tw-target="#verified">
                                </div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center mr-3"  href="{{ route('edit-CourseChapter', $chapter->id) }}">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1" ></i>Edit
                                    </a>
                                    <a type="button" href="javascript:;" class="flex items-center deletebtn text-danger"
                                    data-tw-toggle="modal" data-tw-target="#deleteModal" onclick="delbtn({{ $chapter['id']}})">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('CourseList-list', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('CourseList-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('CourseList-list', ['page' => $page + 1]) }}">
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
    <!-- END: Data List -->

    </div>
    <!-- BEGIN: Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process
                            cannot be undone.</div>
                    </div>
                    <form action="{{route('deleteCourseChapter')}}" method="POST">
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
    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('CourseChapterStatus') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="status_id" name="status_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnActive">Yes,
                                Active it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary btn-submit w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

            function delbtn($id) {
                var id = $id;
                $did = id;

                $('#del_id').val($did);
                $('#id').val($id);
            }
            function editbtn($id, $name,$description,$course_category, $image) {
                var id = $id;
                var gid = $id;
                $cid = id;
                $('#filed_id').val($cid);
                $('#editName').val($name);
                $('#editdescription').val($description);
                $('#editcourse_category_id').val($course_category);
                document.getElementById("thumbs").src = "/" + $image;
            }

            function Validate(event) {
                var regex = new RegExp("^[0-9-!@#$%&<>*?]");
                var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
                if (regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            }

            function editAstrologyCategory($id, $isActive) {
                var id = $id;
                $fid = id;
                var active = $isActive ? 'Inactive' : 'Active';
                document.getElementById('active').innerHTML = "You want to " + active;
                document.getElementById('btnActive').innerHTML = "Yes, " +
                    active + " it";

                $('#status_id').val($fid);
                $('#editName').val($name);
            }

            function preview() {
                document.getElementById("thumb").style.display = "block";
                thumb.src = URL.createObjectURL(event.target.files[0]);
            }

            function previews() {
                document.getElementById("thumbs").style.display = "block";
                thumbs.src = URL.createObjectURL(event.target.files[0]);
            }
        </script>
        <script type="module">

    jQuery.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        }
    })
    jQuery("#add-data").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addCourse') }}",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        location.reload();
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });

        });
        function printErrorMsg (msg) {
        jQuery(".print-name-error-msg").find("ul").html('');
        jQuery.each( msg, function( key, value ) {
            if(key == 'name') {
                jQuery(".print-name-error-msg").css('display','block');
                jQuery(".print-name-error-msg").find("ul").append('<li>'+value+'</li>');
            }
            else {
                toastr.warning(value)
            }
        });
    }
    function printEditErrorMsg (msg) {
        jQuery(".print-edit-name-error-msg").find("ul").html('');
        jQuery.each( msg, function( key, value ) {
            if(key == 'name') {
                jQuery(".print-edit-name-error-msg").css('display','block');
                jQuery(".print-edit-name-error-msg").find("ul").append('<li>'+value+'</li>');
            }
        });
    }
        </script>
         <script>
            $(window).on('load', function() {
                $('.loader').hide();
            })
        </script>
    @endsection
