@extends('../layout/' . $layout)

@section('subhead')
    <title>Puja Order List</title>
@endsection

@section('subcontent')
<style>
    span.select2-dropdown.select2-dropdown--below {
        height: 105px;
        overflow-y: auto;
    }
</style>
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Puja Order List</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            </div>

             <!-- Separate Date Range Filter Form -->
             <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="{{ route('puja-order-list') }}" method="GET" enctype="multipart/form-data" id="filterForm">
                    <!-- From Date -->
                    <label for="from_date" class="font-bold">From :</label>
                    <input type="date" name="from_date" value="{{ $from_date ?? '' }}" class="form-control w-56 box mr-2">

                    <!-- To Date -->
                    <label for="to_date" class="font-bold">To :</label>
                    <input type="date" name="to_date" value="{{ $to_date ?? '' }}" class="form-control w-56 box mr-2">

                    <button class="btn btn-primary shadow-md mr-2">Filter</button>
                    <button type="button" id="clearButton" class="btn btn-secondary">
                        <i data-lucide="x"  class="w-4 h-4 mr-1"></i> Clear
                    </button>
                </form>
              </div>
        </div>
        
    </div>
    @if ($totalRecords > 0)
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2" aria-label="customer-list">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">Astrologer</th>
                        <th class="whitespace-nowrap">User </th>
                        <th class="text-center whitespace-nowrap">Contact</th>
                        <th class="text-center whitespace-nowrap">Address</th>
                        <th class="text-center whitespace-nowrap">Puja</th>
                        <th>Puja Images</th>
                        <th class="text-center whitespace-nowrap">Package</th>
                        <th class="text-center whitespace-nowrap">Price</th>
                        <th class="text-center whitespace-nowrap">Date</th>
                        <th class="text-center whitespace-nowrap">Status</th>

                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                        @foreach ($pujaOrderlist as $order)
                            <tr class="intro-x">
                                <td>{{ ($page - 1) * 15 + ++$no }}</td>
                                <td>
                                    <div class="font-medium whitespace-nowrap" id="pujaOrder"  style="width: 150px;">
                                            <div>
                                            <select class="form-control select2 assignPuja" name="astrologerId" data-id="{{ $order->id }}">
                                                <option value="" disabled selected required>--Select User--</option>
                                                @foreach ($astrologers as $astrologerName)
                                                <option value="{{ $astrologerName->id }}" {{ $order->astrologer_id == $astrologerName->id ? 'selected' : '' }}>
                                                    {{ $astrologerName->name }}
                                                </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                <div class="flex items-center">

                                            <div class="image-fit zoom-in" style="height:2.3rem;width:2.3rem;">

                                                @if(@$order->astrologer->profileImage!=null)
                                                <img class="rounded-full cursor-pointer"
                             src="{{ Str::startsWith( @$order->astrologer->profileImage, ['http://','https://']) ? @$order->astrologer->profileImage : '/' . @$order->astrologer->profileImage }}"
                             onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                             alt="Customer image"
                             onclick="openImage('{{ Str::startsWith( @$order->astrologer->profileImage, ['http://','https://']) ? @$order->astrologer->profileImage : '/' . @$order->astrologer->profileImage }}')" />
                                                @else
                                                 <img class="rounded-full" src="/build/assets/images/person.png"
                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                    alt="image" />

                                                @endif
                                            </div>
                                        </div>
                                </td>
                                <td class="text-center">{{$order->address_number}}</td>
                                <td class="text-center">{{$order->address_flatno }},{{ $order->address_locality}},{{ $order->address_landmark}},{{ $order->address_city }},{{ $order->address_state }},{{ $order->address_country }},{{ $order->address_pincode }}</td>
                                <td class="text-center">{{$order->puja_name}}</td>
                                <td class="text-center">
                                    <div class="flex items-center">
                                            <div class="image-fit zoom-in" style="height:2.3rem;width:2.3rem;">
                                    @if(!empty($order->pujas) && !empty($order->pujas->puja_images))
                                        @php
                                            $images = is_array($order->pujas->puja_images) ? $order->pujas->puja_images : explode(',', $order->pujas->puja_images);
                                        @endphp

                                            @foreach($images as $image)
                                            <img class="rounded-full cursor-pointer"
                                                src="{{ Str::startsWith( $image, ['http://','https://']) ? $image : '/' . $image }}"
                                                onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                alt="Customer image"
                                                onclick="openImage('{{ Str::startsWith( $image, ['http://','https://']) ? $image : '/' . $image }}')" />
                                            @endforeach
                                    @else
                                        <img class="rounded-full" src="/build/assets/images/person.png"
                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                    alt="image" />
                                    @endif
                                    </div>
                                        </div>
                                </td>
                                <td class="text-center">{{$order->package_name}}</td>
                                <td class="text-center">{{$currency->value}} {{number_format($order->order_total_price,2)}}</td>
                                <td class="text-center">
                                    {{ date('d-m-Y', strtotime($order->created_at)) ? date('d-m-Y h:i a', strtotime($order->created_at)) : '--' }}
                                </td>
                                <td class="text-center">{{$order->puja_order_status}}</td>
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

        <!-- END: Data List -->
        <!-- BEGIN: Pagination -->
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline intro-y col-span-12 addbtn ">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination" id="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('puja-order-list', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('puja-order-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('puja-order-list', ['page' => $page + 1]) }}">
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
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  ></script>
<script>
        $(document).ready(function() {
            jQuery('.select2').select2();
        });


    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {

        jQuery(document).on('change','.assignPuja',function(){
            const astrologerId = $(this).val();
            const pujaOrderId = $(this).data('id');

            if (astrologerId) {
                jQuery.ajax({
                    url: '{{ route('puja-order.update') }}',
                    type: 'POST',
                    data: {
                        astrologer_id: astrologerId,
                        puja_order_id: pujaOrderId,
                    },
                    success: function(response) {

                        console.log(response);
                        if (response.success) { // Assuming your response has a success field
                            toastr.success('Astrologer assigned successfully!', 'Success', {
                                "closeButton": true,
                                "progressBar": true
                            });
                        } else {
                            printErrorMsg(response.error);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to assign astrologer. Please try again.', 'Error', {
                            "closeButton": true,
                            "progressBar": true
                        });
                        console.error('Update failed:', xhr);
                    }
                });
            }
        });
    });

    function printErrorMsg(msg) {
        // Display error messages
        jQuery.each(msg, function(key, value) {
            toastr.error(value, 'Validation Error', {
                "closeButton": true,
                "progressBar": true
            });
        });
    }

</script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
        document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('puja-order-list') }}"; // Redirect to remove query parameters
    });
    </script>
@endsection
