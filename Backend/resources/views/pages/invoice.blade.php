<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ ucfirst($appname) }} - Invoice</title>
    <style type="text/css">
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 80px;
            height: 80px;
        }

        .header h1 {
            margin: 10px 0 5px;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #777;
        }

        .text-bold {
            font-weight: bold;
        }

        .w-100{
        width: 100%;
    }

    .gray-color{
        color:#5D5D5D;
    }



        .table-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .table-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-section th, .table-section td {
            border: 1px solid #e0e0e0;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .table-section th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .table-section img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .total-section p {
            margin: 5px 0;
            font-size: 14px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .footer p {
            margin: 5px 0;
        }

        .signatory {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
        }

        .signatory p {
            margin: 5px 0;
        }
        .box-text p{
        line-height:10px;
    }

    
    </style>
</head>

<body>
    @php
    $siteemail = DB::table('systemflag')->where('name', 'siteemail')->first();
    $siteaddress = DB::table('systemflag')->where('name', 'siteaddress')->first();
    $sitenumber = DB::table('systemflag')->where('name', 'sitenumber')->first();
    $signature = DB::table('systemflag')->where('name', 'InvoiceSignature')->first();
    
    @endphp
    <div class="container">
        <!-- Header Section -->
        <div class="header" style="margin-bottom: 30px">
            <img src="{{ url($logo->value) }}" alt="Logo">
            <h1>{{ ucfirst($appname) }}</h1>
            <p>{{ $siteaddress->value }}</p>
            <p>Email: {{ $siteemail->value }} | Phone: {{ $sitenumber->value }}</p>
        </div>

        <div class="add-detail" style="margin-bottom: 50px; position: relative; width: 100%;">
            <p class="text-bold" style="position: absolute; left: 0; margin: 0;">Invoice Id - <span class="gray-color">#{{ $order->id }}</span></p>
            <p class="text-bold" style="position: absolute; right: 0; margin: 0;">Order Date - <span class="gray-color">{{ date('d-m-Y h:i a', strtotime($order->created_at)) }}</span></p>
        </div>
        
        

        <div class="table-section bill-tbl w-100 mt-10">
            <table class="table w-100 mt-10">
                <tr>
                    <th class="w-50">Details</th>
                    <th class="w-50">Address</th>
                </tr>
                <tr>
                    <td>
                        <div class="box-text ">
                            <p>Name : {{$order->userName}}</p>
                            <p>Email: {{ $order->userEmail }}</p>
                            <p>Contact: {{ $order->userContactNo }}</p>
                           
                        </div>
                    </td>
                    <td>
                        <div class="box-text">
                            <p> {{ $order->flatNo }},{{ $order->landmark }}</p>
                            <p>{{ $order->city }},{{ $order->state }}</p>
                            <p>{{ $order->country }}-{{ $order->pincode }}</p>                    
                           
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Order Details Table -->
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Product Name</th>
                        <th>Product Image</th>
                        <th>Order Status</th>
                        <th>Subtotal</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $order->categoryName }}</td>
                        <td>{{ $order->productName }}</td>
                        <td class="text-center">
                            <img src="{{ url($order->productImage) }}" alt="Product Image">
                        </td>
                        <td>{{ $order->orderStatus }}</td>
                        <td>{{ $currencySymbol->value }}{{ $order->payableAmount }}</td>
                        <td>{{ $currencySymbol->value }}{{ $order->totalPayable }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <p>Sub Total: {{ $currencySymbol->value }}{{ $order->payableAmount }}</p>
            <p>Total Payable: {{ $currencySymbol->value }}{{ $order->totalPayable }}</p>
            <p style="font-size: 12px;">(incl. of all taxes)</p>
        </div>

        <!-- Signatory Section -->
        <div class="signatory">
            <p>Authorised Signatory</p>
            <img style="height:55px;width:55px" src="{{ url($signature->value) }}" alt="Signature">
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>This is a system-generated invoice, so a signature is not required.</p>
            <p>Thank you for your order!</p>
        </div>
    </div>
</body>

</html>