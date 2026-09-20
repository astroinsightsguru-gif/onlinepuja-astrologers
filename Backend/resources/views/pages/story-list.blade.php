@extends('../layout/' . $layout)

@section('subhead')
    <title>Story</title>
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')->where('name', 'currencySymbol')->select('value')->first();
    @endphp
    <div class="loader"></div>
    <h2 class="d-inline intro-y text-lg font-medium mt-10">Stories</h2>

    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if (count($story) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
    <table class="table table-report -mt-2" aria-label="gift">
        <thead class="sticky-top">
            <tr>
                <th class="whitespace-nowrap">#</th>
                <th class="whitespace-nowrap">PROFILE</th>
                <th class="text-center whitespace-nowrap">NAME</th>
                <th class="text-center whitespace-nowrap">MEDIATYPE</th>
                <th class="text-center whitespace-nowrap">MEDIA</th>
                <th class="text-center whitespace-nowrap">VIEWS</th>
                <th class="text-center whitespace-nowrap">CREATED AT</th>
                <th class="text-center whitespace-nowrap">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach ($story as $stori)
                <tr class="intro-x">
                    <td>{{ ($page - 1) * 15 + ++$no }}</td>
                    <td>
                        <div class="flex">
                            <div class="w-10 h-10 image-fit zoom-in">
                                <img class="rounded-full cursor-pointer" src="{{ Str::startsWith($stori->profileImage, ['http://','https://']) ? $stori->profileImage : '/' . $stori->profileImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $stori->profileImage }}')" />
                            </div>
                        </div>
                    </td>
                    <td class="text-center">{{ $stori->name }}</td>
                    <td class="text-center">{{ $stori->mediaType }}</td>

                    @if ($stori->mediaType == 'image')
                        <td class="text-center">
                            <div class="flex justify-center">
                                <div class="w-10 h-10 image-fit zoom-in">
                                    <img class="rounded-full cursor-pointer"
                                        src="/{{ $stori->media }}"
                                        onerror="this.onerror=null;this.src='/build/assets/images/default.jpg';"
                                        alt="{{ ucfirst($professionTitle) }} image"
                                        onclick="showImageModal('{{ '/' . $stori->media }}')" />
                                </div>
                            </div>
                        </td>
                    @elseif($stori->mediaType == 'video')
                        <td class="text-center">
                            <video width="140" height="60" controls style="display:inline">
                                <source src="/{{ $stori->media }}" type="video/mp4">
                            </video>
                        </td>
                    @else
                        <td class="text-center font-medium">{{ $stori->media }}</td>
                    @endif

                    <td class="text-center text-success font-medium">{{ $stori->StoryViewCount }}</td>
                    <td class="text-center">{{ date('d-m-Y h:i a', strtotime($stori->created_at)) }}</td>

                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a id="editbtn" href="javascript:;" onclick="delbtn({{ $stori['id'] }})"
                                value="{{ $stori['id'] }}" class="flex items-center text-danger"
                                data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal">
                                <i data-lucide="trash-2" class="editbtn w-4 h-4 mr-1"></i>Delete
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Fullscreen Image Viewer -->
<div class="image-overlay" id="imageOverlay">
                <img src="your-image.jpg" id="popupImage" alt="Full Screen Image">
                <span class="closebtn" id="closeBtn">&times;</span>
            </div>
            <script>
            const overlay = document.getElementById('imageOverlay');
            const closeBtn = document.getElementById('closeBtn');
            function openImage(src) {
                document.getElementById('popupImage').src = src;
                overlay.classList.add('active');
            }
            closeBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
            });
            overlay.addEventListener('click', (e) => {
                if(e.target === overlay) {
                    overlay.classList.remove('active');
                }
            });
            </script>

 
        @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
    @endif
    <div class="d-inline addbtn intro-y col-span-12">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('story-list', ['page' => $page - 1]) }}">
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
                        <a class="page-link" href="{{ route('story-list', ['page' => 1]) }}">1</a>
                    </li>
                    @if ($startPage > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                @for ($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $page == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ route('story-list', ['page' => $i]) }}">{{ $i }}</a>
                    </li>
                @endfor

                @if ($endPage < $totalPages)
                    @if ($endPage < $totalPages - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ route('story-list', ['page' => $totalPages]) }}">{{ $totalPages }}</a>
                    </li>
                @endif

                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('story-list', ['page' => $page + 1]) }}">
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



    <!-- BEGIN: Delete Confirmation Modal -->
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

                    <form action="{{ route('deleteStory') }} " method="POST">
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


    <!-- Modal -->
<div id="imageModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center; z-index: 1000;">
    <div style="position: relative; max-width: 90%; max-height: 90%;">
        <!-- Full Image -->
        <img id="modalImage" src="" alt="Full View" style="max-width: 100%; max-height: 100%; border-radius: 8px;" />
        <!-- Close Button -->
        <button onclick="closeImageModal()" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
    </div>
</div>

    <!-- END: Delete Confirmation Modal -->
@endsection

@section('script')

<script>
    function showImageModal(imageSrc) {
     const modal = document.getElementById('imageModal');
     const modalImage = document.getElementById('modalImage');

     // Set the modal image source
     modalImage.src = imageSrc;
     // Display the modal
     modal.style.display = 'flex';
 }

 function closeImageModal() {
     const modal = document.getElementById('imageModal');
     // Hide the modal
     modal.style.display = 'none';
 }

 </script>
    <script type="text/javascript">
        function delbtn($id, $name) {
            var id = $id;
            $did = id;

            $('#del_id').val($did);
            $('#id').val($id);
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
