@extends('../layout/' . $layout)

@section('subhead')
    <title>Banner</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Banners</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-gift"
        class="d-inline addbtn mt-10 btn btn-primary shadow-md mr-2">Add
        Banner
    </a>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">


        </div>
    </div>
    @if ($totalRecords > 0)
        <div class="grid grid-cols-12 gap-6 mt-5 grid-table-without-search">
            @foreach ($banners as $banner)
                <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                    <div class="box">
                        <div class="p-5">
                            <div
                                class="h-40 2xl:h-56 image-fit rounded-md overflow-hidden before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black before:to-black/10">
                                <img class="rounded-full cursor-pointer" src="{{ Str::startsWith($banner->bannerImage, ['http://','https://']) ? $banner->bannerImage : '/' . $banner->bannerImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $banner->bannerImage }}')" />
                            </div>
                            <div class="text-slate-600 dark:text-slate-500 mt-5">
                                <div class="flex items-center">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    From: {{ date('d-m-Y', strtotime($banner->fromDate)) }}
                                </div>
                                <div class="flex items-center mt-2">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    To: {{ date('d-m-Y', strtotime($banner->toDate)) }}
                                </div>
                                <div class="flex items-center mt-2">
                                    <i data-lucide="video" class="w-4 h-4 mr-2"></i>
                                    {{$banner->bannerType}}
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-center lg:justify-center items-center p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                            <a id="editbtn" href="javascript:;"
                                onclick="editbtn({{ $banner->id }} ,'{{ $banner->bannerImage }}' , '{{ $banner->fromDate }}' , '{{ $banner->toDate }}','{{ $banner->bannerTypeId }}')"
                                class="flex items-center mr-3 " data-tw-target="#edit-banner" data-tw-toggle="modal"><i
                                    data-lucide="check-square" class="editbtn w-4 h-4 mr-1"></i>Edit</a>
                            <div
                                class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0">
                                <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                    href="javascript:;" data-tw-toggle="modal" data-onstyle="success" data-offstyle="danger"
                                    data-toggle="toggle" data-on="Active" data-off="InActive"
                                    {{ $banner->isActive ? 'checked' : '' }}
                                    onclick="editBanners({{ $banner->id }},{{ $banner->isActive }})" href="$banner->id"
                                    data-tw-target="#verified">
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
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
    @if ($totalRecords > 0)
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('banners', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link" href="{{ route('banners', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('banners', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @endif
    <div id="add-gift" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Banner</h2>
                </div>
                <form action="{{ route('addBannerApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">

                                <div class="grid grid-cols-12 gap-6">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <label for="image" class="form-label">Banner Image</label>
                                            <img id="thumb" width="150px" alt="bannerImage" style="display:none" />
                                            <input type="file" class="p-2" name="bannerImage" id="bannerImage"
                                                onchange="preview()" accept="image/*" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="fromDate" class="form-label">From Date</label>
                                            <input type="date" name="fromDate" id="fromDate" class="form-control"
                                                placeholder="From Date" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label for="toDate" class="form-label">To Date</label>
                                            <input type="date" name="toDate" id="toDate" class="form-control"
                                                placeholder="To Date" required>
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="post-form-3" class="form-label">Banner Type</label>
                                        <select data-placeholder="Select categories" class="form-control"
                                            id="bannerTypeId" name="bannerTypeId">
                                            <option value="" disabled selected required>--Select Banner Type--
                                            </option>
                                            @foreach ($bannerType as $bannerTypes)
                                                <option id="bannerTypeId" value={{ $bannerTypes->id }} required>
                                                    {{ $bannerTypes->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add Banner</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-banner" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Banner</h2>
                </div>
                <form action="{{ route('editBannerApi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="filed_id" name="filed_id">
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="intro-y col-span-12">
                                        <div>
                                            <label for="image" class="form-label">Image</label>
                                            <img id="thumbs" width="150px" alt="bannerImage"
                                                onerror="this.style.display='none';" />
                                            <input type="file" class="p-2" name="bannerImage"
                                                onchange="previews()" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="fromDate" class="form-label">From Date</label>
                                            <input type="date" name="fromDate" id="fid" class="form-control"
                                                placeholder="From Date" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label for="toDate" class="form-label">To Date</label>
                                            <input type="date" name="toDate" id="tid" class="form-control"
                                                placeholder="To Date" required>
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="post-form-3" class="form-label">Banner Type</label>
                                        <select data-placeholder="Select categories" value="bannerTypeId"
                                            class="form-control" id="ebannerTypeId" name="bannerTypeId">
                                            <option disabled selected required>--Select Banner Type--</option>
                                            @foreach ($bannerType as $bannerTypes)
                                                <option value={{ $bannerTypes->id }} required>
                                                    {{ $bannerTypes->name }}
                                                </option>
                                            @endforeach
                                        </select>
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


    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('bannerStatusApi') }}" method="POST" enctype="multipart/form-data">
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
    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script type="text/javascript">
        function editbtn($id, $bannerImage, $fromDate, $toDate, $bannerTypeId) {

            var id = $id;
            var fid = $id;
            var aid = $id;
            $cid = id;

            $('#filed_id').val($cid);
            $('#ebannerTypeId').val($bannerTypeId);
            if ($fromDate) {
                var newdate = $fromDate.split("-");
                var date = newdate[2].split(" ");
                date = newdate[0] + '-' + newdate[1] + '-' + date[0]
                $('#fid').val(date);
            }
            if ($toDate) {
                var newdate = $toDate.split("-");
                var date = newdate[2].split(" ");
                date = newdate[0] + '-' + newdate[1] + '-' + date[0]
                $('#tid').val(date);
            }

            document.getElementById("thumbs").src = "/" + $bannerImage;
        }

        function delbtn($id, $name) {
            var id = $id;
            $did = id;

            $('#del_id').val($did);
            $('#id').val($id);
        }

        function editBanner($id, $name) {
            var id = $id;
            $fid = id;

            $('#status_id').val($fid);
            $('#id').val($name);
        }


        function editBanners($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
        }


        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
        }

        function previews() {
            document.getElementById("thumbs").style.display = "block";
            thumbs.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
