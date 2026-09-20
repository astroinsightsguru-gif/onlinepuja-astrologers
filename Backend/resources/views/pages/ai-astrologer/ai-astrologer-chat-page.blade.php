@extends('frontend.layout.master')
<meta name="csrf-token" content="{{ csrf_token() }}">


<style>
.sf_chat_button {
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
    }
    @media (max-width: 768px) {
        .chat-container {
            padding: 15px;
        }
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

    button:disabled, input:disabled, select:disabled, textarea:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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
@section('content')

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
                                <img src="{{asset($astrologer->image)}}"
                                class="rounded-circle mr-1 border border-warning" alt="Sharon Lessman" width="40" height="40">
                            </div>
                            <div class="flex-grow-1 pl-3">
                                <strong>{{$astrologer->name}}</strong>
                            </div>

                            <div id="timerContainer">
                                <div class="text-muted small">Remaining Time: <span id="countdownTimer">00:00</span>
                                    <form id="endChatForm" class="d-inline-block" method="POST" action="{{ route('store.ai.chat.history') }}">
                                        @csrf
                                        <input type="hidden" name="chatDuration" id="chatDuration" value="{{ $chatDuration }}">
                                        <input type="hidden" name="astrologer_id" id="astrologer_id" value="{{ @$astrologerId }}">
                                        <input type="hidden" name="actualDuration" id="actualDuration" value="0">
                                        <button type="button" class="btn view-more" id="endChat">End</button>
                                    </form>
                                    <a href="{{route('ai.chat.list')}}" class="leaveBtn btn view-more" style="display: none;">Leave</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="position-relative">
                    <div class="flex-grow-0 py-3 px-4 border-top">
                        <div class="input-group">
                            <div class="input-group-append"  style="width: 100%;">
                                <div class="chat-box" id="chat-box">
                                </div>
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
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                <button type="button" class="close" id="closeModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to leave? Your chat will be ended.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelLeave">No</button>
                <button type="button" class="btn btn-danger" id="confirmLeave">Yes</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.leaveBtn').hide();
        let initialDuration = {{ $chatDuration }} * 60;
        let duration = initialDuration;
        const savedDuration = localStorage.getItem('remainingTime');
        if (savedDuration) {
            duration = parseInt(savedDuration);
        }

        const countdownTimer = document.getElementById("countdownTimer");
        const actualDurationInput = document.getElementById("actualDuration");

        const interval = setInterval(function() {
            // Calculate minutes and seconds
            const minutes = Math.floor(duration / 60);
            const seconds = duration % 60;

            // Format minutes and seconds
            const formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
            const formattedSeconds = seconds < 10 ? "0" + seconds : seconds;

            // Display the timer
            countdownTimer.textContent = `${formattedMinutes}:${formattedSeconds}`;

            // Check if the countdown has reached zero
            if (duration <= 0) {
                clearInterval(interval);
                countdownTimer.textContent = "00:00";
                submitForm(initialDuration);
            } else {
                if (!localStorage.getItem('formSubmitting')) {
                    localStorage.setItem('remainingTime', duration);
                }
            }
            duration--;
        }, 1000);

        // Event listener for the End button
        document.getElementById("endChat").addEventListener("click", function() {
            clearInterval(interval); // Stop the countdown
            countdownTimer.textContent = "00:00"; // Set display to 00:00

            const actualDuration = initialDuration - duration; // Calculate actual duration used
            actualDurationInput.value = actualDuration; // Set the value to the hidden input
            submitForm(actualDuration);
        });

        let isLeaving = false; // Flag to track if the user has confirmed leaving

        function submitForm(usedDuration) {
            localStorage.setItem('formSubmitting', 'true');
            localStorage.removeItem('remainingTime');
            localStorage.setItem('refreshRedirect', 'true');
            actualDurationInput.value = usedDuration; // Set the actual used duration
            const formData = new FormData(document.getElementById("endChatForm"));
            // Use fetch to submit the form
            fetch(document.getElementById("endChatForm").action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(data => {
                try {
                    // Attempt to parse the JSON
                    const jsonData = JSON.parse(data);
                    toastr.success(jsonData.message);
                    document.getElementById("endChat").style.display = "none"; 
                    $('.leaveBtn').show();
                    // document.getElementById("chat-form").style.display = "none"; // Hide the chat-form
                    // const endChatButton = document.getElementById("endChat");
                    // if (endChatButton) {
                    //     endChatButton.disabled = true;
                    // }
                    // Disable all form elements inside the chat-form
                    const chatFormElements = document.getElementById("chat-form").querySelectorAll('input, button, select, textarea');
                    chatFormElements.forEach(element => {
                        element.disabled = true;
                    });

                    const messageElement = document.createElement('div');
                    messageElement.className = "alert alert-danger";
                    messageElement.textContent = "Chat ended!";
                    messageElement.style.width = "100%";
                    messageElement.style.textAlign = "center";
                    messageElement.style.marginBottom = "15px";
                    // Insert the message element before the chat form
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

        window.addEventListener("unload", function() {
            // Check if the form is not submitting
            if (!localStorage.getItem('formSubmitting')) {
            } else {
                // Remove the flag after the submission
                localStorage.removeItem('remainingTime');
                localStorage.removeItem('formSubmitting');

            }
        });

        // Check if the page was just refreshed and redirect if needed
        if (localStorage.getItem('refreshRedirect')) {
            localStorage.setItem('removeRemainingTime', 'true');
            localStorage.removeItem('refreshRedirect');
            window.location.href = "{{route('front.home')}}";
        }

        // Function to check if the route exists
        function checkRouteAndNavigate(url) {
            fetch(url, { method: 'HEAD' })  // Using HEAD to only check if the URL exists
            .then(response => {
                if (response.ok) {
                    // If the route exists (status 200-299), proceed with navigation
                    window.location.href = url;
                } else {
                    console.error('Route not found, redirecting to home...');
                    window.location.href = "{{route('front.home')}}";
                }
            })
            .catch(error => {
                // If there's an error (e.g., network issue), redirect to home
                console.error('Error checking route, redirecting to home:', error);
                window.location.href = "{{route('front.home')}}";
            });
        }
        // Function to prompt the user with a confirmation alert
        isLeaving = false;
        function showConfirmation(event) {
            if (!localStorage.getItem('formSubmitting') && !isLeaving) {
                event.preventDefault();
                // Show SweetAlert to confirm navigation
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You have an active chat. Do you want to end the chat and leave?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, leave',
                    cancelButtonText: 'No, stay'
                }).then((result) => {

                    if (result.isConfirmed) {
                        const actualDuration = initialDuration - duration; // Calculate actual duration used
                        actualDurationInput.value = actualDuration;
                        // User confirmed to leave; submit the form before leaving
                        submitForm(actualDuration); // Automatically submit the form
                        isLeaving = true; // Set flag to allow the navigation to proceed
                        const targetUrl = event.target.href;
                        checkRouteAndNavigate(targetUrl);
                    }else {
                        isLeaving = false;
                    }
                });
            }
        }
        // Function to handle the "beforeunload" event (when trying to refresh or navigate away from the page)
        window.addEventListener('beforeunload', function(event) {
            if (!isLeaving) {
                event.preventDefault();
                event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        // Intercept clicks on anchor (<a>) elements
            document.querySelectorAll('a').forEach(function(anchor) {
                anchor.addEventListener('click', showConfirmation);
            });

        });
    </script>

    <?php
    $userName = '';
    if (authcheck()) {
        $userName = authcheck()['name'];

    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {

            const userName = '<?php echo htmlspecialchars($userName, ENT_QUOTES, "UTF-8"); ?>';
            showLoader();
            // Step 1: Automatically send a request to the backend to get the greeting message from ChatGPT
            $.ajax({
                url: '{{route("ask.chatgpt")}}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    message: `Send me a greeting. My name is ${userName}, in less than 40 words in hindi.`,
                    astrologerId :{{$astrologerId}}
                },
                success: function (response) {
                    console.log(response);
                    if (response.message) {
                        hideLoader();
                        processMessageSequentially(response.message);
                    } else {
                        console.error('No message returned from ChatGPT');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error occurred:', error);
                    hideLoader();
                    appendMessage('assistant', 'Please wait a second...');
                    location.reload();
                }
            });

            $('#chat-form').on('submit', function (e) {
                e.preventDefault();

                const message = $('#message').val().trim();

                if (message) {
                    appendMessage('user', escapeHtml(message));
                    showLoader();
                    $.ajax({
                        url: '{{route("ask.chatgpt")}}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            message: message,
                            astrologerId :{{$astrologerId}}
                        },
                        success: function (response) {
                            hideLoader();
                            if (response.message) {
                                processMessageSequentially(response.message);
                            } else {
                                console.error('Response message is undefined');
                                appendMessage('assistant', 'Something is wrong.');
                                location.reload();
                            }
                        }
                    });
                    $('#message').val('');
                }
            });

            async function processMessageSequentially(text) {
                const paragraphs = text.split("\n\n");  // Split text into paragraphs based on double new lines (or you can adjust the delimiter)
                // Process each paragraph one by one
                for (let paragraph of paragraphs) {
                    // Add each paragraph with a typewriter effect
                    await appendMessageWithTypewriterEffect('assistant', paragraph);
                }
            }

            async function appendMessageWithTypewriterEffect(role, text) {
                const chatBox = $('#chat-box');
                const messageHtml = $(`<div class="chat-message ${role}"><p></p></div>`);
                // Append the new message div to the chat box
                chatBox.append(messageHtml);
                let index = 0;
                const messageParagraph = messageHtml.find('p');  // Find the <p> element inside the div
                    $('#send-btn').prop('disabled', true);
                    // Typewriter effect: reveal one character at a time
                    while (index < text.length) {
                        messageParagraph.text(messageParagraph.text() + text[index]);  // Update the text of the <p> tag
                            index++;
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
                        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                    }

                    // Function to show the loader
                    function showLoader() {
                        let typing = document.getElementById('typing-loader').innerHTML;

                        const endChatButton = document.getElementById("send-btn");
                        if (endChatButton) {
                            endChatButton.disabled = true;
                        }
                        if ($('#loader-message').length === 0) {
                            const messageHtml1 = `
                                <div class="chat-message assistant" id="loader-message">
                                    <p>${typing}</p>
                                </div>
                            `;
                            $('#chat-box').append(messageHtml1);
                            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // Scroll to bottom
                        }
                    }

                    // Function to hide the loader
                    function hideLoader() {
                        $('#loader-message').remove(); // Remove the loader element from the DOM
                    }


                    function formatBashCode(text) {
                        return text.replace(/```bash([\s\S]*?)```/g, function (match, code) {
                            return `
                        <div class="code-block">
                            <span class="bash-label"> </span>
                            <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
                            <pre>${code.trim()}</pre>
                        </div>
                        `;
                        });
                    }

                    function escapeHtml(text) {
                        return text.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    }

                    function copyToClipboard(button) {
                        const codeBlock = $(button).siblings('pre').text();
                        navigator.clipboard.writeText(codeBlock).then(function () {
                            $(button).text('Copied!');
                            setTimeout(() => {
                                $(button).text('Copy');
                            }, 2000);
                        }).catch(function (error) {
                            console.error('Copy failed:', error);
                        });
                    }
                });

                // function showLoader() {
                //     document.getElementById('loader').style.display = 'flex';
                //     document.body.classList.add('loader-active'); // Disable background interaction
                // }

                // function hideLoader() {
                //     document.getElementById('loader').style.display = 'none';
                //     document.body.classList.remove('loader-active'); // Enable background interaction
                // }
            </script>



            @endsection
