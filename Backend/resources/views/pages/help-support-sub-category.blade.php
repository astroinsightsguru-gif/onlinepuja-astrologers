@extends('../layout/' . $layout)

@section('subhead')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"  ></script>
    <title>Help Support Sub SubCategory</title>
    <style>
        svg {
            display: inherit !important
        }

        .actionbtn {
            display: inline-block
        }
    </style>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10">Help Support Sub SubCategory</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="intro-y box mt-5" style="width: 100%">
                <div id="boxed-accordion" class="p-5">
                    <div class="preview">
                        @foreach ($helpSupportSubSubCategory as $subCategory)
                            <div class="card border p-2 mt-5">
                                <div style="display:inline-block">
                                    <b>{{ $subCategory->title }}</b>
                                    {!! $subCategory->description !!}
                                </div>
                                <div style="float:right">
                                    <a class=" items-center mr-3 text-primary actionbtn" href="javascript:;"
                                        onClick="editHelpSupportSubSubCategory({{ $subCategory->id }},{{ $subCategory->helpSupportId }},{{ $subCategory->helpSupportQuationId }},'{{ $subCategory->title }}',{{ json_encode($subCategory->description) }},{{ $subCategory->isChatWithus }})"
                                        data-tw-toggle="modal" data-tw-target="#edit-support">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit
                                    </a>
                                    <a class=" items-center mr-3 text-danger actionbtn" href="javascript:;"
                                        onclick="deleteBtn({{ $subCategory->id }})" data-tw-toggle="modal"
                                        data-tw-target="#delete-confirmation-modal">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>Delete
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div id="edit-support" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Help Support</h2>
                </div>
                <form action="{{ route('editHelpSupportSubSubCategory') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <input type="hidden" name="id" id="id" class="form-control">
                                    <input type="hidden" name="supportId" id="supportId" class="form-control">
                                    <input type="hidden" name="supportQuestionId" id="supportQuestionId"
                                        class="form-control">
                                    <div class="input">
                                        <div>
                                            <label for="name" class="form-label">Title</label>
                                            <input type="text" onkeypress="return validateJavascript(event);" name="title" id="etitle" class="form-control"
                                                placeholder="Title" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12">
                        <div class="box">
                            <div class="p-5" id="classic-editor">
                                <div class="preview">
                                    <label for="que" class="form-label">Description</label>
                                    <textarea class="form-control editor ml-3" id="editdid" name="editdid"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input">
                        <div class="p-5">
                            <input id="eisChatWithUs" class="show-code form-check-input mr-2 ml-3" name="isChatWithus"
                                type="checkbox">IS
                            Chat With Us!
                        </div>
                    </div>
                    <div class="p-3 mt-5"><button class="btn btn-primary shadow-md mr-2">Save</button>
                        <a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24">Cancel</a>
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
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? </div>
                    </div>

                    <form action="{{ route('deleteHelpSupportSubSubCategory') }}" method="POST">
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
@endsection
@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        function editHelpSupportSubSubCategory($id, $supportId, $supportQuestionId, $title, $description, $isChatWithus) {
            $('#id').val($id);
            $('#supportId').val($supportId);
            $('#supportQuestionId').val($supportQuestionId);
            $('#etitle').val($title);
            $('#description').val($description);
            $('#supportId').val($supportId);
            $isChatWithus = $isChatWithus ? true : false;
            if ($isChatWithus) {
                $("#eisChatWithUs").attr("checked", "checked");
            } else {
                $("#eisChatWithUs").removeAttr('checked');
            }
            var editor = CKEDITOR.instances['editdid'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdid');
            var editor = CKEDITOR.instances['editdid'];
            CKEDITOR.instances['editdid'].setData($description)
        }

        function deleteBtn($id) {
            $('#del_id').val($id);
        }
        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
