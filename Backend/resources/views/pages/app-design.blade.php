@extends('../layout/' . $layout)

@section('subhead')
    <title>App Design</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">App Design</h2>

    </div>
    @if (count($appdesign) > 0)
    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
        <table class="table table-report -mt-2" aria-label="gift">
            <thead class="sticky-top">
                <tr>
                    <th class="whitespace-nowrap">#</th>
                    <th class="whitespace-nowrap">Images</th>
                    <th class="text-center whitespace-nowrap">TITLE</th>
                    <th class="text-center whitespace-nowrap">STATUS</th>
                    <th class="text-center whitespace-nowrap">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 0;
                @endphp
            @foreach ($appdesign as $design)
            <tr class="intro-x">
                <td>{{ ($page - 1) * 15 + ++$no }} </td>
                <td class="px-5 py-3 border-b dark:border-darkmode-300 box w-40 rounded-l-none rounded-r-none border-x-0 shadow-[5px_3px_5px_#00000005] first:rounded-l-[0.6rem] first:border-l last:rounded-r-[0.6rem] last:border-r dark:bg-darkmode-600">
                    <div class="flex">
                        @php
                            $images = is_string($design->image) ? json_decode($design->image) : $design->image;
                            $displayImages = array_slice($images, 0, 3);
                        @endphp

                        @foreach($displayImages as $index => $imagePath)
                            @php
                                // Clean and format the path properly
                                $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $imagePath), '/');
                                $fullPath = asset("storage/{$cleanPath}");
                            @endphp
                            <div class="image-fit zoom-in @if($index > 0) -ml-5 @endif h-10 w-10">
                                <img data-placement="top"
                                     src="/{{ $imagePath }}"
                                     data-fullsrc="/{{ $imagePath }}"
                                     alt="Design image {{ $index + 1 }}"
                                     class="tooltip cursor-pointer rounded-full shadow-[0px_0px_0px_2px_#fff,_1px_1px_5px_rgba(0,0,0,0.32)] dark:shadow-[0px_0px_0px_2px_#3f4865,_1px_1px_5px_rgba(0,0,0,0.32)]"
                                     onclick="openImageModal(this)">
                            </div>
                        @endforeach
                    </div>
                </td>
                <td class="text-center">{{ $design->title }}</td>
                <td class="text-center">
                    <span class="{{ $design->is_active ? 'text-success' : 'text-danger' }}">
                        {{ $design->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="table-report__action w-56">
                    <div class="flex justify-center items-center">
                        <a id="editbtn" href="javascript:;" onclick="status({{ $design['id'] }})"
                            value="{{ $design['id'] }}" class="flex items-center text-danger"
                            data-tw-target="#verified" data-tw-toggle="modal">
                            <i data-lucide="lock" class="editbtn w-4 h-4 mr-1"></i>Change Status
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
            </tbody>
        </table>
    </div>
    @if ($totalRecords > 0)
    <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
@endif
<div class="d-inline addbtn intro-y col-span-12">
    <nav class="w-full sm:w-auto sm:mr-auto">
        <ul class="pagination">
            <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ route('getAppdesign', ['page' => $page - 1]) }}">
                    <i class="w-4 h-4" data-lucide="chevron-left"></i>
                </a>
            </li>

            @php
                $showPages = 15; // Number of pages to show at a time
                $halfShowPages = floor($showPages / 2);
                $startPage = max(1, $page - $halfShowPages);
                $endPage = min($startPage + $showPages - 1, $totalPages);
            @endphp

            @if ($startPage > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ route('getAppdesign', ['page' => 1]) }}">1</a>
                </li>
                @if ($startPage > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            @for ($i = $startPage; $i <= $endPage; $i++)
                <li class="page-item {{ $page == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ route('getAppdesign', ['page' => $i]) }}">{{ $i }}</a>
                </li>
            @endfor

            @if ($endPage < $totalPages)
                @if ($endPage < $totalPages - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ route('getAppdesign', ['page' => $totalPages]) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                <a class="page-link" href="{{ route('getAppdesign', ['page' => $page + 1]) }}">
                    <i class="w-4 h-4" data-lucide="chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>

@else
    <div class="intro-y" style="height:100%">
        <div style="display:flex;align-items:center;height:100%;">
            <div style="margin:auto">
                <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                <h3 class="text-center">No Data Available</h3>
            </div>
        </div>
    </div>
@endif

<!-- Image Modal -->
<div id="imageModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center; z-index: 1000;">
    <div style="position: relative; max-width: 90%; max-height: 90%;">
        <img id="modalImage" src="" alt="Full View" style="max-width: 100%; height: 680px; border-radius: 8px;" />
        <button onclick="closeImageModal()" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
    </div>
</div>

    @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
            {{ $totalRecords }} entries</div>
    @endif
    @if (count($appdesign) > 0)
        <div class="d-inline addbtn intro-y col-span-12 ">
            <nav class="w-full sm:w-auto sm:mr-auto" aria-label="adsVideo">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('getAppdesign', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('getAppdesign', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('getAppdesign', ['page' => $page + 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @endif
    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('appDesignStatus') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="status_id" name="status_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnActive">Yes,
                                Active it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                               >Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        var spinner = $('.loader');


        function status($id, $isActive) {
            var id = $id;
            $fid = id;
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";

            $('#status_id').val($fid);
        }

    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });

// Function to open modal with clicked image
function openImageModal(imgElement) {
    const imageSrc = imgElement.dataset.fullsrc;
    console.log('Opening image:', imageSrc);

    const modalImg = document.getElementById('modalImage');
    modalImg.src = ''; // Clear first to force reload
    modalImg.src = imageSrc + '?t=' + Date.now(); // Add cache buster

    document.getElementById('imageModal').style.display = 'flex';
}

// Function to close modal
function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
    </script>
@endsection
