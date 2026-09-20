@extends('../layout/' . $layout)

@section('subhead')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"  ></script>
    <title>Skills</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Skills</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-skill"
        class="d-inline mt-10 btn btn-primary shadow-md mr-2 addbtn">Add
        Skill</a>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if (count($skills) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report" aria-label="skill">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">NAME</th>
                        <th class="text-center whitespace-nowrap">STATUS</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($skills as $item)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item['name'] }}</div>
                            </td>
                            <td class="w-40">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0">
                                    <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $item['isActive'] ? 'checked' : '' }}
                                        onclick="editSkill({{ $item['id'] }},'{{ $item['name'] }}',{{ $item['isActive'] }})"
                                        href="$item['id']" data-tw-target="#verified" id="switch">
                                </div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn" href="javascript:;"
                                        onclick="editbtn({{ $item['id'] }} , '{{ $item['name'] }}')"
                                        value="{{ $item['name'] }}" class="flex items-center mr-3 "
                                        data-tw-target="#edit-modal" data-tw-toggle="modal"><i data-lucide="check-square"
                                            class="editbtn w-4 h-4 mr-1"></i>Edit</a>
                                    <a id="editbtn" href="javascript:;" onclick="delbtn({{ $item['id'] }})"
                                        value="{{ $item['id'] }}" class="flex items-center text-danger"
                                        data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal"><i
                                            data-lucide="trash-2" class="editbtn w-4 h-4 mr-1"></i>Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
    <div id="add-skill" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Skill </h2>
                </div>

                <div id="form-validation" class="p-5">
                    <div class="preview">
                        <form>
                            <div class="input-form">
                                <label for="name" class="form-label w-full flex flex-col sm:flex-row">
                                    Name
                                </label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name"
                                    required onkeypress="return Validate(event);">
                                <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                    <ul></ul>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit"
                                    class="btn btn-primary shadow-md mr-2 validate-form btn-submit">Add
                                    Skill</button>
                            </div>
                        </form>
                        <div id="success-notification-content" class="toastify-content hidden flex">
                            <i class="text-success" data-lucide="check-circle"></i>
                            <div class="ml-4 mr-4">
                                <div class="font-medium">Skill added successfully!</div>
                            </div>
                        </div>
                        <div id="failed-notification-content" class="toastify-content hidden flex">
                            <i class="text-danger" data-lucide="x-circle"></i>
                            <div class="ml-4 mr-4">
                                <div class="font-medium">Failed skill added!</div>
                                <div class="text-slate-500 mt-1">
                                    Please check the fileld form.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Skill</h2>
                </div>
                <form action="{{ route('editSkillApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" name="name" id="id" class="form-control"
                                                placeholder="Name" required onkeypress="return Validate(event);">
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
    <!-- BEGIN: Pagination -->
    @if (count($skills) > 0)
        @if ($totalRecords > 0)
            <div>
                <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                    {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('skills', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('skills', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('skills', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        </div>
    @endif
    <!-- END: Pagination -->
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

                    <form action="{{ route('deleteSkill') }} " method="POST">
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
    <!-- END: Delete Confirmation Modal -->

    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('skillStatusApi') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <input type="hidden" id="status" name="status">
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
@endsection

@section('script')
    <script type="text/javascript">
        function editbtn($id, $name) {
            var id = $id;
            $cid = id;

            $('#filed_id').val($cid);
            $('#id').val($name);
        }

        function resetButton() {
            var statusId = document.getElementById("status_id").value;
            var status = document.getElementById("status").value;
            if (status == false) {
                $('#switch').removeAttr("checked");
            } else {
                $('#switch').attr("checked", true);
            }
        }

        function editSkill($id, $name, $isActive) {
            var id = $id;
            $fid = id;
            $('#status_id').val($fid);
            $('#id').val($name);
            $('#status').val($isActive);
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";
        }

        function delbtn($id, $name) {
            var id = $id;
            $did = id;

            $('#del_id').val($did);
            $('#id').val($id);
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

        jQuery(".btn-submit").click(function(e) {

            e.preventDefault();

            var name = $("#name").val();

            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addSkillApi') }}",
                data: {
                    name: name
                },
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
            jQuery.each(msg, function(key, value) {
                if (key == 'name') {
                    jQuery(".print-name-error-msg").css('display', 'block');
                    jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
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
