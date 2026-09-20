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
                <form method="GET" action="{{ route('chats.monitoring') }}" id="searchForm">

                    <select name="astrologerId" id="astrologerId">
                        <option value="">-- Select {{ucfirst($professionTitle)}} --</option>
                        @foreach($astrologers as $astrologer)
                            <option value="{{ $astrologer->id }}" {{ request('astrologerId') == $astrologer->id ? 'selected' : '' }}>
                                {{ $astrologer->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="userId" id="userId">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('userId') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->contactNo }})
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="date" id="date" value="{{ request('date') }}">

                   <button type="submit" class="btn btn-primary">
                        <i data-lucide="search"  class="w-4 h-4 mr-1"></i> Search
                    </button>

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
                        <!--<th class="whitespace-nowrap">Astro User Id</th>-->

                        <th class="whitespace-nowrap">Counsellor Name</th>
                        <!--<th class="whitespace-nowrap">User Id</th>-->
                        <th class="whitespace-nowrap">User Name</th>
                        <th class="whitespace-nowrap">Date</th>
                        <th class="whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($completedChats as $clist)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <!--<td>{{$clist->astroUserId}}</td>-->

                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->astrologerName ? $clist->astrologerName : $clist->contactNo }}
                                     @if (isset($astroDefaulterCounts[$clist->astroUserId]))
                                        <!--<span class="badge bg-danger p-1  text-white rounded">-->
                                        <!--    {{ $astroDefaulterCounts[$clist->astroUserId] }}-->
                                        <!--</span>-->
                                        <a href="{{ route('user.data.monitoring.id', ['id' => $clist->astroUserId]) }}" class="badge bg-danger p-1 text-white rounded">
                                            {{ $astroDefaulterCounts[$clist->astroUserId] }}
                                        </a>
                                    @else
                                        <span class="badge bg-success p-1 text-white rounded">0</span> {{-- If no defaulter messages --}}
                                    @endif
                                </div>
                            </td>
                            <!--<td>{{$clist->userId}}</td>-->

                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->Username ? $clist->Username : $clist->contactNo }}
                                     @if (isset($userDefaulterCounts[$clist->userId]))
                                        <!--<span class="badge bg-danger p-1 text-white rounded">-->
                                        <!--    {{ $userDefaulterCounts[$clist->userId] }}-->
                                        <!--</span>-->
                                        <a href="{{ route('user.data.monitoring.id', ['id' => $clist->userId]) }}" class="badge bg-danger p-1 text-white rounded">
                                            {{ $userDefaulterCounts[$clist->userId] }}
                                        </a>
                                    @else
                                        <span class="badge bg-success p-1 text-white rounded">0</span> {{-- If no defaulter messages --}}
                                    @endif
                                </div>
                            </td>
                             <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $clist->updated_at ? date("d-m-Y h:i a", strtotime($clist->updated_at)) : '--' }}</div>
                            </td>
                             <td>
                                <a class="flex items-center mr-3 text-success" href="userchats/{{ $clist->chatId }}">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
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
                        <a class="page-link" href="{{ route('chats.monitoring', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('chats.monitoring', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('chats.monitoring', ['page' => $page + 1]) }}">
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

    <script>
    document.getElementById('clearButton').addEventListener('click', function () {
        const form = document.getElementById('searchForm');
        form.reset(); // Reset the form fields to their default values
        window.location.href = "{{ route('chats.monitoring') }}"; // Redirect to remove query parameters
    });
</script>
@endsection
