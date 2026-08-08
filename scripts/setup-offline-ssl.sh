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

# 1. Install mkcert if missing
if ! command -v mkcert &> /dev/null; then
    echo "📦 Installing mkcert dependency..."
    sudo apt-get update -qq && sudo apt-get install -y -qq libnss3-tools curl
    curl -sJLO "https://dl.filippo.io/mkcert/latest?for=linux/amd64"
    chmod +x mkcert-v*-linux-amd64
    sudo mv mkcert-v*-linux-amd64 /usr/local/bin/mkcert
    rm -f mkcert-v*-linux-amd64
fi

# 2. Install CA
echo "🔑 Initializing Local CA Authority..."
mkcert -install

# 3. Create .certs directory & generate certificate
mkdir -p .certs
echo "📜 Generating SSL certificate for localhost, 127.0.0.1, and ${LAN_IP}..."
mkcert -cert-file .certs/cert.pem -key-file .certs/key.pem localhost 127.0.0.1 "${LAN_IP}"

echo ""
echo "=================================================="
echo "✅ SSL Certificates Successfully Generated!"
echo "=================================================="
echo "Certificates saved to: .certs/cert.pem and .certs/key.pem"
echo ""
echo "📱 PHONE ONE-TIME SETUP INSTRUCTIONS:"
echo "To make your mobile phone trust https://${LAN_IP}:"
echo "1. Copy the Root CA file from WSL to your phone:"
echo "   File Location: $(mkcert -CAROOT)/rootCA.pem"
echo "2. On Android / iOS:"
echo "   - iOS: Email/AirDrop rootCA.pem -> Settings -> Profile Downloaded -> Install -> About -> Certificate Trust Settings -> Enable Full Trust."
echo "   - Android: Settings -> Security -> Encryption & Credentials -> Install a Certificate -> CA Certificate -> Select rootCA.pem."
echo ""
echo "⛵ Start your offline Sail HTTPS server with:"
echo "   ./vendor/bin/sail up -d"
echo "=================================================="
