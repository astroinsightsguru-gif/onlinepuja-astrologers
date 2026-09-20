<!DOCTYPE html>
<html lang="en">

<head>
    <title>Customers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    @php
        $logo = DB::table('systemflag')
            ->where('name', 'AdminLogo')
            ->select('value')
            ->first();

            $appName = DB::table('systemflag')->where('name', 'AppName')->select('value')->first();
    @endphp
    <div style=" display: grid;
    grid-template-columns: auto auto ;">
        <div style="display: inline-block">
            <div style="height:100px;width:100px;margin-bottom:10px">
                <img alt="{{$appName->value}} image" class="logo__image w-6" src="{{asset($logo->value)}}"
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
            <th>Name</th>
            <th>Contact No</th>
            <th>Email</th>
            <th>Birth Date</th>
            <th>Birth Time</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($customers as $cus)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $cus->name }}</td>
                <td>{{ $cus->contactNo }}</td>
                <td>{{ $cus->email }}</td>
                <td> {{ date('d-m-Y', strtotime($cus->birthDate)) ? date('d-m-Y', strtotime($cus->birthDate)) : '--' }}
                </td>
                <td>{{ $cus->birthTime }}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>
