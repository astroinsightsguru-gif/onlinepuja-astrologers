@extends('frontend.layout.master')
<style>
    .btn-cancel{
        background: gold !important;
        box-shadow: 0 2px 3px #ffd70080 !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        border-radius: 40px !important;

        white-space: nowrap !important;
    }

    .table td, .table th {
        vertical-align: baseline !important;

    }
    .btn.view-pdf {

    border-radius: 30px;
    font-size: 15px;
    padding: 8px 30px;
    box-shadow: 0 3px 6px #ee4e5e29;
    font-weight: 500;
}
    .product-image {
        width: 50px !important;
        height: auto;
        max-width: 100%;
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
                                style="color:white;text-decoration:none">My Orders</a>
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
                        <h1 class="h2 font-weight-bold colorblack">My Orders</h1>
                        <p>Check your orders history here.</p>
                    </div>


                    <div class="row pt-1 pb-3" id="historydate">
                        <div class="col-md-12">
                            <h3 class="font16 font-weight-bold py-4">Orders History</h3>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-red text-white">
                                <tr>
                                    <th class="font-weight-semi-bold text-center">
                                      Invoice
                                    </th>
                                    <th class="font-weight-semi-bold text-center">
                                        Product Name
                                    </th>

                                    <th class="font-weight-semi-bold text-center">
                                        Image

                                    </th>

                                    <th class="font-weight-semi-bold text-center">
                                        Price

                                    </th>
                                    {{-- <th class="font-weight-semi-bold text-center">
                                        Tax

                                    </th> --}}
                                    <th class="font-weight-semi-bold text-center">
                                        Total
                                    </th>

                                    <th class="font-weight-semi-bold text-center">
                                        Status
                                    </th>
                                    <th class="font-weight-semi-bold text-center">
                                        Date
                                    </th>
                                    <th class="font-weight-semi-bold text-center">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach($getUserById['recordList'][0]['orders']['order'] as $orderdata)
                                @if(!empty($orderdata))
                                    <tr>
                                        <td><a class="colorblack btn view-pdf" href="{{$orderdata['invoice_link']}}"><i class="fa-solid fa-file-pdf color-red"></i></a></td>
                                        <td>{{$orderdata['productName']}}</td>
                                        <td>
                                            <img class="rounded-m product-image" src="{{ Str::startsWith($orderdata['productImage'], ['http://','https://']) ? $orderdata['productImage'] : '/' . $orderdata['productImage'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $orderdata['productImage'] }}')" /></td>
                                        <td>(-)
                                                @if($walletType == 'Coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                        {{number_format($orderdata['payableAmount'],2)}}</td>
                                        {{-- <td>{{$orderdata['gstPercent']}}%</td> --}}
                                        <td>(-) @if($walletType == 'Coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif{{number_format($orderdata['totalPayable'],2)}}</td>
                                        @if($orderdata['orderStatus']=='Pending')
                                            <td class="text-warning">{{$orderdata['orderStatus']}}</td>
                                        @elseif($orderdata['orderStatus']=='Cancelled')
                                            <td class="text-danger">{{$orderdata['orderStatus']}}</td>
                                        @else
                                            <td class="text-success">{{$orderdata['orderStatus']}}</td>
                                        @endif
                                       <td>{{ \Carbon\Carbon::parse($orderdata['created_at'])->format('d-m-Y h:i a') }}</td>

                                        <td>
                                            @if($orderdata['orderStatus']!='Cancelled' && $orderdata['orderStatus']!='Delivered' && $orderdata['orderStatus']!='Dispatched')
                                                <form class="cancel-form">
                                                    <input type="hidden" value="{{$orderdata['id']}}" name="id">
                                                    <a class="btn btn-cancel cancel-btn">Cancel</a>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" value="0" id="flag">
            </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>
$(document).ready(function() {
    $('.cancel-btn').click(function(e) {
        e.preventDefault();



        @php
            use Symfony\Component\HttpFoundation\Session\Session;
            $session = new Session();
            $token = $session->get('token');
        @endphp

        Swal.fire({
            title: 'Are you sure you want to cancel the order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = $(this).closest('.cancel-form').serialize();
                // console.log(formData);

                $.ajax({
                    url: '{{ route("api.cancelOrder",['token' => $token]) }}',
                    type: 'POST',
                    data: formData,

                    success: function(response) {
                        toastr.success('Order Cancelled Successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = JSON.parse(xhr.responseText).error.paymentMethod[0];
                        toastr.error(errorMessage);
                    }
                });
            }
        });
    });
});

</script>


@endsection
