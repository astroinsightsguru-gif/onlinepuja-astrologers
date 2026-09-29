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

echo "==> [4/6] Writing livekit.yaml"
cat > /opt/livekit/livekit.yaml <<EOF
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

echo "==> [5/6] Writing docker-compose.yml"
cat > /opt/livekit/docker-compose.yml <<EOF
version: "3.8"
services:
  livekit:
    image: livekit/livekit-server:latest
    restart: unless-stopped
    command: --config /etc/livekit.yaml
    env_file: livekit.env
    network_mode: host
    volumes:
      - ./livekit.yaml:/etc/livekit.yaml:ro
      - ./certs:/etc/livekit/certs:ro

  coturn:
    image: coturn/coturn:latest
    restart: unless-stopped
    network_mode: host
    command: >
      -n
      --realm=onlinepuja.live
      --listening-port=3478
      --fingerprint
      --lt-cred-mech
      --static-auth-secret=\${TURN_SECRET:-${TURN_SECRET}}
      --tls-listening-port=5349
      --cert=/etc/turn/certs/fullchain.pem
      --pkey=/etc/turn/certs/privkey.pem
      --no-cli
      --no-tls-relay
      --no-multicast-peers
      --min-port=65534 --max-port=65534
    env_file: turn.env
    volumes:
      - ./certs:/etc/turn/certs:ro
EOF

echo "==> [6/7] TLS certificate for ${DOMAIN} (certbot)"
if [ ! -d "/etc/letsencrypt/live/${DOMAIN}" ]; then
  # Temporary stop nginx if it's already running to get standalone cert or use nginx plugin
  certbot certonly --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@onlinepuja.live || \
  certbot certonly --standalone -d "${DOMAIN}" --non-interactive --agree-tos -m admin@onlinepuja.live || \
  echo "!! certbot warning — ensure ${DOMAIN} points to this server IP in DNS"
fi

mkdir -p /opt/livekit/certs
if [ -d "/etc/letsencrypt/live/${DOMAIN}" ]; then
  cp -L /etc/letsencrypt/live/${DOMAIN}/fullchain.pem /opt/livekit/certs/ 2>/dev/null || true
  cp -L /etc/letsencrypt/live/${DOMAIN}/privkey.pem   /opt/livekit/certs/ 2>/dev/null || true
fi

echo "==> [7/7] nginx vhost + docker compose launch"
cat > /etc/nginx/sites-available/rtc.conf <<EOF
server {
    listen 80;
    server_name ${DOMAIN};
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ${DOMAIN};

    ssl_certificate     /etc/letsencrypt/live/${DOMAIN}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${DOMAIN}/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1:7880;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_read_timeout 3600s;
        proxy_send_timeout 3600s;
    }
}
EOF

ln -sf /etc/nginx/sites-available/rtc.conf /etc/nginx/sites-enabled/rtc.conf 2>/dev/null || true
nginx -t && systemctl reload nginx || systemctl restart nginx || true

cd /opt/livekit
docker compose up -d || docker-compose up -d

# firewall (ufw or iptables) — open the RTC media ports
command -v ufw >/dev/null 2>&1 && ufw allow 80/tcp && ufw allow 443/tcp && ufw allow 7880/tcp && ufw allow 7881/tcp && ufw allow 7882/udp && ufw allow 3478/tcp && ufw allow 3478/udp && ufw allow 5349/tcp && ufw allow 50000:60000/udp || true

echo ""
echo "✔ Done. LiveKit is on https://${DOMAIN}"
echo "  TURN secret (put in livekit.env): ${TURN_SECRET}"
echo "  Test: curl -s https://${DOMAIN}  (should answer with LiveKit welcome text)"
echo ""
echo "  Reminder: Backend/.env must contain the SAME"
echo "  LIVEKIT_API_KEY / LIVEKIT_API_SECRET pair used above."
