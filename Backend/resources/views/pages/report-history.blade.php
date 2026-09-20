<!DOCTYPE html>
<html lang="en">

<head>
    <title>ReportHistory</title>
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
    <div style=" display: grid;
    grid-template-columns: auto auto ;">
        <div style="display: inline-block">
            <div style="height:100px;width:100px;margin-bottom:10px;">
                <img alt="AstroGuru image" class="logo__image w-6" src="{{ url($logo->value) }}"
                    style="height:100%;width:100%;object-fit:cover;border-radius:50%">
            </div>
        </div>
        <div style="display: inline-block;float:right">
            <h4>{{ $title }}</h4>
            <p>{{ $date }}</p>
        </div>
    </div>

    <table class="table table-bordered" aria-label="myPdf">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>{{ucfirst($professionTitle)}}</th>
            <th>Report Type</th>
            <th>Report Date</th>
            <th>Report Charge</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($reportHistory as $history)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $history->userName }}-{{ $history->userContactNo }}</td>
                <td>{{ $history->astrologerName }}-{{ $history->astrologerContactNo }}</td>
                <td>{{ $history->title }}</td>
                <td> {{ date('d-m-Y', strtotime($history->created_at)) ? date('d-m-Y', strtotime($history->created_at)) : '--' }}
                </td>
                <td>{{ number_format($history->reportRate,2) }}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>
