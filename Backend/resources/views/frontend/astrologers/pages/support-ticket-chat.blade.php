@extends('frontend.astrologers.layout.master')

@section('title', 'Ticket Chat - #' . $data['ticketNumber'])

@section('content')

<style>
    :root {
        --chat-primary: #FF8C00;
        --chat-primary-dark: #E07000;
        --chat-accent: #43E97B;
        --chat-bg: #fff8f0;
        --chat-card-bg: #ffffff;
    }

    .chat-page-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 1.5rem 0 3rem;
    }

    .chat-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--chat-primary);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1rem;
        transition: opacity 0.2s;
    }
    .chat-back-link:hover { opacity: 0.75; text-decoration: none; color: var(--chat-primary); }

    .chat-header-card {
        background: linear-gradient(135deg, #FF8C00 0%, #E07000 100%);
        border-radius: 20px 20px 0 0;
        padding: 1.3rem 1.8rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .chat-header-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 130px; height: 130px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .chat-header-card .ticket-info h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
    }
    .chat-header-card .ticket-info .ticket-num {
        font-size: 0.82rem;
        opacity: 0.85;
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 2px 10px;
        display: inline-block;
    }
    .chat-header-card .ticket-info .ticket-status-inline {
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 20px;
        padding: 3px 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-left: 0.5rem;
    }
    .status-inline-WAITING  { background: #FFF3CD; color: #856404; }
    .status-inline-OPEN     { background: #D1ECF1; color: #0C5460; }
    .status-inline-CLOSED   { background: #D4EDDA; color: #155724; }
    .status-inline-PAUSED   { background: #F8D7DA; color: #721C24; }

    .chat-body {
        background: var(--chat-bg);
        border-left: 1px solid rgba(255,140,0,0.12);
        border-right: 1px solid rgba(255,140,0,0.12);
        min-height: 400px;
        max-height: 500px;
        overflow-y: auto;
        padding: 1.5rem 1.5rem 1rem;
        scroll-behavior: smooth;
    }

    .chat-msg {
        display: flex;
        margin-bottom: 1rem;
        animation: fadeInUp 0.3s ease;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-msg.sent {
        justify-content: flex-end;
    }
    .chat-msg.received {
        justify-content: flex-start;
    }

    .chat-bubble {
        max-width: 75%;
        padding: 0.7rem 1rem;
        border-radius: 16px;
        font-size: 0.9rem;
        line-height: 1.45;
        word-break: break-word;
        position: relative;
    }

    .chat-msg.sent .chat-bubble {
        background: linear-gradient(135deg, #FF8C00, #E07000);
        color: #fff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 3px 12px rgba(255,140,0,0.25);
    }
    .chat-msg.received .chat-bubble {
        background: #fff;
        color: #1a1a2e;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid rgba(255,140,0,0.1);
    }

    .chat-bubble .chat-time {
        font-size: 0.68rem;
        opacity: 0.65;
        margin-top: 0.3rem;
        display: block;
        text-align: right;
    }
    .chat-msg.received .chat-bubble .chat-time {
        text-align: left;
    }

    .chat-bubble .chat-sender-label {
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
        opacity: 0.7;
    }
    .chat-msg.received .chat-sender-label {
        color: var(--chat-primary);
    }

    .chat-footer {
        background: #fff;
        border: 1px solid rgba(255,140,0,0.12);
        border-top: 2px solid rgba(255,140,0,0.1);
        border-radius: 0 0 20px 20px;
        padding: 1rem 1.3rem;
    }
    .chat-input-group {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }
    .chat-input {
        flex: 1;
        border: 1.5px solid #ffe0b3;
        border-radius: 14px;
        padding: 0.65rem 1rem;
        font-size: 0.92rem;
        outline: none;
        resize: none;
        min-height: 44px;
        max-height: 120px;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #fffaf5;
    }
    .chat-input:focus {
        border-color: var(--chat-primary);
        box-shadow: 0 0 0 3px rgba(255,140,0,0.12);
        background: #fff;
    }
    .chat-input::placeholder { color: #c8b090; }

    .btn-send-msg {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #FF8C00, #E07000);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.25s;
        box-shadow: 0 4px 15px rgba(255,140,0,0.35);
        flex-shrink: 0;
    }
    .btn-send-msg:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(255,140,0,0.45);
    }
    .btn-send-msg:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    .btn-send-msg i { font-size: 1.1rem; }

    .chat-closed-notice {
        text-align: center;
        padding: 1rem;
        color: #721C24;
        background: #F8D7DA;
        border-radius: 0 0 20px 20px;
        border: 1px solid rgba(255,140,0,0.12);
        border-top: none;
        font-size: 0.88rem;
        font-weight: 600;
    }
    .chat-closed-notice i { margin-right: 6px; }

    .chat-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #c8b090;
    }
    .chat-empty i {
        font-size: 2.5rem;
        margin-bottom: 0.8rem;
        display: block;
        color: #ffd6a0;
    }
    .chat-empty p {
        margin: 0;
        font-size: 0.9rem;
    }

    .sending-indicator {
        display: none;
        text-align: center;
        padding: 0.5rem;
        font-size: 0.82rem;
        color: var(--chat-primary);
    }
    .sending-indicator.active { display: block; }
</style>

<div class="container">
    <div class="chat-page-wrapper">

        {{-- Back Link --}}
        <a href="{{ route('front.astrologers.tickets') }}" class="chat-back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to My Tickets
        </a>

        {{-- Chat Header --}}
        <div class="chat-header-card">
            <div class="ticket-info d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3><i class="fa-solid fa-headset me-2"></i>{{ $data['subject'] }}</h3>
                    <span class="ticket-num">#{{ $data['ticketNumber'] }}</span>
                    <span class="ticket-status-inline status-inline-{{ $data['ticketStatus'] }}">
                        {{ ucfirst(strtolower($data['ticketStatus'])) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Chat Messages Body --}}
        <div class="chat-body" id="chatBody">
            @if(count($messages) > 0)
                @foreach($messages as $msg)
                    @php
                        // Mirror admin's chat.blade.php detection (exact inverse):
                        // Admin shows LEFT  when: userId2['stringValue'] == ticketId  → sent by user/astrologer
                        // Admin shows RIGHT when: userId2['stringValue'] != ticketId  → sent by admin
                        //
                        // Astrologer view (us):
                        //   RIGHT (orange) = sent by US       → userId2 is stringValue AND == ticketId
                        //   LEFT  (white)  = sent by ADMIN    → userId2 is integerValue (or stringValue != ticketId)

                        $userId2Str = $msg['fields']['userId2']['stringValue'] ?? null;
                        $isFromAstrologer = ($userId2Str !== null && (string) $userId2Str === (string) $data['ticketId']);

                        $msgText   = $msg['fields']['message']['stringValue'] ?? '';
                        $msgTime   = isset($msg['createTime'])
                                    ? \Carbon\Carbon::parse($msg['createTime'])->format('d M, h:i A')
                                    : '';
                        $msgStatus = $msg['fields']['status']['stringValue'] ?? '';
                    @endphp

                    @if($msgStatus === 'CLOSED' || $msgStatus === 'PAUSED')
                        <div class="chat-msg received">
                            <div class="chat-bubble" style="background: #fff3cd; color: #856404; border-color: #ffc107;">
                                <div class="chat-sender-label" style="color: #856404;">
                                    <i class="fa-solid fa-circle-info me-1"></i> System
                                </div>
                                {{ $msgText }}
                                <span class="chat-time">{{ $msgTime }}</span>
                            </div>
                        </div>
                    @elseif($isFromAstrologer)
                        <div class="chat-msg sent">
                            <div class="chat-bubble">
                                <div class="chat-sender-label" style="color: rgba(255,255,255,0.75);">You</div>
                                {{ $msgText }}
                                <span class="chat-time">{{ $msgTime }}</span>
                            </div>
                        </div>
                    @else
                        <div class="chat-msg received">
                            <div class="chat-bubble">
                                <div class="chat-sender-label">
                                    <i class="fa-solid fa-headset me-1"></i> Support Team
                                </div>
                                {{ $msgText }}
                                <span class="chat-time">{{ $msgTime }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="chat-empty">
                    <i class="fa-regular fa-comments"></i>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            @endif
        </div>

        {{-- Sending indicator --}}
        <div class="sending-indicator" id="sendingIndicator">
            <i class="fa-solid fa-spinner fa-spin me-1"></i> Sending...
        </div>

        {{-- Chat Input Footer --}}
        @if($data['ticketStatus'] !== 'CLOSED')
            <div class="chat-footer">
                <form id="chatForm" autocomplete="off">
                    @csrf
                    <input type="hidden" name="chatId" value="{{ $data['chatId'] }}">
                    <input type="hidden" name="ticketId" value="{{ $data['ticketId'] }}">
                    <input type="hidden" name="messageCount" value="{{ count($messages) }}">
                    <div class="chat-input-group">
                        <textarea class="chat-input" id="messageInput" name="message"
                            placeholder="Type your message..." rows="1"
                            onkeydown="handleEnterKey(event)"></textarea>
                        <button type="submit" class="btn-send-msg" id="sendBtn" title="Send Message">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="chat-closed-notice">
                <i class="fa-solid fa-lock"></i> This ticket is closed. You cannot send new messages.
            </div>
        @endif

    </div>
</div>

<script>
    // Auto-scroll to bottom on load
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
    });

    function scrollToBottom() {
        var chatBody = document.getElementById('chatBody');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    function handleEnterKey(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chatForm').dispatchEvent(new Event('submit'));
        }
    }

    // Auto-resize textarea
    var messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = '44px';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }

    // Handle form submission
    jQuery('#chatForm').submit(function(e) {
        e.preventDefault();

        var message = jQuery('#messageInput').val().trim();
        if (!message) return;

        // Capture all values BEFORE clearing the textarea
        var chatId   = jQuery('input[name="chatId"]').val();
        var ticketId = jQuery('input[name="ticketId"]').val();
        var token    = jQuery('input[name="_token"]').val();

        var sendBtn   = jQuery('#sendBtn');
        var indicator = jQuery('#sendingIndicator');

        sendBtn.prop('disabled', true);
        indicator.addClass('active');

        // Optimistically add message to chat
        var now = new Date();
        var timeStr = now.toLocaleString('en-IN', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: true });
        var msgHtml = '<div class="chat-msg sent">' +
            '<div class="chat-bubble">' +
                '<div class="chat-sender-label" style="color: rgba(255,255,255,0.75);">You</div>' +
                escapeHtml(message) +
                '<span class="chat-time">' + timeStr + '</span>' +
            '</div>' +
        '</div>';

        jQuery('.chat-empty').remove();
        jQuery('#chatBody').append(msgHtml);
        scrollToBottom();

        // Clear textarea only after capturing value
        jQuery('#messageInput').val('');
        if (messageInput) {
            messageInput.style.height = '44px';
        }

        jQuery.ajax({
            type: 'POST',
            url: "{{ route('front.astrologers.tickets.chat.send') }}",
            data: {
                _token:   token,
                message:  message,
                chatId:   chatId,
                ticketId: ticketId
            },
            dataType: 'JSON',
            success: function(data) {
                sendBtn.prop('disabled', false);
                indicator.removeClass('active');
                toastr.success('Message sent successfully');
            },
            error: function(xhr) {
                sendBtn.prop('disabled', false);
                indicator.removeClass('active');
                var errData = xhr.responseJSON;
                var errorMsg = 'Failed to send message';
                if (errData) {
                    if (errData.error) {
                        errorMsg = errData.error;
                    } else if (errData.errors) {
                        // Show first Laravel validation error
                        var first = Object.values(errData.errors)[0];
                        errorMsg = Array.isArray(first) ? first[0] : first;
                    }
                }
                toastr.error(errorMsg);
            }
        });
    });

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
</script>

@endsection
