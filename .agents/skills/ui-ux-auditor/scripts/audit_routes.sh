#!/usr/bin/env bash

# UI/UX Route Auditor Script for Fishing Logbook
# Usage: bash .agents/skills/ui-ux-auditor/scripts/audit_routes.sh [BASE_URL]

BASE_URL="${1:-http://localhost}"
echo "===================================================="
echo "🎣 Fishing Logbook - UI/UX Route Metadata Auditor"
echo "Target Base URL: ${BASE_URL}"
echo "===================================================="

ROUTES=(
  "/"
  "/login"
  "/register"
)

ERRORS=0

check_route() {
  local path="$1"
  local url="${BASE_URL}${path}"
  
  echo -n "Checking route [${path}]... "
  
  response=$(curl -s -w "\n%{http_code}" "$url")
  http_code=$(echo "$response" | tail -n 1)
  html=$(echo "$response" | sed '$d')

  if [ "$http_code" -ne 200 ] && [ "$http_code" -ne 302 ]; then
    echo "❌ FAILED (HTTP Status: $http_code)"
    ERRORS=$((ERRORS + 1))
    return
  fi

  echo -n "HTTP $http_code OK | "

  # Check Viewport meta
  if echo "$html" | grep -qi 'name="viewport"'; then
    echo -n "Viewport: OK | "
  else
    echo -n "Viewport: ❌ MISSING | "
    ERRORS=$((ERRORS + 1))
  fi

  # Check CSRF token meta
  if echo "$html" | grep -qi 'name="csrf-token"'; then
    echo -n "CSRF: OK | "
  else
    echo -n "CSRF: ❌ MISSING | "
    ERRORS=$((ERRORS + 1))
  fi

  # Check Lucide or Icon setup
  if echo "$html" | grep -qi 'lucide'; then
    echo "Icons: Lucide OK"
  else
    echo "Icons: ⚠️ Warning (no lucide references)"
  fi
}

for route in "${ROUTES[@]}"; do
  check_route "$route"
done

echo "===================================================="
if [ "$ERRORS" -eq 0 ]; then
  echo "✅ All public route UI metadata checks passed!"
  exit 0
else
  echo "❌ Found $ERRORS UI metadata issue(s)."
  exit 1
fi
