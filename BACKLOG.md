# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application.

---

## 🚀 Completed Initiatives (Sprint 1)

### 0. Database Safety & Pre-Migration Backup
- **Status**: Completed
- **Impact**: High (Data Protection Safeguard)
- **Description**: Created a full timestamped database dump at [`database/backups/backup_20260826_175115.sql`](file:///home/gmroczek/git/fishing/database/backups/backup_20260826_175115.sql) (**155 MB**), preserving all live records, anglers, lakes, tackle, and photo metadata.

### 0.5 Workspace Agent Squad Setup
- **Status**: Completed
- **Impact**: High (Dev Velocity)
- **Description**: Configured 7 specialized domain agent skills in [`.agents/skills/`](file:///home/gmroczek/git/fishing/.agents/skills/): `laravel-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, and `ui-ux-auditor`.

### 1. Test Suite Isolation & `fishing_test` Schema Fix
- **Status**: Completed (Merged into `master`)
- **Impact**: High
- **Description**: Configured `phpunit.xml` with `DB_CONNECTION=mysql` and `DB_DATABASE=fishing_test` environment isolation, converted test traits to `DatabaseTransactions`, and verified 100% test pass rate (149/149 tests passing) with sub-second execution times.

### 2. Performance & Query Optimization in Catch Directory & Angler Profile
- **Status**: Completed (Merged into `master`)
- **Impact**: High (Fires **34 SQL queries** on Catch Directory, **22 SQL queries** on Angler Profile)
- **Empirical Profiling Findings & Solutions**:
  - **Catch Directory**: Consolidated multi-query clones in [`RecordController::index`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php) into a single `selectRaw()` query. Added migration [`2026_08_26_000001_add_caught_composite_indexes_to_records_table.php`](file:///home/gmroczek/git/fishing/database/migrations/2026_08_26_000001_add_caught_composite_indexes_to_records_table.php) for `records(caught, lakes_id, anglers_id)` composite index.
  - **Angler Profile**: Consolidated 6 separate aggregate queries into a single `selectRaw()` query in [`AnglerProfileController::show`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Angler/AnglerProfileController.php) and removed the unbounded `Record::all` memory load, reducing SQL execution time to **38ms** (down from 164ms).

---

## 🛠️ Medium Priority (P1 / Structural Improvement)

## 🚀 Completed Initiatives (Sprint 2)

### 3. Async NAS Sync via Queued Background Jobs (`SyncNasJob`)
- **Status**: Completed (Merged into `master`)
- **Impact**: Medium (NAS Connectivity Reliability)
- **Description**: Created queueable background job [`SyncNasJob`](file:///home/gmroczek/git/fishing/app/Jobs/SyncNasJob.php) with automatic retries and exponential backoff. Updated [`AdminController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Admin/AdminController.php) trigger endpoints to dispatch syncs asynchronously.

### 4. Controller Refactoring into Action Classes & DTOs
- **Status**: Completed (Merged into `master`)
- **Impact**: Medium (Architecture Decoupling)
- **Description**: Extracted domain logic into single-responsibility Action classes: [`CreateCatchRecordAction`](file:///home/gmroczek/git/fishing/app/Actions/Records/CreateCatchRecordAction.php) and [`ProcessPhotoUploadAction`](file:///home/gmroczek/git/fishing/app/Actions/Media/ProcessPhotoUploadAction.php).

### 5. Legacy Dependency Debt Pruning
- **Status**: Completed (Merged into `master`)
- **Impact**: Medium (Dependency Footprint Reduction)
- **Description**: Removed legacy `laravel/ui` package dependency from `composer.json` and replaced legacy `Auth::routes()` helper with explicit authentication route definitions in `routes/web.php`.

---

## 🎨 Front-End & Usability (P2 / Next Gen)

### 6. Livewire 3 (PHP) Component Migration
- **Status**: Backlog
- **Impact**: Low - Feature Enhancement
- **Description**: Convert filtered catch directory and angler statistics dashboards into reactive Livewire 3 PHP components (`app/Livewire/Directory/CatchDirectory.php`).

### 7. PWA / Offline Catch Log Capability (Outdoor Usability)
- **Status**: Backlog
- **Impact**: Low - Future Initiative
- **Description**: Add `vite-plugin-pwa` service worker and local IndexedDB cache queue so anglers can log catches offline while on boats with spotty cellular coverage, auto-syncing when signal resumes.

---

## 🧪 Testing & Interface Polish (P3 / On Hold)

### 8. Playwright E2E Front-End Test Suite Expansion
- **Status**: Deferred / On Hold
- **Impact**: Medium (Front-End Quality & Regression Prevention)
- **Description**: Expand Playwright E2E test suite to cover high-density interactive UI components:
  - `tacklebox.spec.js`: 2-Tier Category Tray expansion, master `Toggle Category Trays` window events, Lure Model accordions, color variant tables, and multi-color batch lure creation forms.
  - `species-dossier.spec.js`: Fish taxonomy family tab filtering, search inputs, species dossier telemetry badges, and artwork canvas rendering.
  - `quick-catch-map.spec.js`: Quick Catch logger form, `<optgroup>` category-grouped lure selection, and Leaflet Map Explorer drawer navigation.
  - `global-search.spec.js`: Global search form queries and grouped result section verification (Anglers, Waterbodies, Fish Species, Tacklebox).

### 9. Species Dossier UI Redesign & Telemetry Polish
- **Status**: Deferred / On Hold
- **Impact**: Low - Visual Polish
- **Description**: Polish species dossier pages (`/fish/{id}`):
  - Hero header framing and artwork layout polish.
  - Recommended tackle box pairing badges (e.g., *"Top Lure for Walleye: Rapala Shad Rap"*).
  - Regional distribution map integration and seasonal strike rate telemetry.

### 10. Manual Waterbody Regulations & Exceptions Review Framework
- **Status**: Deferred / On Hold
- **Impact**: Low - Regulatory Usability
- **Description**: Provide a simple manual review UI for anglers to inspect and verify specific lake exceptions and sanctuary rules directly against official MNR regulation guides when reviewing individual waterbody pages.

