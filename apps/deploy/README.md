# 🚀 Free Realtime Stack — VPS Deployment Kit

Everything voice/video/live for the v2 apps runs **free and self-hosted** on
your own VPS. No Agora/Zego/HMS minutes, no per-use bills.

| Component | What | Cost |
|---|---|---|
| **LiveKit** | WebRTC SFU (audio, video, live streaming) | free, Apache-2.0 |
| **COTURN** | TURN relay for users behind strict NAT | free, BSD |
| **Laravel Reverb** | WebSocket server for chat (inside Backend) | free, official Laravel |
| **FCM** | Push notifications | free, unlimited |
| **Gemini API** | Cosmic AI chat (free tier, proxied by backend) | free |

## 1. RTC node (LiveKit + COTURN) — 10 minutes

1. Point DNS `rtc.onlinepuja.live` → the RTC VPS IP.
2. Upload this `deploy/` folder to the VPS (e.g. `/opt/livekit`).
3. Run as root:  `bash setup-rtc-node.sh`
   - installs Docker + nginx, generates TLS cert (certbot), writes config,
     starts both containers, opens firewall ports.
4. Verify: `curl https://rtc.onlinepuja.live` → LiveKit welcome text.

> **Key pairing:** the API key/secret baked into `setup-rtc-node.sh` matches
> `Backend/.env` (`LIVEKIT_API_KEY` / `LIVEKIT_API_SECRET`). If you regenerate
> secrets, update **both** places.

Ports used: `7880/tcp` (signaling), `7881/tcp`, `7882/udp` (WebRTC),
`50000-60000/udp` (host networking), `3478` + `5349` (TURN).

## 2. Backend (one-time)

```bash
# On the web VPS, in Backend/:
# livekit/token endpoint is already committed (LiveKitController.php) —
# just make sure .env has the LIVEKIT_* keys, then:
php artisan config:clear
php artisan route:clear
```

## 3. Chat websockets — Laravel Reverb

```bash
cd Backend
composer require laravel/reverb
php artisan reverb:install        # answers BROADCAST_CONNECTION=reverb, keys
# run it (supervisor, 1 worker):
php artisan reverb:start --host=127.0.0.1 --port=6001
```

Then point the apps at `wss://onlinepuja.live/app/<REVERB_APP_KEY>` and switch
`chat_session_screen.dart` from REST polling to Reverb (REST polling stays as
fallback). Until that's done chat still works via the 3s poller.

## 4. Push — FCM (free, unlimited)

1. Create a Firebase project (free tier) → add Android + iOS apps.
2. Put `google-services.json` / `GoogleService-Info.plist` in the apps.
3. Server: the Backend already ships `kreait/laravel-firebase` — add the
   service-account JSON and send push data payloads for chat/call requests.

## 5. AI astrologer — Gemini free tier

Backend proxy endpoint already planned (`aiChatPath` in `Env`): register a
Gemini API key (free tier), add `GEMINI_API_KEY` to `Backend/.env`, and the
`/api/ai-chat/send` proxy keeps the key server-side (never in the app).
