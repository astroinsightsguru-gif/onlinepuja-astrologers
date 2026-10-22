# 🕉️ OnlinePuja.live — Master Ecosystem Repository

[![Production Status](https://img.shields.io/badge/Production-Live-success?style=for-the-badge&logo=google-chrome)](https://onlinepuja.live)
[![Backend](https://img.shields.io/badge/Backend-Laravel%2010%20(PHP%208.2%2F8.3)-red?style=for-the-badge&logo=laravel)](./Backend)
[![Mobile Apps](https://img.shields.io/badge/Mobile-Flutter%203.x-blue?style=for-the-badge&logo=flutter)](./apps)
[![AI Nervous System](https://img.shields.io/badge/AI%20Engine-Growth%20OS%20Autopilot-purple?style=for-the-badge)](./Backend/app/Brain)

> **Official Website:** [https://onlinepuja.live](https://onlinepuja.live)  
> **Production Server:** Hostinger Cloud VPS (`200.234.41.231`)  
> **AI Onboarding Guide:** See [`AI_CONTEXT.md`](./AI_CONTEXT.md) for complete technical architecture, credentials, and agent instructions.

---

## 📖 Overview

**OnlinePuja.live** is a comprehensive Sanatan Dharma spiritual and astrological platform connecting devotees globally with verified Vedic Purohits, holy temple rituals, certified astrologers, and consecrated spiritual items.

### Key Components

```
d:\Dev\onlinepuja-live
├── AI_CONTEXT.md                  # Comprehensive AI agent mental model & instructions
├── Backend/                       # Laravel 10 Web Application, REST APIs & Growth OS Brain
└── apps/                          # Flutter Mobile Monorepo
    ├── onlinepuja-customer/       # Devotee Mobile App (Android/iOS)
    ├── onlinepuja-astrologer/     # Astrologer Consultation App (Android/iOS)
    ├── onlinepuja-manager/        # Temple & Purohit Operations App (Android/iOS)
    └── shared/                    # Shared models, API client, and 15-language l10n
```

---

## 🌟 Key Features

1. **Sacred Temple Pujas & Live Darshan**:
   - Personalized Gotra Sankalpa performed live at Kashi Vishwanath, Mahakaleshwar Ujjain, Somnath, and Ayodhya.
   - High-definition Sankalp video proof delivered via WhatsApp.
   - Consecrated temple Mahaprasad couriered to devotees across India and worldwide.

2. **Real-Time Astrologer Consultations**:
   - WebRTC 1-on-1 Audio/Video calls via dedicated LiveKit Server (`wss://rtc.onlinepuja.live`).
   - Per-minute wallet billing with real-time timers and fraud prevention.

3. **Cosmic AI Astrologer (Acharya AI Twin)**:
   - 24/7 intelligent Vedic astrology chat powered by Parashari, KP, and Gochar transits.
   - Natural Devnagari Hindi voice calls.

4. **Growth AI OS & Marketing Brain**:
   - **Festival Campaign Radar**: Detects upcoming Hindu festivals (Dev Deepawali, Chhath Puja, Somvati Amavasya, etc.).
   - **Banner Studio**: Automatic PHP GD synthesis of 1200×630 OpenGraph banners and 1080×1080 square social banners.
   - **Video Reel Generator**: 1080×1920 MP4 video reels synthesized using AI visuals and FFmpeg.
   - **Autonomous SEO Blog Engine**: In-depth devotional articles published live to `/blog/{slug}`.
   - **B2B Partner Engine**: Google Maps lead ingestion, automated 25% revenue share tracking (`?partner=CODE`), and multi-channel partner broadcasts.
   - **Parallel Scheduling**: Schedule any upcoming festival or tithi to trigger milestone automations in parallel.

---

## 💻 Tech Stack

- **Backend**: Laravel 10, PHP 8.2/8.3, MySQL 8.4, Redis, Nginx
- **Mobile Apps**: Flutter 3.x, Dart, Riverpod / Provider, WebRTC (`livekit_client`)
- **Real-Time Streaming**: LiveKit Server (`200.234.41.231:7880`)
- **AI & Automation**: Pollinations AI, Gemini LLM, Brevo (Email), Evolution API (WhatsApp), OneSignal / FCM (Push)
- **Payment Gateways**: Cashfree PG, PayPage, Devotee Wallet System

---

## 🚀 Quick Start for Developers & AI Agents

Please refer to [`AI_CONTEXT.md`](./AI_CONTEXT.md) for full setup instructions, Git commands, database schema notes, and VPS deployment workflows.
