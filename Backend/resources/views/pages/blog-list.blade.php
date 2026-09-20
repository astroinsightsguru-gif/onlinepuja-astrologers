@extends('../layout/' . $layout)

@section('subhead')
    <title>Blog</title>
@endsection

@section('subcontent')
<style>
    .visiable
    {
        background: #441cee;
        color: white;
        font-size: 22px;
        font-weight: bold;
    }
</style>
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Blogs</h2>
    <a href="javascript:;" data-tw-toggle="modal" onclick="showEditor()" data-tw-target="#add-blog"
        class="d-inline btn btn-primary shadow-md mr-2 mt-10 addbtn">Add
        Blog
    </a>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">


            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('blogs') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('blogs') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
        </div>
    </div>



                        @if ($totalRecords > 0)
                            <div class="intro-y grid grid-cols-12 gap-6 mt-5 grid-table">
                                @foreach ($blogs as $blog)
                                    <div class="intro-y col-span-12 md:col-span-6 box">
                                        <div
                        class="h-[320px] before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black/90 before:to-black/10 image-fit">
                        @if ($blog->blogImage && 
                            (str_ends_with(strtolower($blog->blogImage), 'jpg') ||
                             str_ends_with(strtolower($blog->blogImage), 'jpeg') ||
                             str_ends_with(strtolower($blog->blogImage), 'png') ||
                             str_ends_with(strtolower($blog->blogImage), 'gif') ||
                             str_ends_with(strtolower($blog->blogImage), 'webp')
                            )
                        )
                            {{-- Show Image --}}
                            <img class="rounded-t-md"
                                 src="{{ Str::startsWith($blog->blogImage, ['http://','https://']) ? $blog->blogImage : '/' . $blog->blogImage }}"
                                 onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                 alt="Blog image"
                                 onclick="openImage('{{ $blog->blogImage }}')">
                        @else
                            {{-- Show Video if not image --}}
                            <video controls style="height:100%;width:100%;object-fit:cover;position:absolute;z-index:10">
                                <source src="/{{ $blog->blogImage }}" type="video/mp4">
                            </video>
                        @endif

                        <div class="absolute w-full flex items-center px-5 pt-6 z-10">
                            <div class="ml-3 text-white mr-auto">
                                <a href="" class="font-medium">{{ $blog['author'] }}</a>
                                <div class="text-xs mt-0.5">
                                    @if ($blog['postedOn'])
                                        {{ date('d-m-Y', strtotime($blog['postedOn'])) }}
                                    @endif
                                </div>
                            </div>
                            <div class="dropdown ml-3">
                                <a href="javascript:;"
                                    class="visiable bg-white/20 dropdown-toggle w-8 h-8 flex items-center justify-center rounded-full"
                                    aria-expanded="false" data-tw-toggle="dropdown">
                                    <i data-lucide="more-vertical" class="w-4 h-4 text-white"></i>
                                </a>
                                <div class="dropdown-menu w-40">
                                    <ul class="dropdown-content">
                                        <li>
                                            <a href="{{ route('getBlogById', $blog['id']) }}" class="dropdown-item">
                                                <i data-lucide="eye" class="w-4 h-4 mr-2"></i> View Blog
                                            </a>
                                        </li>
                                        <li>
                                            <a id="editbtn" href="javascript:;"
                                                onclick="editbtn({{ $blog['id'] }},'{{ $blog['title'] }}', '{{ $blog['postedOn'] }}','{{ $blog['author'] }}',{{ json_encode($blog['description']) }},'{{ $blog['blogImage'] }}','{{ $blog['extension'] }}','{{ $blog['previewImage'] }}')"
                                                onclick="showEditor()" class="dropdown-item" data-tw-target="#edit-modal"
                                                data-tw-toggle="modal" data-tw-dismiss="dropdown"><i data-lucide="check-square"
                                                    class="editbtn w-4 h-4 mr-2"></i>Edit
                                                Blog</a>
                                        </li>
                                        <li>
                                            <a id="deletebtn" href="javascript:;"
                                               onclick="deletebtn({{ $blog['id'] }})"
                                               class="dropdown-item" 
                                               data-tw-target="#delete-confirmation-modal"
                                               data-tw-toggle="modal"
                                               data-tw-dismiss="dropdown"> <!-- Add this line -->
                                                <i data-lucide="trash-2" class="editbtn w-4 h-4 mr-2"></i>Delete Blog
                                            </a>
                                        </li>
 
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="px-5 pt-5 text-slate-600 dark:text-slate-500">
                        <h2 class="font-medium text-xl">{{ $blog['title'] }}</h2>
                    </div>
                    <div class="p-5 text-slate-600 dark:text-slate-500">{!! $blog['description'] !!}</div>
                    <div
                        class="px-5 pt-3 pb-5 border-t border-slate-200/60 dark:border-darkmode-400 intro-y flex flex-col sm:flex-row items-center">
                        <div class="w-full flex text-slate-500 text-xs sm:text-sm">

                            <div class="mr-2">
                                Views: <span class="font-medium">{{ $blog['viewer'] ? $blog['viewer'] : '0' }}</span>
                            </div>
                        </div>
                        <div
                            class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                    mt-3 sm:mt-0">
                            <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                href="javascript:;" data-tw-toggle="modal" data-onstyle="success" data-offstyle="danger"
                                data-toggle="toggle" data-on="Active" data-off="InActive"
                                {{ $blog['isActive'] ? 'checked' : '' }}
                                onclick="editBlogStatus({{ $blog['id'] }},{{ $blog['isActive'] }})" href="$blog['id']"
                                data-tw-target="#verified">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12 ">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('blogs', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('blogs', ['page' => $i + 1, 'searchString' => $searchString]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('blogs', ['page' => $page + 1, 'searchString' => $searchString]) }}">
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
    <div id="add-blog" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Blog</h2>
                </div>
                <form data-single="true" method="POST" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Title" required onkeypress="return Validate(event);">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="sm:grid grid-cols-2 gap-2">
                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="postedOn" class="form-label">Posted On</label>
                                                    <input type="date" id="postedOn" name="postedOn"
                                                        class="form-control" placeholder="PostedOn"
                                                        aria-describedby="input-group-3" required>
                                                </div>
                                            </div>
                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="author" class="form-label">Posted By</label>
                                                    <input type="text" name="author" id="author"
                                                        class="form-control" placeholder="Posted By" required
                                                        onkeypress="return Validate(event);">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input mt-2">
                                            <div>
                                                <label for="image" class="form-label">Blog Image/Video</label>
                                                <img id="thumb" width="150px" style="margin-bottom: 10px"
                                                    alt="" />
                                                <video id="myVideo" style="width:150px;object-fit:cover;">
                                                    <source id="addBlogImage" type="video/mp4">
                                                    <track label="English" kind="subtitles" srclang="en" default />
                                                </video>
                                                <input type="file" name="blogImage" id="blogImage"
                                                    onchange="preview()" required accept="video/mp4,image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control ml-3" id="description" name="description"></textarea>
                                    </div>

                                    {{-- <div class="input mt-2">
                                        <div>
                                            <label for="image" class="form-label">Preview Image</label>
                                            <img id="videopre" width="150px" style="margin-bottom: 10px"
                                                alt="" />
                                            <input type="file" name="previewImage" id="previewImage"
                                                onchange="videoPreview()" accept="image/*">
                                        </div>
                                    </div> --}}
                                    <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add
                                            Blog</button>
                                    </div>
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
                    <h2 class="font-medium text-base mr-auto">Edit Blog</h2>
                </div>
                <form id="edit-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="etitle" class="form-control"
                                                placeholder="Name" required onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="sm:grid grid-cols-2 gap-2">

                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="postedOn" class="form-label">Posted On</label>
                                                    <input type="date" id="epostedOn" name="postedOn"
                                                        class="form-control" placeholder="PostedOn"
                                                        aria-describedby="input-group-3" required>
                                                </div>
                                            </div>
                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="author" class="form-label">Posted By</label>
                                                    <input type="text" name="author" id="eauthor"
                                                        class="form-control" placeholder="Posted By" required
                                                        onkeypress="return Validate(event);">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="input mt-2">
                                            <div>
                                                <label for="image" class="form-label">Blog Image/Video</label>
                                                <img id="thumbs" width="150px"
                                                    style="margin-bottom: 10px;display:none" alt="" />
                                                <video controls id="editMyVideo"
                                                    style="width:150px;object-fit:cover;display:none" preload="metadata">
                                                    <source id="blogvideo" type="video/mp4">
                                                    <track label="English" kind="subtitles" srclang="en" default />
                                                </video>
                                                <input type="file" name="eblogImage" id="eblogImage"
                                                    onchange="previews()" accept="video/mp4,image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control ml-3" id="editdescription" name="editdescription"></textarea>
                                    </div>
                                    {{-- <div class="input mt-2">
                                        <div>
                                            <label for="image" class="form-label">Preview Image</label>
                                            <img id="videopreviews" width="150px" style="margin-bottom: 10px"
                                                alt="" />
                                            <input type="file" name="previewImages" id="previewImages"
                                                onchange="editVideoPreviews()" accept="image/*">
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="mt-5"><button
                                    type="submit"class="btn btn-primary shadow-md mr-2">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
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
                    <form action="{{ route('blogStatusApi') }}" method="POST" enctype="multipart/form-data">
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

                    <form action="{{ route('deleteBlog') }} " method="POST">
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
    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        var spinner = $('.loader');

        function editbtn($id, $title, $postedOn, $author, $description, $blogImage, $extension, $previewImage) {
            $('#filed_id').val($id);
            $('#etitle').val($title);
            $('#eauthor').val($author);
            if ($postedOn) {
                var newdate = $postedOn.split("-");
                var date = newdate[2].split(" ");
                date = newdate[0] + '-' + newdate[1] + '-' + date[0]
            } else {
                date = null;
            }
            $('#epostedOn').val(date);
            var editor = CKEDITOR.instances['editdescription'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdescription');
            var editor = CKEDITOR.instances['editdescription'];
            CKEDITOR.instances['editdescription'].setData($description)
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png') {
                document.getElementById("thumbs").style.display = "block";
                document.getElementById("editMyVideo").style.display = "none";
                document.getElementById("thumbs").src = "/" + $blogImage;
            } else {
                document.getElementById("editMyVideo").style.display = "block";
                document.getElementById("thumbs").style.display = "none";
                blogvideo.src = "/" + $blogImage;
                editMyVideo.load();
                editMyVideo.onended = function() {
                    URL.revokeObjectURL(editMyVideo.currentSrc);
                };
                document.getElementById("editMyVideo").controls = true;
                document.getElementById("videopreviews").src = "/" + $previewImage;
            }
        }


        function editBlogStatus($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
            $('#id').val($name);
        }


        function preview() {
            if (document.querySelector('#blogImage').value.split('.').pop() == 'mp4') {
                document.getElementById("myVideo").style.display = "block";
                document.getElementById("thumb").style.display = "none";
                addBlogImage.src = URL.createObjectURL(event.target.files[0]);
                myVideo.load();
                myVideo.onended = function() {
                    URL.revokeObjectURL(myVideo.currentSrc);
                };
                document.getElementById("myVideo").controls = true;
            } else {
                document.getElementById("thumb").style.display = "block";
                thumb.src = URL.createObjectURL(event.target.files[0]);
                document.getElementById("myVideo").style.display = "none";
                addBlogImage.src = null;
            }
        }

        function previews() {
            if (document.querySelector('#eblogImage').value.split('.').pop() == 'mp4') {
                document.getElementById("editMyVideo").style.display = "block";
                document.getElementById("thumbs").style.display = "none";
                blogvideo.src = URL.createObjectURL(event.target.files[0]);
                editMyVideo.load();
                editMyVideo.onended = function() {
                    URL.revokeObjectURL(editMyVideo.currentSrc);
                };
                document.getElementById("editMyVideo").controls = true;
            } else {
                document.getElementById("thumbs").style.display = "block";
                thumbs.src = URL.createObjectURL(event.target.files[0]);
                document.getElementById("editMyVideo").style.display = "none";
                blogvideo.src = null;
                videopreviews.src = null;
            }
        }

        function videoPreview() {
            videopre.src = URL.createObjectURL(event.target.files[0]);

        }

        function editVideoPreviews() {
            videopreviews.src = URL.createObjectURL(event.target.files[0]);

        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
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

        function deletebtn($id) {
            $('#del_id').val($id);
        }

        jQuery(function() {
            jQuery('#edit-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                data.append('editdescription', CKEDITOR.instances['editdescription'].getData());
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('editBlogApi') }}",
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
                data.append('description', CKEDITOR.instances['description'].getData());
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('addBlogApi') }}",
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
        });
    </script>
@endsection
