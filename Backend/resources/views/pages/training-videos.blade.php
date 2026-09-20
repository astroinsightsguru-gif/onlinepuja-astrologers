@extends('../layout/' . $layout)

@section('subhead')
    <title>Training Videos</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Training Videos</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <a class="btn btn-primary shadow-md mr-2" data-tw-target="#add-video" data-tw-toggle="modal">Add New Video</a>
        </div>
    </div>
    @if (count($videos) > 0)
        <div class="intro-y grid grid-cols-12 gap-6 mt-5 withoutsearch">
            <!-- BEGIN: Blog Layout -->

            @foreach ($videos as $video)
                <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4 box fitbox">


                    <div class="p-5" style="word-break:break-all">
                        <div class="h-40 2xl:h-56 image-fit">
                            <img alt="Video image" class="rounded-md"
                                src="/{{ $video['cover_image'] }}">
                        </div>
                        <a target="_blank"href="{{ $video['video_link'] }}" class="block font-medium text-base mt-5"
                            style="color: blue;height:60px">{{ $video['video_link'] }}</a>
                        <div class="text-slate-600 dark:text-slate-500 mt-2">{{ $video['title'] }}</div>
                        <div class="text-slate-600 dark:text-slate-500 mt-2">Type: {{ ucwords($video['type']) }}</div>
                    </div>
                    <div
                        class="flex justify-center lg:justify-center items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">

                        <a id="editbtn" href="javascript:;"
                        onclick="editbtn({{ $video['id'] }}, '{{ addslashes($video['video_link']) }}', '{{ addslashes($video['cover_image']) }}', '{{ addslashes($video['title']) }}', '{{ $video['type'] }}')"
                        class="flex items-center mr-3" data-tw-target="#edit-modal" data-tw-toggle="modal">
                        <i data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit
                    </a>
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
                                onclick="editVideoStatus({{ $video['id'] }},{{ $video['isActive'] }})"
                                href="$video['id']" data-tw-target="#verified">
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
                    <h2 class="font-medium text-base mr-auto">Add Training Video</h2>
                </div>
                <form action="{{ route('addTrainingVideo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="name" class="form-label">Video For</label>
                                            <select data-placeholder="Select Type" class="form-control" id="type" name="type">
                                                <option  value="user">User</option>
                                                <option  value="astrologer">{{ucfirst($professionTitle)}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Video Title</label>
                                            <input onkeypress="return validateJavascript(event);" type="text" name="title" id="title" class="form-control"
                                                placeholder="Video Title" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="video_link" class="form-label">Video Link</label>
                                            <input onkeypress="return validateJavascript(event);" type="text" name="video_link" id="video_link" class="form-control"
                                                placeholder="Video Link" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <label for="coverImage" class="form-label mt-2">Cover Image</label>
                                            <img id="thumb" width="150px" alt="coverImage" style="display:none"/>
                                            <input type="file" class="mt-2" name="cover_image" id="image"
                                                onchange="preview()" accept="image/*" required>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Save</button>
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
                    <h2 class="font-medium text-base mr-auto">Edit Training Video</h2>
                </div>
                <form action="{{ route('editTrainingVideo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="">
                                <div class="sm:grid grid-cols gap-2">

                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <label for="name" class="form-label">Video For</label>
                                                <select data-placeholder="Select Type" class="form-control" id="videotype" name="type">
                                                    <option  value="user">User</option>
                                                    <option  value="astrologer">Astrologer</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <label for="videoTitle" class="form-label">Video Title</label>
                                                <input onkeypress="return validateJavascript(event);" type="text" name="title" id="aid"
                                                    class="form-control" placeholder="Video Title" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="youtubeLink" class="form-label">Video Link</label>
                                            <input onkeypress="return validateJavascript(event);" type="text" name="video_link" id="id" class="form-control"
                                                placeholder="Name" required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                <label for="coverImage" class="form-label">Cover Image</label>
                                                <img id="thumbs" width="150px" alt="coverImage" onerror="this.style.display='none';"/>
                                                <input type="file" class="mt-2" name="cover_image" id="gid"
                                                    onchange="previews()" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
    @endif
    @if (count($videos) > 0)
        <div class="d-inline addbtn intro-y col-span-12 ">
            <nav class="w-full sm:w-auto sm:mr-auto" aria-label="adsVideo">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('getTrainingVideo', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('getTrainingVideo', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('getTrainingVideo', ['page' => $page + 1]) }}">
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
                    <form action="{{ route('statusTrainingVideo') }}" method="POST" enctype="multipart/form-data">
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
                    <form action="{{ route('deleteTrainingVideo') }}" method="POST">
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
       function editbtn(id, youtubeLink, coverImage, videoTitle, videoType) {
        $('#filed_id').val(id);
        $('#id').val(youtubeLink);
        $('#aid').val(videoTitle);
        document.getElementById("thumbs").src = "/" + coverImage;
        
        // Set the selected option in the type dropdown
        $('#videotype').val(videoType).trigger('change');
    }

        function deletebtn($id) {
            $('#del_id').val($id);
        }

        function editVideo($id, $name) {
            var id = $id;
            $fid = id;

            $('#status_id').val($fid);
            $('#id').val($name);
        }

        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
        }

        function previews() {
            document.getElementById("thumbs").style.display = "block";
            thumbs.src = URL.createObjectURL(event.target.files[0]);
        }

        function editVideoStatus($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
            $('#id').val($name);
        }
        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
