@extends('../layout/' . $layout)

@section('subhead')
    <title>Email Template</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Email Template</h2>
    {{-- <a href="javascript:;" data-tw-toggle="modal" onclick="showEditor()" data-tw-target="#add-blog"
        class="d-inline btn btn-primary shadow-md mr-2 mt-10 addbtn">Add
        Template
    </a> --}}


    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto withoutsearch">
            <table class="table table-report -mt-2" aria-label="notification">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">Type</th>
                        <th class="whitespace-nowrap">SUBJECT</th>
                        <th class="whitespace-nowrap">DESCRIPTION</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($emails as $email)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $email->name)) }}</td>
                            <td>{{ $email->subject }}</td>

                            {{-- ✅ Description ko ek email-template ke andar wrap karke dikhayenge --}}
                            <td>
                                <div style="font-family: Arial, sans-serif; border:1px solid #ddd; border-radius:8px; overflow:hidden; max-width:600px; margin:auto;">

                                    {{-- Header with logo --}}
                                    <div style="background:#f4f4f4; padding:15px; text-align:center;">
                                        <img src="{{asset(systemflag('AdminLogo'))}}" alt="Company Logo" style="height:60px;">
                                    </div>

                                    {{-- Subject --}}
                                    <div style="padding:15px; border-bottom:1px solid #eee; background:#fafafa;">
                                        <h3 style="margin:0; font-size:18px; color:#333;">
                                            {{ $email->subject }}
                                        </h3>
                                    </div>

                                    {{-- Body from DB --}}
                                    <div style="padding:20px; font-size:14px; color:#444; line-height:1.6;">
                                        {!! $email->description !!}
                                    </div>

                                    {{-- Footer --}}
                                    <div style="background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#777;">
                                        © {{date('Y')}} {{systemflag('AppName')}}. All rights reserved.
                                    </div>
                                </div>
                            </td>

                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn" href="javascript:;"
                                        onclick="editbtn({{ $email['id'] }},'{{ $email['name'] }}','{{ $email['subject'] }}',{{ json_encode($email['description']) }})"
                                        onclick="showEditor()" class="dropdown-item" data-tw-target="#edit-modal"
                                        data-tw-toggle="modal" data-tw-dismiss="dropdown">
                                        <i data-lucide="check-square" class="editbtn w-4 h-4 mr-2"></i>Edit
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
                        <a class="page-link" href="{{ route('getEmailTemplate', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('getEmailTemplate', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('getEmailTemplate', ['page' => $page + 1]) }}">
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
                    <h2 class="font-medium text-base mr-auto">Add Email</h2>
                </div>
                <form data-single="true" method="POST" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Type</label>
                                            <select class="form-control" id="commissionType" name="name"
                                                 required>

                                                    <option  value="partner_registration">
                                                        Partner Registration</option>
                                                        <option  value="verify"> Verify</option>
                                                        <option  value="unverify"> Verify</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="sm:grid grid-cols-2 gap-2">
                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="subject" class="form-label">Subject</label>
                                                    <input type="text" name="subject" id="subject" class="form-control"
                                                placeholder="subject" required onkeypress="return Validate(event);">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control ml-3" id="description" name="description"></textarea>
                                    </div>

                                    <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add
                                            Email Template</button>
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
                    <h2 class="font-medium text-base mr-auto">Edit Template</h2>
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
                                            <label for="title" class="form-label">Type</label>
                                            <select class="form-control" id="email_type" name="name" required>
                                                <option value="partner_registration" {{ $email['type'] === 'partner_registration' ? 'selected' : '' }}>
                                                    Partner Registration
                                                </option>
                                                <option value="verify" {{ $email['type'] === 'verify' ? 'selected' : '' }}>
                                                    Verify
                                                </option>
                                                <option value="unverify" {{ $email['type'] === 'unverify' ? 'selected' : '' }}>
                                                    Unverify
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="sm:grid grid-cols-2 gap-2">

                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="editsubject" class="form-label">Subject</label>
                                                    <input type="text" id="editsubject" name="subject"
                                                        class="form-control" placeholder="Subject"
                                                        aria-describedby="input-group-3" required>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control ml-3" id="editdescription" name="editdescription"></textarea>
                                    </div>


                                    <div>
                                        <h3>Note</h3>
                                        <ul style="list-style-type: disc; padding-left: 20px;">
                                            <li>Use &#123;&#123;$username&#125;&#125; like this for name</li>
                                            <li>Use &#123;&#123;$logo&#125;&#125; like this for logo and use img tag for that</li>
                                        </ul>
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


    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        var spinner = $('.loader');

        function editbtn($id, $title,$subject,$description) {
            $('#filed_id').val($id);
            $('#email_type').val($title);
            $('#editsubject').val($subject);

            var editor = CKEDITOR.instances['editdescription'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdescription');
            var editor = CKEDITOR.instances['editdescription'];
            CKEDITOR.instances['editdescription'].setData($description)

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
                    url: "{{ route('editEmailTemplate') }}",
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
                    url: "{{ route('addEmailTemplate') }}",
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
