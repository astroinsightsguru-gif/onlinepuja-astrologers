<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wallet History Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        h2 { margin: 5px 0; }
        p { margin: 2px 0; }
        img { max-height: 60px; margin-bottom: 5px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 4px;
            font-size: 11px;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .filters {
            margin-top: 10px;
            font-size: 11px;
        }

        .filters ul {
            margin: 5px 0 0 15px;
            padding: 0;
        }

        .filters li {
            margin-bottom: 2px;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #555;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <img src="https://astrotest.diploy.in/public/storage/images/AdminLogo1732085016.png" alt="Logo">
        <h2>Wallet History Report</h2>
        <p>Generated on: <strong>{{ $generated_at }}</strong></p>
    </div>

    <div class="filters">
        <strong>Filters Applied:</strong>
        @if(request('searchString') || request('from_date') || request('to_date') || request('paymentMethod'))
            <ul>
                @if(request('searchString'))
                    <li>Search: "{{ request('searchString') }}"</li>
                @endif
                @if(request('paymentMethod'))
                    <li>Payment Method: {{ ucfirst(request('paymentMethod')) }}</li>
                @endif
                @if(request('from_date'))
                    <li>From Date: {{ \Carbon\Carbon::parse(request('from_date'))->format('d-m-Y') }}</li>
                @endif
                @if(request('to_date'))
                    <li>To Date: {{ \Carbon\Carbon::parse(request('to_date'))->format('d-m-Y') }}</li>
                @endif
            </ul>
        @else
            <span>None (All records included)</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">ID</th>
                <th style="width:100px;">User Name</th>
                <th style="width:80px;">Contact</th>
                <th style="width:50px;">Mode</th>
                <th style="width:70px;">Payment For</th>
                <th style="width:80px;">Reference</th>
                <th style="width:70px;" class="text-right">Amount ({{ $currency->value ?? '₹' }})</th>
                <th style="width:50px;" class="text-right">GST ({{ $gst }}%)</th>
                <th style="width:80px;" class="text-right">Total Amount</th>
                <th style="width:50px;">Status</th>
                <th style="width:80px;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($wallet as $row)
                @php
                    $normalAmount = $row->amount;
                    $gstAmount = ($normalAmount * $gst) / 100;
                    $totalAmount = $normalAmount + $gstAmount;
                @endphp
                <tr>
                    <td class="text-center">{{ $row->id }}</td>
                    <td>{{ $row->userName }}</td>
                    <td>{{ $row->userContact }}</td>
                    <td>{{ ucfirst($row->paymentMode) }}</td>
                    <td>{{ $row->payment_for }}</td>
                    <td>{{ $row->paymentReference }}</td>
                    <td class="text-right">{{ number_format($normalAmount, 2) }}</td>
                    <td class="text-right">{{ number_format($gstAmount, 2) }}</td>
                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                    <td class="text-center">{{ ucfirst($row->paymentStatus) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Report generated automatically by {{ config('app.name') }} on {{ now()->format('d-m-Y h:i A') }}</p>
    </div>
</body>
</html>
