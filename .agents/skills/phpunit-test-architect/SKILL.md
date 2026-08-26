---
name: phpunit-test-architect
description: Specialized PHPUnit & Feature Testing Agent for writing, debugging, running, and asserting backend unit/feature tests, Blade component views, Eloquent models, and HTTP controllers via Laravel Sail in the Fishing Logbook application.
---

# PHPUnit Test Architect Skill

This skill governs backend unit and feature testing using PHPUnit / Pest across [`tests/Feature/`](file:///home/gmroczek/git/fishing/tests/Feature/) and [`tests/Unit/`](file:///home/gmroczek/git/fishing/tests/Unit/).

---

## Execution Commands (Laravel Sail)

- **Run full test suite**:
  ```bash
  ./vendor/bin/sail exec -T laravel.test php artisan test
  ```
- **Run a specific test class**:
  ```bash
  ./vendor/bin/sail exec -T laravel.test php artisan test --filter=RecordControllerTest
  ```
- **Run tests in parallel**:
  ```bash
  ./vendor/bin/sail exec -T laravel.test php artisan test --parallel
  ```

---

## Project Testing Rules & Conventions

1. **Blade Component HTML Entity Encoding (`$this->blade(...)`)**:
   - When writing tests for custom Blade components, test for unescaped value strings (e.g. `$view->assertSee('14.5')`) to account for Blade's automatic HTML entity encoding (`&quot;`) on attribute strings.
2. **Database Isolation & Safety**:
   - Tests run against the `fishing_test` MySQL schema. Use `Illuminate\Foundation\Testing\DatabaseTransactions` or `RefreshDatabase` trait.
   - NEVER execute destructive database commands (`migrate:fresh`, `db:wipe`) on live environments.
3. **Fakes & Mocks Standard**:
   - **HTTP Requests (NAS Sync & Weather APIs)**: Always call `Http::fake()` to prevent real outgoing network calls during test runs.
   - **File Storage (Photos & Avatars)**: Call `Storage::fake('public')` before uploading files.
   - **Notifications & Mail**: Call `Notification::fake()` and `Mail::fake()`.
4. **Model Factories & Seeders**:
   - Leverage Eloquent factories (`Record::factory()`, `Angler::factory()`, `Lake::factory()`) for state setup.
