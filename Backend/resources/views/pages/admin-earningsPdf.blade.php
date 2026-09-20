<!DOCTYPE html>
<html lang="en">

<head>
    <title>Customers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    @php
    use Illuminate\Support\Facades\Auth;
    $authuser=Auth::guard('web')->user();
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
            <th>{{ucfirst($professionTitle)}}</th>
            <th>Order Type</th>
            <th>Duration</th>
            <th>Total Amount</th>
            <th>{{ucfirst($professionTitle)}} Earning</th>
            <th>Admin Earning</th>
            <th>Date</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($earnings as $earning)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $earning->userName?$earning->userName:'--' }}</td>
                <td>{{ $earning->astrologerName }}</td>
                <td>{{ $earning->orderType ? $earning->orderType : '--' }}</td>
                @if($authuser->country=='India')

                <td>{{ $earning->totalMin ? $earning->totalMin.' min' : '--' }}</td>
                @php
                $earning->totalPayable=convertusdtoinr( $earning->totalPayable,$earning->inr_usd_conversion_rate?:1);
                $earning->adminearningAmount=convertusdtoinr(  $earning->adminearningAmount,$earning->inr_usd_conversion_rate?:1);
            @endphp
        @endif
                <td>{{ $earning->totalPayable ? number_format($earning->totalPayable,2) : '--' }}</td>
                <td>{{  number_format($earning->totalPayable-$earning->adminearningAmount,2) ?? '--'}}</td>
                <td>{{ $earning->adminearningAmount ? number_format($earning->adminearningAmount,2) : '--' }}</td>
                <td> {{ date('d-m-Y h:i a', strtotime($earning->updated_at)) ? date('d-m-Y h:i a', strtotime($earning->updated_at)) : '--' }}
                </td>

            </tr>
        @endforeach
    </table>

</body>

</html>
