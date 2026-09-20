@extends('frontend.astrologers.layout.master')

<!-- <link rel="stylesheet" href="{{ asset('public/frontend/agora/index.css') }}"> -->
<style>
    /* Same CSS as user page */
    @media only screen and (max-width: 767px) {
        #local-player div:first-child,
        #remote-playerlist div:first-child {
            min-height: 0px !important;
            position: unset !important;
        }
    }
    .dIzgYQV4CBbzZxzJbwbS{
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

    .player, video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .name-tag {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
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
    .agora-container, .hms-container, .zegocloud-container {
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
        background: rgba(0,0,0,0.8);
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
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

    .btn-kundali {
        background: linear-gradient(45deg, #FFD700, #FFA500);
        color: #000;
        border: none;
        font-weight: bold;
    }

    /* Tab Styles for Kundali Modal */
    .tab-headbox {
        overflow-x: auto;
        /* Enable horizontal scrolling */
        overflow-y: hidden;
        /* Hide vertical overflow */
        white-space: nowrap;
        /* Prevent wrapping of tabs */
        -webkit-overflow-scrolling: touch;
        /* Smooth scrolling on touch devices */
        border-bottom: 1px solid #c2185b !important;
        /* Add border to the container */
    }

    #kundaliTab {
        display: inline-flex;
        /* Use inline-flex for horizontal layout */
        flex-wrap: nowrap;
        /* Prevent wrapping of tabs */
        padding: 0 15px;
        /* Add padding to the tabs container */
        margin-bottom: 0;
        /* Remove default margin */
        list-style: none;
        /* Remove list styling */
    }

    .nav-tabs {
        border-bottom: none !important;
        /* Remove default border */
    }

    .nav-tabs .nav-item {
        margin-bottom: -1px;
        /* Align tabs with the border */
        flex-shrink: 0;
        /* Prevent tabs from shrinking */
    }

    #kundaliTab li a.active {
        border-color: #c2185b #c2185b #FFFFFF !important;
        /* Active tab border color */
        color: #5E5E5E;
        /* Active tab text color */
    }

    #kundaliTab li a {
        font-weight: 600;
        /* Tab text font weight */
        color: #000000;
        /* Tab text color */
        white-space: nowrap;
        /* Prevent text wrapping */
        padding: 0.5rem 1rem !important;
        /* Tab padding */
        border: 1px solid transparent;
        /* Default tab border */
        border-top-left-radius: 0.25rem;
        /* Rounded corners */
        border-top-right-radius: 0.25rem;
        /* Rounded corners */
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #495057;
        /* Active tab text color */
        background-color: #fff;
        /* Active tab background color */
        border-color: #dee2e6 #dee2e6 #fff;
        /* Active tab border color */
    }

    .nav-tabs>li>a {
        background: white !important;
        /* Tab background color */
        border-bottom: 1px solid #c2185b !important;
        /* Tab bottom border */
    }

    @media (max-width: 768px) {
        #kundaliTab {
            display: inline-flex !important;
            /* Use inline-flex for wrapping behavior */
            flex-wrap: wrap !important;
            /* Allow tabs to wrap to the next line */
            overflow-x: visible !important;
            /* Disable horizontal scrolling */
        }

        .nav-tabs .nav-item {
            flex: 1 1 auto;
            /* Allow tabs to grow and shrink as needed */
            text-align: center;
            /* Center-align tab text */
        }

        #kundaliTab li a {
            padding: 0.5rem 0.75rem !important;
            /* Adjust padding for smaller screens */
            font-size: 14px;
            /* Reduce font size for better fit */
        }
    }

    @media (max-width: 576px) {
        svg {
            height: auto !important;
        }
    }
</style>

@section('content')
@php
use Symfony\Component\HttpFoundation\Session\Session;
@endphp
@if (astroauthcheck())
@php
$session = new Session();
$token = $session->get('astrotoken');
$userId = $callrequest->userId;
$astrologerId = astroauthcheck()['astrologerId'];
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
                        <a href="{{ route('front.astrologerindex') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Call</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Kundali Report Modal -->
<div class="modal fade" id="kundaliModal" tabindex="-1" role="dialog" aria-labelledby="kundaliModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kundaliModalLabel">Kundali Report</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="kundaliCloseBtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="kundaliContent">
                <!-- Content will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="kundaliCloseFooterBtn">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input id="appid" type="hidden" value="{{ $agoraAppIdValue->value }}">
<input id="token" type="hidden" value="{{ $callrequest->token }}">
<input id="channel" type="hidden" value="{{ $callrequest->channelName }}">
<input id="callMethod" type="hidden" value="{{ $callrequest->call_method }}">
<input id="astrologerId" type="hidden" value="{{ $astrologerId }}">
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
                <span id="remainingTime" class="color-red">
                    @php
                        $minutes = floor($callrequest->call_duration / 60);
                        $seconds = $callrequest->call_duration % 60;
                        echo sprintf('%02d:%02d', $minutes, $seconds);
                    @endphp
                </span>

                <!-- Agora Controls -->
                <div id="agoraControls" class="agora-container">
                    <button class="video-action-button mic" onclick="toggleMic()" id="mic-icon">
                        <i class="fas fa-microphone"></i>
                    </button>
                    @if($call_type==11)
                    <button class="video-action-button camera" onclick="toggleVideo()" id="video-icon">
                        <i class="fas fa-video"></i>
                    </button>
                    @endif
                    <button class="video-action-button endcall" onclick="endCall()" id="leave">Leave</button>
                </div>

                <!-- Zegocloud UIKit Controls -->
                <div id="zegocloudControls" class="zegocloud-container" style="display: none;">
                    <!-- Zegocloud UIKit provides its own controls -->
                    <button class="video-action-button endcall" onclick="endCall()">Leave Call</button>
                </div>

                <button type="button" class="btn btn-kundali mb-2" id="kundaliButton">
                    <i class="fa-solid fa-file"></i> Kundali
                </button>
            </div>
        </div>

        <div class="app-main col-md-9 col-sm-12 order-sm-0">
            <!-- Agora Video Container -->
            <div class="video-call-wrapper shadow agora-container" id="agoraContainer">
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="local-player-name">{{ astroauthcheck()['name'] }}</a>
                    <div id="local-player" class="player"></div>
                    <div class="avatar-fallback" id="local-avatar">
                        {{ substr(astroauthcheck()['name'], 0, 1) }}
                    </div>
                </div>
                <div class="video-participant">
                    <a href="javascript:void(0);" class="name-tag" id="remote-player-name">
                        @php
                            $userName = 'Loading...';
                            if(isset($getUser) && isset($getUser['recordList'][0]['name'])) {
                                $userName = $getUser['recordList'][0]['name'];
                            }
                        @endphp
                        {{ $userName }}
                    </a>
                    <div id="remote-playerlist"></div>
                    <div class="avatar-fallback" id="remote-avatar">
                        @php
                            if(isset($getUser) && isset($getUser['recordList'][0]['name'])) {
                                echo substr($getUser['recordList'][0]['name'], 0, 1);
                            } else {
                                echo 'U';
                            }
                        @endphp
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
@endsection

@section('scripts')
<!-- <script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script> -->


<script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
<script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>

<script>
    // Global variables
    var currentProvider = "{{ $callrequest->call_method }}";
    var callDuration = parseInt({{ $callrequest->call_duration }});
    var remainingTime = callDuration;
    var timerInterval = null;
    var callEnded = false;
    var userJoined = false; // Track if user has actually joined
    var timerStarted = false; // Track if timer has started

    // Agora variables
    var agoraClient = null;
    var localAudioTrack = null;
    var localVideoTrack = null;
    var remoteUsers = {};

    // Zegocloud variables
    var zegoUIKit = null;
    var zegoJoined = false;

    $(document).ready(function() {
        console.log('Initializing astrologer call with provider:', currentProvider);

        // Set user name from API or default
        fetchUserName();

        // Initialize timer display (but don't start counting yet)
        updateTimerDisplay();

        initializeCall();
        setupKundaliButton();

        // Periodically fetch user name in case it's not available initially
        setInterval(function() {
            if (document.getElementById('remote-player-name').textContent === 'Loading...' ||
                document.getElementById('remote-player-name').textContent === 'User') {
                fetchUserName();
            }
        }, 5000);
    });

    // Function to fetch and display user name
    function fetchUserName() {
        $.ajax({
            url: '{{ route('front.callStatus', ['callId' => $callrequest->id]) }}',
            type: 'GET',
            success: function(response) {
                var userName = 'User';

                if (response.call) {
                    // Try different possible fields for user name
                    if (response.call.userName) {
                        userName = response.call.userName;
                    } else if (response.call.user && response.call.user.name) {
                        userName = response.call.user.name;
                    } else if (response.call.user && response.call.user.userName) {
                        userName = response.call.user.userName;
                    } else if (response.call.userName) {
                        userName = response.call.userName;
                    } else if (response.user && response.user.name) {
                        userName = response.user.name;
                    }
                }

                // Update display
                var nameElement = document.getElementById('remote-player-name');
                if (nameElement) {
                    nameElement.textContent = userName;
                }

                var avatarElement = document.getElementById('remote-avatar');
                if (avatarElement) {
                    avatarElement.textContent = userName.charAt(0).toUpperCase();
                }

                console.log('User name set to:', userName);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching user name:', error);
                // Keep default "User" if fetch fails
            }
        });
    }

    // Function to update timer display
    function updateTimerDisplay() {
        var minutes = Math.floor(remainingTime / 60);
        var seconds = remainingTime % 60;
        var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' +
                           (seconds < 10 ? '0' : '') + seconds;
        var timeElement = document.getElementById('remainingTime');
        if (timeElement) {
            timeElement.textContent = formattedTime;
        }
    }

    function initializeCall() {
        if (currentProvider === 'agora') {
            initializeAgora();
        } else if (currentProvider === 'zegocloud') {
            initializeZegocloudUIKit();
        } else {
            // Default to Agora
            currentProvider = 'agora';
            initializeAgora();
        }
    }

    // ========== AGORA IMPLEMENTATION ==========
    async function initializeAgora() {
        try {
            showProviderUI('agora');
            console.log('Initializing Agora...');

            const appID = document.getElementById('appid').value;
            const token = document.getElementById('token').value;
            const channel = document.getElementById('channel').value;
            const isVideoCall = "{{ $call_type }}" == "11";

            if (!appID || !token || !channel) {
                throw new Error('Missing Agora configuration');
            }

            // Create Agora client
            agoraClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

            // Event handlers
            agoraClient.on("user-published", async (user, mediaType) => {
                await agoraClient.subscribe(user, mediaType);
                console.log("Subscribe success:", mediaType);

                // Mark user as joined
                if (!userJoined) {
                    userJoined = true;
                    console.log("User has joined the call - starting timer now");

                    // Fetch user name again when user joins
                    fetchUserName();

                    // Start timer only when user joins
                    if (!timerStarted) {
                        fetchCallStatus();
                        timerStarted = true;
                    }
                }

                if (mediaType === "video") {
                    const remoteVideoTrack = user.videoTrack;
                    const playerContainer = document.getElementById("remote-playerlist");

                    // Clear any existing content
                    playerContainer.innerHTML = '';

                    // Hide avatar
                    document.getElementById('remote-avatar').style.display = 'none';

                    // Play video
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

            agoraClient.on("user-unpublished", (user, mediaType) => {
                console.log("User unpublished:", mediaType);
                if (mediaType === "video") {
                    document.getElementById('remote-avatar').style.display = 'flex';
                }
            });

            agoraClient.on("user-left", (user) => {
                console.log("User left:", user.uid);
                delete remoteUsers[user.uid];
                document.getElementById('remote-avatar').style.display = 'flex';

                // Automatically end call when user leaves
                console.log("User left the call, ending call automatically");
                setTimeout(function() {
                    if (!callEnded) {
                        endCall();
                    }
                }, 1000);
            });

            // Join channel
            await agoraClient.join(appID, channel, token, null);
            console.log("Joined Agora channel successfully");

            // Create and publish local tracks
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

            // Don't start timer here - wait for user to join
            // Timer will start in user-published event handler

        } catch (error) {
            console.error('Agora initialization failed:', error);
            showError('Failed to initialize Agora: ' + error.message);
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

    // ========== ZEGOCLOUD IMPLEMENTATION ==========
    async function initializeZegocloudUIKit() {
        try {
            showProviderUI('zegocloud');
            console.log('Initializing Zegocloud UIKit...');

            const appID = "{{ systemflag('zegoAppId') }}";
            const serverSecret = "{{ systemflag('zegoServerSecret') }}";
            const userID = "{{ $astrologerId }}";
            const userName = "{{astroauthcheck()['name']}}";
            const roomID = document.getElementById('channel').value;
            const isVideoCall = "{{ $call_type }}" == "11";

            if (!appID || !serverSecret || !roomID) {
                throw new Error('Missing Zegocloud configuration');
            }

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
                    // Timer will start when user joins (detected via onUserJoin or when user publishes)
                },

                onUserJoin: (users) => {
                    console.log('User joined Zegocloud room:', users);
                    if (!userJoined) {
                        userJoined = true;
                        console.log("User has joined the call via Zegocloud - starting timer now");

                        // Fetch user name when user joins
                        fetchUserName();

                        // Start timer when user joins
                        if (!timerStarted) {
                            fetchCallStatus();
                            timerStarted = true;
                        }
                    }
                },

                onLeaveRoom: () => {
                    console.log('Zegocloud UIKit: Left room');
                    if (zegoJoined) endCall();
                },

                onUserLeave: (users) => {
                    console.log('User left:', users);
                    userJoined = false;
                    setTimeout(() => {
                        if (zegoJoined && !callEnded) {
                            console.log('User left, ending call automatically...');
                            endCall();
                        }
                    }, 1000);
                },

                onError: (error) => {
                    console.error('Zegocloud UIKit Error:', error);
                    showError('Zegocloud error: ' + error.message);
                }
            };

            zegoUIKit.joinRoom(config);

        } catch (error) {
            console.error('Zegocloud UIKit initialization failed:', error);
            showError('Failed to initialize Zegocloud: ' + error.message);
        }
    }

    // ========== COMMON FUNCTIONS ==========
    function showProviderUI(provider) {
        // Hide all containers
        document.querySelectorAll('.agora-container, .zegocloud-container').forEach(el => {
            el.style.display = 'none';
        });

        // Show selected provider
        if (provider === 'zegocloud') {
            document.getElementById('zegocloudContainer').style.display = 'block';
            document.getElementById('zegocloudControls').style.display = 'block';
        } else if (provider === 'agora') {
            document.getElementById('agoraContainer').style.display = 'grid';
            document.getElementById('agoraControls').style.display = 'block';
        }
    }

    function showError(message) {
        console.error('Error:', message);
        toastr.error(message);
    }

    function fetchCallStatus() {
        $.ajax({
            url: '{{ route('front.callStatus', ['callId' => $callrequest->id]) }}',
            type: 'GET',
            success: function(response) {
                // Update user name if available
                var userName = 'User';
                if (response.call) {
                    if (response.call.userName) {
                        userName = response.call.userName;
                    } else if (response.call.user && response.call.user.name) {
                        userName = response.call.user.name;
                    } else if (response.call.user && response.call.user.userName) {
                        userName = response.call.user.userName;
                    }
                }

                var nameElement = document.getElementById('remote-player-name');
                if (nameElement) {
                    nameElement.textContent = userName;
                }

                var avatarElement = document.getElementById('remote-avatar');
                if (avatarElement) {
                    avatarElement.textContent = userName.charAt(0).toUpperCase();
                }

                // Start timer only if call is confirmed and user has joined
                if (response.call && response.call.callStatus === 'Confirmed' && userJoined) {
                    var updateTime = new Date(response.call.updated_at).getTime();
                    startTimer(updateTime);
                } else if (userJoined) {
                    // Start timer with default time if user has joined
                    var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
                    startTimer(updateTime);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching call status:', error);
                // Only start timer if user has joined
                if (userJoined) {
                    var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
                    startTimer(updateTime);
                }
            }
        });
    }

    function startTimer(updateTime) {
        // Prevent multiple timers
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        setupFirebaseListener();

        // If updateTime is not provided, use current time
        if (!updateTime) {
            updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
        }

        $.get("{{ route('front.getDateTime') }}", function(response) {
            var currentTime = new Date(response).getTime();
            var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
            remainingTime = callDuration - elapsedTime;

            if (remainingTime < 0) {
                remainingTime = 0;
            }

            // Update display immediately
            updateTimerDisplay();

            // Start interval timer
            timerInterval = setInterval(function() {
                remainingTime--;
                if (remainingTime < 0) {
                    remainingTime = 0;
                }
                updateTimerDisplay();

                if (remainingTime <= 0) {
                    if (timerInterval) {
                        clearInterval(timerInterval);
                        timerInterval = null;
                    }
                    endCall();
                }
            }, 1000);
        }).fail(function() {
            console.error("Error fetching server time, using local time");
            // Fallback to local time
            var currentTime = new Date().getTime();
            var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
            remainingTime = callDuration - elapsedTime;

            if (remainingTime < 0) {
                remainingTime = 0;
            }

            updateTimerDisplay();

            timerInterval = setInterval(function() {
                remainingTime--;
                if (remainingTime < 0) {
                    remainingTime = 0;
                }
                updateTimerDisplay();

                if (remainingTime <= 0) {
                    if (timerInterval) {
                        clearInterval(timerInterval);
                        timerInterval = null;
                    }
                    endCall();
                }
            }, 1000);
        });
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
                    var firebaseData = doc.data();
                    var newDuration = firebaseData.duration;
                    var previousDuration = callDuration;

                    if (newDuration && newDuration > previousDuration) {
                        callDuration = newDuration;
                        var additionalTime = callDuration - previousDuration;
                        remainingTime += additionalTime;
                        console.log("Added additional time from Firebase:", additionalTime);
                    }
                }
            }, (error) => {
                console.error("Firebase listener error:", error);
            });
    }

    async function endCall() {
        if (callEnded) return;
        callEnded = true;

        if (typeof timerInterval !== 'undefined') {
            clearInterval(timerInterval);
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
                console.log('Agora cleanup completed');
            } catch (error) {
                console.error('Error during Agora cleanup:', error);
            }
        }

        // Cleanup Zegocloud
        if (typeof zegoUIKit !== 'undefined' && zegoUIKit && zegoJoined) {
            try {
                zegoUIKit.leaveRoom();
                zegoUIKit = null;
                zegoJoined = false;
                console.log('Zegocloud UIKit cleanup completed');
            } catch (error) {
                console.error('Error during Zegocloud UIKit cleanup:', error);
            }
        }

        // Update status
        try {
            await $.ajax({
                url: "{{ route('api.addCallStatus') }}",
                type: 'POST',
                data: {
                    status: 'Online',
                    token: "{{ $token }}",
                    astrologerId: "{{ $astrologerId }}"
                }
            });
            console.log('Astrologer status updated to Online');
        } catch (error) {
            console.error('Error updating call status:', error);
        }

        toastr.success('Call ended successfully');

        setTimeout(() => {
            window.location.href = "{{ route('front.astrologerindex') }}";
        }, 2000);
    }

    window.addEventListener('beforeunload', function(e) {
        if (!callEnded) {
            endCall();
        }
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

   // Replace the setupKundaliButton function with this corrected version:

function setupKundaliButton() {
    $('#kundaliButton').click(function() {
        var userId = "{{ $userId }}";

        // Show loading text
        $('#kundaliContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3">Loading Kundali Report...</p></div>');

        // Open modal immediately to show loading
        $('#kundaliModal').modal('show');

        // Call the API
        $.ajax({
            url: "{{ url('/api/kundali/getKundaliReport') }}",
            type: "POST",
            data: {
                userId: userId
            },
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log('Kundali API Response:', response);

                // Check if response is valid
                if (!response || response.planet.status == 400 || response.planet.status == 402) {
                    $('#kundaliContent').html('<h3 class="text-center mt-5 mb-5">No Kundali Found</h3>');
                    return;
                }

                // Generate and populate modal content
                var html = generateKundaliReportHTML(response);
                $('#kundaliContent').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Kundali API Error:', error);
                $('#kundaliContent').html('<div class="alert alert-danger m-3">Error fetching Kundali report. Please try again.</div>');
            }
        });
    });
}

// Make sure generateKundaliReportHTML is accessible globally
function generateKundaliReportHTML(response) {
    var html = `
    <ul class="nav nav-tabs" id="kundaliTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab"
                aria-controls="basic" aria-selected="true">Basic Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="planetaryposition-tab" data-toggle="tab" href="#planetaryposition" role="tab"
                aria-controls="planetaryposition" aria-selected="false">Planetary Position</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="predictions-tab" data-toggle="tab" href="#predictions" role="tab"
                aria-controls="predictions" aria-selected="false">Predictions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="shodashvarga-tab" data-toggle="tab" href="#shodashvarga" role="tab"
                aria-controls="shodashvarga" aria-selected="false">Shodashvarga</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ashtakvarga-tab" data-toggle="tab" href="#ashtakvarga" role="tab"
                aria-controls="ashtakvarga" aria-selected="false">Ashtakvarga</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="mahadasha-tab" data-toggle="tab" href="#mahadasha" role="tab"
                aria-controls="mahadasha" aria-selected="false">Mahadasha</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="yogini-tab" data-toggle="tab" href="#yogini" role="tab"
                aria-controls="yogini" aria-selected="false">Yogini Dasha</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dosha-tab" data-toggle="tab" href="#dosha" role="tab"
                aria-controls="dosha" aria-selected="false">Dosha</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab"
                aria-controls="report" aria-selected="false">Report</a>
        </li>
    </ul>

    <div class="tab-content" id="kundaliTabContent">
        <!-- Basic Details Tab -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
            ${generateBasicDetailsHTML(response)}
        </div>

        <!-- Planetary Position Tab -->
        <div class="tab-pane fade" id="planetaryposition" role="tabpanel" aria-labelledby="planetaryposition-tab">
            ${generatePlanetaryPositionHTML(response)}
        </div>

        <!-- Predictions Tab -->
        <div class="tab-pane fade" id="predictions" role="tabpanel" aria-labelledby="predictions-tab">
            ${generatePredictionsHTML(response)}
        </div>

        <!-- Shodashvarga Tab -->
        <div class="tab-pane fade" id="shodashvarga" role="tabpanel" aria-labelledby="shodashvarga-tab">
            ${generateShodashvargaHTML(response)}
        </div>

        <!-- Ashtakvarga Tab -->
        <div class="tab-pane fade" id="ashtakvarga" role="tabpanel" aria-labelledby="ashtakvarga-tab">
            ${generateAshtakvargaHTML(response)}
        </div>

        <!-- Mahadasha Tab -->
        <div class="tab-pane fade" id="mahadasha" role="tabpanel" aria-labelledby="mahadasha-tab">
            ${generateMahadashaHTML(response)}
        </div>

        <!-- Yogini Dasha Tab -->
        <div class="tab-pane fade" id="yogini" role="tabpanel" aria-labelledby="yogini-tab">
            ${generateYoginiDashaHTML(response)}
        </div>

        <!-- Dosha Tab -->
        <div class="tab-pane fade" id="dosha" role="tabpanel" aria-labelledby="dosha-tab">
            ${generateDoshaHTML(response)}
        </div>

        <!-- Report Tab -->
        <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
            ${generateReportHTML(response)}
        </div>
    </div>`;

    return html;
}

// Helper functions remain the same as in your original code
function generateBasicDetailsHTML(response) {
    return `
    <div class="row py-3">
        <div class="col-sm-12 mt-4">
            <div class="table-responsive table-theme shadow-pink p-3">
                <table class="table table-bordered border-pink font-14 mb-0">
                    <tbody>
                        <tr><th class="cellhead"><b>Name</b></th><td>${response.recordList.name || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Birth Date</b></th><td>${response.recordList.birthDate || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Birth Time</b></th><td>${response.recordList.birthTime || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Birth Place</b></th><td>${response.recordList.birthPlace || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Latitude</b></th><td>${response.recordList.latitude || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Longitude</b></th><td>${response.recordList.longitude || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Timezone</b></th><td>${response.recordList.timezone || 'N/A'}</td></tr>
                        <tr><th class="cellhead"><b>Rasi</b></th><td>${response.planet.response.rasi || 'N/A'}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;
}

function generatePlanetaryPositionHTML(response) {
    if (response.planet.status === 400) {
        return `<p class="text-center py-5">No Record Found</p>`;
    }

    var filteredData = Object.keys(response.planet.response)
        .filter(key => !isNaN(key))
        .map(key => response.planet.response[key]);

    var rows = filteredData.map(planet => `
        <tr>
            <td>${planet.full_name || 'N/A'}</td>
            <td>${planet.is_combust ? 'C' : ''}</td>
            <td>${planet.retro ? 'R' : ''}</td>
            <td>${planet.zodiac || 'N/A'}</td>
            <td>${planet.local_degree || 'N/A'}</td>
            <td>${planet.global_degree || 'N/A'}</td>
            <td>${planet.nakshatra || 'N/A'}</td>
            <td>${planet.nakshatra_pada || 'N/A'}</td>
        </tr>
    `).join('');

    return `
        <div class="row py-3">
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="matchV_thead bg-pink color-red">
                            <tr>
                                <th class="cellhead">Planet</th>
                                <th class="cellhead">C</th>
                                <th class="cellhead">R</th>
                                <th class="cellhead">Rashi</th>
                                <th class="cellhead">Local Degree</th>
                                <th class="cellhead">Global Degree</th>
                                <th class="cellhead">Nakshatra</th>
                                <th class="cellhead">Pada</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
}

function generatePredictionsHTML(response) {
    if (response.personal.status === 400) {
        return `<p class="text-center py-5">No Record Found</p>`;
    }

    var predictions = response.personal.response.map((prediction, index) => {
        var houseNumber = index + 1;
        var houseWord = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh',
            'Eighth', 'Ninth', 'Tenth', 'Eleventh', 'Twelfth'
        ][houseNumber - 1] || houseNumber;

        return `
        <div class="panel panel-default mb-3">
            <div class="panel-heading">
                <h3 class="panel-title mb-0">
                    <a class="accordion-toggle font-weight-semi d-block py-2 colorblack font-16" data-toggle="collapse" data-parent="#accordion" href="#accAbount_${index}">
                        ${houseWord} House
                    </a>
                </h3>
            </div>
            <div id="accAbount_${index}" class="panel-collapse collapse ${index === 0 ? 'show' : ''}" data-parent="#accordion">
                <div class="panel-body px-0 px-md-3 py-4 border-top">
                    <p>${prediction.personalised_prediction}</p>
                </div>
            </div>
        </div>`;
    }).join('');

    return `
        <div class="row py-3">
            <div class="col-12">
                <h2 class="font-24 p-3">Predictions</h2>
            </div>
            <div class="col-12">
                <div class="panel-group my-1 p-3" id="accordion">${predictions}</div>
            </div>
        </div>`;
}

function generateShodashvargaHTML(response) {
    if (!response.charts) {
        return `<p class="text-center py-5">No Record Found</p>`;
    }

    var chartNames = {
        'D1': 'Rasi', 'D2': 'Hora', 'D3': 'Drekkana', 'D4': 'Chaturthamsa',
        'D5': 'Panchamamsa', 'D6': 'Shastamsa', 'D7': 'Saptamsa', 'D8': 'Astamsa',
        'D9': 'Navamsa', 'D10': 'Dasamsa', 'D11': 'Rudramsa', 'D12': 'Dwadasamsa',
        'D16': 'Shodasamsa', 'D20': 'Vimsamsa', 'D24': 'Siddhamsa', 'D27': 'Nakshatramsa',
        'D30': 'Trimsamsa', 'D40': 'Khavedamsa', 'D45': 'Akshavedamsa', 'D60': 'Shastyamsa',
        'chalit': 'Chalit', 'sun': 'Sun', 'moon': 'Moon', 'kp_chalit': 'Kp Chalit'
    };

    var charts = Object.keys(response.charts).map(key => `
        <div class="col-md-4 col-sm-6 col-12 mt-3">
            <p class="font-16 mb-1"><strong>${chartNames[key] || key}</strong></p>
            <div class="svg-container">${response.charts[key]}</div>
        </div>
    `).join('');

    return `
        <div class="py-3">
            <h2 class="p-3">Horoscope Chart</h2>
            <div class="row p-3">${charts}</div>
        </div>`;
}

function generateAshtakvargaHTML(response) {
    if (response.ashtakvarga.status === 400) {
        return `<p class="text-center py-5">No Record Found</p>`;
    }

    var ashtakvargaRows = response.ashtakvarga.response.ashtakvarga_order
        .filter(name => name !== 'Ascendant')
        .map((name, index) => `
        <tr>
            <td>${name}</td>
            ${response.ashtakvarga.response.ashtakvarga_points[index].map(point => `<td>${point}</td>`).join('')}
        </tr>
    `).join('');

    var binnashtakvargaRows = Array.from({ length: 12 }, (_, i) => `
        <tr>
            ${Object.values(response.binnashtakvarga.response).map(points => `<td>${points[i]}</td>`).join('')}
        </tr>
    `).join('');

    return `
        <div class="row py-3">
            <div class="col-12">
                <h2 class="font-24 p-3">Ashtakvarga</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">&nbsp;</th>
                                <th>Ar</th><th>Ta</th><th>Ge</th><th>Ca</th><th>Le</th><th>Vi</th>
                                <th>Li</th><th>Sc</th><th>Sa</th><th>Ca</th><th>Aq</th><th>Pi</th>
                            </tr>
                        </thead>
                        <tbody>${ashtakvargaRows}</tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <h2 class="font-24 p-3">Binnashtakvarga</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                ${Object.keys(response.binnashtakvarga.response).map(name => `<th>${name}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>${binnashtakvargaRows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
}

function generateMahadashaHTML(response) {
    if (response.mahaDasha.status === 400) {
        return `<p class="text-center py-5">No Record Found</p>`;
    }

    var mahadashaRows = response.mahaDasha.response.mahadasha.map((dasha, index) => `
        <tr>
            <td>${dasha}</td>
            <td>${response.mahaDasha.response.mahadasha_order[index]}</td>
        </tr>
    `).join('');

    var predictions = response.mahaDashaPrediction.response.dashas.map(prediction => `
        <div class="prediction-block mb-4 p-3 border">
            <h4 class="font-18">${prediction.dasha} (${prediction.dasha_start_year} - ${prediction.dasha_end_year})</h4>
            <p class="font-14"><strong>Prediction:</strong> ${prediction.prediction}</p>
            <p class="font-14"><strong>Planet in Zodiac:</strong> ${prediction.planet_in_zodiac}</p>
        </div>
    `).join('');

    return `
        <div class="row py-3">
            <div class="col-12">
                <h2 class="font-24 p-3">Mahadasha</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">MahaDasha</th>
                                <th class="cellhead">MahaDasha Order</th>
                            </tr>
                        </thead>
                        <tbody>${mahadashaRows}</tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <h3 class="font-20 mb-2 p-3">Mahadasha Predictions</h3>
            </div>
            <div class="col-12 px-3">${predictions}</div>
        </div>`;
}

function generateYoginiDashaHTML(response) {
    if (response.yoginiDashaMain.status === 400) {
        return `<p class="text-center py-5">No Record Found</p>`;
    }

    var rows = response.yoginiDashaMain.response.dasha_list.map((dasha, index) => `
        <tr>
            <td>${dasha}</td>
            <td>${response.yoginiDashaMain.response.dasha_lord_list[index]}</td>
            <td>${response.yoginiDashaMain.response.dasha_end_dates[index]}</td>
        </tr>
    `).join('');

    return `
        <div class="row py-3">
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">Dasha</th>
                                <th class="cellhead">Dasha Lord</th>
                                <th class="cellhead">End Date</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
}

function generateDoshaHTML(response) {
    var doshas = ['mangalDosh', 'kaalsarpDosh', 'manglikDosh', 'pitraDosh', 'papasamayaDosh'];
    var doshaHTML = doshas.map(dosha => {
        if (!response[dosha] || response[dosha].status === 400) {
            return `<div class="col-12 mb-3"><p class="text-center">No Record Found for ${dosha}</p></div>`;
        }

        return `
        <div class="col-12 mb-3">
            <div class="table-responsive table-theme shadow-pink p-3">
                <table class="table table-bordered border-pink font-14 mb-0">
                    <thead class="font-13">
                        <tr class="bg-pink color-red font-weight-normal">
                            <th class="cellhead" colspan="2">${dosha.replace(/([A-Z])/g, ' $1').trim()}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <p>${response[dosha].response.bot_response}</p>
                                ${response[dosha].response.remedies ? `
                                    <h5 class="font-16 mt-3">Remedies</h5>
                                    <div class="dosha-remedies">
                                        ${response[dosha].response.remedies.map(remedy => `<p>• ${remedy}</p>`).join('')}
                                    </div>
                                ` : ''}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>`;
    }).join('');

    return `
        <div class="row py-3">
            <div class="col-12">
                <h2 class="font-24 p-3">Doshas</h2>
            </div>
            ${doshaHTML}
        </div>`;
}

function generateReportHTML(response) {
    var ascendantReport = response.ascendantReport && response.ascendantReport.status === 200 ? `
        <div class="col-12">
            <h2 class="font-24 p-3">Ascendant Report</h2>
            <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                <table class="table table-bordered border-pink font-14 mb-0">
                    <thead class="font-13">
                        <tr class="bg-pink color-red font-weight-normal">
                            <th class="cellhead">Aspect</th>
                            <th class="cellhead">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${response.ascendantReport.response.map(ascendant => `
                            <tr><td><strong>Ascendant</strong></td><td>${ascendant.ascendant}</td></tr>
                            <tr><td><strong>Ascendant Lord</strong></td><td>${ascendant.ascendant_lord}</td></tr>
                            <tr><td><strong>Ascendant Lord Location</strong></td><td>${ascendant.ascendant_lord_location} (${ascendant.ascendant_lord_house_location}th house)</td></tr>
                            <tr><td><strong>General Prediction</strong></td><td>${ascendant.general_prediction}</td></tr>
                            <tr><td><strong>Personalized Prediction</strong></td><td>${ascendant.personalised_prediction}</td></tr>
                            <tr><td><strong>Verbal Location</strong></td><td>${ascendant.verbal_location}</td></tr>
                            <tr><td><strong>Ascendant Lord Strength</strong></td><td>${ascendant.ascendant_lord_strength}</td></tr>
                            <tr><td><strong>Symbol</strong></td><td>${ascendant.symbol}</td></tr>
                            <tr><td><strong>Zodiac Characteristics</strong></td><td>${ascendant.zodiac_characteristics}</td></tr>
                            <tr><td><strong>Lucky Gem</strong></td><td>${ascendant.lucky_gem}</td></tr>
                            <tr><td><strong>Day for Fasting</strong></td><td>${ascendant.day_for_fasting}</td></tr>
                            <tr><td><strong>Gayatri Mantra</strong></td><td>${ascendant.gayatri_mantra}</td></tr>
                            <tr><td><strong>Flagship Qualities</strong></td><td>${ascendant.flagship_qualities}</td></tr>
                            <tr><td><strong>Spirituality Advice</strong></td><td>${ascendant.spirituality_advice}</td></tr>
                            <tr><td><strong>Good Qualities</strong></td><td>${ascendant.good_qualities}</td></tr>
                            <tr><td><strong>Bad Qualities</strong></td><td>${ascendant.bad_qualities}</td></tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    ` : `<div class="col-12"><p class="text-center py-3">No Ascendant Record Found</p></div>`;

    var planetReport = response.planetReport ? Object.keys(response.planetReport).map(planet => {
        if (response.planetReport[planet].status === 200) {
            return response.planetReport[planet].response.map(planetDetails => `
                <div class="col-12 mb-3">
                    <h3 class="font-20 p-3">${planet} Report</h3>
                    <div class="table-responsive table-theme shadow-pink p-3">
                        <table class="table table-bordered border-pink font-14 mb-0">
                            <tbody>
                                <tr><td><strong>Planet Location</strong></td><td>${planetDetails.planet_location} (${planetDetails.planet_native_location}th house)</td></tr>
                                <tr><td><strong>Planet Zodiac</strong></td><td>${planetDetails.planet_zodiac}</td></tr>
                                <tr><td><strong>Zodiac Lord</strong></td><td>${planetDetails.zodiac_lord}</td></tr>
                                <tr><td><strong>Zodiac Lord Location</strong></td><td>${planetDetails.zodiac_lord_location} (${planetDetails.zodiac_lord_house_location}th house)</td></tr>
                                <tr><td><strong>General Prediction</strong></td><td>${planetDetails.general_prediction}</td></tr>
                                <tr><td><strong>Planet Definitions</strong></td><td>${planetDetails.planet_definitions}</td></tr>
                                <tr><td><strong>Gayatri Mantra</strong></td><td>${planetDetails.gayatri_mantra}</td></tr>
                                <tr><td><strong>Qualities Long</strong></td><td>${planetDetails.qualities_long}</td></tr>
                                <tr><td><strong>Qualities Short</strong></td><td>${planetDetails.qualities_short}</td></tr>
                                <tr><td><strong>Affliction</strong></td><td>${planetDetails.affliction}</td></tr>
                                <tr><td><strong>Personalized Prediction</strong></td><td>${planetDetails.personalised_prediction || 'N/A'}</td></tr>
                                <tr><td><strong>Verbal Location</strong></td><td>${planetDetails.verbal_location}</td></tr>
                                <tr><td><strong>Planet Zodiac Prediction</strong></td><td>${planetDetails.planet_zodiac_prediction}</td></tr>
                                <tr><td><strong>Positive Keywords</strong></td><td>${planetDetails.character_keywords_positive.join(', ')}</td></tr>
                                <tr><td><strong>Negative Keywords</strong></td><td>${planetDetails.character_keywords_negative.join(', ')}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `).join('');
        } else {
            return `<div class="col-12"><p class="text-center py-3">No ${planet} Record Found</p></div>`;
        }
    }).join('') : '<div class="col-12"><p class="text-center py-3">No Planet Reports Found</p></div>';

    return `
        <div class="row py-3">
            ${ascendantReport}
            <div class="col-12 mt-4">
                <h2 class="font-24 p-3">Planet Reports</h2>
                ${planetReport}
            </div>
        </div>`;
}
    </script>
@endsection
