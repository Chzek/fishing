# Synology NAS Production Deployment Guide (Container Manager)

This step-by-step guide walks you through deploying the Fishing Logbook application to a Synology NAS using **Container Manager** (Synology DSM 7.2+ Docker Compose UI), **File Station**, and **DSM Reverse Proxy**.

---

## 1. Prerequisites & Router Setup

1. **Install Container Manager**: Open Synology **Package Center** and install **Container Manager**.
2. **Domain Name & DDNS**: Ensure your NAS has a domain or DDNS configured under **Control Panel > External Access > DDNS** (e.g. `fishing.yourdomain.com` or `yourname.synology.me`).
3. **SSL Certificate**: Go to **Control Panel > Security > Certificate** and request a free Let's Encrypt SSL certificate for your domain.
4. **Router Port Forwarding**: On your home internet router, forward external port **443 (HTTPS)** to your Synology NAS local IP address.
   *(Note: Do NOT expose Docker ports directly to the internet; Synology DSM Reverse Proxy handles SSL termination on port 443).*

---

## 2. Prepare Files in File Station

1. Open **File Station** on your Synology NAS.
2. Navigate to the `docker` shared folder and create a folder named `fishinglog` (full path: `/volume1/docker/fishinglog`).
3. Copy/upload your Fishing Logbook application codebase into `/docker/fishinglog`.

Expected folder layout in File Station:
```text
docker/
└── fishinglog/
    ├── Dockerfile.nas
    ├── docker-compose.yml
    ├── nginx.conf
    ├── .env
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    └── artisan
```

### Create Required Configuration Files in `/docker/fishinglog`:

#### A. `Dockerfile.nas`
*(Required because stock PHP 8.2 Alpine lacks required extensions for Laravel & MySQL)*

```dockerfile
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip opcache

WORKDIR /var/www/html
```

#### B. `nginx.conf`
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;

    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### C. `.env`
```env
APP_NAME="Fishing Logbook"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://fishing.yourdomain.com
TRUSTED_PROXIES=*

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fishinglog
DB_USERNAME=fishing_user
DB_PASSWORD=your_secure_db_password
```

#### D. `docker-compose.yml`
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.nas
    container_name: fishinglog_app
    restart: always
    volumes:
      - .:/var/www/html
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
      - .:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf:ro
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

## 3. Set Directory Permissions (File Station or SSH)

Inside File Station, permission adjustments are needed so PHP-FPM (`www-data`, UID 82) inside the container can write logs and cache:

* **Via File Station GUI**:
  1. Right-click the `/docker/fishinglog/storage` folder and click **Properties**.
  2. Under **Permission**, click **Create** or **Edit**.
  3. Select User/Group: **Everyone**, check **Read** and **Write**, apply to **this folder, sub-folders and files**, then click **Save**.
  4. Repeat step 1–3 for `/docker/fishinglog/bootstrap/cache`.

* **Or via SSH terminal** (optional fast method):
  ```bash
  chmod -R 775 /volume1/docker/fishinglog/storage /volume1/docker/fishinglog/bootstrap/cache
  chown -R 82:82 /volume1/docker/fishinglog/storage /volume1/docker/fishinglog/bootstrap/cache
  ```

---

## 4. Deploy via Synology Container Manager

1. Open **Container Manager** from the Synology DSM main menu.
2. Click **Project** on the left menu.
3. Click **Create** (top button).
4. Configure the project settings:
   - **Project Name**: `fishing-logbook`
   - **Path**: Click **Set** and select `/docker/fishinglog`.
   - **Source**: Select **Use existing docker-compose.yml**.
5. Click **Next** -> Click **Next** -> Click **Done**.
6. Container Manager will build the custom PHP image and launch all containers (`fishinglog_app`, `fishinglog_web`, and `fishinglog_db`). Wait for the status to change to **Healthy / Running** (Green).

---

## 5. Configure Synology Reverse Proxy & SSL

1. Open **Synology Control Panel**.
2. Go to **Login Portal > Advanced** tab, then click **Reverse Proxy**.
3. Click **Create** and enter the following:
   - **Reverse Proxy Name**: `Fishing Logbook`
   - **Source**:
     - Protocol: `HTTPS`
     - Hostname: `fishing.yourdomain.com`
     - Port: `443`
   - **Destination**:
     - Protocol: `HTTP`
     - Hostname: `localhost` (or `127.0.0.1`)
     - Port: `8085`
   - Click **Save**.
4. Go to **Control Panel > Security > Certificate** tab.
5. Click **Settings** (top toolbar).
6. Find `fishing.yourdomain.com` in the list and select your Let's Encrypt SSL certificate in the dropdown. Click **OK**.

---

## 6. Run Initial Migrations & Setup via Container Manager GUI Terminal

1. Open **Container Manager**.
2. Click **Container** on the left menu.
3. Select `fishinglog_app` container and click **Action > Open Terminal** (or click on `fishinglog_app` and open the **Terminal** tab).
4. Click **Create** to launch a `/bin/sh` or `/bin/bash` terminal session.
5. Execute the following setup commands:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Field Laptop Sync Setup (`.env`)

On your field laptop, open your local `.env` file and set the NAS connection details:

```env
NAS_URL=https://fishing.yourdomain.com
NAS_API_TOKEN=your_admin_api_token_here
```

To trigger manual two-way sync from your laptop:
```bash
./vendor/bin/sail artisan sync:nas
```
Or navigate to the laptop's Web App Admin Console and click **Sync Now with NAS**.


