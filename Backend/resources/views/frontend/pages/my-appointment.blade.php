@extends('frontend.layout.master')

@section('content')
<div class="container my-5">
    <h2 class="text-center mb-4">📅 My Appointments</h2>

    {{-- 🔹 Filter Links --}}
    <div class="text-center mb-3 d-flex flex-wrap justify-content-center gap-2">
        <a href="javascript:void(0)" class="btn btn-outline-secondary filter-btn active" data-status="All">All</a>
        <a href="javascript:void(0)" class="btn btn-outline-info filter-btn" data-status="Scheduled">Scheduled</a>
        <a href="javascript:void(0)" class="btn btn-outline-primary filter-btn" data-status="Pending">Pending</a>
        <a href="javascript:void(0)" class="btn btn-outline-success filter-btn" data-status="Completed">Completed</a>
        <a href="javascript:void(0)" class="btn btn-outline-danger filter-btn" data-status="Rejected">Rejected</a>
        <a href="javascript:void(0)" class="btn btn-outline-warning filter-btn" data-status="Expired">Expired</a>
    </div>

    {{-- 🔹 Toastr Flash Messages --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            toastr.success("{{ session('success') }}");
            @endif
            @if(session('error'))
            toastr.error("{{ session('error') }}");
            @endif
            @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
            @endif
            @if(session('info'))
            toastr.info("{{ session('info') }}");
            @endif
        });
    </script>

    @if($appointments->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center" id="appointmentsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Astrologer</th>
                    <th>Call Type</th>
                    <th>Schedule Date</th>
                    <th>Schedule Time</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $index => $appointment)
                @php
                // Determine the true display status
                if ($appointment->appointmentStatus === 'Refunded') {
                $displayStatus = 'Rejected';
                $badgeClass = 'bg-danger';
                } elseif ($appointment->callStatus === 'Completed') {
                $displayStatus = 'Completed';
                $badgeClass = 'bg-success';
                } elseif ($appointment->callStatus === 'Rejected') {
                $displayStatus = 'Rejected';
                $badgeClass = 'bg-danger';
                } elseif ($appointment->IsSchedule == 1 && in_array($appointment->callStatus, ['Pending', 'Accepted'])) {
                if ($appointment->schedule_date && $appointment->schedule_time) {
                $scheduleDateTime = \Carbon\Carbon::parse($appointment->schedule_date . ' ' . $appointment->schedule_time);
                $duration = $appointment->call_duration/60 ?? 15;
                $endDateTime = $scheduleDateTime->copy()->addMinutes($duration);
                if (\Carbon\Carbon::now()->greaterThan($endDateTime)) {
                $displayStatus = 'Expired';
                $badgeClass = 'bg-secondary';
                } else {
                $displayStatus = 'Scheduled';
                $badgeClass = 'bg-info';
                }
                } else {
                $displayStatus = 'Scheduled';
                $badgeClass = 'bg-info';
                }
                } elseif ($appointment->callStatus === 'Pending') {
                $displayStatus = 'Pending';
                $badgeClass = 'bg-primary';
                } else {
                $displayStatus = $appointment->callStatus ?? 'Unknown';
                $badgeClass = 'bg-secondary';
                }

                // Determine action visibility (Cancel vs Delete)
                $showCancel = false;
                $showDelete = false;

                if ($appointment->IsSchedule == 1 && $appointment->schedule_date && $appointment->schedule_time) {
                $scheduleDateTime = \Carbon\Carbon::parse($appointment->schedule_date . ' ' . $appointment->schedule_time);
                $duration = ($appointment->call_duration / 60) ?? 15;
                $endDateTime = $scheduleDateTime->copy()->addMinutes($duration);
                $isExpired = \Carbon\Carbon::now()->greaterThan($endDateTime);

                if ($isExpired) {
                $showDelete = true;
                } elseif (in_array($displayStatus, ['Scheduled', 'Pending'])) {
                $showCancel = true;
                }
                } else {
                if (in_array($displayStatus, ['Pending', 'Scheduled'])) {
                $showCancel = true;
                } else {
                $showDelete = true;
                }
                }
                @endphp
                <tr data-status="{{ $displayStatus }}">
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <img src="{{ Str::startsWith($appointment->profileImage, ['http://','https://']) ? $appointment->profileImage : '/' . $appointment->profileImage }}"
                            onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                            class="rounded-circle me-2" width="40" height="40" alt="Astrologer">
                        {{ $appointment->astrologerName }}
                    </td>
                    <td>
                        {{ $appointment->call_type == 10 ? '🎙 Audio Call' : '📹 Video Call' }}
                        @if($appointment->call_duration)
                        <span class="text-muted small d-block mt-1">
                            <i class="fa-solid fa-clock me-1"></i> {{ $appointment->call_duration/60 }} Mins
                        </span>
                        @endif
                    </td>
                    <td>{{ $appointment->schedule_date ?? '-' }}</td>
                    <td>{{ $appointment->schedule_time ?? '-' }}</td>
                    <td>₹{{ number_format($appointment->amount, 2) }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $displayStatus }}</span>
                    </td>
                    <td>
                        @if($showCancel)
                        <form action="{{ route('appointment.delete', $appointment->callId) }}" method="POST" class="delete-appointment-form">
                            @csrf
                            <button type="button" class="btn btn-sm btn-warning text-dark trigger-confirm-btn" data-message="Are you sure you want to cancel this appointment?">Cancel</button>
                        </form>
                        @elseif($showDelete)
                        <form action="{{ route('appointment.delete', $appointment->callId) }}" method="POST" class="delete-appointment-form">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-danger trigger-confirm-btn" data-message="Are you sure you want to permanently delete this appointment record?">Delete</button>
                        </form>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="alert alert-info text-center">
        No appointments found.
    </div>
    @endif
</div>

{{-- 🔹 Filter Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.filter-btn');
        const rows = document.querySelectorAll('#appointmentsTable tbody tr');

        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Toggle active class
                buttons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const status = this.dataset.status;

                rows.forEach(row => {
                    if (status === 'All' || row.dataset.status === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });
</script>

{{-- 🔹 Confirmation Modal --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: none; padding-top: 20px; padding-bottom: 0;">
                <h5 class="modal-title w-100 text-center fw-bold" id="confirmationModalLabel" style="font-size: 1.25rem; color: #333;">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px; top: 20px; font-size: 1.5rem; background: transparent; border: none; outline: none; line-height: 1;">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fa-solid fa-triangle-exclamation fa-3x animate__animated animate__pulse animate__infinite"></i>
                </div>
                <p id="confirmationModalMessage" class="fw-semibold px-3" style="font-size: 1.05rem; color: #555;">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: none; padding-bottom: 25px;">
                <button type="button" class="btn btn-light px-4 py-2 fw-semibold" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px; border: 1px solid #ddd; color: #666;">No, Keep It</button>
                <button type="button" class="btn btn-danger px-4 py-2 fw-semibold" id="confirmActionButton" style="border-radius: 8px; background-color: #dc3545; border: none;">Yes, Proceed</button>
            </div>
        </div>
    </div>
</div>

{{-- 🔹 JS for Confirmation Modal --}}
<script>
    $(document).ready(function() {
        var pendingForm = null;

        $(document).on('click', '.trigger-confirm-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            pendingForm = btn.closest('form');
            var message = btn.data('message') || 'Are you sure you want to perform this action?';

            // Update message inside the modal
            document.getElementById('confirmationModalMessage').textContent = message;

            // Open the modal
            $('#confirmationModal').modal('show');
        });

        // Handle confirmed action via AJAX
        $('#confirmActionButton').on('click', function() {
            if (!pendingForm) return;

            var actionUrl = pendingForm.attr('action');
            var confirmBtn = $(this);
            confirmBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: pendingForm.serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#confirmationModal').modal('hide');
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        // Reload page after short delay to reflect changes
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    $('#confirmationModal').modal('hide');
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        toastr.error(resp.message || 'Something went wrong. Please try again.');
                    } catch (e) {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    confirmBtn.prop('disabled', false).html('Yes, Proceed');
                }
            });
        });
    });
</script>
@endsection