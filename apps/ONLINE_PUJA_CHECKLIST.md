# 🕉️ OnlinePuja.live — Master Environment & Pending Tasks Checklist

> **Document Name:** Online Puja Checklist  
> **Last Updated:** October 10, 2026  
> **Repository:** `d:\onlinepuja-live\onlinepuja-astro\Main\apps`  
> **Live Production:** [onlinepuja.live](https://onlinepuja.live)

---

## 🌐 1. Complete Environment Architecture

### 🖥️ Production Backend & Server
- **Server IP / Host:** `200.234.41.231` (Hostinger Cloud VPS)
- **SSH Access:** `ssh root@200.234.41.231`
- **Application Root:** `/home/onlinepujalive/htdocs/onlinepuja.live`
- **Stack:** Laravel 10+, PHP 8.2 / 8.3 CLI, Cloudflare CDN & Reverse Proxy
- **Database:** MySQL `u776663242_onlinepuja`
- **Active Admin Panels:**
  - Executive Growth & Goals: `https://onlinepuja.live/admin/growth-os`
  - Master Brain & Chatbot: `https://onlinepuja.live/admin/growth-os/brain`
  - OmniRoute & AI Vault: `https://onlinepuja.live/admin/growth-os/ai`
  - Content & Blogs Studio: `https://onlinepuja.live/admin/growth-os/content`
  - Social Multi-Publisher: `https://onlinepuja.live/admin/growth-os/social`

---

### 📱 Mobile Applications Workspace
- **Workspace Directory:** `d:\onlinepuja-live\onlinepuja-astro\Main\apps`
- **Astrologer Partner App:** `apps/onlinepuja-astrologer`
  - **Package ID:** `live.onlinepuja.astro` / `live.onlinepuja.onlinepuja_v2_partner`
  - **Status:** Installed & verified running on physical testbed.
- **Customer Devotee App:** `apps/onlinepuja-customer`
  - **Package ID:** `live.onlinepuja.app`
  - **Status:** Compiles clean, 0 compiler errors.
- **Shared Vedic Core Library:** `apps/shared` (`op_shared`)
  - Houses Vedic calendars, panchang calculations, gotra vault, `LocaleManager`, and `AppStrings`.

---

### 📲 Physical Testbeds & Hardware
- **Connected Physical Device:** Samsung Galaxy `SM E146B` (Galaxy F14 5G)
- **ADB Serial:** `RZCW41MZ6TL` (Connected via USB)
- **Installed Build:** Debug arm64-v8a APK running with Vulkan Impeller rendering.

---

### 🤖 Multi-Tier Free AI Generation Engine
Configured inside [AI Engine & Vault](https://onlinepuja.live/admin/growth-os/ai):
- **Text Synthesis:** OmniRoute &rarr; Google Gemini &rarr; OpenRouter &rarr; Pollinations
- **Image Generation:** OmniRoute Flux &rarr; Pollinations Flux &rarr; Cloudflare Workers AI &rarr; Hugging Face &rarr; Pollinations Turbo
- **Video Reels:** OmniRoute Video &rarr; Hugging Face &rarr; Pollinations Motion &rarr; Cinematic Reel

---

## 📋 2. Prioritized Pending Tasks Checklist

### 🔴 Critical Blockers (Revenue & Navigation)
- [ ] **1. Supply Live Razorpay Payment Credentials**
  - *Location:* `Admin -> Settings -> Payment` or `system_settings` table.
  - *Issue:* Razorpay keys are currently dummy placeholders (`razorpayLivekeyId`, `razorpayLiveSecretkey`).
  - *Impact:* Customer checkout and wallet recharges cannot complete.
  - *Action:* Replace with real Razorpay Key ID and Secret.

- [ ] **2. Fix Route Reference in `routes/api.php`**
  - *Location:* `routes/api.php:54`
  - *Issue:* `Route::post('partner/register', [Partner\RegistrationController::class, 'register'])` causes `Class "Partner\RegistrationController" does not exist` on `route:list` / `route:cache`.
  - *Action:* Update to `[RegistrationController::class, 'register']`.

---

### 🟡 Mobile App UI & Localization Audit (Astrologer App)
- [ ] **3. Implement Reactive Language Support in Astrology App**
  - *Root Cause Identified:* `LocaleManager` is only referenced in `profile_screen.dart` to pick a language. `onlinepuja-astrologer` screens (`home_shell.dart`, `requests_screen.dart`, `orders_fulfillment_screen.dart`, `wallet_screen.dart`, `availability_screen.dart`) use 100% hardcoded English strings and don't listen to `LocaleManager.instance.currentLanguage`.
  - *Action:*
    - Wrap `HomeShell` and root views with `ValueListenableBuilder<AppLanguage>`.
    - Extend `AppStrings` with Astrologer-specific terminology (e.g., *Consultation Requests, Earnings, Availability, Go Online, Fulfillment, Devotee Rating*).
    - Replace hardcoded English strings with reactive localized tokens across all 15 Indian languages.

- [ ] **4. Fix Loose Boxes, Unaligned Text & Floating Buttons**
  - *Areas Identified:*
    - **Dashboard Hero Balance Card (`home_shell.dart`):** `Withdraw Funds` and `Statements` buttons have mismatched horizontal padding and shrink text awkwardly on smaller screens. Need locked 48px height with fixed iconography and tight borders.
    - **Today's Practice Grid (`home_shell.dart`):** `StatTile` with `childAspectRatio: 1.45` wraps lines loosely. Standardize to a tight fixed aspect ratio with uniform text truncation and metrics alignment.
    - **Floating Action Buttons:** In `RequestsScreen` and `OrdersFulfillmentScreen`, acceptance/status buttons float loosely without a docked, fixed bottom action bar. Lock them into pinned bottom sheets or safe-area anchored bars.
    - **Status Radar & Toggle:** Align switch padding with the outer card margin so it does not feel detached.
    - **Call/Chat Acceptance Modal:** Fix loose button spacing in the ringing consultation dialog so "ACCEPT" and "REJECT" are locked side-by-side with equal optical weight.

- [ ] **5. Customer App UI Polish & Deprecation Cleanup**
  - *Areas Identified:*
    - Replace deprecated `.withOpacity()` with `.withValues(alpha: ...)` across `puja_detail_screen.dart`, `sankalp_vault_screen.dart`, and `onlinepuja_ai_dialog.dart`.
    - Replace deprecated `value` with `initialValue` in `family_gotra_vault_screen.dart`.

---

### 🔵 Third-Party Integrations & SEO
- [ ] **6. Google Search Console Site Verification**
  - *Issue:* Service account lacks verified ownership of `https://onlinepuja.live/` (HTTP 403 on automated sitemap & indexing inspection).
  - *Action:* Add Google service account email as Delegated Owner in Google Search Console.

- [ ] **7. Meta Graph API Permissions (Instagram & Facebook)**
  - *Issue:* Analytics API returns OAuth token permission restrictions.
  - *Action:* Submit Meta App Review for `read_insights` and `pages_read_user_content` advanced access.
