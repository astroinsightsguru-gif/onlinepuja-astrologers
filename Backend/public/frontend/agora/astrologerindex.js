    // create Agora client
    var client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

    var localTracks = {
    videoTrack: null,
    audioTrack: null
    };
    var remoteUsers = {};
    // Agora client options
    var options = {
    appid: null,
    channel: null,
    uid: null,
    token: null
    };

    // the demo can auto join channel with params in url
    $(() => {
    var urlParams = new URL(location.href).searchParams;
    options.appid = urlParams.get("appid");
    options.channel = urlParams.get("channel");
    options.token = urlParams.get("token");
    if (options.appid && options.channel) {
        $("#appid").val(options.appid);
        $("#token").val(options.token);
        $("#channel").val(options.channel);
        $("#join-form").submit();
    }
    })



    $(document).ready(async function(){
        try {
            options.appid = $("#appid").val();
            options.token = $("#token").val();
            options.channel = $("#channel").val();
            await join();
            if(options.token) {
            $("#success-alert-with-token").css("display", "block");
            } else {
            $("#success-alert a").attr("href", `index.html?appid=${options.appid}&channel=${options.channel}&token=${options.token}`);
            $("#success-alert").css("display", "block");
            }
        } catch (error) {
            console.error(error);
        } finally {
            $("#leave").attr("disabled", false);
        }
    });



    $("#leave").click(function (e) {
        leave();
    })


    const call_type = new URLSearchParams(window.location.search).get('call_type');
console.log(call_type);

async function join() {
    // add event listener to play remote tracks when remote user publishes.
    client.on("user-published", handleUserPublished);
    client.on("user-unpublished", handleUserUnpublished);
    // client.on("user-left", endastroCall);

    // join a channel and create local tracks, using Promise.all to run them concurrently
    const tracks = [AgoraRTC.createMicrophoneAudioTrack()];
    if (call_type != 10) {
        tracks.push(AgoraRTC.createCameraVideoTrack());
    }

    [options.uid, localTracks.audioTrack, localTracks.videoTrack] = await Promise.all([
        // join the channel
        client.join(options.appid, options.channel, options.token || null),
        // create local tracks
        ...tracks
    ]);

    // play local video track if it exists
    if (localTracks.videoTrack) {
        localTracks.videoTrack.play("local-player");
        // $("#local-player-name").text(`localVideo(${options.uid})`);
    }

    // publish local tracks to channel
    await client.publish(Object.values(localTracks));
    console.log("publish success");
}

join();



    async function leave() {
    for (trackName in localTracks) {
        var track = localTracks[trackName];
        if(track) {
        track.stop();
        track.close();
        localTracks[trackName] = undefined;
        }
    }

    // remove remote users and player views
    remoteUsers = {};
    $("#remote-playerlist").html("");

    // leave the channel
    await client.leave();

    $("#local-player-name").text("");
    $("#remote-player-name").text("");
    $("#join").attr("disabled", false);
    $("#leave").attr("disabled", true);
    console.log("client leaves channel success");

    }



    // Toggle Mic
    function toggleMic() {
        if ($("#mic-icon i").hasClass('fa-microphone')) {
        localTracks.audioTrack.setEnabled(false).then(() => {
            console.log("Audio Muted.");
            $("#mic-icon i").removeClass('fa-microphone').addClass('fa-microphone-slash');
        }).catch(err => {
            console.error("Failed to mute audio:", err);
        });
        } else {
        localTracks.audioTrack.setEnabled(true).then(() => {
            console.log("Audio Unmuted.");
            $("#mic-icon i").removeClass('fa-microphone-slash').addClass('fa-microphone');
        }).catch(err => {
            console.error("Failed to unmute audio:", err);
        });
        }
    }

    // Toggle Video
    function toggleVideo() {
        if ($("#video-icon i").hasClass('fa-video')) {
        localTracks.videoTrack.setEnabled(false).then(() => {
            console.log("Video Muted.");
            $("#video-icon i").removeClass('fa-video').addClass('fa-video-slash');
        }).catch(err => {
            console.error("Failed to mute video:", err);
        });
        } else {
        localTracks.videoTrack.setEnabled(true).then(() => {
            console.log("Video Unmuted.");
            $("#video-icon i").removeClass('fa-video-slash').addClass('fa-video');
        }).catch(err => {
            console.error("Failed to unmute video:", err);
        });
        }
    }


    async function subscribe(user, mediaType) {
    const uid = user.uid;
    // subscribe to a remote user
    await client.subscribe(user, mediaType);
    console.log("subscribe success");
    if (mediaType === 'video') {
        // $("#remote-player-name").text(`remoteUser(${uid})`);
        const player = $(`
        <div id="player-wrapper-${uid}">
            <div id="player-${uid}" class="player"></div>
        </div>
        `);
        $("#remote-playerlist").append(player);
        user.videoTrack.play(`player-${uid}`);
    }
    if (mediaType === 'audio') {
        user.audioTrack.play();
    }
    }

    function handleUserPublished(user, mediaType) {
    const id = user.uid;
    remoteUsers[id] = user;
    subscribe(user, mediaType);
    }

    function handleUserUnpublished(user) {

    const id = user.uid;
    delete remoteUsers[id];
    $(`#player-wrapper-${id}`).remove();

    }

    // client.on("user-left", endastroCall);
