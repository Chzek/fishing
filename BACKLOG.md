# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application.

---

## 🚀 High Priority (P0 / Near Term)

### 1. Test Suite Isolation & `fishing_test` Schema Fix
- **Status**: Backlog
- **Impact**: High
- **Description**: Configure `phpunit.xml` and test database transaction reset logic so `./vendor/bin/sail test` executes cleanly without table collision or schema migration lock errors on the `fishing_test` MySQL database.

### 2. Performance & Query Optimization in Catch Directory & Angler Profile
- **Status**: Backlog
- **Impact**: High (Fires **38 SQL queries** on Catch Directory, **29 SQL queries** on Angler Profile)
- **Empirical Profiling Findings**:
  - **Catch Directory (38 Queries, ~125ms DB time)**: [`RecordController::index`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php#L37-L120) runs 38 database queries per request. SQL joins calling `DATE(records.caught)` cause **Full Table Scans** (`rows: 991`, `Using temporary; Using filesort`) taking ~65ms of cumulative DB time.
  - **Angler Profile (29 Queries, ~164ms total time)**: [`AnglerProfileController::show`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Angler/AnglerProfileController.php#L14-L122) fires 29 queries. It executes an **unbounded `Record::all` memory load** (`Record::where('anglers_id', $angler->id)->get()`) loading entire catch histories into PHP RAM alongside paginated results. It also runs 6 separate queries for basic counts/averages that belong in a single `selectRaw()` query.
- **Target Solutions**:
  - Consolidate weather joins and aggregate metrics into `selectRaw()` queries across both controllers.
  - Remove redundant unbounded record memory load in `AnglerProfileController`.
  - Cache Angler Profile statistics via `Cache::remember("angler_stats_{$angler->id}")` reducing query count from **29 $\rightarrow$ 2**.

---

## 🛠️ Medium Priority (P1 / Structural Improvement)

### 3. Controller Refactoring into Action Classes & DTOs
- **Status**: Backlog
- **Impact**: Medium
- **Description**: Extract business logic from fat controllers ([`RecordController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php), [`LureController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/LureController.php), [`ExpeditionController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/ExpeditionController.php)) into single-responsibility Action classes (`CreateCatchRecordAction`, `ProcessPhotoUploadAction`).

### 4. Async NAS Sync via Queued Background Jobs (`SyncNasJob`)
- **Status**: Backlog
- **Impact**: Medium
- **Description**: Wrap [`NasSyncService`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php) triggers into a queueable background job (`SyncNasJob`) to prevent HTTP request timeouts during large photo payload syncs.

### 5. Legacy Dependency Debt Pruning
- **Status**: Backlog
- **Impact**: Medium
- **Description**: Remove `laravel/ui` (legacy Bootstrap auth scaffolding) and replace `spatie/laravel-html` calls with native Blade components (`<x-input>`, `<x-select>`).

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

