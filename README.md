# 🎣 Fishing Logbook (Telemetry & Field Logger)

An offline-first wilderness fishing logbook and environmental telemetry system built with **Laravel 12**, **Livewire 3**, **Alpine.js**, **Tailwind CSS v4**, **IndexedDB**, and **Leaflet.js**. Containerized via **Laravel Sail** and designed specifically for remote wilderness operation on boats, at remote cabins, or out on the water where internet access is unavailable.

---

## 🌟 Core Application Subsystems & Features

### ⛵ 1. Boat Quick Catch Logger (`/record/quick`)
- **Touch-Optimized Field Logging**: Designed for rapid, one-handed catch logging on smartphones and tablets out on the boat.
- **Hardware Satellite GPS Pinpointing**: Directly queries your device's internal satellite GPS chip (`navigator.geolocation`) without requiring cellular data, recording precise `latitude` and `longitude`.
- **Offline IndexedDB Queue & Idempotency**: Automatically queues catches in browser `IndexedDB` when offline with client-side UUIDs (`client_id`) to guarantee zero duplicate records upon reconnecting.
- **Live Sync Floating Toast & Offline Review (`/record/offline-review`)**: Real-time toast notifications alert anglers when connectivity is restored, with an interactive review screen displaying queued and synced catches on a map.

### 🎣 2. Searchable Autocomplete Lure & Tackle Selector (`<livewire:ui.lure-selector>`)
- **2-Tier Category Grouped Trays**: Fast, searchable dropdown tray organized by tackle taxonomy (*Crankbaits, Jigs, Soft Plastics, Spinners & Spoons, Topwater, Swimbaits, Rigs*).
- **Technical Specs & Telemetry Badges**: Real-time display of lure manufacturer/brand pills, color variants, running depths (*e.g., 4-9 ft*), weights/sizes, and historical verified catch counts from your logbook.
- **Integrated Everywhere**: Seamlessly embedded in the Standard Catch Logger (`/record/create`), Edit Catch (`/record/{id}/edit`), and Boat Quick Catch (`/record/quick`).

### 🧭 3. Interactive Hydrographic Map Explorer (`/map/explorer`)
- **Dynamic Viewport Bounding-Box Querying**: Renders lakes, waypoints, and hydrographic data dynamically as you pan and zoom across Ontario waters.
- **Slide-Over Detail Drawer**: Clicking any waterbody opens a right-hand slide-over drawer showing lake catch statistics, target species breakdowns, longest/fattest records, top productive lures, and one-tap catch shortcuts.
- **Multi-Filter Controls**: Filter the map in real time by target species, angler, lure, trophy catches only, and expedition season years.

### 🗺️ 4. Offline Canadian Hydrographic Map Downloader (`/map/offline`)
- **PWA Map Tile Caching**: Pre-download regional hydrographic map tile packs (NRCan CanVec & Esri topo zoom levels 7 to 14) into browser `CacheStorage` before leaving cellular range.
- **Proximity Layer & Collision Warnings**: Built-in 2-mile lake lookup warns anglers when logging a catch near existing lakes to prevent accidental duplicate waterbody creation.

### 🎨 5. 18-Species Fish Vector Artwork & Avatar System (`<x-fishAvatar>`)
- **High-Resolution Vector Art**: Custom cel-shaded artwork with unified slate-blue badge styling for all 18 Ontario freshwater sportfish species located in [`public/images/fish/avatars/`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/).
- **Species Dossier & Taxonomy Index (`/fish`, `/fish/{id}`)**: Family tab filtering (*Salmonidae, Percidae, Esocidae, Centrarchidae*), species dossiers with biological and telemetry stats, and direct Catch Directory link banners.

### ⚡ 6. Zero-Layout-Shift Native Blade Lucide Icons
- **Server-Rendered SVGs**: Powered by `mallardduck/blade-lucide-icons` and `blade-ui-kit/blade-icons`, rendering Lucide icons as native inline SVGs directly from the backend.
- **No Client-Side JavaScript Flash**: Completely eliminated runtime JS DOM injection, icon flickering, and Livewire DOM morph layout shifts.

### 📊 7. App-Wide Generic Livewire 3 Data Table (`@livewire('components.generic-data-table')`)
- **Unified Reactive Tables**: Powers Lakes, Anglers, Catches, Expeditions, Trash, and Admin Users.
- **Instant Search & Multi-Column Sorting**: Real-time debounced search, Shift-click multi-column sorting, dynamic relation counts, soft-delete management, and customizable column visibility.

### 🔄 8. Two-Way Synology NAS Remote Synchronization (`/admin/sync`)
- **Remote Outbox Ingestion**: Bidirectional synchronization between local field laptops/tablets and the master Synology NAS server.
- **Base64 Photo & Media Transfer**: Synchronizes catch photos and profile avatars across devices.
- **Last-Write-Wins (LWW) Conflict Resolution**: Invariant tracking using timestamped `synced_at` and `pending_upstream` states across all 13 database models.

### 🌤️ 9. Environmental Weather Telemetry & Lake Weather Sync
- **Open-Meteo Integration**: Automatically associates daily atmospheric metrics (air temp, barometric pressure, wind speed/direction, sky conditions) with lake coordinates and catch timestamps in `lake_daily_weather`.
- **Command-Line Backfill**:
  ```bash
  ./vendor/bin/sail artisan weather:sync
  ```

### ⚡ 10. Global Omnibox Command Palette (`Ctrl+K` or `/search`)
- Fast keyboard-driven command palette for searching across 6 Eloquent models (**Anglers**, **Lakes**, **Fish Species**, **Lures**, **Expeditions**, **Catches**) and jumping straight to quick action routes.

---

## 🔒 Offline Ad-hoc Boat Wi-Fi & HTTPS Setup Guide

Modern mobile browsers (iOS Safari & Android Chrome) require a **Secure Context (HTTPS)** for both **Progressive Web App (PWA)** offline features and **Hardware GPS Geolocation** (`navigator.geolocation`).

This project includes a pre-configured **Caddy SSL container** and automated helper scripts for 100% offline HTTPS operation over your boat or cabin's local ad-hoc Wi-Fi network.

### Step 1: Run the Offline SSL Setup Script
In your WSL 2 terminal, run:
```bash
./scripts/setup-offline-ssl.sh [OPTIONAL_LAN_IP]
```
*This automatically generates trusted local SSL certificates in `.certs/` for `localhost`, `127.0.0.1`, and your ad-hoc Wi-Fi IP address using `mkcert`.*

### Step 2: One-Time Mobile Phone Root CA Installation
To make your mobile phones trust `https://<YOUR-AD-HOC-IP>` without security warnings:
1. Run `mkcert -CAROOT` in WSL to locate `rootCA.pem`.
2. Transfer `rootCA.pem` to your mobile device (via USB, Bluetooth, or SD card).
3. **Android**: Navigate to *Settings > Security > Encryption & Credentials > Install CA Certificate* and select `rootCA.pem`.
4. **iOS**: Open `rootCA.pem` under *Settings > Profile Downloaded > Install*, then go to *Settings > About > Certificate Trust Settings* and toggle **Full Trust**.

### Step 3: Launch Containers & Access
```bash
./vendor/bin/sail up -d
```
Access the application on your phone at `https://<YOUR-AD-HOC-IP>` with full PWA and offline GPS pinpointing capability!

---

## 🚀 Local Development & Execution Guidelines

The host development machine does not require a global PHP CLI. All PHP, Artisan, Composer, Node, and test commands are executed via **Laravel Sail** inside **WSL 2**.

### Container Management Commands

| Action | Command |
| :--- | :--- |
| **Start Environment** | `./vendor/bin/sail up -d` |
| **Stop Containers** | `./vendor/bin/sail stop` |
| **Tear Down Network/Volumes** | `./vendor/bin/sail down` |
| **Check Container Status** | `./vendor/bin/sail ps` |
| **Enter Container Bash Shell** | `./vendor/bin/sail bash` |
| **Laravel Tinker REPL** | `./vendor/bin/sail artisan tinker` |
| **View Realtime Logs** | `./vendor/bin/sail logs -f` |

---

## 🧪 Automated Testing

### 1. PHPUnit Backend & Feature Tests
Run the entire backend unit and feature test suite:
```bash
./vendor/bin/sail test
```
Run a specific test class:
```bash
./vendor/bin/sail test tests/Feature/LureSelectorLivewireTest.php
```

### 2. Playwright End-to-End Browser Tests
Run the complete browser E2E test suite in headless mode:
```bash
./vendor/bin/sail npm run test:e2e
```
Run a specific Playwright test file:
```bash
./vendor/bin/sail npm run test:e2e -- tests/e2e/catch-directory.spec.js
```
Run headed mode for visual browser debugging:
```bash
./vendor/bin/sail npm run test:e2e:headed
```

---

## 💾 Database Safety & Backup Protocols

> [!CAUTION]
> This application runs against a live/shared database environment. **NEVER** execute destructive schema commands (`migrate:fresh`, `migrate:reset`, `migrate:refresh`, or `db:wipe`) without explicit confirmation.

Before executing schema migrations or bulk data operations, always create a timestamped database backup:

### 📦 Creating a Timestamped Database Backup
```bash
./vendor/bin/sail exec -T mysql bash -c 'mysqldump --no-tablespaces -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE' > "database/backups/backup_$(date +%Y%m%d_%H%M%S).sql"
```

### 📥 Restoring a Database Backup
```bash
./vendor/bin/sail exec -T mysql bash -c 'mysql -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE' < database/backups/backup_YYYYMMDD_HHMMSS.sql
```

---

## 📦 Project Roadmap & Backlog

See [`BACKLOG.md`](file:///home/gmroczek/git/fishing/BACKLOG.md) for prioritized upcoming initiatives, architecture refactoring, and completed milestones.

---

## 📄 License
The Fishing Logbook application is open-source software licensed under the [MIT license](LICENSE).
