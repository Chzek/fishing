# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application, continuously audited and prioritized by our specialized agent team (`laravel-architect`, `livewire-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, `ui-ux-auditor`).

---

## 🎯 Active Priority Roadmap (Ranked by Impact & Value)

### 🚀 Priority 1 (P1): Angling Experience & Reactive Workflows

*All current P1 features completed. Advancing priority backlog.*


---

### ⚙️ Priority 2 (P2): Infrastructure, Performance & Refactoring

#### 1. Synology NAS Connectivity & Real-Time Sync Diagnostic Console (`/admin/sync`)
- **Agents**: `nas-sync-architect`, `laravel-architect`
- **Impact**: **Medium** (Admin & Operations Reliability)
- **Description**: Enhanced diagnostics dashboard inside Admin Portal showing live Synology NAS ping latency, mutual SSL certificate status, per-model synchronization outbox breakdown, and automated retry mechanism for failed media chunk transfers.

#### 2. Database Composite Index Optimization & Query Profiling Audit
- **Agents**: `query-profiler-optimizer`, `laravel-architect`
- **Impact**: **Medium** (Scalability & Low-Latency Performance)
- **Description**: Add targeted composite MySQL indexes to `records` (`(anglers_id, caught)`, `(lakes_id, fish_breeds_id)`, `(fish_breeds_id, length)`) to accelerate generic data table multi-sort filtering, species telemetry aggregations, and personal best queries under high logbook volume.

#### 3. Test Suite Architecture Modernization & Strict Assertion Refactoring (`testing-best-practices`)
- **Agents**: `phpunit-test-architect`, `laravel-architect`
- **Impact**: **Medium** (Test Suite Health, Determinism & Rigor)
- **Description**: Modernize the PHPUnit backend test suite according to upstream Laravel Boost `testing-best-practices`:
  - **Strict Assertions & DB Count Helpers**: Replace loose `$this->assertEquals()` with strict `$this->assertSame()` across feature tests (`NasSyncServiceTest`, `PhotoUploadTest`, etc.) and adopt `$this->assertDatabaseCount()` / `$this->assertDatabaseEmpty()`.
  - **Behavioral Relationship Testing**: Refactor legacy reflection unit tests (`method_exists()`) in `RecordTest`, `FishFamilyTest`, and `LakeTest` into observable Eloquent relationship behavior and query tests.
  - **Self-Contained Fixtures**: Remove mutable database record creation from `setUp()` in legacy unit/feature tests (`AnglerTest`, `CrewTest`) to guarantee independent, self-contained test execution.
  - **Boilerplate Pruning**: Remove obsolete default Laravel starter stub (`ExampleTest.php`).
  - **Triple-Tier Write Verifications**: Ensure all mutation endpoints verify the HTTP response/redirect, exact database row state (`assertDatabaseHas`), and any dispatched side effects.

#### 4. Consolidate `RecordController@index` Multi-Query Telemetry (`CatchTelemetryService`)
- **Agents**: `query-profiler-optimizer`, `laravel-architect`
- **Impact**: **Medium** (Controller Slimming & Query Optimization)
- **Description**: Refactor `RecordController@index` and `/record/directory` by extracting a dedicated `CatchTelemetryService`. Replace 7 consecutive cloned query executions with a consolidated aggregate query and cache layer.

#### 5. Livewire Reference Data Caching (`LureSelector` Categories)
- **Agents**: `livewire-architect`, `query-profiler-optimizer`
- **Impact**: **Low** (Sub-Second UI Responsiveness)
- **Description**: Cache distinct lure categories in `app/Livewire/Ui/LureSelector.php` with `Cache::remember('lure_categories', 86400, ...)` to eliminate redundant database extraction on every debounced keystroke.

#### 6. Dynamic Weather Relationship N+1 Elimination (`Record::scopeWithDailyWeather`)
- **Agents**: `query-profiler-optimizer`, `laravel-architect`
- **Impact**: **Medium** (N+1 Query Elimination)
- **Description**: Eliminate query-per-row execution in `Record::getDailyWeatherAttribute` by creating an explicit query scope `scopeWithDailyWeather($query)` for single-pass eager loading in collection views.

#### 7. Centralized Image Optimization & Upload Action (`ProcessPhotoUploadAction`)
- **Agents**: `laravel-architect`
- **Impact**: **Medium** (DRY Code Architecture)
- **Description**: Unify duplicate private `optimizeAndSaveImage()` helper methods in `AnglerController` and `FishBreedController` into `ProcessPhotoUploadAction` (or a dedicated `OptimizeAndStoreMediaAction`).

#### 8. Model Cast Modernization with Native Laravel 12 `casts()`
- **Agents**: `laravel-architect`
- **Impact**: **Medium** (Strict Type Safety)
- **Description**: Standardize all 13 Eloquent models (`Lake`, `Record`, `Lure`, `FishingZone`, `Photo`, etc.) to use Laravel 12's `protected function casts(): array` with explicit scalar and datetime types (`float`, `integer`, `boolean`, `datetime`).

#### 9. Controller Form Request Standardization (`StorePhotoRequest`, `StoreExpeditionRequest`, `StoreLakeRequest`)
- **Agents**: `laravel-architect`
- **Impact**: **Low** (Validation Consistency)
- **Description**: Extract dedicated Form Request classes for `PhotoController`, `ExpeditionController`, and `LakeController` to replace inline `$request->validate()` calls with uniform validation and authorization gating.

---

## 📦 Laravel & Livewire Ecosystem Package Evaluation

| Package | Category | Primary Benefit | Recommended Priority |
| :--- | :--- | :--- | :--- |
| [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup) | DB Safety / DevOps | Automated, scheduled timestamped database and media directory backups to `storage/app/backups/` & NAS. | **Completed** |
| [`blade-ui-kit/blade-lucide-icons`](https://github.com/blade-ui-kit/blade-lucide-icons) | Blade / UI | Server-rendered Lucide icons (`<x-lucide-fish />`) eliminating JS DOM injection delays & SVG duplication. | **Completed** |
| [`larastan/larastan`](https://github.com/larastan/larastan) *(dev)* | Static Analysis | Strict level typing, Eloquent relationship validation, and null safety checks across all 13 models & services. | **Completed** |
| [`matanyadaev/laravel-eloquent-spatial`](https://github.com/matanyadaev/laravel-eloquent-spatial) | GIS / Mapping | Native MySQL 8 spatial geometry (`Point`, `Polygon`) with spherical distance scopes (`whereDistanceSphere`) for sub-millisecond waypoint & nearby radius lookups. | **Completed** |
| [`livewire/volt`](https://github.com/livewire/volt) | Livewire DX | Single-file reactive components for lightweight boat widgets (Barometer Telemetry, species badges). | **P3 (DX)** |
| [`laravel/boost`](https://github.com/laravel/boost) *(dev)* | AI Tooling / MCP | Embedded MCP server and AI guidelines/skills for Antigravity-assisted development. | **Completed** |

---

## 🏆 Completed Milestones (Merged into `master`)

1. **AI Governance & Durable Codebase Conventions Bootstrap (`.ai/rules/`)**:
   - Established permanent, path-scoped rule registry in [`.ai/rules/index.md`](file:///home/gmroczek/git/fishing/.ai/rules/index.md) and area rule files for Models, Actions & Services, Controllers & Form Requests, Livewire 3 & Frontend, Testing with Sail, and remote Synology NAS Synchronization.
   - Fully aligned with Antigravity's on-demand skills architecture in `.agents/skills/` and global project safety rules in `.agents/AGENTS.md`.
2. **Native MySQL 8 Spatial Engine & Eloquent Geometry Integration (`matanyadaev/laravel-eloquent-spatial`)**:
   - Integrated `matanyadaev/laravel-eloquent-spatial:^4.8` with native MySQL 8 `POINT` geometries (SRID 4326 WGS 84) on `lakes` and `records` tables.
   - Added `HasSpatial` trait and `'location' => Point::class` casting across [`Lake.php`](file:///home/gmroczek/git/fishing/app/Models/Lake.php) and [`Record.php`](file:///home/gmroczek/git/fishing/app/Models/Record.php).
   - Wired bidirectional automatic lifecycle synchronization between traditional `latitude`/`longitude` floats and spatial `Point` objects for 100% backward compatibility with existing web forms, APIs, and two-way Synology NAS synchronization.
   - Refactored `Lake::nearby()` to execute native spherical distance queries (`whereDistanceSphere`, `withDistanceSphere`, `orderByDistanceSphere`), eliminating legacy CPU-bound Haversine trigonometry.
   - Verified with dedicated feature tests ([`SpatialModelTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/SpatialModelTest.php) and [`OfflineMapTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/OfflineMapTest.php)).
3. **Personal Best Trophy Cards Grayscale Watermark Graphics**:
   - Engineered scalable, vector Blade components ([`watermarkTapeMeasure.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkTapeMeasure.blade.php) and [`watermarkDialScale.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkDialScale.blade.php)) with diagonal ribbon rotation and vintage platform scale line-art in subtle slate-gray styling.
   - Seamlessly integrated across Personal Best cards on Lake Show, Expedition Show, Angler Profile, and User Profile views.
4. **AI Development Infrastructure & Agent Skills Integration (`laravel/boost`)**:
   - Installed `laravel/boost:^2.8` and `laravel/mcp` as development dependencies within Laravel Sail.
   - Configured [`boost.json`](file:///home/gmroczek/git/fishing/boost.json) and [`AGENTS.md`](file:///home/gmroczek/git/fishing/AGENTS.md) scoped specifically for **Antigravity**.
   - Integrated and synced upstream standard agent skills into [`.agents/skills/`](file:///home/gmroczek/git/fishing/.agents/skills) (`infer-conventions`, `laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`, `pulse-development`, `deploying-to-cloud`).
   - Wired automated post-update synchronization hook (`"@php artisan boost:update --ansi"`) into [`composer.json`](file:///home/gmroczek/git/fishing/composer.json).
5. **NAS Sync Multi-Chunk Media Streaming & SHA-256 Deduplication (`MediaSyncManager`)**:
   - Engineered dedicated [`MediaSyncManager`](file:///home/gmroczek/git/fishing/app/Services/MediaSyncManager.php) service for high-performance two-tier synchronization between local instances and the Synology NAS server.
   - Replaced bloated in-memory base64 encoding with 1 MB binary chunk slicing (`/api/v1/sync/media/chunk`) and streaming downloads (`/api/v1/sync/media/download`), reducing peak PHP memory consumption by >75% and eliminating +33% base64 network overhead.
   - Integrated SHA-256 checksum verification and pre-upload hash comparison (`/api/v1/sync/media/verify`) for zero-overhead deduplication.
   - Maintained full backward compatibility with legacy `_base64` payloads from older sync clients.
   - Fully tested with comprehensive feature tests in [`NasMediaChunkSyncTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasMediaChunkSyncTest.php), [`NasSyncServiceTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncServiceTest.php), and [`NasSyncApiTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncApiTest.php).
6. **Species Tactical Angler Intelligence Hub & Trophy Records Hall of Fame (`/fish/{id}`)**:
   - Transformed species dossier (`/fish/{id}`) into an Angler Intelligence Hub with unified dark hero styling (`bg-slate-900 border-slate-800`) and a 4-column KPI telemetry metrics row (Total Logged, Record Length, Record Weight, C&R Conservation rate).
   - Added Ontario Master Angler Benchmark bar, All-Time Length & Weight Champion spotlight cards, and Top 5 All-Time specimens ranking strip.
   - Built 4-Quadrant Tactical Matrix: Productive Tackle & Lures (categories, top models with PB, colorways), Waterbody Hotspot Rankings, Seasonal & Weather Triggers (water temp ranges, monthly catch curves, sky conditions), and Species Master Angler & C&R Ethics.
   - Integrated Waterbody Directory Table and direct link to pre-filtered Catch Logbook Directory (`/record/directory?species={id}`).
   - Covered with PHPUnit Feature tests ([`FishBreedControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/FishBreedControllerTest.php)) and Playwright E2E browser tests ([`species-dossier.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/species-dossier.spec.js)).
7. **Interactive Tacklebox Category Trays & Color Variant Grid (`@livewire('tacklebox.lure-catalog')`)**:
   - Built full-featured Telemetry & Depth-Tier Workstation Livewire 3 component in [`app/Livewire/Tacklebox/LureCatalog.php`](file:///home/gmroczek/git/fishing/app/Livewire/Tacklebox/LureCatalog.php) and [`resources/views/livewire/tacklebox/lure-catalog.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/tacklebox/lure-catalog.blade.php).
   - Features real-time multi-dimensional search & filtering (debounced query, category pills, brand selector, depth-zone pills: Surface 0ft, Shallow 1–5ft, Mid 6–10ft, Deep 10–20ft, Deep 20ft+).
   - Expandable Category Drawer Trays and Lure Model Cards with technical specs (Length, Weight, Depth range), Catch Efficiency Bars (verified catches & PB badges), and interactive Colorway Variant Grid.
   - Integrated 1-Click "+ Log Catch" buttons on every variant firing `open-quick-catch` with pre-filled `lure_id` into the Global Quick Catch Modal.
   - Built inline "+ Add Colorway Variant" modal with comma-separated batch color creation via [`CreateLureVariantAction.php`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php).
   - Covered with PHPUnit Feature tests ([`LureCatalogLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/LureCatalogLivewireTest.php)) and Playwright E2E browser tests ([`tacklebox.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/tacklebox.spec.js)).
8. **Global Quick Catch Slide-Over Drawer Modal (`@livewire('modals.quick-catch-modal')`)**:
   - Created reactive Livewire 3 slide-over modal drawer mounted globally in [`resources/views/layouts/app.blade.php`](file:///home/gmroczek/git/fishing/resources/views/layouts/app.blade.php) with dark frosted backdrop and Option C Telemetry v2 styling.
   - Connected 1-tap catch logging triggers across Desktop Sidebar, Mobile Sticky Header, Floating Navigation Bar (+), Map Explorer Lake Drawer, and Expedition Trip Dossier via `$dispatch('open-quick-catch', { lake_id, expedition_id, ... })` and global keyboard shortcut (`Alt + C` or `Q`).
   - Built with lazy-loaded dropdown options, device geolocation GPS acquisition, tacklebox lure selector integration, and instant trophy personal best celebration notifications.
   - Dispatches reactive `catch-saved`, `refresh-records`, and `refresh-map` events to parent pages upon logging.
   - Covered with PHPUnit tests ([`QuickCatchModalLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/QuickCatchModalLivewireTest.php)) and Playwright E2E browser tests ([`quick-catch-modal.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/quick-catch-modal.spec.js)).
9. **Static Analysis & Strict Typing Auditing (`larastan/larastan`)**:
   - Configured `larastan/larastan:^3.0` (PHPStan 2.x Level 5) in [`phpstan.neon`](file:///home/gmroczek/git/fishing/phpstan.neon) scanning `app/` and `routes/`.
   - Hardened all 13 Eloquent models with complete `@property` / `@property-read` docblocks and explicit relationship return types (`: BelongsTo`, `: HasMany`, `: HasManyThrough`, `: HasOne`, `: MorphMany`).
   - Hardened [`HasUuidAndSyncTracking`](file:///home/gmroczek/git/fishing/app/Traits/HasUuidAndSyncTracking.php) with `setAttribute()` to eliminate dynamic property mutations across all models.
   - Replaced all non-config `env()` calls with `config('services.nas.*')` for production config cache safety.
   - Annotated all JsonResource classes with `/** @mixin \Fishinglog\Models\<Model> */`.
   - Standardized controller docblock return types and eliminated 340+ typing bugs down to **0 static analysis errors**.
   - Integrated `"analyse": "vendor/bin/phpstan analyse --memory-limit=2G"` composer command and documented usage in `README.md` and `laravel-architect` skill.
10. **Automated Database & Media Backup Package (`spatie/laravel-backup`)**:
    - Integrated `spatie/laravel-backup:^9.0` configured for full MySQL dumps, uploaded media assets (`storage/app/public`), and multi-tier grandfather-father-son retention rules (7 days all, 30 days daily, 8 weeks weekly, 12 months monthly, 2 years yearly, 5 GB storage ceiling).
    - Configured production-only automated Console schedules in [`routes/console.php`](file:///home/gmroczek/git/fishing/routes/console.php) (`backup:clean` at 01:00, `backup:run --only-db` at 02:00, full `backup:run` on Sundays at 03:00).
    - Hooked automated pre-migration safety snapshots into [`synology-nas-deploy/update_nas.sh`](file:///home/gmroczek/git/fishing/synology-nas-deploy/update_nas.sh).
    - Documented all CLI backup commands and disaster recovery restoration procedures in [`README.md`](file:///home/gmroczek/git/fishing/README.md).
11. **Codebase Usage Cleanup, Dead Code Elimination & Action Class Wiring**:
    - Pruned dead/unrendered legacy Blade components (`stat-card.blade.php`, `components/form/input.blade.php`, `resources/views/vendor/log-viewer/`).
    - Injected and wired [`CreateCatchRecordAction`](file:///home/gmroczek/git/fishing/app/Actions/Records/CreateCatchRecordAction.php) and [`ProcessPhotoUploadAction`](file:///home/gmroczek/git/fishing/app/Actions/Media/ProcessPhotoUploadAction.php) into [`RecordController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php), [`RecordApiController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/RecordApiController.php), and [`PhotoController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/PhotoController.php).
    - Removed empty/unimplemented stub routes and methods in [`routes/web.php`](file:///home/gmroczek/git/fishing/routes/web.php) (`CrewController`, `PostController`, `FishFamilyController`, `FishBreedController`).
    - Cleaned up obsolete Laravel Mix artifacts (`public/mix-manifest.json`, `public/js/app.js`, `public/css/app.css`).
    - Pruned redundant packages (`spatie/laravel-html`, `laravel/helpers`, `lucide`, `alpinejs`) and aliases.
12. **Automated NAS Deployment Synchronization (`synology-nas-deploy/update_nas.sh`)**:
    - Added automated `composer install --no-dev --optimize-autoloader --no-interaction` execution inside the `fishinglog_app` Docker container on every GitHub Actions deployment, eliminating missing package 500 errors on the Synology NAS.
13. **Test Suite Execution Performance Optimization (Playwright Session Caching & Fast Target)**:
    - Implemented `globalSetup` in [`tests/e2e/global-setup.js`](file:///home/gmroczek/git/fishing/tests/e2e/global-setup.js) generating pre-authenticated storage state (`playwright/.auth/user.json`), eliminating redundant per-test `/login` cycles across all 56 E2E tests.
    - Added dedicated `"test:e2e:fast"` npm target in [`package.json`](file:///home/gmroczek/git/fishing/package.json) executing Desktop Chromium suite in **1.4 minutes (84 seconds)** down from 3.5+ minutes (over 60% speed reduction).
    - Configured robust DOM and client-side validation assertions in [`tests/e2e/auth.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/auth.spec.js) and Livewire helpers for rock-solid deterministic execution in Laravel Sail.
14. **Playwright Interactive E2E Front-End Test Suite Expansion & Master README Consolidation**:
    - Built an automated 8-spec end-to-end testing suite (56 multi-viewport tests across Desktop Chromium and Google Pixel 10 Pro touch emulation).
    - Validated full coverage across Authentication, Catch Directory Generic Data Table (search, sorting, density, column picker), Tacklebox (KPIs, categories, lures, telemetry), Boat Quick Catch Logger & Leaflet Lake Explorer, Species Dossier, and Autocomplete Lure Selector.
    - Merged and consolidated `readme.md` and `README.md` into a unified master documentation guide detailing all 10 core subsystems, offline boat Wi-Fi SSL architectures, and test execution procedures.
15. **Searchable Autocomplete Lure & Tackle Selector (`@livewire('ui.lure-selector')`)**:
    - Built a reactive Livewire 3 autocomplete component with real-time debounced query filtering, category pill headers, 2-tier grouped tackle lists, manufacturer badges, technical specs, and verified catch stats.
    - Replaced static HTML select dropdowns in Standard Catch Logger (`/record/create`), Edit Catch (`/record/{id}/edit`), and boat Quick Catch (`/record/quick`).
    - Covered with PHPUnit Feature tests (`tests/Feature/LureSelectorLivewireTest.php`) and Playwright E2E browser tests (`tests/e2e/lure-selector.spec.js`).
16. **Blade Lucide Icon Native Migration (`mallardduck/blade-lucide-icons`)**:
    - Replaced all runtime client-side JavaScript icon injection (`<i data-lucide="...">` + `createIcons()`) across 72 Blade templates with server-rendered `<x-lucide-...>` and `<x-dynamic-component :component="'lucide-' . $icon" />` tags.
    - Stripped client-side JS bundle overhead, removed Livewire DOM morph and SPA navigation re-scan event listeners, eliminating icon flickering and layout shifts.
17. **Complete 18-Species Fish Avatar Library & Prompt Architecture**:
    - Generated high-resolution vector artwork with cel-shading and unified slate-blue badge styling for all 18 freshwater species in [`public/images/fish/avatars/`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/).
    - Documented master style guide and prompts in [`public/images/fish/avatars/PROMPTS.md`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/PROMPTS.md).
    - Removed legacy `.svg` files and integrated `<x-fishAvatar>` across Species Index, Catches Directory, User & Angler Profile Personal Bests, and Admin Portal.
18. **Species Dossier Direct Catch Directory Banner**:
    - Replaced redundant recent catch cards on [`/fish/{id}`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php) with an interactive **Catches Logbook Directory** banner link pre-filtered to the species.
19. **Admin User-Angler Linking & Account Management**:
    - Added `admin_user_actions` column to `GenericDataTable` allowing administrators to pair registered users to Angler profiles, toggle admin roles, and manage accounts.
20. **App-Wide Generic Livewire 3 Data Table (`@livewire('components.generic-data-table')`)**:
    - Created unified data table with dynamic columns, instant search, multi-column Shift-click sorting, relation counts, soft-delete views, and custom query scopes across Lakes, Anglers, Catches, Expeditions, Trash, and Admin Users.
21. **Controller Refactoring into Action Classes & Domain Services**:
    - Extracted business logic from fat controllers into single-responsibility Action classes and Domain Services ([`CreateLureVariantAction`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php), [`ExpeditionAnalyticsService`](file:///home/gmroczek/git/fishing/app/Services/ExpeditionAnalyticsService.php)).
22. **Expedition & Species Dossier Query Optimization**:
    - Eliminated N+1 queries across trip dashboards, lake distribution tables, and monthly telemetry charts.
23. **Offline Catch Queue Sync Indicator & Background Resync Worker (`@livewire('ui.offline-sync-indicator')`)**:
    - Built a reactive Livewire 3 top navigation bar indicator badge monitoring IndexedDB offline catches logged while on the water.
    - Integrated background resync worker with exponential backoff and client-side offline catch intercept in Global Quick Catch modal and Quick Logger.
    - Hardened PWA offline dropdown rehydration and Service Worker asset pre-caching with `Promise.allSettled()`.
    - Covered with feature tests in [`OfflineSyncIndicatorTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/Livewire/OfflineSyncIndicatorTest.php) and [`OfflineSyncApiTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/OfflineSyncApiTest.php).
24. **Angler Name Normalization & Filter Dropdown Resolution (`/map/explorer`)**:
    - Standardized Angler name rendering across filter dropdowns, omnibox search, and map explorer controls.
    - Fixed attribute case sensitivity in [`explorer.blade.php`](file:///home/gmroczek/git/fishing/resources/views/map/explorer.blade.php) using `$angler->full_name` instead of raw UUID fallback (`Angler #...`).
    - Added defensive property accessors on [`Angler.php`](file:///home/gmroczek/git/fishing/app/Models/Angler.php) (`firstname`, `lastname`, `middlename`, `name`, `full_name`, `formal_name`).
    - Verified with unit and feature tests in [`AnglerTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/AnglerTest.php) and [`MapExplorerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/MapExplorerTest.php).
25. **Solunar & Moon Phase Feeding Forecast Matrix (`@livewire('widgets.solunar-forecast')`)**:
    - Engineered offline-first pure mathematical astronomical calculation engine in [`SolunarService.php`](file:///home/gmroczek/git/fishing/app/Services/SolunarService.php) utilizing Julian Date celestial algorithms, synodic lunar cycles (29.53058867 days), solar declination, and observer GPS coordinates to compute peak 2-hour Major feeding periods (moon overhead/underfoot), 1-hour Minor feeding periods (moonrise/moonset), exact sunrise/sunset, 1-5 star day ratings, and 24-hour hour-by-hour feeding activity levels.
    - Created reactive Livewire 3 component [`SolunarForecast.php`](file:///home/gmroczek/git/fishing/app/Livewire/Widgets/SolunarForecast.php) and Tailwind Blade view [`solunar-forecast.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/widgets/solunar-forecast.blade.php) featuring date stepping controls (prev/today/next), a 4-metric overview ribbon, and an interactive 24-hour visual activity bar with glowing Major (gold) and Minor (teal) peak window indicators.
    - Integrated seamlessly into the Lake Dossier view ([`lake/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/show.blade.php)) with collapsible accordion card layout and zero external network dependencies.
    - Verified with 3 unit tests ([`SolunarServiceTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/SolunarServiceTest.php)) and 5 feature tests ([`SolunarForecastTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/Livewire/SolunarForecastTest.php)) (229 total passing tests across the entire application suite).
