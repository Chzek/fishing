# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application, compiled from our specialized agent team audit (`laravel-architect`, `query-profiler-optimizer`, `nas-sync-architect`, `phpunit-test-architect`, `playwright-e2e-tester`, `seasoned-angler-advisor`, `ui-ux-auditor`).

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
