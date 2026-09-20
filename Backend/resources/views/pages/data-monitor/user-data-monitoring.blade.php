@extends('../layout/' . $layout)

@section('subhead')
    <title>ContactUs List</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Chats Monitoring</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
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

                        <th class="whitespace-nowrap">Name</th>
                        <th class="whitespace-nowrap">Contact</th>
                        <th class="whitespace-nowrap">Email</th>
                        <th class="whitespace-nowrap">Defaulter Time</th>
                        <th class="whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($userDefaulter as $clist)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->name ? $clist->name : '--' }}
                                   
                                </div>
                            </td>
                            <td>
                               <div class="font-medium whitespace-nowrap">
                                    {{ $clist->contactNo ? $clist->contactNo : '--' }}
                                </div>
                            </td>
                             <td>
                               <div class="font-medium whitespace-nowrap">
                                    {{ $clist->email ? $clist->email : '--' }}
                                </div>
                            </td>

                            
                             <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->defaulter_count}}</div>
                            </td>
                             <td>
                                <a class="flex items-center mr-3 text-success" href="{{ route('user.data.monitoring.id', ['id' => $clist->user_id]) }}">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>Show
                                </a>
                            </td>
                        
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
                        <a class="page-link" href="{{ route('contactlist', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('contactlist', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('contactlist', ['page' => $page + 1]) }}">
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
        })
    </script>
@endsection
