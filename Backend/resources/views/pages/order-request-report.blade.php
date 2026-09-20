
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
    <div style=" display: grid;
    grid-template-columns: auto auto ;">
        <div style="display: inline-block">
            <div style="height:100px;width:100px;margin-bottom:10px">
                <img alt="AstroGuru image" class="logo__image w-6" src="{{ url($logo->value) }}"
                    style="height:100%;width:100%;object-fit:cover;border-radius:50%">
            </div>
        </div>
        <div style="display: inline-block;float:right">
            <h4>{{ $title }}</h4>
            <p>{{ $date }}</p>
        </div>
    </div>
    <table class="table table-bordered" aria-label="chatHistory">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Product</th>
            <th>Amount</th>
            <th>OrderDate</th>
            <th>OrderStatus</th>
            <th>OrderAddress</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($orderRequest as $order)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $order->userName }}</td>
                <td>{{ $order->productName }}({{ $order->categoryName }})</td>
                <td>{{ number_format($order->payableAmount,2) }}</td>
                <td>{{ date('d-m-Y', strtotime($order->created_at)) ? date('d-m-Y h:i a', strtotime($order->created_at)) : '--' }}
                </td>
                <td>{{ $order->orderStatus }}</td>
                <td>{{ $order->flatNo }},{{ $order->landmark }},{{ $order->city }},{{ $order->state }},{{ $order->country }}-{{ $order->pincode }}
                </td>
            </tr>
        @endforeach
    </table>

</body>

</html>

