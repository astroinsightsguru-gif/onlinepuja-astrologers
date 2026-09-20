@extends('frontend.astrologers.layout.master')
<style>
    .table-container {
        max-height: 400px;
        /* Adjust the maximum height as needed */
        overflow-y: auto;
    }

        /* Add selected state styling */
.loadGiftItems a.selected {
    box-shadow: 0px 3px 6px #EE4E5E33 !important;
    background: #FFF5F6 !important;
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
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getAstrologerCall') }}"
                                style="color:white;text-decoration:none">My Calls</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rounded modalcenter" id="puja_popup" tabindex="-1" aria-labelledby="myModel_puja_popup"
style="display: none;" aria-hidden="true">
<div class="modal-dialog">
    <form id="pujaForm">
        <div class="modal-content">
            <button type="button" class="close text-right mr-3 mt-1" data-dismiss="modal">×</button>
            <!-- Modal body -->
            <div class="modal-body px-0">
                <div class="position-relative  text-center w-100">
                    <h3 class="d-block font-weight-bold font-20" id="leave-expert-name">Send Puja</h3>

                </div>

                @if(count($pujas)>0)

                <input type="hidden" name="astrologerId" value="{{ astroauthcheck()['astrologerId'] }}">
                <input type="hidden" name="userId" value="">
                <input type="hidden" name="puja_id" value="">

                <div class="bg-white text-center p-2">
                    <div id="loadGiftItems" class="loadGiftItems d-flex flex-wrap" style="height: 400px;">
                        @foreach ($pujas as $puja)
                            <div class="loadGiftItem d-flex align-items-center justify-content-center"
                                id="user-puja-{{ $puja->id }}" data-puja-id="{{ $puja->id }}"  style="height: 150px;width: 50%;">
                                <a href="javascript:void(0)"
                                    style="width:100%;height:100%;max-width:100%;">
                                    @php
                                    $firstImage = $puja->puja_images[0] ?? 'public/frontend/homeimage/360.png';
                                     @endphp
                                    <img src="/{{ $firstImage }}" class="mt-1"
                                        style="width: 70px;height:70px;border-radius:15%">
                                    <p style="margin-bottom: 0;font-size:14px" class="gift-name text-wrap">
                                        {{ \Illuminate\Support\Str::limit($puja->puja_title, 58, '...') }}</p>
                                    <p>{{$currency->value}} {{$puja->puja_price}}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-center p-5">No Puja Found</p>
                @endif
            </div>

            @if(count($pujas)>0)
            <div class="d-flex align-items-center justify-content-center pb-4">
                    <a class="btn btn-Waitlist send-puja  active">Send</a>
            </div>
            @endif

        </div>
    </form>
</div>
</div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="inpage">
                    <div class="text-left pb-md-4 pb-2">
                        <h1 class="h2 font-weight-bold colorblack">My Calls</h1>
                        <p>Check your complete call history here.</p>
                    </div>
                    <div class="table-responsive" id="walletTransactionTable">
                        <div class="row pt-1 pb-3" id="historydate">
                            <div class="col-md-12">
                                <h3 class="font16 font-weight-bold py-4">Call History</h3>

                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction Details</th>
                                        <th>Call Rate</th>
                                       <th >Send Puja</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($getAstrologerCall['recordList'][0]['callHistory'] as $calldata)
                                        @if (!empty($calldata))
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h5>
                                                            {{$calldata['call_type']==10 ? 'Audio' : 'Video'}} Call with
                                                            {{ $calldata['name'] }} for
                                                            {{ $calldata['totalMin'] }} minutes

                                                        </h5>
                                                    </div>
                                                    <div class="font-12 text-muted">
                                                        {{ date('j-F-Y H:i a', strtotime($calldata['created_at'])) }}

                                                    </div>
                                                    <div class="font-12 text-muted mt-1">
                                                        <span class="text-success">Completed</span>

                                                    </div>

                                                </td>

                                                <td class="text-success">
                                                    <div class="font-medium">
                                                        (+) {{ $currency['value'] }}{{ number_format($calldata['deduction'],2) }}</div>
                                                </td>
                                                <td>
                                                    <a data-target="#puja_popup" data-user-id="{{ $calldata['userId'] }}" data-toggle="modal" class="btn btn-chat pujapopup">Puja</a>
                                                </td>
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
<script>
    $(document).ready(function() {
        $('.loadGiftItems a').on('click', function(e) {
            e.preventDefault(); // Prevent default anchor behavior

            $('.loadGiftItems a').removeClass('selected');

            $(this).addClass('selected');

            // Get the puja-id from the parent div
            var selectedPujaId = $(this).closest('div').data('puja-id');
            $('input[name="puja_id"]').val(selectedPujaId);
        });

        $('.pujapopup').on('click',function(e){
            e.preventDefault();
            $('.loadGiftItems a').removeClass('selected');
            var selectedUserId = $(this).data('user-id');
            $('input[name="userId"]').val(selectedUserId);
        })
    });

</script>

<script>
 $('.send-puja').click(function(e) {
            e.preventDefault();
            var pujaId = $('input[name="puja_id"]').val();
            if (!pujaId) {
                toastr.error('Please select a puja first');
                return false;
            }
            var formData = $('#pujaForm').serialize();
            $.ajax({
                url: "{{ route('api.sendPujatoUser') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Puja Sent Successfully');
                    $('#puja_popup').modal('hide');
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    toastr.error(response.message);
                }
            });
        });
</script>

@endsection
