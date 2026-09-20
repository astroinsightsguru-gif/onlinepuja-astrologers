@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10">Help Support</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-help-support"
                class="btn btn-primary shadow-md mr-2" onClick="clearForm()">Add
                Help Support</a>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2" aria-label="help-support">
                <thead style="display:none">
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($helpSupport as $support)
                        <tr class="intro-x">
                            <td>{{ $support['name'] }}</td>


                            <td class="w-40">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                             mt-3 sm:mt-0">
                                </div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center ">
                                    <a class="flex items-center mr-3 text-primary" href="javascript:;"
                                        data-tw-toggle="modal" data-tw-target="#add-helpSupport-sub-category"
                                        onclick="addSubCategory({{ $support['id'] }})">
                                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i>Add
                                    </a>
                                    <a class="flex items-center mr-3 text-success"
                                        href="{{ route('helpSupportsubCategory', $support['id']) }}">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                    </a>
                                    <a class="flex items-center mr-3"
                                        onclick="editbtn({{ $support['id'] }},'{{ $support['name'] }}')" href="javascript:;"
                                        data-tw-toggle="modal" data-tw-target="#edit-support">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit
                                    </a>
                                    <a class="flex items-center text-danger"
                                        onclick="deleteHelpSupport({{ $support['id'] }})" data-tw-toggle="modal"
                                        href="javascript:;" data-tw-target="#delete-confirmation-modal">
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
        <div id="add-help-support" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-m">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Add Help Support</h2>
                    </div>
                    <form action="helpSupport/add" method="POST" enctype="multipart/form-data" id="help-support">
                        @csrf
                        <div id="input" class="p-5">
                            <div class="preview">
                                <div class="mt-3">
                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <label for="que" class="form-label">Title</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    placeholder="Title" required onkeypress="return Validate(event);">
                                                <div class="mt-2"><button class="btn btn-primary mr-3 w-24">Save
                                                    </button><a type="button" data-tw-dismiss="modal"
                                                        class="btn btn-secondary w-24">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="add-helpSupport-sub-category" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Add Help Support SubCategory</h2>
                    </div>
                    <form action="{{ route('addHelpSupportSubCategory') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="input" class="p-5">
                            <div class="preview">
                                <div class="mt-3">
                                    <div class="sm:grid grid-cols gap-2">
                                        <input type="hidden" name="supportId" id="supportId" class="form-control">
                                        <div class="input">
                                            <div>
                                                <label for="que" class="form-label">SubCategory</label>
                                                <input type="text" name="subCategory" id="subCategory"
                                                    class="form-control" placeholder="SubCategory" required
                                                    onkeypress="return Validate(event);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="p-5" id="classic-editor">
                                <div class="preview">
                                    <label for="que" class="form-label">Description</label>
                                    <textarea class="form-control editor ml-3" id="did" name="did" required></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="input">
                            <div class="p-5">
                                <input id="isChatWithUs" data-target="#checkbox-switch"
                                    class="show-code form-check-input mr-2 ml-3" name="isChatWithus" type="checkbox">IS
                                Chat With Us!
                            </div>
                        </div>
                        <div class="pb-8 mt-2 ml-2"><button class="btn btn-primary mr-3 w-24">Save
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="edit-support" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Edit Help Support</h2>
                    </div>
                    <form action="{{ route('editHelpSupport') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="input" class="p-5">
                            <div class="preview">
                                <div class="mt-3">
                                    <div class="sm:grid grid-cols gap-2">
                                        <input type="hidden" name="id" id="id" class="form-control">
                                        <div class="input">
                                            <div>
                                                <label for="name" class="form-label">Title</label>
                                                <input type="text" name="title" id="etitle" class="form-control"
                                                    placeholder="Title" required onkeypress="return Validate(event);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Save</button>
                                    <a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>It also delete
                            subcategory</div>
                    </div>

                    <form action="{{ route('deleteHelpSupport') }}" method="POST">
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
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        function editbtn($id, $title) {
            var id = $id;
            $fid = id;
            $('#id').val($fid);
            $('#etitle').val($title);

        }

        function addSubCategory($id) {
            var id = $id;
            $fid = id;
            $('#supportId').val($fid);
            var editor = CKEDITOR.instances['did'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('did');
        }

        function deleteHelpSupport($id) {
            $('#del_id').val($id);
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function clearForm() {
            var ele = document.getElementById('help-support').reset();
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
