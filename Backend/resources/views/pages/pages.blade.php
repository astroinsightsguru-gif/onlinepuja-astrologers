@extends('../layout/' . $layout)

@section('subhead')
    <title>Pages</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Pages</h2>
     <a href="javascript:;" data-tw-toggle="modal" onclick="showEditor()" data-tw-target="#add-page"
        class="d-inline btn btn-primary shadow-md mr-2 mt-10 addbtn">Add
        Page
    </a> 
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
        <div class="intro-y grid grid-cols-12 gap-6 mt-5 grid-table">
            @foreach ($pages as $page)
                <div class="intro-y col-span-12 md:col-span-6 box">

                    <div class="px-5 pt-5 text-slate-600 dark:text-slate-500">
                        <h2 class="font-medium text-xl">{{ $page->title }}</h2>
                    </div>
                    <div class="p-5 text-slate-600 dark:text-slate-500">{!! $page->description!!}</div>
                    <div
                        class="px-5 pt-3 pb-5 border-t border-slate-200/60 dark:border-darkmode-400 intro-y flex flex-col sm:flex-row items-center">
                        <div class="w-full flex text-slate-500 text-xs sm:text-sm">
                            <a href="javascript:;"
                            onclick="editbtn({{ $page->id }},'{{ $page->title }}','{{ $page->type }}',{{ json_encode($page->description) }})"
                            class="dropdown-item" data-tw-target="#edit-modal"
                            data-tw-toggle="modal">
                            <i data-lucide="check-square" class="w-4 h-4 mr-2"></i><span>Edit</span>
                         </a>
                        </div>

                        <div
                            class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                    mt-3 sm:mt-0">
                            <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                href="javascript:;" data-tw-toggle="modal" data-onstyle="success" data-offstyle="danger"
                                data-toggle="toggle" data-on="Active" data-off="InActive"
                                {{ $page->isActive ? 'checked' : '' }}
                                onclick="editpageStatus({{ $page->id}},{{ $page->isActive}})" href="$page->id"
                                data-tw-target="#verified">

                        </div>

                    </div>
                </div>
            @endforeach
        </div>



    <div id="add-page" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add page</h2>
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

                                    <select class="form-control"  name="type"
                                     required>
                                    <option disabled selected>--Select Type--</option>

                                        <option value="privacy"> Privacy Policy</option>
                                       <option value="terms"> Terms & Conditions</option>
                                       <option value="aboutus"> About Us</option>
                                       <option value="refundpolicy"> Refund Policy</option>
                                       <option value="astrologerPrivacy">Astrologer Privacy Policy</option>
                                       <option value="astrologerTerms">Astrologer Terms & Conditions</option>
                                       <option value="others">Others</option>

                                </select>

                                    <div class="input" id="add-page-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control ml-3" id="description" name="description"></textarea>
                                    </div>


                                    <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add
                                            page</button>
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
                    <h2 class="font-medium text-base mr-auto">Edit page</h2>
                </div>
                <form id="edit-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5">
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

                                    <select class="form-control" id="etype"  name="type"
                                    required>
                                   <option disabled selected>--Select Type--</option>

                                       <option value="privacy"> Privacy Policy</option>
                                       <option value="terms"> Terms & Conditions</option>
                                       <option value="aboutus"> About Us</option>
                                       <option value="refundpolicy"> Refund Policy</option>
                                       <option value="astrologerPrivacy">Astrologer Privacy Policy</option>
                                       <option value="astrologerTerms">Astrologer Terms & Conditions</option>
                                       <option value="others">Others</option>

                               </select>

                               <div class="input" id="edit-page-editor">
                                <label for="editdescription" class="from-label">Description</label>
                                <textarea class="form-control ml-3" id="editdescription" name="editdescription"></textarea>
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
    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('pageStatusApi') }}" method="POST" enctype="multipart/form-data">
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

                    <form action="{{ route('deletepage') }} " method="POST">
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

        function editbtn($id, $title, $type, $description) {
            $('#filed_id').val($id);
            $('#etitle').val($title);
            $('#etype').val($type);

            setTimeout(() => {
                initializeEditor('#editdescription', $description);
            }, 100);
        }


        function editpageStatus($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
            $('#id').val($name);
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
            initializeEditor('#description');
        }

        function deletebtn($id) {
            $('#del_id').val($id);
        }

        jQuery(function() {
            jQuery('#edit-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                data.append('editdescription', getEditorData('#editdescription'));
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('editpageApi') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
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
                data.append('description', getEditorData('#description'));
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('addpageApi') }}",
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
        })
    </script>
@endsection
