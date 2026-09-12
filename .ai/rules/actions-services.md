# Actions & Domain Services Rules

**Scope**: `app/Actions/**`, `app/Services/**`

## Invariants

1. **Single-Purpose Action Classes**:
   - Place mutating business logic into single-purpose Action classes under `app/Actions/<Domain>/` (e.g. `app/Actions/Records/CreateCatchRecordAction.php`, `app/Actions/Lures/CreateLureVariantAction.php`).
   - Action classes must expose a single, strongly-typed public `execute(...)` method.
   - Return the created or modified Eloquent model instance or domain result DTO.

2. **Domain Services**:
   - Complex, multi-model business logic and external integrations belong in dedicated domain services under `app/Services/` (e.g. `MediaSyncManager`, `ExpeditionAnalyticsService`, `WeatherTelemetryService`, `GeoZoneDetector`).
   - Domain services must be constructor-injected or resolved via the Laravel service container.

3. **Transaction Boundaries**:
   - Any Action or Service method that performs multi-table database mutations (e.g. creating records with photo attachments and updating angler stats) must wrap the operation within `DB::transaction(function () { ... })`.

4. **Static Analysis & Strict Typing**:
   - All Action and Service methods must specify explicit parameter type hints and return type declarations.
   - Avoid generic `mixed` types; document array shapes in PHPDoc blocks where structured arrays are returned.
