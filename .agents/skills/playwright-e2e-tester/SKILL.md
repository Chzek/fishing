---
name: playwright-e2e-tester
description: Specialized End-to-End Testing Agent for writing, debugging, and executing Playwright E2E browser tests across high-density interactive Blade, Livewire, and Alpine components in the Fishing Logbook web application.
---

# Playwright E2E Tester Skill

This skill provides testing guidelines and execution procedures for Playwright end-to-end browser tests in [`tests/e2e/`](file:///home/gmroczek/git/fishing/tests/e2e/).

---

## Execution Commands

- **Run all E2E tests**:
  ```bash
  npm run test:e2e
  ```
- **Run headed mode for visual debugging**:
  ```bash
  npm run test:e2e:headed
  ```
- **View HTML test report**:
  ```bash
  npm run test:e2e:report
  ```

---

## Testing Standards & Conventions

1. **Unique Element Test IDs**:
   - Ensure interactive UI elements have explicit `data-testid="..."` attributes (e.g. `data-testid="quick-catch-submit"`, `data-testid="lure-category-tray"`).
2. **Interactive UI Verification Focus**:
   - **Tacklebox Trays**: Test 2-tier Category Tray expansion, accordion toggles, and variant table rendering.
   - **Species Dossier**: Verify family tab filtering, search inputs, and telemetry badge calculations.
   - **Quick Catch & Leaflet Map**: Test Quick Catch form submissions, lure `<optgroup>` selections, and Leaflet Map Explorer drawer navigation.
   - **Global Search**: Test Omnibox search keyboard shortcuts (`Ctrl+K` / `/`) and multi-category result grouping.
