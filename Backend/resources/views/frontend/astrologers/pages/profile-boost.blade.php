@extends('frontend.astrologers.layout.master')

@section('content')
<div class="container my-5">
    <h2 class="text-center mb-4">Profile Boost History</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Chat Commission</th>
                    <th>Call Commission</th>
                    <th>Video Call Commission</th>
                    <th>Bootsted Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profileBoostHistory['recordList'] as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['chat_commission']}}%</td>
                    <td>{{ $data['call_commission']}}%</td>
                    <td>{{ $data['video_call_commission']}}%</td>
                    <td>{{ date('d M Y h:i A', strtotime($data['boosted_datetime']))}}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">No data found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
