#!/usr/bin/env bash
set -e

# Source user and system profiles if available in non-interactive SSH
[ -f /etc/profile ] && source /etc/profile 2>/dev/null || true
[ -f ~/.profile ] && source ~/.profile 2>/dev/null || true
[ -f ~/.bashrc ] && source ~/.bashrc 2>/dev/null || true

# Export common Synology DSM package paths to PATH
export PATH="/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/var/packages/Git/target/bin:/var/packages/Git/target/usr/bin:/var/packages/ContainerManager/target/usr/bin:/var/packages/Docker/target/usr/bin:$PATH"

echo "=================================================="
echo "    Synology NAS Application Update Script       "
echo "=================================================="

# Move to repository root directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR/.." 2>/dev/null || cd "$SCRIPT_DIR"

# Locate git binary
GIT_BIN="git"
if ! command -v git >/dev/null 2>&1; then
    if [ -f "/var/packages/Git/target/bin/git" ]; then
        GIT_BIN="/var/packages/Git/target/bin/git"
    elif [ -f "/var/packages/Git/target/usr/bin/git" ]; then
        GIT_BIN="/var/packages/Git/target/usr/bin/git"
    elif [ -f "/usr/local/bin/git" ]; then
        GIT_BIN="/usr/local/bin/git"
    else
        echo "--> [Error] Git binary not found in PATH or Synology package directories."
        echo "--> PATH is: $PATH"
        exit 1
    fi
fi

# 1. Pull latest changes from master
echo "--> Fetching and resetting to latest origin/master using ($GIT_BIN)..."
$GIT_BIN fetch origin master
$GIT_BIN reset --hard origin/master

# 2. Re-apply NAS deployment configs to project root if needed
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

# Determine docker execution method (direct or with sudo)
DOCKER_CMD="docker"
if ! docker ps >/dev/null 2>&1; then
    if sudo -n docker ps >/dev/null 2>&1; then
        DOCKER_CMD="sudo docker"
    fi
fi

# 3. Execute Laravel optimization & migration commands
if command -v docker >/dev/null 2>&1 && $DOCKER_CMD ps --format '{{.Names}}' | grep -q "^fishinglog_app$"; then
    echo "--> Running optimization and migrations inside fishinglog_app container..."
    $DOCKER_CMD exec fishinglog_app php artisan optimize:clear
    $DOCKER_CMD exec fishinglog_app php artisan migrate --force
    $DOCKER_CMD exec fishinglog_app php artisan config:clear
    $DOCKER_CMD exec fishinglog_app php artisan route:clear
    $DOCKER_CMD exec fishinglog_app php artisan view:clear
    $DOCKER_CMD exec fishinglog_app chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
elif [ -f "artisan" ]; then
    echo "--> Running optimization commands in local environment..."
    php artisan optimize:clear
    php artisan migrate --force
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    chmod -R 777 storage bootstrap/cache 2>/dev/null || true
else
    echo "--> [Warning] Neither fishinglog_app container nor local artisan was found."
fi

echo "=================================================="
echo "  UPDATE COMPLETE! Synology NAS is up to date.    "
echo "=================================================="
