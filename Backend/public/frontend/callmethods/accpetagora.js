$(document).on('click','.acceptcall',function(e) {
            e.preventDefault();

            @php
                $session = new Session();
                $token = $session->get('astrotoken');
                $astrologerId=astroauthcheck()['astrologerId'];
            @endphp

            var form = $(this).closest('form');
            var formData = form.serialize();

            var astrologerId="{{astroauthcheck()['astrologerId']}}";


            var callId = formData.split("callId=")[1];
            callId = parseInt(callId, 10);

            var partnerId = formData.split("partnerId=")[1];
            partnerId = parseInt(partnerId, 10);
            var call_type = formData.split("call_type=")[1];
            partnerId = parseInt(call_type, 10);
            var call_method = formData.split("call_method=")[1];


            function handleCallAcceptance(callId, formData, token, channelName, call_method,callType, agoraAppIdValue = null, agorCertificateValue = null) {
                $.ajax({
                    url: "{{ route('api.acceptCallRequest', ['token' => $token]) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        toastr.success('Please wait...');

                        // Check if the call method is 'exotel'
                        if (call_method === 'exotel' || call_method === 'hms' || call_method === 'zegocloud') {
                            storeCallToken(callId,call_method,callType);
                        } else {
                            generateRtcToken(channelName, agoraAppIdValue, agorCertificateValue, callId, callType);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseText);
                    }
                });
            }

    // Store token for 'exotel' calls
            function storeCallToken(callId,call_method,callType) {
                $.ajax({
                    url: "{{ route('api.storeToken') }}",
                    type: 'POST',
                    data: {
                        callId: callId,
                        fromWeb:1
                    },
                    success: function(response_call) {
                       // Remove this if not needed
                       if(call_method == 'hms' || call_method == 'zegocloud'){
                          toastr.success('Call accepted successfully');
                        window.location.href = "{{ route('front.astrologercall') }}" + "?callId=" + callId + "&call_type=" + callType+ "&call_method=" + call_method;
                       }else{
                           toastr.success('Call accepted successfully..You will get Phone Call Soon');
                       }
                    },
                    error: function(xhr) {
                        toastr.error(JSON.parse(xhr.responseText).error.paymentMethod[0]);
                    }
                });
            }

    // Generate RTC token and store for non-'exotel' calls
    function generateRtcToken(channelName, agoraAppIdValue, agorCertificateValue, callId, callType) {
        $.ajax({
            url: "{{ route('api.generateRtcToken') }}",
            type: 'POST',
            data: {
                appID: agoraAppIdValue,
                appCertificate: agorCertificateValue,
                channelName: channelName
            },
            success: function(response) {
                var RtcToken = response.rtcToken;

                // Store RTC token
                $.ajax({
                    url: "{{ route('api.storeToken') }}",
                    type: 'POST',
                    data: {
                        channelName: channelName,
                        token: RtcToken,
                        callId: callId
                    },
                    success: function(response_call) {
                        toastr.success('Call accepted successfully');
                        window.location.href = "{{ route('front.astrologercall') }}" + "?callId=" + callId + "&call_type=" + callType;
                    },
                    error: function(xhr) {
                        toastr.error(JSON.parse(xhr.responseText).error.paymentMethod[0]);
                    }
                });
            },
            error: function(xhr) {
                toastr.error(xhr.responseText);
            }
        });
    }

    // Example usage
    handleCallAcceptance(callId, formData, '{{$token}}', '{{$channel_name}}', call_method,call_type, '<?=$agoraAppIdValue->value; ?>', '<?=$agorcertificateValue->value; ?>');


        });

        // Reject Chat

        $(document).on('click','.rejectcall',function(e) {

            // e.preventDefault();

            @php
                $token = $session->get('astrotoken');
            @endphp

            var form = $(this).closest('form');
            var formData = form.serialize();

            $.ajax({
                url: "{{ route('api.rejectCallRequest', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Call Rejected Successfully.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });
