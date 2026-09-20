@extends('../layout/' . $layout)

@section('subhead')
<title>{{ucfirst($appname)}} in News</title>
@endsection

@section('subcontent')
<div class="loader"></div>
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">{{ucfirst($appname)}} News</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a class="btn btn-primary shadow-md mr-2" data-tw-target="#add-video" data-tw-toggle="modal"
            onclick="document.getElementById('add-form').reset();">Add New News</a>
    </div>
</div>
@if (count($news) > 0)
<div class="intro-y grid grid-cols-12 gap-6 mt-5 withoutsearch">
    @foreach ($news as $video)
    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4 box fitbox">


        <div class="p-5" style="word-break:break-all">
            <div class="h-40 2xl:h-56 image-fit">
                <img class=" cursor-pointer"
                    src="{{ Str::startsWith( $video['bannerImage'], ['http://','https://']) ? $video['bannerImage'] : '/' . $video['bannerImage'] }}"
                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                    alt="Customer image"
                    onclick="openImage('{{ Str::startsWith( $video['bannerImage'], ['http://','https://']) ? $video['bannerImage'] : '/' . $video['bannerImage'] }}')" />
            </div>
            <a target="_blank" href="{{ $video['link'] }}" class="block font-medium text-base mt-5"
                style="color: blue;height:60px">{{ $video['link'] }}</a>
            <div class="text-slate-600 dark:text-slate-500 mt-2">
                <p><b>Channel:</b> {{ $video['channel'] }}</p>
                <p><b>News Date:</b>
                    {{ date('d-m-Y', strtotime($video['newsDate'])) ? date('d-m-Y', strtotime($video['newsDate'])) : '--' }}
                </p>

                {!! \Illuminate\Support\Str::words($video['description'], 30, '...') !!}

            </div>
        </div>
        <div
            class="flex justify-center lg:justify-center items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">

            <a id="editbtn" href="javascript:;"
                onclick="editbtn({{ $video['id'] }} , '{{ $video['link'] }}','{{ $video['bannerImage'] }}', '{{ $video['description'] }}','{{ $video['channel'] }}','{{ $video['newsDate'] }}')"
                class="flex items-center mr-3 " data-tw-target="#edit-modal" data-tw-toggle="modal"><i
                    data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit</a>
            <a id="deletebtn" href="javascript:;" onclick="deletebtn({{ $video['id'] }})"
                class="flex items-center mr-3 " data-tw-target="#deleteModal" data-tw-toggle="modal"><i
                    data-lucide="trash-2" class="deletebtn w-4 h-4 mr-1"></i>Delete</a>
            <div
                class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0">
                <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                    href="javascript:;" data-tw-toggle="modal" data-onstyle="success" data-offstyle="danger"
                    data-toggle="toggle" data-on="Active" data-off="InActive"
                    {{ $video['isActive'] ? 'checked' : '' }}
                    onclick="editNewsStatus({{ $video['id'] }},{{ $video['isActive'] }})" href="$video['id']"
                    data-tw-target="#verified">
            </div>
        </div>
    </div>
    @endforeach
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
<div id="add-video" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Add {{ucfirst($appname)}} News</h2>
            </div>
            <form id="add-form" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="input" class="p-5">
                    <div class="preview">
                        <div class="mt-3">
                            <div class="sm:grid grid-cols gap-2">
                                <div class="input">
                                    <div>
                                        <label for="youtubeLink" class="form-label">Channel</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="channel" id="channel" class="form-control"
                                            placeholder="Channel" required>
                                    </div>
                                </div>
                                <div class="input">
                                    <div>
                                        <label for="youtubeLink" class="form-label">Link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="link" id="link" class="form-control"
                                            placeholder="Link" required>
                                    </div>
                                </div>
                                <div class="input">
                                    <div>
                                        <label id="input-group" class="form-label">News Date</label>
                                        <input type="date" id="newsDate" name="newsDate" class="form-control inputs"
                                            placeholder="newsDate" aria-describedby="input-group-3" required>
                                    </div>
                                </div>
                                <div class="input">
                                    <div>
                                        <label id="input-group" class="form-label">Description</label>
                                        <textarea onkeypress="return validateJavascript(event);" class="form-control" id="description" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-6">
                                <div class="intro-y col-span-12">
                                    <div>
                                        <label for="coverImage" class="form-label mt-2">Banner Image</label>
                                        <img id="thumb" width="150px" alt="coverImage" style="display:none" />
                                        <input type="file" class="mt-2" name="bannerImage" onchange="preview()"
                                            accept="image/*" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add {{ucfirst($appname)}} News</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Edit {{ucfirst($appname)}}News</h2>
            </div>
            <form method="POST" enctype="multipart/form-data" id="edit-form">
                @csrf
                <div id="input" class="p-5">
                    <div class="preview">
                        <div class="mt-3">
                            <div class="sm:grid grid-cols gap-2">
                                <div class="input">
                                    <div>
                                        <input type="hidden" name="filed_id" id="filed_id" class="form-control"
                                            placeholder="Channel">
                                        <label for="youtubeLink" class="form-label">Channel</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="channel" id="echannel" class="form-control"
                                            placeholder="Channel" required>
                                    </div>
                                </div>
                                <div class="input">
                                    <div>
                                        <label for="youtubeLink" class="form-label">Link</label>
                                        <input onkeypress="return validateJavascript(event);" type="text" name="link" id="elink" class="form-control"
                                            placeholder="Link" required>
                                    </div>
                                </div>
                                <div class="input">
                                    <div>
                                        <label for="postedOn" class="form-label">News Date</label>
                                        <input type="date" id="enewsDate" name="newsDate" class="form-control"
                                            placeholder="newsdate" aria-describedby="input-group-3" required>
                                    </div>
                                </div>
                                <div class="input">
                                    <div>
                                        <label id="input-group" class="form-label">Description</label>
                                        <textarea onkeypress="return validateJavascript(event);" class="form-control" id="edescription" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-6">
                                <div class="intro-y col-span-12">
                                    <div>
                                        <label for="coverImage" class="form-label mt-2">Banner Image</label>
                                        <img id="thumbs" width="150px" alt="coverImage" style="display:none" />
                                        <input type="file" class="mt-2" name="bannerImage"
                                            onchange="previews()" accept="image/*">
                                    </div>
                                </div>
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
<div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
    {{ $totalRecords }} entries
</div>
@endif
@if (count($news) > 0)
<div class="d-inline addbtn intro-y col-span-12 ">
    <nav class="w-full sm:w-auto sm:mr-auto" aria-label="adsVideo">
        <ul class="pagination">
            <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ route('astroguruNews', ['page' => $page - 1]) }}">
                    <i class="w-4 h-4" data-lucide="chevron-left"></i>
                </a>
            </li>
            @for ($i = 0; $i < $totalPages; $i++)
                <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                <a class="page-link"
                    href="{{ route('astroguruNews', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                </li>
                @endfor
                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('astroguruNews', ['page' => $page + 1]) }}">
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
                <form action="{{ route('newsStatusApi') }}" method="POST" enctype="multipart/form-data">
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
                <form action="{{ route('deleteNews') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="del_id" name="del_id">
                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                        <button class="btn btn-danger w-24">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    var spinner = $('.loader');

    function editbtn($id, $link, $bannerImage, $description, $channel, $newsDate) {
        $('#filed_id').val($id);
        $('#elink').val($link);
        $('#echannel').val($channel);

        var newdate = $newsDate.split("-");
        var date = newdate[2].split(" ");
        date = newdate[0] + '-' + newdate[1] + '-' + date[0];
        $('#enewsDate').val(date);

        document.getElementById("thumbs").style.display = "block";
        document.getElementById("thumbs").src = "/" + $bannerImage;

        // ✅ Set CKEditor data dynamically
        if (editorInstance) {
            editorInstance.setData($description); // Use $description, not description
        }
    }

    function deletebtn($id) {
        $('#del_id').val($id);
    }

    function preview() {
        document.getElementById("thumb").style.display = "block";
        thumb.src = URL.createObjectURL(event.target.files[0]);
    }

    function previews() {
        document.getElementById("thumbs").style.display = "block";
        thumbs.src = URL.createObjectURL(event.target.files[0]);
    }

    function validateJavascript(event) {
        var regex = new RegExp("^[<>]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }

    function showEditor() {
        var editor = CKEDITOR.instances['description'];
        if (editor) {
            editor.destroy(true);
        }
        CKEDITOR.replace('description');
        var editor = CKEDITOR.instances['description'];
    }

    function editNewsStatus($id, $isActive) {
        var id = $id;
        $fid = id;
        var active = $isActive ? 'Inactive' : 'Active';
        document.getElementById('active').innerHTML = "You want to " + active;
        document.getElementById('btnActive').innerHTML = "Yes, " +
            active + " it";

        $('#status_id').val($fid);
    }
    jQuery(function() {
        jQuery('#edit-form').submit(function(e) {
            e.preventDefault();
            spinner.show();
            var data = new FormData(this);
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('editNewsApi') }}",
                data: data,
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

    jQuery(function() {
        jQuery('#add-form').submit(function(e) {
            e.preventDefault();
            spinner.show();
            var data = new FormData(this);
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addNewsApi') }}",
                data: data,
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
</script>
<script>
    $(window).on('load', function() {
        $('.loader').hide();
    })
</script>
@endsection
