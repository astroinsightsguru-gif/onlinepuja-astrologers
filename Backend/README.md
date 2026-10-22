# 🕉️ OnlinePuja.live — Laravel 10 Backend & Growth AI OS

This directory contains the central web application, admin console, REST APIs for the mobile apps, and the **Growth AI OS Autonomous Marketing Brain**.

---

## 🏛️ Architecture Highlights

### 1. Growth AI OS & Master Brain (`app/Http/Controllers/Admin/GrowthOsController.php`)
- **Marketing Cockpit (`/admin/growth-os/marketing`)**:
  - Festival Campaign Radar (`app/Services/FestivalCampaignEngine.php`)
  - Banner Studio (`app/Services/BannerStudioService.php`) — GD 1200x630 & 1080x1080
  - Video Reel Synthesizer — Pollinations + FFmpeg 1080x1920 MP4
  - SEO Blog Publisher — writes directly to `blogs` and `content_plans`
  - B2B Partner Hub — Google Maps scraper ingestion + 25% revenue share tracking
  - Evolution API WhatsApp integration (QR + pairing code)
  - Custom Event Parallel Scheduler
- **Autopilot Cockpit (`/admin/growth-os/autopilot`)**:
  - 5 autonomous loops: Gochar transit drip, Vedic Panchang, Festival reminders, AI Twin fallback, Churn recovery.
- **AI Engine (`/admin/growth-os/ai`)**:
  - AI Swarm, prompt customization, and LLM provider fallbacks.

### 2. Client Mobile APIs (`routes/api.php`)
- Devotee User APIs (`app/Http/Controllers/API/User/`)
- Astrologer APIs & Session Tracking (`app/Http/Controllers/API/Astrologer/`)
- AI Twin & Vedic Chat (`app/Http/Controllers/API/Astrologer/AiTwinController.php`)
- LiveKit WebRTC Token Generator (`app/Http/Controllers/API/User/LiveKitController.php`)

### 3. Public Web Frontend (`routes/website.php` & `routes/web.php`)
- Homepage, Kundali calculation, Panchang widgets
- Puja Catalog & Booking: `/pujas`, `/pujas/{slug}`, `/puja-checkout/{id}`
- B2B Partner tracking: automatically credits 25% commission when user lands on `/pujas/{slug}?partner=B2B-CODE`
- Devotional Blogs: `/blog/{slug}`

---

## ⚙️ Cron Schedule (`app/Console/Kernel.php`)

```php
// Daily Festival Campaign Radar (WhatsApp, Brevo Email, Push)
$schedule->command('festivals:campaign-radar --channels=whatsapp,email,push')->dailyAt('06:30');

// Autonomous Daily Gochar & Panchang Drips
$schedule->command('autopilot:run --loop=gochar')->dailyAt('06:30');
$schedule->command('autopilot:run --loop=panchang')->dailyAt('06:00');

// Recover Abandoned Puja Bookings
$schedule->command('pujas:recover-abandoned')->hourly();
```

---

## 🚀 Deployment Workflow to Production VPS

```bash
# 1. SCP modified files to VPS
scp -i ~/.ssh/id_rsa <LocalFile> root@200.234.41.231:/home/onlinepujalive/htdocs/onlinepuja.live/<RemoteFile>

# 2. Clear view and route caches on VPS
ssh root@200.234.41.231 "cd /home/onlinepujalive/htdocs/onlinepuja.live && php artisan view:clear && php artisan route:clear"
```
