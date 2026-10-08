@extends('frontend.layout.master')
@section('content')
<style>
   .error-message{
    font-size: 0.8rem;
   } 
</style>
<div class="container">
    <div class="row">
        <div class="col-12 contactbox align-self-center text-center" style="margin-top :0!important">
            <h2 class="font26 weight500 colorblack mb-1 mt-3 cat-heading pb-4">Contact Us</h2>
            <div class="row pt-3 pt-md-5 justify-content-between inpage mt-4">
                <div class="col-12 col-md-6 text-left">
                    <h2 class="font22 weight500">{!!$appName->value!!}</h2>
                    <span class="onlinepuja-logo-subtext">Consult Online {{ucfirst($professionTitle)}} Anytime</span>
                    <p class="font14 mb-4">
                        {!!$siteaddress!!}
                    </p>
                    <p class="font14 mb-1"><i class="fa fa-headphones mr-1 font16"></i> Customer Support:
                        {!! $sitenumber!!}</p>
                    <p class="font14"><i class="fa fa-envelope mr-1 font16"></i> <a
                            href="mailto:support@anytimeastro.com" class="colorblack font14">{!! $siteemail!!}</a></p>
                </div>

                <div class="col-12 col-md-6 pt-5 pt-md-0  text-left">
                    <h2 class="font22 weight500">Have any questions?</h2>
                    <p class="font14 mb-4">
                        We are happy to help. Tell us your issue and we will get back to you at the earliest.
                    </p>

                    <div class="alert" role="alert" style="display: none;"></div>

                    <form id="contactform" method="post" action="{{ route('front.store.contact') }}" novalidate>
                        @csrf
                        <div class="form-group mb-0 d-flex">
                            <div class="form-group contacform row w-100">
                                <div class="d-block d-md-flex w-100">
                                    <div class="col-md-6 mb-3">
                                        <input autocomplete="off" class="form-control" id="contact_name"
                                            name="contact_name" placeholder="Name" type="text">
                                        <span id="contact_name_error" class="error-message text-danger"></span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input autocomplete="off" class="form-control" id="contact_email"
                                            name="contact_email" placeholder="Email Address" type="text">
                                        <span id="contact_email_error" class="error-message text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <textarea autocomplete="off" class="form-control inputtextarea" id="contact_message"
                                        maxlength="500" name="contact_message" placeholder="Write your message here"
                                        rows="2" style="height:200px;width:100%"></textarea>
                                    <span id="contact_message_error" class="error-message text-danger"></span>
                                </div>
                                <div class="col-md-12">
                                    <input type="submit" class="btn btn-primary bigorange w-100" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
  $(document).ready(function() {
    $('#contactform').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $('.error-message').text('');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#contactform')[0].reset();
                $('.alert').removeClass('alert-danger').addClass('alert-success').text(response.success).fadeIn();
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 3000); // Hide alert after 3 seconds
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, errorMessage) {
                        $('#' + field + '_error').text(errorMessage[0]);
                    });
                    $('.alert').removeClass('alert-success').addClass('alert-danger').text('There are errors in your form. Please correct them.').fadeIn();
                } else {
                    $('.alert').removeClass('alert-success').addClass('alert-danger').text('Server Error: ' + error).fadeIn();
                }
            }
        });
    });
});



</script>






@endsection