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
