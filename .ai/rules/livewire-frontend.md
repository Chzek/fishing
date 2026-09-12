# Livewire 3 & Frontend Rules

**Scope**: `app/Livewire/**`, `resources/views/**`

## Invariants

1. **Debounced Livewire Inputs**:
   - Real-time search fields, filters, and dynamic text inputs must use debounced data binding: `wire:model.live.debounce.300ms` (or `debounce.500ms`) to prevent input thrashing and excessive network roundtrips.

2. **Event-Driven Architecture**:
   - Cross-component communication must use standard Livewire 3 event dispatching:
     - Dispatch from Blade: `$dispatch('open-quick-catch', { lake_id: '...' })`
     - Dispatch from PHP: `$this->dispatch('catch-saved', id: $record->id)`
     - Listen in PHP: `#[On('catch-saved')] public function refreshCatches(): void`

3. **Alpine.js & DOM Morphing**:
   - Use Alpine.js (`x-data`, `x-show`, `x-transition`) for client-only UI states (slide-over drawers, dropdowns, modal visibility, Leaflet map initialization).
   - Add unique `wire:key` attributes to all looped elements and dynamic cards inside `@foreach` / `@forelse` blocks to eliminate Livewire 3 DOM morphing conflicts.

4. **Design System & Blade Components**:
   - Follow the application's outdoor dark/frosted telemetry aesthetic using curated Tailwind tokens (`slate-900`, `slate-800`, `teal-500`, `amber-500`, `sky-500`).
   - Custom SVG watermarks or decorative graphics must use `pointer-events-none select-none` and appropriate opacity so text content remains 100% legible.
