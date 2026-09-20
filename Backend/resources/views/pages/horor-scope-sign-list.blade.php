@extends('../layout/' . $layout)

@section('subhead')
    <title>HoroScope Sign</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">HoroScope Signs</h2>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report -mt-2" aria-label="horor-scope-sign">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">NAME</th>
                        <th class="whitespace-nowrap">IMAGE</th>
                        <th class="text-center whitespace-nowrap">STATUS</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($signs as $horoScope)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="flex">
                                    <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="rounded-full cursor-pointer" src="/{{ $horoScope['image'] }}"
                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                    alt="Customer image"
                                    onclick="openImage('/{{ $horoScope['image'] }}')" />
                     
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $horoScope['name'] }}</div>

                            </td>

                            <td class="w-40">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0">
                                    <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $horoScope['isActive'] ? 'checked' : '' }}
                                        onclick="editHoroScope({{ $horoScope['id'] }},'{{ $horoScope['name'] }}',{{ $horoScope['isActive'] }})"
                                        href="$horoScope['id']" data-tw-target="#verified">
                                </div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn" href="javascript:;"
                                        onclick="editbtn({{ $horoScope['id'] }} , '{{ $horoScope['name'] }}','{{ $horoScope['image'] }}')"
                                        class="flex items-center mr-3 " data-tw-target="#edit-modal"
                                        data-tw-toggle="modal"><i data-lucide="check-square"
                                            class="editbtn w-4 h-4 mr-1"></i>Edit</a>
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
            <nav class="w-full sm:w-auto sm:mr-auto" aria-label="horo-scope-sign">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('horoscopeSigns', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('horoscopeSigns', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('horoscopeSigns', ['page' => $page + 1]) }}">
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
    <div id="add-gift" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add HoroScope</h2>
                </div>
                <form id="add-data" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="Name" onkeypress="return Validate(event);" required>
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-6 py-4">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <img id="thumb" width="150px" alt="signImage" style="display:none" />
                                            <input type="file" class="mt-2" name="image" id="image"
                                                onchange="preview()" accept="image/*">
                                            <div class="text-danger print-image-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add Horor
                                    Scope</button>
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
                    <h2 class="font-medium text-base mr-auto">Edit HororScope Sign</h2>
                </div>
                <form action="{{ route('editHororScopeSignApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2 py-4">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" name="name" id="id" class="form-control"
                                                placeholder="Name" required onkeypress="return Validate(event);" required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                <img id="thumbs" width="150px" alt="signImage"
                                                    onerror="this.style.display='none';" />
                                                <input type="file" class="mt-2" name="image"
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
                    <form action="#" method="POST">
                        @csrf
                        @method('DELETE')
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
                    <form action="{{ route('horoScopeStatusApi') }}" method="POST" enctype="multipart/form-data">
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
        function editbtn($id, $name, $image) {

            var id = $id;
            var gid = $id;

            $cid = id;

            $('#filed_id').val($cid);
            $('#id').val($name);
            $('#gid').val($image);
            document.getElementById("thumbs").src = "/" + $image;
        }

        function editHoroScope($id, $name, $isActive) {
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
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        jQuery("#add-data").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addHororScopeSignApi') }}",
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
            jQuery(".print-image-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (key == 'name') {
                    jQuery(".print-name-error-msg").css('display', 'block');
                    jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'image') {
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
