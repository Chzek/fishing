#!/usr/bin/env bash
set -e

echo "=================================================="
echo "    Synology NAS Application Update Script       "
echo "=================================================="

# Move to script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# 1. Pull latest changes from master
echo "--> Fetching and resetting to latest origin/master..."
git fetch origin master
git reset --hard origin/master

# 2. Re-apply NAS deployment configs to project root
echo "--> Syncing Synology NAS configurations..."
if [ -d "synology-nas-deploy" ]; then
    cp synology-nas-deploy/docker-compose.yml docker-compose.yml 2>/dev/null || true
    cp synology-nas-deploy/nginx.conf nginx.conf 2>/dev/null || true
    cp synology-nas-deploy/Dockerfile.nas Dockerfile 2>/dev/null || true
fi

# Ensure .env file exists
if [ ! -f ".env" ] && [ -f "synology-nas-deploy/.env.nas" ]; then
    echo "--> Creating default .env from .env.nas template..."
    cp synology-nas-deploy/.env.nas .env
fi

# 3. Execute Laravel optimization & migration commands
if command -v docker >/dev/null 2>&1 && docker ps --format '{{.Names}}' | grep -q "^fishinglog_app$"; then
    echo "--> Running optimization commands inside fishinglog_app container..."
    docker exec fishinglog_app php artisan config:clear
    docker exec fishinglog_app php artisan cache:clear
    docker exec fishinglog_app php artisan view:clear
    docker exec fishinglog_app php artisan migrate --force
    docker exec fishinglog_app chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
elif [ -f "artisan" ]; then
    echo "--> Running optimization commands in current container environment..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan migrate --force
    chmod -R 777 storage bootstrap/cache 2>/dev/null || true
fi

echo "=================================================="
echo "  UPDATE COMPLETE! Synology NAS is ready to sync. "
echo "=================================================="
