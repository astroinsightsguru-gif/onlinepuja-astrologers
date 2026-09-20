@extends('../layout/' . $layout)

@section('subhead')
    <title>Team Member</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')
            ->where('name', 'Currency')
            ->select('value')
            ->first();
    @endphp
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Team List</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-team"
        class="d-inline mt-10 addbtn btn btn-primary shadow-md mr-2"
        onclick="document.getElementById('add-data').reset();">Add
        Team List</a>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if (count($teamMembers) > 0)
       <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
    <table class="table table-report -mt-2" aria-label="team">
        <thead class="sticky-top">
            <tr>
                <th class="whitespace-nowrap">#</th>
                <th class="whitespace-nowrap">PROFILE</th>
                <th class="whitespace-nowrap">NAME</th>
                <th class="text-center whitespace-nowrap">EMAIL</th>
                <th class="text-center whitespace-nowrap">CONTACT NO</th>
                <th class="text-center whitespace-nowrap">TEAM ROLE</th>
                <th class="text-center whitespace-nowrap">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach ($teamMembers as $team)
                <tr class="intro-x">
                    <td>{{ ($page - 1) * 15 + ++$no }} </td>
                    <td>
                        <div class="flex">
                            <div class="w-10 h-10 image-fit zoom-in">
                                <a href="javascript:;" 
                                   onclick="openImageModal('/{{ $team->profile }}')">
                                   <img class="rounded-full cursor-pointer" src="/{{ $team->profile }}"
                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                    alt="Customer image"
                                    onclick="openImage('/{{ $team->profile }}')" />
                
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="font-medium whitespace-nowrap">{{ $team->name }}</div>
                    </td>
                    <td class="text-center">{{ $team->email }}</td>
                    <td class="text-center">{{ $team->contactNo }}</td>
                    <td class="text-center">{{ $team->teamRole }}</td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a id="editbtn" href="javascript:;"
                                onclick="editbtn({{ $team->id }} , '{{ $team->name }}','{{ $team->profile }}','{{ $team->email }}','{{ $team->contactNo }}',{{ $team->teamRoleId }})"
                                class="flex items-center mr-3 " data-tw-target="#edit-modal"
                                data-tw-toggle="modal"><i data-lucide="check-square"
                                    class="editbtn w-4 h-4 mr-1"></i>Edit</a>
                            <a id="editbtn" href="javascript:;" onclick="delbtn({{ $team->id }},{{ $team->userId }})"
                                value="{{ $team->id }}" class="flex items-center text-danger"
                                data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal"><i
                                    data-lucide="trash-2" class="editbtn w-4 h-4 mr-1"></i>Delete</a>
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


        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('team-list', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('team-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('team-list', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <!-- END: Data List -->
    <div id="add-team" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto" onclick="addTeamList()">Add Team List</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="add-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="">
                                    <label for="post-form-3" class="form-label">Select Role</label>
                                    <select data-placeholder="Select categories" class="form-control" id="bannerTypeId"
                                        name="teamRoleId">
                                        <option value="" disabled selected>--Select Role--
                                        </option>
                                        @foreach ($teamMem as $role)
                                            <option id="teamRoleId" value={{ $role->id }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger print-teamRoleId-error-msg mb-2" style="display:none">
                                        <ul></ul>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="Name" onkeypress="return Validate(event);">
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <label for="profile" class="form-label mt-2"
                                                style="display: block;">Profile</label>
                                            <img id="thumb" width="150px" alt="team" style="display:none" />
                                            <input type="file" class="mt-2" name="profile" id="profile"
                                                onchange="preview()" class="form-control" accept="image/*">
                                            <div class="text-danger print-image-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2 py-4">
                                    <div class="input">
                                        <div>
                                            <label for="email" class="form-label">Email</label>
                                            <input onkeypress="return validateJavascript(event);" type="email" name="email" id="email" class="form-control"
                                                placeholder="example@gmail.com">
                                            <div class="text-danger print-email-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="contactNo" class="form-label">Contact No</label>
                                            <input type="text" name="contactNo" id="contactNo" class="form-control"
                                                placeholder="Contact No" onKeyDown="numbersOnly(event)" maxlength="10">
                                            <div class="text-danger print-contactNo-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2 mt-2">
                                    <div class="input">
                                        <div>
                                            <label for="password" class="form-label">Password</label>
                                            <input onkeypress="return validateJavascript(event);" type="password" name="password" id="password" class="form-control"
                                                placeholder="******">
                                            <div class="text-danger print-password-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit"
                                    class="btn btn-primary shadow-md mr-2 btn-submit">Add
                                    Team Member</button>
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
                    <h2 class="font-medium text-base mr-auto">Edit Team Member</h2>
                </div>
                <form id="edit-data" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="">
                                        <label for="post-form-3" class="form-label">Select Role</label>
                                        <select data-placeholder="Select categories" class="form-control" id="teamRoleId"
                                            name="teamRoleId">
                                            <option value="" disabled selected>--Select Role--
                                            </option>
                                            @foreach ($teamMem as $role)
                                                <option value={{ $role->id }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger print-teamRoleId-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" name="name" id="id" class="form-control"
                                                placeholder="Name" onkeypress="return Validate(event);">
                                                <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                <label for="image" class="form-label"
                                                    style="display: block;">Profile</label>
                                                <img id="thumbs" width="150px" alt="team"
                                                    onerror="this.style.display='none';">
                                                <input type="file" class="mt-2" name="profile"
                                                    onchange="previews()" accept="image/*">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:grid grid-cols gap-2 py-4">
                                        <div class="input">
                                            <div>
                                                <label for="email" class="form-label">Email</label>
                                                <input onkeypress="return validateJavascript(event);" type="email" name="email" id="aid" class="form-control"
                                                    placeholder="example@gmail.com">
                                                <div class="text-danger print-email-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <label for="contactNo" class="form-label">Contact No</label>
                                                <input type="text" name="contactNo" id="cid"
                                                    class="form-control" placeholder="Contact No"
                                                    onKeyDown="numbersOnly(event)" maxlength="10">
                                                <div class="text-danger print-contactNo-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:grid grid-cols gap-2 mt-2">
                                        <div class="input">
                                            <div>
                                                <label for="password" class="form-label">Password</label>
                                                <input onkeypress="return validateJavascript(event);" type="password" name="password" id="epassword" class="form-control"
                                                    placeholder="Enter new password if you want to change old password">
                                                <div class="text-danger print-password-error-msg mb-2" style="display:none">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:grid grid-cols gap-2 mt-2">
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
    <!-- BEGIN: Delete Confirmation Modal -->
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

                    <form action="{{ route('deleteMember') }} " method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="userId" name="userId">
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
                    <form action="{{ route('giftStatusApi') }}" method="POST" enctype="multipart/form-data">
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
        function editbtn($id, $name, $profile, $email, $contactNo, $teamRoleId) {
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery(".print-contactNo-error-msg").find("ul").html('');
            jQuery(".print-email-error-msg").find("ul").html('');
            jQuery(".print-teamRoleId-error-msg").find("ul").html('');
            jQuery(".print-password-error-msg").find("ul").html('');

            var id = $id;
            var gid = $id;
            var aid = $id;
            $cid = id;

            $('#filed_id').val($cid);
            $('#id').val($name);
            $('#aid').val($email);
            $('#cid').val($contactNo);
            $('#teamRoleId').val($teamRoleId);
            document.getElementById("thumbs").src = "/" + $profile;
        }

        function delbtn($id, $userId) {
            $('#del_id').val($id);
            // $('#id').val($id);
            $('#userId').val($userId);
        }

        function editGift($id, $name, $isActive) {
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
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
            jQuery(".print-image-error-msg").find("ul").html('');
        }

        function previews() {
            document.getElementById("thumbs").style.display = "block";
            thumbs.src = URL.createObjectURL(event.target.files[0]);
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }


        function numbersOnly(e) {
            var keycode = e.keyCode;
            if (!(keycode != 9 && e.shiftKey == false && (keycode == 46 || keycode == 8 || keycode ==
                    37 ||
                    keycode == 39 || (keycode >=
                        48 && keycode <= 57)))) {
                e.preventDefault();
            }
        }

        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        jQuery("#add-data").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addTeamApi') }}",
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
        jQuery("#edit-data").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('editTeamMemberApi') }}",
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

        function printErrorMsg(msg) {
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery(".print-contactNo-error-msg").find("ul").html('');
            jQuery(".print-email-error-msg").find("ul").html('');
            jQuery(".print-teamRoleId-error-msg").find("ul").html('');
            jQuery(".print-password-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (key == 'name') {
                    jQuery(".print-name-error-msg").css('display', 'block');
                    jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'contactNo') {
                    jQuery(".print-contactNo-error-msg").css('display', 'block');
                    jQuery(".print-contactNo-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'email') {
                    jQuery(".print-email-error-msg").css('display', 'block');
                    jQuery(".print-email-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'teamRoleId') {
                    jQuery(".print-teamRoleId-error-msg").css('display', 'block');
                    jQuery(".print-teamRoleId-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'password') {
                    jQuery(".print-password-error-msg").css('display', 'block');
                    jQuery(".print-password-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                else {
                    toastr.warning(value);
                }

            });
        }

        function addTeamList() {
            document.getElementById("thumbs").style.display = "none";
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery(".print-contactNo-error-msg").find("ul").html('');
            jQuery(".print-email-error-msg").find("ul").html('');
            jQuery(".print-teamRoleId-error-msg").find("ul").html('');
            jQuery(".print-password-error-msg").find("ul").html('');
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
