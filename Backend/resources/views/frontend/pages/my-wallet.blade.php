@extends('frontend.layout.master')
<style>
    .table-container {
        max-height: 400px;
        /* Adjust the maximum height as needed */
        overflow-y: auto;
    }

    .pagination .page-item.active .page-link {
        background-color: #e5b002 !important;
        border-color: #e5b002 !important;
        color: #fff !important;
    }

    .pagination .page-link {
        color: #e5b002 !important;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d !important;
    }
</style>
@section('content')

<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize; ">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getMyWallet') }}"
                            style="color:white;text-decoration:none">My Wallet</a>
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
                    <h1 class="h2 font-weight-bold colorblack">My Wallet</h1>
                    <p>Check your balance, add money and see your complete transaction history here.</p>
                </div>
                <div class="d-flex pb-3 align-items-center justify-content-between border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="row flex-nowrap align-items-center">
                            <div class="col-auto pr-0">
                                <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/Payment.svg') }}"
                                    alt="Wallet">
                            </div>
                            <div class="col-auto pr-0">
                                <h3 class="font22 orangecolor font-weight-semibold m-0 p-0">
                                    <span id="wallbalance"
                                        class="">
                                        @if($walletType == 'Coin')
                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                        @else
                                        {{ $currency['value'] }}
                                        @endif
                                        {{ number_format($getProfile['data']['totalWalletAmount'], 2) }}</span>
                                </h3>
                                <span class="font-12 colorblack font-weight-semi">Current Balance</span>
                            </div>
                        </div>
                    </div>
                    <div class=" text-right">
                        <button class="btn  btn-chat chatbrown" id="walletPlanLoader" type="button"
                            style="display:none;" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading..
                        </button>

                        <a href="{{ route('front.walletRecharge') }}" class="btn btn-chat" id="btnAddMoney">Add
                            Money</a>
                    </div>
                </div>

                <div class="d-flex flex-nowrap nav nav-tabs">
                    <a data-toggle="tab" id="paymentLog" href="#paymentLogsTable"
                        class="text-decoration-none   weight500 py-2 py-sm-3 px-2 px-sm-3 d-inline-block border-bottom borderbrown">
                        Payment Logs
                    </a>
                    <a data-toggle="tab" id="walletTransaction" href="#walletTransactionTable"
                        class="text-decoration-none text-dark py-2 py-sm-3 px-2 px-sm-3 d-inline-block">
                        Wallet Transaction
                    </a>
                </div>



                <div class="tab-content mt-3">
                    <div class="table-responsive  tab-pane fade show active" id="paymentLogsTable">
                        <div class="row pt-1 pb-3" id="historydate">
                            <div class="col-md-12">
                                <h3 class="font16 font-weight-bold py-4">Payment Logs</h3>

                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <!-- Payment Logs Table Content -->
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Payment Mode</th>
                                        <th>Amount</th>
                                        <th>Cashback Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getUserById['recordList'][0]['paymentLogs']['payment'] as $walletdata)
                                    @if (!empty($walletdata))
                                    <tr>
                                        <td>{{ $walletdata['orderId'] }}</td>
                                        <td>{{ $walletdata['paymentMode'] }}</td>
                                        <td>(+)
                                            @if($walletType == 'Coin')
                                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                            @else
                                            {{ $currency['value'] }}
                                            @endif
                                            {{ number_format($walletdata['amount'], 2) }}
                                        </td>
                                        <td>(+)
                                            @if($walletType == 'Coin')
                                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                            @else
                                            {{ $currency['value'] }}
                                            @endif
                                            {{ number_format($walletdata['cashback_amount'], 2) }}
                                        </td>
                                        @if ($walletdata['paymentStatus'] == 'success')
                                        <td class="text-success">{{ $walletdata['paymentStatus'] }}</td>
                                        @else
                                        <td class="text-danger">{{ $walletdata['paymentStatus'] }}</td>
                                        @endif
                                        <td>{{ date("d-m-Y h:i a" , strtotime($walletdata['created_at'])) }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @php
                        $paymentTotal = $getUserById['recordList'][0]['paymentLogs']['totalCount'] ?? 0;
                        $paymentPerPage = (isset($perPage) && $perPage > 0) ? $perPage : 20;
                        $paymentTotalPages = ceil($paymentTotal / $paymentPerPage);
                        @endphp
                        @if ($paymentTotalPages > 1)
                        <div class="d-flex justify-content-center mt-3">
                            <nav aria-label="Payment Logs Pagination">
                                <ul class="pagination pagination-sm" style="overflow-y: visible !important; height: auto !important;">
                                    <li class="page-item {{ $paymentPage <= 1 ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['payment_page' => $paymentPage - 1]) }}" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    @for ($i = 1; $i <= $paymentTotalPages; $i++)
                                        @if ($i==1 || $i==$paymentTotalPages || ($i>= $paymentPage - 2 && $i <= $paymentPage + 2))
                                            <li class="page-item {{ $i == $paymentPage ? 'active' : '' }}">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['payment_page' => $i]) }}">{{ $i }}</a>
                                            </li>
                                            @elseif ($i == 2 || $i == $paymentTotalPages - 1)
                                            @if (($i == 2 && $paymentPage > 4) || ($i == $paymentTotalPages - 1 && $paymentPage < $paymentTotalPages - 3))
                                                <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                                </li>
                                                @endif
                                                @endif
                                                @endfor
                                                <li class="page-item {{ $paymentPage >= $paymentTotalPages ? 'disabled' : '' }}">
                                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['payment_page' => $paymentPage + 1]) }}" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>

                    <div class="table-responsive  tab-pane fade" id="walletTransactionTable">
                        <div class="row pt-1 pb-3" id="historydate">
                            <div class="col-md-12">
                                <h3 class="font16 font-weight-bold py-4">Transaction History</h3>

                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction Details</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- {{dd($getUserById['recordList'][0]['walletTransaction']['wallet'])}} --}}
                                    @foreach ($getUserById['recordList'][0]['walletTransaction']['wallet'] as $walletdata)
                                    @if (!empty($walletdata))
                                    <tr>
                                        <td>
                                            <div>
                                                <h5>
                                                    @if ($walletdata['transactionType'] == 'Call' || $walletdata['transactionType'] == 'Chat' || $walletdata['transactionType'] == 'VideoCall')
                                                    {{ $walletdata['transactionType'] }} with
                                                    {{ $walletdata['name'] }} for
                                                    {{ $walletdata['totalMin'] }} minutes

                                                    @elseif($walletdata['transactionType'] == 'aiChat' )
                                                    Chat with
                                                    {{ $walletdata['aiastrologername'] }} for
                                                    {{ $walletdata['totalMin'] }} minutes
                                                    @elseif($walletdata['transactionType'] == 'astromallOrder' && $walletdata['isCredit']==0)
                                                    Product Ordered
                                                    @elseif($walletdata['transactionType'] == 'pujaOrder' && $walletdata['isCredit']==0)
                                                    Puja Ordered
                                                    @elseif($walletdata['transactionType'] == 'astromallOrder' && $walletdata['isCredit']==1)
                                                    Product Cancelled
                                                    @elseif($walletdata['transactionType'] == 'Gift')
                                                    Sent {{ $walletdata['transactionType'] }} to
                                                    {{ $walletdata['name'] }}

                                                    @elseif($walletdata['transactionType'] == 'KundliView')
                                                    {{ $walletdata['transactionType'] }}
                                                    @else
                                                    {{ $walletdata['transactionType'] }} Received
                                                    @endif
                                                </h5>
                                            </div>
                                            <div class="font-12 text-muted">
                                                {{ date('d-m-Y h:i a', strtotime($walletdata['created_at'])) }}
                                            </div>
                                        </td>
                                        @if ($walletdata['transactionType'] == 'Cashback' || $walletdata['transactionType'] == 'pujaRefund' || $walletdata['transactionType'] == 'Refund')
                                        <td class="text-success">
                                            <div class="font-medium">(+)
                                                @if($walletType == 'Coin')
                                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                @else
                                                {{ $currency['value'] }}
                                                @endif
                                                {{ number_format($walletdata['amount'], 2) }}
                                            </div>
                                        </td>
                                        @elseif ($walletdata['transactionType'] == 'astromallOrder' && $walletdata['isCredit']==1)
                                        <td class="text-success">
                                            <div class="font-medium">(+)
                                                @if($walletType == 'Coin')
                                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                @else
                                                {{ $currency['value'] }}
                                                @endif
                                                {{ number_format($walletdata['amount'], 2) }}
                                            </div>
                                        </td>
                                        @elseif ($walletdata['transactionType'] == 'Referral' && $walletdata['isCredit']==1)
                                        <td class="text-success">
                                            <div class="font-medium">(+)
                                                @if($walletType == 'Coin')
                                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                @else
                                                {{ $currency['value'] }}
                                                @endif
                                                {{number_format( $walletdata['amount'],2) }}
                                            </div>
                                        </td>
                                        @else
                                        <td class="text-danger">
                                            <div class="font-medium">(-)
                                                @if($walletType == 'Coin')
                                                <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                @else
                                                {{ $currency['value'] }}
                                                @endif
                                                {{number_format( $walletdata['amount'],2) }}
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @php
                        $transTotal = $getUserById['recordList'][0]['walletTransaction']['totalCount'] ?? 0;
                        $transPerPage = (isset($perPage) && $perPage > 0) ? $perPage : 20;
                        $transTotalPages = ceil($transTotal / $transPerPage);
                        @endphp
                        @if ($transTotalPages > 1)
                        <div class="d-flex justify-content-center mt-3">
                            <nav aria-label="Wallet Transactions Pagination">
                                <ul class="pagination pagination-sm" style="overflow-y: visible !important; height: auto !important;">
                                    <li class="page-item {{ $transPage <= 1 ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['trans_page' => $transPage - 1]) }}" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    @for ($i = 1; $i <= $transTotalPages; $i++)
                                        @if ($i==1 || $i==$transTotalPages || ($i>= $transPage - 2 && $i <= $transPage + 2))
                                            <li class="page-item {{ $i == $transPage ? 'active' : '' }}">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['trans_page' => $i]) }}">{{ $i }}</a>
                                            </li>
                                            @elseif ($i == 2 || $i == $transTotalPages - 1)
                                            @if (($i == 2 && $transPage > 4) || ($i == $transTotalPages - 1 && $transPage < $transTotalPages - 3))
                                                <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                                </li>
                                                @endif
                                                @endif
                                                @endfor
                                                <li class="page-item {{ $transPage >= $transTotalPages ? 'disabled' : '' }}">
                                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['trans_page' => $transPage + 1]) }}" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>
                </div>



            </div>


        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    $(document).ready(function() {
        // Auto open Wallet Transaction tab if trans_page exists in query parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('trans_page')) {
            // Remove active status from payment log tab
            $('#paymentLog').removeClass("border-bottom borderbrown ");
            $('#paymentLog').addClass("text-dark");
            $('#paymentLogsTable').removeClass("show active");

            // Set active status on wallet transaction tab
            $('#walletTransaction').addClass("border-bottom borderbrown ");
            $('#walletTransaction').removeClass("text-dark");
            $('#walletTransactionTable').addClass("show active");
        }

        $("#paymentLog").on('click', function() {
            $('#paymentLog').addClass("border-bottom borderbrown ");
            $('#paymentLog').removeClass("text-dark");
            $('#walletTransaction').removeClass("border-bottom borderbrown ");
            $('#walletTransaction').addClass("text-dark");
        });

        $("#walletTransaction").on('click', function() {
            $('#walletTransaction').addClass("border-bottom borderbrown  ");
            $('#walletTransaction').removeClass("text-dark");
            $('#paymentLog').removeClass("border-bottom borderbrown ");
            $('#paymentLog').addClass("text-dark");
        });
    });
</script>




@endsection
