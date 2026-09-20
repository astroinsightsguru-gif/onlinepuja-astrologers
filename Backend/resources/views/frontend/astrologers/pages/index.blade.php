@extends('frontend.astrologers.layout.master')
<style>
    .card {
        border: 2px solid #ffd70085 !important;
        overflow: hidden;
        /* width: max-content; */
    }

    .card-body {

        background: #ffd7001f;
    }

    .card-title {
        font-size: 20px;
        font-weight: 600;
        color: #212529;
    }

    .status {
        gap: 4%;
        display: flex;
    }

    .status {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 10px;
    }

    .nodata {
        color: #DFDCC9;
        font-size: 25px;
    }

    .astrorequests {
        overflow-y: auto;
        max-height: 400px;
        min-height: 200px;
        height: 400px;
    }



    /*New*/
    .status-container {
        width: 10%;
        position: fixed;
        bottom: 0;
        right: 0;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        margin: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }

    /* Mobile view adjustments */
    @media screen and (max-width: 768px) {
        .status-container {
            width: 30%;

        }
    }

    .status-section {
        margin-bottom: 10px;
    }

    .status-section label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    select {
        width: 100%;
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ccc;
        background-color: #f8f8f8;
        color: #333;
        font-size: 14px;
    }

    select option {
        padding: 5px;
    }


    .status-dropdown select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #fff;
        /* Default background color */
        color: #000;
        /* Default text color */
    }

    /* Optional: Styling for dropdown container */
    .status-dropdown {
        position: relative;
    }

    .status-dropdown::after {
        content: ' ';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border-width: 0 5px 5px 5px;
        border-style: solid;
        border-color: #000 transparent transparent transparent;
    }

    .test-sound-btn {
        font-size: 14px;
        width: 100%;
        background-color: #ffd702;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
    }

    .test-sound-btn:hover {
        background-color: #ffd702;
    }

    /*.img-fluid{*/
    /*    height: 100% !important;*/
    /*}*/
</style>
@section('content')
@php
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Facades\DB;
$getAstrologer=DB::table('astrologers')->where('id',astroauthcheck()['astrologerId'])->first();
$assetBase = asset('');
$blankProfile = asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/blank-profile.png');
@endphp


<div class="status-container">
    <div class="status-section">
        <span class="status-label">Chat Status:</span>
        <div class="status-dropdown">
            <select id="chat-status" class="status-select">
                <option value="Online" {{ $getAstrologer->chatStatus == 'Online' ? 'selected' : '' }}>Online</option>
                <option value="Offline" {{ $getAstrologer->chatStatus == 'Offline' || empty($getAstrologer->chatStatus) ? 'selected' : '' }}>Offline</option>
            </select>
        </div>
    </div>
    <div class="status-section">
        <span class="status-label">Call Status:</span>
        <div class="status-dropdown">
            <select id="call-status" class="status-select">
                <option value="Online" {{ $getAstrologer->callStatus == 'Online' ? 'selected' : '' }}>Online</option>
                <option value="Offline" {{ $getAstrologer->callStatus == 'Offline' || empty($getAstrologer->callStatus) ? 'selected' : '' }}>Offline</option>
            </select>
        </div>
    </div>

    <!-- The Test Sounds button now uses the global sound player from the layout -->
    <div class="status-section">
        <button id="test-sound-btn" class="test-sound-btn"><i class="fa-solid fa-play"></i> Test Sounds</button>
    </div>
</div>

<div class="container mt-2">
    <div class="row p-3">
        <div class="col-12 mb-4">
            <h2 class="cat-heading-match font-weight-bold text-center">Explore Your Path with Astrology</h2>
            <div>
                <p class="pt-3 text-center">Explore the mysteries of the zodiac, uncover your birth chart's secrets, and navigate planetary alignments.</p>
                <p class="text-center ">Delve into the intricate tapestry of astrology with personalized horoscopes, insightful birth charts, and transformative guidance. Explore the mysteries of the stars and unlock the secrets of your destiny.</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card m-1 px-0">
                <div class="card-body text-center py-3">
                    <span class="card-title">Chat Request</span>
                </div>
                <ul class="list-group list-group-flush astrorequests" id="chatRequests">

                    @if(isset($getChatRequest['recordList']['chatRequest']) && count($getChatRequest['recordList']['chatRequest']) > 0)
                    @foreach($getChatRequest['recordList']['chatRequest'] as $request)

                    <form action="" id="chatForm">
                        <li class="list-group-item d-flex justify-content-center align-items-center">
                            <input type="hidden" name="chatId" id="chatId" value="{{ $request['chatId'] }}">
                            <input type="hidden" name="partnerId" id="partnerId" value="{{ $request['userId'] }}">
                            <input type="hidden" name="userId" id="userId" value="{{ $request['astrologerId'] }}">
                            <div class="d-flex justify-content-between">
                                <div class="w-25 pr-1">
                                    @if($request['profile'])
                                    <img src="{{ asset($request['profile']) }}" class="rounded-circle img-fluid" style="height: 70px;width:110px" alt="Avatar">
                                    @else
                                    <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/blank-profile.png') }}" class="rounded-circle img-fluid" style="height: 100% !important;" alt="Avatar">
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <span>{{$request['name'] ?:'User'}}</span>
                                    <div class="d-flex">
                                        <i class="fa fa-calendar mt-1" aria-hidden="true"></i>&nbsp;<span>{{ date('d-m-Y', strtotime($request['chatcreatedat'])) }}</span>
                                    </div>
                                </div>
                                <div class="status">
                                    <a class="bg-light-success text-dark border border-success px-3 py-2 acceptchat">Accept</a>
                                    <a class="badge bg-light-danger text-dark border border-danger px-3 py-2 rejectchat">Reject</a>
                                </div>
                            </div>
                        </li>
                    </form>
                    @endforeach
                    @else
                    <li class="list-group-item d-flex justify-content-center align-items-center h-100">
                        <div class="d-flex justify-content-between">
                            <p class="text-center card-title nodata">No Record Found !</p>
                        </div>
                    </li>
                    @endif

                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card m-1 px-0">
                <div class="card-body text-center py-3">
                    <span class="card-title">Call Request</span>
                </div>
                <ul class="list-group list-group-flush astrorequests" id="callRequests">
                    @if(isset($getCallRequest['recordList']) && count($getCallRequest['recordList']) > 0)
                    @foreach($getCallRequest['recordList'] as $request)
                    <form id="callForm">
                        <li class="list-group-item d-flex justify-content-center align-items-center">
                            <input type="hidden" name="callId" id="callId" value="{{ $request['callId'] }}">
                            <input type="hidden" name="partnerId" id="partnerId" value="{{ $request['userId'] }}">
                            <input type="hidden" name="userId" id="userId" value="{{ $request['astrologerId'] }}">
                            <input type="hidden" id="call_type" name="call_type" value="{{ $request['call_type'] }}">
                            <input type="hidden" id="call_method" name="call_method" value="{{ @$request['call_method'] }}">
                            <div class="d-flex justify-content-between">
                                <div class="w-25 pr-1">
                                    @if($request['profile'])
                                    <img src="{{ asset($request['profile']) }}" class="rounded-circle img-fluid" style="height: 70px;width:110px" alt="Avatar">
                                    @else
                                    <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/blank-profile.png') }}" class="rounded-circle img-fluid" style="height: 100% !important;" alt="Avatar">
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <span>{{$request['name']?:'User' }}</span>
                                    <div class="d-flex">
                                        <i class="fa fa-calendar mt-1" aria-hidden="true"></i>&nbsp;<span>{{ date('d-m-Y', strtotime($request['callcreatedat'])) }}</span>
                                    </div>
                                    @if($request['call_type']==10)
                                    <div class="d-flex">
                                        <i class="fa-solid fa-phone mt-1"></i>&nbsp;<span>Audio Call</span>
                                    </div>
                                    @else
                                    <div class="d-flex">
                                        <i class="fas fa-video mt-1"></i>&nbsp;<span>Video Call</span>
                                    </div>
                                    @endif

                                </div>
                                <div class="status">
                                    <a class="badge bg-light-success text-dark border border-success px-3 py-2 acceptcall">Accept</a>
                                    <a class="badge bg-light-danger text-dark border border-danger px-3 py-2 rejectcall">Reject</a>
                                </div>
                            </div>
                        </li>
                    </form>
                    @endforeach
                    @else
                    <li class="list-group-item d-flex justify-content-center align-items-center h-100">
                        <div class="d-flex justify-content-between">
                            <p class="text-center card-title nodata">No Record Found !</p>
                        </div>
                    </li>
                    @endif


                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card m-1 px-0">
                <div class="card-body text-center py-3">
                    <span class="card-title">Report Request</span>
                </div>
                <ul class="list-group list-group-flush astrorequests" id="reportRequests">
                    @if(isset($getUserReport['recordList']) && count($getUserReport['recordList']) > 0)
                    @foreach($getUserReport['recordList'] as $getUserReport)
                    <div>
                        <span class="text-dark font-weight-bold ml-2">{{$getUserReport['reportType']}}</span>
                    </div>
                    <li class="list-group-item d-flex justify-content-center align-items-center reportList"
                        data-id="{{ $getUserReport['id'] }}" style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <div class="w-25 pr-1">
                                <img src="{{asset($getUserReport['profile'])}}" class="rounded-circle img-fluid" style="height: 70px;width:110px" alt="Avatar">
                            </div>
                            <div class="d-flex flex-column">
                                <span>{{$getUserReport['firstName']}} {{$getUserReport['lastName']}}</span>
                                <div class="d-flex">
                                    <i class="fa fa-calendar mt-1 " aria-hidden="true"></i>&nbsp;<span>{{ date('d-m-Y', strtotime($getUserReport['birthDate'])) }}</span>
                                </div>
                                <div class="d-flex">
                                    <i class="fa fa-clock mt-1"></i>&nbsp;<span>{{$getUserReport['birthTime']}}</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                    @else
                    <li class="list-group-item d-flex justify-content-center align-items-center h-100">
                        <div class="d-flex justify-content-between">
                            <p class="text-center card-title nodata">No Record Found !</p>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0px;">
                <h3 class="modal-title" id="reportModalLabel">Report Details</h3>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reportForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- User details and file upload form elements will be populated here -->
                </div>
                <div class="modal-footer " style="border-top : 0px;">
                    <button type="submit" style="font-weight: 600;" class="btn btn-warning">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Waiting Loader Modal -->
<div class="modal fade" id="waitingModal" tabindex="-1" role="dialog" aria-labelledby="waitingModalLabel" aria-hidden="true" data-backdrop="static" data-bs-backdrop="static" data-keyboard="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem; margin-bottom: 20px;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h4 class="mt-3 font-weight-bold" style="color: #333;" id="waitingModalMessage">Processing Request...</h4>
                <p class="text-muted mb-0">Please wait while we connect your session. Do not close or refresh this page.</p>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
    var button = document.getElementById('test-sound-btn');

    button.addEventListener('click', function() {
        var isPlaying = window.toggleTestSound();
        if (isPlaying) {
            button.innerHTML = '<i class="fa-solid fa-stop"></i> Stop Sounds';
        } else {
            button.innerHTML = '<i class="fa-solid fa-play"></i> Test Sounds';
        }
    });

    // Listen to the global sound element for reset
    document.addEventListener('DOMContentLoaded', function() {
        var globalSound = document.getElementById('global-notification-sound');
        if (globalSound) {
            globalSound.addEventListener('pause', function() {
                if (!window.manualSoundPlaying) {
                    button.innerHTML = '<i class="fa-solid fa-play"></i> Test Sounds';
                }
            });
        }
    });
</script>

<script>
    // ------------------------------------Chat Section----------------------------------------------------------------------
    $(document).on('click', '.acceptchat', function(e) {
        e.preventDefault();

        @php
        $session = new Session();
        $token = $session->get('astrotoken');
        $astrologerId = astroauthcheck()['astrologerId'];
        @endphp

        var form = $(this).closest('form');
        var formData = form.serialize();

        var astrologerId = "{{astroauthcheck()['astrologerId']}}";


        var chatId = formData.split("chatId=")[1];
        chatId = parseInt(chatId, 10);

        var partnerId = formData.split("partnerId=")[1];
        partnerId = parseInt(partnerId, 10);

        $('#waitingModalMessage').text('Accepting chat request...');
        $('#waitingModal').modal('show');

        $.ajax({
            url: "{{ route('api.insertChatRequest', ['token' => $token,'astrologerId' =>$astrologerId]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                $.ajax({
                    url: "{{ route('api.acceptChatRequest', ['token' => $token]) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        toastr.success('Chat Started Successfully..Wait');
                        window.location.href = "{{ route('front.astrologerchat') }}" + "?partnerId=" +
                            partnerId + "&chatId=" + chatId;
                    },
                    error: function(xhr, status, error) {
                        $('#waitingModal').modal('hide');
                        toastr.error(xhr.responseText);
                    }
                });
            },
            error: function(xhr, status, error) {
                $('#waitingModal').modal('hide');
                toastr.error(xhr.responseText);
            }
        });
    });

    // Reject Chat

    $(document).on('click', '.rejectchat', function(e) {
        e.preventDefault();

        @php
        $token = $session->get('astrotoken');
        @endphp

        var form = $(this).closest('form');
        var formData = form.serialize();

        $('#waitingModalMessage').text('Rejecting chat request...');
        $('#waitingModal').modal('show');

        $.ajax({
            url: "{{ route('api.rejectChatRequest', ['token' => $token]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success('Chat Rejected Successfully.');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                $('#waitingModal').modal('hide');
                toastr.error(xhr.responseText);
            }
        });
    });
</script>

{{-- ----------------------------------------Call Section ------------------------------------------------- --}}

<script>
    $(document).on('click', '.acceptcall', function(e) {
        e.preventDefault();

        @php
        $session = new Session();
        $token = $session->get('astrotoken');
        $astrologerId = astroauthcheck()['astrologerId'];
        @endphp

        var form = $(this).closest('form');
        var formData = form.serialize();

        var astrologerId = "{{astroauthcheck()['astrologerId']}}";


        var callId = formData.split("callId=")[1];
        callId = parseInt(callId, 10);

        var partnerId = formData.split("partnerId=")[1];
        partnerId = parseInt(partnerId, 10);
        var call_type = formData.split("call_type=")[1];
        partnerId = parseInt(call_type, 10);
        var call_method = formData.split("call_method=")[1];

        $('#waitingModalMessage').text('Accepting call request...');
        $('#waitingModal').modal('show');

        function handleCallAcceptance(callId, formData, token, channelName, call_method, callType, agoraAppIdValue = null, agorCertificateValue = null) {
            $.ajax({
                url: "{{ route('api.acceptCallRequest', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Please wait...');

                    // Check if the call method is 'exotel'
                    if (call_method === 'exotel' || call_method === 'hms' || call_method === 'zegocloud') {
                        storeCallToken(callId, call_method, callType);
                    } else {
                        generateRtcToken(channelName, agoraAppIdValue, agorCertificateValue, callId, callType);
                    }
                },
                error: function(xhr) {
                    $('#waitingModal').modal('hide');
                    toastr.error(xhr.responseText);
                }
            });
        }

        // Store token for 'exotel' calls
        function storeCallToken(callId, call_method, callType) {
            $.ajax({
                url: "{{ route('api.storeToken') }}",
                type: 'POST',
                data: {
                    callId: callId,
                    fromWeb: 1
                },
                success: function(response_call) {
                    // Remove this if not needed
                    if (call_method == 'hms' || call_method == 'zegocloud') {
                        toastr.success('Call accepted successfully');
                        window.location.href = "{{ route('front.astrologercall') }}" + "?callId=" + callId + "&call_type=" + callType + "&call_method=" + call_method;
                    } else {
                        $('#waitingModal').modal('hide');
                        toastr.success('Call accepted successfully..You will get Phone Call Soon');
                    }
                },
                error: function(xhr) {
                    $('#waitingModal').modal('hide');
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
                            $('#waitingModal').modal('hide');
                            toastr.error(JSON.parse(xhr.responseText).error.paymentMethod[0]);
                        }
                    });
                },
                error: function(xhr) {
                    $('#waitingModal').modal('hide');
                    toastr.error(xhr.responseText);
                }
            });
        }

        // Example usage
        handleCallAcceptance(callId, formData, '{{$token}}', '{{$channel_name}}', call_method, call_type, '<?= $agoraAppIdValue->value; ?>', '<?= $agorcertificateValue->value; ?>');


    });

    // Reject Call

    $(document).on('click', '.rejectcall', function(e) {

        // e.preventDefault();

        @php
        $token = $session->get('astrotoken');
        @endphp

        var form = $(this).closest('form');
        var formData = form.serialize();

        $('#waitingModalMessage').text('Rejecting call request...');
        $('#waitingModal').modal('show');

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
                $('#waitingModal').modal('hide');
                toastr.error(xhr.responseText);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        window.updateChatRequests = function(requests) {
            const chatRequests = $('#chatRequests');
            if (chatRequests.length === 0) return;
            chatRequests.empty();
            if (requests && requests.length > 0) {
                requests.forEach(request => {
                    const profileImg = request.profile ? `${request.profile}` : '{{ $blankProfile }}';
                    const name = request.name ? `${request.name}` : 'User';
                    chatRequests.append(`
                            <form id="chatForm">
                                <li class="list-group-item d-flex justify-content-center align-items-center">
                                    <input type="hidden" name="chatId" id="chatId" value="${request.chatId}">
                                    <input type="hidden" name="partnerId" id="partnerId" value="${request.userId}">
                                    <input type="hidden" name="userId" id="userId" value="${request.astrologerId}">
                                    <div class="d-flex justify-content-between">
                                        <div class="w-25 pr-1">
                                            <img src="${profileImg}" class="rounded-circle img-fluid" style="height: 70px;width:110px" alt="Avatar">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span>${name}</span>
                                            <div class="d-flex">
                                                <i class="fa fa-calendar mt-1" aria-hidden="true"></i>&nbsp;<span>${new Date(request.chatcreatedat).toLocaleDateString()}</span>
                                            </div>
                                        </div>
                                        <div class="status">
                                            <a class="badge bg-light-success text-dark border border-success  px-3 py-2 acceptchat">Accept</a>
                                            <a class="badge bg-light-danger text-dark border border-danger  px-3 py-2 rejectchat">Reject</a>
                                        </div>
                                    </div>
                                </li>
                            </form>
                        `);
                });
            } else {
                chatRequests.append('<li class="list-group-item d-flex justify-content-center align-items-center h-100"><p class="text-center card-title nodata">No Record Found !</p></li>');
            }
        }

        window.updateCallRequests = function(requests) {
            const callRequests = $('#callRequests');
            if (callRequests.length === 0) return;
            callRequests.empty();
            if (requests && requests.length > 0) {
                requests.forEach(request => {
                    const profileImg = request.profile ? `${request.profile}` : '{{ $blankProfile }}';
                    const name = request.name ? `${request.name}` : 'User';
                    callRequests.append(`
                            <form id="callForm">
                                <li class="list-group-item d-flex justify-content-center align-items-center">
                                    <input type="hidden" name="callId" id="callId" value="${request.callId}">
                                    <input type="hidden" name="partnerId" id="partnerId" value="${request.userId}">
                                    <input type="hidden" name="userId" id="userId" value="${request.astrologerId}">
                                    <input type="hidden" id="call_type" name="call_type" value="${request.call_type}">
                                    <input type="hidden" id="call_method" name="call_method" value="${request.call_method}">
                                    <div class="d-flex justify-content-between">
                                        <div class="w-25 pr-1">
                                            <img src="${profileImg}" class="rounded-circle img-fluid" style="height: 70px;width:110px" alt="Avatar">
                                        </div>
                                        <div class="d-flex flex-column">
                                             <span>${name}</span>
                                            <div class="d-flex">
                                                <i class="fa fa-calendar mt-1" aria-hidden="true"></i>&nbsp;<span>${new Date(request.callcreatedat).toLocaleDateString()}</span>
                                            </div>
                                            ${request.call_type == 10 ?
                                            '<div class="d-flex"><i class="fa-solid fa-phone mt-1"></i>&nbsp;<span>Audio Call</span></div>' :
                                            '<div class="d-flex"><i class="fas fa-video mt-1"></i>&nbsp;<span>Video Call</span></div>'}
                                        </div>
                                        <div class="status">
                                            <a class="badge bg-light-success text-dark border border-success  px-3 py-2 acceptcall">Accept</a>
                                            <a class="badge bg-light-danger text-dark border border-danger  px-3 py-2 rejectcall">Reject</a>
                                        </div>
                                    </div>
                                </li>
                            </form>
                        `);
                });
            } else {
                callRequests.append('<li class="list-group-item d-flex justify-content-center align-items-center h-100"><p class="text-center card-title nodata">No Record Found !</p></li>');
            }
        }

        window.updateReportRequests = function(requests) {
            const reportRequests = $('#reportRequests');
            if (reportRequests.length === 0) return;
            reportRequests.empty();
            if (requests && requests.length > 0) {
                requests.forEach(request => {
                    const profileImg = request.profile ? `${request.profile}` : '{{ $blankProfile }}';
                    const lastName = request.lastName ? request.lastName : '';
                    reportRequests.append(`
                            <div><span class="text-dark font-weight-bold ml-2">${request.reportType}</span></div>
                            <li class="list-group-item d-flex justify-content-center align-items-center reportList" data-id="${request.id}" style="cursor: pointer;">
                                <div class="d-flex justify-content-between">
                                    <div class="w-25 pr-1">
                                        <img src="${profileImg}" class="rounded-circle img-fluid" style="height: 70px;width:110px" alt="Avatar">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span>${request.firstName} ${lastName}</span>
                                        <div class="d-flex">
                                            <i class="fa fa-calendar mt-1 " aria-hidden="true"></i>&nbsp;<span>${new Date(request.birthDate).toLocaleDateString()}</span>
                                        </div>
                                        <div class="d-flex">
                                            <i class="fa fa-clock mt-1"></i>&nbsp;<span>${request.birthTime}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        `);
                });
            } else {
                reportRequests.append('<li class="list-group-item d-flex justify-content-center align-items-center h-100"><p class="text-center card-title nodata">No Record Found !</p></li>');
            }
        }

        // Note: fetch Chat/Call/Report Requests is now handled by the master layout globally.
    });
</script>
<script>
    $(document).ready(function() {
        // Click event handler for list items (assuming .list-group-item is the class of your list items)
        $(document).on('click', '.reportList', function(e) {
            e.preventDefault();
            var reportId = $(this).data('id');

            $.ajax({
                url: '/api/getUserReportRequestById',
                method: 'POST',
                data: {
                    id: reportId
                },
                success: function(response) {
                    if (response.status === 200 && response.recordList.length > 0) {
                        var record = response.recordList[0];

                        var profileImage = record.profile ? `${record.profile}` : '{{ $blankProfile }}';

                        var html = `
                            <div>
                                <div class="user-card">
                                    <input type="hidden" id="id" name="id" value="${record.id}">
                                    <div class="user-card-img">
                                        <img src="${profileImage}" alt="Report Image" class="img-fluid style="height: 100% !important;" rounded-circle">
                                    </div>
                                    <div class="user-card-info">
                                        <h2>${record.firstName} ${record.lastName}</h2>
                                        <p><span>Gender:</span> ${record.gender}</p>
                                        <p><span>Birth Date:</span> ${record.birthDate}</p>
                                        <p><span>Birth Time:</span> ${record.birthTime}</p>
                                        <p><span>Birth Place:</span> ${record.birthPlace}</p>
                                        <p><span>Occupation:</span> ${record.occupation}</p>
                                        <p><span>Marital Status:</span> ${record.maritalStatus}</p>
                                        <p><span>Report Type:</span> ${record.reportType}</p>
                                        <p><span>Comments:</span> ${record.comments}</p>
                                        <!-- Add any additional details here -->
                                    </div>
                                </div>
                                <div class="mt-3 mb-3 container">
                                    <label for="reportFile" class="form-label">Upload PDF</label>
                                    <input type="file" accept=".pdf" class="form-control" id="reportFile"  style="height: 44px">
                                    <input type="hidden" class="form-control" id="Base64reportFile" name="reportFile">
                                </div>
                            </div>`;
                        $('.modal-body').html(html);
                        $('#reportModal').modal('show');
                    } else {
                        toastr.error('Error: No record found or unexpected status ' + response.status);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseText);
                }
            });
        });
        $(document).on('change', '#reportFile', function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var binaryString = e.target.result;
                    var base64String = btoa(binaryString);
                    $('#Base64reportFile').val(base64String);
                };
                reader.readAsBinaryString(file);
            }
        });
        // Form submission handler
        $('#reportForm').submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: '{{ route("api.addUserReportFile")}}',
                method: 'POST',
                data: {
                    id: $('#id').val(),
                    reportFile: $('#Base64reportFile').val()
                },
                success: function(response) {
                    console.log(response);
                    if (response.status === 200) {
                        toastr.success('Pdf Uploded Successfully');
                        $('#reportModal').modal('hide');
                        window.location.href = "{{route('front.astrologerindex')}}";
                    } else {
                        toastr.error('Error: No record found or unexpected status ' + response.status);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseText);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        @php
        $session = new Session();
        $token = $session->get('astrotoken');
        $astrologerId = astroauthcheck()['astrologerId'];
        @endphp

        $('#chat-status').on('change', function() {
            let chatStatus = $(this).val();
            $.ajax({
                url: "{{ route('api.addChatStatus') }}",
                type: 'POST',
                data: {
                    status: chatStatus,
                    token: "{{$token}}",
                    astrologerId: "{{$astrologerId}}"
                },
                success: function(response) {
                    toastr.success('Chat status updated');
                },
                error: function(xhr, status, error) {
                    toastr.error('Error updating chat status:', error);
                }
            });
        });

        $('#call-status').on('change', function() {
            let callStatus = $(this).val();
            $.ajax({
                url: "{{ route('api.addCallStatus') }}",
                type: 'POST',
                data: {
                    status: callStatus,
                    token: "{{$token}}",
                    astrologerId: "{{$astrologerId}}"
                },
                success: function(response) {

                    toastr.success('Call status updated');
                },
                error: function(xhr, status, error) {
                    toastr.error('Error updating call status:', error);
                }
            });
        });


    });
</script>


@endsection
