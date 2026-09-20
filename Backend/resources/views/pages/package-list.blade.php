@extends('../layout/' . $layout)

@section('subhead')
<title>Puja Packages</title>
@endsection

@section('subcontent')
<div class="loader"></div>
<h2 class="intro-y text-lg font-medium mt-10 d-inline">Packages</h2>

<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn" href="puja-package/add">Add Packages</a>
@if ($totalRecords > 0)
    <!-- BEGIN: Data List -->
    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
        <table class="table table-report mt-2" aria-label="customer-list">
            <thead class="sticky-top">
                <tr>
                    <th class="whitespace-nowrap">#</th>
                    <th class="whitespace-nowrap">TITLE</th>
                    <th class="whitespace-nowrap text-center">PERSON</th>
                    <th class="text-center whitespace-nowrap">PACKAGE PRICE (INR)</th>
                    <th class="text-center whitespace-nowrap">PACKAGE PRICE (USD)</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="todo-list">
                @php
                    $no = 0;
                @endphp
                @foreach ($packeges as $user)
                    <tr class="intro-x">
                        <td>{{ ($page - 1) * 15 + ++$no }}</td>
                        <td>
                            <div class="font-medium whitespace-nowrap">{{ $user->title ? $user->title : '--' }}</div>
                        </td>
                        <td class="text-center">{{ $user->person ? $user->person : '--' }}</td>
                        <td class="text-center">{{ $user->package_price ? $user->package_price : '--' }}</td>
                        <td class="text-center">{{ $user->package_price_usd ? $user->package_price_usd : '--' }}</td>

                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0 ,mr-3">
                                    <input class="toggle-class show-code form-check-input mr-3 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $user->package_status ? 'checked' : '' }}
                                        onclick="editPackage({{ $user->id }},{{ $user->package_status }})"
                                        href="$user->id" data-tw-target="#verified">
                                </div>

                                <a class="flex items-center mr-3" href="{{ route('edit-pujapackage', $user->id) }}">
                                    <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit
                                </a>
                                <a type="button" href="javascript:;" class="flex items-center deletebtn text-danger"
                                    data-tw-toggle="modal" data-tw-target="#deleteModal" onclick="delbtn({{ $user->id }})">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- END: Data List -->
    <!-- BEGIN: Pagination -->
    @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries
        </div>
    @endif
    <div class="d-inline addbtn intro-y col-span-12">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('package-list', ['page' => $page - 1]) }}">
                        <i class="w-4 h-4" data-lucide="chevron-left"></i>
                    </a>
                </li>
                @for ($i = 0; $i < $totalPages; $i++)
                    <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                        <a class="page-link" href="{{ route('package-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                    </li>
                @endfor
                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('package-list', ['page' => $page + 1]) }}">
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
                <img src="build/assets/images/nodata.png" style="height:290px" alt="noData">
                <h3 class="text-center">No Data Available</h3>
            </div>
        </div>
    </div>
@endif
<!-- END: Pagination -->

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
                <form action="{{ route('deletePujaPackage') }}" method="POST">
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
<div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('PackageStatus') }}" method="POST"
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
<!-- END: Delete Confirmation Modal -->
@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        jQuery('.select2').select2();
    });
</script>
<script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
</script>

<script type="text/javascript">
    @if (Session::has('error'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.warning("{{ session('error') }}");
    @endif
    function delbtn($id) {
        var id = $id;
        $did = id;

        $('#del_id').val($did);
        $('#id').val($id);
    }


    function editPackage($id, $isActive) {
                var id = $id;
                $fid = id;
                var active = $isActive ? 'Inactive' : 'Active';
                document.getElementById('active').innerHTML = "You want to " + active;
                document.getElementById('btnActive').innerHTML = "Yes, " +
                    active + " it";

                $('#status_id').val($fid);
                $('#editName').val($name);
            }
</script>
<script type="text/javascript">
    var spinner = $('.loader');
</script>
<script>
    $(window).on('load', function () {
        $('.loader').hide();
    })
</script>
@endsection
