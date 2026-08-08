#!/usr/bin/env bash

# Fishing Logbook Offline SSL Generator
# Generates local SSL certificates for Ad-hoc Wi-Fi & Offline Boat Networks

set -e

LAN_IP="${1:-$(hostname -I | awk '{print $1}')}"

echo "=================================================="
echo "⛵ Fishing Logbook Offline SSL Certificate Setup"
echo "=================================================="
echo "Target LAN / Ad-hoc Wi-Fi IP: ${LAN_IP}"
echo ""

mkdir -p .certs

if command -v mkcert &> /dev/null; then
    echo "📜 Generating trusted SSL certificate via mkcert for localhost, 127.0.0.1, and ${LAN_IP}..."
    mkcert -cert-file .certs/cert.pem -key-file .certs/key.pem localhost 127.0.0.1 "${LAN_IP}"
else
    echo "📜 Generating OpenSSL SAN certificate for localhost, 127.0.0.1, and ${LAN_IP}..."
    openssl req -x509 -newkey rsa:2048 -nodes -sha256 -days 3650 \
      -keyout .certs/key.pem -out .certs/cert.pem \
      -subj "/CN=${LAN_IP}" \
      -addext "subjectAltName=DNS:localhost,IP:127.0.0.1,IP:${LAN_IP}"
fi

echo ""
echo "=================================================="
echo "✅ SSL Certificates Successfully Generated!"
echo "=================================================="
echo "Certificates saved to: .certs/cert.pem and .certs/key.pem"
echo ""
echo "⛵ Start or restart your Sail containers to activate HTTPS on port 443:"
echo "   ./vendor/bin/sail restart"
echo ""
echo "🌐 Access your app over HTTPS at:"
echo "   https://localhost  or  https://${LAN_IP}"
echo "=================================================="
