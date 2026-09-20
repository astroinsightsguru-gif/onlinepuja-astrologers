<!DOCTYPE html>
<html lang="en">

<head>
    <title>Chat History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    @php
    $logo = DB::table('systemflag')
    ->where('name', 'AdminLogo')
    ->select('value')
    ->first();
    @endphp
    <div style="display: grid; grid-template-columns: auto auto ;">
        <div style="display: inline-block">
            <div style="height:100px;width:100px;margin-bottom:20px">
                <img alt="AstroGuru image" class="logo__image w-6" src="{{ asset($logo->value) }}"
                style="height:90%;width:90%;object-fit:cover;border-radius:50%">
            </div>
        </div>
        <div style="display: inline-block;float:right;">
            <h4>{{ $title }}</h4>
            <p>{{ $date }}</p>
        </div>
    </div>
    <table class="table table-bordered" aria-label="chatHistory">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Astrologer</th>
            <th>Chat Rate</th>
            <th>Chat Time</th>
            <th>Total Min</th>
            <th>Deduction</th>
        </tr>
        @php
        $no = 0;
        @endphp
        @foreach ($chatHistory as $history)
        <tr>
            <td>{{ ++$no }}</td>
            <td>{{ $history->userName }} - {{ $history->contactNo }}</td>
            <td>{{ $history->astrologerName }}</td>
            <td>{{ number_format($history->chat_rate,2) }}</td>
            <td>{{ date('d-m-Y', strtotime($history->updated_at)) ? date('d-m-Y h:i', strtotime($history->updated_at)) : '--' }}</td>
            @php
            $durationInSeconds = $history->chat_duration;
            $minutes = floor($durationInSeconds / 60); // Get the whole minutes
            $seconds = $durationInSeconds % 60; // Get the remaining seconds
            @endphp


            <td>
                {{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}
            </td>
            <td>{{ number_format($history->deduction,2) }}</td>

        </tr>
        @endforeach
    </table>

</body>

</html>
