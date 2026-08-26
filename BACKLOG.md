# Application Backlog & Modernization Roadmap

This backlog tracks technical debt resolution, architecture refactoring, and feature initiatives for the **Fishing Logbook** application.

---

## 🧪 Future Initiatives & Interface Polish (P3 / On Hold)

### 1. Playwright E2E Front-End Test Suite Expansion
- **Status**: Deferred / On Hold
- **Impact**: Medium (Front-End Quality & Regression Prevention)
- **Description**: Expand Playwright E2E test suite to cover high-density interactive UI components:
  - `tacklebox.spec.js`: 2-Tier Category Tray expansion, master `Toggle Category Trays` window events, Lure Model accordions, color variant tables, and multi-color batch lure creation forms.
  - `species-dossier.spec.js`: Fish taxonomy family tab filtering, search inputs, species dossier telemetry badges, and artwork canvas rendering.
  - `quick-catch-map.spec.js`: Quick Catch logger form, `<optgroup>` category-grouped lure selection, and Leaflet Map Explorer drawer navigation.
  - `global-search.spec.js`: Global search form queries and grouped result section verification (Anglers, Waterbodies, Fish Species, Tacklebox).

### 2. Species Dossier UI Redesign & Telemetry Polish
- **Status**: Deferred / On Hold
- **Impact**: Low - Visual Polish
- **Description**: Polish species dossier pages (`/fish/{id}`):
  - Hero header framing and artwork layout polish.
  - Recommended tackle box pairing badges (e.g., *"Top Lure for Walleye: Rapala Shad Rap"*).
  - Regional distribution map integration and seasonal strike rate telemetry.

### 3. Manual Waterbody Regulations & Exceptions Review Framework
- **Status**: Deferred / On Hold
- **Impact**: Low - Regulatory Usability
- **Description**: Provide a simple manual review UI for anglers to inspect and verify specific lake exceptions and sanctuary rules directly against official MNR regulation guides when reviewing individual waterbody pages.
