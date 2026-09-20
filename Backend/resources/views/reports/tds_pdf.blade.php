<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TDS Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 13px; }
        th { background: #f5f5f5; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-bottom: 20px; }
        .logo { width: 120px; }
    </style>
</head>
<body>

<div class="header">
    <img src="https://astrotest.diploy.in/public/storage/images/AdminLogo1732085016.png" alt="Site Logo" class="logo"><br>
    <h2>TDS Report</h2>
    @if($searchString)
        <p>For Astrologer: <strong>{{ $searchString }}</strong></p>
    @endif
    @if($from_date && $to_date)
        <p>Date Range: <strong>{{ $from_date }}</strong> to <strong>{{ $to_date }}</strong></p>
    @endif
</div>

<div class="summary">
    <strong>Total Astrologers:</strong> {{ $totalAstrologers }} <br>
    <strong>Total Withdraw:</strong> ₹{{ number_format($totalWithdraw, 2) }} <br>
    <strong>Total TDS:</strong> ₹{{ number_format($totalTDS, 2) }} <br>
    <strong>Total Payable:</strong> ₹{{ number_format($totalPayable, 2) }} <br>
    <strong>Total Wallet Amount:</strong> ₹{{ number_format($totalWallet, 2) }}
</div>

<table>
    <thead>
        <tr>
            <th>Astrologer</th>
            <th>Contact</th>
            <th>Total Withdraw</th>
            <th>TDS Deducted</th>
            <th>Payable Amount</th>
            <th>Wallet Amount</th>
            <th>Total Earned</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportGrouped as $astrologer => $records)
            @php
                $contact = $records->first()->Contact;
                $totalWithdrawAstro = $records->sum('withdrawAmount');
                $totalTDSAstro = $records->sum('tds_pay_amount');
                $totalPayableAstro = $records->sum('pay_amount');
                $walletAmount = $walletData[$astrologer]->wallet_amount ?? 0;
                $totalEarned = $totalWithdrawAstro + $walletAmount;
            @endphp
            <tr>
                <td>{{ $astrologer }}</td>
                <td>{{ $contact }}</td>
                <td>₹{{ number_format($totalWithdrawAstro, 2) }}</td>
                <td>₹{{ number_format($totalTDSAstro, 2) }}</td>
                <td>₹{{ number_format($totalPayableAstro, 2) }}</td>
                <td>₹{{ number_format($walletAmount, 2) }}</td>
                <td>₹{{ number_format($totalEarned, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
