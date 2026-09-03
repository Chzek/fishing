# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application, continuously audited and prioritized by our specialized agent team (`laravel-architect`, `livewire-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, `ui-ux-auditor`).

---

## 🎯 Active Priority Roadmap (Ranked by Impact & Value)

### 🔥 Priority 1 (P1): High-Impact Usability & Reliability

#### 1. Static Analysis & Strict Typing Auditing (`larastan/larastan`)
- **Agents**: `laravel-architect` & `phpunit-test-architect`
- **Impact**: **High** (Type Safety & Regression Prevention)
- **Description**: Configure PHPStan / Larastan at Level 5+ to guarantee strict typing, Eloquent relationship validation, and null-safety across all 13 models, services, and Action classes.

---

### 🚀 Priority 2 (P2): Angling Experience & Reactive Workflows

#### 2. Global Quick Catch Slide-Over Drawer Modal (`@livewire('modals.quick-catch-modal')`)
- **Agents**: `livewire-architect` & `ui-ux-auditor`
- **Impact**: **High** (1-Tap Catch Logging Anywhere)
- **Description**: Extract a slide-over modal component triggered from any page (Top Nav, Map Explorer `/map`, or Expedition Trip view) via `$dispatch('open-quick-catch')`, allowing anglers to log a catch without leaving their active map or trip dossier.

#### 3. Interactive Tacklebox Category Trays & Color Variant Grid (`@livewire('tacklebox.lure-catalog')`)
- **Agents**: `livewire-architect` & `seasoned-angler-advisor`
- **Impact**: **Medium-High** (Sub-second Tray Expansion)
- **Description**: Reusable Livewire catalog components for 2-Tier Category Trays, expanding lure model accordions, and inline color variant management.

#### 4. Species Dossier Recommended Tackle Badging & Quick Catch Shortcuts (`seasoned-angler-advisor` & `ui-ux-auditor`)
- **Agents**: `seasoned-angler-advisor` & `ui-ux-auditor`
- **Impact**: **Medium** (Usability & Angling Knowledge)
- **Description**: Enrich species dossier pages (`/fish/{id}`) and boat Quick Catch logger:
  - Display top-producing tackle pairing badges (e.g., *"Top Lure for Walleye: Rapala Shad Rap"*).
  - Add target species quick-filter shortcuts on the boat Quick Catch logger form.

#### 5. Waterbody Regulations & Exceptions Review Framework (`seasoned-angler-advisor`)
- **Agents**: `seasoned-angler-advisor`
- **Impact**: **Medium** (Regulatory Usability)
- **Description**: Provide a structured UI for anglers to inspect and verify specific lake exceptions and sanctuary rules directly against official FMZ regulation guides when reviewing individual waterbody pages.

---

### ⚙️ Priority 3 (P3): Infrastructure, Sync & Optimization

#### 7. NAS Sync Base64 Media Payload Chunking (`nas-sync-architect`)
- **Agents**: `nas-sync-architect`
- **Impact**: **Medium** (NAS Sync RAM Optimization)
- **Description**: Enhance [`NasSyncService.php`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php) photo payload synchronization to stream binary media files in multipart chunks instead of loading large base64 strings into PHP RAM during bulk catch syncs.

#### 8. Live Weather & Telemetry Barometer Widget (`@livewire('widgets.weather-telemetry')`)
- **Agents**: `livewire-architect` & `seasoned-angler-advisor`
- **Impact**: **Medium** (Real-Time Weather Signals)
- **Description**: Reactive weather widget that auto-fetches or updates live barometric pressure trends, wind velocity/direction, and surface water temp when selecting lakes during catch logging.

#### 9. Catch Logbook CSV / Excel Streaming Export (`spatie/laravel-simple-excel`)
- **Agents**: `laravel-architect`
- **Impact**: **Low-Medium** (Data Portability)
- **Description**: Zero-overhead streaming CSV/XLSX export for annual Catch Logbooks and Expedition summary sheets.

---

## 📦 Laravel & Livewire Ecosystem Package Evaluation

| Package | Category | Primary Benefit | Recommended Priority |
| :--- | :--- | :--- | :--- |
| [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup) | DB Safety / DevOps | Automated, scheduled timestamped database and media directory backups to `storage/app/backups/` & NAS. | **Completed** |
| [`blade-ui-kit/blade-lucide-icons`](https://github.com/blade-ui-kit/blade-lucide-icons) | Blade / UI | Server-rendered Lucide icons (`<x-lucide-fish />`) eliminating JS DOM injection delays & SVG duplication. | **Completed** |
| [`larastan/larastan`](https://github.com/larastan/larastan) *(dev)* | Static Analysis | Strict level typing, Eloquent relationship validation, and null safety checks across all 13 models & services. | **P1 (Immediate)** |
| [`matanyadaev/laravel-eloquent-spatial`](https://github.com/matanyadaev/laravel-eloquent-spatial) | GIS / Mapping | Native MySQL 8 spatial geometry (`Point`, `Polygon`) with distance scopes (`whereDistance`) for Leaflet waypoint radius queries. | **P2 (Map Feature)** |
| [`spatie/laravel-simple-excel`](https://github.com/spatie/laravel-simple-excel) | Data Export | Zero-overhead streaming CSV/XLSX export for annual Catch Logbooks and Expedition summary sheets. | **P2 (Feature-driven)** |
| [`livewire/volt`](https://github.com/livewire/volt) | Livewire DX | Single-file reactive components for lightweight boat widgets (Barometer Telemetry, species badges). | **P3 (DX)** |
| [`laravel/boost`](https://github.com/laravel/boost) *(dev)* | AI Tooling / MCP | Optional MCP server for IDEs; requires Sail container wrapper configuration. | **P3 (Optional)** |

---

## 🏆 Completed Milestones (Merged into `master`)

1. **Automated Database & Media Backup Package (`spatie/laravel-backup`)**:
   - Integrated `spatie/laravel-backup:^9.0` configured for full MySQL dumps, uploaded media assets (`storage/app/public`), and multi-tier grandfather-father-son retention rules (7 days all, 30 days daily, 8 weeks weekly, 12 months monthly, 2 years yearly, 5 GB storage ceiling).
   - Configured production-only automated Console schedules in [`routes/console.php`](file:///home/gmroczek/git/fishing/routes/console.php) (`backup:clean` at 01:00, `backup:run --only-db` at 02:00, full `backup:run` on Sundays at 03:00).
   - Hooked automated pre-migration safety snapshots into [`synology-nas-deploy/update_nas.sh`](file:///home/gmroczek/git/fishing/synology-nas-deploy/update_nas.sh).
   - Documented all CLI backup commands and disaster recovery restoration procedures in [`README.md`](file:///home/gmroczek/git/fishing/README.md).
2. **Codebase Usage Cleanup, Dead Code Elimination & Action Class Wiring**:
   - Pruned dead/unrendered legacy Blade components (`stat-card.blade.php`, `components/form/input.blade.php`, `resources/views/vendor/log-viewer/`).
   - Injected and wired [`CreateCatchRecordAction`](file:///home/gmroczek/git/fishing/app/Actions/Records/CreateCatchRecordAction.php) and [`ProcessPhotoUploadAction`](file:///home/gmroczek/git/fishing/app/Actions/Media/ProcessPhotoUploadAction.php) into [`RecordController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php), [`RecordApiController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/RecordApiController.php), and [`PhotoController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/PhotoController.php).
   - Removed empty/unimplemented stub routes and methods in [`routes/web.php`](file:///home/gmroczek/git/fishing/routes/web.php) (`CrewController`, `PostController`, `FishFamilyController`, `FishBreedController`).
   - Cleaned up obsolete Laravel Mix artifacts (`public/mix-manifest.json`, `public/js/app.js`, `public/css/app.css`).
   - Pruned redundant packages (`spatie/laravel-html`, `laravel/helpers`, `lucide`, `alpinejs`) and aliases.
3. **Automated NAS Deployment Synchronization (`synology-nas-deploy/update_nas.sh`)**:
   - Added automated `composer install --no-dev --optimize-autoloader --no-interaction` execution inside the `fishinglog_app` Docker container on every GitHub Actions deployment, eliminating missing package 500 errors on the Synology NAS.
4. **Test Suite Execution Performance Optimization (Playwright Session Caching & Fast Target)**:
   - Implemented `globalSetup` in [`tests/e2e/global-setup.js`](file:///home/gmroczek/git/fishing/tests/e2e/global-setup.js) generating pre-authenticated storage state (`playwright/.auth/user.json`), eliminating redundant per-test `/login` cycles across all 56 E2E tests.
   - Added dedicated `"test:e2e:fast"` npm target in [`package.json`](file:///home/gmroczek/git/fishing/package.json) executing Desktop Chromium suite in **1.4 minutes (84 seconds)** down from 3.5+ minutes (over 60% speed reduction).
   - Configured robust DOM and client-side validation assertions in [`tests/e2e/auth.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/auth.spec.js) and Livewire helpers for rock-solid deterministic execution in Laravel Sail.
5. **Playwright Interactive E2E Front-End Test Suite Expansion & Master README Consolidation**:
   - Built an automated 8-spec end-to-end testing suite (56 multi-viewport tests across Desktop Chromium and Google Pixel 10 Pro touch emulation).
   - Validated full coverage across Authentication, Catch Directory Generic Data Table (search, sorting, density, column picker), Tacklebox (KPIs, categories, lures, telemetry), Boat Quick Catch Logger & Leaflet Lake Explorer, Species Dossier, and Autocomplete Lure Selector.
   - Merged and consolidated `readme.md` and `README.md` into a unified master documentation guide detailing all 10 core subsystems, offline boat Wi-Fi SSL architectures, and test execution procedures.
6. **Searchable Autocomplete Lure & Tackle Selector (`@livewire('ui.lure-selector')`)**:
   - Built a reactive Livewire 3 autocomplete component with real-time debounced query filtering, category pill headers, 2-tier grouped tackle lists, manufacturer badges, technical specs, and verified catch stats.
   - Replaced static HTML select dropdowns in Standard Catch Logger (`/record/create`), Edit Catch (`/record/{id}/edit`), and boat Quick Catch (`/record/quick`).
   - Covered with PHPUnit Feature tests (`tests/Feature/LureSelectorLivewireTest.php`) and Playwright E2E browser tests (`tests/e2e/lure-selector.spec.js`).
7. **Blade Lucide Icon Native Migration (`mallardduck/blade-lucide-icons`)**:
   - Replaced all runtime client-side JavaScript icon injection (`<i data-lucide="...">` + `createIcons()`) across 72 Blade templates with server-rendered `<x-lucide-...>` and `<x-dynamic-component :component="'lucide-' . $icon" />` tags.
   - Stripped client-side JS bundle overhead, removed Livewire DOM morph and SPA navigation re-scan event listeners, eliminating icon flickering and layout shifts.
8. **Complete 18-Species Fish Avatar Library & Prompt Architecture**:
   - Generated high-resolution vector artwork with cel-shading and unified slate-blue badge styling for all 18 freshwater species in [`public/images/fish/avatars/`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/).
   - Documented master style guide and prompts in [`public/images/fish/avatars/PROMPTS.md`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/PROMPTS.md).
   - Removed legacy `.svg` files and integrated `<x-fishAvatar>` across Species Index, Catches Directory, User & Angler Profile Personal Bests, and Admin Portal.
9. **Species Dossier Direct Catch Directory Banner**:
   - Replaced redundant recent catch cards on [`/fish/{id}`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php) with an interactive **Catches Logbook Directory** banner link pre-filtered to the species.
10. **Admin User-Angler Linking & Account Management**:
    - Added `admin_user_actions` column to `GenericDataTable` allowing administrators to pair registered users to Angler profiles, toggle admin roles, and manage accounts.
11. **App-Wide Generic Livewire 3 Data Table (`@livewire('components.generic-data-table')`)**:
    - Created unified data table with dynamic columns, instant search, multi-column Shift-click sorting, relation counts, soft-delete views, and custom query scopes across Lakes, Anglers, Catches, Expeditions, Trash, and Admin Users.
12. **Controller Refactoring into Action Classes & Domain Services**:
    - Extracted business logic from fat controllers into single-responsibility Action classes and Domain Services ([`CreateLureVariantAction`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php), [`ExpeditionAnalyticsService`](file:///home/gmroczek/git/fishing/app/Services/ExpeditionAnalyticsService.php)).
13. **Expedition & Species Dossier Query Optimization**:
    - Eliminated N+1 queries across trip dashboards, lake distribution tables, and monthly telemetry charts.
