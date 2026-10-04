# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application, continuously audited and prioritized by our specialized agent team (`laravel-architect`, `livewire-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, `ui-ux-auditor`).

---

## 🎯 Active Priority Roadmap (Ranked by Impact & Value)

### 🏆 Priority 1 (P1): Navigation Ergonomics, UI Polish & Authentication

#### 1. `/fish` Directory View Unification & Dual Pagination Pruning
- **Agents**: `livewire-architect`, `ui-ux-auditor`
- **Impact**: **Medium** (UI Consistency & Bugfix)
- **Description**:
  - Fix artifact in `resources/views/fish/index.blade.php` where switching to Table View renders the `GenericDataTable` component while leaving the legacy Blade pagination (`$fishes->links()`) and HTTP GET family filter pills rendered simultaneously.
  - Standardize `/fish` to use `GenericDataTable` as the primary directory view or encapsulate grid/table view modes cleanly within a reactive Livewire component with unified pagination and family filtering.

#### 2. Self-Service Password Reset Flow & Admin User Password Management Tooling
- **Agents**: `laravel-architect`, `ui-ux-auditor`
- **Impact**: **Medium-High** (User Account Administration & Support)
- **Description**:
  - Register standard authentication password reset routes (`password.request`, `password.email`, `password.reset`, `password.update`) in `routes/web.php` backed by `ForgotPasswordController` and `ResetPasswordController`.
  - Add an Admin "Reset User Password" modal action on `/admin/users` allowing administrators to generate temporary passwords or send one-time reset links immediately for locked-out members.

---

### 🚀 Priority 2 (P2): Tactical Intelligence, UI/UX & Spatial Visualizations

#### 1. Map Explorer Context Menu: 1-Click "Create Lake" & "Quick Catch" Pinning
- **Agents**: `ui-ux-auditor`, `livewire-architect`
- **Impact**: **High** (Field Mapping & Catch Logging Speed)
- **Description**:
  - Add Leaflet `contextmenu` (right-click on desktop, long-press on mobile) event handling on the Map Explorer (`/map/explorer`).
  - Display a sleek tactical context popover at the clicked `(lat, lng)` coordinates with two quick actions:
    1. **"Add Lake Here"**: Opens lake creation pre-populated with clicked coordinates.
    2. **"Log Catch Here"**: Dispatches `open-quick-catch` with GPS coordinates pre-filled.

#### 2. Fullscreen Catch & Expedition Media Lightbox / Multi-Photo Carousel (`<x-media-lightbox />`)
- **Agents**: `ui-ux-auditor`, `livewire-architect`
- **Impact**: **Medium-High** (Photo Gallery UX & Boat Sharing)
- **Description**:
  - Build a global, responsive Alpine.js media lightbox component for viewing catch photos, expedition gallery snapshots, and journal scans in high resolution.
  - Support multi-photo sets with previous/next carousel controls, thumbnail navigation strip, keyboard shortcuts (`←`/`→`/`Esc`), touch swipe gestures, and photo metadata telemetry overlay (Angler, Species, Length/Weight, Lake, Date).

#### 3. Lake & Expedition Dossier Spatial Map Rebalancing (2-Column Taller Aspect Layout)
- **Agents**: `ui-ux-auditor`, `seasoned-angler-advisor`
- **Impact**: **Medium** (Dossier Layout & Cartographic Usability)
- **Description**:
  - Restructure map presentation on `/lake/{id}` and `/expedition/{id}` from narrow, full-width strips (420px) to a responsive 2-column grid layout (e.g. 2/3 map canvas + 1/3 companion telemetry/trophy card stack).
  - Increase map height to 520px–600px for expanded bathymetric and track visibility without excessive page scrolling.

#### 4. Lake Directory Species Biodiversity Columns, Filtering & Map Popovers
- **Agents**: `laravel-architect`, `ui-ux-auditor`
- **Impact**: **Medium** (Fishery Intelligence & Search)
- **Description**:
  - Add a `Species Present` aggregate column to the Lake Directory (`/lake`) displaying species avatar chips for verified catches logged in each waterbody.
  - Integrate a "Filter by Species" dropdown in `GenericDataTable` for lakes.
  - Enhance Map Explorer push-pin popovers with species avatar badges to quickly identify target fisheries on the water.

#### 5. Interactive JavaScript Telemetry Visualizations (Chart.js & Livewire/Alpine)
- **Agents**: `ui-ux-auditor`, `livewire-architect`, `seasoned-angler-advisor`
- **Impact**: **High** (Tactical Angling Analytics & Data Science)
- **Description**: Replace static SVG/CSS charts with interactive, hardware-accelerated Chart.js canvases wrapped in reactive Alpine.js components:
  * **Species Ratio Interactive Donut**: Hover slice details, species percentage breakdown, and animated transitions.
  * **Daily / Monthly Catch Cadence Bars**: Animated multi-series bar charts with trip pace metrics.
  * **Barometric Pressure vs Catch Velocity**: 24-hour dual Y-axis spline/bar chart correlating pressure drops with strike frequency.
  * **Water Temperature × Lure Category Matrix**: Strike zone heatmap identifying high-probability tackle per water temp band.
  * **Angler Multi-Skill Radar**: 5-axis crew comparison chart (Lunker Max, Volume, C&R %, Species Diversity, Active Waters).

#### 6. Outdoor Boat Usability & Responsive Table-to-Card Stack
- **Agents**: `ui-ux-auditor`, `livewire-architect`, `seasoned-angler-advisor`
- **Impact**: **Medium-High** (Mobile & Boat Cockpit Usability)
- **Description**: Optimize user experience for open-water boat navigation under direct sunlight and high glare:
  * **Mobile Card Stack**: Automatically collapse dense data tables into vertical card feeds on viewports `< 640px` to eliminate horizontal panning on mobile devices.
  * **44px Tap Target Enforcement**: Audit and expand touch boundaries on all mobile filter pills, table sorting chevrons, and pagination buttons.
  * **WCAG AAA Sunlight Contrast**: Elevate secondary text contrast ratios to $\ge 7:1$ to prevent washout on polarized mobile screens.

#### 7. Fish Species Illustration Asset Pipeline: Alpha Transparency & WebP/PNG Conversion
- **Agents**: `ui-ux-auditor`, `laravel-architect`
- **Impact**: **Medium** (Visual Polish & Dark Mode Aesthetic)
- **Description**: Upgrade the fish species and lure asset library with true alpha transparency:
  * **Background Removal**: Strip solid white JPEG backgrounds from all 18+ species side-profile illustrations (`public/images/fish/`) and avatar thumbnails (`public/images/fish/avatars/`).
  * **Modern Alpha Format**: Convert assets to transparent `.webp` / `.png` with lossless compression, updated in `FishBreed` model fallback resolution.
  * **Subtle Dark Mode Glow / Drop Shadow**: Apply subtle ambient illumination filters so dark-scaled species (e.g., Largemouth Bass, Walleye) remain distinctly visible against dark slate backgrounds.

#### 8. Humminbird Helix AutoChart Live & Custom Sonar Bathymetry Ingestion
- **Agents**: `seasoned-angler-advisor`, `laravel-architect`, `ui-ux-auditor`
- **Impact**: **High** (Custom Fishery Intelligence & Depth Mapping)
- **Description**: Build an ingestion pipeline for personal Humminbird Helix AutoChart Live sonar data (`acdata` folder / AutoChart Zero Line SD card, AutoChart PC exports, CSV/XYZ soundings, and GeoJSON contour vectors):
  * **File Ingestion & Parsing**: Support uploading AutoChart exports (XYZ soundings `[Lat, Lng, Depth, Hardness]`, `.acd` track logs, and shapefile/GeoJSON contour layers).
  * **Lake Association & Storage**: Link imported contour maps and depth points directly to specific `Lake` records in the database.
  * **Interactive Map Layer**: Render private, high-definition (1-foot / 3-foot) bathymetric contours on both the **Map Explorer** (`/map/explorer`) and **Lake Dossier** (`/lake/{id}`) with custom color ramping, depth labels in feet, and bottom hardness / weedline overlays.
  * **Offline Support**: Integrate custom lake contours into the Offline Region Downloader (`/map/offline`) for 100% offline navigation out on the water.

#### 9. Consolidate Lake Show Telemetry Queries (`LakeController@show`)
- **Agents**: `query-profiler-optimizer`, `laravel-architect`
- **Impact**: **Medium** (Database Query Optimization)
- **Description**: Consolidate the 5 separate count and aggregation queries in `LakeController@show` (total catches, longest catch, heaviest catch, unique visits, unique anglers) into a consolidated single-pass aggregation query.

---

### 🛠️ Priority 3 (P3): Gamification, AI Intelligence, Synology NAS & DevOps

#### 1. Angler Achievement Badges & Gamification Engine (1980s/90s Boy Scout Merit Badge Style)
- **Agents**: `ui-ux-auditor`, `seasoned-angler-advisor`, `laravel-architect`
- **Impact**: **High** (Angler Engagement, Youth Appeal & Trip Heritage)
- **Description**:
  - Build an achievement badge and points gamification engine rendered on Angler Profiles (`/angler/{id}`).
  - **Visual Aesthetic**: Inspired by 1980s/1990s Boy Scout Merit Badges with colored-pencil texture, circular embroidered borders, and progressive color saturation as point tiers increase.
  - **Badge Metadata**: Each earned badge records `date_earned`, points awarded, and a direct link to the triggering catch or expedition record.
  - **Badge Catalog**:
    * **Quantity Milestones**: 1st, 5th, 10th, 25th, 50th, 100th, 150th catches (General volume + species-specific milestones with higher point weights).
    * **Species Diversity (DEI / Variety)**: 3, 6, 9+ freshwater species caught.
    * **Angler Cadence**: Distinct days logging catches.
    * **Exploration**: Number of distinct lakes fished with verified catches.
    * **Consecutive Seasons Streak**: Consecutive calendar years active in the logbook (including attendance on expeditions with zero personal catches).
    * **High Seas**: Catching fish on Lake Superior / big open water.
    * **Weathered**: Catches across varied weather extremes (rapid pressure drop, rain, fog, high wind).
    * **Nomad / Traveler**: Catches spanning multiple geographic zones / FMZs.
    * **Total Fish Yardage / Mileage**: Summing cumulative inches into real-world milestones (*"Around the Cabin"* = 100ft perimeter, *"Touchdown"* = 100 yards, *"Going the Mile"* = 1 mile).
    * **New Water Pioneer**: Angler present on an expedition when a new lake was first discovered/mapped.
    * **Maverick & Goose**: Rotating title for #1 and #2 on Crew Leaderboards, featuring a historical holder timeline.
    * **Skunked**: Humorous kid-friendly badge (awarded after having an expedition with 0 catches following at least 2 successful catch trips).
    * **Conservationist**: Catch-and-Release milestones (1, 5, 10, 25, 50, 100+ releases).
  - **Architecture**: Domain event listener on `CatchLoggedEvent` processing rules via `BadgeEvaluatorService`, stored in `badges` and `angler_badges` pivot tables.

#### 2. Handwritten Expedition Journal AI OCR & Structured Transcription Pipeline
- **Agents**: `laravel-architect`, `ui-ux-auditor`
- **Impact**: **Medium-High** (Historical Logbook Digitization & Trip Archive)
- **Description**:
  - Create a structured digitization pipeline for physical Canadian trip logbook journals.
  - Support photographing journal pages and transcribing them via AI vision/OCR into structured `JournalEntry` records (or templated `Post` records) attached to `Expedition` models.
  - Store original photo references (`photo_id`), transcription text, transcription dates, and structured metadata (weather observations, boat pairings, camp quotes, daily prompt questions).

#### 3. Low-Bandwidth Chunked NAS Outbox Push & Scheduled Sync Health Webhook
- **Agents**: `nas-sync-architect`
- **Impact**: **Medium** (Remote Data Integrity)
- **Description**: Add chunked outbox streaming (50 records per payload) for low-bandwidth cellular / boat satellite connections, and add a scheduled health monitor triggering notifications if NAS sync is unreachable or failing for >24 hours.

#### 4. Automated Backup Verification & Restore Drill Command (`backup:verify`)
- **Agents**: `laravel-architect`
- **Impact**: **Low-Medium** (Disaster Recovery Verification)
- **Description**: Create an `artisan backup:verify` command that unzips recent Spatie backup archives in a temporary staging environment to verify SQL dump validity and image asset completeness.

---

### ⏳ Deferred / Postponed (Future Consideration)

#### 1. FMZ Fishing Regulations Warning & Slot Limit Compliance Alerts *(Deferred from P2.1)*
- **Agents**: `seasoned-angler-advisor`, `laravel-architect`
- **Impact**: **High** (Legal & Conservation Compliance)
- **Description**: Connect existing `FishingRule` and `FishingZone` relations to the Quick Catch Modal and Lake Dossier to provide real-time slot limit compliance warnings (e.g., Ontario FMZ 2 / FMZ 4 Walleye slot: *None between 16.1" and 22.0"*) and season open/close indicators during catch logging.

#### 2. Trophy Catch Brag Card Generator & Chartplotter GPX/CSV Export *(Deferred from P2.2)*
- **Agents**: `ui-ux-auditor`, `laravel-architect`
- **Impact**: **Medium-High** (Social Sharing & Marine Navigation Integration)
- **Description**: Generate a downloadable high-resolution branded brag card (catch length/weight, lake, lure, solunar rating, photo) for social sharing, and implement 1-click GPX/CSV waypoint export formatted specifically for Garmin, Humminbird, and Lowrance chartplotters.

---

## 📦 Laravel & Livewire Ecosystem Package Evaluation

| Package | Category | Primary Benefit | Recommended Priority |
| :--- | :--- | :--- | :--- |
| [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup) | DB Safety / DevOps | Automated, scheduled timestamped database and media directory backups to `storage/app/backups/` & NAS. | **Completed** |
| [`blade-ui-kit/blade-lucide-icons`](https://github.com/blade-ui-kit/blade-lucide-icons) | Blade / UI | Server-rendered Lucide icons (`<x-lucide-fish />`) eliminating JS DOM injection delays & SVG duplication. | **Completed** |
| [`larastan/larastan`](https://github.com/larastan/larastan) *(dev)* | Static Analysis | Strict level typing, Eloquent relationship validation, and null safety checks across all 13 models & services. | **Completed** |
| [`matanyadaev/laravel-eloquent-spatial`](https://github.com/matanyadaev/laravel-eloquent-spatial) | GIS / Mapping | Native MySQL 8 spatial geometry (`Point`, `Polygon`) with spherical distance scopes (`whereDistanceSphere`) for sub-millisecond waypoint & nearby radius lookups. | **Completed** |
| [`livewire/volt`](https://github.com/livewire/volt) | Livewire DX | Single-file reactive components for lightweight boat widgets (Theme Switcher, Solunar date controls). | **P2 (Active)** |
| [`chart.js`](https://www.chartjs.org/) | Frontend / Charts | Hardware-accelerated, lightweight HTML5 canvas telemetry charts (Barometer vs Strike, Species Donuts, Radar). | **P2 (Active)** |
| [`laravel/boost`](https://github.com/laravel/boost) *(dev)* | AI Tooling / MCP | Embedded MCP server and AI guidelines/skills for Antigravity-assisted development. | **Completed** |

---

## 🏆 Completed Milestones (Merged into `master`)

1. **Admin Overview Notifications & Activity Alerts Hub (P1.1)**:
   - Upgraded `/admin` notifications into a polymorphic, multi-source **Notifications & Activity Alerts Hub** displaying both user registration alerts and angling trophy milestones with live badge counters.
   - Built client-side Alpine.js filtering tabs (`All`, `Milestones`, `Registrations`), contextual icons/badges (All-Time Record, Lake Record, Personal Best, User Registration), and direct deep-action links (`View Catch →`, `Pair Profile →`).
   - Implemented individual single notification dismissal (`POST /admin/notifications/{id}/mark-read`) alongside bulk dismissal (`POST /admin/notifications/mark-all-read`).
   - Verified with dedicated feature tests in [`AdminNotificationsHubTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AdminNotificationsHubTest.php).
2. **Logbook-Wide All-Time & Lake Record Milestone Detection (P1.2)**:
   - Upgraded [`Record::checkTrophyMilestone()`](file:///home/gmroczek/git/fishing/app/Models/Record.php) with a 4-tier milestone detection hierarchy:
     1. `all_time_record`: All-Time Logbook Record across all anglers and waterbodies.
     2. `lake_record`: Waterbody-specific record for that fish breed.
     3. `species_pb`: Individual Angler Personal Best.
     4. `first_species_catch`: First logged specimen of a species.
   - Enhanced [`TrophyCatchLogged.php`](file:///home/gmroczek/git/fishing/app/Notifications/TrophyCatchLogged.php) to render tailored notification copy for catching anglers vs system administrators.
   - Extended [`CheckTrophyMilestoneListener.php`](file:///home/gmroczek/git/fishing/app/Listeners/CheckTrophyMilestoneListener.php) to dispatch trophy milestone notifications to catching anglers and all active Admin users.
   - Fully covered with feature tests in [`TrophyNotificationTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/TrophyNotificationTest.php).
3. **Application Timezone Parity & Quick Catch Date Drift Fix (`America/Detroit`) (P1.3)**:
   - Configured `config('app.timezone')` and `APP_TIMEZONE` in `.env` and `.env.example` to `America/Detroit` (Eastern Time).
   - Eliminated late evening catch date drift where catches logged after 8:00 PM EDT in Michigan/Ontario rolled over into UTC tomorrow.
   - Standardized `QuickCatchModal.php` default mount date to `now()->format('Y-m-d')`.
   - Verified with unit tests in [`TimezoneTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/TimezoneTest.php).
4. **Full Codebase Exception Catching & Logging Audit**:
   - Audited and hardened exception handling across `AdminBackupController`, `AdminInviteController`, `AdminQuickAddController`, `AdminController`, `LivewireConsoleComponent`, `NasSyncApiPushController`, `NasSyncApiPullController`, `NasSyncApiMediaVerifyController`, `NasSyncApiMediaDownloadController`, `NasSyncApiMediaChunkController`, and `OfflineCatchSyncApiController`.
   - Added structured contextual logging (`Log::error()`, `Log::warning()`) and `report($e)` integration without breaking end-user response flows.
5. **Multi-Day Solunar Forecast & Astronomical Trip Planner (P1.1 prior)**:
   - Extended [`SolunarService.php`](file:///home/gmroczek/git/fishing/app/Services/SolunarService.php) with multi-day predictive window engine (`getMultiDayForecast`) computing 1–5 star ratings, peak trip feeding days, moon illumination/phases, and major/minor windows for 1 to 14 days.
   - Upgraded `@livewire('widgets.solunar-forecast')` with a compact, single-line horizontal trip outlook strip and reactive day switching across Expedition Dossiers (`/expedition/{id}`) and Lake Dossiers (`/lake/{id}`).
   - Covered with dedicated unit & feature tests in [`SolunarMultiDayForecastTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/SolunarMultiDayForecastTest.php) and [`ExpeditionSolunarPlannerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/ExpeditionSolunarPlannerTest.php).
2. **Barometric Pressure Velocity & Tactical Weather Trigger Badges (P1.2)**:
   - Extended [`WeatherTelemetryService.php`](file:///home/gmroczek/git/fishing/app/Services/WeatherTelemetryService.php) and [`LakeDailyWeather.php`](file:///home/gmroczek/git/fishing/app/Models/LakeDailyWeather.php) with 3-hour pressure velocity calculation ($\Delta P = P_{\text{hour}} - P_{\text{hour}-3}$) and 5-category tactical feeding classifications (Rapid Drop, Falling, Stable, Rising, Rapid Rise).
   - Created `<x-tacticalPressureBadge />` and upgraded `<x-barometerTrend />` with outdoor-tested badges, target depths, and lure/presentation recommendations across Catch Records (`/record/{id}`), Lake Dossiers (`/lake/{id}`), and Expedition recaps.
   - Tested with dedicated unit tests in [`BarometricPressureVelocityTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/BarometricPressureVelocityTest.php).
3. **AI Governance & Durable Codebase Conventions Bootstrap (`.ai/rules/`)**:
   - Established permanent, path-scoped rule registry in [`.ai/rules/index.md`](file:///home/gmroczek/git/fishing/.ai/rules/index.md) and area rule files for Models, Actions & Services, Controllers & Form Requests, Livewire 3 & Frontend, Testing with Sail, and remote Synology NAS Synchronization.
   - Fully aligned with Antigravity's on-demand skills architecture in `.agents/skills/` and global project safety rules in `.agents/AGENTS.md`.
4. **Native MySQL 8 Spatial Engine & Eloquent Geometry Integration (`matanyadaev/laravel-eloquent-spatial`)**:
   - Integrated `matanyadaev/laravel-eloquent-spatial:^4.8` with native MySQL 8 `POINT` geometries (SRID 4326 WGS 84) on `lakes` and `records` tables.
   - Added `HasSpatial` trait and `'location' => Point::class` casting across [`Lake.php`](file:///home/gmroczek/git/fishing/app/Models/Lake.php) and [`Record.php`](file:///home/gmroczek/git/fishing/app/Models/Record.php).
   - Wired bidirectional automatic lifecycle synchronization between traditional `latitude`/`longitude` floats and spatial `Point` objects for 100% backward compatibility with existing web forms, APIs, and two-way Synology NAS synchronization.
   - Refactored `Lake::nearby()` to execute native spherical distance queries (`whereDistanceSphere`, `withDistanceSphere`, `orderByDistanceSphere`), eliminating legacy CPU-bound Haversine trigonometry.
   - Verified with dedicated feature tests ([`SpatialModelTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/SpatialModelTest.php) and [`OfflineMapTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/OfflineMapTest.php)).
5. **Personal Best & Brag Board Trophy Cards Grayscale Watermark Graphics**:
   - Engineered scalable, vector Blade components ([`watermarkTapeMeasure.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkTapeMeasure.blade.php), [`watermarkDialScale.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkDialScale.blade.php), [`watermarkTopRod.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkTopRod.blade.php), [`watermarkLure.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkLure.blade.php), and [`watermarkPouringCan.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkPouringCan.blade.php)).
   - Features diagonal tape ribbons, vintage platform scale dials, deep-flex rod blanks with fly reels, tacklebox lure category watermarks (crankbait, spinnerbait, spoon, jig), and a clean minimalist pouring beer can homage ("BLUE") for empty bait states.
   - Seamlessly integrated across Personal Best cards on Lake Show, Expedition Show, Angler Profile, and User Profile views.
   - Covered with full Blade component test assertions in [`BladeComponentsTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/BladeComponentsTest.php).
6. **AI Development Infrastructure & Agent Skills Integration (`laravel/boost`)**:
   - Installed `laravel/boost:^2.8` and `laravel/mcp` as development dependencies within Laravel Sail.
   - Configured [`boost.json`](file:///home/gmroczek/git/fishing/boost.json) and [`AGENTS.md`](file:///home/gmroczek/git/fishing/AGENTS.md) scoped specifically for **Antigravity**.
   - Integrated and synced upstream standard agent skills into [`.agents/skills/`](file:///home/gmroczek/git/fishing/.agents/skills) (`infer-conventions`, `laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`, `pulse-development`, `deploying-to-cloud`).
   - Wired automated post-update synchronization hook (`"@php artisan boost:update --ansi"`) into [`composer.json`](file:///home/gmroczek/git/fishing/composer.json).
7. **NAS Sync Multi-Chunk Media Streaming & SHA-256 Deduplication (`MediaSyncManager`)**:
   - Engineered dedicated [`MediaSyncManager`](file:///home/gmroczek/git/fishing/app/Services/MediaSyncManager.php) service for high-performance two-tier synchronization between local instances and the Synology NAS server.
   - Replaced bloated in-memory base64 encoding with 1 MB binary chunk slicing (`/api/v1/sync/media/chunk`) and streaming downloads (`/api/v1/sync/media/download`), reducing peak PHP memory consumption by >75% and eliminating +33% base64 network overhead.
   - Integrated SHA-256 checksum verification and pre-upload hash comparison (`/api/v1/sync/media/verify`) for zero-overhead deduplication.
   - Maintained full backward compatibility with legacy `_base64` payloads from older sync clients.
   - Fully tested with comprehensive feature tests in [`NasMediaChunkSyncTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasMediaChunkSyncTest.php), [`NasSyncServiceTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncServiceTest.php), and [`NasSyncApiTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncApiTest.php).
8. **Species Tactical Angler Intelligence Hub & Trophy Records Hall of Fame (`/fish/{id}`)**:
   - Transformed species dossier (`/fish/{id}`) into an Angler Intelligence Hub with unified dark hero styling (`bg-slate-900 border-slate-800`) and a 4-column KPI telemetry metrics row (Total Logged, Record Length, Record Weight, C&R Conservation rate).
   - Added Ontario Master Angler Benchmark bar, All-Time Length & Weight Champion spotlight cards, and Top 5 All-Time specimens ranking strip.
   - Built 4-Quadrant Tactical Matrix: Productive Tackle & Lures (categories, top models with PB, colorways), Waterbody Hotspot Rankings, Seasonal & Weather Triggers (water temp ranges, monthly catch curves, sky conditions), and Species Master Angler & C&R Ethics.
   - Integrated Waterbody Directory Table and direct link to pre-filtered Catch Logbook Directory (`/record/directory?species={id}`).
   - Covered with PHPUnit Feature tests ([`FishBreedControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/FishBreedControllerTest.php)) and Playwright E2E browser tests ([`species-dossier.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/species-dossier.spec.js)).
9. **Interactive Tacklebox Category Trays & Color Variant Grid (`@livewire('tacklebox.lure-catalog')`)**:
   - Built full-featured Telemetry & Depth-Tier Workstation Livewire 3 component in [`app/Livewire/Tacklebox/LureCatalog.php`](file:///home/gmroczek/git/fishing/app/Livewire/Tacklebox/LureCatalog.php) and [`resources/views/livewire/tacklebox/lure-catalog.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/tacklebox/lure-catalog.blade.php).
   - Features real-time multi-dimensional search & filtering (debounced query, category pills, brand selector, depth-zone pills: Surface 0ft, Shallow 1–5ft, Mid 6–10ft, Deep 10–20ft, Deep 20ft+).
   - Expandable Category Drawer Trays and Lure Model Cards with technical specs (Length, Weight, Depth range), Catch Efficiency Bars (verified catches & PB badges), and interactive Colorway Variant Grid powered by an intelligent tactical colorway swatch resolver on [`Lure.php`](file:///home/gmroczek/git/fishing/app/Models/Lure.php) (handling Coppertreuse, Twilight, Mud Minnow, Craw, Perch, etc.).
   - Integrated 1-Click "+ Log Catch" buttons on every variant firing `open-quick-catch` with pre-filled `lure_id` into the Global Quick Catch Modal.
   - Built inline "+ Add Colorway Variant" modal with comma-separated batch color creation via [`CreateLureVariantAction.php`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php), with automated brand prefix normalization to prevent duplicate brand entries in the `name` column.
   - Covered with PHPUnit Unit & Feature tests ([`LureTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/LureTest.php), [`LureCatalogLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/LureCatalogLivewireTest.php)) and Playwright E2E browser tests ([`tacklebox.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/tacklebox.spec.js)).
10. **Global Quick Catch Slide-Over Drawer Modal (`@livewire('modals.quick-catch-modal')`)**:
    - Created reactive Livewire 3 slide-over modal drawer mounted globally in [`resources/views/layouts/app.blade.php`](file:///home/gmroczek/git/fishing/resources/views/layouts/app.blade.php) with dark frosted backdrop and Option C Telemetry v2 styling.
    - Connected 1-tap catch logging triggers across Desktop Sidebar, Mobile Sticky Header, Floating Navigation Bar (+), Map Explorer Lake Drawer, and Expedition Trip Dossier via `$dispatch('open-quick-catch', { lake_id, expedition_id, ... })` and global keyboard shortcut (`Alt + C` or `Q`).
    - Built with lazy-loaded dropdown options, device geolocation GPS acquisition, tacklebox lure selector integration, and instant trophy personal best celebration notifications.
    - Dispatches reactive `catch-saved`, `refresh-records`, and `refresh-map` events to parent pages upon logging.
    - Covered with PHPUnit tests ([`QuickCatchModalLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/QuickCatchModalLivewireTest.php)) and Playwright E2E browser tests ([`quick-catch-modal.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/quick-catch-modal.spec.js)).
11. **Static Analysis & Strict Typing Auditing (`larastan/larastan`)**:
    - Configured `larastan/larastan:^3.0` (PHPStan 2.x Level 5) in [`phpstan.neon`](file:///home/gmroczek/git/fishing/phpstan.neon) scanning `app/` and `routes/`.
    - Hardened all 13 Eloquent models with complete `@property` / `@property-read` docblocks and explicit relationship return types (`: BelongsTo`, `: HasMany`, `: HasManyThrough`, `: HasOne`, `: MorphMany`).
    - Hardened [`HasUuidAndSyncTracking`](file:///home/gmroczek/git/fishing/app/Traits/HasUuidAndSyncTracking.php) with `setAttribute()` to eliminate dynamic property mutations across all models.
    - Replaced all non-config `env()` calls with `config('services.nas.*')` for production config cache safety.
    - Annotated all JsonResource classes with `/** @mixin \Fishinglog\Models\<Model> */`.
    - Standardized controller docblock return types and eliminated 340+ typing bugs down to **0 static analysis errors**.
    - Integrated `"analyse": "vendor/bin/phpstan analyse --memory-limit=2G"` composer command and documented usage in `README.md` and `laravel-architect` skill.
12. **Catches Logbook `/record` Latency Optimization & Telemetry Caching**:
    - Profiled and eliminated 34 synchronous SQL queries on `/record` down to 0 queries on cached hits and 19 queries on cold misses (~98.5% execution speedup from ~1.2s to ~1.8ms).
    - Removed dead-weight 10-item pagination and deep `lake.dailyWeather` eager loading from [`RecordController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php).
    - Consolidated duplicate weather telemetry aggregation scans into a single SQL query and optimized macro target species shifts with single-query conditional sums in [`CatchTelemetryService.php`](file:///home/gmroczek/git/fishing/app/Services/CatchTelemetryService.php).
    - Wired automatic cache invalidation across `Record` model lifecycle events (`saved`, `deleted`, `restored`) in [`Record.php`](file:///home/gmroczek/git/fishing/app/Models/Record.php).
    - Added dedicated cache and invalidation feature tests in [`RecordSummaryDashboardTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/RecordSummaryDashboardTest.php) with 100% test pass rate.
13. **Automated Database & Media Backup Package (`spatie/laravel-backup`)**:
    - Integrated `spatie/laravel-backup:^9.0` configured for full MySQL dumps, uploaded media assets (`storage/app/public`), and multi-tier grandfather-father-son retention rules (7 days all, 30 days daily, 8 weeks weekly, 12 months monthly, 2 years yearly, 5 GB storage ceiling).
    - Configured production-only automated Console schedules in [`routes/console.php`](file:///home/gmroczek/git/fishing/routes/console.php) (`backup:clean` at 01:00, `backup:run --only-db` at 02:00, full `backup:run` on Sundays at 03:00).
    - Hooked automated pre-migration safety snapshots into [`synology-nas-deploy/update_nas.sh`](file:///home/gmroczek/git/fishing/synology-nas-deploy/update_nas.sh).
    - Documented all CLI backup commands and disaster recovery restoration procedures in [`README.md`](file:///home/gmroczek/git/fishing/README.md).
14. **Codebase Usage Cleanup, Dead Code Elimination & Action Class Wiring**:
    - Pruned dead/unrendered legacy Blade components (`stat-card.blade.php`, `components/form/input.blade.php`, `resources/views/vendor/log-viewer/`).
    - Injected and wired [`CreateCatchRecordAction`](file:///home/gmroczek/git/fishing/app/Actions/Records/CreateCatchRecordAction.php) and [`ProcessPhotoUploadAction`](file:///home/gmroczek/git/fishing/app/Actions/Media/ProcessPhotoUploadAction.php) into [`RecordController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php), [`RecordApiController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/RecordApiController.php), and [`PhotoController`](file:///home/gmroczek/git/fishing/app/Http/Controllers/PhotoController.php).
    - Removed empty/unimplemented stub routes and methods in [`routes/web.php`](file:///home/gmroczek/git/fishing/routes/web.php) (`CrewController`, `PostController`, `FishFamilyController`, `FishBreedController`).
    - Cleaned up obsolete Laravel Mix artifacts (`public/mix-manifest.json`, `public/js/app.js`, `public/css/app.css`).
    - Pruned redundant packages (`spatie/laravel-html`, `laravel/helpers`, `lucide`, `alpinejs`) and aliases.
15. **Automated NAS Deployment Synchronization (`synology-nas-deploy/update_nas.sh`)**:
    - Added automated `composer install --no-dev --optimize-autoloader --no-interaction` execution inside the `fishinglog_app` Docker container on every GitHub Actions deployment, eliminating missing package 500 errors on the Synology NAS.
16. **Test Suite Execution Performance Optimization (Playwright Session Caching & Fast Target)**:
    - Implemented `globalSetup` in [`tests/e2e/global-setup.js`](file:///home/gmroczek/git/fishing/tests/e2e/global-setup.js) generating pre-authenticated storage state (`playwright/.auth/user.json`), eliminating redundant per-test `/login` cycles across all 56 E2E tests.
    - Added dedicated `"test:e2e:fast"` npm target in [`package.json`](file:///home/gmroczek/git/fishing/package.json) executing Desktop Chromium suite in **1.4 minutes (84 seconds)** down from 3.5+ minutes (over 60% speed reduction).
    - Configured robust DOM and client-side validation assertions in [`tests/e2e/auth.spec.js`](file:///home/gmroczek/git/fishing/tests/e2e/auth.spec.js) and Livewire helpers for rock-solid deterministic execution in Laravel Sail.
17. **Playwright Interactive E2E Front-End Test Suite Expansion & Master README Consolidation**:
    - Built an automated 8-spec end-to-end testing suite (56 multi-viewport tests across Desktop Chromium and Google Pixel 10 Pro touch emulation).
    - Validated full coverage across Authentication, Catch Directory Generic Data Table (search, sorting, density, column picker), Tacklebox (KPIs, categories, lures, telemetry), Boat Quick Catch Logger & Leaflet Lake Explorer, Species Dossier, and Autocomplete Lure Selector.
    - Merged and consolidated `readme.md` and `README.md` into a unified master documentation guide detailing all 10 core subsystems, offline boat Wi-Fi SSL architectures, and test execution procedures.
18. **Searchable Autocomplete Lure & Tackle Selector (`@livewire('ui.lure-selector')`)**:
    - Built a reactive Livewire 3 autocomplete component with real-time debounced query filtering, category pill headers, 2-tier grouped tackle lists, manufacturer badges, technical specs, and verified catch stats.
    - Replaced static HTML select dropdowns in Standard Catch Logger (`/record/create`), Edit Catch (`/record/{id}/edit`), and boat Quick Catch (`/record/quick`).
    - Covered with PHPUnit Feature tests (`tests/Feature/LureSelectorLivewireTest.php`) and Playwright E2E browser tests (`tests/e2e/lure-selector.spec.js`).
19. **Blade Lucide Icon Native Migration (`mallardduck/blade-lucide-icons`)**:
    - Replaced all runtime client-side JavaScript icon injection (`<i data-lucide="...">` + `createIcons()`) across 72 Blade templates with server-rendered `<x-lucide-...>` and `<x-dynamic-component :component="'lucide-' . $icon" />` tags.
    - Stripped client-side JS bundle overhead, removed Livewire DOM morph and SPA navigation re-scan event listeners, eliminating icon flickering and layout shifts.
20. **Complete 18-Species Fish Avatar Library & Prompt Architecture**:
    - Generated high-resolution vector artwork with cel-shading and unified slate-blue badge styling for all 18 freshwater species in [`public/images/fish/avatars/`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/).
    - Documented master style guide and prompts in [`public/images/fish/avatars/PROMPTS.md`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/PROMPTS.md).
    - Removed legacy `.svg` files and integrated `<x-fishAvatar>` across Species Index, Catches Directory, User & Angler Profile Personal Bests, and Admin Portal.
21. **Species Dossier Direct Catch Directory Banner**:
    - Replaced redundant recent catch cards on [`/fish/{id}`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php) with an interactive **Catches Logbook Directory** banner link pre-filtered to the species.
22. **Admin User-Angler Linking & Account Management**:
    - Added `admin_user_actions` column to `GenericDataTable` allowing administrators to pair registered users to Angler profiles, toggle admin roles, and manage accounts.
23. **App-Wide Generic Livewire 3 Data Table (`@livewire('components.generic-data-table')`)**:
    - Created unified data table with dynamic columns, instant search, multi-column Shift-click sorting, relation counts, soft-delete views, and custom query scopes across Lakes, Anglers, Catches, Expeditions, Trash, and Admin Users.
24. **Controller Refactoring into Action Classes & Domain Services**:
    - Extracted business logic from fat controllers into single-responsibility Action classes and Domain Services ([`CreateLureVariantAction`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php), [`ExpeditionAnalyticsService`](file:///home/gmroczek/git/fishing/app/Services/ExpeditionAnalyticsService.php)).
25. **Expedition & Species Dossier Query Optimization**:
    - Eliminated N+1 queries across trip dashboards, lake distribution tables, and monthly telemetry charts.
26. **Offline Catch Queue Sync Indicator & Background Resync Worker (`@livewire('ui.offline-sync-indicator')`)**:
    - Built a reactive Livewire 3 top navigation bar indicator badge monitoring IndexedDB offline catches logged while on the water.
    - Integrated background resync worker with exponential backoff and client-side offline catch intercept in Global Quick Catch modal and Quick Logger.
    - Hardened PWA offline dropdown rehydration and Service Worker asset pre-caching with `Promise.allSettled()`.
    - Covered with feature tests in [`OfflineSyncIndicatorTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/Livewire/OfflineSyncIndicatorTest.php) and [`OfflineSyncApiTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/OfflineSyncApiTest.php).
27. **Angler Name Normalization & Filter Dropdown Resolution (`/map/explorer`)**:
    - Standardized Angler name rendering across filter dropdowns, omnibox search, and map explorer controls.
    - Fixed attribute case sensitivity in [`explorer.blade.php`](file:///home/gmroczek/git/fishing/resources/views/map/explorer.blade.php) using `$angler->full_name` instead of raw UUID fallback (`Angler #...`).
    - Added defensive property accessors on [`Angler.php`](file:///home/gmroczek/git/fishing/app/Models/Angler.php) (`firstname`, `lastname`, `middlename`, `name`, `full_name`, `formal_name`).
    - Verified with unit and feature tests in [`AnglerTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/AnglerTest.php) and [`MapExplorerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/MapExplorerTest.php).
28. **Solunar & Moon Phase Feeding Forecast Matrix (`@livewire('widgets.solunar-forecast')`)**:
    - Engineered offline-first pure mathematical astronomical calculation engine in [`SolunarService.php`](file:///home/gmroczek/git/fishing/app/Services/SolunarService.php) utilizing Julian Date celestial algorithms, synodic lunar cycles (29.53058867 days), solar declination, and observer GPS coordinates to compute peak 2-hour Major feeding periods (moon overhead/underfoot), 1-hour Minor feeding periods (moonrise/moonset), exact sunrise/sunset, 1-5 star day ratings, and 24-hour hour-by-hour feeding activity levels.
    - Created reactive Livewire 3 component [`SolunarForecast.php`](file:///home/gmroczek/git/fishing/app/Livewire/Widgets/SolunarForecast.php) and Tailwind Blade view [`solunar-forecast.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/widgets/solunar-forecast.blade.php) featuring date stepping controls (prev/today/next), a 4-metric overview ribbon, and an interactive 24-hour visual activity bar with glowing Major (gold) and Minor (teal) peak window indicators.
    - Integrated seamlessly into the Lake Dossier view ([`lake/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/show.blade.php)) with collapsible accordion card layout and zero external network dependencies.
    - Verified with 3 unit tests ([`SolunarServiceTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/SolunarServiceTest.php)) and 5 feature tests ([`SolunarForecastTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/Livewire/SolunarForecastTest.php)).
29. **Synology NAS Connectivity & Real-Time Sync Diagnostic Console (`/admin/sync`)**:
    - Built dedicated Remote Synchronization & Diagnostic Console view (`/admin/sync`) and reactive Livewire 3 console ([`SyncDiagnosticConsole.php`](file:///home/gmroczek/git/fishing/app/Livewire/Admin/SyncDiagnosticConsole.php) and [`sync-diagnostic-console.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/admin/sync-diagnostic-console.blade.php)).
    - Provides real-time network latency probes (`checkConnectivity`), peer TLS / SSL certificate inspection (issuer, validity, expiration countdown), Bearer token authorization checks, and 1MB chunked binary media diagnostics.
    - Implemented comprehensive 13-Model Outbox & Sync Health matrix tracking entity synchronization percentage progress, local modification times, and pending outbox queues with "Filter Pending Only" toggle.
    - Wired interactive admin controls for Live Ping probes, Incremental Sync execution, Full Baseline pull reconcile, and Mark All Synced state clearing.
    - Added direct navigation hooks from the Admin Dashboard header and Two-Way Sync Engine card.
    - Verified with 32 comprehensive tests across [`AdminNasSyncConsoleTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AdminNasSyncConsoleTest.php), [`NasSyncServiceTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncServiceTest.php), and [`NasSyncApiTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncApiTest.php).
30. **Database Composite Index Optimization & Query Profiling Audit (`records` table)**:
    - Added 6 targeted MySQL composite indexes to the `records` table in [`2026_09_12_000002_add_composite_indexes_to_records_table.php`](file:///home/gmroczek/git/fishing/database/migrations/2026_09_12_000002_add_composite_indexes_to_records_table.php):
      * `records_anglers_caught_idx` (`['anglers_id', 'caught']`): Accelerates angler profile timeline history and chronologically sorted catch tables.
      * `records_lakes_fish_breeds_idx` (`['lakes_id', 'fish_breeds_id']`): Accelerates lake biodiversity queries and waterbody species breakdowns.
      * `records_fish_breeds_length_idx` (`['fish_breeds_id', 'length']`): Accelerates species length trophy leaderboards and PB evaluations.
      * `records_fish_breeds_weight_idx` (`['fish_breeds_id', 'weight']`): Accelerates species heavyweight trophy leaderboards.
      * `records_lakes_caught_idx` (`['lakes_id', 'caught']`): Accelerates lake chronological catch logs and seasonal activity curves.
      * `records_anglers_fish_breeds_idx` (`['anglers_id', 'fish_breeds_id']`): Accelerates angler species life-list calculations and per-species personal bests.
    - Verified with dedicated feature tests in [`DatabaseCompositeIndexTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/DatabaseCompositeIndexTest.php).
31. **Test Suite Architecture Modernization & Strict Assertion Refactoring (`testing-best-practices`)**:
    - Modernized backend PHPUnit test suite to align with upstream Laravel Boost `testing-best-practices` standards.
    - Refactored unit tests in [`RecordTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/RecordTest.php), [`CrewTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/CrewTest.php), and [`LakeTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/LakeTest.php) from fragile reflection methods (`method_exists()`, `get_class()`) into behavioral Eloquent relationship integration tests.
    - Removed mutable `$this->angler` and `$this->lake` fixtures from `setUp()` in [`AnglerTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/AnglerTest.php) and [`LakeTest.php`](file:///home/gmroczek/git/fishing/tests/Unit/LakeTest.php) for fully isolated, self-contained test execution.
    - Upgraded assertions across unit and feature tests to strict `$this->assertSame()` and adopted database state assertions (`$this->assertDatabaseCount()`, `$this->assertDatabaseHas()`).
    - Pruned obsolete starter boilerplate stub (`ExampleTest.php`).
32. **Complete P2 Architecture Modernization, Caching & Performance Consolidation**:
    - **P2.1**: Extracted [`CatchTelemetryService.php`](file:///home/gmroczek/git/fishing/app/Services/CatchTelemetryService.php) consolidating `RecordController@index` multi-query telemetry (lifetime catches, totals, averages, leaderboards, top 5 anglers/lakes, macro species shifts, weather distributions, and weather matrix) into a reusable service class.
    - **P2.2**: Implemented reference data caching in [`LureSelector.php`](file:///home/gmroczek/git/fishing/app/Livewire/Ui/LureSelector.php) with `Cache::remember('lure_categories', 86400, ...)` and automatic invalidation via `Lure::booted()` lifecycle hooks and [`CreateLureVariantAction.php`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php).
    - **P2.3**: Added `scopeWithDailyWeather($query)` in [`Record.php`](file:///home/gmroczek/git/fishing/app/Models/Record.php) eliminating N+1 daily weather lookups in record collections.
    - **P2.4**: Centralized image optimization and resizing into [`ProcessPhotoUploadAction.php`](file:///home/gmroczek/git/fishing/app/Actions/Media/ProcessPhotoUploadAction.php) (`optimizeAndSave()`) and refactored [`AnglerController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Angler/AnglerController.php) and [`FishBreedController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/FishBreedController.php) to use it.
    - **P2.5**: Modernized all 14 Eloquent models (`Lake`, `Record`, `Angler`, `Lure`, `Photo`, `Expedition`, `FishingRule`, `FishingZone`, `LakeDailyWeather`, `FishBreed`, `FishFamily`, `Crew`, `Post`, `User`) to native Laravel 12 `protected function casts(): array`.
    - **P2.6**: Standardized [`PhotoController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/PhotoController.php) with dedicated [`StorePhotoRequest.php`](file:///home/gmroczek/git/fishing/app/Http/Requests/StorePhotoRequest.php).
    - Reached **257 passing tests (1049 assertions)** with **0 failures** and **0 PHPStan errors (level 5)**.
33. **Canada Garmin GPX Extraction & Interactive Map Explorer Layer**:
    - Converted and extracted 100% of Garmin GPX coordinates (`Canada.gpx`) into a structured GeoJSON FeatureCollection ([`canada-gps-layer.geojson`](file:///home/gmroczek/git/fishing/public/json/canada-gps-layer.geojson)).
    - Extracted 138 waypoints categorized with custom Garmin symbol metadata (`Fishing Area`, `Boat Ramp`, `Reef`, `Lodging`, `Trail Head`, `Water Source`, etc.) including elevations and timestamps.
    - Extracted 31 navigation/lake tracks and routes spanning Catfish Lake, access trails, and regional corridors (3,896 coordinate track points).
    - Integrated as an interactive overlay layer (`📍 Canada GPS (Waypoints & Tracks)`) in the Leaflet Map Explorer ([`explorer.blade.php`](file:///home/gmroczek/git/fishing/resources/views/map/explorer.blade.php)) with dynamic custom divIcons, rich waypoint detail popups, track polylines, and layer switcher controls.
    - Verified with feature tests in [`MapExplorerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/MapExplorerTest.php) with the full test suite passing at **270 tests (1173 assertions)**.
34. **Weather Telemetry Sync Open-Meteo Date Normalization Bugfix**:
    - Fixed Open-Meteo HTTP 400 Bad Request error caused by Carbon datetime string casting (`YYYY-MM-DD HH:MM:SS`) in [`WeatherTelemetryService.php`](file:///home/gmroczek/git/fishing/app/Services/WeatherTelemetryService.php).
    - Added strict date normalization (`YYYY-MM-DD`) and regex validation supporting both string dates and `\DateTimeInterface` objects.
    - Optimized [`FetchMissingWeatherCommand.php`](file:///home/gmroczek/git/fishing/app/Console/Commands/FetchMissingWeatherCommand.php) to deduplicate distinct `(lakes_id, DATE(caught))` queries at the SQL layer.
    - Verified all 878 mappable historical catches are now 100% weather-synced (0 pending fetchable), with dedicated feature tests in [`WeatherTelemetryTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/WeatherTelemetryTest.php).
35. **Synology NAS Sync Spatial Location Geometry Push Bugfix**:
    - Resolved HTTP 500 error during upstream push of `lakes` and `records` caused by raw array serialization of spatial `location` geometry (`['type' => 'Point', 'coordinates' => [...]]`).
    - Updated [`NasSyncService.php`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php) and [`SyncApiController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/SyncApiController.php) to omit raw array spatial payloads and let the model's `saving` lifecycle hook automatically build `Point($latitude, $longitude, 4326)`.
    - Executed live two-way sync against Synology NAS successfully syncing all pending outbox records (3 pushed, 5 pulled, 0 remaining pending).
    - Verified with all 37 NAS feature tests passing in [`AdminNasSyncConsoleTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AdminNasSyncConsoleTest.php), [`NasMediaChunkSyncTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasMediaChunkSyncTest.php), [`NasSyncApiTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncApiTest.php), and [`NasSyncServiceTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/NasSyncServiceTest.php).
36. **Soft-Deleted Entity Synchronization Tracking & Outbox Integration**:
    - Hooked `static::deleted` and `static::restored` in [`HasUuidAndSyncTracking.php`](file:///home/gmroczek/git/fishing/app/Traits/HasUuidAndSyncTracking.php) to automatically flip database `sync_status` to `pending_upstream` when models are soft-deleted or restored.
    - Updated `scopePendingUpstream` and `scopeSynced` to include `withTrashed()` so soft-deleted records are properly surfaced in Outbox counts and pushed to the NAS server.
    - Added `withTrashed()` resolution when finding existing models during push and pull ingest in [`NasSyncService.php`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php) and [`SyncApiController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/SyncApiController.php), preventing SQL duplicate key exceptions.
37. **GenericDataTable & Expeditions Date Formatting Polish**:
    - Enhanced `'type' => 'date'` column rendering in [`generic-data-table.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/components/generic-data-table.blade.php) using Carbon to format dates cleanly as `M j, Y` without unwanted time components (`00:00:00`), resolving issue on `/expeditions` and other date-bearing data tables.
    - Updated expedition details header date range in [`expedition/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/show.blade.php) to format `start` and `finish` as clean dates.
    - Updated feature assertions in [`GenericDataTableLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/GenericDataTableLivewireTest.php).
38. **Map Explorer, Lake Dossier & Offline Downloader Bathymetry Basemap Integration**:
    - Integrated OpenTopoMap Bathymetry & Relief basemap (`🌊 Bathymetry & Contours`) into [`explorer.blade.php`](file:///home/gmroczek/git/fishing/resources/views/map/explorer.blade.php) and [`lake/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/show.blade.php).
    - Removed synthetic vector overlay lines and OpenSeaMap nautical soundings from map views to maintain clean, focused data accuracy across backcountry Algoma waters.
    - Added Bathymetry & Contours Pack to the Offline Region Map Downloader ([`map/offline.blade.php`](file:///home/gmroczek/git/fishing/resources/views/map/offline.blade.php)) enabling seamless pre-caching for boat/offline usage.
    - Optimized [`ExplorerController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/ExplorerController.php) seasons query using fast distinct SQL year selection (`Record::whereNotNull('caught')->selectRaw('DISTINCT YEAR(caught) as yr')...`).
    - Verified with all 275 tests (1,200 assertions) passing cleanly.
39. **Livewire GenericDataTable Preferences Persistence (P2.2)**:
    - Added `#[Url(history: true)]` URL query syncing to `$perPage` in [`GenericDataTable.php`](file:///home/gmroczek/git/fishing/app/Livewire/Components/GenericDataTable.php) alongside dynamic pagination reset (`updatedPerPage()`) to prevent out-of-bounds pagination slicing.
    - Added interactive `perPage` dropdown selector (`15`, `25`, `50`, `100` items/page) to the pagination toolbar ribbon in [`generic-data-table.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/components/generic-data-table.blade.php).
    - Modernized [`data-table.js`](file:///home/gmroczek/git/fishing/resources/js/components/data-table.js) Alpine controller to guarantee immediate reactive column toggling (`visibleColumns` object immutability) and `localStorage` persistence across all table instances.
    - Added comprehensive Livewire tests in [`GenericDataTableLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/GenericDataTableLivewireTest.php).
40. **Form Modernization for Lake, Angler, and Expedition Entities (P2.3)**:
    - Upgraded [`angler/create.blade.php`](file:///home/gmroczek/git/fishing/resources/views/angler/create.blade.php) and [`angler/edit.blade.php`](file:///home/gmroczek/git/fishing/resources/views/angler/edit.blade.php) to incorporate `<x-photo-upload-input>` with client-side canvas compression for angler avatars.
    - Standardized design tokens, error alert banners, and action buttons across [`lake/create.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/create.blade.php), [`lake/edit.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/edit.blade.php), [`expedition/create.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/create.blade.php), and [`expedition/edit.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/edit.blade.php).
    - Expanded test coverage across [`LakeControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/LakeControllerTest.php), [`AnglerControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AnglerControllerTest.php), and [`ExpeditionControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/ExpeditionControllerTest.php).
    - Full test suite passing at **281 tests (1,224 assertions)**.
41. **Decoupled Domain Event Pipeline (`CatchLoggedEvent`) (P3.1)**:
    - Implemented [`CatchLoggedEvent`](file:///home/gmroczek/git/fishing/app/Events/CatchLoggedEvent.php) dispatched by [`CreateCatchRecordAction.php`](file:///home/gmroczek/git/fishing/app/Actions/Records/CreateCatchRecordAction.php) whenever a catch is registered across any system entry point.
    - Created dedicated listeners in `app/Listeners/`:
      * [`CheckTrophyMilestoneListener.php`](file:///home/gmroczek/git/fishing/app/Listeners/CheckTrophyMilestoneListener.php): Evaluates personal bests/trophy benchmarks and dispatches `TrophyCatchLogged` notification to the appropriate angler user account.
      * [`InvalidateTelemetryCacheListener.php`](file:///home/gmroczek/git/fishing/app/Listeners/InvalidateTelemetryCacheListener.php): Clears `angler_stats_overview` and flushes `CatchTelemetryService` leaderboards.
      * [`FetchCatchWeatherListener.php`](file:///home/gmroczek/git/fishing/app/Listeners/FetchCatchWeatherListener.php): Automatically triggers Open-Meteo daily weather synchronization for the catch's waterbody and timestamp.
    - Streamlined [`RecordController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/RecordController.php), [`QuickCatchModal.php`](file:///home/gmroczek/git/fishing/app/Livewire/Modals/QuickCatchModal.php), and [`RecordApiController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/RecordApiController.php) to eliminate duplicated secondary side-effects.
    - Added dedicated feature tests in [`CatchLoggedEventPipelineTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/CatchLoggedEventPipelineTest.php) with the full test suite passing at **286 tests (1,230 assertions)** and **0 PHPStan errors (Level 5)**.
42. **Design Token & Blade Component Standardization (`<x-card>`, `<x-badge>`, `<x-pageHero>`) (P2.1)**:
    - Designed and implemented universal encapsulated Blade UI components with dark mode support (`dark:`) and outdoor boat high-contrast tokens:
      * [`card.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/card.blade.php): Surface card container with support for header titles, subtitles, dynamic Lucide icons, icon tinting, pill badges, action links/slots, and footer telemetry ribbons.
      * [`badge.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/badge.blade.php): Standardized pill badges supporting 8 distinct palette variants (`teal`, `emerald`, `amber`, `sky`, `purple`, `rose`, `indigo`, `slate`), 3 sizes (`sm`, `md`, `lg`), `font-mono` numeric typography, and inline dynamic icons.
      * [`pageHero.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/pageHero.blade.php): Standardized page-level dark telemetry hero banner with support for action buttons, badge pill indicators, and custom telemetry metric slots.
    - Refactored all major application views to adopt these standardized components:
      * Angler Profile & Account Dossier ([`profile/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/profile/show.blade.php))
      * Angler Analytics & Production Summary ([`angler/stats.blade.php`](file:///home/gmroczek/git/fishing/resources/views/angler/stats.blade.php))
      * Angler Directory Index ([`angler/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/angler/index.blade.php))
      * Catches Analytics & Production Dashboard ([`record/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/record/index.blade.php))
      * Lake Dossier & Bathymetric Telemetry ([`lake/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/show.blade.php))
      * Lake Directory Index ([`lake/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/index.blade.php))
      * Expedition Detail & Multi-Day Log ([`expedition/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/show.blade.php))
      * Expedition Directory Index ([`expedition/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/index.blade.php))
    - Expanded unit and feature test coverage in [`BladeComponentsTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/BladeComponentsTest.php).
    - Verified entire test suite passing cleanly at **289 passing tests (1,249 assertions)** and **0 PHPStan errors (Level 5)**.
43. **System / Light / Dark 3-Way Theme Engine & Livewire Switcher (P2.2)**:
    - Implemented instant Zero-FOUC `<head>` theme engine in [`layouts/app.blade.php`](file:///home/gmroczek/git/fishing/resources/views/layouts/app.blade.php) with real-time `matchMedia` listeners for dynamic OS system dark/light switching.
    - Added database migration (`2026_09_20_000001_add_theme_preference_to_users_table.php`) adding `theme_preference` column (`system`, `light`, `dark`) with model defaults in [`User.php`](file:///home/gmroczek/git/fishing/app/Models/User.php) and [`UserFactory.php`](file:///home/gmroczek/git/fishing/database/factories/UserFactory.php).
    - Built reactive 3-way Livewire theme switcher component ([`ThemeSwitcher.php`](file:///home/gmroczek/git/fishing/app/Livewire/Ui/ThemeSwitcher.php) and [`theme-switcher.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/ui/theme-switcher.blade.php)) with sub-second Alpine client DOM sync.
    - Mounted switcher in Desktop Sidebar footer, Mobile Drawer, and User Account Preferences ([`profile/edit.blade.php`](file:///home/gmroczek/git/fishing/resources/views/profile/edit.blade.php)).
    - Updated [`ProfileController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/ProfileController.php) to validate and save user theme preferences.
    - Configured class-based dark mode selector `@variant dark (&:where(.dark, .dark *));` in [`resources/css/app.css`](file:///home/gmroczek/git/fishing/resources/css/app.css).
    - Added comprehensive feature tests in [`ThemeEngineTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/ThemeEngineTest.php).
    - Verified with all **294 tests passing (1,265 assertions)** and **0 PHPStan errors (Level 5)**.
44. **GenericDataTable & Sub-Tables Dark Mode Overhaul (P2.3)**:
    - Applied comprehensive `dark:` styling across [`generic-data-table.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/components/generic-data-table.blade.php) covering the interactive top toolbar, search input, clear button, dynamic filter selects, operator number inputs, date range popovers, row counter pills, and column visibility picker.
    - Reinforced table headers (`<thead>`, `<tr>`, `<th>`) with solid dark backgrounds (`dark:bg-slate-950`), high-contrast column labels (`dark:text-slate-300`), active sort indicators (`dark:bg-teal-950/80 dark:text-teal-300`), and subtle borders (`dark:border-slate-800`).
    - Styled all 15+ column cell formatters (species/angler avatars, links, family badges, unlinked pills, user role badges, GPS coordinates, weather and pressure badges, and admin user assignment forms).
    - Overhauled bottom pagination footer and per-page / density toggles in [`tailwind.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/pagination/tailwind.blade.php).
    - Standardized static table styling and empty states in [`emptyState.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/emptyState.blade.php), [`lure/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lure/show.blade.php), [`lure/model.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lure/model.blade.php), [`lure/category.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lure/category.blade.php), and [`sync-diagnostic-console.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/admin/sync-diagnostic-console.blade.php).
    - Compiled production Tailwind CSS assets (`public/build/assets/app-*.css`) with zero layout shifts.
    - Maintained 100% test pass rate (**294 passing tests, 1,265 assertions**) and **0 PHPStan errors (Level 5)**.
45. **NAS & Spatie Backup Management & Monitoring Console (`/admin/backups`)**:
    - Engineered dedicated Admin Backup Console in [`AdminBackupController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Admin/AdminBackupController.php) utilizing `Spatie\Backup\Config\Config` and `BackupDestinationStatusFactory` for deep runtime inspection of configured backup disks.
    - Built comprehensive telemetry dashboard ([`resources/views/admin/backups/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/admin/backups/index.blade.php)) with dark mode support (`dark:`):
      * **Reachability & Health Banner**: Real-time status badge (`● Healthy & Reachable` / `● Action Required`), failure causes, and disk indicator (`nas_backups` / `local_backups`).
      * **KPI Metric Cards**: Total snapshot count, storage quota gauge bar (`usedStorage` vs `megabytesInTotal`), latest snapshot timestamp with relative time, and oldest retention archive timestamp.
      * **Spatie Config & Retention Breakdown**: Visual summary of database engines (`mysql`), source paths (`storage/app/public`), gzip compression, and multi-tier pruning rules (7d all, 30d daily, 8w weekly, 12m monthly, 2y yearly).
      * **Snapshot Archive Table**: Granular listing of `.zip` archive filenames, exact file sizes in MB/GB, modification timestamps, safe direct download action, and confirmation modal deletion.
      * **Manual On-Demand Backup Triggers**: 1-click execution for "Backup DB Now" (`backup:run --only-db`), "Full Backup" (`backup:run`), and "Run Cleanup" (`backup:clean`).
    - Integrated NAS Backups overview card and navigation link with dynamic snapshot counter into Main Admin Portal ([`admin/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/admin/index.blade.php)), Sync Console ([`admin/sync/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/admin/sync/index.blade.php)), User Linking ([`admin/users/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/admin/users/index.blade.php)), and Trash Bin ([`admin/trash/index.blade.php`](file:///home/gmroczek/git/fishing/resources/views/admin/trash/index.blade.php)).
    - Added 10 dedicated feature tests in [`AdminBackupsTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AdminBackupsTest.php) covering auth gates, admin authorization, telemetry rendering, overview card counters, DB & full manual backup execution, cleanup execution, safe archive downloads, path traversal protection (`..`), and archive deletion.
    - Verified 100% test pass rate across the full test suite (**304 passing tests, 1,300 assertions**) and **0 PHPStan errors (Level 5)**.
46. **Catch Edit Route Wildcard Fix, Field Data Repairs & Sticky Paper Logbook Pre-Population (P1.1 & P1.2)**:
    - Fixed 405 Method Not Allowed error on catch edits by registering `Route::match(['put', 'patch'], '/{record}', [RecordController::class, 'update'])->name('record.update')` in [`routes/web.php`](file:///home/gmroczek/git/fishing/routes/web.php).
    - Added **Paper Logbook Fast Mode** on [`record/create.blade.php`](file:///home/gmroczek/git/fishing/resources/views/record/create.blade.php): upon saving a catch, sequentially retains previous values for Angler, Lake, Fish Species, Lure/Tackle (`<livewire:ui.lure-selector />`), and Catch Date while clearing unique catch fields (weight, length, temperature, notes, coordinates) and adding a 1-tap "Clear Defaults" button.
    - Executed database field updates:
      * Created & linked **Acme Little Cleo (Orange / Silver)** lure to Geren Mroczek's Northern Pike catch on 9/26/2026.
      * Linked **Rapala Shad Rap (Red, White)** to all 4 catches by Danny Brauer on 9/27/2026.
      * Updated surface water temperature to **61°F** on all 6 catches in Davies Lake on 9/27/2026.
    - Created full pre-update database backup snapshot via Spatie (`backup:run --only-db`).
    - Added feature tests in [`RecordControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/RecordControllerTest.php) covering parameterized PUT updates and sticky pre-population rendering.
    - Verified with **305 passing tests (1,306 assertions)** with **0 failures** and **0 PHPStan errors (Level 5)**.
47. **Angler Soft-Deletion Management, Dark Mode Visual Polish & 2-Decimal Scale Alignment (P1.3, P1.4, P1.5)**:
    - **Angler Soft-Deletion & Management Actions**:
      * Added Danger Zone card with confirmation dialog to [`angler/edit.blade.php`](file:///home/gmroczek/git/fishing/resources/views/angler/edit.blade.php) invoking `DELETE /angler/{id}`.
      * Enhanced [`generic-data-table.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/components/generic-data-table.blade.php) with dedicated Angler row actions (View Profile, Edit Details, and Soft-Delete Angler) hooked into Synology NAS sync outbox tracking.
      * Registered standard named routes (`angler.update`, `angler.destroy`, etc.) in [`routes/web.php`](file:///home/gmroczek/git/fishing/routes/web.php).
      * Added test assertions in [`AnglerControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AnglerControllerTest.php) verifying soft-deletion and unlinking safeguards.
    - **Dark Mode Form & Watermark Visual Polish**:
      * Resolved white-on-white text clash during input focus in Tacklebox Colorway Variant modal within [`resources/views/livewire/tacklebox/lure-catalog.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/tacklebox/lure-catalog.blade.php) (`dark:focus:bg-slate-800 dark:text-white`).
      * Overhauled [`resources/views/components/watermarkTapeMeasure.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/watermarkTapeMeasure.blade.php) with high-contrast metallic amber styling (`dark:fill-amber-400/[0.08]`, `dark:stroke-amber-400/30`, `dark:fill-amber-300/80`), replacing the washed out grey stripe with crisp ruler ticks and legible inch markings.
    - **Fish Scale 2-Decimal Weight Precision Alignment**:
      * Updated all catch logging forms and slide-over drawers to `step="0.01"` and `placeholder="e.g. 4.25"` in [`quick-catch-modal.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/modals/quick-catch-modal.blade.php) and [`record/quick.blade.php`](file:///home/gmroczek/git/fishing/resources/views/record/quick.blade.php).
      * Standardized weight formatting to 2 decimal places (`number_format(..., 2)`) across all leaderboards and dossier cards ([`profile/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/profile/show.blade.php), [`expedition/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/show.blade.php), [`lake/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/show.blade.php), [`fish/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php)).
      * Updated [`FishBreedControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/FishBreedControllerTest.php) assertions.
    - Verified entire test suite passing with **306 passing tests (1,310 assertions)** and **0 PHPStan errors (Level 5)**.
48. **Exception Catching & Diagnostic Logging Audit & Enhancements**:
    - Performed comprehensive audit of exception handling patterns across background jobs, event listeners, domain services, controllers, Livewire components, and REST API endpoints.
    - Integrated structured exception logging (`Log::error()`, `Log::warning()`, and `report($e)`) across:
      * [`AdminBackupController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Admin/AdminBackupController.php): `create()` and `clean()` actions to record `mysqldump` and retention prune errors in `laravel.log`.
      * [`AdminController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Admin/AdminController.php): `index()`, `triggerSync()`, `triggerBaselineSync()`, `markAllSynced()`, and `triggerWeatherSync()` actions.
      * [`SyncDiagnosticConsole.php`](file:///home/gmroczek/git/fishing/app/Livewire/Admin/SyncDiagnosticConsole.php): `triggerSync()`, `triggerBaselineSync()`, and `markAllSynced()` actions.
      * [`SyncApiController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/Api/v1/SyncApiController.php): wrapped per-entity outbox push processing in defensive `try/catch` blocks with model and entity UUID logging.
    - Added dedicated error logging regression tests in [`AdminBackupsTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AdminBackupsTest.php) and [`AdminWeatherSyncTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/AdminWeatherSyncTest.php).
    - Verified entire test suite passing cleanly with **309 passing tests (1,325 assertions)** and **0 failures**.
49. **Expedition Distinct Anglers Calculation & Table Alignment (P1.1)**:
    - Unified expedition angler identification across registered rosters and logged catches into a clean, low-maintenance distinct union:
      * **Expedition Model Distinct Accessors** ([`Expedition.php`](file:///home/gmroczek/git/fishing/app/Models/Expedition.php)): Added `$expedition->angler_ids`, dynamic `$expedition->anglers` collection accessor, and `$expedition->anglers_count` attribute to compute all unique anglers associated with a trip (from registered `crews` table or catches during the expedition date window).
      * **ExpeditionAnalyticsService Architecture** ([`ExpeditionAnalyticsService.php`](file:///home/gmroczek/git/fishing/app/Services/ExpeditionAnalyticsService.php)): Updated service to return `totalAnglersCount`, `daysFishedCount`, `totalTripDays`, `dailyAvgCatches`, and a unified `crewLeaderboard` for all distinct expedition anglers.
      * **Expedition Dossier UI Clean Up** ([`expedition/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/expedition/show.blade.php)): Streamlined trip header and Trip Crew Leaderboard to display a clean single metric (`X Anglers`) without splitting members into separate roster vs guest groups.
      * **GenericDataTable Expedition Column Type** ([`GenericDataTable.php`](file:///home/gmroczek/git/fishing/app/Livewire/Components/GenericDataTable.php) & [`generic-data-table.blade.php`](file:///home/gmroczek/git/fishing/resources/views/livewire/components/generic-data-table.blade.php)): Optimized SQL subquery union (`count(distinct angler_id)`) to calculate and sort by total unique trip anglers (`anglers_count`) with a single high-contrast pill badge.
    - Added comprehensive feature tests in [`ExpeditionAnalyticsTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/ExpeditionAnalyticsTest.php) and [`GenericDataTableLivewireTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/GenericDataTableLivewireTest.php).
51. **`/fish/{id}` Top 5 All-Time Specimens Weight Metric Prominence & Layout Polish (P1.1)**:
    - Redesigned the Top 5 All-Time Specimens strip in [`resources/views/fish/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php):
      * **Length & Weight Stacked on Right**: Rendered prominent amber monospace length badge (`38.0"`) with bold emerald scale weight (`14.50 lbs`) stacked directly underneath when weight telemetry is recorded, eliminating unnecessary placeholder text.
      * **Angler & Waterbody Hierarchy**: Standardized high-contrast typography (`text-sm font-bold` for angler name, `text-xs` for lake waterbody) with subtle hover color transitions.
      * **Centered Catch Date Footer**: Positioned larger `text-xs font-mono` catch date centered across a subtle top border line.
    - Added feature test in [`FishBreedControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/FishBreedControllerTest.php).
    - Verified with all **324 tests passing (1,402 assertions)** and **0 PHPStan errors (Level 5)**.
52. **Reusable `<x-specimenCard>` Blade Component & `/lake/{id}` Top 5 Catches Integration (P1.1)**:
    - Built reusable Blade component [`resources/views/components/specimenCard.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/specimenCard.blade.php) featuring:
      * Standardized layout: #1 gold ring rank badge on top-left, length (`38.0"`) & scale weight (`14.50 lbs`) stacked on top-right, angler name and adaptive subtitle in center, and centered catch date in footer.
      * Context-adaptive subtitle logic: supports `:showLake="true"` (renders lake name on species view), `:showSpecies="true"` (renders fish breed on lake view), `:showLake="true" :showSpecies="true"` (renders `Species • Lake` for future `/expedition/{id}` trips), and custom `subtitle` overrides.
    - Refactored [`resources/views/fish/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php) to use `<x-specimenCard :rank="$rank" :record="$trophy" :showLake="true" />`.
    - Modernized [`resources/views/lake/show.blade.php`](file:///home/gmroczek/git/fishing/resources/views/lake/show.blade.php) and [`app/Http/Controllers/LakeController.php`](file:///home/gmroczek/git/fishing/app/Http/Controllers/LakeController.php):
      * Replaced the old "Species Statistics" section with a "Top 5 Lake Catches" strip using `<x-specimenCard :rank="$rank" :record="$catch" :showSpecies="true" />`.
      * Backed by eager-loaded `$topCatches` query (`Record::with(['angler', 'fishBreed', 'lake'])->where('lakes_id', $lake->id)->orderBy('length', 'desc')->limit(5)->get()`).
    - Added comprehensive unit and feature test coverage in [`BladeComponentsTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/BladeComponentsTest.php), [`LakeControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/LakeControllerTest.php), and [`FishBreedControllerTest.php`](file:///home/gmroczek/git/fishing/tests/Feature/FishBreedControllerTest.php).
    - Verified entire test suite (**326 passing tests, 1,420 assertions**) with **0 failures** and **0 PHPStan errors (Level 5)**.
53. **Species-Normalized Trophy Scoring Engine ("Expedition Best Catch") (P3.2)**:
    - Built comprehensive length-normalized scoring system ($\text{Score} = (\text{Length} / L_{bench}) \times 100$) eliminating apex predator bias across mixed-species waters and trips.
    - **Database Migration & Backfill**:
      * Added `trophy_length_bench` and `trophy_weight_bench` to `fish_breeds` table.
      * Backfilled official Ontario Master Angler benchmark sizes across all 18 freshwater species.
    - **`TrophyScoringService` Architecture** ([`app/Services/TrophyScoringService.php`](file:///home/gmroczek/git/fishing/app/Services/TrophyScoringService.php)):
      * Implemented `calculateScore()`, `getTrophyTier()` (Master Angler 🏆, Gold Class 🥇, Silver Class 🥈, Standard), `getTopNormalizedCatches()`, and `estimateWeight()` biological power-curve regressions.
      * Multi-tier tie-breakers: `trophy_score DESC` $\to$ `weight DESC` $\to$ `length DESC` $\to$ `caught DESC`.
    - **UI Touchpoints & Component Enhancements**:
      * Enhanced `<x-specimenCard>` with `:showScore="true"` prop to render high-contrast score pills (`105.0 pts`).
      * Upgraded `/lake/{id}` (Lake Dossier) to rank Top 5 Lake Catches by normalized trophy score.
      * Upgraded `/expedition/{id}` (Expedition Dossier) with the new **"Expedition Best Catches"** trophy-normalized strip using `<x-specimenCard :showSpecies="true" :showLake="true" :showScore="true" />`.
    - **Documentation**: Documented the full scoring methodology, formula, benchmarks, and tier standards in [`README.md`](file:///home/gmroczek/git/fishing/README.md).
    - **Comprehensive Test Suite**: Added `TrophyScoringServiceTest.php` and feature tests in `ExpeditionControllerTest.php`, `LakeControllerTest.php`, and `BladeComponentsTest.php`.
    - Verified entire test suite (**332 passing tests, 1,466 assertions**) with **0 failures** and **0 PHPStan errors (Level 5)**.



