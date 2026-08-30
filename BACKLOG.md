# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application, compiled from our specialized agent team audit (`laravel-architect`, `livewire-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, `ui-ux-auditor`).

---

## 🚀 Completed Initiatives (P0 Roadmap)

### 1. Controller Refactoring into Action Classes & Services (`laravel-architect`)
- **Status**: Completed (Merged into `master`)
- **Impact**: High (Code Maintainability & Clean Architecture)
- **Description**: Extracted business logic from fat controllers into single-responsibility Action classes and Domain Services:
  - [`CreateLureVariantAction`](file:///home/gmroczek/git/fishing/app/Actions/Lures/CreateLureVariantAction.php): Refactored color variant batch loops and image uploads.
  - [`ExpeditionAnalyticsService`](file:///home/gmroczek/git/fishing/app/Services/ExpeditionAnalyticsService.php): Extracted trip analytics calculations (Brag Board, MVP angler, MVP lure, weather coverage) from [`ExpeditionController::show`](file:///home/gmroczek/git/fishing/app/Http/Controllers/ExpeditionController.php).
  - Dedicated `FormRequest` validation classes: [`StoreAnglerRequest`](file:///home/gmroczek/git/fishing/app/Http/Requests/StoreAnglerRequest.php), [`UpdateAnglerRequest`](file:///home/gmroczek/git/fishing/app/Http/Requests/UpdateAnglerRequest.php), [`StoreLakeRequest`](file:///home/gmroczek/git/fishing/app/Http/Requests/StoreLakeRequest.php).

### 2. Expedition & Species Dossier Query Optimization (`query-profiler-optimizer`)
- **Status**: Completed (Merged into `master`)
- **Impact**: High (Database Performance)
### 2.5 Reactive Livewire 3 Catch Directory Component Migration (`laravel-architect`)
- **Status**: Completed (Merged into `master`)
- **Impact**: High (Interactive UX & Sub-second Live Filtering)
- **Description**: Migrated the Catches Logbook Directory into a reactive Livewire 3 component ([`CatchDirectory.php`](file:///home/gmroczek/git/fishing/app/Livewire/Directory/CatchDirectory.php)) styled with the application's `<x-table.wrapper>` design system. Form controls (Search, Species, Lake, Length Operator & Inches) update the dataset dynamically without page reloads.

---

## 🛠️ Medium Priority (P1 / Infrastructure & Testing)

### 3. NAS Sync Base64 Media Payload Chunking (`nas-sync-architect`)
- **Status**: Backlog
- **Impact**: Medium (NAS Sync Reliability)
- **Description**: Enhance [`NasSyncService.php`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php) photo payload synchronization to stream binary media files in multipart chunks instead of loading large base64 strings into PHP RAM.

### 4. Playwright Interactive E2E Front-End Test Suite Expansion (`playwright-e2e-tester`)
- **Status**: Backlog
- **Impact**: Medium (Regression Prevention)
- **Description**: Expand Playwright E2E browser tests to cover high-density interactive UI components:
  - `catch-directory.spec.js`: Livewire 3 reactive debounced search, dropdown filters, and pagination.
  - `tacklebox.spec.js`: 2-Tier Category Tray expansion, Lure Model accordions, color variant tables, and multi-color batch lure creation forms.
  - `quick-catch-map.spec.js`: Quick Catch logger form, `<optgroup>` category-grouped lure selection, and Leaflet Map Explorer drawer navigation.

---

## 🎨 Front-End & Usability (P2 / Angler UX & Domain Features)

### 5. Species Dossier Recommended Tackle Badging & Quick Catch Shortcuts (`seasoned-angler-advisor` & `ui-ux-auditor`)
- **Status**: Backlog
- **Impact**: Low - Usability & Angling Experience
- **Description**: Enrich species dossier pages (`/fish/{id}`) and boat Quick Catch logger:
  - Display top-producing tackle pairing badges (e.g., *"Top Lure for Walleye: Rapala Shad Rap"*).
  - Add target species quick-filter shortcuts on the boat Quick Catch logger form.

### 6. Waterbody Regulations & Exceptions Review Framework (`seasoned-angler-advisor`)
- **Status**: Backlog
- **Impact**: Low - Regulatory Usability
- **Description**: Provide a simple manual review UI for anglers to inspect and verify specific lake exceptions and sanctuary rules directly against official MNR regulation guides when reviewing individual waterbody pages.

### 6.5 Fish Breed Avatars & Species Vector Artwork Asset Library (`seasoned-angler-advisor` & `ui-ux-auditor`)
- **Status**: Backlog (P1 Recommended)
- **Impact**: Medium-High (Visual Density, Directory Recognition & Dossier Branding)
- **Description**: Produce, curate, and optimize high-resolution species vector/PNG avatar assets in `public/images/fish/avatars/` for all freshwater fish breeds (Walleye, Northern Pike, Smallmouth/Largemouth Bass, Lake Trout, Brook Trout, Muskellunge, Yellow Perch, Crappie, etc.):
  - Ensure standardized circular bounding, transparent background, and taxonomic family accent ring contrast in [`fishAvatar.blade.php`](file:///home/gmroczek/git/fishing/resources/views/components/fishAvatar.blade.php).
  - Verify seamless rendering across Catch Logbook, Species Directory (`/fish`), Brag Board MVP cards, and Quick Catch logger dropdowns.

---

## 📦 Laravel & Livewire Ecosystem Package Roadmap

Evaluated packages recommended for integration to boost developer velocity, runtime efficiency, and domain feature capabilities:

| Package | Category | Primary Benefit | Recommended Priority |
| :--- | :--- | :--- | :--- |
| [`blade-ui-kit/blade-lucide-icons`](https://github.com/blade-ui-kit/blade-lucide-icons) | Blade / UI | Server-rendered Lucide icons (`<x-lucide-fish />`) eliminating JS DOM injection delays & SVG duplication. | **P1 (Immediate)** |
| [`spatie/laravel-backup`](https://github.com/spatie/laravel-backup) | DB Safety / DevOps | Automated, scheduled timestamped database and media directory backups to `database/backups/` & NAS. | **P1 (Immediate)** |
| [`larastan/larastan`](https://github.com/larastan/larastan) *(dev)* | Static Analysis | Strict level typing, Eloquent relationship validation, and null safety checks across all 13 models & services. | **P1 (Immediate)** |
| [`matanyadaev/laravel-eloquent-spatial`](https://github.com/matanyadaev/laravel-eloquent-spatial) | GIS / Mapping | Native MySQL 8 spatial geometry (`Point`, `Polygon`) with distance scopes (`whereDistance`) for Leaflet waypoint radius queries. | **P2 (Map Feature)** |
| [`spatie/laravel-simple-excel`](https://github.com/spatie/laravel-simple-excel) | Data Export | Zero-overhead streaming CSV/XLSX export for annual Catch Logbooks and Expedition summary sheets. | **P2 (Feature-driven)** |
| [`livewire/volt`](https://github.com/livewire/volt) | Livewire DX | Single-file reactive components for lightweight boat widgets (Barometer Telemetry, species badges). | **P3 (DX)** |
| [`laravel/boost`](https://github.com/laravel/boost) *(dev)* | AI Tooling / MCP | Optional MCP server for IDEs (Cursor/Claude Code); requires Sail container wrapper configuration. | **P3 (Optional)** |

---

## 🧩 Reusable Livewire 3 Component Architecture Roadmap (`livewire-architect` & `laravel-architect`)

### 7. Searchable Autocomplete Lure & Tackle Selector (`@livewire('ui.lure-selector')`)
- **Status**: Backlog (P1 Recommended)
- **Impact**: High (Boat Usability & Quick Catch UX)
- **Description**: Build a reusable Livewire autocomplete selector with lure thumbnails, manufacturer badges, 2-tier category grouping (*Crankbaits $\rightarrow$ Rapala Shad Rap*), and instant search for Quick Catch (`/record/quick`) and Standard Logger (`/record/create`).

### 8. Generic Livewire Data Table Component (`@livewire('components.generic-data-table')`)
- **Status**: Completed (Merged into `master`)
- **Impact**: High (App-Wide Code & UX Unification)
- **Description**: Extracted a generic Livewire 3 data table component ([`GenericDataTable.php`](file:///home/gmroczek/git/fishing/app/Livewire/Components/GenericDataTable.php)) with dynamic column rendering, real-time search, 1-click header sorting, and custom pagination. Migrated Lakes Roster ([`/lake`](file:///home/gmroczek/git/fishing/resources/views/lake/index.blade.php)) and Anglers Roster ([`/angler`](file:///home/gmroczek/git/fishing/resources/views/angler/index.blade.php)).

### 9. Global Quick Catch Slide-Over Drawer Modal (`@livewire('modals.quick-catch-modal')`)
- **Status**: Backlog (P2 Recommended)
- **Impact**: High (1-Tap Catch Logging Anywhere)
- **Description**: Extract a slide-over modal component triggered from any page (Top Nav, Map Explorer `/map`, or Expedition Trip view) via `$dispatch('open-quick-catch')`.

### 10. Interactive Tacklebox Category Trays & Color Variant Grid (`@livewire('tacklebox.lure-catalog')`)
- **Status**: Backlog (P2 Recommended)
- **Impact**: High (Sub-second Tray Expansion)
- **Description**: Reusable Livewire catalog components for 2-Tier Category Trays, expanding lure model accordions, and inline color variant management.

### 11. Live Weather & Telemetry Barometer Widget (`@livewire('widgets.weather-telemetry')`)
- **Status**: Backlog (P3 Recommended)
- **Impact**: Medium (Real-Time Weather Signals)
- **Description**: Reactive weather widget that auto-fetches or updates live barometric pressure trends, wind velocity/direction, and surface water temp when selecting lakes during catch logging.

