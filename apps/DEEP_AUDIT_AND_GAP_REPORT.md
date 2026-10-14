# 🕉️ OnlinePuja.live — Deep Audit Report & Step-by-Step Execution Checklist

> **Scope**: End-to-end technical, operational, SEO/marketing, payments, and product audit of OnlinePuja.live.  
> **Date**: October 14, 2026  
> **Production Server**: Hostinger Cloud VPS (`200.234.41.231`) | `https://onlinepuja.live`  

---

## 1. 🔍 Executive Audit Summary

OnlinePuja.live is a unique, unified spiritual platform combining:
1. **Sacred Temple Pujas & Live Darshan** (Varanasi, Ujjain, Ayodhya, Haridwar)
2. **Doorstep Pandit Booking** (Home/Office Vedic rituals with Samagri kits)
3. **1-on-1 Astrologer Consultations** (Real-time LiveKit WebRTC audio & video)
4. **Cosmic AI Astrologer** (24/7 autonomous Vedic twin & voice calls)
5. **Vedic Bhandar** (Consecrated gemstones, rudraksha, and yantras)

While core engines are now largely connected (crontab active, LiveKit WebRTC operational, OneSignal SDK integrated, FFmpeg installed), several functional and growth bottlenecks exist across payments, link building, mobile UX, and automated SEO.

---

## 2. 📑 Exhaustive Gap Extraction & Deep Findings

### 2.1. 💳 Payments & Monetization Gaps
- **Gap 1.1: Live Razorpay Credentials**: System settings still hold placeholder keys (`razorpayLivekeyId`, `razorpayLiveSecretkey`). Wallet recharge and direct checkout cannot complete real transactions.
- **Gap 1.2: Cashfree Payment Gateway Integration**: `config/cashfree.php` is created, but environment variables (`CASHFREE_APP_ID`, `CASHFREE_SECRET_KEY`) are missing from `.env` on VPS, and Cashfree is not yet registered as a dynamic flag in `flaggroup` / `systemflag` (ID 2).
- **Gap 1.3: NRI / Diaspora International Payment Rails**: `puja_orders` supports `inr_usd_conversion_rate`, but international cards often fail on domestic Indian Razorpay accounts without international debit/credit activation or Stripe/PayPal dynamic fallback.
- **Gap 1.4: Escrow & Pandit Payout Settlement**: Doorstep Pandit booking requires an automated payout flow to release fees to Purohits only upon ritual completion OTP verification.

### 2.2. 🌐 SEO, Link Building & Organic Traffic Gaps
- **Gap 2.1: Programmatic Sacred Temple Silos**: Currently, temple pujas are loaded dynamically via single-page endpoints. Static Google-indexable HTML landing pages for high-volume keywords ("Kashi Vishwanath Rudrabhishek booking", "Ujjain Mahakal Kaal Sarp puja cost") are missing structured schema markup (`Product`, `Service`, `FAQPage`).
- **Gap 2.2: Automated Backlink & Outreach Architecture**: No automated backlink engine or partner directory exists for temple trust tie-ups, religious travel bloggers, or dharmik forums.
- **Gap 2.3: Sitemap & Google Search Console Automation**: Sitemaps must auto-update upon new puja creation or weekly Panchang generation and ping Google Search Console API.
- **Gap 2.4: Local SEO for Doorstep Pandits**: Missing city/locality programmatic landing pages (e.g. `onlinepuja.live/pandit-in-mumbai`, `onlinepuja.live/pandit-in-delhi`).

### 2.3. 📿 Devotional Tech & Religious Tourism Gaps
- **Gap 3.1: Sankalp Video Proof Delivery**: `puja_orders` has a `puja_video` column, but there is no streamlined 1-tap uploader for Tirtha Purohits to record devotee Gotra Sankalp and automatically push CloudFront streaming links to the devotee's app.
- **Gap 3.2: Doorstep Pandit Dispatch Flow**: The Customer App currently displays Temple Pujas prominently, but lacks a dedicated "Book Pandit for Home" flow that captures:
  - Language preference (Hindi, Sanskrit, Marathi, Tamil, Telugu, Gujarati, Bengali)
  - Home address & Muhurat slot
  - "With Samagri Kit" toggle
- **Gap 3.3: Sanctified Prasad Logistics Tracking**: No automatic webhook syncing with Indian couriers (Shiprocket / Bluedart) for dispatched Prasad boxes.

### 2.4. 📱 Mobile Applications (Customer & Astrologer) Gaps
- **Gap 4.1: Background Full-Screen Incoming Call Overlay**: On Android 10+, notifications can ring, but a full-screen incoming call UI (like WhatsApp or Astrotalk) using `flutter_callkit_incoming` is needed when the phone is locked.
- **Gap 4.2: Astrologer App Order Fulfillment UI**: The Astrologer App has consultation requests, but needs a polished tab for reviewing prescribed puja commissions (`puja_recommends`) and doorstep booking dispatch notices.
- **Gap 4.3: App Store Deep Linking & Universal Links**: Deep links from social captions (`https://onlinepuja.live/app?puja=12`) need AssetLinks (`.well-known/assetlinks.json`) configured on VPS Nginx to open directly inside the Flutter app.

### 2.5. 🤖 Growth OS, Autopilot & Social Media Gaps
- **Gap 5.1: Comment-to-DM Webhook**: Devotees commenting "MAHADEV" on Instagram reels need an automated Meta webhook listener to send immediate DMs with Free Kundli links and ₹50 Dakshina wallet credits.
- **Gap 5.2: YouTube Data API v3 Shorts Auth**: Automated publishing queue currently supports Facebook and Instagram; YouTube Shorts OAuth token must be configured for video reel sync.
- **Gap 5.3: Automated Co-Branded Reels for Astrologers**: The CLI command `php artisan social:plan-astrologers` exists, but needs automated asset rendering to generate daily personalized video reels for top-rated platform astrologers.

---

## 3. 📋 Step-by-Step Master Execution Checklist

```mermaid
flowchart LR
    P1["Phase 1: Payments & Core Fixes"] --> P2["Phase 2: SEO Silos & Link Building"]
    P2 --> P3["Phase 3: Doorstep Pandit & Sankalp Engine"]
    P3 --> P4["Phase 4: Social Virality & Comment-to-DM"]
    P4 --> P5["Phase 5: Production Polish & Scale"]
```

### 🔴 Phase 1: Payment Gateways & Core Revenue Rails (Days 1–3)
- [ ] **1.1. Cashfree Payment Gateway Setup**:
  - Add Cashfree environment credentials to `.env` on VPS (`CASHFREE_APP_ID`, `CASHFREE_SECRET_KEY`, `CASHFREE_BASE_URL`).
  - Register Cashfree under `flaggroup` (id 2 - Payments) and `systemflag` so it can be managed from the Admin Panel.
  - Implement Cashfree Order creation & webhook verification inside `PaymentController.php`.
- [ ] **1.2. Live Razorpay Production Keys**:
  - User to input live Razorpay credentials in `Admin -> Settings -> Payments`.
- [ ] **1.3. International NRI Payment Toggle**:
  - Expose USD ($) / INR (₹) switcher on checkout pages.
  - Enable PayPal/Stripe or International Razorpay card acceptance.
- [ ] **1.4. Wallet Auto-Credit Verification**:
  - Verify webhook signature validation to ensure wallet recharges credit instantly without manual support tickets.

---

### 🟡 Phase 2: SEO Silos, Link Building & Lead Acquisition (Days 4–7)
- [ ] **2.1. Programmatic Pilgrimage Puja Pages**:
  - Build SEO-optimized dynamic blade routes for 50+ sacred pilgrimage shrines (`/puja/kashi-rudrabhishek`, `/puja/ujjain-kaalsarp`).
  - Inject JSON-LD Schema (`Product`, `Service`, `FAQPage`, `BreadcrumbList`).
- [ ] **2.2. Programmatic Doorstep Pandit Pages**:
  - Create city-wise landing pages targeting high-intent search volume (`/book-pandit/mumbai`, `/book-pandit/delhi`, `/book-pandit/bangalore`).
- [ ] **2.3. Automated Link Building & Partner Directory**:
  - Build a public "Sanatan Dharma Partner & Temple Trust Directory" to exchange authorized links with verified pilgrimage trusts and devotional bloggers.
- [ ] **2.4. XML Sitemap & Google Search Console Automation**:
  - Auto-generate `sitemap-pujas.xml` and `sitemap-pandits.xml` and submit via Google Search Console API.

---

### 🟢 Phase 3: Doorstep Pandit Booking & Temple Sankalp Video Engine (Days 8–14)
- [ ] **3.1. Customer App "Book Pandit at Home" Flow**:
  - Dedicated home screen tile for Doorstep Pandit Booking.
  - Form capturing: Ceremony type, date, Muhurat slot, home address, Pandit language preference, and Samagri Kit option.
- [ ] **3.2. Astrologer/Pandit Partner App Dispatch Radar**:
  - Push booking alerts to nearby verified Pandits based on pin-code.
  - 15-minute acceptance timer with auto-escalation to platform manager.
- [ ] **3.3. Tirtha Sankalp Video Delivery System**:
  - Admin/Purohit portal UI to upload devotee Sankalp video clip (`.mp4`).
  - Devotee receives instant push notification to stream and download their personalized Gotra Sankalp video.
- [ ] **3.4. Sanctified Prasad Courier Integration**:
  - Connect Shiprocket / Delhivery API for 1-click AWB generation and devotee SMS tracking.

---

### 🟣 Phase 4: Social Virality, Comment-to-DM & Lead Funnels (Days 15–20)
- [ ] **4.1. Comment-to-DM Automated Funnel**:
  - Set up Meta Webhook endpoint in `GrowthOsController.php`.
  - Auto-respond to keywords ("MAHADEV", "KUNDLI", "PUJA") with direct DM containing Free Kundli PDF + ₹50 Wallet Gift.
- [ ] **4.2. Daily Rashi 9:16 Video Automation**:
  - Leverage installed `ffmpeg` on VPS to stitch daily 9:16 vertical video shorts for 12 Zodiac signs.
- [ ] **4.3. YouTube Shorts Auto-Publisher**:
  - Configure Google Cloud OAuth service account for YouTube Data API v3 publishing.
- [ ] **4.4. Astrologer Referral Commission Push**:
  - Ensure Astrologer receives instant wallet notification when their prescribed puja (`puja_recommends`) is booked.

---

### 🔵 Phase 5: App Deep Linking, VoIP Calling Polish & Production Hardening (Days 21–25)
- [ ] **5.1. Universal App Deep Links**:
  - Deploy `assetlinks.json` on `onlinepuja.live/.well-known/assetlinks.json` for Android app auto-opening from social links.
- [ ] **5.2. VoIP Background Calling Ring Screen**:
  - Integrate `flutter_callkit_incoming` in Astrologer Partner App for full-screen incoming call ringing when locked.
- [ ] **5.3. Performance & Caching Hardening**:
  - Enable Redis caching for high-frequency Panchang and Astrologer availability queries.
