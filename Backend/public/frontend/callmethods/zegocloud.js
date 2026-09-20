<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="loading" class="loading">
    <h2>🔄 Connecting to call...</h2>
    <div class="spinner"></div>
</div>

<div id="errorBox" class="error-box">
    <h3>❌ Connection Failed</h3>
    <p id="errorText"></p>
    <button onclick="goBack()">Go Back</button>
</div>

<div id="root"></div>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        background: #1a1a2e;
        font-family: Arial, sans-serif;
        overflow: hidden;
    }
    .loading {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        color: white;
        background: #1a1a2e;
    }
    .spinner {
        border: 4px solid rgba(255,255,255,0.3);
        border-top: 4px solid white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .error-box {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        z-index: 10000;
    }
    .error-box h3 {
        color: #dc3545;
    }
    .error-box button {
        background: #667eea;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        margin-top: 15px;
        cursor: pointer;
    }
</style>

<script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>

<script>
/**
 * ZegoCloud integration (Agora-style)
 */
(async function() {

    // ✅ Step 1: Extract parameters from URL
    const urlParams = new URLSearchParams(window.location.search);
    const callId = urlParams.get("callId");
    const callType = urlParams.get("call_type"); // 0=video, 10=audio
    const callMethod = urlParams.get("call_method");
    const userId = urlParams.get("userId");
    const userType = urlParams.get("userType");
    const token = urlParams.get("token");
    const channelName = urlParams.get("channel") || `room_${callId}`;

    const appID = {{ env('ZEGO_APP_ID') }};
    const serverSecret = "{{ env('ZEGO_SERVER_SECRET') }}";

    let joined = false;
    let callStartTime = Date.now();
    let zegoInstance = null;

    console.log("Zego Params:", { callId, callType, callMethod, userId, userType, channelName });

    // ✅ Step 2: Initialize SDK safely
    async function waitForSDK() {
        return new Promise((resolve, reject) => {
            let attempts = 0;
            const check = setInterval(() => {
                if (typeof ZegoUIKitPrebuilt !== "undefined") {
                    clearInterval(check);
                    resolve();
                } else if (++attempts > 50) {
                    clearInterval(check);
                    reject("SDK failed to load");
                }
            }, 100);
        });
    }

    // ✅ Step 3: Create token
    function generateZegoToken() {
        return ZegoUIKitPrebuilt.generateKitTokenForTest(
            parseInt(appID),
            serverSecret,
            channelName.toString(),
            userId.toString(),
            userType + "_" + userId
        );
    }

    // ✅ Step 4: Join Room (like join() in Agora)
    async function joinZego() {
        try {
            await waitForSDK();

            const kitToken = generateZegoToken();
            zegoInstance = ZegoUIKitPrebuilt.create(kitToken);

            const isVideoCall = callType != 10; // 10 = audio call
            const config = {
                container: document.querySelector("#root"),
                scenario: {
                    mode: isVideoCall
                        ? ZegoUIKitPrebuilt.VideoCall
                        : ZegoUIKitPrebuilt.VoiceCall
                },
                showPreJoinView: false,
                turnOnCameraWhenJoining: isVideoCall,
                turnOnMicrophoneWhenJoining: true,
                useFrontFacingCamera: true,
                showMyCameraToggleButton: isVideoCall,
                showMyMicrophoneToggleButton: true,
                showLeavingView: false,
                showTextChat: false,
                showUserList: false,
                showRoomTimer: true,
                maxUsers: 2,
                onJoinRoom: () => {
                    joined = true;
                    document.getElementById("loading").style.display = "none";
                    callStartTime = Date.now();
                    console.log("✅ Joined room successfully");
                },
                onLeaveRoom: () => {
                    console.log("🚪 Left room");
                    endCall();
                },
                onUserLeave: () => {
                    console.log("👋 Remote user left");
                    setTimeout(endCall, 1000);
                },
                onError: (error) => {
                    console.error("Zego error:", error);
                    showError("Connection error: " + (error.message || "Unknown"));
                }
            };

            zegoInstance.joinRoom(config);
            console.log("Joining room:", channelName);

        } catch (err) {
            console.error("Zego join error:", err);
            showError(err.message);
        }
    }

    // ✅ Step 5: Leave Room (like leave() in Agora)
    async function leaveZego() {
        if (zegoInstance) {
            try {
                await zegoInstance.leaveRoom();
            } catch (e) {
                console.warn("Leave error:", e);
            }
        }
        console.log("✅ Left room successfully");
        endCall();
    }

    // ✅ Step 6: End Call (save duration & redirect)
    async function endCall() {
        const duration = Math.ceil((Date.now() - callStartTime) / 60000);
        try {
            await fetch("/api/end-call", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    request_id: callId,
                    duration: duration
                })
            });
        } catch (e) {
            console.error("End call API error:", e);
        }
        goBack();
    }

    // ✅ Step 7: Redirect based on user type
    function goBack() {
        if (userType === "user") {
            window.location.href = "/user-call";
        } else {
            window.location.href = "/astrologer-requests";
        }
    }

    // ✅ Step 8: Show Error
    function showError(message) {
        document.getElementById("loading").style.display = "none";
        document.getElementById("errorBox").style.display = "block";
        document.getElementById("errorText").textContent = message;
    }

    // ✅ Step 9: Auto-join on load (like Agora)
    await joinZego();

    // ✅ Step 10: Leave on tab close
    window.addEventListener("beforeunload", (e) => {
        if (joined) {
            e.preventDefault();
            e.returnValue = "";
            endCall();
        }
    });

})();
</script>
