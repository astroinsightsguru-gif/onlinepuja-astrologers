@extends('frontend.astrologers.layout.master')
<style>
    .table-container {
        max-height: 400px;
        /* Adjust the maximum height as needed */
        overflow-y: auto;
    }
</style>
@section('content')
    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.astrologerindex') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getAstrologerReport') }}"
                                style="color:white;text-decoration:none">My Reports</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="inpage">
                    <div class="text-left pb-md-4 pb-2">
                        <h1 class="h2 font-weight-bold colorblack">My Reports</h1>
                        <p>Check your complete report history here.</p>
                    </div>
                    <div class="table-responsive" id="walletTransactionTable">
                        <div class="row pt-1 pb-3" id="historydate">
                            <div class="col-md-12">
                                <h3 class="font16 font-weight-bold py-4">Report History</h3>

                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction Details</th>
                                        <th>Report Rate</th>
                                        <!-- <th class="text-center">View Report</th> -->
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($getAstrologerReport['recordList'][0]['report'] as $reportdata)
                                        @if (!empty($reportdata))
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h5>
                                                            Received Report
                                                            {{ $reportdata['name'] }}

                                                        </h5>
                                                    </div>
                                                    <div class="font-12 text-muted">
                                                        {{ date('d-m-Y h:i a', strtotime($reportdata['created_at'])) }}

                                                    </div>
                                                    <div class="font-12 text-muted mt-1">
                                                        <span class="text-success">Completed</span>

                                                    </div>

                                                </td>

                                                <td class="text-success">
                                                    <div class="font-medium">
                                                        (+) {{ $currency['value'] }}{{ number_format($reportdata['reportRate'],2) }}</div>
                                                </td>
<!--
                                                <td class="text-center">
                                                    @if($reportdata['reportFile'])
                                                    <a href="/{{$reportdata['reportFile']}}" class="btn btn-chat">View</a>
                                                    @endif
                                                </td> -->
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
