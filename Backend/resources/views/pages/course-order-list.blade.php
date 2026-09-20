@extends('../layout/' . $layout)

@section('subhead')
    <title>Course Order List</title>
@endsection

@section('subcontent')
<style>
    span.select2-dropdown.select2-dropdown--below {
        height: 105px;
        overflow-y: auto;
    }
</style>
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Course Order List</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            </div>

            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
                <form action="{{ route('courseOrderList') }}" method="GET" enctype="multipart/form-data" id="filterForm">
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
                        <th class="whitespace-nowrap">Astrologer </th>
                        <th class="text-center whitespace-nowrap">Course Name</th>
                        {{-- <th class="text-center whitespace-nowrap">Course Price</th> --}}
                        {{-- <th class="text-center whitespace-nowrap">Gst amount</th> --}}
                        <th class="text-center whitespace-nowrap">Total Amount</th>
                        <th class="text-center whitespace-nowrap">Completion Status</th>
                        <th class="text-center whitespace-nowrap">Date</th>


                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                        @foreach ($courseOrderlist as $order)
                            <tr class="intro-x">
                                <td>{{ ($page - 1) * 15 + ++$no }}</td>
                                <td class="text-center">
                                <div class="flex items-center">

                                            <div class="image-fit zoom-in" style="height:2.3rem;width:2.3rem;">

                                                @if(@$order->astrologer->profileImage!=null)
                                                <img class="rounded-full" src="/{{ @$order->astrologer->profileImage }}"
                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                    alt="image" />
                                                @else
                                                 <img class="rounded-full" src="/build/assets/images/person.png"
                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                    alt="image" />

                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium">{{ $order->astrologer->name??'---' }}</div>
                                            </div>
                                        </div>
                                </td>
                                <td class="text-center">{{$order->course->name}}</td>
                                {{-- <td class="text-center">{{$currency->value}} {{$order->course_price }}</td> --}}
                                {{-- <td class="text-center">{{$currency->value}} {{$order->course_gst_amount }}</td> --}}
                                <td class="text-center">{{$currency->value}} {{number_format($order->course_total_price,2) }}</td>
                                <td class="text-center">{{$order->course_completion_status}}</td>
                                <td class="text-center">{{date("d-m-Y h:i a" , strtotime($order->created_at))}}</td>

                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
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
                        <a class="page-link" href="{{ route('courseOrderList', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('courseOrderList', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('courseOrderList', ['page' => $page + 1]) }}">
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

    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
        document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('courseOrderList') }}"; // Redirect to remove query parameters
    });
    </script>
@endsection
