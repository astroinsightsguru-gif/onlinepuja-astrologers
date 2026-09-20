<!DOCTYPE html>
<html lang="en">

<head>
    <title>PartnerWise Earning</title>
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
    <table class="table table-bordered" aria-label="partnerwise-earning">
        <tr>
            <th>ID</th>
            <th>{{ucfirst($professionTitle)}}</th>
            <th>Total Earning</th>
            <th>Chat Earning</th>
            <th>Call Earning</th>
            <th>Report Earning</th>
            <th>Puja Earning</th>
            <th>Course Earning</th>
            <th>Gift Earning</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($partnerWiseEarning as $earning)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $earning->astrologerName ?? 'Unknown'}}</td>
                <td>{{ number_format($earning->totalNewEarning,2) ?? 0}}</td>
                <td>{{ number_format($earning->chatEarning,2) ?? 0}}</td>
                <td>{{ number_format($earning->callEarning,2) ?? 0}}</td>
                <td>{{ number_format($earning->reportEarning,2) ?? 0}}</td>
                <td>{{ number_format($earning->pujaEarning,2) ?? 0}}</td>
                <td>{{ number_format($earning->courseEarning,2) ?? 0}}</td>
                <td>{{ number_format($earning->giftEarning,2) ?? 0}}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>
