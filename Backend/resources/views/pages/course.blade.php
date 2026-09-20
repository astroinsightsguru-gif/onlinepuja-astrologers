@extends('../layout/' . $layout)

@section('subhead')
    <title>Course</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Course</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-gift"
        class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn"
        onclick="document.getElementById('add-data').reset();document.getElementById('thumb').style.display = 'none'">Add
        Course</a>
    <div class="grid grid-cols-12 gap-6 ">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>

    {{-- ✅ Store badge data safely in JS to avoid HTML encoding issues --}}
    {{-- course_badge is stored as a JSON string in DB e.g. '["testing"]'     --}}
    {{-- We json_decode it first so @json() outputs a real JS array, not a    --}}
    {{-- double-encoded string like "[\"testing\"]"                           --}}
    <script>
        window.courseBadges = {};
        window.courseImages = {};
    </script>

    @foreach ($courses as $course)
        @php
            $badgeDecoded = is_string($course['course_badge'])
                ? json_decode($course['course_badge'], true)
                : ($course['course_badge'] ?? []);
            $badgeDecoded = is_array($badgeDecoded) ? $badgeDecoded : [];
        @endphp
        <script>
            window.courseBadges[{{ $course['id'] }}] = @json($badgeDecoded);
            window.courseImages[{{ $course['id'] }}]  = "{{ $course['image'] ? '/' . ltrim($course['image'], '/') : '' }}";
        </script>
    @endforeach

    <!-- BEGIN: Data List -->
    @if (count($courses) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report -mt-2" aria-label="astrologer-category">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">IMAGE</th>
                        <th class="whitespace-nowrap">NAME</th>
                        <th class="whitespace-nowrap">PRICE(INR)</th>
                        <th class="whitespace-nowrap">PRICE(USD)</th>
                        <th class="whitespace-nowrap">CATEGORY</th>
                        <th class="text-center whitespace-nowrap">STATUS</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($courses as $course)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="flex">
                                    <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="rounded-full"
                                            src="/{{ $course['image'] }}"
                                            onerror="this.onerror=null;this.src='/build/assets/images/default.jpg';"
                                            alt="{{ ucfirst($professionTitle) }} image" />
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $course['name'] }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $course['course_price'] }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $course['course_price_usd'] ?: ' - -' }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $course->category->name }}</div>
                            </td>
                            <td class="w-40">
                                <div class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                                    <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $course['isActive'] ? 'checked' : '' }}
                                        onclick="editAstrologyCategory({{ $course['id'] }},{{ $course['isActive'] }})"
                                        href="{{ $course['id'] }}" data-tw-target="#verified">
                                </div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn_{{ $course['id'] }}"
                                        href="javascript:;"
                                        onclick="editbtn(this)"
                                        data-id="{{ $course['id'] }}"
                                        data-name="{{ $course['name'] }}"
                                        data-description="{{ $course['description'] }}"
                                        data-category="{{ $course['course_category_id'] }}"
                                        data-price="{{ $course['course_price'] }}"
                                        data-price-usd="{{ $course['course_price_usd'] }}"
                                        data-image="{{ $course['image'] }}"
                                        class="flex items-center mr-3"
                                        data-tw-target="#edit-modal"
                                        data-tw-toggle="modal">
                                        <i data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit
                                    </a>
                                    <a type="button" href="javascript:;" class="flex items-center deletebtn text-danger"
                                        data-tw-toggle="modal" data-tw-target="#deleteModal"
                                        onclick="delbtn({{ $course['id'] }})">
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
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }}">
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

    <!-- BEGIN: Add Course Modal -->
    <div id="add-gift" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Course</h2>
                </div>
                <form id="add-data" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-0">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="course_category_id" class="form-label">Course Category</label>
                                            <select data-placeholder="Select categories" class="form-control"
                                                id="course_category_id" name="course_category_id">
                                                <option value="" disabled selected required>--Select Category--</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2">
                                        <div>
                                            <label for="name" class="form-label">Course Name</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="Name" onkeypress="return Validate(event);" required>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2">
                                        <div>
                                            <label for="course_price" class="form-label">Course Price (INR)</label>
                                            <input type="number" name="course_price" id="course_price"
                                                class="form-control" placeholder="Price" required>
                                            <div class="text-danger print-course_price-error-msg mb-2"
                                                style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2">
                                        <div>
                                            <label for="course_price_usd" class="form-label">Course Price (USD)</label>
                                            <input type="number" name="course_price_usd" id="course_price_usd"
                                                class="form-control" placeholder="Price" required>
                                            <div class="text-danger print-course_price_usd-error-msg mb-2"
                                                style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2">
                                        <div>
                                            <label for="description" class="form-label">Course Description</label>
                                            <textarea id="description" required class="form-control" name="description"
                                                placeholder="description" minlength="10"></textarea>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2">
                                        <div>
                                            <label for="course_badge" class="form-label">Course Badge</label>
                                            <select name="course_badge[]" id="course_badge" class="form-control"
                                                multiple="multiple">
                                            </select>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-6 py-4">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <label for="image" class="form-label">Course Image</label>
                                            <img id="thumb" width="150px" alt="course-image" style="display:none" />
                                            <input type="file" class="mt-2" name="image" id="image"
                                                onchange="preview()" accept="image/*" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <button class="btn btn-submit btn-primary shadow-md mr-2">Add Course</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Add Course Modal -->

    <!-- BEGIN: Edit Course Modal -->
    <div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Course</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{ route('editCourse') }}">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-0">
                                <div class="sm:grid grid-cols gap-2 py-0">

                                    <div class="input">
                                        <div>
                                            <label for="editcourse_category_id" class="form-label">Course Category</label>
                                            <select data-placeholder="Select categories" class="form-control"
                                                id="editcourse_category_id" name="course_category_id">
                                                <option value="" disabled selected required>--Select Category--</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input mt-2">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="editName" class="form-label">Course Name</label>
                                            <input type="text" name="name" id="editName" class="form-control"
                                                placeholder="Name" required onkeypress="return Validate(event);">
                                            <div class="text-danger print-edit-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:grid grid-cols gap-2 mt-2">
                                        <div class="input">
                                            <div>
                                                <label for="editcourse_price" class="form-label">Course Price (INR)</label>
                                                <input type="number" name="course_price" id="editcourse_price"
                                                    class="form-control" placeholder="Price" required>
                                                <div class="text-danger print-course_price-error-msg mb-2"
                                                    style="display:none">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:grid grid-cols gap-2 mt-2">
                                        <div class="input">
                                            <div>
                                                <label for="editcourse_price_usd" class="form-label">Course Price (USD)</label>
                                                <input type="number" name="course_price_usd" id="editcourse_price_usd"
                                                    class="form-control" placeholder="Price" required>
                                                <div class="text-danger print-course_price_usd-error-msg mb-2"
                                                    style="display:none">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input mt-2">
                                        <div>
                                            <label for="editdescription" class="form-label">Course Description</label>
                                            <textarea id="editdescription" required class="form-control" name="description"
                                                placeholder="description" minlength="10"></textarea>
                                            <div class="text-danger print-edit-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:grid grid-cols gap-2 mt-2">
                                        <div class="input">
                                            <div>
                                                <label for="editcourse_badge" class="form-label">Course Badge</label>
                                                <select name="course_badge[]" id="editcourse_badge" class="form-control"
                                                    multiple="multiple">
                                                </select>
                                                <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 gap-6 mt-2">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                {{-- ✅ Image preview for edit --}}
                                                <img id="thumbs" width="150px" alt="course-image"
                                                    style="display:none; margin-bottom:8px;"
                                                    onerror="this.style.display='none';" />
                                                <input type="file" class="mt-2" name="image" id="editImageInput"
                                                    onchange="previews()" accept="image/*">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="mt-5">
                                <button class="btn edit-btn-submit btn-primary shadow-md mr-2">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Edit Course Modal -->

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
                    <form action="{{ route('deleteCourse') }}" method="POST">
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
    <!-- END: Delete Confirmation Modal -->

    <!-- BEGIN: Status Toggle Modal -->
    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('CourseStatus') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="status_id" name="status_id">
                        <div class="px-5 pb-8 text-center">
                            <button class="btn btn-primary mr-3" id="btnActive">Yes, Active it!</button>
                            <a type="button" data-tw-dismiss="modal" class="btn btn-secondary btn-submit w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Status Toggle Modal -->

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    jQuery(document).ready(function () {
        // Badge selects
        jQuery('#course_badge').select2({
            tags: true,
            placeholder: "Add badge",
            tokenSeparators: [','],
            width: '100%'
        });
        jQuery('#editcourse_badge').select2({
            tags: true,
            placeholder: "Add badge",
            tokenSeparators: [','],
            width: '100%'
        });

        // Category selects — must be Select2 so .trigger('change') works
        jQuery('#course_category_id').select2({
            placeholder: "Select categories",
            width: '100%'
        });
        jQuery('#editcourse_category_id').select2({
            placeholder: "Select categories",
            width: '100%'
        });
    });
</script>

<script type="text/javascript">
    @if (Session::has('error'))
        toastr.options = { "closeButton": true, "progressBar": true }
        toastr.warning("{{ session('error') }}");
    @endif

    function delbtn($id) {
        $('#del_id').val($id);
    }

    function editbtn(element) {
        var $el              = $(element);
        var id               = $el.data('id');
        var name             = $el.data('name');
        var description      = $el.data('description');
        var course_category  = $el.data('category');
        var course_price     = $el.data('price');
        var course_price_usd = $el.data('price-usd');

        // Populate text fields
        $('#filed_id').val(id);
        $('#editName').val(name);
        $('#editdescription').val(description);
        $('#editcourse_price').val(course_price);
        $('#editcourse_price_usd').val(course_price_usd);

        // Set category — set val first, then trigger separately (avoids "trigger is not a function" if Select2 not ready)
        var $catSelect = jQuery('#editcourse_category_id');
        $catSelect.val(course_category);
        if (typeof $catSelect.trigger === 'function') {
            $catSelect.trigger('change');
        }

        // ✅ BADGES — window.courseBadges[id] is already a clean JS array
        //    e.g. ["testing", "offer"] — no JSON.parse needed
        var $badgeSelect = jQuery('#editcourse_badge');
        $badgeSelect.empty().trigger('change');

        var badgeData = window.courseBadges[id];

        if (Array.isArray(badgeData) && badgeData.length > 0) {
            var selectedValues = [];
            badgeData.forEach(function (badge) {
                if (!badge) return;
                var val  = String(badge).trim();
                var text = String(badge).trim();
                if (val) {
                    $badgeSelect.append(new Option(text, val, true, true));
                    selectedValues.push(val);
                }
            });
            $badgeSelect.val(selectedValues).trigger('change');
        }

        // ✅ IMAGE — use window.courseImages which already has the correct /storage/... path
        //    Set image AFTER modal is visible to avoid onerror firing on empty src
        var thumbsImg = document.getElementById('thumbs');
        thumbsImg.src = '';
        thumbsImg.style.display = 'none';

        var imageSrc = window.courseImages[id];
        if (imageSrc) {
            // Wait for modal to be shown before setting src (avoids onerror race condition)
            jQuery('#edit-modal').one('shown.tw.modal show.bs.modal shown.bs.modal', function () {
                thumbsImg.onerror = function () { this.style.display = 'none'; };
                thumbsImg.onload  = function () { this.style.display = 'block'; };
                thumbsImg.src = imageSrc;
            });
            // Fallback: set after short delay in case modal event doesn't fire
            setTimeout(function () {
                if (!thumbsImg.src || thumbsImg.src === window.location.href) {
                    thumbsImg.onerror = function () { this.style.display = 'none'; };
                    thumbsImg.onload  = function () { this.style.display = 'block'; };
                    thumbsImg.src = imageSrc;
                }
            }, 300);
        }
    }

    function Validate(event) {
        var regex = new RegExp("^[0-9\\-!@#$%&<>*?]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }

    function editAstrologyCategory($id, $isActive) {
        var active = $isActive ? 'Inactive' : 'Active';
        document.getElementById('active').innerHTML = "You want to " + active;
        document.getElementById('btnActive').innerHTML = "Yes, " + active + " it";
        $('#status_id').val($id);
    }

    function preview() {
        var thumb = document.getElementById('thumb');
        thumb.style.display = 'block';
        thumb.src = URL.createObjectURL(event.target.files[0]);
    }

    function previews() {
        var thumbs = document.getElementById('thumbs');
        thumbs.style.display = 'block';
        thumbs.src = URL.createObjectURL(event.target.files[0]);
    }
</script>

<script type="module">
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery("#add-data").submit(function (e) {
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: "{{ route('addCourse') }}",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (data) {
                if (jQuery.isEmptyObject(data.error)) {
                    toastr.options = { "closeButton": true, "progressBar": true };
                    location.reload();
                } else {
                    printErrorMsg(data.error);
                }
            }
        });
    });

    function printErrorMsg(msg) {
        jQuery(".print-name-error-msg").find("ul").html('');
        jQuery.each(msg, function (key, value) {
            if (key === 'name') {
                jQuery(".print-name-error-msg").css('display', 'block');
                jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
            } else {
                toastr.warning(value);
            }
        });
    }

    function printEditErrorMsg(msg) {
        jQuery(".print-edit-name-error-msg").find("ul").html('');
        jQuery.each(msg, function (key, value) {
            if (key === 'name') {
                jQuery(".print-edit-name-error-msg").css('display', 'block');
                jQuery(".print-edit-name-error-msg").find("ul").append('<li>' + value + '</li>');
            }
        });
    }
</script>

<script>
    $(window).on('load', function () {
        $('.loader').hide();
    });
</script>
@endsection