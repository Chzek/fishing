# Detailed Route UI/UX Checklist

This reference maps out every key user-facing route in `routes/web.php` and its specific UI/UX requirements.

---

## 1. Landing & Authentication
- **`GET /` (`welcome.blade.php`)**:
  - Hero banner with high-resolution visual backdrop and Telemetry v2 brand tagline.
  - Clear CTA buttons (`Login`, `Register`, `Explore Quick Catch`).
  - Feature highlights (Offline Boat Logging, Lake Explorer, Weather Telemetry, Gear Analytics).

- **`GET /login`, `GET /register` (`resources/views/auth/`)**:
  - Centered card layout with dark Slate background (`bg-slate-900` or `bg-slate-800`).
  - Form validation error feedback styled in `text-red-400` / `border-red-500`.
  - Accessible inputs with `<label>` tags and clear focus outlines (`focus:ring-2 focus:ring-teal-500`).

---

## 2. Profile & Dashboard
- **`GET /profile` (`resources/views/profile/`)**:
  - Overview grid displaying total catches, trophy count, top lures, and personal record lengths.
  - Avatar management section (`/angler/avatar`) with file upload preview.
  - Weather summary widget for recent fishing trips.

---

## 3. Maps & Spatial Exploration
- **`GET /map/explorer` (`resources/views/map/explorer.blade.php`)**:
  - Full-screen or max-height Leaflet viewport (`h-[calc(100vh-8rem)]`).
  - Multi-filter bar: `Fish Species`, `Angler`, `Lure`, `Trophy Only`, `Season Year`.
  - Layer switcher: `[ 🗺️ Topo / Waterbody ]` | `[ 🛰️ Satellite Imagery ]`.
  - Slide-over detail drawer on marker click showing species breakdown, record catches, and quick catch trigger.

- **`GET /map/offline` (`resources/views/map/offline.blade.php`)**:
  - Pre-trip map tile download manager UI (`📥 Download Wawa Region Pack (~55 MB)`).
  - PWA CacheStorage progress bar & status indicators.
  - Downloaded region bounding box preview on Leaflet map.

---

## 4. Field Logging & Catches
- **`GET /record/quick` (`resources/views/record/quick.blade.php`)**:
  - Optimized for single-hand mobile touch usage on the water.
  - Large tap targets (44px+ height), high contrast selects/inputs.
  - Instant offline local queue saving (`IndexedDB` + Client UUID idempotency).
  - Status feedback badge (`⛵ Catches Queued`).

- **`GET /record`, `GET /record/{id}` (`resources/views/record/`)**:
  - Catches list with species badges, length/weight callouts, lure tags, and date/time.
  - Detail view displaying lake location map pin, weather telemetry card (Air Temp, Pressure, Wind, Sky), and photo/notes.

---

## 5. Expeditions & Crew
- **`GET /expedition` (`resources/views/expedition/`)**:
  - Expedition cards showing start/end dates, location, crew members, and total catches logged.
  - Crew member avatars and shared trip stats.

---

## 6. Lakes, Waters & Gear Catalog
- **`GET /lake`, `GET /lake/create`, `GET /lake/{id}` (`resources/views/lake/`)**:
  - Lake list with region tags, bottom terrain tags (`Granite/Rock`, `Weedline`, `Drop-off`), and max depth.
  - Location picker map pin on create/edit views with `📍 Use Current GPS Location` hardware satellite button.
  - Proximity collision warning banner if pin is within 2 miles of an existing lake.

- **`GET /lure` (`resources/views/lure/`)**:
  - Tackle catalog grid with lure images, type tags (`Crankbait`, `Jig`, `Spoon`, `Topwater`), color pattern, and catch efficacy stats.
