@extends('frontend.layout.master')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<style>
    .sf_chat_button1 {
    display: none !important;
    }
    .chat-box {
        height: 400px;
        overflow-y: auto;
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 10px;
        width: 100%;
    }
    .chat-message {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-end;
    }
    .chat-message.user {
        justify-content: flex-end;
    }
    .chat-message.assistant {
        display: inline-block;
    }
    .chat-message.assistant p {
        margin-top: 0;
        margin-bottom: 0!important;
    }
    .chat-message p {
        display: inline-block;
        padding: 10px 15px;
        border-radius: 5px;
        /* word-wrap: break-word;  */
    }
    .chat-message.user p {
        background-color: #7ca9ec;
        color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .chat-message.assistant {
        background-color: #e9ecef;
        color: #333;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius:5px;
        /* max-width: 85%; */
    }
    @media (max-width: 768px) {
        .chat-container {
            padding: 15px;
        }
        /* .chat-message p {
        max-width: 90%;
        } */
    }

    .code-block {
        background-color: #2d2d2d;
        color: #ffffff;
        padding: 15px;
        border-radius: 5px;
        font-family: monospace;
        position: relative;
        margin: auto;
        display: block;
        max-width: 100%;
    }

    .copy-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: transparent;
        color: #ffffff;
        border: 1px solid #ffffff;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    }

    .copy-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .bash-label {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #2d2d2d;
        color: #ffffff;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 12px;
    }
</style>
<style>
    .rounded {
        border: 1px solid #e4e5e6;
        border-radius: 10px !important;
    }

    .attachment-icon {
        cursor: pointer;
    }

    .attachment-icon i {
        padding: 10px;
        color: #aaa;
    }

    .attachment-icon:hover i {
        color: #333;
    }

    .chat-message-right {
        max-width: 70% !important;
    }

    .chat-message-left {
        max-width: 70% !important;
    }

    .chat-message-left {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .message-user-left-img {
        display: flex;
        gap: 10px;
        justify-content: start;
        align-items: center;
    }

    .message-user-left-img img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .message-user-left-text {
        position: relative;
        padding: 15px 25px;
        background-color: #ffffff;
        border-radius: 15px;
        color: #000000;
        max-width: 250px;
    }

    .message-user-left-text::before {
        content: '';
        position: absolute;
        top: -26px;
        left: 15px;
        border-right: 15px solid transparent;
        border-top: 15px solid transparent;
        border-left: 0px solid transparent;
        border-bottom: 15px solid #ffffff;
    }

    .chat-messages.p-4 {
        background : #ececec;
    }

    .chat-message.chat-message-right {
        display: flex;
        flex-direction: column;
        align-items: end;
        gap: 15px;
    }

    .message-user-right {
        display: flex;
        flex-direction: column;
        align-items: end;
        gap: 15px;
    }

    .message-user-right-img {
        display: flex;
        gap: 10px;
        justify-content: end;
        align-items: center;
    }

    .message-user-right-img img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .message-user-right-text {
        position: relative;
        padding: 15px 25px;
        background-color: #c5e2e8;
        border-radius: 15px;
        color: #000000;
        width: 250px;
    }

    .message-user-right-text::before {
        content: '';
        position: absolute;
        top: -26px;
        right: 15px;
        border-right: 0px solid transparent;
        border-top: 15px solid transparent;
        border-left: 15px solid transparent;
        border-bottom: 15px solid #c5e2e8;
    }

    .bg-msg {
        background: #207678c4 !important;
    }

    #test{
        font-size: 18px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background-color: #207678;
        color: #ffffff;
        cursor: pointer;
    }


    .btn-new {

        width: 10% !important;
    }
    /* Show the profile details, timer, and controls in mobile view */
    @media (max-width: 767.98px) {
        .chat-messages {
            padding-bottom: 120px;
        }

        .card .py-2 {
            display: block !important;
        }

        #timerContainer {
            display: block !important;
            text-align: center;
        }

        .input-group {
            padding: 10px;
        }


        .rounded-circle {
            width: 30px;
            height: 30px;
        }

        /
        .flex-grow-1 strong {
            font-size: 14px;
        }

        .fa-paperclip, .fa-face-smile {
            font-size: 14px;
        }

        #remainingTime {
            font-size: 14px;
        }

        #endChat {
            font-size: 12px;
            padding: 5px 10px;
        }


        .btn-new {
            font-size: 12px !important;
            width: 42% !important;
        }
    }
    /* Optional: Add some CSS to style disabled elements */
    button:disabled, input:disabled, select:disabled, textarea:disabled {
        opacity: 0.5;  /* Gray out the disabled elements */
        cursor: not-allowed; /* Change the cursor to a 'not allowed' icon */
    }

</style>
<!-- CSS for styling the questions -->
<style>
    #random-questions-container {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .question-box {
        display: inline-block;
        padding: 1px 7px;
        margin: 1px;
        background-color: #7ca9ec87;
        color: #000000;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .question-box:hover {
        background-color: #71a4f1;
        color: white;
    }
    .question{
        justify-content: center;
        display: flex;
    }
    .input-group{
        justify-content: center;
    }

</style>

<style>
    .typing-loader{
        margin-top:-20px;
        margin-left:80%;
        width: 6px;
        height: 6px;
        -webkit-animation: line 1s linear infinite alternate;
        -moz-animation: line 1s linear infinite alternate;
        animation: line 1s linear infinite alternate;
    }
    #typing-loader{
        background: #e9ecef!important;
        display:flex;
        font-size: 18px;
    }
    .p{
        padding-right:55px!important;
        margin: 0!important;
        margin-left: -30px!important;
    }

    @-webkit-keyframes line{
        0%{

            background-color: rgba(0,0,0, 1);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,0.2),
            24px 0px 0px 0px rgba(0,0,0,0.2);

        }
        25%{
            background-color: rgba(0,0,0, 0.4);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,2),
            24px 0px 0px 0px rgba(0,0,0,0.2);

        }
        75%{ background-color: rgba(0,0,0, 0.4);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,0.2),
            24px 0px 0px 0px rgba(0,0,0,2);

        }
    }

    @-moz-keyframes line{
        0%{

            background-color: rgba(0,0,0, 1);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,0.2),
            24px 0px 0px 0px rgba(0,0,0,0.2);

        }
        25%{
            background-color: rgba(0,0,0, 0.4);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,2),
            24px 0px 0px 0px rgba(0,0,0,0.2);

        }
        75%{ background-color: rgba(0,0,0, 0.4);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,0.2),
            24px 0px 0px 0px rgba(0,0,0,2);

        }
    }

    @keyframes line{
        0%{

            background-color: rgba(0,0,0, 1);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,0.2),
            24px 0px 0px 0px rgba(0,0,0,0.2);

        }
        25%{
            background-color: rgba(0,0,0, 0.4);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,2),
            24px 0px 0px 0px rgba(0,0,0,0.2);

        }
        75%{ background-color: rgba(0,0,0, 0.4);
            box-shadow: 12px 0px 0px 0px rgba(0,0,0,0.2),
            24px 0px 0px 0px rgba(0,0,0,2);

        }
    }
</style>

<div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
    <div class="container">
        <div class="row afterLoginDisplay">
            <div class="col-md-12 d-flex align-items-center">
                <span style="text-transform: capitalize; ">
                    <span class="text-white breadcrumbs">
                        <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                            <i class="fa fa-home font-18"></i>
                        </a>
                        <i class="fa fa-chevron-right"></i>
                        <span class="breadcrumbtext">Chat</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>
@php
$logo = DB::table('systemflag')
->where('name', 'AdminLogo')
->select('value')
->first();
@endphp

<div id="typing-loader" style="display: none;">
    <p class="p">Astrologer Thinking</p>
    <div class="typing-loader"></div>
</div>

<main class="content">
    <div class="container p-0">
        <h1 class="h3 mb-3 mt-5 ml-4">Chat</h1>
        <div class="card ">
            <div class="row g-0">
                <div class="col-12 col-lg-12 col-xl-12">
                    <div class="py-2 px-4 border-bottom d-none d-lg-block">
                        <div class="d-flex align-items-center py-1">
                            <div class="position-relative">
                                <img src="{{asset(@$astrologer->image)}}"
                                class="rounded-circle mr-1 border border-warning" alt="Sharon Lessman" width="40" height="40">
                            </div>
                            <div class="flex-grow-1 pl-3">
                                <strong>{{@$astrologer->name}}</strong>
                            </div>

                            <div id="timerContainer">
                                <div class="text-muted small"><span id="countdownTimer"></span>
                                    <form id="endChatForm" class="d-inline-block" method="POST" action="{{ route('store.master.ai.chat.history') }}">
                                        @csrf
                                        <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

                                        <div id="time">Time: <span id="timerDisplayHHMMSS">00:00:00</span></div>
                                        <div id="timerDisplayMinutes" hidden>Time (minutes): 0 minutes</div>
                                        <div id="timerDisplayCharge" hidden>Total Charge: {{@$currency['value']}} 0.00</div>
                                        <div id="" class="font-weight-bold">Balance: {{@$currency['value']}} <span id="availableBalance">{{@$user_balance}}</span></div>

                                        <input type="hidden" name="user_balance" id="user_balance" value="{{@$user_balance}}">

                                        <input type="hidden" name="chat_charge" id="chat_charge" value="{{@$astrologer->chat_charge}}">

                                        <button type="button" class="btn view-more" id="endChat">End</button>
                                    </form>
                                    <a href="{{url('/')}}" class="leaveBtn btn view-more" style="display: none;">Leave</a>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="position-relative">
                    <div class="flex-grow-0 py-3 px-4 border-top">
                        <div class="input-group">
                            <div class="input-group-append" style="width: 100%;">
                                <div class="chat-box" id="chat-box">
                                </div>
                            </div>
                            <div id="random-questions-container"  style="display: none;">
                                <div id="question1" class="question-box"></div>
                                <div id="question2" class="question-box"></div>
                            </div>
                            <form id="chat-form" class="input-group">
                                <input type="text" id="message" name="message" class="form-control" placeholder="Type your message here..." required>
                                <button type="submit" class="btn btn-chat" id="send-btn">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="text-danger">
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@section('scripts')


    <?php
    $userName = '';
    if (authcheck()) {
        $userName = authcheck()['name'];
    }
    ?>
    <?php
    $currency = $currency['value'];
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            if (localStorage.getItem('reloadAftSubmit')) {
                reloadAftSubmit();
            }
             $('.leaveBtn').hide();
            const currency = '<?php echo $currency; ?>';
            const userName = '<?php echo htmlspecialchars($userName, ENT_QUOTES, "UTF-8"); ?>';
            let timerInterval;

            let timer = parseInt(localStorage.getItem('timer')) || 0;
            let userBalance = parseFloat(localStorage.getItem('balance')) || parseFloat("{{$user_balance}}"); // Assuming you pass the initial user balance dynamically from your backend
            let chatCharge = parseFloat(document.getElementById("chat_charge").value);  // Get the chat charge value

            let totalCharge = 0;
            if(chatCharge !== null){
                if(userBalance > chatCharge){
                    $('#send-btn').prop('disabled', false);
                }else{
                    $('#send-btn').prop('disabled', true);
                }
                if(userBalance > chatCharge){
                    showLoader()

                    $.ajax({
                        url: '{{route("ask.master")}}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            message: `Send me a greeting. My name is ${userName}, in less than 40 words in hindi.`,
                        },
                        success: function (response) {
                            if (response.message) {
                                hideLoader()
                                processMessageSequentially(response.message);

                                let timerInterval = setInterval(function() {
                                    let hours = Math.floor(timer / 3600);
                                    let minutes = Math.floor((timer % 3600) / 60);
                                    let seconds = timer % 60; // Remaining seconds

                                    // Format time as hh:mm:ss (with leading zeros for hours, minutes, and seconds if needed)
                                    let formattedTimeHHMMSS =
                                    (hours < 10 ? "0" : "") + hours + ":" +
                                    (minutes < 10 ? "0" : "") + minutes + ":" +
                                    (seconds < 10 ? "0" : "") + seconds;

                                    // Calculate the rounded minutes (rounding up if there are remaining seconds)
                                    let roundedMinutes = Math.ceil(timer / 60); // Round up to next minute if there are seconds

                                    // Format the rounded minutes
                                    let formattedTimeMinutes = roundedMinutes + " minute" + (roundedMinutes > 1 ? "s" : "");

                                    // Get chat charge from the input field
                                    let chatCharge = parseFloat(document.getElementById("chat_charge").value);

                                    // Multiply rounded minutes by chat charge
                                    let totalCharge = roundedMinutes * chatCharge;
                                    let availableBalance = {{$user_balance}} - totalCharge;
                                    let balance = {{$user_balance}}
                                    // Update the timer display for rounded minutes
                                    document.getElementById("timerDisplayMinutes").textContent = "Time (minutes): " + formattedTimeMinutes;
                                    document.getElementById("timerDisplayCharge").textContent = "Total Charge: " + currency + totalCharge.toFixed(2); // Format to 2 decimal places
                                    // Update the timer display for hh:mm:ss format
                                    document.getElementById("timerDisplayHHMMSS").textContent =  formattedTimeHHMMSS;
                                    document.getElementById("availableBalance").textContent =  availableBalance.toFixed(2); // Format to 2 decimal places

                                    updateLocalStorage();
                                    // Check if it's the end of the current minute (when seconds = 59)
                                    if (seconds === 59) {
                                        // Check if the total charge for the next minute exceeds the user balance
                                        let nextCharge = (roundedMinutes + 1) * chatCharge;  // Calculate the charge for the next minute

                                        if (nextCharge > userBalance) {
                                            // If the total charge exceeds the balance, stop the timer
                                            clearInterval(timerInterval); // Stop the timer
                                            document.getElementById("timerDisplayCharge").textContent = "Total Charge: " + currency + totalCharge.toFixed(2) + " (Insufficient balance to proceed)";
                                            submitChatData(timer)
                                        }
                                    }
                                    timer++;
                                }, 1000);  // Timer interval is set to 1 second

                                document.getElementById("endChat").addEventListener("click", function() {
                                    clearInterval(timerInterval);
                                    submitChatData(timer)
                                });

                            } else {
                                console.error('No message returned from ChatGPT');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error occurred:', error);
                            hideLoader()
                            appendMessage('assistant', 'Please wait a second...');
                            location.reload();

                        }
                    });
                }else{
                    processMessageSequentially('⚠️ Please recharge your wallet to continue chatting!');

                    // Disable the "End" button instead of hiding it
                    // const endChatButton = document.getElementById("endChat");
                    // if (endChatButton) {
                    //     endChatButton.disabled = true;
                    // }
                    document.getElementById("endChat").style.display = "none";
                         $('.leaveBtn').show();

                    // Disable all form elements inside the chat-form
                    const chatFormElements = document.getElementById("chat-form").querySelectorAll('input, button, select, textarea');
                    chatFormElements.forEach(element => {
                        element.disabled = true; // Disable individual form elements inside the chat-form
                    });
                    toastr.warning('⚠️ Please recharge your wallet to continue chatting!');
                }
            }else{
                showLoader()

                $.ajax({
                    url: '{{route("ask.master")}}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        message: `Send me a greeting. My name is ${userName}, in less than 40 words in hindi.`,
                    },
                    success: function (response) {
                        if (response.message) {
                            hideLoader()
                            processMessageSequentially(response.message);

                            let timerInterval = setInterval(function() {
                                let hours = Math.floor(timer / 3600);
                                let minutes = Math.floor((timer % 3600) / 60);
                                let seconds = timer % 60; // Remaining seconds

                                // Format time as hh:mm:ss (with leading zeros for hours, minutes, and seconds if needed)
                                let formattedTimeHHMMSS =
                                (hours < 10 ? "0" : "") + hours + ":" +
                                (minutes < 10 ? "0" : "") + minutes + ":" +
                                (seconds < 10 ? "0" : "") + seconds;

                                // Calculate the rounded minutes (rounding up if there are remaining seconds)
                                let roundedMinutes = Math.ceil(timer / 60); // Round up to next minute if there are seconds

                                // Format the rounded minutes
                                let formattedTimeMinutes = roundedMinutes + " minute" + (roundedMinutes > 1 ? "s" : "");
                                // Get chat charge from the input field
                                let chatCharge = parseFloat(document.getElementById("chat_charge").value);

                                // Multiply rounded minutes by chat charge
                                let totalCharge = roundedMinutes * chatCharge;
                                let availableBalance = {{$user_balance}} - totalCharge;
                                let balance = {{$user_balance}}

                                // Update the timer display for rounded minutes
                                document.getElementById("timerDisplayMinutes").textContent = "Time (minutes): " + formattedTimeMinutes;
                                document.getElementById("timerDisplayCharge").textContent = "Total Charge: " + currency + totalCharge.toFixed(2); // Format to 2 decimal places
                                // Update the timer display for hh:mm:ss format
                                // document.getElementById("timerDisplayHHMMSS").textContent =  formattedTimeHHMMSS;
                                // document.getElementById("availableBalance").textContent =  availableBalance.toFixed(2); // Format to 2 decimal places

                                updateLocalStorage();
                                // Check if it's the end of the current minute (when seconds = 59)
                                // if (seconds === 59) {
                                //     // Check if the total charge for the next minute exceeds the user balance
                                //     let nextCharge = (roundedMinutes + 1) * chatCharge;  // Calculate the charge for the next minute

                                //     if (nextCharge > userBalance) {
                                //         // If the total charge exceeds the balance, stop the timer
                                //         clearInterval(timerInterval); // Stop the timer
                                //         document.getElementById("timerDisplayCharge").textContent = "Total Charge: INR " + totalCharge.toFixed(2) + " (Insufficient balance to proceed)";
                                //         submitChatData(timer)
                                //     }
                                // }
                                timer++;
                            }, 1000);  // Timer interval is set to 1 second
                            document.getElementById("endChat").addEventListener("click", function() {
                                clearInterval(timerInterval);
                                submitChatData(timer)
                            });

                        } else {
                            console.error('No message returned from ChatGPT');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error occurred:', error);
                        hideLoader()
                        appendMessage('assistant', 'Please wait a second...');
                        location.reload();
                        localStorage.removeItem('masterSubmitting');


                    }
                });
            }

            function updateLocalStorage() {
                localStorage.setItem('timer', timer);
                localStorage.setItem('balance', availableBalance);
            }
            function clearLocalStorage() {
                localStorage.removeItem('timer');
                localStorage.removeItem('balance');
            }

            window.addEventListener('unload', function () {
                console.log('unload')

                // This event will trigger when the page is actually unloaded, not when it's about to be unloaded.
                localStorage.removeItem('timer');
                localStorage.removeItem('balance');

                clearInterval(timerInterval); // Clear any ongoing timers
            });

            window.addEventListener('beforeunload', function (e) {

                if (!localStorage.getItem('masterSubmitting')) {
                    console.log('false');
                    clearInterval(timerInterval);
                    localStorage.removeItem('timer');
                    localStorage.removeItem('balance');
                    submitChatData(timer);
                }else{
                    localStorage.setItem('reloadAftSubmit', 'true');
                    console.log('true');
                } // Submit chat data (your function)

            });
            function reloadAftSubmit(){
                localStorage.removeItem('masterSubmitting');
                localStorage.removeItem('timer');
                localStorage.removeItem('balance');
                window.location.href = "{{route('front.home')}}";
            }
            // Function to send chat data and handle the response
            function submitChatData(timer) {
                // Prepare the data
                const data = {
                    astrologer_id: {{$astrologer->id}}, // You can dynamically pass this if needed
                    timeDuraction: timer,
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
                    message: 'Chat ended successfully.'
                };

                // Log the form submission process
                localStorage.setItem('masterSubmitting', 'true');
                localStorage.setItem('refreshRedirectMaster', 'true');
                clearLocalStorage();
                // Start the fetch process
                const routeUrl = '{{ route("store.master.ai.chat.history") }}'; // Route for your API

                fetch(routeUrl, { // Replace with your actual API endpoint URL
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data) // Send data as JSON
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text();
                })
                .then(data => {
                    try {
                        const jsonData = JSON.parse(data); // Parse the response as JSON
                        toastr.success(jsonData.message); // Show success message using toastr

                        // Disable the "End Chat" button
                        document.getElementById("endChat").style.display = "none";
                         $('.leaveBtn').show();
                        // const endChatButton = document.getElementById("endChat");
                        // if (endChatButton) {
                        //     endChatButton.disabled = true;
                        // }

                        // Disable all form elements inside the chat-form
                        const chatFormElements = document.getElementById("chat-form").querySelectorAll('input, button, select, textarea');
                        chatFormElements.forEach(element => {
                            element.disabled = true;
                        });

                        // Create and display an alert message
                        const messageElement = document.createElement('div');
                        messageElement.className = "alert alert-danger";
                        messageElement.textContent = "Chat ended!";
                        messageElement.style.width = "100%";
                        messageElement.style.textAlign = "center";
                        messageElement.style.marginBottom = "15px";

                        // Insert the message element before the chat-form
                        document.getElementById("chat-form").parentNode.insertBefore(messageElement, document.getElementById("chat-form"));
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.log('Response data:', data); // Log the raw response
                        toastr.error('An error occurred while processing the response.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while submitting the form. Please try again.');
                });
            }

            isLeaving = false;
            function showConfirmation(event) {
                if (!localStorage.getItem('masterSubmitting') && !isLeaving) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        text: 'You have an active chat. Do you want to end the chat and leave?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, leave',
                        cancelButtonText: 'No, stay'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            clearInterval(timerInterval);
                            submitChatData(timer)
                            clearLocalStorage();
                            isLeaving = true; // Set flag to allow the navigation to proceed
                            const targetUrl = event.target.href;
                            checkRouteAndNavigate(targetUrl);
                        }else {
                            isLeaving = false; // Reset flag
                        }
                    });
                }
            }

            // Intercept clicks on anchor (<a>) elements
                document.querySelectorAll('a').forEach(function(anchor) {
                    anchor.addEventListener('click', showConfirmation);
                });


                // Function to get two random questions
                var questions = @json($questions);

                function getRandomQuestions() {
                    const randomIndex1 = Math.floor(Math.random() * questions.length);
                    let randomIndex2 = Math.floor(Math.random() * questions.length);

                    // Ensure the second index is different from the first
                    while (randomIndex1 === randomIndex2) {
                        randomIndex2 = Math.floor(Math.random() * questions.length);
                    }

                    console.log("Random Questions:", questions[randomIndex1], questions[randomIndex2]); // Debugging line
                    return [questions[randomIndex1], questions[randomIndex2]];
                }

                // Function to display the random questions in the view
                function displayRandomQuestions() {
                    const randomQuestions = getRandomQuestions();

                    // Get the div elements where the questions will be displayed
                    const question1Div = document.getElementById('question1');
                    const question2Div = document.getElementById('question2');
                    const questionsContainer = document.getElementById('random-questions-container');

                    // If there are valid questions
                    if (randomQuestions[0] && randomQuestions[1]) {
                        // Show the questions container
                        questionsContainer.style.display = 'block';

                        // Set the text content for each question
                        question1Div.textContent = randomQuestions[0];
                        question2Div.textContent = randomQuestions[1];

                        // Add click event listeners to each question
                        question1Div.addEventListener('click', function () {
                            $('#message').val(randomQuestions[0]);  // Set message input field with the clicked question
                            $('#chat-form').submit();  // Submit the form
                        });

                        question2Div.addEventListener('click', function () {
                            $('#message').val(randomQuestions[1]);  // Set message input field with the clicked question
                            $('#chat-form').submit();  // Submit the form
                        });
                    } else {
                        // If no questions, hide the questions container
                        questionsContainer.style.display = 'none';
                    }
                }



                //---------------------------chat-botton-------------------------------------

                $('#chat-form').on('submit', function (e) {
                    e.preventDefault();
                    const message = $('#message').val().trim();

                    if (message) {
                        appendMessage('user', escapeHtml(message));
                        showLoader()
                        $.ajax({
                            url: '{{route("ask.master")}}',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                message: message,
                            },
                            success: function (response) {
                                hideLoader()
                                // displayRandomQuestions();

                                if (response.message) {
                                    processMessageSequentially(response.message);
                                } else {
                                    console.error('Response message is undefined');
                                    appendMessage('assistant', 'An error occurred: No message returned.');
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Error occurred:', error);
                                hideLoader()
                                appendMessage('assistant', 'Please wait a second...');
                                location.reload();
                                localStorage.removeItem('masterSubmitting');

                            }
                        });
                        $('#message').val('');
                    }
                });

                async function processMessageSequentially(text) {
                    const paragraphs = text.split("\n\n");  // Split text into paragraphs based on double new lines (or you can adjust the delimiter)
                    for (let paragraph of paragraphs) {
                        await appendMessageWithTypewriterEffect('assistant', paragraph);
                    }
                }

                async function appendMessageWithTypewriterEffect(role, text) {
                    const chatBox = $('#chat-box');  // Assuming #chat-box exists in your HTML
                    const messageHtml = $(`<div class="chat-message ${role}"><p></p></div>`);
                    chatBox.append(messageHtml);

                    let index = 0;
                    const messageParagraph = messageHtml.find('p');  // Find the <p> element inside the div
                        $('#send-btn').prop('disabled', true);
                        while (index < text.length) {
                            messageParagraph.text(messageParagraph.text() + text[index]);  // Update the text of the <p> tag
                                index++;
                                scrollToBottom();
                                await new Promise(resolve => setTimeout(resolve, 25)); // Adjust speed here (50ms per character)
                            }
                            $('#send-btn').prop('disabled', false);
                        }

                        function appendMessage(role, text) {
                            const messageHtml = `
                            <div class="chat-message ${role}">
                                <p>${text}</p>
                            </div>
                            `;
                            $('#chat-box').append(messageHtml);
                            // $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                            scrollToBottom();
                        }

                        function escapeHtml(text) {
                            return text.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                        }

                    });
                    // Function to show the loader
                    function showLoader() {
                        let typing = document.getElementById('typing-loader').innerHTML;

                        $('#send-btn').prop('disabled', true);
                        if ($('#loader-message').length === 0) {
                            const messageHtml1 = `
                                <div class="chat-message assistant" id="loader-message">
                                    <p>${typing}</p>
                                </div>
                            `;
                            $('#chat-box').append(messageHtml1);
                            scrollToBottom();
                            // $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // Scroll to bottom
                        }
                    }

                    // Function to hide the loader
                    function hideLoader() {
                        $('#send-btn').prop('disabled', false);
                        $('#loader-message').remove(); // Remove the loader element from the DOM
                    }

                    function scrollToBottom() {
                    const chatBox = $('#chat-box');
                        chatBox.scrollTop(chatBox[0].scrollHeight);
                    }


                </script>



                @endsection
