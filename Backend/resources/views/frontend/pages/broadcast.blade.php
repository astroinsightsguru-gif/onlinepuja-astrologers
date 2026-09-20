@extends('frontend.layout.master')
@section('content')

    <style>
     * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body {
          background: #313131;
        }
        .large-img:hover,
        .medium-img:hover {
          cursor: pointer;
        }
        /* media queries for large screen */
        @media screen and (min-width: 1199px) {
          .main-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
          }
        }
        .presenting-container {
          background: #535353;
          border-radius: 10px;
          margin-top: 1rem;
          padding: 4px;
        }
        .presenting {
          display: flex;
          column-gap: 0.5rem;
          align-items: center;
        }
        .presenting h3 {
          color: #ffffff;
          font-size: 1.3rem;
        }
        .smallImg {
          width: 30px;
          height: 30px;
          border-radius: 50%;
        }
        /* content css */
        .content-container {
          margin-top: 28px;
        }
        .large-img {
          width: 100%;
          height: 100%;
          border-radius: 10px;
        }
        /* side content */
        .side-content {
          position: relative;
        }
        .medium-img {
          width: 100%;
          height: 100%;
        }
        .side-content span {
          color: #ffffff;
          font-family: "Poppins";
          font-size: 1.15rem;
          position: absolute;
          z-index: 1;
          bottom: 0.2rem;
          left: 0.3rem;
        }

        /* footer content */
        .footer-content h2 {
          color: #ffffff;
          font-size: 1rem;
          font-family: "poppins";
        }
        .middle-icons span {
          background: #535353;
          display: inline-block;
          width: 38px;
          height: 38px;
          border-radius: 50%;
          text-align: center;
        }
        /*.middle-icons span:last-child {*/
        /*  background: #ff4343;*/
        /*  border-radius: 40px;*/
        /*  width: 62px;*/
        /*  height: 36px;*/
        /*}*/
        .middle-icons span i,
        .end-icons span i {
          color: #ffffff;
          margin-top: 10px;
        }
        .middle-icons span:hover,
        .end-icons span:hover {
          cursor: pointer;
        }
        .end-icons span {
          display: inline-block;
          width: 34px;
          height: 34px;
        }
        .middle-icons span i:hover,
        .end-icons span i:hover {
          scale: 1.5;
          transition: 1s;
        }
        /* blink text animation */
        .blink-soft {
          margin-bottom: 0 !important;
          
        }
   
        /* media queries for mobile and tablet */
        @media screen and (max-width: 1024px) {
          .content-container .row .col-lg-6:nth-child(2) {
            margin-top: 28px;
          }
          .side-content span {
            font-size: 0.75rem;
          }
          .footer-content {
            flex-direction: column;
            justify-content: center;
            align-items: center;
            row-gap: 1rem;
          }
        }
        #local-stream
        {
            /*min-height: 420px;*/
            height: 120px; /* Adjust height as needed */
            border: 2px solid #ffd700e8;
            border-radius: 8px;
            overflow: hidden;
            max-width: 40%;
            
        }
        .video-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        .stream {
          
            height: 120px; /* Adjust height as needed */
            border: 2px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }
        .controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px; /* Space between buttons */
            margin: 20px 0;
        }
        .timer {
            text-align: center;
            font-size: 1.5rem;
            margin: 10px 0;
        }
        .all-video-view {
            border: 1px solid #65a9fd;
            border-radius: 5px;
        }
        .middle-icons 
        {
          gap: 10px;
           display: flex;
        }
        div#astro-stream
        { 
            height: 420px;
            overflow: auto;
        }
        div#astro-stream .stream
        { 
            height: 420px;
            overflow: auto;
        }
        
        
           @media (max-width: 480px) {
        .slide {
            height: 100% !important;
        }
    }

    .footer {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .footer i {
        padding: 5px;
        border-radius: 15px;
    }

    .read {
        border: none;
        background: #ffd700;
        color: #0e0c0c;
        padding: 5px 8px;
        border-radius: 15px;
    }

    html {
        scroll-behavior: smooth;
    }

    #LongDescription {
        position: sticky;
        top: 68px;
        background: #fff;
        z-index: 2;

    }

    .slide {
        height: 456px !important;

    }


    .scrollable-container::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-container::-webkit-scrollbar-thumb {
        background-color: #f68d1c;
        border-radius: 10px;
    }

    .scrollable-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* For Firefox */
    .scrollable-container {
        scrollbar-width: thin;
        scrollbar-color: #f68d1c #f1f1f1;
    }
    .card {
    margin: 10px !important;
    padding: 0px 10px !important;

}

.astro-large-img .large-img {
    border: 2px solid #ffd700e8 !important;
} 
.carousel-inner img {
    height: 455px !important;
}
    </style>
    
    
    <div class="container mt-5 puja-details">
    <div class="product-detail bg-white py-4 mb-5">
        
            <div class="row py-4">
                <div class="col-12 col-md-7 d-flex align-items-center">
                    @if(!empty($puja->puja_images))
                    <div id="productCarousel" class="carousel slide product-large-image position-relative"
                        data-ride="carousel">
                        <div class="carousel-inner">
                            
                            @foreach ($puja->puja_images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img class="rounded-full cursor-pointer" src="{{ Str::startsWith($image, ['http://','https://']) ? $image : '/' . $image }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $image }}')" />
                                </div>
                            @endforeach
                           
                        </div>
                        <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    @else
                    <div class="carousel slide product-large-image position-relative">
                        <img class="border-30 w-100" id="PujaImg0" src="{{ asset('public/frontend/homeimage/360.png') }}"
                            alt="Puja Image 0">
                    </div>
                    @endif
                </div>
                <div class="col-12 col-md-5 px-4 mt-3 mt-md-0">
                    <div>
                        <span class="font-weight-semi-bold border-bottom border-gray">{{ $puja->category->name ?? '--' }}</span>
                    </div>
                    <div class="mt-3">
                        <span class=" puja-title font-weight-bold font-26">{{ $puja->puja_title }}</span>
                    </div>
                    <div class="mt-3">
                        <span
                            class="mt-2 puja-subTitle text-secondary font-20 font-weight-semi-bold">{{ $puja->puja_subtitle ?? '---' }}</span>
                    </div>
                    <div class="mt-3 d-flex align-items-start">
                        <i class="fa-solid fa-place-of-worship me-2 mr-2"></i>
                        <div>
                            <span class="d-block" style="font-size: 15px;">{{ $puja->puja_place }}</span>
                        </div>
                    </div>
                    <?php
                         $now = \Carbon\Carbon::now();
                    // Assume these are Carbon date instances
                        $startDatetime = \Carbon\Carbon::parse($puja->puja_start_datetime);
                        $endDatetime = \Carbon\Carbon::parse($puja->puja_end_datetime);

                        // Format the display date and time
                        $startDateDisplay = $startDatetime->format('j M, D');
                        $endDateDisplay = $endDatetime->format('j M, D');
                        $startTimeDisplay = $startDatetime->format('H:i');
                        $endTimeDisplay = $endDatetime->format('H:i');
                        $sameDate = $startDatetime->isSameDay($endDatetime);
                        $duration = $now->diffInSeconds($endDatetime);

                        // New
                        $now2 = \Carbon\Carbon::now();
                        $startDatetime2 = \Carbon\Carbon::parse($puja->puja_start_datetime);

                        // How many seconds have passed since puja started
                        $duration2 = $startDatetime2->diffInSeconds($now2, false); // false means allow negative values
                        if ($duration2 < 0) {
                            $duration2 = 0; // If puja not yet started, show 00:00:00
                        }
                        
                     ?>

                      


                    <div class="mt-3 d-flex align-items-start">
                    <i class="fa fa-calendar me-2 mr-2" aria-hidden="true"></i>
                        <div >
                            <span class="d-block" style="font-size: 15px;">{{$startDateDisplay.' '. $startTimeDisplay }}</span>
                        </div>
                    </div>

                    <div class="footer mt-5 mb-5">
                      <a href="javascript:void(0);"  id="startBroadcast" class="read w-100"><span class="justify-content-center d-flex">Join Puja<i class="fa-solid fa-arrow-right ml-1"></i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-container puja-broadcast-live d-none">
      <!-- header -->
      <div class="container presenting-container">
        <div class="presenting">
          <h3 class="blink-soft ml-3">{{ $puja->puja_title }}</h3>
          <h2 id="timer" class="timer ml-auto mr-3" style="color:#c3c4c5 !important;">Started  00:00:00</h2>
          @if ($roomuid === 'astrologer')
                <button id="endPujaBtn" class="btn btn-danger btn-sm mr-3">End Puja</button>
            @endif

        </div>
      </div>
      <!-- content -->
      <div class="container content-container">
        <div class="row">
          <div class="col-lg-6 col-12" id="astro-stream">
          </div>
          <div class="col-lg-6 col-12 row" id="streams" >
              <div class="col-lg-4 col-12 p-0 ml-2" id="local-stream">
              </div>
          </div>
        </div>
      </div>
      <!-- footer -->
      <div class="container mt-5">
        <div class="footer-content d-flex justify-content-center mb-5">
          <div class="middle-icons">
              <!--<button id="startBroadcast" class="btn btn-success">Join</button>-->
            <span id="toggleMic">
              <i class="fa fa-microphone"  aria-hidden="true"></i>
            </span>
            <span id="toggleCamera">
              <i class="fa fa-video-camera"  aria-hidden="true"></i>
            </span>
            <span id="flipCamera">
              <i class="fa fa-refresh" aria-hidden="true"></i>
            </span>
            <!--<span>-->
            <!--  <i class="fa fa-phone" onclick="endBroadcast()" aria-hidden="true"></i>-->
            <!--</span>-->
          </div>
        </div>
      </div>
    </div>
    @endsection
    @section('scripts')
    <script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>
        <script>
            const roomId = "{{ $roomId }}"; // Get roomId from Laravel
            const APP_ID = "{{$agoraAppIdValue->value}}"; // Replace with your Agora App ID
            const token = null; // Replace with your token if needed
    
            // Create an Agora client
            const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
    
            let microphoneTrack; // To hold the microphone track
            let cameraTrack; // To hold the camera track
            let timer; // To hold the timer interval
            let duration = 3600; // Total duration in seconds (1 hour)
            let isMicMuted = false; 
            let isCameraMuted = false;
    
            client.on("user-published", async (user, mediaType) => {
                
                console.log(user,'userrrrrrrrrrrrrrrrrr');
                // Subscribe to the remote user
                await client.subscribe(user, mediaType);
            
                // Create or find the remote user's container
                let remoteContainer = document.querySelector(`div[data-uid="${user.uid}"]`);
                if (!remoteContainer) {
                    
                    if(user.uid=='astrologer')
                    {
                        // $('#astro-stream').attr("data-uid", user.uid);
                        // $('#astro-stream').html();
                        
                         // Create a container for the remote stream if it doesn't exist
                        remoteContainer = $('#astro-stream');
                        // remoteContainer.className = "col-4 px-1 mb-1";
                        // remoteContainer.attr("data-uid", user.uid); // Set a data attribute for the user's UID
                
                        remoteContainer.html(`
                            <div class="side-content" data-uid="${user.uid}">
                                <div class="stream"></div>
                                <div class="blank-screen d-none" style="height:420px; border: 2px solid #ffd700e8; border-radius: 8px; overflow: hidden;"></div>
                            </div>`);
                    }
                    else
                    {
                        // Create a container for the remote stream if it doesn't exist
                        remoteContainer = document.createElement("div");
                        remoteContainer.className = "col-4 px-1 mb-1";
                        remoteContainer.setAttribute("data-uid", user.uid); // Set a data attribute for the user's UID
                
                        remoteContainer.innerHTML = `
                            <div class="side-content">
                                <div class="stream"></div>
                                <div class="blank-screen d-none" style="height:120px; border: 2px solid #ccc; border-radius: 8px; overflow: hidden;"></div>
                            </div>`;
                        
                        document.getElementById("streams").appendChild(remoteContainer);
                    }
                    
                    
                }
            
                if (mediaType === "video") {
                    // Get the remote video track
                    const remoteVideoTrack = user.videoTrack;
                    if (remoteVideoTrack) {
                        remoteVideoTrack.play(remoteContainer.querySelector('.side-content .stream'));
                        remoteContainer.querySelector('.stream').classList.remove('d-none');
                        remoteContainer.querySelector('.blank-screen').classList.add('d-none'); // Hide blank screen
                    }
                }
            
                if (mediaType === "audio") {
                    const remoteAudioTrack = user.audioTrack;
                    if (remoteAudioTrack) {
                        remoteAudioTrack.play(); // Play audio track
                    }
                }
            });
            
          client.on("user-unpublished", (user, mediaType) => {
            const remoteContainer = document.querySelector(`div[data-uid="${user.uid}"]`);
        
            // Condition for when the user turns off their camera
            if (mediaType === "video" && remoteContainer) {
                // Hide the video stream and show a blank screen when the camera is turned off
                remoteContainer.querySelector('.stream').classList.add('d-none');
                remoteContainer.querySelector('.blank-screen').classList.remove('d-none'); // Show blank screen
                remoteContainer.querySelector('.blank-screen').style.backgroundColor = 'black'; // Set the blank screen to black
            }
        });
        
        client.on("user-left", (user) => {
            const userId = user.uid; // Get the UID of the user who left
            const remoteStreamDiv = document.querySelector(`div[data-uid="${userId}"]`);
        
            if (remoteStreamDiv) {
                // Remove the div from the DOM when the user leaves
                remoteStreamDiv.remove();
                if(user.uid='astrologer'){
                    toastr.warning('Puja ended by astrologer.');
                    setTimeout(() => {
                        window.location.href="{{ route('front.home') }}"
                    }, 5000); // After 2 seconds
                }
            }
        });


    
            document.getElementById('startBroadcast').onclick = async () => {
                const isPujaEnded = {{ $puja->isPujaEnded ? 'true' : 'false' }};

                if (isPujaEnded) {
                    toastr.error("Puja has already ended.");
                    return;
                }
                try {
                 
                    await client.join(APP_ID, roomId, null, '{{$roomuid}}');
    
                    [microphoneTrack, cameraTrack] = await AgoraRTC.createMicrophoneAndCameraTracks();
                    
                    if('{{$roomuid}}'=='astrologer')
                    {
                        cameraTrack.play("astro-stream");
                        
                        $('#local-stream').hide();
                        $("#astro-stream").addClass('astro-large-img');
                    }
                    else
                    {
                        cameraTrack.play("local-stream");
                    }
                    
                    
                    $('[id^="agora-video-player-track-cam"]').addClass('large-img');
    
                    await client.publish([microphoneTrack, cameraTrack]);
                    
                    $('.puja-details').addClass('d-none');
                    
                    $('.puja-broadcast-live').removeClass('d-none');
    
                    console.log("Broadcast started successfully");
                    startTimer({{$duration2}});
                } catch (error) {
                    console.error("Error starting broadcast:", error);
                }
            };
        
            document.getElementById('toggleMic').onclick = () => {
                if (microphoneTrack) {
                    // Toggle the muted state
                    isMicMuted = !isMicMuted; // Toggle the state
                    microphoneTrack.setEnabled(!isMicMuted); // Enable or disable based on the new state
            
                    const micIcon = document.querySelector('#toggleMic i');
                    if (isMicMuted) {
                        micIcon.className = "fa fa-microphone-slash"; // Mute icon
                    } else {
                        micIcon.className = "fa fa-microphone"; // Unmute icon
                    }
                }
            };

          

        document.getElementById('toggleCamera').onclick = () => {
            if (cameraTrack) {
                // Toggle the muted state
                isCameraMuted = !isCameraMuted; // Change state
        
                cameraTrack.setEnabled(!isCameraMuted); // Enable or disable based on the new state
        
                const cameraIcon = document.querySelector('#toggleCamera i');
                if (isCameraMuted) {
                    cameraIcon.className = "fa fa-video-slash"; // Change to mute icon
                } else {
                    cameraIcon.className = "fa fa-video-camera"; // Change to video icon
                }
            }
        };

 
        // function startTimer(duration) {
        //     let remainingTime = duration;

        //     timer = setInterval(() => {
        //         if (remainingTime <= 0) {
        //             clearInterval(timer);
        //             endBroadcast();
        //             return;
        //         }

        //         remainingTime--;
        //         const hours = Math.floor(remainingTime / 3600);
        //         const minutes = Math.floor((remainingTime % 3600) / 60);
        //         const seconds = remainingTime % 60;

        //         document.getElementById('timer').textContent = `End In  ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        //     }, 1000);
        // }


        function startTimer(duration) {
            let elapsedTime = duration;

            timer = setInterval(() => {
                elapsedTime++;
                const hours = Math.floor(elapsedTime / 3600);
                const minutes = Math.floor((elapsedTime % 3600) / 60);
                const seconds = elapsedTime % 60;

                document.getElementById('timer').textContent = `Started: ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }, 1000);
        }

        // function endBroadcast() {
        //     // Stop tracks
        //     if (microphoneTrack) {
        //         microphoneTrack.stop();
        //         microphoneTrack.close();
        //     }
        //     if (cameraTrack) {
        //         cameraTrack.stop();
        //         cameraTrack.close();
        //     }

        //     // Leave the channel
        //     client.leave();
        //     document.getElementById('timer').textContent = "Broadcast ended.";
        //     console.log("Broadcast ended.");
        //     window.location.href = "";
        // }
        
        
        
        // for camera flip 
        let currentCamera = 0; // 0 for front camera, 1 for back camera
        let cameraDevices = []; // To store available camera devices
        
        async function getCameraDevices() {
            const devices = await AgoraRTC.getCameras(); // Get all camera devices
            cameraDevices = devices;
            console.log("Available cameras:", cameraDevices);
        }
        
        // Call this function to fetch camera devices when the page loads
        getCameraDevices();
        
        document.getElementById('flipCamera').onclick = async () => {
            try {
                if (cameraDevices.length > 1) { // Ensure there are multiple cameras available
                    currentCamera = (currentCamera + 1) % cameraDevices.length; // Toggle between front and back cameras
        
                    // Switch to the selected camera device
                    await cameraTrack.setDevice(cameraDevices[currentCamera].deviceId);
                    console.log(`Switched to ${cameraDevices[currentCamera].label}`);
                } else {
                    alert("No additional cameras found.");
                }
            } catch (error) {
                console.error("Error flipping camera:", error);
            }
        };

    </script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const endBtn = document.getElementById('endPujaBtn');

        if (endBtn) {
            endBtn.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to end the Puja broadcast.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, end it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('{{ route("broadcast.endpuja") }}', {
                            puja_id: {{ $puja->id }}
                        })
                        .then(res => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Puja Ended',
                                text: res.data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            endBroadcast();
                            // setTimeout(() => {
                            //     endBroadcast(); // Call your old broadcast cleanup function
                            // }, 1600);
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong while ending the puja.'
                            });
                        });
                    }
                });
            });
        }
    });

    function endBroadcast() {
        if (microphoneTrack) {
            microphoneTrack.stop();
            microphoneTrack.close();
        }
        if (cameraTrack) {
            cameraTrack.stop();
            cameraTrack.close();
        }

        client.leave();
        document.getElementById('timer').textContent = "Broadcast ended.";

        // Redirect to puja list
        window.location.href = "{{ route('front.astrologerindex') }}";
    }
</script>


@endsection


