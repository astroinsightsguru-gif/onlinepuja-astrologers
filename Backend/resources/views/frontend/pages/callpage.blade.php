@extends('frontend.layout.master')
@php
use Symfony\Component\HttpFoundation\Session\Session;
$session = new Session();
$token = $session->get('token');
@endphp
@php
    $call_method = $callrequest->call_method ?? 'agora';
@endphp
<!-- <link rel="stylesheet" href="{{ asset('public/frontend/agora/index.css') }}"> -->
<style>
    @media only screen and (max-width: 767px) {

        #local-player div:first-child,
        #remote-playerlist div:first-child {
            min-height: 0px !important;
            position: unset !important;
        }
    }

    .dIzgYQV4CBbzZxzJbwbS {
        display: none !important;
    }
    .eLS4omBUBKIdRuH3vIbv{
        display: none!important;
    }
    .QeMJj1LEulq1ApqLHxuM{
        display: none!important;
    }

    .video-call-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        height: 500px;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
    }

    .video-participant {
        position: relative;
        background: #2a2a2a;
        border-radius: 8px;
        overflow: hidden;
    }

    .player,
    video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .name-tag {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10;
    }

    .video-action-button {
        margin: 5px;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        background: #007bff;
        color: white;
        cursor: pointer;
    }

    .video-action-button.muted,
    .video-action-button.off {
        background-color: #f44336 !important;
    }

    .video-action-button.endcall {
        background: #dc3545;
    }

    .navigation {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
    }

    #remainingTime {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #dc3545;
    }

    /* Provider containers */
    .agora-container,
    .hms-container,
    .zegocloud-container {
        display: none;
    }

    /* Zegocloud UIKit Container */
    #zegocloudUIKitContainer {
        width: 100%;
        height: 500px;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Loading states */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 9999;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .avatar-fallback {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(45deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }
</style>

@section('content')
@if (authcheck())
@php
$userId = authcheck()['id'];
$astrologerId = request()->query('astrologerId');
$callId = request()->query('callId');
$call_type = request()->query('call_type');
@endphp
@endif

<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize;">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Call</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Intake Form Modal -->
<div class="modal fade mt-2 mt-md-5" id="intake" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">Intake Form</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt-0 pb-0">
                <form class="px-3 font-14" method="get" id="intakeForm">
                    <input type="hidden" name="astrologerId" value="{{ $astrologerId }}">
                    @if (authcheck())
                    <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                    @endif
                    <div class="col-12 py-3">
                        <div class="form-group mb-0">
                            <label>Select Time You want to call<span class="color-red">*</span></label><br>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="180"> 3 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="300"> 5 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="600"> 10 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="900"> 15 mins
                                </label>
                                <label class="btn btn-info btn-sm">
                                    <input type="radio" name="call_duration" value="1200"> 20 mins
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 py-3">
                        <div class="row">
                            <div class="col-12 pt-md-3 text-center mt-2">
                                <button class="font-weight-bold ml-0 w-100 btn btn-chat" id="loaderintakeBtn" type="button" style="display:none;" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                                </button>
                                <button type="submit" class="btn btn-block btn-chat px-4 px-md-5 mb-2" id="intakeBtn">Continue Call</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input id="appid" type="hidden" value="{{ $agoraAppIdValue }}">
<input id="token" type="hidden" value="{{ $callrequest->token }}">
<input id="channel" type="hidden" value="{{ $callrequest->channelName }}">
<input id="callMethod" type="hidden" value="{{ $callrequest->call_method }}">
<input id="userId" type="hidden" value="{{ $userId }}">
<input id="callType" type="hidden" value="{{ $call_type }}">

<!-- Loading Overlay -->
<!-- <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p id="loadingText">Initializing call...</p>
</div> -->

<section class="container">
    <div class="row">
        <div class="col-md-2 col-sm-12 order-md-0 order-2 bottom-sm-0 bottom-buttons">
            <div class="navigation flex-sm-column h-100">
                <span id="remainingTime" class="color-red">{{ $callrequest->call_duration }}</span>
                @if($call_method == 'agora')
                    <button class="video-action-button mic" onclick="toggleMic()" id="mic-icon">
                        <i class="fas fa-microphone"></i>
                    </button>
                    @if($call_type == 11)
                        <button class="video-action-button camera" onclick="toggleVideo()" id="video-icon">
                            <i class="fas fa-video"></i>
                        </button>
                    @endif
                @else
                    <span></span>
                @endif


                <a
                    data-toggle="modal"
                    data-target="#intake"
                    class="btn btn-report mr-3 mb-2 add-topup-btnn"
                    id="addTopupLink">
                    Add Topup
                </a>
                <form id="endCallForm" class="d-inline-block">
                    <input type="hidden" name="callId" value="{{ $callId }}">
                    <input type="hidden" name="totalMin" id="totalMin" value="">
                    <button type="button" class="video-action-button endcall" id="leave" onclick="endCall()">Leave</button>
                    <small style="display:block">Note : call can be end after 1 min</small>
                </form>
                <div class="video-call-actions"></div>
            </div>
        </div>

        <div class="app-main col-md-9 col-sm-12 order-sm-0">
            <!-- Agora Video Container -->
            <div class="video-call-wrapper shadow agora-container" id="agoraContainer">
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="local-player-name">{{ authcheck()['name'] }}</a>
                    <div id="local-player" class="player"></div>
                    <div class="avatar-fallback" id="local-avatar">
                        {{ substr(authcheck()['name'], 0, 1) }}
                    </div>
                </div>
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="remote-player-name">Astrologer</a>
                    <div id="remote-playerlist"></div>
                    <div class="avatar-fallback" id="remote-avatar">
                        A
                    </div>
                </div>
            </div>

            <!-- Zegocloud UIKit Container -->
            <div class="shadow zegocloud-container" id="zegocloudContainer" style="display: none;">
                <div id="zegocloudUIKitContainer"></div>
            </div>
        </div>
    </div>
</section>

<!-- Insufficient TopUp Modal -->
<div class="modal fade" id="insufficientTopUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Update Top Up</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <p>Your current session will expire soon. Please Top Up Now.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning text-white" data-dismiss="modal" data-toggle="modal" data-target="#intake">
                    Top Up Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>




{{-- Loading Overlay --}}
<!-- <div id="loadingOverlay"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; flex-direction:column;">
    <div id="loadingText" style="color:white; font-size:18px;">Loading...</div>
</div> -->


// =============================================
// USER SIDE - Fixed Agora Implementation
// =============================================
// Add this BEFORE the existing scripts in user blade file

<script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>

<script>
    // Global variables - declared once at top level
    var agoraClient = null;
    var localAudioTrack = null;
    var localVideoTrack = null;
    var remoteUsers = {};
    var callMethod = "{{ $callrequest->call_method }}";
    var isVideoCall = "{{ $call_type }}" == "11";
    var astrologerJoined = false; // Track if astrologer has actually joined

    $(document).ready(function() {
        if (callMethod === 'agora') {
            initializeAgora();
        }
        // Zegocloud initialization happens in the existing script
    });

    async function initializeAgora() {
        try {
            console.log('Initializing Agora for user...');

            // Show Agora container
            document.getElementById('agoraContainer').style.display = 'grid';
            document.querySelectorAll('.zegocloud-container').forEach(el => {
                el.style.display = 'none';
            });

            const appID = document.getElementById('appid').value;
            const token = document.getElementById('token').value;
            const channel = document.getElementById('channel').value;

            if (!appID || !token || !channel) {
                throw new Error('Missing Agora configuration');
            }

            // Create client
            agoraClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

            // Event: user published (astrologer joined)
            agoraClient.on("user-published", async (user, mediaType) => {
                await agoraClient.subscribe(user, mediaType);
                console.log("Subscribed to", mediaType, "from user", user.uid);

                // Mark astrologer as joined
                if (!astrologerJoined) {
                    astrologerJoined = true;
                    console.log("Astrologer has joined the call - starting timer now");

                    // Start timer when astrologer joins
                    if (!timerStartedMain) {
                        var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
                        $.get("{{ route('front.getDateTime') }}", function(response) {
                            var currentTime = new Date(response).getTime();
                            var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                            remainingTimeMain = callDurationMain - elapsedTime;

                            if (remainingTimeMain < 0) {
                                remainingTimeMain = 0;
                            }

                            startTimer();
                            timerStartedMain = true;
                        }).fail(function() {
                            console.error("Error fetching server time");
                            var currentTime = new Date().getTime();
                            var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                            remainingTimeMain = callDurationMain - elapsedTime;

                            if (remainingTimeMain < 0) {
                                remainingTimeMain = 0;
                            }

                            startTimer();
                            timerStartedMain = true;
                        });
                    }
                }

                if (mediaType === "video") {
                    const remoteVideoTrack = user.videoTrack;
                    const playerContainer = document.getElementById("remote-playerlist");
                    playerContainer.innerHTML = '';
                    document.getElementById('remote-avatar').style.display = 'none';
                    remoteVideoTrack.play(playerContainer);
                    console.log("Remote video playing");
                }

                if (mediaType === "audio") {
                    const remoteAudioTrack = user.audioTrack;
                    remoteAudioTrack.play();
                    console.log("Remote audio playing");
                }

                remoteUsers[user.uid] = user;
            });

            // Event: user unpublished
            agoraClient.on("user-unpublished", (user, mediaType) => {
                console.log("User unpublished:", mediaType);
                if (mediaType === "video") {
                    document.getElementById('remote-avatar').style.display = 'flex';
                }
            });

            // Event: user left (astrologer left)
            agoraClient.on("user-left", (user) => {
                console.log("Astrologer left:", user.uid);
                delete remoteUsers[user.uid];
                document.getElementById('remote-avatar').style.display = 'flex';

                // Automatically end call when astrologer leaves
                console.log("Astrologer left the call, ending call automatically");
                setTimeout(function() {
                    if (typeof callEndedAgora === 'undefined' || !callEndedAgora) {
                        if (typeof endCall === 'function') {
                            endCall();
                        }
                    }
                }, 1000);
            });

            // Join channel
            await agoraClient.join(appID, channel, token, null);
            console.log("Joined Agora channel successfully");

            // Create local tracks
            localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();

            if (isVideoCall) {
                localVideoTrack = await AgoraRTC.createCameraVideoTrack();
                localVideoTrack.play("local-player");
                document.getElementById('local-avatar').style.display = 'none';
                await agoraClient.publish([localAudioTrack, localVideoTrack]);
                console.log("Published audio and video");
            } else {
                await agoraClient.publish([localAudioTrack]);
                console.log("Published audio only");
            }

        } catch (error) {
            console.error('Agora initialization failed:', error);
            alert('Failed to initialize call: ' + error.message);
        }
    }

    async function toggleMic() {
        if (localAudioTrack) {
            try {
                const newState = !localAudioTrack.enabled;
                await localAudioTrack.setEnabled(newState);
                
                const micBtn = document.getElementById('mic-icon');
                if (newState) {
                    micBtn.classList.remove('muted');
                    micBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                } else {
                    micBtn.classList.add('muted');
                    micBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
                }
            } catch (err) {
                console.error("Error toggling microphone:", err);
            }
        }
    }

    async function toggleVideo() {
        if (localVideoTrack) {
            try {
                const newState = !localVideoTrack.enabled;
                await localVideoTrack.setEnabled(newState);
                
                const videoBtn = document.getElementById('video-icon');
                if (newState) {
                    videoBtn.classList.remove('off');
                    videoBtn.innerHTML = '<i class="fas fa-video"></i>';
                    document.getElementById('local-avatar').style.display = 'none';
                } else {
                    videoBtn.classList.add('off');
                    videoBtn.innerHTML = '<i class="fas fa-video-slash"></i>';
                    document.getElementById('local-avatar').style.display = 'flex';
                }
            } catch (err) {
                console.error("Error toggling video:", err);
            }
        }
    }

    // Enhanced endCall function for Agora cleanup
    async function endCallAgora() {
        if (typeof agoraClient !== 'undefined' && agoraClient) {
            try {
                if (localAudioTrack) {
                    localAudioTrack.close();
                    localAudioTrack = null;
                }
                if (localVideoTrack) {
                    localVideoTrack.close();
                    localVideoTrack = null;
                }
                await agoraClient.leave();
                agoraClient = null;
                console.log('Agora cleanup completed');
            } catch (error) {
                console.error('Agora cleanup error:', error);
            }
        }
    }
</script>
{{-- ===================== CALL SCRIPTS ===================== --}}
@if($call_method == 'agora')
    {{-- ===================== AGORA SCRIPTS ===================== --}}
    <!-- <script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>
    <script src="{{ asset('public/frontend/agora/index.js') }}"></script> -->
    <script>
            var callEndedAgora = false;

    async function endCall() {
        if (typeof callEndedAgora !== 'undefined' && callEndedAgora) {
            return;
        }
        callEndedAgora = true;

        // Clear timer
        if (typeof timerInterval !== 'undefined' && timerInterval) {
            clearInterval(timerInterval);
        }

        // Cleanup Agora
        await endCallAgora();

        @php
            $session = new Session();
            $token = $session->get('token');
        @endphp

        var totalSeconds = (typeof callDurationMain !== 'undefined' && typeof remainingTimeMain !== 'undefined')
            ? callDurationMain - remainingTimeMain
            : 0;
        $("#totalMin").val(totalSeconds);

        var formData = $('#endCallForm').serialize();

        $.ajax({
            url: "{{ route('api.endCall', ['token' => $token]) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success('Call Ended Successfully');
                setTimeout(function() {
                    window.location.href = "{{ route('front.home') }}";
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Error ending call:', error);
                toastr.error(xhr.responseText || 'Error ending call');
                // Still redirect even on error
                setTimeout(function() {
                    window.location.href = "{{ route('front.home') }}";
                }, 2000);
            }
        });
    }
    </script>

@elseif($call_method == 'zegocloud')
    {{-- ===================== ZEGOCLOUD SCRIPT ===================== --}}
<script>
        var currentProvider = "{{ $callrequest->call_method }}";
        var callDurationZego = parseInt("{{$callrequest->call_duration}}");
        var remainingTimeZego = callDurationZego;
        var timerIntervalZego;
        var callEndedZego = false;

        var zegoUIKit = null;
        var zegoJoined = false;

        $(document).ready(function() {
            console.log('Initializing call with provider:', currentProvider);
            initializeCall();
            // Timer will start when astrologer joins (not immediately)
        });

        function initializeCall() {
            showLoading('Initializing call...');
            if (currentProvider === 'agora') {
                initializeAgora();
            } else if (currentProvider === 'zegocloud') {
                initializeZegocloudUIKit();
            } else {
                currentProvider = 'agora';
                initializeAgora();
            }
        }

        async function initializeZegocloudUIKit() {
            try {
                showProviderUI('zegocloud');
                showLoading('Connecting to Zegocloud...');

                const appID = "{{ systemflag('zegoAppId') }}";
                const serverSecret = "{{ systemflag('zegoServerSecret') }}";
                const userID = "{{ $userId }}";
                const userName = "{{authcheck()['name']}}";
                const roomID = document.getElementById('channel').value;
                const isVideoCall = "{{ $call_type }}" == "11";

                if (!appID) throw new Error('Zegocloud App ID is missing');
                if (!serverSecret || serverSecret === '') throw new Error('Zegocloud Server Secret is missing or invalid');
                if (!roomID) throw new Error('Room ID is missing');

                const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(
                    parseInt(appID),
                    serverSecret,
                    roomID,
                    userID,
                    userName
                );

                zegoUIKit = ZegoUIKitPrebuilt.create(kitToken);

                const config = {
                    container: document.querySelector("#zegocloudUIKitContainer"),
                    scenario: {
                        mode: isVideoCall ? ZegoUIKitPrebuilt.VideoCall : ZegoUIKitPrebuilt.VoiceCall,
                    },
                    showPreJoinView: false,
                    turnOnCameraWhenJoining: isVideoCall,
                    turnOnMicrophoneWhenJoining: true,
                    useFrontFacingCamera: true,
                    showMyCameraToggleButton: isVideoCall,
                    showMyMicrophoneToggleButton: true,
                    showAudioVideoSettingsButton: true,
                    showTextChat: false,
                    showUserList: false,
                    showRoomTimer: true,
                    maxUsers: 2,
                    layout: "Auto",
                    showLayoutButton: false,
                    showScreenSharingButton: false,
                    videoResolutionDefault: ZegoUIKitPrebuilt.VideoResolution_360P,

                    onJoinRoom: () => {
                        console.log('✅ Zegocloud UIKit: Joined room successfully');
                        zegoJoined = true;
                        hideLoading();
                    },

                    onLeaveRoom: () => {
                        console.log('Zegocloud UIKit: Left room');
                        if (zegoJoined) endCall();
                    },

                    onUserLeave: (users) => {
                        console.log('Astrologer left:', users);
                        astrologerJoined = false;
                        setTimeout(() => {
                            if (zegoJoined && (typeof callEndedZego === 'undefined' || !callEndedZego)) {
                                console.log('Astrologer left, ending call automatically...');
                                endCall();
                            }
                        }, 1000);
                    },

                    onUserJoin: (users) => {
                        console.log('Astrologer joined Zegocloud room:', users);
                        if (!astrologerJoined) {
                            astrologerJoined = true;
                            console.log("Astrologer has joined the call via Zegocloud - starting timer now");

                            // Start timer when astrologer joins
                            if (!timerStartedMain) {
                                var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
                                $.get("{{ route('front.getDateTime') }}", function(response) {
                                    var currentTime = new Date(response).getTime();
                                    var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                                    remainingTimeMain = callDurationMain - elapsedTime;

                                    if (remainingTimeMain < 0) {
                                        remainingTimeMain = 0;
                                    }

                                    startTimer();
                                    timerStartedMain = true;
                                }).fail(function() {
                                    console.error("Error fetching server time");
                                    var currentTime = new Date().getTime();
                                    var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                                    remainingTimeMain = callDurationMain - elapsedTime;

                                    if (remainingTimeMain < 0) {
                                        remainingTimeMain = 0;
                                    }

                                    startTimer();
                                    timerStartedMain = true;
                                });
                            }
                        }
                    },

                    onError: (error) => {
                        console.error('Zegocloud UIKit Error:', error);
                        showError('Zegocloud error: ' + error.message);
                    }
                };

                zegoUIKit.joinRoom(config);
            } catch (error) {
                console.error('Zegocloud UIKit initialization failed:', error);
                showError('Failed to initialize Zegocloud: ' + (error.message || 'Unknown error'));
                hideLoading();
            }
        }

        function showProviderUI(provider) {
            document.querySelectorAll('.agora-container, .zegocloud-container').forEach(el => {
                el.style.display = 'none';
            });
            if (provider === 'zegocloud') {
                document.getElementById('zegocloudContainer').style.display = 'block';
            } else {
                document.getElementById(provider + 'Container').style.display = 'block';
            }
        }

        function showLoading(message) {
            const overlay = document.getElementById('loadingOverlay');
            const text = document.getElementById('loadingText');
            if (overlay) {
                overlay.style.display = 'flex';
                if (text && message) text.textContent = message;
            }
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'none';
        }

        function showError(message) {
            console.error('Error:', message);
            alert('Error: ' + message);
        }

        function startTimer() {
            function updateTimer() {
                const minutes = Math.floor(remainingTimeZego / 60);
                const seconds = remainingTimeZego % 60;
                const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                const timeElement = document.getElementById('remainingTime');
                if (timeElement) {
                    timeElement.textContent = formattedTime;
                }
            }

            updateTimer();
            timerIntervalZego = setInterval(() => {
                remainingTimeZego--;
                updateTimer();

                if (remainingTimeZego <= 0) {
                    if (timerIntervalZego) clearInterval(timerIntervalZego);
                    endCall();
                }
                if (remainingTimeZego === 90 || remainingTimeZego === 30) {
                    $('#insufficientTopUpModal').modal('show');
                }
            }, 1000);
        }

        async function endCall() {
            if (typeof callEndedZego !== 'undefined' && callEndedZego) return;
            callEndedZego = true;

            if (typeof timerIntervalZego !== 'undefined' && timerIntervalZego) {
                clearInterval(timerIntervalZego);
            }

            // Cleanup Agora
            if (typeof agoraClient !== 'undefined' && agoraClient) {
                try {
                    if (localAudioTrack) {
                        localAudioTrack.close();
                        localAudioTrack = null;
                    }
                    if (localVideoTrack) {
                        localVideoTrack.close();
                        localVideoTrack = null;
                    }
                    await agoraClient.leave();
                    agoraClient = null;
                } catch (e) {
                    console.error('Agora cleanup error:', e);
                }
            }

            // Cleanup Zegocloud
            if (typeof zegoUIKit !== 'undefined' && zegoUIKit && zegoJoined) {
                try {
                    zegoUIKit.leaveRoom();
                    zegoUIKit = null;
                    zegoJoined = false;
                } catch (e) {
                    console.error('Zego cleanup error:', e);
                }
            }

            var totalSeconds = (typeof callDurationZego !== 'undefined' && typeof remainingTimeZego !== 'undefined')
                ? callDurationZego - remainingTimeZego
                : 0;
            $("#totalMin").val(totalSeconds);

            try {
                const response = await fetch("{{ route('api.endCall', ['token' => $token]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        callId: "{{ $callId }}",
                        totalMin: totalSeconds
                    })
                });
                if (response.ok) {
                    console.log('Call ended successfully on server');
                }
            } catch (err) {
                console.error('Error ending call on server:', err);
            }

            toastr.success('Call ended successfully');
            setTimeout(() => {
                window.location.href = "{{ route('front.home') }}";
            }, 2000);
        }

        window.addEventListener('beforeunload', function(e) {
            if (!callEnded) endCall();
        });

        function toggleMic() {
        if (localAudioTrack) {
            localAudioTrack.setEnabled(!localAudioTrack.enabled);
            const micBtn = document.getElementById('mic-icon');
            if (localAudioTrack.enabled) {
                micBtn.classList.remove('muted');
                micBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            } else {
                micBtn.classList.add('muted');
                micBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
            }
        }
    }

        function toggleVideo() {
        if (localVideoTrack) {
            localVideoTrack.setEnabled(!localVideoTrack.enabled);
            const videoBtn = document.getElementById('video-icon');
            if (localVideoTrack.enabled) {
                videoBtn.classList.remove('off');
                videoBtn.innerHTML = '<i class="fas fa-video"></i>';
                document.getElementById('local-avatar').style.display = 'none';
            } else {
                videoBtn.classList.add('off');
                videoBtn.innerHTML = '<i class="fas fa-video-slash"></i>';
                document.getElementById('local-avatar').style.display = 'flex';
            }
        }
    }
 </script>
@endif


<script>
    $(document).ready(function() {
       $('#intakeBtn').click(function(e) {
    e.preventDefault();

    $('#intakeBtn').hide();
    $('#loaderintakeBtn').show();

    setTimeout(function() {
        $('#intakeBtn').show();
        $('#loaderintakeBtn').hide();
    }, 3000);

    var astrocharge = "{{ $getAstrologer['recordList'][0]['charge'] }}";
    var wallet_amount = "{{ $walletAmount ?? 0}}";
    var callId = "{{ $callId }}";
    var token = "{{ session('token') }}";
    var astrologerId = "{{ $getAstrologer['recordList'][0]['id'] }}";
    var userId = "{{ authcheck() ? authcheck()['id'] : 'null' }}";

    $.ajax({
        url: "{{ route('api.getcurrentCallDuration', ['callId' => $callId]) }}",
        type: 'POST',
        success: function(response) {
            if (response.callDuration) {
                let callDurationMinutes = response.callDuration / 60;
                let remainingWalletAmount = wallet_amount - (callDurationMinutes * astrocharge);
                remainingWalletAmount = remainingWalletAmount.toFixed(2);

                var formData = $('#intakeForm').serialize();
                var call_duration = $('input[name="call_duration"]:checked').val();
                var call_duration_minutes = Math.ceil(call_duration / 60);
                var total_charge = astrocharge * call_duration_minutes;

                if (total_charge <= remainingWalletAmount) {
                    // Continue call - update duration
                    $.ajax({
                        url: "{{ route('api.updatecallMinute') }}",
                        type: 'POST',
                        data: {
                            call_duration: call_duration,
                            callId: callId,
                        },
                        success: function(updateResponse) {
                            console.log("Call duration updated successfully");

                            // Force refresh the timer after a short delay to allow Firebase sync
                            setTimeout(function() {
                                refreshTimer();
                            }, 1000);

                            toastr.success('Call Continued');
                            $('#intake').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseText);
                        },
                    });
                } else {
                    // Redirect to payment
                    $.ajax({
                        url: "{{ route('user.addpayment', ['token' => $token]) }}",
                        type: 'POST',
                        data: {
                            amount: total_charge,
                            payment_for: "topupcall",
                            durationcall: call_duration,
                            callId: callId,
                        },
                        success: function(response) {
                            $('#intake').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');

                            // Listen for payment completion (you may need to implement this)
                            window.addEventListener('message', function(event) {
                                if (event.data === 'payment_completed') {
                                    setTimeout(function() {
                                        refreshTimer();
                                    }, 2000);
                                }
                            });

                            window.open(response.url, '_blank', 'width=800,height=600,resizable=yes,scrollbars=yes');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseText);
                        },
                    });
                }
            } else {
                toastr.error('Invalid call duration.');
            }
        },
        error: function(xhr) {
            let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
            toastr.error(errorMessage || 'An error occurred while fetching the call duration.');
        },
    });
});
    });
    $(document).ready(function() {
        $('button.mode-switch').click(function() {
            $('body').toggleClass('dark');
        });

        $(".btn-close-right").click(function() {
            $(".right-side").removeClass("show");
            $(".expand-btn").addClass("show");
        });

        $(".expand-btn").click(function() {
            $(".right-side").addClass("show");
            $(this).removeClass("show");
        });
    });
</script>

<script>
    var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
    var callDurationMain = parseInt("{{ $callrequest->call_duration }}");
    var remainingTimeMain = callDurationMain;
    var elapsedTimeMain = 0;
    var timerIntervalMain = null;
    var timerStartedMain = false; // Track if timer has started

    // Timer will start when astrologer joins, not immediately
    // This prevents timer from starting before astrologer joins
    setupFirebaseListener();

    // Initialize timer display but don't start counting
    updateTimer();

    $("#local-player-name").text("{{ authcheck()['name'] }}");
    $("#remote-player-name").text("{{ $getAstrologer['recordList'][0]['name'] }}");

    function updateTimer() {
        var minutes = Math.floor(remainingTimeMain / 60);
        var seconds = remainingTimeMain % 60;
        var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        var timeElement = document.getElementById('remainingTime');
        if (timeElement) {
            timeElement.innerHTML = formattedTime;
        }
    }

    function startTimer() {
        if (timerIntervalMain) {
            clearInterval(timerIntervalMain);
        }

        updateTimer();

        timerIntervalMain = setInterval(function() {
            remainingTimeMain--;
            if (remainingTimeMain < 0) {
                remainingTimeMain = 0;
            }
            updateTimer();

            if (remainingTimeMain <= 0) {
                if (timerIntervalMain) {
                    clearInterval(timerIntervalMain);
                }
                if (typeof endCall === 'function') {
                    endCall();
                }
                return;
            }

            if (remainingTimeMain == 90 || remainingTimeMain == 30) {
                $('#insufficientTopUpModal').modal('show');
            }

            var totalSeconds = callDurationMain - remainingTimeMain;
            var leaveBtn = document.getElementById('leave');
            if (leaveBtn) {
                if (totalSeconds >= 60) {
                    leaveBtn.disabled = false;
                } else {
                    leaveBtn.disabled = true;
                }
            }
        }, 1000);
    }

 function setupFirebaseListener() {
    const callId = '{{ $callId }}';
    if (typeof firebase === 'undefined' || !firebase.firestore) {
        console.warn('Firebase is not loaded');
        return;
    }

    const db = firebase.firestore();

    db.collection('updatecall').doc(callId)
        .onSnapshot((doc) => {
            if (doc.exists) {
                const firebaseData = doc.data();
                const newDuration = firebaseData.duration;

                // Only update if new duration is different and greater
                if (newDuration && newDuration > callDurationMain) {
                    console.log("Top-up detected! Old duration:", callDurationMain, "New duration:", newDuration);

                    // Calculate how much time was added
                    const addedTime = newDuration - callDurationMain;

                    // Update the total duration
                    callDurationMain = newDuration;

                    // Add the new time to remaining time
                    remainingTimeMain += addedTime;

                    console.log("Added", addedTime, "seconds. New remaining time:", remainingTimeMain);

                    // Update the display immediately
                    if (typeof updateTimer === 'function') {
                        updateTimer();
                    }

                    // Show success message
                    toastr.success('Call time extended by ' + Math.ceil(addedTime / 60) + ' minutes');
                }
            }
        }, (error) => {
            console.error("Firebase listener error:", error);
        });
}

// Also update the refreshTimer function for consistency:

function refreshTimer() {
    $.get("{{ route('front.getDateTime') }}", function(response) {
        const currentTime = new Date(response).getTime();
        const updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
        const elapsedTime = Math.floor((currentTime - updateTime) / 1000);

        // Get the latest call duration from backend
        $.ajax({
            url: "{{ route('api.getcurrentCallDuration', ['callId' => $callId]) }}",
            type: 'POST',
            success: function(response) {
                if (response.callDuration) {
                    const newDuration = response.callDuration;

                    if (newDuration > callDurationMain) {
                        // Top-up detected
                        const addedTime = newDuration - callDurationMain;
                        callDurationMain = newDuration;
                        remainingTimeMain += addedTime;

                        console.log("Timer refreshed with top-up - Added:", addedTime, "seconds");
                    } else {
                        // Normal refresh - recalculate based on elapsed time
                        callDurationMain = newDuration;
                        remainingTimeMain = callDurationMain - elapsedTime;
                    }

                    if (remainingTimeMain < 0) {
                        remainingTimeMain = 0;
                    }

                    if (typeof updateTimer === 'function') {
                        updateTimer();
                    }
                    console.log("Timer refreshed - Duration:", callDurationMain, "Remaining:", remainingTimeMain);
                }
            },
            error: function(xhr) {
                console.error("Error refreshing timer:", xhr);
            }
        });
    }).fail(function() {
        console.error("Error fetching server time for timer refresh");
    });
}
</script>
@endsection
