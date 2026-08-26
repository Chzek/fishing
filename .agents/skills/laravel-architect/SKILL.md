---
name: laravel-architect
description: Master Laravel & Software Architect Agent for enforcing Laravel 12 conventions, designing clean domain services/Action classes, refactoring fat controllers, managing Eloquent relationships & migrations safely via Laravel Sail, and structuring Livewire 3 / Blade components in the Fishing Logbook application.
---

# Laravel Architect & Code Standards Skill

This skill governs core application architecture, Laravel 12 conventions, and code organization across the **Fishing Logbook** codebase.

---

## 1. Environment & Execution Guardrails

- **Laravel Sail Execution**:
  * ALWAYS execute PHP, Artisan, and Composer commands via Sail inside WSL (`./vendor/bin/sail artisan ...`, `./vendor/bin/sail test`).
- **Database Safety**:
  * Shared/live database environment. NEVER execute destructive schema commands (`migrate:fresh`, `db:refresh`, `db:wipe`) without explicit confirmation.
  * Always write idempotent database migrations and timestamped backups in `database/backups/`.

---

## 2. Architecture Patterns & Standards

### A. Controller Refactoring (Action Class Pattern)
Extract complex business logic out of HTTP controllers into invokable Action classes (`app/Actions/`):
- `app/Actions/Records/CreateCatchRecordAction.php`
- `app/Actions/Media/ProcessPhotoUploadAction.php`
- Keep controllers ultra-thin: validate via Form Request $\rightarrow$ invoke Action $\rightarrow$ return View/Response.

### B. Eloquent Models & Pipeline Filters
- Leverage Eloquent relationships (`belongsTo`, `hasMany`, `morphMany`).
- Use Pipeline filters (`app/Pipes/Filters/`) for complex listing query parameters (`SortBy`, `FilterBySearch`, `FilterByLength`, `FilterByAngler`, `FilterByLure`).

### C. Livewire 3 & Blade Component Standards
- Write reactive UI components using **Livewire 3 (standard PHP classes)** in `app/Livewire/`.
- Ensure Blade components handle attribute HTML entity encoding properly in tests (`$view->assertSee(...)`).
- Maintain Tailwind CSS v4 design tokens (`@theme`) in [`resources/css/app.css`](file:///home/gmroczek/git/fishing/resources/css/app.css).
