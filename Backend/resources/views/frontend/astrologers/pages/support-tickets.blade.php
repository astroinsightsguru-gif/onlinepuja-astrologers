@extends('frontend.astrologers.layout.master')

@section('title', 'My Support Tickets')

@section('content')

<style>
    :root {
        --ticket-primary: #FF8C00;
        --ticket-secondary: #FF6B6B;
        --ticket-accent: #43E97B;
        --ticket-dark: #1a1a2e;
        --ticket-card-bg: #ffffff;
        --ticket-border: rgba(255, 140, 0, 0.15);
    }

    .tickets-hero {
        background: linear-gradient(135deg, #FF8C00 0%, #E07000 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: #fff;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .tickets-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .tickets-hero::after {
        content: '';
        position: absolute;
        bottom: -30px; left: 40px;
        width: 100px; height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .tickets-hero h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    .tickets-hero p {
        opacity: 0.85;
        margin: 0;
        font-size: 0.95rem;
    }
    .btn-create-ticket {
        background: #fff;
        color: #FF8C00;
        font-weight: 700;
        border-radius: 12px;
        padding: 0.6rem 1.4rem;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.25s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .btn-create-ticket:hover {
        background: #fff5e6;
        color: #E07000;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .ticket-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--ticket-border);
        box-shadow: 0 2px 12px rgba(255,140,0,0.07);
        transition: box-shadow 0.25s, transform 0.25s;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .ticket-card:hover {
        box-shadow: 0 8px 30px rgba(255,140,0,0.13);
        transform: translateY(-2px);
    }
    .ticket-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.3rem 0.6rem;
        border-bottom: 1px solid #fef0e0;
    }
    .ticket-number {
        font-size: 0.78rem;
        font-weight: 700;
        color: #FF8C00;
        background: rgba(255,140,0,0.1);
        border-radius: 8px;
        padding: 3px 10px;
        letter-spacing: 0.5px;
    }
    .ticket-status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 20px;
        padding: 4px 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-WAITING  { background: #FFF3CD; color: #856404; }
    .status-OPEN     { background: #D1ECF1; color: #0C5460; }
    .status-CLOSED   { background: #D4EDDA; color: #155724; }
    .status-PAUSED   { background: #F8D7DA; color: #721C24; }

    .ticket-card-body {
        padding: 0.8rem 1.3rem 1rem;
    }
    .ticket-subject {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 0.3rem;
    }
    .ticket-desc {
        font-size: 0.87rem;
        color: #7a7a9d;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ticket-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 0.6rem;
        flex-wrap: wrap;
    }
    .ticket-meta-item {
        font-size: 0.78rem;
        color: #a0a0b8;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .ticket-meta-item i { font-size: 0.75rem; }

    .empty-state {
        text-align: center;
        padding: 3.5rem 1rem;
        background: #fff;
        border-radius: 20px;
        border: 2px dashed var(--ticket-border);
    }
    .empty-state .empty-icon {
        font-size: 3.5rem;
        color: #ffd6a0;
        margin-bottom: 1rem;
    }
    .empty-state h3 { color: #3d3d6b; font-weight: 600; }
    .empty-state p  { color: #9a9ab8; font-size: 0.9rem; }

    .pagination .page-link {
        border-radius: 8px;
        margin: 0 2px;
        color: #FF8C00;
        border-color: var(--ticket-border);
    }
    .pagination .page-item.active .page-link {
        background: #FF8C00;
        border-color: #FF8C00;
        color: #fff;
    }

    .btn-view-chat {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: linear-gradient(135deg, #FF8C00, #E07000);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.45rem 1.1rem;
        text-decoration: none;
        transition: all 0.25s;
        box-shadow: 0 3px 12px rgba(255,140,0,0.25);
    }
    .btn-view-chat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255,140,0,0.4);
        color: #fff;
        text-decoration: none;
    }
    .btn-view-chat i { font-size: 0.8rem; }
</style>

<div class="container py-4">

    {{-- Hero Banner --}}
    <div class="tickets-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1><i class="fa-solid fa-headset me-2"></i>My Support Tickets</h1>
            <p>Track all your support requests and their current status.</p>
        </div>
        <a href="{{ route('front.astrologers.tickets.create') }}" class="btn-create-ticket">
            <i class="fa-solid fa-plus"></i> New Ticket
        </a>
    </div>

    {{-- Ticket List --}}
    @if($tickets->count() > 0)
        @foreach($tickets as $ticket)
            <div class="ticket-card">
                <div class="ticket-card-header">
                    <span class="ticket-number">#{{ $ticket->ticketNumber }}</span>
                    <span class="ticket-status-badge status-{{ $ticket->ticketStatus }}">
                        @if($ticket->ticketStatus === 'WAITING')
                            <i class="fa-solid fa-clock me-1"></i>
                        @elseif($ticket->ticketStatus === 'OPEN')
                            <i class="fa-solid fa-envelope-open me-1"></i>
                        @elseif($ticket->ticketStatus === 'CLOSED')
                            <i class="fa-solid fa-check-circle me-1"></i>
                        @else
                            <i class="fa-solid fa-pause-circle me-1"></i>
                        @endif
                        {{ ucfirst(strtolower($ticket->ticketStatus)) }}
                    </span>
                </div>
                <div class="ticket-card-body">
                    <p class="ticket-subject">{{ $ticket->subject }}</p>
                    <p class="ticket-desc">{{ $ticket->description }}</p>
                    <div class="ticket-meta">
                        @if($ticket->categoryName)
                            <span class="ticket-meta-item">
                                <i class="fa-solid fa-tag"></i> {{ $ticket->categoryName }}
                            </span>
                        @endif
                        <span class="ticket-meta-item">
                            <i class="fa-regular fa-calendar"></i>
                            {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, h:i A') }}
                        </span>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <a href="{{ route('front.astrologers.tickets.chat', $ticket->id) }}" class="btn-view-chat">
                            <i class="fa-solid fa-comments"></i> View Chat
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $tickets->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-ticket"></i></div>
            <h3>No Support Tickets Yet</h3>
            <p>You haven't raised any support requests. If you need help, create a new ticket!</p>
            <a href="{{ route('front.astrologers.tickets.create') }}" class="btn-create-ticket mx-auto mt-2 d-inline-flex">
                <i class="fa-solid fa-plus"></i> Create Your First Ticket
            </a>
        </div>
    @endif

</div>

<script>
    $(document).ready(function() {
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>

@endsection
