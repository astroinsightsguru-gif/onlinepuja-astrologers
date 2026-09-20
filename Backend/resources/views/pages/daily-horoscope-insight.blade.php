@extends('../layout/' . $layout)

@section('subhead')
    <title>Daily Horoscope Insight</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Daily Horoscope Insight</h2>
    <form class="addbtn mt-10" action="{{ route('dailyHoroscopeInsight') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="relative w-56 mx-auto" style="display: inline-block;margin-left: 13px">
            <div
                class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                <i data-lucide="calendar" class="w-4 h-4"></i>
            </div>
            <input type="text" id="filterDate" name="filterDate" class="datepicker form-control pl-12"
                data-single-mode="true" value={{ $filterDate }}>
        </div>
        <div style="display: inline-block" class="input mt-2 sm:mt-0">
            <select class="form-control w-full" id="filterSign" name="filterSign" value="filterSign">
                @foreach ($signs as $sign)
                    <option id="signId" @if ($sign['id'] == $selectedId) selected @endif value="{{ $sign['id'] }}">
                        {{ $sign['name'] }}</option>
                @endforeach
            </select>
        </div>
        <button style="display:inline-flex;top: 4px; position: relative;" id="deletebtn"
            class="btn btn-primary w-32 mr-2 mb-2"><i data-lucide="filter"
                class="deletebtn w-4 h-4 mr-2 "></i>Apply</button>
    </form>
    <a onClick="addDailyHoroscope()" data-tw-toggle="modal" data-tw-target="#add-dailyHoroscope"
        style="top: 4px; position: relative;"
        class="btn btn-primary shadow-md mr-2 d-inline mt-10 addbtn horobtn horo-insight">Add
        Daily Horoscope Insight
    </a>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>

    <!-- BEGIN: Seller Details -->
    <div class="col-span-12 lg:col-span-7 2xl:col-span-8">
        <div class="grid grid-cols-12 gap-6 mt-5 list-table">
            @foreach ($dailyHoroscopeInsight as $insight)
                <div class="intro-y col-span-12 sm:col-span-6 2xl:col-span-4 xl:col-span-4">
                    <div class="box">
                        <div class="p-5">
                            <div
                                class="h-40 2xl:h-56 rounded-md overflow-hidden  before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black/90 before:to-black/10 image-fit">
                                <img alt="Horoscope image" class="rounded-t-md" src="/{{ $insight->coverImage }}">
                                <div class="absolute w-full flex items-center px-3 pt-2 z-10">
                                    <div class="ml-3 text-white mr-auto">
                                    </div>
                                    <div class="dropdown ml-3">
                                        <a href="javascript:;"
                                            class="bg-white/20 dropdown-toggle w-8 h-8 flex items-center justify-center rounded-full"
                                            aria-expanded="false" data-tw-toggle="dropdown">
                                            <i data-lucide="more-vertical" class="w-4 h-4 text-white"></i>
                                        </a>
                                        <div class="dropdown-menu w-40">
                                            <ul class="dropdown-content">
                                                <li>
                                                    <a id="editbtn"
                                                        onclick='editbtn({{ $insight->id }},"{{ $insight->name }}","{{ $insight->coverImage }}","{{ $insight->title }}",{{ json_encode($insight->description) }},{{ $insight->horoscopeSignId }},"{{ $insight->horoscopeDate }}","{{ $insight->link }}")'
                                                        onclick="" class="dropdown-item"
                                                        data-tw-target="#edit-dailyHoroscope" data-tw-toggle="modal"
                                                        href="javascript:;"><i data-lucide="check-square"
                                                            class="editbtn w-4 h-4 mr-2"></i>Edit</a>
                                                    <a id="deletebtn" onclick="deletebtn({{ $insight->id }})"
                                                        onclick="" class="dropdown-item" data-tw-target="#deleteModal"
                                                        data-tw-toggle="modal" href="javascript:;"><i data-lucide="trash-2"
                                                            class="deletebtn w-4 h-4 mr-2"></i>Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 text-white px-5 pb-6 z-10">
                                    <a href="" class="block font-medium text-base">{{ $insight->name }}</a>
                                    <span
                                        class="text-white/90 text-xs mt-3">{{ date('d-m-Y', strtotime($insight->horoscopeDate)) }}</span>
                                </div>
                            </div>
                            <div class="text-slate-600 dark:text-slate-500 mt-5">
                                <h2 class="font-medium text-xl">{{ $insight->title }}</h2>
                                <div class="flex items-center mt-2">
                                    <i data-lucide="layers" class="w-4 h-4 mr-2"></i> Horoscope Sign:
                                    {{ $insight->signName }}
                                </div>
                                <div class="flex items-center mt-2">
                                    <p>{!! $insight->description !!}</p>
                                </div>
                                @if ($insight->link)
                                    <a href=" {{ $insight->link }}"class="flex mt-2 active" target="_blanck"
                                        style="color: blue">
                                        <i data-lucide="link" class="w-4 h-4 mr-2"></i> link:
                                        {{ $insight->link }}
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
    <div id="add-dailyHoroscope" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Daily Horoscope Insight</h2>
                </div>
                <form data-single="true" action="{{ route('addDailyHoroscopeInsight') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Horoscope Sign</label>
                                        <select class="form-control" id="horoscopeSignId" name="horoscopeSignId"
                                            value="horoscopeSignId">
                                            <option disabled selected>--Select Sign--</option>
                                            @foreach ($signs as $sign)
                                                <option id="productCategoryId" value={{ $sign['id'] }}>
                                                    {{ $sign['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="input-group" class="form-label">HoroScope Date</label>
                                            <input type="date" id="horoscopeDate" name="horoscopeDate"
                                                class="form-control" placeholder="horoscopeDate"
                                                aria-describedby="input-group-3" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Name" aria-describedby="input-group-3" required
                                                onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Title</label>
                                            <input onkeypress="return validateJavascript(event);" type="text"
                                                id="title" name="title" class="form-control" placeholder="Title"
                                                aria-describedby="input-group-3" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Link</label>
                                            <input onkeypress="return validateJavascript(event);" type="text"
                                                id="link" name="link" class="form-control" placeholder="Link"
                                                aria-describedby="input-group-3" required>
                                        </div>
                                    </div>

                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control  ml-3" id="description" name="description" required></textarea>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                <label for="profile" class="form-label">Image</label>
                                                <img id="thumb" width="150px" alt="coverImage"
                                                    style="display:none" />
                                                <input type="file" class="mt-2" name="coverImage" id="coverImage"
                                                    onchange="preview()" accept="image/*" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add
                                            Daily Horoscope Insight</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <div id="edit-dailyHoroscope" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Daily Horoscope Insight</h2>
                </div>
                <form data-single="true" action="{{ route('editDailyHoroscopeInsight') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Horoscope Sign</label>
                                        <select class="form-control" id="horoscopeSignId" name="horoscopeSignId"
                                            value="horoscopeSignId">
                                            <option disabled selected>--Select Sign--</option>
                                            @foreach ($signs as $sign)
                                                <option id="productCategoryId" value={{ $sign['id'] }}>
                                                    {{ $sign['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="input-group" class="form-label">HoroScope Date</label>
                                            <input type="date" id="horoscopeDate" name="horoscopeDate"
                                                class="form-control" placeholder="horoscopeDate"
                                                aria-describedby="input-group-3" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="id" name="id" class="form-control">
                                            <label id="amount" class="form-label">Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Name" aria-describedby="input-group-3" required
                                                onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Title</label>
                                            <input onkeypress="return validateJavascript(event);" type="text"
                                                id="title" name="title" class="form-control" placeholder="Title"
                                                aria-describedby="input-group-3" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Link</label>
                                            <input onkeypress="return validateJavascript(event);" type="text"
                                                id="link" name="link" class="form-control" placeholder="Link"
                                                aria-describedby="input-group-3" required>
                                        </div>
                                    </div>

                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control" required id="editdescription" name="editdescription">Your content here</textarea>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                <label for="profile" class="form-label">Image</label>
                                                <img id="thumbs" width="150px" alt="coverImage" />
                                                <input type="file" class="mt-2" name="coverImage" id="coverImage"
                                                    onchange="editPreview()" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Save</button>
                                    </div>
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
                    <form action="{{ route('deleteHoroscopeInsight') }}" method="POST">
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
    {{-- </div> --}}
    <!-- END: Seller Details -->
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
        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
        }

        function editPreview() {
            thumbs.src = URL.createObjectURL(event.target.files[0]);
        }

        function editbtn($id, $name, $coverImage, $title, $description, $horoscopeSignId, $horoscopeDate, $link) {

            $('#id').val($id);
            $('#name').val($name);
            $('#title').val($title);
            $('#description').val($description);
            $('#horoscopeSignId').val($horoscopeSignId);
            $('#title').val($title);
            $('#link').val($link);
            var newdate = $horoscopeDate.split("-");
            var date = newdate[2].split(" ");
            date = newdate[0] + '-' + newdate[1] + '-' + date[0]
            $('#horoscopeDate').val(date);
            document.getElementById("thumbs").src = "/" + $coverImage;
            var editor = CKEDITOR.instances['editdescription'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdescription');
            var editor = CKEDITOR.instances['editdescription'];
            CKEDITOR.instances['editdescription'].setData($description)
        }

        function deletebtn($id) {
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

        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function addDailyHoroscope() {
            var editor = CKEDITOR.instances['description'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('description');
            var editor = CKEDITOR.instances['description'];
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
