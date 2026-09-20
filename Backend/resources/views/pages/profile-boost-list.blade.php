@extends('../layout/' . $layout)

@section('subhead')
<title>Profile Benifts</title>
@endsection

@section('subcontent')
<div class="loader"></div>
<h2 class="intro-y text-lg font-medium mt-10 d-inline">Profile Benifts</h2>

@if ($totalRecords > 0)
    <!-- BEGIN: Data List -->
    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
        <table class="table table-report mt-2" aria-label="customer-list">
            <thead class="sticky-top">
                <tr>
                    <th class="whitespace-nowrap ">#</th>
                    <th class="whitespace-nowrap text-center">CHAT COMMISSION</th>
                    <th class="whitespace-nowrap text-center">CALL COMMISSION</th>
                    <th class="text-center whitespace-nowrap">PROFILE MONTHLY BOOST</th>
                    <th class="text-center whitespace-nowrap">PROFILE BENEFITS</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="todo-list">
                @php
                    $no = 0;
                @endphp
                @foreach ($profilelist as $boost)
                @php
                    $benefits = is_array($boost->profile_boost_benefits) ? $boost->profile_boost_benefits : json_decode($boost->profile_boost_benefits, true);
                    $benefitsString = is_array($benefits) ? implode(', ', $benefits) : $benefits;
                @endphp
                    <tr class="intro-x">
                        <td>{{ ($page - 1) * 15 + ++$no }}</td>
                        <td>
                            <div class="font-medium whitespace-nowrap text-center">{{ $boost->chat_commission ? $boost->chat_commission : '--' }} %</div>
                        </td>
                        <td class="text-center">{{ $boost->call_commission ? $boost->call_commission : '--' }} %</td>
                        <td class="text-center">{{ $boost->profile_boost ? $boost->profile_boost : '--' }}</td>
                        <td>
                            <div class="font-medium whitespace-nowrap text-center" style="cursor: pointer;" title="{{ $benefitsString }}">
                                {{ Str::words($benefitsString, 10, '...') }}
                            </div>
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                            <a class="flex items-center mr-3 " href="{{ route('profile-boost', $boost->id)}}" aria-disabled="true"><i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit</a>

                            </div>
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
            {{ $totalRecords }} entries
        </div>
    @endif
    <div class="d-inline addbtn intro-y col-span-12">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('profile-list', ['page' => $page - 1]) }}">
                        <i class="w-4 h-4" data-lucide="chevron-left"></i>
                    </a>
                </li>
                @for ($i = 0; $i < $totalPages; $i++)
                    <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                        <a class="page-link" href="{{ route('profile-list', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                    </li>
                @endfor
                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ route('profile-list', ['page' => $page + 1]) }}">
                        <i class="w-4 h-4" data-lucide="chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
@else
    <div class="intro-y mt-5" style="height:100%">
        <div style="display:flex;align-items:center;height:100%;">
            <div style="margin:auto">
                <img src="build/assets/images/nodata.png" style="height:290px" alt="noData">
                <h3 class="text-center">No Data Available</h3>
            </div>
        </div>
    </div>
@endif
<!-- END: Pagination -->

@endsection

@section('script')
<script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
</script>
@if (Session::has('error'))
<script type="text/javascript">
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.warning("{{ session('error') }}");
</script>
@endif
<script type="text/javascript">
    var spinner = $('.loader');
</script>
<script>
    $(window).on('load', function () {
        $('.loader').hide();
    })
</script>
@endsection
