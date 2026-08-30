# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application, continuously audited and prioritized by our specialized agent team (`laravel-architect`, `livewire-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, `ui-ux-auditor`).

---

## 🎯 Active Priority Roadmap (Ranked by Impact & Value)

### 🔥 Priority 1 (P1): High-Impact Usability & Reliability

#### 1. Automated Database & Media Backup Package (`spatie/laravel-backup`)
- **Agents**: `laravel-architect` & `nas-sync-architect`
- **Impact**: **High** (Database Safety & Disaster Recovery)
- **Description**: Integrate `spatie/laravel-backup` via Sail with scheduled, timestamped MySQL and `storage/app/public` media snapshots to `database/backups/` and the Synology NAS.

#### 2. Playwright Interactive E2E Front-End Test Suite Expansion (`playwright-e2e-tester`)
- **Agents**: `playwright-e2e-tester` & `phpunit-test-architect`
- **Impact**: **High** (Regression Prevention)
- **Description**: Expand Playwright E2E browser tests to cover high-density interactive UI components:
  - `catch-directory.spec.js`: Livewire 3 reactive debounced search, multi-filter combinations, and pagination.
  - `tacklebox.spec.js`: 2-Tier Category Tray expansion, Lure Model accordions, color variant tables, and multi-color batch lure creation forms.
  - `quick-catch-map.spec.js`: Quick Catch logger form, `<optgroup>` category-grouped lure selection, and Leaflet Map Explorer drawer navigation.

---

### 🚀 Priority 2 (P2): Angling Experience & Reactive Workflows

#### 3. Global Quick Catch Slide-Over Drawer Modal (`@livewire('modals.quick-catch-modal')`)
- **Agents**: `livewire-architect` & `ui-ux-auditor`
- **Impact**: **High** (1-Tap Catch Logging Anywhere)
- **Description**: Extract a slide-over modal component triggered from any page (Top Nav, Map Explorer `/map`, or Expedition Trip view) via `$dispatch('open-quick-catch')`, allowing anglers to log a catch without leaving their active map or trip dossier.

#### 4. Interactive Tacklebox Category Trays & Color Variant Grid (`@livewire('tacklebox.lure-catalog')`)
- **Agents**: `livewire-architect` & `seasoned-angler-advisor`
- **Impact**: **Medium-High** (Sub-second Tray Expansion)
- **Description**: Reusable Livewire catalog components for 2-Tier Category Trays, expanding lure model accordions, and inline color variant management.

#### 5. Species Dossier Recommended Tackle Badging & Quick Catch Shortcuts (`seasoned-angler-advisor` & `ui-ux-auditor`)
- **Agents**: `seasoned-angler-advisor` & `ui-ux-auditor`
- **Impact**: **Medium** (Usability & Angling Knowledge)
- **Description**: Enrich species dossier pages (`/fish/{id}`) and boat Quick Catch logger:
  - Display top-producing tackle pairing badges (e.g., *"Top Lure for Walleye: Rapala Shad Rap"*).
  - Add target species quick-filter shortcuts on the boat Quick Catch logger form.

#### 6. Waterbody Regulations & Exceptions Review Framework (`seasoned-angler-advisor`)
- **Agents**: `seasoned-angler-advisor`
- **Impact**: **Medium** (Regulatory Usability)
- **Description**: Provide a structured UI for anglers to inspect and verify specific lake exceptions and sanctuary rules directly against official FMZ regulation guides when reviewing individual waterbody pages.

---

### ⚙️ Priority 3 (P3): Infrastructure, Sync & Optimization

#### 7. NAS Sync Base64 Media Payload Chunking (`nas-sync-architect`)
- **Agents**: `nas-sync-architect`
- **Impact**: **Medium** (NAS Sync RAM Optimization)
- **Description**: Enhance [`NasSyncService.php`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php) photo payload synchronization to stream binary media files in multipart chunks instead of loading large base64 strings into PHP RAM during bulk catch syncs.

#### 8. Static Analysis & Strict Typing Auditing (`larastan/larastan`)
- **Agents**: `laravel-architect` & `phpunit-test-architect`
- **Impact**: **Medium** (Type Safety & Code Quality)
- **Description**: Configure PHPStan / Larastan at Level 5+ to guarantee strict typing, Eloquent relationship validation, and null-safety across all 13 models, services, and Action classes.

#### 9. Live Weather & Telemetry Barometer Widget (`@livewire('widgets.weather-telemetry')`)
- **Agents**: `livewire-architect` & `seasoned-angler-advisor`
- **Impact**: **Medium** (Real-Time Weather Signals)
- **Description**: Reactive weather widget that auto-fetches or updates live barometric pressure trends, wind velocity/direction, and surface water temp when selecting lakes during catch logging.

#### 10. Catch Logbook CSV / Excel Streaming Export (`spatie/laravel-simple-excel`)
- **Agents**: `laravel-architect`
- **Impact**: **Low-Medium** (Data Portability)
- **Description**: Zero-overhead streaming CSV/XLSX export for annual Catch Logbooks and Expedition summary sheets.

---

## 📦 Laravel & Livewire Ecosystem Package Evaluation

| Package | Category | Primary Benefit | Recommended Priority |
| :--- | :--- | :--- | :--- |
| [`blade-ui-kit/blade-lucide-icons`](https://github.com/blade-ui-kit/blade-lucide-icons) | Blade / UI | Server-rendered Lucide icons (`<x-lucide-fish />`) eliminating JS DOM injection delays & SVG duplication. | **Completed** |
| [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup) | DB Safety / DevOps | Automated, scheduled timestamped database and media directory backups to `database/backups/` & NAS. | **P1 (Immediate)** |
| [`larastan/larastan`](https://github.com/larastan/larastan) *(dev)* | Static Analysis | Strict level typing, Eloquent relationship validation, and null safety checks across all 13 models & services. | **P1 (Immediate)** |
| [`matanyadaev/laravel-eloquent-spatial`](https://github.com/matanyadaev/laravel-eloquent-spatial) | GIS / Mapping | Native MySQL 8 spatial geometry (`Point`, `Polygon`) with distance scopes (`whereDistance`) for Leaflet waypoint radius queries. | **P2 (Map Feature)** |
| [`spatie/laravel-simple-excel`](https://github.com/spatie/laravel-simple-excel) | Data Export | Zero-overhead streaming CSV/XLSX export for annual Catch Logbooks and Expedition summary sheets. | **P2 (Feature-driven)** |
| [`livewire/volt`](https://github.com/livewire/volt) | Livewire DX | Single-file reactive components for lightweight boat widgets (Barometer Telemetry, species badges). | **P3 (DX)** |
| [`laravel/boost`](https://github.com/laravel/boost) *(dev)* | AI Tooling / MCP | Optional MCP server for IDEs; requires Sail container wrapper configuration. | **P3 (Optional)** |

---

## 🏆 Completed Milestones (Merged into `master`)

1. **Searchable Autocomplete Lure & Tackle Selector (`@livewire('ui.lure-selector')`)**:
   - Built a reactive Livewire 3 autocomplete component with real-time debounced query filtering, category pill headers, 2-tier grouped tackle lists, manufacturer badges, technical specs, and verified catch stats.
   - Replaced static HTML select dropdowns in Standard Catch Logger (`/record/create`), Edit Catch (`/record/{id}/edit`), and boat Quick Catch (`/record/quick`).
   - Covered with PHPUnit Feature tests (`tests/Feature/LureSelectorLivewireTest.php`) and Playwright E2E browser tests (`tests/e2e/lure-selector.spec.js`).
2. **Blade Lucide Icon Native Migration (`mallardduck/blade-lucide-icons`)**:
   - Replaced all runtime client-side JavaScript icon injection (`<i data-lucide="...">` + `createIcons()`) across 72 Blade templates with server-rendered `<x-lucide-...>` and `<x-dynamic-component :component="'lucide-' . $icon" />` tags.
   - Stripped client-side JS bundle overhead, removed Livewire DOM morph and SPA navigation re-scan event listeners, eliminating icon flickering and layout shifts.
3. **Complete 18-Species Fish Avatar Library & Prompt Architecture**:
   - Generated high-resolution vector artwork with cel-shading and unified slate-blue badge styling for all 18 freshwater species in [`public/images/fish/avatars/`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/).
   - Documented master style guide and prompts in [`public/images/fish/avatars/PROMPTS.md`](file:///home/gmroczek/git/fishing/public/images/fish/avatars/PROMPTS.md).
   - Removed legacy `.svg` files and integrated `<x-fishAvatar>` across Species Index, Catches Directory, User & Angler Profile Personal Bests, and Admin Portal.
4. **Species Dossier Direct Catch Directory Banner**:
   - Replaced redundant recent catch cards on [`/fish/{id}`](file:///home/gmroczek/git/fishing/resources/views/fish/show.blade.php) with an interactive **Catches Logbook Directory** banner link pre-filtered to the species.
5. **Admin User-Angler Linking & Account Management**:
   - Added `admin_user_actions` column to `GenericDataTable` allowing administrators to pair registered users to Angler profiles, toggle admin roles, and manage accounts.
6. **App-Wide Generic Livewire 3 Data Table (`@livewire('components.generic-data-table')`)**:
   - Created unified data table with dynamic columns, instant search, multi-column Shift-click sorting, relation counts, soft-delete views, and custom query scopes across Lakes, Anglers, Catches, Expeditions, Trash, and Admin Users.
7. **Controller Refactoring into Action Classes & Domain Services**:
   - Extracted business logic from fat controllers into single-responsibility Action classes and Domain Services ([`CreateLureVariantAction`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php), [`ExpeditionAnalyticsService`](file:///home/gmroczek/git/fishing/app/Services/ExpeditionAnalyticsService.php)).
8. **Expedition & Species Dossier Query Optimization**:
   - Eliminated N+1 queries across trip dashboards, lake distribution tables, and monthly telemetry charts.
