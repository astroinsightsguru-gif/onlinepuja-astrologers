# 🕉️ OnlinePuja.live — AI Onboarding & Master Architecture Context

> **Audience:** Any new AI Coding Assistant (Antigravity, Cursor, Claude, ChatGPT, GitHub Copilot) or Software Engineer.  
> **Purpose:** Provide an instantaneous, single-glance mental model of the entire project ecosystem, tasks, goals, credentials, tech stack, and workflows.  
> **Last Updated:** October 2026  
> **Production URL:** [https://onlinepuja.live](https://onlinepuja.live)

---

## ⚡ 60-Second Executive Summary

**OnlinePuja.live** is an Indian spiritual, devotional, and astrological super-platform catering to millions of Sanatan Dharma devotees worldwide. The ecosystem bridges devotees with authentic temple rituals, verified Vedic astrologers, certified gemstones/samagri, and cutting-edge autonomous AI astrology.

### 4 Core Pillars:
1. **Sacred Temple Pujas & Live Sankalp Darshan**: Booking authentic Vedic Havans and personalized Gotra Sankalpas at holy pilgrimage teerthas (Kashi/Varanasi, Ujjain, Somnath, Ayodhya) with sanctified Mahaprasad home delivery.
2. **Live Astrologer Consultation (WebRTC)**: 1-on-1 audio & video consultations between devotees and certified astrologers, powered by wallet per-minute billing and LiveKit WebRTC.
3. **Cosmic AI Astrologer (Acharya AI Twin & Voice Calls)**: 24/7 intelligent LLM-powered Vedic horoscope predictions, Kundali analysis, Gochar transits, and fluent Devnagari voice calls.
4. **Growth AI OS & Marketing Brain**: An autonomous festival campaign nerve center that orchestrates multi-channel marketing campaigns, synthesizes HD promotional banners, generates 1080×1920 MP4 video reels, publishes SEO blog guides, and runs a national B2B partner distribution network.

---

## 📂 Monorepo Structure

```
d:\Dev\onlinepuja-live
├── README.md                      # Project root documentation
├── AI_CONTEXT.md                  # This file: AI agent mental model & guidelines
│
├── Backend/                       # Laravel 10 Full-Stack Web, Admin & API Engine
│   ├── app/
│   │   ├── AI/                    # VedicPromptEnhancer, AI Providers (Pollinations, OmniRoute)
│   │   ├── Brain/                 # AutopilotEngine, ContentPipeline, CommandRouter, Vault
│   │   ├── Console/Commands/      # FestivalRadar, Sitemap, B2B Dispatch, Shorts generator
│   │   ├── Http/Controllers/
│   │   │   ├── Admin/GrowthOsController.php # Growth AI OS & Marketing Brain API
│   │   │   ├── API/               # Flutter client endpoints (User, Astrologer, AiTwin)
│   │   │   └── frontend/          # Web frontend controllers (Puja, Blog, Kundali)
│   │   ├── Services/              # FestivalCampaignEngine, BannerStudioService, Brevo, Evolution
│   │   └── Social/                # Multi-channel publishers (Telegram, Meta, etc.)
│   ├── resources/views/pages/growth-os/ # Marketing, Autopilot, AI, SEO, Content Cockpits
│   └── routes/                    # api.php, web.php, website.php
│
└── apps/                          # Unified Flutter Mobile Monorepo
    ├── onlinepuja-customer/       # Devotee Android/iOS App (live.onlinepuja.app)
    ├── onlinepuja-astrologer/     # Astrologer Partner App (live.onlinepuja.astro)
    ├── onlinepuja-manager/        # Operations, Purohit & Temple Dispatch App
    ├── shared/ (op_shared)        # Shared API client, 15-language l10n, models
    └── PROJECT_ONLINEPUJA_LIVE.md # Historical detailed system architecture
```

---

## 🖥️ Production Infrastructure & Credentials

| Resource | Specification | Details / Notes |
|:---|:---|:---|
| **Production Server** | Hostinger Cloud VPS (`200.234.41.231`) | Ubuntu Linux, Root access via SSH key `C:\Users\tripc\.ssh\id_rsa` |
| **Production Webroot** | `/home/onlinepujalive/htdocs/onlinepuja.live` | Nginx reverse proxy + PHP 8.2/8.3 FPM |
| **MySQL Database** | Host: `127.0.0.1:3306`, DB: `onlinepujaastro` | User: `opastro`, Password: `l7wb8ciFmVHc2Qevy58XEVsU` |
| **LiveKit WebRTC** | `200.234.41.231:7880` (WSS: `wss://rtc.onlinepuja.live`) | Real-time audio/video streaming server |
| **Domain & CDN** | Cloudflare SSL + Cache Proxy | Origin SSL, port 443 |

---

## 🛠️ Critical Tooling & Execution Gotchas (Must Read for AI)

1. **Git Execution on Windows Host**:
   - `git` is NOT in the global Windows `%PATH%`.
   - The verified Git binary is at: `C:\src\flutter\bin\mingit\cmd\git.exe`.
   - Always run Git commands via:
     ```powershell
     & "C:\src\flutter\bin\mingit\cmd\git.exe" -C "D:\Dev\onlinepuja-live" <command>
     ```
2. **Three Git Remotes**:
   - `origin`: `https://github.com/astroinsightsguru-gif/onlinepuja-users.git`
   - `astrologers`: `https://github.com/astroinsightsguru-gif/onlinepuja-astrologers.git`
   - `manager`: `https://github.com/astroinsightsguru-gif/onlinepuja-manager.git`
   - When pushing commits, push to all 3 remotes:
     ```powershell
     git push manager main; git push origin main; git push astrologers main
     ```
3. **Deploying Backend Changes to VPS**:
   - Transfer files via SCP:
     ```powershell
     scp -i C:\Users\tripc\.ssh\id_rsa <LocalPath> root@200.234.41.231:/home/onlinepujalive/htdocs/onlinepuja.live/<RemotePath>
     ```
   - Clear caches immediately after deployment:
     ```bash
     php artisan view:clear && php artisan route:clear
     ```
4. **Key Database Nuances**:
   - `puja_orders` foreign key to user is `user_id` (lowercase with underscore).
   - `blogs` table uses `blogImage` (landscape 1200×630) and `previewImage` (square 1080×1080).
   - `content_plans` does not have a `slug` column (stores `title`, `channel`, `pillar`, `status`, `posted_url`, `payload`).
   - `b2b_leads` table stores national travel agents & astrologers for B2B puja resale (tracks `partner_code`, `commission_rate: 25.00`, `total_bookings`, `total_revenue_earned`).

---

## 🚀 Growth AI OS & Marketing Automation Architecture

The Growth Hub (`/admin/growth-os/marketing`) contains:
- **`FestivalCampaignEngine.php`**: Orchestrates master campaigns for upcoming Hindu festivals (Dev Deepawali, Chhath Puja, Somvati Amavasya, etc.).
- **`BannerStudioService.php`**: Synthesizes GD banners on the fly (1200×630 landscape & 1080×1080 square).
- **Video Reel Generator**: Generates 1080×1920 MP4 vertical reels using Pollinations AI + FFmpeg.
- **Autonomous SEO Blog Engine**: Writes and publishes 1,600+ word devotional articles with Sanskrit Shlokas, Muhurat, and FAQs directly to `/blog/{slug}`.
- **Parallel Multi-Festival Radar**: Allows scheduling custom events via modal, executing parallel campaigns with T-14, T-7, T-3, T-1, and Day-0 automated milestones.
- **B2B Partner Engine**: Google Maps lead ingestion, automated 25% revenue share tracking via `?partner=CODE`, and WhatsApp/Email broadcast proposals.

---

## 🎯 Active Goals & Roadmap

1. **Dev Deepawali & Maha Deepdaan 2026 in Varanasi (Nov 24, 2026)**:
   - Target: 1,000+ remote devotee bookings & ₹5,00,000+ revenue via direct + B2B distribution.
2. **Parallel Festival Pipelines**:
   - Continuous automatic asset generation and promotion for upcoming festivals (Karwa Chauth, Dhanteras, Diwali, Chhath Puja, Dev Deepawali, Somvati Amavasya).
3. **App Store & Play Store Submissions**:
   - Continuous CI/CD deployment of Customer, Astrologer, and Manager Flutter apps.
