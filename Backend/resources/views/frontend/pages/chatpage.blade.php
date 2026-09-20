@extends('frontend.layout.master')

<style>
    /* .add-topup-btnn{
        display:none !important;

    } */
     .btn-new {

       width: 13% !important;
   }
   @media (max-width: 767.98px) {
        .btn-new {
            font-size: 12px !important;
            width: 42% !important;
        }
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


/* Additional Styles */

/* Scrollbar styling for the chat container */
.chat-messages {
    overflow-y: auto;
    max-height: 400px;
}

.chat-messages::-webkit-scrollbar {
    width: 8px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background-color: #aaa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chat-message-left, .chat-message-right {
        max-width: 90%;
    }

    .message-user-left-text, .message-user-right-text {
        max-width: 100%;
    }

    .chat-messages {
        max-height: 300px;
    }
}

/* Smooth scrolling for new messages */
.chat-messages {
    scroll-behavior: smooth;
}

/* Add a subtle shadow to message bubbles for depth */
.message-user-left-text, .message-user-right-text {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Transition for hover effects on attachment icon */
.attachment-icon i {
    transition: color 0.2s ease;
}

/* Button hover enhancements */
.btn-chat:hover {
    background-color: #007bff;
    color: white;
    transition: background-color 0.3s ease;
}

/* Subtle hover effect for images */
.message-user-left-img img, .message-user-right-img img {
    transition: transform 0.3s ease;
}

.message-user-left-img img:hover, .message-user-right-img img:hover {
    transform: scale(1.1);
}

.chat-messages {
    height: 57vh !important;
    max-height: 57vh !important;
}

</style>

@section('content')
    @if (authcheck())
        @php
            $userId = authcheck()['id'];
            $astrologerId = request()->query('astrologerId');
            $chatId = request()->query('chatId');

            $astrologerUserId = DB::table('astrologers')
            ->where('id', $astrologerId)
            ->value('userId');

            $keywords = DB::table('block-keywords')->get(['type','pattern']);


        @endphp
    @endif


            {{-- Intake Form  chat --}}
            <div class="modal fade  mt-2 mt-md-5 " id="intake" tabindex="-1" role="dialog"
            aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">

                        <h4 class="modal-title font-weight-bold">
                            Intake Form
                        </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body pt-0 pb-0">
                        <div class="bg-white body">
                            <div class="row ">

                                <div class="col-lg-12 col-12 ">
                                    <div class="mb-3 ">

                                        <form class="px-3 font-14" method="post" id="intakeForm">


                                                  <input type="hidden" name="astrologerId" value="{{ $astrologerId }}">
                                                   @if (authcheck())
                                                  <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                                  @endif
                                                        <div class="col-12 py-3">
                                                            <div class="form-group mb-0">
                                                                <label>Select Time You want to chat<span
                                                                        class="color-red">*</span></label><br>
                                                                <div class="btn-group-toggle" data-toggle="buttons">
                                                                     <label class="btn btn-info btn-sm">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration300" value="180"> 3 mins
                                                                    </label>
                                                                    <label class="btn btn-info btn-sm">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration300" value="300"> 5 mins
                                                                    </label>
                                                                    <label class="btn btn-info btn-sm">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration600" value="600"> 10 mins
                                                                    </label>
                                                                    <label class="btn btn-info btn-sm">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration900" value="900"> 15 mins
                                                                    </label>
                                                                    <label class="btn btn-info btn-sm">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration1200" value="1200"> 20 mins
                                                                    </label>
                                                                    <label class="btn btn-info btn-sm">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration1500" value="1500"> 25 mins
                                                                    </label>
                                                                    <label class="btn btn-info btn-sm mt-2">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration1800" value="1800"> 30 mins
                                                                    </label>
                                                                     <label class="btn btn-info btn-sm mt-2">
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration3600" value="3600"> 1 hour
                                                                    </label>
                                                                       <label class="btn btn-info btn-sm mt-2" >
                                                                        <input type="radio" name="chat_duration"
                                                                            id="chat_duration7200" value="7200"> 2 hour
                                                                    </label>

                                                                </div>
                                                            </div>
                                                        </div>

                                            </div>

                                            <div class="col-12 col-md-12 py-3">
                                                <div class="row">

                                                    <div class="col-12 pt-md-3 text-center mt-2">
                                                        <button class="font-weight-bold ml-0 w-100 btn btn-chat"
                                                            id="loaderintakeBtn" type="button" style="display:none;"
                                                            disabled>
                                                            <span class="spinner-border spinner-border-sm" role="status"
                                                                aria-hidden="true"></span> Loading...
                                                        </button>
                                                        <button type="submit"
                                                            class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                                            id="intakeBtn" style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#ffd700e0';"onmouseout="this.style.backgroundColor='';" >Continue Chat</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    <div class="pt-1 pb-1 bg-red d-none d-md-block onlinepuja-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">

                    <span style="text-transform: capitalize; ">


                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <span
                                class="breadcrumbtext">Chat</span>

                        </span>

                    </span>

                </div>
            </div>
        </div>
    </div>

    <main class="content">
        <div class="container p-0">

            <h1 class="h3 mb-3 mt-4 ml-4">Chat</h1>

            <div class="card ">
                <div class="row g-0">

                    <div class="col-12 col-lg-12 col-xl-12">

                        <div class="py-2 px-4 border-bottom d-none d-lg-block">
                            <div class="d-flex align-items-center py-1">
                                <div class="position-relative">
                                    @if($getAstrologer['recordList'][0]['profileImage'])
                                    <img width="40" height="40" class="rounded-circle mr-1" src="{{ Str::startsWith($getAstrologer['recordList'][0]['profileImage'], ['http://','https://']) ? $getAstrologer['recordList'][0]['profileImage'] : $getAstrologer['recordList'][0]['profileImage'] }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Astro Image" />
                                    @else
                                    <img src="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                                        class="rounded-circle mr-1" alt="Sharon Lessman" width="40" height="40">
                                    @endif
                                </div>
                                <div class="flex-grow-1 pl-3">
                                    <strong>{{ $getAstrologer['recordList'][0]['name'] }}</strong>
                                    {{-- <div class="text-muted small"><i>100 seconds</i></div> --}}
                                </div>
                                <a
                                data-toggle="modal"
                                data-target="#intake"
                                class="btn btn-report mr-3 mb-2 add-topup-btnn"
                                id="addTopupLink">
                                Add Topup
                              </a>
                                <div id="timerContainer">
                                    <div class="text-muted small">Remaining : <span id="remainingTime"
                                            class="color-red">{{ $chatrequest->chat_duration }} seconds &nbsp;</span><span>
                                            <form id="endChatForm" class="d-inline-block">
                                                <input type="hidden" name="chatId" value="{{ $chatId }}">
                                                <input type="hidden" name="totalMin" id="totalMin" value="">
                                                <button class="btn view-more" id="endChat">End</button>
                                            </form>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="position-relative">
                            <div class="chat-messages p-4">
                            </div>

                            <div class="flex-grow-0 py-3 px-4 border-top">
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <label for="fileInput" class="attachment-icon">
                                            <input type="file" id="fileInput" class="d-none"> <!-- Hidden file input -->
                                            <i class="fas fa-paperclip"></i> <!-- Attachment icon -->
                                        </label>
                                    </div>
                                    <input type="text" id="fileDisplay"  class="form-control" placeholder="Enter your message..">
                                    <button class="btn btn-chat" id="sendButton">Send</button>
                                </div>
                            </div>

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

<div class="modal fade" id="insufficientTopUpModal" tabindex="-1" aria-labelledby="insufficientTopUpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-danger" id="insufficientTopUpModalLabel">Update Top Up</h5>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body text-center">
        <p>Your current session will be expire soon. Please Top Up Now.</p>
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button
          type="button"
          class="btn btn-warning text-white"
          data-dismiss="modal"
          data-toggle="modal"
          data-target="#intake"
          style="transition: background-color 0.3s;"
          onmouseover="this.style.backgroundColor='#ffd700e0';"
          onmouseout="this.style.backgroundColor='';"
        >
          Top Up Now
        </button>
        </div>

    </div>
  </div>
</div>


@endsection


@section('scripts')
    <script>
        document.getElementById('fileInput').addEventListener('change', function() {
            const fileInput = this;
            const fileName = fileInput.files[0] ? fileInput.files[0].name : 'No file chosen';
            document.getElementById('fileDisplay').value = fileName;
        });

        var userId = "{{ $userId }}";
        var astrologerId = "{{ $astrologerId }}";
        var astrologerUserId = "{{$astrologerUserId}}";

        var patterns = {!! json_encode($keywords) !!};
        console.log("Raw Keywords:", patterns);  // Check the raw data in the console

        // Function to decode and parse patterns
        function decodeAndParsePatterns(patterns) {
            // Ensure that the patterns is an array of objects and process accordingly
            try {
                patterns.forEach(item => {
                    if (item.type === 'offensive-word' && item.pattern) {
                        try {
                            // If the pattern is a stringified array, parse it into an actual array
                            item.pattern = JSON.parse(item.pattern);
                             console.error('item.pattern:', item.pattern);
                        } catch (e) {
                            console.error('Error parsing offensive word pattern:', e);
                            item.pattern = []; // Set it to an empty array if parsing fails
                        }
                    }
                });

                return patterns;
            } catch (error) {
                console.error("Error in decodeAndParsePatterns:", error);
                return []; // Return an empty array if an error occurs
            }
        }

        // Decode and parse the patterns
        const parsedPatterns = decodeAndParsePatterns(patterns);
        console.log("Parsed Patterns:", parsedPatterns);  // Check the final parsed patterns

        let sensitiveWordsCount = 0;
        var defaulter = 0;

        // Function to mask sensitive words
        function maskSensitiveWord(word, type) {
            if (type === 'email') {
                const parts = word.split('@');
                const localPart = parts[0];
                const domainPart = parts[1];

                const maskedLocalPart = localPart.length > 2
                ? localPart.charAt(0) + '*'.repeat(localPart.length - 2) + localPart.slice(-1)
                : localPart; // If it's too short, don't mask
                return maskedLocalPart + '@' + domainPart;
            }

            if (type === 'phone') {
                return word.length > 2
                ? word.charAt(0) + '*'.repeat(word.length - 2) + word.charAt(word.length - 1)
                : word; // If it's too short, don't mask
            }

            if (type === 'url') {
                // Mask URL (e.g., https://www.******.com)
                const urlParts = word.split('://');
                return urlParts[0] + '://*****.com';
            }

            if (type === 'offensive-word') {
                // Mask offensive words
                const firstChar = word.charAt(0);
                const lastChar = word.charAt(word.length - 1);
                const masked = firstChar + '*'.repeat(word.length - 2) + lastChar;
                return masked;
            }

            return word; // Return word unchanged if no pattern matches
        }

        // Function to process the message and mask sensitive words
        function processMessage(message) {
            let maskedMessage = message;
            sensitiveWordsCount = 0; // Reset the count at the start of each message processing

            // Check and mask all sensitive words based on patterns
            parsedPatterns.forEach((pattern) => {
                // Handle 'phone' and 'email' types when pattern is "true"
                if (pattern.pattern === 'true') {
                    // Phone regex pattern
                    if (pattern.type === 'phone') {
                        const phoneRegex = /\b(\+91|91)?\d{10}\b/g;
                        const matches = message.match(phoneRegex);
                        if (matches) {
                            defaulter = 1;
                            sensitiveWordsCount += matches.length;
                            matches.forEach((match) => {
                                const maskedWord = maskSensitiveWord(match, 'phone');
                                maskedMessage = maskedMessage.replace(match, maskedWord);
                            });
                        }
                    }

                    // Email regex pattern
                    if (pattern.type === 'email') {
                        const emailRegex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/g;
                        const matches = message.match(emailRegex);
                        if (matches) {
                            defaulter = 1;
                            sensitiveWordsCount += matches.length;
                            matches.forEach((match) => {
                                const maskedWord = maskSensitiveWord(match, 'email');
                                maskedMessage = maskedMessage.replace(match, maskedWord);
                            });
                        }
                    }
                }

                // Offensive word pattern, if it is an array
                if (pattern.type === 'offensive-word' && Array.isArray(pattern.pattern)) {
                    const offensiveWordsRegex = new RegExp(`\\b(${pattern.pattern.join('|')})\\b`, 'gi');
                    const matches = message.match(offensiveWordsRegex);
                    if (matches) {
                        defaulter = 1;
                        sensitiveWordsCount += matches.length;
                        matches.forEach((match) => {
                            const maskedWord = maskSensitiveWord(match, 'offensive-word');
                            maskedMessage = maskedMessage.replace(match, maskedWord);
                        });
                    }
                }
            });

            return maskedMessage;
        }

// ----------------------------------------end-defaulter---------------------------



        const firestore = firebase.firestore();

        // Initialize Firebase updatechat document with initial duration
        const db = firebase.firestore();
        db.collection('updatechat').doc("{{ $chatId }}").set({
            duration: {{ $chatrequest->chat_duration }}
        }, { merge: true });

        // Function to send a message
        function sendMessage(senderId, receiverId, message, isEndMessage, attachementPath) {
            const chatRef = firestore.collection('chats').doc(`${receiverId}_${senderId}`).collection('userschat').doc(
                receiverId).collection('messages');
            const timestamp = new Date();
            // Generate a unique ID for the message
            const messageId = chatRef.doc().id;

            chatRef.doc(messageId).set({
                    id: null,
                    createdAt: timestamp,
                    invitationAcceptDecline: null,
                    isDelete: false,
                    isEndMessage: isEndMessage,
                    isRead: false,
                    messageId: messageId,
                    reqAcceptDecline: null,
                    status: null,
                    updatedAt: timestamp,
                    url: null,
                    userId1: senderId,
                    userId2: receiverId,
                    message: message,
                    attachementPath: attachementPath, // Pass attachementPath to the message object
                })
                .then(() => {
                    // console.log("Message sent with ID: ", messageId);
                })
                .catch((error) => {
                    console.error("Error sending message: ", error);
                });
        }


        $(document).on('keydown', '#fileDisplay', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    $('#sendButton').click();
                }
        });

        $(document).ready(function() {
            $(document).on('click', '#sendButton', function() {
                // ---------------------store-defaulter-message------------------------------------------------
                const originalMessage = $('#fileDisplay').val();  // Get the original message
                const processedMessage = processMessage(originalMessage);  // Process to mask sensitive words
                $('#fileDisplay').val(processedMessage);

                if(defaulter == 1){
                    $.ajax({
                        url: '/store-defaulter-message',  // Replace with your actual backend URL
                        type: 'POST',
                        data: {
                            message: originalMessage,
                            userId: userId,
                            type: 'user',
                            sender_type: 'user',
                            sender_id: userId,
                            receiver_type: 'astrologer',
                            receiver_id: astrologerUserId,
                            _token: '{{ csrf_token() }}'  // CSRF token for Laravel (or your backend framework)
                        },
                        success: function(response) {
                            defaulter = 0;
                            toastr.warning(response.message);

                        },
                        error: function(xhr, status, error) {
                            alert('Error sending message: ' + error);
                        }
                    });

                }else{
                    defaulter = 0;

                }
            // --------------------end-defaulter-message-store----------------------------------------------

                const messageInput = $(this).closest('.input-group').find('.form-control');
                // console.log('Input field value:', messageInput.val());

                const message = messageInput.val() ? messageInput.val().trim() : '';
                // console.log('Trimmed message:', message);

                const fileInput = $(this).closest('.input-group').find('#fileInput')[0];
                const file = fileInput.files[0]; // Get the selected file

                if (message !== '' || file) { // Check if message or file is not empty
                    console.log('Message or file is not empty');

                    // Check if file is present, if so, upload it to Firebase Storage
                    if (file) {
                        const storageRef = firebase.storage().ref();
                        const fileName = `${astrologerId}_${userId}/${file.name}`;
                        const fileRef = storageRef.child(fileName);

                        fileRef.put(file).then((snapshot) => {
                            console.log('File uploaded successfully');
                            snapshot.ref.getDownloadURL().then((downloadURL) => {
                                // console.log('File download URL:', downloadURL);
                                // Send the message as null when a file is attached
                                sendMessage(userId, astrologerId, null, false,
                                    downloadURL); // Pass download URL to sendMessage
                                messageInput.val('');
                                fileInput.value = ''; // Clear file input after sending
                            });
                        }).catch((error) => {
                            console.error('Error uploading file:', error);
                        });

                    } else {
                        // If no file, simply send the message
                        sendMessage(userId, astrologerId, message, false,
                            ''); // Pass empty string as attachment path
                        messageInput.val('');
                    }
                } else {
                    toastr.error('Message and file are empty');
                }
            });
        });


        let chatOpenedTime = Date.now();

        function fetchAndRenderMessages(receiverId, senderId) {
            const senderChatRef = firestore.collection('chats').doc(`${receiverId}_${senderId}`).collection('userschat')
                .doc(receiverId).collection('messages');

            senderChatRef.orderBy('createdAt', 'asc').onSnapshot(snapshot => {
                snapshot.docChanges().forEach(change => {
                    const message = change.doc.data();
                    if (change.type === 'added') {

                        renderMessage(message, receiverId);
                        scrollToBottom();

                        if (message.createdAt && message.createdAt.toMillis() > chatOpenedTime) {
                            if (message.isEndMessage) {
                                clearInterval(timerInterval);
                                endChat();
                            }
                        }
                    }

                });
            });
        }

        function scrollToBottom() {
            const chatMessagesContainer = document.querySelector('.chat-messages');
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        }



        function renderMessage(message, receiverId) {
            const chatMessagesContainer = document.querySelector('.chat-messages');
            const isScrolledToBottom = chatMessagesContainer.scrollHeight - chatMessagesContainer.clientHeight <=
                chatMessagesContainer.scrollTop + 1;

            const messageElement = document.createElement('div');
            messageElement.classList.add('chat-message');

            const timestamp = message.createdAt.toDate();
            const formattedTime = timestamp.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit'
            });



            var newupdateTime = new Date("{{ $chatrequest->updated_at }}").toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
            var newtimestamp = timestamp.toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
            @if($getAstrologer['recordList'][0]['profileImage'])
                var astroprofile = "{{ asset($getAstrologer['recordList'][0]['profileImage']) }}";
            @else
                var astroprofile = "{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img-new.png') }}";
            @endif

            @if(authcheck()['profile'])
            var userprofile="{{ asset(authcheck()['profile']) }}";
            @else
            var userprofile="{{ asset('public/frontend/onlinepujacdn/dashaspeaks/web/content/images/user-img-new.png') }}";
            @endif

            if (message.isEndMessage == true) {
    messageElement.innerHTML = `
        <div class="chat-message chat-message-center d-flex m-3" style="justify-content: center;">
            <div class="color-red bg-pink rounded-pill border py-1 px-3 mr-3 mb-2 text-center" style="width: 60%;">
                ${convertToLink(message.message)}
            </div>
        </div>`;
            } else if (message.userId1 === receiverId) {
                // Message sent by the receiver, render on the left side
                messageElement.classList.add('chat-message-left');
                messageElement.innerHTML = `
                    <div  class="message-user-left-img">
                        <img src="${astroprofile}" class="rounded-circle mr-1" alt="Sender" width="40" height="40">
                        <strong>{{ $getAstrologer['recordList'][0]['name'] }}</strong>
                            <small>${formattedTime}</small>
                    </div>
                    <div class="message-user-left-text">
                        ${message.attachementPath ? renderAttachment(message.attachementPath) : `<strong>${convertToLink(message.message)}</strong>`}
                    </div>`;
            } else {
                // Message sent by the sender, render on the right side
                messageElement.classList.add('chat-message-right');
                messageElement.innerHTML = `
                    <div class="message-user-right-img ">
                        <strong>You</strong>
                          <small>${formattedTime}</small>
                        <img src="${userprofile}" class="rounded-circle mr-1" alt="You" width="40" height="40">
                    </div>
                    <div class="message-user-right-text">
                        ${message.attachementPath ? renderAttachment(message.attachementPath) : `<strong>${convertToLink(message.message)}</strong>`}
                    </div>`;
            }
            if (message.isEndMessage == true && (newtimestamp >= newupdateTime)) {
                clearInterval(timerInterval);
                // endChat();
                // window.location.href = "{{ route('front.home') }}"; // Reload the page
            }


            chatMessagesContainer.appendChild(messageElement);

            if (isScrolledToBottom) {
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
            }
        }


         function convertToLink(message) {
            const urlPattern = /(https?:\/\/[^\s]+)/g;
            return message.replace(urlPattern, '<a href="$1" target="_blank">$1</a>');
        }


        function renderAttachment(attachementPath) {
            if (!attachementPath) return ''; // No attachment provided

            // Remove query parameters from the URL
            const filePathWithoutParams = attachementPath.split('?')[0];

            // Extract the file extension
            const fileExtension = filePathWithoutParams.split('.').pop().toLowerCase();

            // List of image file extensions
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

            // If the file is an image, render it as an image
            if (imageExtensions.includes(fileExtension)) {
                return `<img src="${attachementPath}" style="max-height:250px;" alt="Attachment" class="img-fluid">`;
            }

            // Define icons for different file types
            const fileIcons = {
                pdf: 'fa-file-pdf',
                xlsx: 'fa-file-excel',
                xls: 'fa-file-excel',
                docx: 'fa-file-word',
                doc: 'fa-file-word',
                txt: 'fa-file-alt',
                csv: 'fa-file-csv',
                zip: 'fa-file-archive',
                // Add more file types as needed
            };

            // Default icon for unknown file types
            const defaultIcon = 'fa-file';

            // Get the appropriate icon for the file type
            const fileIcon = fileIcons[fileExtension] || defaultIcon;

            // Render the file attachment with an icon and download link
            return `
                <div class="file-attachment" style="cursor: pointer;" onclick="downloadFile('${attachementPath}')">
                    <i class="fas ${fileIcon}"></i>
                    <p class="mt-2">Attachment</p>
                </div>`;
        }
        // Function to check if a URL points to an image
        function isImage(url) {
            return /\.(jpeg|jpg|gif|png)$/i.test(url);
        }


        document.addEventListener('DOMContentLoaded', function() {
            fetchAndRenderMessages(astrologerId, userId);
        });


        function downloadFile(url) {
            window.open(url, '_blank');

        }
    </script>


    <script>
        let timerInterval;
        var chatDuration;
        var serverTime;
        var remainingTime;
        let chatEnded = false;

        // Update the timer UI
        function updateTimer() {
            if(chatEnded)
                return false;

            let minutes = Math.floor(remainingTime / 60);
            let seconds = remainingTime % 60;
            let formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            document.getElementById('remainingTime').innerHTML = formattedTime + '&nbsp;&nbsp;&nbsp;';

        }

        // Function to end chat
        function endChat() {
            if (chatEnded) {
                return;
            }
            chatEnded = true;

            @php
                use Symfony\Component\HttpFoundation\Session\Session;
                $session = new Session();
                $token = $session->get('token');
            @endphp

            var formData = $('#endChatForm').serialize();

            $.ajax({
                url: "{{ route('api.endChatRequest', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Chat Ended Successfully');
                    sendMessage(userId, astrologerId, "{{ authcheck()['name'] }} -> Chat Ended", true, null);
                    window.location.href = "{{ route('front.home') }}";
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        }

        $(document).ready(function() {
            let updateTime = new Date("{{ $chatrequest->updated_at }}").getTime();
            chatDuration = {{ $chatrequest->chat_duration }};
            serverTime = '';
            remainingTime = '';
            // Fetch server time and then start the timer
            $.get("{{ route('front.getDateTime') }}", function(response) {
                // Assuming the response contains the server time in 'Y-m-d H:i:s' format
                serverTime = new Date(response).getTime();

                // Calculate elapsed time and remaining time
                let elapsedTime = Math.floor((serverTime - updateTime) / 1000);
                remainingTime = chatDuration - elapsedTime;

                // Ensure remainingTime is not negative
                if (remainingTime < 0) {
                    remainingTime = 0;
                }

                startTimer();

            }).fail(function() {
                console.error("Error fetching server time");
            });

            function startTimer(){
                setupFirebaseListener();
                timerInterval = setInterval(function() {
                    if(chatEnded)
                        return false;

                    remainingTime--; // Decrement remaining time

                    if (remainingTime <= 0) {
                        remainingTime = 0; // Prevent negative time
                        clearInterval(timerInterval);
                        endChat(); // Call endChat if time is up
                    }

                    // Update the timer UI
                    updateTimer();

                      if(remainingTime == 90){
                       $('#insufficientTopUpModal').modal('show');
                    }
                    if(remainingTime == 30){
                       $('#insufficientTopUpModal').modal('show');
                    }

                    // fetchCurrentChatDuration();

                     let totalSeconds = chatDuration - remainingTime;
                    $("#endChat").prop("disabled", totalSeconds < 60);

                 }, 1000);

            }

            function setupFirebaseListener() {
                const chatId = '{{ $chatId }}'; // Your Laravel chat ID
                const db = firebase.firestore();

                // Listen to the specific document in 'updatechat' collection
                db.collection('updatechat').doc(chatId)
                    .onSnapshot((doc) => {
                        if (doc.exists) {
                            const firebaseData = doc.data();
                            const newDuration = firebaseData.duration;
                            const previousDuration = chatDuration;

                            // Update chatDuration
                            chatDuration = newDuration;

                            // Adjust remaining time only if duration increased
                            if (chatDuration > previousDuration) {
                                const additionalTime = chatDuration - previousDuration;
                                remainingTime += additionalTime;
                                console.log("Added additional time from Firebase:", additionalTime);

                                // Update UI immediately
                                updateTimer();
                            }
                        }
                    }, (error) => {
                        console.error("Firebase listener error:", error);
                    });
            }

        });

        $(document).ready(function() {
            $('#endChat').click(function(e) {
                e.preventDefault();
                endChat();
            });
        });



        $(window).on('beforeunload', function () {
            if (!chatEnded) {
                sendMessage(userId, astrologerId, "{{ authcheck()['name'] }} -> Chat Ended", true, null);
                endChat();
            }
        });


        $(document).ready(function () {
        $('#intakeBtn').click(function (e) {
            e.preventDefault();

            // Show loader and hide button
            $('#intakeBtn').hide();
            $('#loaderintakeBtn').show();

            setTimeout(function () {
                $('#intakeBtn').show();
                $('#loaderintakeBtn').hide();
            }, 3000);

            // Variables from PHP
            var astrocharge = {{ $getAstrologer['recordList'][0]['charge'] }};
            var wallet_amount = {{ authcheck() ? $walletAmount : 0 }};
            var chatId = "{{ $chatId }}";
            var token = "{{ session('token') }}";
            var astrologerId = "{{ $getAstrologer['recordList'][0]['id'] }}";
            var userId = {{ authcheck() ? authcheck()['id'] : 'null' }};

            // AJAX to get current chat duration
            $.ajax({
                url: "{{ route('api.getcurrentDuration', ['chatId' => $chatId]) }}",
                type: 'POST',
                success: function (response) {
                    if (response.chatDuration) {
                        // Calculate the remaining wallet amount
                        let chatDurationMinutes = response.chatDuration / 60;
                        let remainingWalletAmount = wallet_amount - (chatDurationMinutes * astrocharge);
                        remainingWalletAmount = remainingWalletAmount.toFixed(2);

                        // Form data
                        var formData = $('#intakeForm').serialize();
                        var urlParams = new URLSearchParams(formData);
                        var chat_duration = parseInt(urlParams.get('chat_duration'));
                        var chat_duration_minutes = Math.ceil(chat_duration / 60);
                        var total_charge = astrocharge * chat_duration_minutes;

                        console.log(remainingWalletAmount);

                        if (total_charge <= remainingWalletAmount) {
                            // Continue chat
                            $.ajax({
                                url: "{{ route('api.updatechatMinute') }}",
                                type: 'POST',
                                data: {
                                    chat_duration: chat_duration,
                                    chatId: chatId,
                                },
                                success: function () {
                                    toastr.success('Chat time updated successfully');
                                    remainingTime += chat_duration;
                                    chatDuration += chat_duration;
                                    updateTimer();
                                    // Update Firebase for astrologer sync
                                    const db = firebase.firestore();
                                    console.log("Updating Firebase with duration:", chatDuration);
                                    db.collection('updatechat').doc(chatId).set({
                                        duration: chatDuration
                                    }, { merge: true });
                                    $('#intake').modal('hide');
                                    $('.modal-backdrop').remove(); // Ensure backdrop removal
                                    $('body').removeClass('modal-open'); // Re-enable scrolling

                                },
                                error: function (xhr) {
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
                                    payment_for:"topupchat",
                                    durationchat: chat_duration,
                                    chatId: chatId,
                                },
                                success: function (response) {
                                      $('#intake').modal('hide');
                                    $('.modal-backdrop').remove(); // Ensure backdrop removal
                                    $('body').removeClass('modal-open'); // Re-enable scrolling
                                     window.open(response.url, '_blank', 'width=800,height=600,resizable=yes,scrollbars=yes');
                                },
                                error: function (xhr) {
                                    toastr.error(xhr.responseText);
                                },
                            });
                        }
                    } else {
                        toastr.error('Invalid chat duration.');
                    }
                },
                error: function (xhr) {
                    let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
                    toastr.error(errorMessage || 'An error occurred while fetching the chat duration.');
                },
            });
        });
    });


    </script>
@endsection
