@extends('../layout/' . $layout)

@section('subhead')
    <title>Report Types</title>
@endsection

@section('subcontent')
    <div class="loader"></div>

    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Report Types</h2>
    <a data-tw-toggle="modal" data-tw-target="#add-astro" onclick="showEditor()"
        class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn" href="javascript:;">Add Report Type</a>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">


            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('reportTypes') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('reportTypes') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
        </div>
    </div>

    <!-- BEGIN: Users Layout -->
    @if ($totalRecords > 0)
        <div class="grid grid-cols-12 gap-6 mt-5 grid-table">
            @foreach ($reports as $report)
                <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                    <div class="box">
                        <div class="p-5">
                            <div
                                class="h-40 2xl:h-56 image-fit rounded-md overflow-hidden before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black before:to-black/10">
                                <img alt="Product image" class="rounded-md" src="/{{ $report['reportImage'] }}">

                                <div class="absolute bottom-0 text-white px-5 pb-6 z-10">
                                    <a href="" class="block font-medium text-base">{{ $report['title'] }}</a>
                                </div>
                            </div>
                            <div class="text-slate-600 dark:text-slate-500 mt-5">
                                {!! $report['description'] !!}

                            </div>
                        </div>
                        <div class="flex  p-5 border-t border-slate-200/60 dark:border-darkmode-400">

                            <a id="editbtn" href="javascript:;"
                                onclick="editbtn({{ $report['id'] }} , '{{ $report['title'] }}',{{ json_encode($report['description']) }},'{{ $report['reportImage'] }}')"
                                onclick="" class="flex  mr-3 " data-tw-target="#edit-modal" data-tw-toggle="modal"><i
                                    data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit</a>
                            <div
                                class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                     mt-3 sm:mt-0">
                                <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                    href="javascript:;" data-tw-toggle="modal" data-onstyle="success" data-offstyle="danger"
                                    data-toggle="toggle" data-on="Active" data-off="InActive"
                                    {{ $report['isActive'] ? 'checked' : '' }}
                                    onclick="editReportStatus({{ $report['id'] }},{{ $report['isActive'] }})"
                                    href="$report['id']" data-tw-target="#verified">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="intro-y mt-5" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="report">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <div id="add-astro" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Report Type</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="add-report">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <label for="reportImage" class="form-label">Report Image</label>
                                            <img id="thumb" width="150px" alt="report" style="display:none" />
                                            <input type="file" class="mt-2" name="reportImage" id="reportImage"
                                                onchange="preview()" accept="image/*">
                                                <div class="text-danger print-image-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2 mt-3">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Title" required onkeypress="return Validate(event);">
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="inpu mt-3" id="classic-editor">
                                    <label for="description" class="from-label">Description</label>
                                    <textarea class="form-control ml-3" id="description" name="description"></textarea>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit"class="btn btn-primary shadow-md mr-2">Add Report
                                    Type
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Report Type</h2>
                </div>
                <form action="{{ route('editReportApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <input type="hidden" name="editId" id="editId" class="form-control"
                                                placeholder="Title" required>
                                            <label for="reportImage" class="form-label">Report Image</label>
                                            <img id="thumbs" width="150px" alt="report"
                                                onerror="this.style.display='none';" />
                                            <input type="file" class="mt-2" name="reportImage" id="ereportImage"
                                                onchange="previews()" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2 mt-3">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="etitle" class="form-control"
                                                placeholder="Title" required onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                </div>
                                <div class="inpu mt-3" id="classic-editor">
                                    <label for="description" class="from-label">Description</label>
                                    <textarea class="form-control ml-3" id="editdescription" name="editdescription"></textarea>
                                </div>
                            </div>
                            <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($totalRecords > 0)
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('reportTypes', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('reportTypes', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('reportTypes', ['page' => $page + 1]) }}">
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
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('reportStatusApi') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="status_id" name="status_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnActive">Yes,
                                Active it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- </div> --}}
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        function editbtn($id, $title, $description, $image) {

            var id = $id;
            var gid = $id;

            $cid = id;

            $('#editId').val($cid);
            $('#etitle').val($title);
            document.getElementById("thumbs").src = "/" + $image;
            var editor = CKEDITOR.instances['editdescription'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdescription');
            var editor = CKEDITOR.instances['editdescription'];
            CKEDITOR.instances['editdescription'].setData($description)
        }

        function editAstroMall($id, $name) {
            var id = $id;
            $fid = id;


            $('#id').val($name);
        }

        function editReportStatus($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
            $('#id').val($name);
        }


        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
            jQuery(".print-image-error-msg").find("ul").html('');
        }

        function previews() {
            document.getElementById("thumbs").style.display = "block";
            thumbs.src = URL.createObjectURL(event.target.files[0]);
        }

        function showEditor() {
            var ele = document.getElementById('add-report').reset();
            var editor = CKEDITOR.instances['description'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('description');
            var editor = CKEDITOR.instances['description'];
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        jQuery("#add-report").submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);
            data.append('description', CKEDITOR.instances['description'].getData());
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addReportApi') }}",
                data: data,
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

        function printErrorMsg(msg) {
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery(".print-image-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (key == 'title') {
                    jQuery(".print-name-error-msg").css('display', 'block');
                    jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'reportImage') {
                    jQuery(".print-image-error-msg").css('display', 'block');
                    jQuery(".print-image-error-msg").find("ul").append('<li>' + value + '</li>');
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
