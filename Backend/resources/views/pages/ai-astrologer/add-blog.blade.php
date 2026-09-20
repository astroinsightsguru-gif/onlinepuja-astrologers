@extends('../layout/' . $layout)

@section('subhead')
    <title>Add Skill</title>
@endsection

@section('subcontent')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">
            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Add Blog</h2>
                </div>
                <form data-single="true" action="{{ route('addBlogApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Title" required>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="sm:grid grid-cols-2 gap-2">
                                            <div class="input">
                                                <div>
                                                    <label for="image" class="form-label">Image</label>
                                                    <img id="thumb" width="150px"alt="blogImage" />
                                                    <input type="file" name="blogImage" id="blogImage"
                                                        onchange="preview()" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="input mt-2 sm:mt-0">
                                                <div>
                                                    <label for="author" class="form-label">Posted By</label>
                                                    <input type="text" name="author" id="author" class="form-control"
                                                        placeholder="Posted By" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control editor ml-3" id="description" name="description">Your content here</textarea>
                                    </div>
                                    <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add Blog</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function preview() {
            thumb.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
    @vite('resources/js/ckeditor-classic.js')
@endsection
