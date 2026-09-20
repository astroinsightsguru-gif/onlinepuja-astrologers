@extends('../layout/' . $layout)

@section('subhead')
    <title>Puja Category</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Puja Categories</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-gift"
        class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn"
        onclick="document.getElementById('add-data').reset();document.getElementById('thumb').style.display = 'none'">Add
        Puja Category</a>

    <!-- BEGIN: Grid Data List -->
    @if (count($categories) > 0)
        <div class="grid grid-cols-12 gap-6 mt-5 grid-table">
            @foreach ($categories as $astroCat)
                <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                    <div class="box">
                        <div class="p-5">
                            <div
                                class="h-40 2xl:h-56 image-fit rounded-md overflow-hidden before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black before:to-black/10">
                                <img alt="Category image" class="rounded-md" src="{{ Str::startsWith($astroCat->image, ['http://','https://']) ? $astroCat->image : '/' . $astroCat->image }}"
                                    onerror="this.src='/build/assets/images/person.png'">
                                <div class="absolute bottom-0 text-white px-5 pb-6 z-10">
                                    <a href="javascript:;" class="block font-medium text-base">{{ $astroCat['name'] }}</a>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-center lg:justify-center items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">

                            <a id="editbtn" href="javascript:;"
                                onclick="editbtn({{ $astroCat['id'] }} , '{{ $astroCat['name'] }}','{{ $astroCat['image'] }}')"
                                class="flex items-center mr-3" data-tw-target="#edit-modal" data-tw-toggle="modal">
                                <i data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit
                            </a>

                            <div class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                                <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                    href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                    data-offstyle="danger" data-on="Active" data-off="InActive"
                                    {{ $astroCat['isActive'] ? 'checked' : '' }}
                                    onclick="editAstrologyCategory(`{{ $astroCat['id'] }}`,`{{ $astroCat['isActive'] }}`)"
                                    data-tw-target="#verified">
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
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <!-- END: Grid Data List -->

    <!-- Add Puja Category Modal -->
    <div id="add-gift" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Puja Category</h2>
                </div>
                <form id="add-data" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5">
                        <div class="mt-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name"
                                onkeypress="return Validate(event);" required>
                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                <ul></ul>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="image" class="form-label">Category Image</label>
                            <img id="thumb" width="150px" alt="astrologer-category" style="display:none">
                            <input type="file" class="mt-2" name="image" id="image" onchange="preview()"
                                accept="image/*" required>
                        </div>
                        <div class="mt-5">
                            <button class="btn btn-submit btn-primary shadow-md mr-2">Add Puja Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Puja Category Modal -->
    <div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Puja Category</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{ route('editPujaCategory') }}">
                    @csrf
                    <div class="p-5">
                        <input type="hidden" id="filed_id" name="filed_id">
                        <div class="mt-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" name="name" id="editName" class="form-control" placeholder="Name"
                                onkeypress="return Validate(event);" required>
                            <div class="text-danger print-edit-name-error-msg mb-2" style="display:none">
                                <ul></ul>
                            </div>
                        </div>
                        <div class="mt-4">
                            <img id="thumbs" width="150px" alt="astrologer-category" onerror="this.style.display='none';">
                            <input type="file" class="mt-2" name="image" onchange="previews()" accept="image/*">
                        </div>
                        <div class="mt-5">
                            <button class="btn edit-btn-submit btn-primary shadow-md mr-2">Save</button>
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
                    <form action="{{ route('PujaCategoryStatus') }}" method="POST"
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
@endsection

@section('script')
<script type="text/javascript">
    @if (Session::has('error'))
        toastr.options = { "closeButton": true, "progressBar": true }
        toastr.warning("{{ session('error') }}");
    @endif

    function editbtn($id, $name, $image) {
        $('#filed_id').val($id);
        $('#editName').val($name);
        document.getElementById("thumbs").src = "/" + $image;
    }

    function Validate(event) {
        var regex = new RegExp("^[0-9-!@#$%&<>*?]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) { event.preventDefault(); return false; }
    }

    function editAstrologyCategory($id, $isActive) {
        var active = $isActive ? 'Inactive' : 'Active';
        document.getElementById('active').innerHTML = "You want to " + active;
        document.getElementById('btnActive').innerHTML = "Yes, " + active + " it";
        $('#status_id').val($id);
    }

    function preview() { document.getElementById("thumb").style.display = "block"; thumb.src = URL.createObjectURL(event.target.files[0]); }
    function previews() { document.getElementById("thumbs").style.display = "block"; thumbs.src = URL.createObjectURL(event.target.files[0]); }

    function openImage(src) { document.getElementById("viewImage").src = src; document.getElementById("imageViewer").classList.remove("hidden"); document.body.style.overflow = "hidden"; }
    function closeImage() { document.getElementById("imageViewer").classList.add("hidden"); document.body.style.overflow = "auto"; }

    $(window).on('load', function() { $('.loader').hide(); });
</script>

<script type="module">
    jQuery.ajaxSetup({ headers:{ 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content') } })
    jQuery("#add-data").submit(function(e) {
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: "{{ route('addPujaCategory') }}",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(data) {
                if (jQuery.isEmptyObject(data.error)) {
                    toastr.options = { "closeButton": true, "progressBar": true }
                    location.reload();
                } else { printErrorMsg(data.error); }
            }
        });
    });

    function printErrorMsg(msg) {
        jQuery(".print-name-error-msg").find("ul").html('');
        jQuery.each(msg, function(key, value) {
            if(key == 'name') {
                jQuery(".print-name-error-msg").css('display','block');
                jQuery(".print-name-error-msg").find("ul").append('<li>'+value+'</li>');
            } else { toastr.warning(value) }
        });
    }

    function printEditErrorMsg(msg) {
        jQuery(".print-edit-name-error-msg").find("ul").html('');
        jQuery.each(msg, function(key, value) {
            if(key == 'name') {
                jQuery(".print-edit-name-error-msg").css('display','block');
                jQuery(".print-edit-name-error-msg").find("ul").append('<li>'+value+'</li>');
            }
        });
    }
</script>
@endsection
