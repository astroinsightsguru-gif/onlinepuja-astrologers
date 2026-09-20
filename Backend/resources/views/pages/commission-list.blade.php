@extends('../layout/' . $layout)

@section('subhead')
    <title>Commissions</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Commissions</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-skill"
        class="d-inline addbtn btn btn-primary shadow-md mr-2 mt-10" onclick="clearModel()">Add
        Commission</a>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            </div>
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if (count($commission) > 0)
    
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report -mt-2" aria-label="commission-list">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class=" whitespace-nowrap">COMMISSION TYPE</th>
                        <th class="text-center whitespace-nowrap">{{ucfirst($professionTitle)}}</th>
                        <th class="text-center whitespace-nowrap">COMMISSION</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    
                    @foreach ($commission as $commi)
                        <tr class="intro-x">
                            <td>{{ ++$no }}</td>

                            <td>
                                {{ $commi->commssionType }}
                            </td>
                            <td class="text-center">{{ $commi->astrologerName }}</td>
                            <td class="text-center">
                                {{ $commi->commission }}%
                            </td>

                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn" href="javascript:;"
                                        onclick="editbtn({{ $commi->id }} , '{{ $commi->astrologerId }}','{{ $commi->commissionTypeId }}', '{{ $commi->commission }}')"
                                        class="flex items-center mr-3 " data-tw-target="#edit-modal"
                                        data-tw-toggle="modal"><i data-lucide="check-square"
                                            class="editbtn w-4 h-4 mr-1"></i>Edit</a>

                                        <a type="button" href="javascript:;" class="flex items-center deletebtn text-danger"
                                        data-tw-toggle="modal" data-tw-target="#deleteModal"
                                        onclick="delbtn({{ $commi->id }})">
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
                        <a class="page-link" href="{{ route('commissions', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('commissions', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('commissions', ['page' => $page + 1]) }}">
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
    <!-- BEGIN: Delete Confirmation Modal -->

    <div id="add-skill" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Commission</h2>
                </div>
                <form enctype="multipart/form-data" id="commission-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label class="form-label">Commission Type</label>
                                        <select class="form-control" id="commissionType" name="commissionTypeId"
                                            value="commissionTypeId" required>
                                            @foreach ($commissionType as $commission)
                                                <option  value={{ $commission['id'] }}>
                                                    {{ $commission['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger print-commissionTypeId-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="astrologerId" class="form-label">{{ucfirst($professionTitle)}}</label>
                                        <select data-placeholder="Search User" class="form-control" id="astrologerId"
                                            name="astrologerId" value="astrologerId" required>
                                            @foreach ($astrologer as $astro)
                                                <option id="astrologerId" value={{ $astro->id }}>
                                                    {{ $astro->name }}-{{ $astro->contactNo }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger print-astrologerId-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label for="commission" class="form-label">Commission(%)</label>
                                            <input type="text" name="commission" id="commission" class="form-control"
                                                placeholder="Commission" onKeyDown="numbersOnly(event)" maxlength="3"
                                                max="100">
                                            <div class="text-danger print-commission-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit"class="btn btn-primary shadow-md mr-2">Add
                                    Commission</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Commission</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="edit-commission-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <input type="hidden" id="filed_id" name="filed_id">
                                        <label class="form-label">Commission Type</label>
                                        <select class="form-control" id="commissionTypeId" name="commissionTypeId">
                                            <option disabled selected>--Select Commission Type--</option>
                                            @foreach ($commissionType as $commission)
                                                <option value={{ $commission['id'] }}>
                                                    {{ $commission['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger print-commissionTypeId-error-msg mb-2"
                                            style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="astrologerId" class="form-label">{{ucfirst($professionTitle)}}</label>
                                        <select data-placeholder="Search User" class="form-control" id="editastrologerId"
                                            name="astrologerId" value="astrologerId" required>
                                            @foreach ($astrologer as $astro)
                                                <option id="astrologerId" value={{ $astro->id }}>
                                                    {{ $astro->name }}-{{ $astro->contactNo }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger print-astrologerId-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label for="commission" class="form-label">Commission(%)</label>
                                            <input type="text" name="commission" id="editcommission"
                                                class="form-control" placeholder="Commission" required
                                                onKeyDown="numbersOnly(event)">
                                            <div class="text-danger print-commission-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
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
                    <form action="{{ route('deleteCommission') }}" method="POST">
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
@endsection

@section('script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"  ></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"  ></script>
    <script type="text/javascript">
        toastr.options = {
            "closeButton": true,
            "newestOnTop": true,
            "positionClass": "toast-top-right"
        };
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif
        $(document).ready(function() {
            jQuery('.js-example-basic-single').select2();
        });

        function clearModel() {
            jQuery(".print-commissionTypeId-error-msg").find("ul").html('');
            jQuery(".print-commission-error-msg").find("ul").html('');
            jQuery(".print-astrologerId-error-msg").find("ul").html('');
            var ele = document.getElementById('commission-form').reset();
        }

        function editbtn($id, $astrologerId, $commissionTypeId, $commission) {
            jQuery(".print-commissionTypeId-error-msg").find("ul").html('');
            jQuery(".print-commission-error-msg").find("ul").html('');
            jQuery(".print-astrologerId-error-msg").find("ul").html('');
            var id = $id;
            $cid = id;
            $('#filed_id').val($cid);
            $('#editastrologerId').val($astrologerId);
            $('#commissionTypeId').val($commissionTypeId);
            $('#editcommission').val($commission);
        }

        function delbtn($id, $name) {
            var id = $id;
            $did = id;

            $('#del_id').val($did);
            $('#astrologerId').val($id);
        }

        function numbersOnly(e) {
            var keycode = e.keyCode;
            if ((keycode < 48 || keycode > 57) && (keycode < 96 || keycode > 105) && keycode !=
                9 && keycode != 8 && keycode != 37 && keycode != 38 && keycode != 39 && keycode != 40 && keycode != 46) {
                e.preventDefault();
            }
        }

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        jQuery("#commission-form").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addCommissionApi') }}",
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

        jQuery("#edit-commission-form").submit(function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('editCommissionApi') }}",
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
            jQuery(".print-commissionTypeId-error-msg").find("ul").html('');
            jQuery(".print-commission-error-msg").find("ul").html('');
            jQuery(".print-astrologerId-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (key == 'commissionTypeId') {
                    jQuery(".print-commissionTypeId-error-msg").css('display', 'block');
                    jQuery(".print-commissionTypeId-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'commission') {
                    jQuery(".print-commission-error-msg").css('display', 'block');
                    jQuery(".print-commission-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (key == 'astrologerId') {
                    jQuery(".print-astrologerId-error-msg").css('display', 'block');
                    jQuery(".print-astrologerId-error-msg").find("ul").append('<li>' + value + '</li>');
                }
                if (!key) {
                    toastr.error(value);
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
