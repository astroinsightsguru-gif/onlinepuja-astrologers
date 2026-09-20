<!DOCTYPE html>
<html lang="en">

<head>
    <title>CallHistory</title>
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
            <div style="height:100px;width:100px;margin-bottom:10px">
                <img alt="AstroGuru image" class="logo__image w-6"
                    src="{{ url($logo->value) }}"
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
            <th>Call Rate</th>
            <th>Call Time</th>
            <th>Total Min</th>
            <th>Deduction</th>
            <th>Call Status</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($callHistory as $history)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $history->userName }}</td>
                <td>{{ $history->astrologerName }}</td>
                <td>{{ number_format($history->callRate,2) }}</td>
                <td> {{ date('d-m-Y', strtotime($history->updated_at)) ? date('d-m-Y h:i', strtotime($history->updated_at)) : '--' }}
                </td>
                <td>{{ $history->totalMin }}</td>
                <td>{{ $history->deduction }}</td>
                <td>{{ number_format($history->callStatus,2) }}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>
