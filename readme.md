# Fishing Logbook

**Fishing Logbook** is a Laravel-based web application designed for anglers to log, track, and analyze their fishing trips, catches, gear, and fishing locations.

---

## 🎣 Features & Usage

- **Angler Profiles & Avatars**: Create and customize angler profiles, manage account settings, and update profile avatars.
- **Catch & Record Logging**: Record detailed fishing catches, including fish family, breed, size/weight, lure used, date/time, and exact location.
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
- **Local Application URL**: [http://localhost](http://localhost) or `http://fishinglog.local`

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
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail test --filter=AnglerTest"
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
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail exec -T mysql bash -c 'mysqldump -u \$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE'" > backup.sql
```

**Timestamped Post-Trip Backup (e.g. `backup_20260801_0910.sql`):**
```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail exec -T mysql bash -c 'mysqldump -u \$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE'" > "backup_$(date +%Y%m%d_%H%M%S).sql"
```

---

### 📥 Restoring the Database

To restore data from a `.sql` backup file back into the Sail MySQL database:

```bash
wsl bash -c "cd /mnt/c/git/fishing && ./vendor/bin/sail exec -T mysql bash -c 'mysql -u \$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE'" < backup.sql
```

> **Note:** Ensure your Sail containers are running (`sail up -d`) prior to running backup or restore commands.

