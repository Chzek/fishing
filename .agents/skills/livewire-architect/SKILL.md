---
name: livewire-architect
description: Specialized Livewire 3 & Alpine.js Frontend Architect Agent for designing reactive Livewire components, managing debounced wire:model inputs, optimizing sub-second DOM morphing, handling Livewire 3 pagination/sorting, and eliminating Alpine JS event loop conflicts in the Fishing Logbook web application.
---

# Livewire 3 & Alpine.js Frontend Architect Skill

This skill governs Livewire 3 component architecture, reactive state management, Alpine.js event bus integration, and frontend table reactivity across the **Fishing Logbook** codebase.

---

## 1. Core Invariants & Environment Rules

- **Execution Environment**:
  - Always verify Livewire components and feature tests via Laravel Sail (`./vendor/bin/sail exec -T laravel.test php artisan test --filter=CatchDirectoryLivewireTest`).
- **No Duplicate Alpine.js Bundling**:
  - **CRITICAL**: Livewire 3 automatically includes and starts Alpine.js out-of-the-box. Never invoke `Alpine.start()` manually in `resources/js/app.js` or import duplicate Alpine packages, as this breaks Livewire's DOM scanner and `wire:model` reactivity.
  - Register custom Alpine data components inside `document.addEventListener('livewire:init', ...)` in `app.js`.

---

## 2. Component Architecture Standards

### A. Livewire 3 Class Conventions (`app/Livewire/`)
- Place reactive PHP components in `app/Livewire/` (e.g. [`app/Livewire/Directory/CatchDirectory.php`](file:///home/gmroczek/git/fishing/app/Livewire/Directory/CatchDirectory.php)).
- Use Livewire 3 property attributes for URL query string sync:
  ```php
  #[Url(history: true)]
  public string $search = '';

  #[Url(history: true, as: 'sort_by')]
  public string $sortBy = 'date';
  ```
- Use property update hooks to reset pagination to Page 1 whenever filters change:
  ```php
  public function updatedSearch(): void { $this->resetPage(); }
  public function updatedSpecies(): void { $this->resetPage(); }
  ```

### B. Reactive Table Sorting
- Avoid static URL redirects (`window.location.search = ...`) in table headers.
- Implement an explicit `sortByColumn(string $column)` method on Livewire component classes:
  ```php
  public function sortByColumn(string $column): void
  {
      if ($this->sortBy === $column) {
          $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
          $this->sortBy = $column;
          $this->sortOrder = 'asc';
      }
      $this->resetPage();
  }
  ```

### C. Blade View Design System & Layout (`resources/views/livewire/`)
- Align Livewire views with the application's `<x-table.wrapper>` design tokens.
- Order top search toolbar filters to mirror table column sequence (e.g. `Search` $\rightarrow$ `Angler` $\rightarrow$ `Lake` $\rightarrow$ `Species` $\rightarrow$ `Length`).
- Wrap bottom pagination and table density toggles in a matching toolbar container (`bg-slate-50 border border-slate-200/80 rounded-xl p-3.5`).

---

## 3. Performance & Asset Directives

- **Input Debouncing**: Use `wire:model.live.debounce.300ms` for text inputs and `wire:model.live` for select dropdowns.
- **Eager Loading**: Eager-load all Eloquent model relationships (`with(['angler', 'lake.dailyWeather', 'fishBreed', 'lure'])`) inside `render()` to prevent N+1 query bottlenecks during re-renders.
- **DOM Morph Hooks**: Re-initialize dynamic icons (Lucide) after Livewire updates:
  ```javascript
  document.addEventListener('livewire:initialized', () => window.initLucideIcons());
  document.addEventListener('livewire:navigated', () => window.initLucideIcons());
  ```

---

## 4. Preventing Layout Shift & FOUC "Loading Hiccups"

To prevent the Flash of Unstyled Content (FOUC) or layout "hiccups" when Livewire components mount or update in the browser, strictly follow these architectural rules:

- **Server-Side Pre-rendered Default Classes**:
  - Never rely exclusively on Alpine `:class="..."` for initial padding, margins, or layout styles. Provide complete server-rendered default Tailwind classes (e.g., `class="py-3.5 px-4 ..."`) on the Blade HTML element so initial HTML is perfectly styled before Alpine JS initializes, then use Alpine object bindings `:class="{ 'py-2 px-4': density === 'compact', 'py-3.5 px-4': density !== 'compact' }"` for dynamic state changes.
- **Unique Morph DOM Keys (`wire:key`)**:
  - Always assign explicit `wire:key` attributes to table rows (`<tr wire:key="row-{{ $record->id ?? $loop->index }}">`) and headers (`<th wire:key="th-{{ $colKey }}">`). This prevents Livewire 3's DOM morphing engine from tearing down and rebuilding DOM nodes during re-renders.
- **Smooth Network Transitions (`wire:loading.class`)**:
  - Apply `wire:loading.class="opacity-60 pointer-events-none transition-opacity duration-150"` to table containers to provide smooth visual feedback during Livewire server requests.
- **Explicit Icon Sizing**:
  - Always assign fixed size and shrink rules (e.g., `class="w-4 h-4 inline-block shrink-0"`) to Lucide icon placeholders (`<i data-lucide="...">`) so layout bounds remain stable while icons are hydrated into SVG elements.
- **`x-cloak` for Alpine Overlays**:
  - Add `x-cloak` to Alpine dropdowns, filter menus, and floating tooltips so unrendered overlays do not flash on initial load.

