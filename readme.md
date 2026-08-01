# Fishing Logbook

**Fishing Logbook** is a Laravel-based web application designed for anglers to log, track, and analyze their fishing trips, catches, gear, and fishing locations.

---

## 🎣 Features & Usage

- **Angler Profiles & Avatars**: Create and customize angler profiles, manage account settings, and update profile avatars.
- **Catch & Record Logging**: Record detailed fishing catches, including fish family, breed, size/weight, lure used, date/time, and exact location.
- **⚡ Boat Quick Catch Mode**: Touch-optimized mobile interface for rapid logging on the water (`/record/quick`).
- **⛵ Offline Boat Logging & Sync**: Progressive Web App (PWA) with `IndexedDB` local storage and duplicate prevention (`client_id` UUIDs) for logging catches without internet access.
- **Lake & Waterbody Tracking**: Catalog visited lakes and waterbodies, log visits, and maintain historical location records.
- **Lure & Tackle Catalog**: Manage lures, baits, and tackle setups to analyze what gear works best in different conditions.
- **Expeditions & Crew Management**: Group fishing trips into expeditions and keep track of crew members on shared trips.
- **Community & Posts Feed**: Share updates, trip notes, and fishing posts.
- **Admin Dashboard**: Administrator tools for managing users and application metadata.

---

## 🏗️ Architecture & Tech Stack

- **Framework**: Laravel 10 (PHP 8.2)
- **Runtime Environment**: Containerized via **Laravel Sail** running in **WSL 2 (Ubuntu)**
- **Database**: MySQL 8.0 (Containerized via Sail)
- **Offline Engine**: Progressive Web App (Service Worker + IndexedDB + Client UUID Idempotency)
- **Local Application URL**: [http://localhost](http://localhost) or `http://fishinglog.local`

---

## ⛵ Offline Boat Logging & Cabin Wi-Fi Network Setup

The application features a **Progressive Web App (PWA)** with an **Offline Boat Logging Engine** (`IndexedDB`) that allows you and your crew to log catches out on remote lakes without cellular service, and automatically sync them when back at the cabin.

---

### 🌐 1. Cabin Wi-Fi & Multi-Device Local Setup

To make the web application accessible to phones, tablets, and laptops on your local cabin Wi-Fi network:

1. **Find your Host Machine's Local IP Address**:
   - On the Windows host computer running Sail, open PowerShell or Command Prompt and run:
     ```powershell
     ipconfig
     ```
   - Locate your local IPv4 address (e.g. `192.168.1.150`).

2. **Configure `.env` Network Access**:
   - Set `APP_URL` in `.env` to your local IP address:
     ```env
     APP_URL=http://192.168.1.150
     ```

3. **Allow Windows Firewall Access**:
   - Ensure inbound connections to port `80` (or `8000`) are allowed through Windows Defender Firewall for private local networks.

4. **Accessing & Installing PWA on Mobile Devices**:
   - On any phone or tablet connected to the cabin Wi-Fi, open Safari (iOS) or Chrome (Android) and navigate to `http://192.168.1.150`.
   - Tap **"Share" -> "Add to Home Screen"** (iOS) or **"Install App"** (Android) to add the app icon to your phone's home screen.

---

### 🎣 2. How to Log Catches Offline on the Boat

1. **Open Quick Catch Mode (`/record/quick`)**:
   - Open the app icon on your phone or tap **`⚡ Quick Catch`** in the navigation bar.
2. **Log the Catch Out on the Water**:
   - Select the Angler, Lake, Fish Species, Length (in), Weight (lbs), and Lure.
   - Tap **"💾 Save Catch Log"**.
3. **Offline Queueing**:
   - If you are out of cell/Wi-Fi range, the app instantly saves the catch to local device storage (`IndexedDB`).
   - A yellow status badge appears in the header: **`⛵ 1 Catches Queued (Sync Now)`**.
   - Every catch generates a unique Client UUID (`client_id`) to **prevent duplicate entries**.

---

### 🔄 3. Synchronizing Catches at the Cabin

1. **Automatic Sync**:
   - Once your phone/tablet reconnects to the cabin Wi-Fi network, the background sync engine automatically detects connectivity and uploads all queued catches to the server.
2. **Manual One-Tap Sync**:
   - You can also tap the yellow **`⛵ Catches Queued (Sync Now)`** button in the header anytime to trigger an immediate manual synchronization.
3. **Completion Notice**:
   - A confirmation banner appears (`🎉 Successfully synced catches to server!`) and your MySQL database is updated cleanly without duplicates.

---

## 🚀 Local Development & Setup

This guide documents environment management, server lifecycle commands, test execution, and common utility tasks using **Laravel Sail + WSL 2**.

### Prerequisites
- Windows 10/11 with **WSL 2** (Ubuntu) and **Docker Desktop** (WSL 2 backend enabled).

---

### Quick Reference Commands

#### 🚀 Starting the Application
Start the containerized environment in detached mode:
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail up -d"
```

#### 🧪 Running Tests
Run the PHPUnit test suite inside the Sail container:
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail test"
```

Run a specific test class or filter:
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail test --filter=OfflineSyncApiTest"
```

#### 🛑 Stopping the Application
Stop running Sail containers:
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail stop"
```

Stop containers and remove container networks/volumes:
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail down"
```

---

## 🛠️ Common Utility Commands

| Action | Command |
| :--- | :--- |
| **Check Container Status** | `wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail ps"` |
| **Run Database Migrations** | `wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail artisan migrate"` |
| **Laravel Tinker Shell** | `wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail artisan tinker"` |
| **Container Bash Shell** | `wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail bash"` |
| **View Realtime Logs** | `wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail logs -f"` |

---

## 💾 Database Backup & Restore

To safeguard your trip logs, catch history, and gear data after a fishing trip, you can export (backup) and import (restore) your MySQL database using Laravel Sail.

### 📦 Backing Up the Database

Run this command to create a database backup file (uses environment variables automatically configured in Sail):

**Quick Backup (`backup.sql`):**
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail exec -T mysql bash -c 'mysqldump --no-tablespaces -u \$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE'" > backup.sql
```

**Timestamped Post-Trip Backup (e.g. `backup_20260801_0910.sql`):**
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail exec -T mysql bash -c 'mysqldump --no-tablespaces -u \$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE'" > "backup_$(date +%Y%m%d_%H%M%S).sql"
```

---

### 📥 Restoring the Database

To restore data from a `.sql` backup file back into the Sail MySQL database:

```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail exec -T mysql bash -c 'mysql -u \$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE'" < backup.sql
```

> **Note:** Ensure your Sail containers are running (`sail up -d`) prior to running backup or restore commands.
