# Comprehensive Synology NAS Deployment & Troubleshooting Guide

This guide documents the complete end-to-end production deployment process for hosting the **Fishing Logbook** application on a Synology NAS using **DSM 7.2+ Container Manager**, **File Station**, and **DSM Reverse Proxy**.

---

## Table of Contents
1. [Prerequisites & Router Setup](#1-prerequisites--router-setup)
2. [Deployment Package Files](#2-deployment-package-files)
3. [Uploading Codebase (ZIP & Extract Method)](#3-uploading-codebase-zip--extract-method)
4. [Container Manager Project Setup](#4-container-manager-project-setup)
5. [Reverse Proxy & SSL Setup](#5-reverse-proxy--ssl-setup)
6. [Initial App Setup & Permission Configuration](#6-initial-app-setup--permission-configuration)
7. [Troubleshooting & Common Fixes](#7-troubleshooting--common-fixes)
8. [Field Laptop Two-Way Synchronization](#8-field-laptop-two-way-synchronization)

---

## 1. Prerequisites & Router Setup

1. **Synology DSM 7.2+**: Ensure your Synology NAS is running DSM 7.2 or higher.
2. **Container Manager**: Installed from Synology **Package Center**.
3. **DDNS & SSL Certificate**:
   - Configure Synology DDNS under **Control Panel > External Access > DDNS** (e.g., `fishinglog.chzek-safe.synology.me`).
   - Request a Let's Encrypt SSL certificate under **Control Panel > Security > Certificate**.
4. **Router Port Forwarding**:
   - Log into your home router admin portal (`192.168.1.1` or router app).
   - Forward external port **443 (TCP)** to your Synology NAS local IP address on internal port **443 (TCP)**.

---

## 2. Deployment Package Files

The `synology-nas-deploy/` directory contains all required production configuration files:

- **`docker-compose.yml`**: Configures `app` (PHP 8.2-FPM), `webserver` (Nginx port 8085), and `mysql` (MySQL 8.0).
- **`Dockerfile.nas`**: Custom Alpine PHP 8.2-FPM container with Composer, GD, PDO, ZIP, and BCMath.
- **`nginx.conf`**: Nginx webserver config passing FastCGI and `X-Forwarded-Proto` HTTPS headers.
- **`.env.nas`**: Production environment configuration template.

---

## 3. Uploading Codebase (ZIP & Extract Method)

To avoid browser drag-and-drop file loss when uploading folders via File Station:

1. **On your computer**, select the required application directories:
   - `app/`
   - `bootstrap/`
   - `config/`
   - `database/`
   - `public/`
   - `resources/`
   - `routes/`
   - `storage/`
   - `vendor/` (or run `composer install` inside container)
   - `artisan`

2. Compress these into a single archive: **`project.zip`**.

3. In **File Station**, navigate to `/docker/fishinglog/`.
4. Upload `project.zip` and copy all files from `synology-nas-deploy/` into `/docker/fishinglog/`.
5. Right-click `project.zip` → **Extract** → **Extract to current location**.
6. Rename `.env.nas` to `.env`.

---

## 4. Container Manager Project Setup

1. Open Synology **Container Manager**.
2. Go to the **Project** tab and click **Create**.
3. Fill in settings:
   - **Project Name**: `fishinglog`
   - **Path**: `/docker/fishinglog`
   - **Source**: Select *Use existing docker-compose.yml*.
4. Click **Next** → **Done**.
5. Wait for containers (`fishinglog_app`, `fishinglog_web`, `fishinglog_db`) to show status **Healthy / Running**.

---

## 5. Reverse Proxy & SSL Setup

1. Open **Synology Control Panel > Login Portal > Advanced** tab → click **Reverse Proxy**.
2. Click **Create**:
   - **Reverse Proxy Name**: `Fishing Logbook`
   - **Source**:
     - Protocol: `HTTPS`
     - Hostname: `fishinglog.chzek-safe.synology.me`
     - Port: `443`
   - **Destination**:
     - Protocol: `HTTP`
     - Hostname: `localhost` (or `127.0.0.1`)
     - Port: **`8085`**  *(Crucial: Must be port 8085, NOT port 80)*
3. Save the entry.
4. Go to **Control Panel > Security > Certificate** tab → click **Settings**.
5. Assign your Let's Encrypt certificate to `fishinglog.chzek-safe.synology.me` and click **OK**.

---

## 6. Initial App Setup & Permission Configuration

1. In **Container Manager**, go to **Container** tab.
2. Select `fishinglog_app` → **Action > Open Terminal** (or Terminal tab).
3. Click **Create** → **Launch with command** → type **`/bin/sh`**.
4. Run the initialization commands:

```bash
composer install
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan config:clear
php artisan view:clear
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
```

---

## 7. Troubleshooting & Common Fixes

### A. Mixed Content Error (CSS/JS assets fail to load over HTTPS)
- **Cause**: Synology Reverse Proxy terminates SSL on port 443 and forwards HTTP to container.
- **Fix**: 
  1. Ensure `nginx.conf` passes `fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;`.
  2. Ensure `AppServiceProvider.php` forces HTTPS:
     ```php
     if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_starts_with(config('app.url'), 'https://')) {
         URL::forceScheme('https');
     }
     ```
  3. Ensure `.env` has `APP_URL=https://fishinglog.chzek-safe.synology.me` and `ASSET_URL=https://fishinglog.chzek-safe.synology.me`.

### B. "Your website is not set up yet" Page
- **Cause**: Reverse Proxy destination port set to `80` (Synology Web Station).
- **Fix**: Change Reverse Proxy destination port to **`8085`**.

### C. `groupadd: invalid group ID 'sail'` Build Error
- **Cause**: Container Manager used Sail dev Dockerfile instead of `Dockerfile.nas`.
- **Fix**: Replace `docker-compose.yml` with `synology-nas-deploy/docker-compose.yml` pointing to `dockerfile: Dockerfile.nas`.

### D. `UnexpectedValueException: Permission denied` on Storage
- **Cause**: Container process user lacks write permissions on `storage/logs`.
- **Fix**: Run `chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache` inside container terminal.

---

## 8. Field Laptop Two-Way Synchronization

The app includes an automated two-way Outbox sync engine (`sync:nas`) between your field laptop and NAS server.

1. **Laptop `.env` configuration**:
   ```env
   NAS_URL=https://fishinglog.chzek-safe.synology.me
   NAS_API_TOKEN=your_admin_api_token_here
   ```
2. **Execute sync from laptop**:
   ```bash
   ./vendor/bin/sail artisan sync:nas
   ```
   Or click **Sync Now with NAS** in the laptop web app interface.
