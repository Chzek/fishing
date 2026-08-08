---
name: ui-ux-auditor
description: Audits, reviews, tests, and enforces UI/UX design standards, responsive layouts, Tailwind CSS design tokens, outdoor boat Quick Catch usability, Leaflet map drawer UX, and visual excellence across the Fishing Logbook web application.
---

# UI/UX Auditor Skill (`ui-ux-auditor`)

This skill provides a comprehensive auditing, testing, and review framework to maintain state-of-the-art UI/UX quality across the **Fishing Logbook** web application.

---

## 🎨 1. Core Design System & Tokens (Option C Telemetry v2)

Every view in the application must strictly adhere to the established **Telemetry v2 Design System**:

| Design Token Category | Approved Class / Value | Context & Usage |
| :--- | :--- | :--- |
| **Dark Navigation / Sidebar** | `bg-slate-900`, `bg-slate-950/50` | Sidebar, mobile top header, slide-over drawer, mobile bottom bar |
| **Borders & Dividers** | `border-slate-800`, `border-slate-700` | Navigation boundaries, card borders, section rules |
| **Main Body Background** | `bg-slate-100` | Core content background behind main cards and forms |
| **Primary Brand Accent** | `teal-500`, `teal-400`, `teal-600` | Brand logo highlight, Quick Catch buttons, active navigation states |
| **Active Nav Link** | `bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400` | Selected navigation items in sidebar |
| **Alerts & Warnings** | `amber-400`, `amber-500` | Weather telemetry badges, admin privileges, offline warnings |
| **Sync Success Banner** | `emerald-600` | Global offline sync alert notification banner |
| **Destructive / Error** | `red-400`, `bg-red-500/10` | Delete actions, logout links, form error validation states |
| **Typography Hierarchy** | `font-sans antialiased text-slate-900` | Body text (`text-xs`, `text-sm`, `text-base`, `font-semibold`) |
| **Icons Standard** | Lucide (`<i data-lucide="..."></i>`) | Re-initialized dynamically via `window.initLucideIcons()` |

---

## 📱 2. Responsive Layout Architecture

Verify that every page cleanly supports both **Desktop** and **Mobile** viewports without horizontal scrolling (`overflow-x-hidden` or `min-w-0` on container wrappers):

### Desktop Layout (`lg:flex`)
- **64-width Sidebar (`w-64`)**:
  - Brand Header with `anchor` icon & `Telemetry v2` tag.
  - Primary **Quick Catch** action button (`/record/quick`).
  - Categorized links: **Field Tools** (`Dashboard & Stats`, `Map Explorer`, `Offline Maps`) and **Logbook Records** (`Expeditions`, `Catches Log`, `Lakes & Waters`, `Lures & Gear`, `Anglers`).
  - Footer with **Offline Sync Badge** (`offline-sync-badge`) and User Profile / Logout.
- **Main Content**: Wrapped in `p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto`.

### Mobile Layout (`lg:hidden`)
- **Sticky Top Bar (`sticky top-0 z-40`)**: Includes brand logo, Quick Catch button, and hamburger toggle opening the Alpine.js slide-over drawer (`mobileMenuOpen`).
- **Slide-over Drawer**: Alpine transition overlay containing full navigation links and logout action.
- **Fixed Bottom Navigation Bar (`fixed bottom-0 z-40`)**:
  - 5 key quick actions: `Stats`, `Map`, **Elevated Center Quick Catch (+)**, `Catches`, `Expeditions`.
  - Active tab highlighted in `text-teal-400`.

---

## ⚡ 3. Outdoor Boat Quick Catch Usability (`/record/quick`)

When auditing or building Quick Catch and mobile form interfaces:

1. **Touch Target Size**: All interactive buttons, select inputs, and radios must have a minimum tap area of **44x44px** (`py-3 px-4`).
2. **Outdoor Contrast**: Input fields must use high-contrast backgrounds (`bg-white` or dark cards with high contrast borders) readable in direct sunlight on a boat.
3. **Form Simplification**: Default to dropdowns/selects over free text entry where possible (Angler, Lake, Species, Lure).

---

## 🗺️ 4. Interactive Maps & Detail Drawer UX (`/map/explorer`, `/map/offline`)

1. **Leaflet Container Height**: Map canvas container must explicitly specify height (e.g., `h-[calc(100vh-8rem)]` or `min-h-[500px]`).
2. **Layer Switcher**: Include floating toggle control for `[ 🗺️ Topo / Waterbody ]` vs `[ 🛰️ Satellite Imagery ]`.
3. **Right Slide-over Drawer**: Clicking a map lake marker opens a responsive slide-over detail drawer displaying:
   - Lake name, bottom terrain, max depth, species breakdown.
   - Longest & fattest record catch badges.
   - Direct button to log a catch at that lake.
4. **Collision & Proximity Alerts**: Warning banners when dropping pins within 2 miles of existing lakes.

---

## 🔍 5. Step-by-Step UI/UX Audit Execution Protocol

When requested to run a UI/UX audit on a page or feature, follow these 4 steps:

### Step 1: Blade Template Code Analysis
- Inspect the targeted `.blade.php` file in `resources/views/`.
- Ensure it extends `@extends('layouts.app')` and defines `@section('content')`.
- Check for CSRF meta tags, correct `@auth`/`@guest` directives, and proper `<i data-lucide="...">` tags.
- Verify semantic HTML tags (`<main>`, `<header>`, `<nav>`, `<aside>`, `<article>`).

### Step 2: Route & HTTP Health Check
- Run the route audit script against local Sail:
  ```bash
  bash .agents/skills/ui-ux-auditor/scripts/audit_routes.sh
  ```
- Confirm HTTP 200 status and key metadata presence across target routes.

### Step 3: Design Token & Layout Check
- Verify color utility classes match the Telemetry v2 design matrix (Teal accents, Slate dark containers).
- Check responsive classes (`hidden lg:flex`, `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3`).
- Confirm form inputs have `<label>` elements with matching `for` IDs.

### Step 4: Remediation & Recommendations
- Summarize any UI/UX defects found (contrast gaps, missing Lucide icons, broken mobile padding, improper active link highlighting).
- Provide immediate code fixes.

---

## 📑 Detailed Route Checklist Reference

For specific route audit requirements, consult [references/ui-checklist.md](file://./references/ui-checklist.md).
