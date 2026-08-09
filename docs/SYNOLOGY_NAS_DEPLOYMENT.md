# Synology NAS Production Deployment Guide

This guide provides step-by-step instructions for deploying the Fishing Logbook application to a Synology NAS using **Container Manager** (Docker Compose) and setting up secure remote access and two-way sync.

---

## 1. Prerequisites on Synology NAS
1. **Container Manager**: Install from Synology Package Center.
2. **Domain Name & DDNS**: Ensure your NAS has a domain or DDNS configured (e.g. `fishing.yourdomain.com` or Synology DDNS `yourname.synology.me`).
3. **SSL Certificate**: Go to **Control Panel > Security > Certificate** and request a free Let's Encrypt SSL certificate for your domain.

---

## 2. Docker Compose Setup for Synology NAS

Create a directory on your NAS (e.g., `/volume1/docker/fishinglog`) and place `docker-compose.nas.yml` and `.env` files there.

### `docker-compose.nas.yml`
```yaml
version: '3.8'

services:
  app:
    image: php:8.2-fpm-alpine
    container_name: fishinglog_app
    restart: always
    volumes:
      - ./app:/var/www/html
      - ./storage:/var/www/html/storage
    environment:
      - CONTAINER_ROLE=app
    networks:
      - fishing_net
    depends_on:
      - mysql

  webserver:
    image: nginx:alpine
    container_name: fishinglog_web
    restart: always
    ports:
      - "8085:80"
    volumes:
      - ./app:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    networks:
      - fishing_net
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    container_name: fishinglog_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - fishing_net

networks:
  fishing_net:
    driver: bridge

volumes:
  db_data:
```

---

## 3. Synology Reverse Proxy & SSL Setup

1. Open **Synology DSM Control Panel**.
2. Go to **Login Portal > Advanced > Reverse Proxy**.
3. Click **Create** and set the following parameters:
   - **Reverse Proxy Name**: Fishing Logbook
   - **Source Protocol**: HTTPS
   - **Source Hostname**: `fishing.yourdomain.com`
   - **Source Port**: 443
   - **Destination Protocol**: HTTP
   - **Destination Hostname**: `localhost` (or `127.0.0.1`)
   - **Destination Port**: `8085`
4. Under **Control Panel > Security > Certificate**, click **Settings** and assign your Let's Encrypt certificate to the `fishing.yourdomain.com` reverse proxy entry.

---

## 4. Initial Database Migration & Admin Setup

Run migrations inside the container:
```bash
docker exec -it fishinglog_app php artisan migrate --force
```

---

## 5. Laptop Sync Configuration (`.env`)

On your field laptop, configure the NAS connection details in `.env`:

```env
NAS_URL=https://fishing.yourdomain.com
NAS_API_TOKEN=your_admin_api_token_here
```

To trigger manual sync from the laptop:
```bash
./vendor/bin/sail artisan sync:nas
```
Or click **Sync Now with NAS** in the Admin Console.
