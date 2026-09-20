@extends('frontend.layout.master')
<style>
    .table-container {
        max-height: 400px;
        /* Adjust the maximum height as needed */
        overflow-y: auto;
    }
</style>
@section('content')
@php
$userId = authcheck()['id'];
@endphp

    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getMyCall') }}"
                                style="color:white;text-decoration:none">My Calls</a>
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
                                        <th>Deduction</th>
                                        <th class="text-center">Review</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($getUserById['recordList'][0]['callRequest']['callHistory'] as $calldata)
                                        @if (!empty($calldata))
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h5>
                                                            {{$calldata['call_type']==10 ? 'Audio' : 'Video'}} Call with
                                                            {{ $calldata['astrologerName'] }} for
                                                            {{ $calldata['totalMin'] }} minutes

                                                        </h5>
                                                    </div>
                                                    <div class="font-12 text-muted">
                                                        {{ date('d-m-Y h:i a', strtotime($calldata['created_at'])) }}

                                                    </div>
                                                    <div class="font-12 text-muted mt-1">
                                                        <span class="text-success">Completed</span>

                                                    </div>

                                                </td>

                                                <td class="text-danger">
                                                    <div class="font-medium">
                                                        (-) 
                                                        @if($walletType == 'coin')
                                                        <img src="{{ asset($coinIcon) }}" alt="Wallet Icon" width="15">
                                                    @else
                                                        {{ $currency['value'] }}
                                                    @endif
                                                {{ number_format($calldata['deduction'],2) }}</div>
                                                </td>
                                                <td class="text-center">

                                                    <a href="javascript::void()" data-toggle="modal" data-target="#reviewmodal" class="btn btn-chat reviewBtn"  data-astrologer-id="{{ $calldata['astrologerId'] }}">Review</a>
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


    <div id="reviewmodal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm h-100 d-flex align-items-center">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">
                        Review
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="Review">
                        <input type="hidden" name="userId" id="userId" value="{{ $userId }}">
                        <input type="hidden" id="astrologerId" name="astrologerId" value="">

                        <div class="text-center">
                            <div class="form-group">
                                <label for="rating">Rating:</label>
                                <div class="star-rating"
                                    data-rating="{{ isset($getUserHistoryReview['recordList'][0]['rating']) ? $getUserHistoryReview['recordList'][0]['rating'] : '' }}">
                                    <input type="radio" id="star5" name="rating" value="5"><label
                                        for="star5"></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label
                                        for="star4"></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label
                                        for="star3"></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label
                                        for="star2"></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label
                                        for="star1"></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="review">Description:</label>
                                <textarea class="form-control" id="review" name="review" rows="3" placeholder="Enter your review" required>{{ isset($getUserHistoryReview['recordList'][0]['review']) ? $getUserHistoryReview['recordList'][0]['review'] : '' }}</textarea>
                            </div>
                            <button class="btn btn-chat" id="reviewbtn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>


$(document).ready(function () {
    $('.reviewBtn').click(function () {
        var astrologerId = $(this).data('astrologer-id'); // Get astrologer ID from the button
        $('#astrologerId').val(astrologerId);
    });
});



    $('#reviewbtn').click(function(e) {
        e.preventDefault();

        var form = document.getElementById('Review');
        if (form.checkValidity() === false) {
            form.reportValidity();
            return;
        }

        @php
            use Symfony\Component\HttpFoundation\Session\Session;
            $session = new Session();

            $token = $session->get('token');
        @endphp

        var formData = $('#Review').serialize();
        // console.log(formData);

        $.ajax({
            url: "{{ route('api.addUserReview', ['token' => $token]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success('Review Added Successfully');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseText);
            }
        });
    });
</script>
@endsection
