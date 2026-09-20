@extends('../layout/' . $layout)

@section('subhead')
<title>Add Website Faqs</title>
@endsection

@section('subcontent')
<div class="loader"></div>
<h2 class="intro-y text-lg font-medium mt-10 d-inline">Website Faq's</h2>
<a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-gift"
    class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn"
    onclick="document.getElementById('add-data').reset();document.getElementById('thumb').style.display = 'none'">Add Website Faqs</a>
<div class="grid grid-cols-12 gap-6 ">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
    </div>
</div>
<!-- BEGIN: Data List -->
@if (count($webfaq) > 0)
    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
        <table class="table table-report -mt-2" aria-label="astrologer-category">
            <thead class="sticky-top">
                <tr>
                    <th class="whitespace-nowrap">#</th>
                    <th class="whitespace-nowrap">TITLE</th>
                    <th class="whitespace-nowrap">DESCRIPTION</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 0;
                @endphp
                @foreach ($webfaq as $faq)
                    <tr class="intro-x">
                        <td>{{ ($page - 1) * 15 + ++$no }}</td>
                        <td>
                            <div class="font-medium whitespace-nowrap">{{ $faq->title }}</div>
                        </td>
                        <td>
                            <div class="font-medium whitespace-nowrap" style="cursor: pointer;" title="{{ $faq->description }}">
                                {{ Str::words($faq->description, 10, '...') }}
                            </div>
                        </td>


                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a id="editbtn" href="javascript:void(0);"
                                    onclick="editbtn({{ $faq->id }} , '{{ $faq->title}}',`{!! $faq->description !!}`)"
                                    class="flex items-center mr-3 " data-tw-target="#edit-modal" data-tw-toggle="modal"><i
                                        data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit</a>


                                <a type="button" href="javascript:;" class="flex items-center deletebtn text-danger"
                                    data-tw-toggle="modal" data-tw-target="#deleteModal" onclick="delbtn({{ $faq->id }})">
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
            {{ $totalRecords }} entries
        </div>
    @endif
    <div class="d-inline addbtn intro-y col-span-12">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('web-faq-list', ['page' => $page - 1]) }}">
                        <i class="w-4 h-4" data-lucide="chevron-left"></i>
                    </a>
                </li>
                @for ($i = 0; $i < $totalPages; $i++)
                    <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                        <a class="page-link" href="{{ route('web-faq-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                    </li>
                @endfor
                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('web-faq-list', ['page' => $page + 1]) }}">
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
                <h2 class="font-medium text-base mr-auto">Add Website Faqs</h2>
            </div>
            <form id="add-data" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="input" class="p-5">
                    <div class="preview">
                        <div class="mt-3">
                            <div class="sm:grid grid-cols gap-2">
                                <div class="input">
                                    <div>
                                        <label for="name" class="form-label">Title</label>
                                        <input type="text" name="title" id="title" class="form-control"
                                            placeholder="Add Title" onkeypress="return Validate(event);" required>
                                        <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sm:grid grid-cols gap-2">
                                <div class="input">
                                    <div>
                                        <label for="name" class="form-label">Description</label>
                                        <textarea id="description" name="description" rows="4"
                                            class="form-control"></textarea>
                                        <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="mt-5"><button class="btn btn-submit btn-primary shadow-md mr-2">Add Website Faqs</button>
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
                <h2 class="font-medium text-base mr-auto">Edit Website Faqs</h2>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('editWebFaq') }}">
                @csrf
                <div id="input" class="p-5">
                    <div class="preview">
                        <div class="mt-3">
                            <div class="sm:grid grid-cols gap-2">
                                <div class="input">
                                    <input type="hidden" id="filed_id" name="filed_id">
                                    <div>
                                        <label for="name" class="form-label">Title</label>
                                        <input type="text" name="title" id="title" class="form-control"
                                            placeholder="Add Title" onkeypress="return Validate(event);" required>
                                        <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:grid grid-cols gap-2">
                                <div class="input">
                                    <div>
                                        <label for="name" class="form-label">Description</label>
                                        <textarea id="description" name="description" rows="4"
                                            class="form-control"></textarea>
                                        <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="mt-5"><button class="btn edit-btn-submit btn-primary shadow-md mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
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
                <form action="{{ route('deleteWebFaq') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="faq_id" name="faq_id">
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

        $('#faq_id').val($did);
        $('#id').val($id);
    }
    function editbtn($id, $title, $description) {
        var id = $id;
        var gid = $id;

        $cid = id;

        $('#filed_id').val($cid);
        $('#title').val($title);
        $('#description').val($description);

    }

    function Validate(event) {
        var regex = new RegExp("^[0-9-!@#$%&<>*?]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) {
            event.preventDefault();
            return false;
        }
    }

</script>
<script type="module">

    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })


    jQuery("#add-data").submit(function (e) {
        e.preventDefault();
        jQuery.ajax({
            type: 'POST',
            url: "{{ route('addWebFaq') }}",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (data) {
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
        jQuery.each(msg, function (key, value) {
            if (key == 'name') {
                jQuery(".print-name-error-msg").css('display', 'block');
                jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            else {
                toastr.warning(value)
            }
        });
    }
    function printEditErrorMsg(msg) {
        jQuery(".print-edit-name-error-msg").find("ul").html('');
        jQuery.each(msg, function (key, value) {
            if (key == 'name') {
                jQuery(".print-edit-name-error-msg").css('display', 'block');
                jQuery(".print-edit-name-error-msg").find("ul").append('<li>' + value + '</li>');
            }
        });
    }


</script>
<script>
    $(window).on('load', function () {
        $('.loader').hide();
    })
</script>
@endsection