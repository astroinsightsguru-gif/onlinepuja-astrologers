@extends('frontend.layout.master')
<style>
    .puja-brodcast-btn {
    background: gold;
    box-shadow: 0 2px 3px #ffd70080;
    font-size: 15px;
    font-weight: 600;
    border-radius: 40px;
    padding: 2px 20px;
    margin: 0 5px;
    white-space: nowrap;
    position: relative;
    color: black;
}
.blinking-text {
    font-size: 12px;
    top: -7px;
    right: -5px;
    position: absolute;
    animation: blink 1s step-start infinite;
    color: #ff0000;
}

@keyframes blink {
    50% {
        opacity: 0;
    }
}

@media (min-width: 1199px) {
    .container {
        max-width: 1300px !important;
    }
}

.inpage table td {
    white-space: unset !important;
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
                        <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getAstrologerChat') }}"
                            style="color:white;text-decoration:none">My Puja List</a>
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
                    <h1 class="h2 font-weight-bold colorblack">My Puja</h1>
                    <p>Check your puja list here..</p>
                </div>
                <div class="table-responsive" id="walletTransactionTable">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="padding-left:0px !important;">{{$professionTitle}} Name</th>
                                    <th class="text-center" style="width:22% !important;">Puja Name</th>
                                    <th class="text-center">Price</th>
                                     <th class="text-center">Puja Start Date</th>
                                     <th class="text-center">Puja Duration</th>
                                     <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($astrologerPujaList->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="6">
                                            <p>No Puja Found !</p>
                                        </td>
                                    </tr>
                                @else
                                @foreach ($astrologerPujaList as $pujadata)
                                    @if (!empty($pujadata))
                                        <tr>
                                            <td class="text-center" style="padding-left:0px !important;">
                                                <div>
                                                    <p>{{$pujadata->astrologername ?? 'Not Assigned'}}</p>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <span>{{$pujadata->puja_title}}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <span>(-) 
                                                       @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                        {{number_format($pujadata->puja_price,2)}}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ date('d-m-Y h:i a', strtotime($pujadata->puja_start_datetime)) }}
                                            </td>
                                            <td class="text-center">
                                                {{$pujadata->puja_duration }} mins
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route("front.pujacheckout",['slug' => $pujadata->astrologerslug,'id'=>$pujadata->id]) }}" class="btn btn-chat mx-1" title="Edit">
                                                    Purchase
                                                </a>
                                            </td>
                                        </tr>
                                       @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="packageDetailsModal" tabindex="-1" role="dialog" aria-labelledby="packageDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="packageDetailsModalLabel">Package Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Package details will be injected here -->
                <div id="packageDetailsContent" class="p-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('a[data-toggle="modal"]').click(function() {

        var packageData = $(this).data('package');

        var content = '<p><strong>Package Name :</strong> '  + packageData.title + '</p>';
        content += `<p><strong>Price:</strong> 
                        @if($walletType == 'coin')
                            <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                        @else
                            {{ $currency['value'] }}
                        @endif
                        ${packageData.package_price}
                    </p>`;
        content += '<p><strong>For:</strong> '  + packageData.person + ' persons</p>';
        content += '<p><strong>Description:</strong></p><ul>';

        // Loop through the description array and add items to the list
        packageData.description.forEach(function(desc) {
            content += '<li>' + desc + '</li>';
        });

        content += '</ul>';

        // Inject the content into the modal
        $('#packageDetailsContent').html(content);
    });
});
</script>

@endsection
