#!/usr/bin/env bash
# ============================================================
# Online Puja — RTC node setup (LiveKit + COTURN), FREE stack
# Run ONCE on the RTC VPS as root:
#   bash setup-rtc-node.sh
# ============================================================
set -euo pipefail

DOMAIN="rtc.onlinepuja.live"
LIVEKIT_API_KEY="${LIVEKIT_API_KEY:-ke0hzp3b19g4j28}"
LIVEKIT_API_SECRET="${LIVEKIT_API_SECRET:-665cc21a5c01fdffdb6b111afd2805baa973473c30c06bc498a4a7c93f827087d}"
TURN_SECRET="$(openssl rand -hex 32)"

echo "==> [1/6] Installing Docker + nginx (skip if present)"
command -v docker >/dev/null 2>&1 || (curl -fsSL https://get.docker.com | sh && systemctl enable --now docker)
command -v nginx  >/dev/null 2>&1 || (apt-get update && apt-get install -y nginx certbot python3-certbot-nginx)

echo "==> [2/6] Preparing /opt/livekit"
mkdir -p /opt/livekit/certs
cd /opt/livekit

echo "==> [3/6] Writing livekit.env / turn.env"
cat > livekit.env <<EOF
LIVEKIT_KEYS=${LIVEKIT_API_KEY}: ${LIVEKIT_API_SECRET}
EOF

cat > turn.env <<EOF
TURN_SECRET=${TURN_SECRET}
EOF

echo "==> [4/6] Writing livekit.yaml (keys injected)"
cp /path/to/this/repo/apps/deploy/livekit.yaml ./livekit.yaml 2>/dev/null || true
# Fill the key pair directly if the copied file still has the placeholder
sed -i "s#^  APIhpYpPzLbYhyV5: PLACEHOLDER_API_SECRET#  ${LIVEKIT_API_KEY}: ${LIVEKIT_API_SECRET}#" livekit.yaml 2>/dev/null || {
cat > livekit.yaml <<EOF
port: 7880
bind_addresses:
  - "0.0.0.0"
rtc:
  udp_port: 7882
  tcp_port: 7881
  use_external_ip: true
turn:
  enabled: true
  domain: ${DOMAIN}
  tls_port: 5349
keys:
  ${LIVEKIT_API_KEY}: ${LIVEKIT_API_SECRET}
logging:
  level: info
EOF
}

echo "==> [5/6] TLS certificate for ${DOMAIN} (certbot, free)"
if [ ! -d "/etc/letsencrypt/live/${DOMAIN}" ]; then
  certbot certonly --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@onlinepuja.live || \
  echo "!! certbot failed — make sure ${DOMAIN} DNS A-record points to this server first"
  mkdir -p /opt/livekit/certs
  cp /etc/letsencrypt/live/${DOMAIN}/fullchain.pem /opt/livekit/certs/ 2>/dev/null || true
  cp /etc/letsencrypt/live/${DOMAIN}/privkey.pem  /opt/livekit/certs/ 2>/dev/null || true
fi

echo "==> [6/6] nginx vhost + docker compose up"
cp /path/to/this/repo/apps/deploy/nginx-rtc.conf /etc/nginx/sites-available/rtc.conf 2>/dev/null || true
ln -sf /etc/nginx/sites-available/rtc.conf /etc/nginx/sites-enabled/rtc.conf 2>/dev/null || true
nginx -t && systemctl reload nginx

docker compose -f docker-compose.yml up -d || docker-compose up -d

# firewall (ufw) — open the RTC ports
command -v ufw >/dev/null 2>&1 && ufw allow 7880/tcp && ufw allow 7881/tcp && ufw allow 7882/udp && ufw allow 3478/tcp && ufw allow 3478/udp && ufw allow 5349/tcp || true

echo ""
echo "✔ Done. LiveKit is on https://${DOMAIN}"
echo "  TURN secret (put in livekit.env): ${TURN_SECRET}"
echo "  Test: curl -s https://${DOMAIN}  (should answer with LiveKit welcome text)"
echo ""
echo "  Reminder: Backend/.env must contain the SAME"
echo "  LIVEKIT_API_KEY / LIVEKIT_API_SECRET pair used above."
