@extends('../layout/' . $layout)

@section('subhead')
    <title>Admin TDS Earnings</title>
@endsection

@section('subcontent')
<style>
/* Small modal styles - adjust as needed */
.simple-modal { display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; }
.simple-modal.open { display: flex; }
.simple-modal .overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.45); }
.simple-modal .dialog { position: relative; background: #fff; max-width: 520px; width: 100%; border-radius: 8px; box-shadow: 0 6px 30px rgba(0,0,0,0.2); z-index: 2; padding: 0; overflow: hidden; }
.simple-modal .header, .simple-modal .footer { padding: 12px 16px; border-bottom: 1px solid #eee; }
.simple-modal .header { display:flex; justify-content:space-between; align-items:center; }
.simple-modal .body { padding: 16px; }
.simple-modal .footer { border-top: 1px solid #eee; border-bottom: none; text-align: right; }
.simple-modal .close-btn { background: transparent; border: none; font-size: 20px; cursor: pointer; }
.simple-modal textarea.form-control { width:100%; min-height:100px; padding:8px; border:1px solid #ccc; border-radius:4px; resize:vertical; }
.simple-modal .btn { padding:6px 12px; border-radius:4px; cursor:pointer; border: none; }
.simple-modal .btn-secondary { background:#6c757d; color:#fff; }
.simple-modal .btn-danger { background:#dc3545; color:#fff; }
</style>

<div class="loader"></div>
<h2 class="intro-y text-lg font-medium mt-10 d-inline">Admin TDS Earnings</h2>

<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
<a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            <form action="{{ route('tds-gst') }}" method="GET" enctype="multipart/form-data">
                <div class="w-56 relative text-slate-500" style="display:inline-block">
                    <input value="{{ request('searchString') }}" type="text" class="form-control w-56 box pr-10"
                           placeholder="Search by user name..." id="searchString" name="searchString">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
                <button class="btn btn-primary shadow-md mr-2">Search</button>
            </form>
        </div>
        <div class="w-50 sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            <form action="{{ route('tds-gst') }}" method="GET" id="dropdownForm">
                <select name="orderType" id="orderType" class="form-control box mr-2 ml-5">
                    <option value="">Withdraw Type</option>
                    <option value="pending" {{ request('orderType') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approve" {{ request('orderType') == 'approve' ? 'selected' : '' }}>Approved</option>
                    <option value="reject" {{ request('orderType') == 'reject' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-auto">
            <form action="{{ route('tds-gst') }}" method="GET" enctype="multipart/form-data" id="filterForm">
                <label for="from_date" class="font-bold">From :</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control w-56 box mr-2">

                <label for="to_date" class="font-bold">To :</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control w-56 box mr-2">

                <button class="btn btn-primary shadow-md mr-2">Filter</button>
                <button type="button" id="clearButton" class="btn btn-secondary">
                    <i data-lucide="x" class="w-4 h-4 mr-1"></i> Clear
                </button>
            </form>
        </div>
    </div>
</div>
@if (count($AdminGetTDScomm) > 0)
<div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table mt-5">
    <table class="table table-report mt-2" aria-label="customer-list">
        <thead class="sticky-top">
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Withdraw Amount</th>
                <th>Admin Commission</th>
                <th>Status</th>
                <th>Reject Reason</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($AdminGetTDScomm as $key => $item)
            <tr class="intro-x">
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->user->name ?? 'N/A' }}</td>
                <td>₹ {{ $item->amount }}</td>
                <td>₹ {{ $item->commission }}</td>
                <td>
                    @if($item->status == 0)
                        <span class="btn btn-sm badge bg-warning text-white">Pending</span>
                    @elseif($item->status == 1)
                        <span class="btn btn-sm badge bg-success text-white">Approved</span>
                    @elseif($item->status == 2)
                        <span class="btn btn-sm badge bg-danger text-white">Rejected</span>
                    @endif
                </td>
                <td>{{ $item->reject_reason ?? '—' }}</td>
                <td class="text-center">
                    @if($item->status == 0)
                        <a href="{{ route('tds-gst.status', ['id' => $item->id, 'action' => 'approve']) }}" 
                           class="btn btn-sm btn-success text-white"
                           onclick="return confirm('Are you sure you want to accept this withdraw request?')">
                           Approve
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-danger reject-btn" 
                                data-id="{{ $item->id }}">
                            Reject
                        </button>
                    @else
                        <span class="btn btn-sm text-white" style="background: black;">Rejected</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center mt-10">
    <img src="/build/assets/images/nodata.png" style="height:200px" alt="No Data">
    <h3 class="mt-3">No Data Available</h3>
</div>
@endif

<div id="rejectModalSimple" class="simple-modal" aria-hidden="true">
  <div class="overlay" data-close-modal></div>
  <div class="dialog" role="dialog" aria-modal="true" aria-labelledby="rejectModalLabel">
    <div class="header">
      <h4 id="rejectModalLabel" class="m-0">Reject Withdrawal</h4>
      <button type="button" class="close-btn" data-close-modal aria-label="Close">×</button>
    </div>
    <form id="rejectForm" method="POST" action="{{ route('tds-gst.reject') }}">
      @csrf
      <input type="hidden" name="id" id="reject_id">
      <div class="body">
        <label for="reject_reason" class="font-bold">Reason for Rejection</label>
        <textarea name="reject_reason" id="reject_reason" class="form-control" required></textarea>
      </div>
      <div class="footer">
        <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn-danger">Reject</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('script')
<script>
document.getElementById('orderType').addEventListener('change', function() {
    document.getElementById('dropdownForm').submit();
});
document.getElementById('clearButton').addEventListener('click', function() {
    window.location.href = "{{ route('tds-gst') }}";
});

// ===== Modal logic (vanilla JS) =====
(function () {
    const modal = document.getElementById('rejectModalSimple');
    const rejectIdInput = document.getElementById('reject_id');
    const reasonInput = document.getElementById('reject_reason');

    // Open modal on reject button click
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            rejectIdInput.value = id;
            reasonInput.value = '';
            modal.classList.add('open');
            reasonInput.focus();
        });
    });

    // Close modal handlers (buttons and overlay)
    modal.querySelectorAll('[data-close-modal]').forEach(el => {
        el.addEventListener('click', function () {
            modal.classList.remove('open');
        });
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
            modal.classList.remove('open');
        }
    });

    // Optional: basic client-side validation on submit to ensure non-empty reason
    document.getElementById('rejectForm').addEventListener('submit', function (e) {
        if (!reasonInput.value.trim()) {
            e.preventDefault();
            alert('Please enter a reject reason.');
            reasonInput.focus();
            return false;
        }
        // allow submit (POST)
    });
})();
</script>
@endsection
