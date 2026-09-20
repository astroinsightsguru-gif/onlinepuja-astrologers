@extends('frontend.astrologers.layout.master')

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
                    <th>User</th>
                    <th>Call Type</th>
                    <th>Schedule Date</th>
                    <th>Schedule Time</th>
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

                // Determine action visibility (Start Call vs Cancel vs Delete)
                $showStartCall = false;
                $showCancel = false;
                $showDelete = false;

                if ($appointment->IsSchedule == 1 && $appointment->schedule_date && $appointment->schedule_time) {
                $scheduleDateTime = \Carbon\Carbon::parse($appointment->schedule_date . ' ' . $appointment->schedule_time);
                $duration = ($appointment->call_duration / 60) ?? 15;
                $endDateTime = $scheduleDateTime->copy()->addMinutes($duration);
                $isExpired = \Carbon\Carbon::now()->greaterThan($endDateTime);

                if ($isExpired) {
                $showDelete = true;
                } else {
                if (in_array($displayStatus, ['Scheduled', 'Pending'])) {
                $showStartCall = true;
                $showCancel = true;
                }
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
                        <img src="{{ $appointment->userProfile ?? '/build/assets/images/person.png' }}"
                            onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                            class="rounded-circle me-2" width="40" height="40" alt="User">
                        {{ $appointment->userName ?? 'User' }}
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
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $displayStatus }}</span>
                    </td>
                    <td>
                        @if($showStartCall || $showCancel)
                        <div class="d-flex justify-content-center gap-1">
                            @if($showStartCall)
                            <button type="button" class="btn btn-sm btn-success start-call-btn"
                                data-call-id="{{ $appointment->callId }}"
                                data-partner-id="{{ $appointment->userId }}"
                                data-astrologer-id="{{ $appointment->astrologerId }}"
                                data-call-type="{{ $appointment->call_type }}"
                                data-call-method="{{ $appointment->call_method }}"
                                data-channel-name="{{ $appointment->channelName }}"
                                data-schedule-date="{{ $appointment->schedule_date }}"
                                data-schedule-time="{{ $appointment->schedule_time }}">
                                <i class="fa-solid fa-phone"></i> Start Call
                            </button>
                            @endif
                            @if($showCancel)
                            <form action="{{ route('astroappointment.delete', $appointment->callId) }}" method="POST" class="delete-appointment-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger trigger-confirm-btn" data-message="Are you sure you want to cancel this appointment?">Cancel</button>
                            </form>
                            @endif
                        </div>
                        @elseif($showDelete)
                        <div class="d-flex justify-content-center">
                            <form action="{{ route('astroappointment.delete', $appointment->callId) }}" method="POST" class="delete-appointment-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-outline-danger trigger-confirm-btn" data-message="Are you sure you want to delete this appointment record permanently?">Delete</button>
                            </form>
                        </div>
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

{{-- 🔹 Start Call Script --}}
<script>
    $(document).on('click', '.start-call-btn', function(e) {
        e.preventDefault();

        var btn = $(this);
        var callId = btn.data('call-id');
        var partnerId = btn.data('partner-id');
        var astrologerId = btn.data('astrologer-id');
        var callType = btn.data('call-type');
        var callMethod = btn.data('call-method');
        var channelName = btn.data('channel-name');
        var scheduleDate = btn.data('schedule-date');
        var scheduleTime = btn.data('schedule-time');

        // Check if the scheduled time has arrived (allow 5 minutes early)
        if (scheduleDate && scheduleTime) {
            var scheduledDateTime = new Date(scheduleDate + ' ' + scheduleTime);
            var now = new Date();
            var diffMinutes = (scheduledDateTime - now) / (1000 * 60);

            if (diffMinutes > 5) {
                toastr.warning('This call is scheduled for ' + scheduleDate + ' at ' + scheduleTime + '. You can start the call 5 minutes before the scheduled time.');
                return;
            }
        }

        // Disable button to prevent double click
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Starting...');

        var formData = 'callId=' + callId + '&partnerId=' + partnerId + '&userId=' + astrologerId + '&call_type=' + callType + '&call_method=' + callMethod;

        // Accept the call request first
        $.ajax({
            url: "{{ route('api.acceptCallRequest', ['token' => $token]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success('Please wait...');

                // Use storeToken for all call methods (it handles Agora/HMS/Zegocloud/Exotel internally)
                storeCallToken(callId, callMethod, callType);
            },
            error: function(xhr) {
                toastr.error('Failed to accept call. Please try again.');
                btn.prop('disabled', false).html('<i class="fa-solid fa-phone"></i> Start Call');
            }
        });
    });

    // Store token and start call (handles all call methods via storeToken API)
    function storeCallToken(callId, callMethod, callType) {
        $.ajax({
            url: "{{ route('api.storeToken') }}",
            type: 'POST',
            data: {
                callId: callId,
                fromWeb: 1
            },
            success: function(response_call) {
                if (callMethod == 'exotel') {
                    toastr.success('Call accepted successfully. You will get a phone call soon.');
                } else {
                    toastr.success('Call started successfully');
                    window.location.href = "{{ route('front.astrologercall') }}" + "?callId=" + callId + "&call_type=" + callType + "&call_method=" + callMethod;
                }
            },
            error: function(xhr) {
                try {
                    toastr.error(JSON.parse(xhr.responseText).error.paymentMethod[0]);
                } catch (e) {
                    toastr.error('Failed to start call. Please try again.');
                }
            }
        });
    }
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
        var confirmModalEl = document.getElementById('confirmationModal');
        var confirmModal = new bootstrap.Modal(confirmModalEl);
        var pendingForm = null;

        $(document).on('click', '.trigger-confirm-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            pendingForm = btn.closest('form');
            var message = btn.data('message') || 'Are you sure you want to perform this action?';

            // Update message inside the modal
            document.getElementById('confirmationModalMessage').textContent = message;

            // Open the modal using Bootstrap 5 API
            confirmModal.show();
        });

        // Handle confirmed action via AJAX
        document.getElementById('confirmActionButton').addEventListener('click', function() {
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
                    confirmModal.hide();
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
                    confirmModal.hide();
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