<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ucfirst($professionTitle)}} Earning</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    
    <div style=" display: grid;
    grid-template-columns: auto auto ;">
        <div style="display: inline-block">
            <div style="height:100px;width:100px;margin-bottom:10px">
                <img alt="AstroGuru image" class="logo__image w-6" src=""
                    style="height:100%;width:100%;object-fit:cover">
            </div>
        </div>
        <div style="display: inline-block;float:right">
            <h4>{{ $title }}</h4>
            <p>{{ $date }}</p>
        </div>
    </div>
    <div class="row">
        <h6 class="ml-2">{{ucfirst($professionTitle)}} Name: {{ $astrologerName }}</h6>
    </div>
    <table class="table table-bordered" aria-label="astrologer-list">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Order Type</th>
            <th>Order Amount</th>
            <th>Total Min</th>
            <th>Charge</th>
            <th>Order Date</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($astrologerEarning as $earning)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $earning->userName }}</td>
                <td>{{ $earning->orderType }}</td>
                <td>{{ number_format($earning->totalPayable,2) }}</td>
                <td>{{ $earning->totalMin }}</td>
                <td>{{ number_format($earning->charge,2) }}</td>
                <td> {{ date('d-m-Y', strtotime($earning->created_at)) ? date('d-m-Y h:i', strtotime($earning->created_at)) : '--' }}
                </td>
            </tr>
        @endforeach
    </table>

</body>

</html>
