@extends('frontend.astrologers.layout.master')

@section('title', 'Create Support Ticket')

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

    .create-ticket-wrapper {
        max-width: 700px;
        margin: 0 auto;
        padding: 2rem 0 3rem;
    }

    .page-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--ticket-primary);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: opacity 0.2s;
    }
    .page-back-link:hover { opacity: 0.75; text-decoration: none; color: var(--ticket-primary); }

    .create-hero {
        background: linear-gradient(135deg, #FF8C00 0%, #E07000 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem 1.8rem;
        color: #fff;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .create-hero::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.07);
    }
    .create-hero h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
    }
    .create-hero p {
        opacity: 0.85;
        margin: 0;
        font-size: 0.9rem;
    }

    .ticket-form-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 30px rgba(255, 140, 0, 0.08);
        padding: 2rem 2.5rem;
        border: 1px solid rgba(255, 140, 0, 0.12);
    }

    .form-group-custom {
        margin-bottom: 1.4rem;
    }
    .form-label-custom {
        font-size: 0.88rem;
        font-weight: 600;
        color: #3d3d6b;
        margin-bottom: 0.45rem;
        display: block;
    }
    .form-label-custom .required-star {
        color: #FF6B6B;
        margin-left: 2px;
    }

    .form-control-custom {
        width: 100%;
        border: 1.5px solid #ffe0b3;
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-size: 0.92rem;
        color: #1a1a2e;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #fffaf5;
        font-family: inherit;
    }
    .form-control-custom:focus {
        border-color: var(--ticket-primary);
        box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.12);
        background: #fff;
    }
    .form-control-custom.is-invalid {
        border-color: #FF6B6B;
        box-shadow: 0 0 0 3px rgba(255,107,107,0.1);
    }
    .form-control-custom::placeholder { color: #c8b090; }

    select.form-control-custom {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23FF8C00' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 2.5rem;
    }

    textarea.form-control-custom {
        min-height: 130px;
        resize: vertical;
    }

    .error-msg {
        color: #FF6B6B;
        font-size: 0.8rem;
        margin-top: 0.3rem;
        display: block;
    }

    .info-tip {
        background: rgba(255, 140, 0, 0.07);
        border-left: 3px solid var(--ticket-primary);
        border-radius: 8px;
        padding: 0.7rem 1rem;
        font-size: 0.84rem;
        color: #b36200;
        margin-bottom: 1.6rem;
    }
    .info-tip i { margin-right: 6px; }

    .btn-submit-ticket {
        background: linear-gradient(135deg, #FF8C00, #E07000);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2.2rem;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.25s;
        box-shadow: 0 4px 20px rgba(255, 140, 0, 0.3);
    }
    .btn-submit-ticket:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(255, 140, 0, 0.4);
        color: #fff;
    }
    .btn-submit-ticket:active {
        transform: translateY(0);
    }

    .btn-cancel-ticket {
        background: transparent;
        color: #8888aa;
        border: 1.5px solid #ffe0b3;
        border-radius: 12px;
        padding: 0.73rem 1.6rem;
        font-size: 0.92rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }
    .btn-cancel-ticket:hover {
        border-color: var(--ticket-primary);
        color: var(--ticket-primary);
        text-decoration: none;
    }

    .char-counter {
        font-size: 0.78rem;
        color: #a0a0b8;
        text-align: right;
        margin-top: 0.3rem;
    }
</style>

<div class="container">
    <div class="create-ticket-wrapper">

        {{-- Back Link --}}
        <a href="{{ route('front.astrologers.tickets') }}" class="page-back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to My Tickets
        </a>

        {{-- Hero --}}
        <div class="create-hero">
            <h2><i class="fa-solid fa-headset me-2"></i>New Support Ticket</h2>
            <p>Describe your issue below and our support team will respond as soon as possible.</p>
        </div>

        {{-- Form Card --}}
        <div class="ticket-form-card">

            <div class="info-tip">
                <i class="fa-solid fa-circle-info"></i>
                Please provide as much detail as possible so our team can help you quickly.
            </div>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('front.astrologers.tickets.store') }}" method="POST" id="ticketForm">
                @csrf

                {{-- Category --}}
                <div class="form-group-custom">
                    <label class="form-label-custom" for="helpSupportId">
                        Support Category <span class="required-star">*</span>
                    </label>
                    <select name="helpSupportId" id="helpSupportId"
                        class="form-control-custom {{ $errors->has('helpSupportId') ? 'is-invalid' : '' }}">
                        <option value="">— Select a category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('helpSupportId') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('helpSupportId')
                        <span class="error-msg"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                {{-- Subject --}}
                <div class="form-group-custom">
                    <label class="form-label-custom" for="subject">
                        Subject <span class="required-star">*</span>
                    </label>
                    <input type="text" name="subject" id="subject"
                        class="form-control-custom {{ $errors->has('subject') ? 'is-invalid' : '' }}"
                        placeholder="Brief summary of your issue"
                        value="{{ old('subject') }}"
                        maxlength="255"
                        oninput="updateCounter(this, 'subjectCounter', 255)">
                    <div class="char-counter">
                        <span id="subjectCounter">{{ strlen(old('subject', '')) }}</span>/255
                    </div>
                    @error('subject')
                        <span class="error-msg"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="form-group-custom">
                    <label class="form-label-custom" for="description">
                        Description <span class="required-star">*</span>
                    </label>
                    <textarea name="description" id="description"
                        class="form-control-custom {{ $errors->has('description') ? 'is-invalid' : '' }}"
                        placeholder="Describe your issue in detail. Include steps to reproduce, what you expected, and what actually happened."
                        oninput="updateCounter(this, 'descCounter', 5000)"
                        maxlength="5000">{{ old('description') }}</textarea>
                    <div class="char-counter">
                        <span id="descCounter">{{ strlen(old('description', '')) }}</span>/5000
                    </div>
                    @error('description')
                        <span class="error-msg"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
                    <button type="submit" class="btn-submit-ticket" id="submitBtn">
                        <i class="fa-solid fa-paper-plane"></i> Submit Ticket
                    </button>
                    <a href="{{ route('front.astrologers.tickets') }}" class="btn-cancel-ticket">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function updateCounter(el, counterId, max) {
        document.getElementById(counterId).textContent = el.value.length;
    }

    document.getElementById('ticketForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
    });
</script>

@endsection
