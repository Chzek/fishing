# Synology NAS Quick Deployment Package

This folder contains all production configuration files needed to host or rebuild the Fishing Logbook application on Synology NAS using **DSM 7.2+ Container Manager** and **DSM Reverse Proxy**.

---

## Folder Contents:

| File | Description |
|---|---|
| `docker-compose.yml` | Synology Container Manager Docker Compose configuration (`nginx:1.30.7-alpine`, PHP 8.2.36-FPM, MySQL 8.0) |
| `Dockerfile.nas` | Custom PHP 8.2.36-FPM Alpine container with GD, PDO, ZIP, Composer, and hardened PHP security ini |
| `Dockerfile` | Copy of `Dockerfile.nas` for compatibility |
| `nginx.conf` | Web server configuration with HTTPS header forwarding |
| `.env.nas` | Production environment configuration template |
| `README_NAS.md` | Quick instructions |

---

## 3-Step Quick Rebuild on Synology NAS:

1. **Copy/Upload Files to NAS**:
   Copy all files from this `synology-nas-deploy/` folder (along with your application code folders: `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `vendor`) into `/volume1/docker/fishinglog/`.

2. **Set `.env` File**:
   Rename `.env.nas` to `.env` inside `/volume1/docker/fishinglog/`.

3. **Deploy via Container Manager**:
   - Open Synology **Container Manager > Project**.
   - Create/Rebuild project using `/volume1/docker/fishinglog`.
   - Open Container Terminal (`/bin/sh`) and run:
     ```bash
     php artisan key:generate --force
     php artisan migrate --force
     php artisan storage:link
     php artisan config:clear
     chmod -R 777 storage bootstrap/cache
     ```
