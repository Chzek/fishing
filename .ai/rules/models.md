# Model & Database Rules

**Scope**: `app/Models/**`, `database/migrations/**`

## Invariants

1. **UUID Primary Keys & Sync Tracking**:
   - All domain models must use string UUID primary keys and sync status tracking via the `\Fishinglog\Traits\HasUuidAndSyncTracking` trait.
   - Models must declare `protected $fillable = ['id', 'sync_status', 'synced_at', ...];`.
   - Never use auto-incrementing integer IDs for new entities.

2. **Geospatial Geometry & Coordinates**:
   - Use `MatanYadaev\EloquentSpatial\Objects\Point` and `MatanYadaev\EloquentSpatial\Traits\HasSpatial` for spatial points (`location` column, SRID 4326 WGS 84).
   - Cast `location` in the model's `casts()` method: `'location' => Point::class`.
   - Maintain automatic bidirectional synchronization between legacy `latitude`/`longitude` float properties and the spatial `location` Point in model lifecycle hooks (`static::saving(...)`).

3. **Type Casting (`casts()`)**:
   - Use native Laravel 12 `protected function casts(): array` method definitions on all models rather than the legacy `$casts` property.
   - Explicitly cast date/time columns (`'caught' => 'datetime'`, `'synced_at' => 'datetime'`) and boolean flags (`'released' => 'boolean'`).
   - Preserve decimal string precision for length/weight formatting where required by views.

4. **Soft Deletes**:
   - Core domain models (`Lake`, `Record`, `Lure`, `Angler`, `Photo`, `Expedition`) must use `Illuminate\Database\Eloquent\SoftDeletes`.
   - Database migrations must include `$table->softDeletes()`.

5. **Migration Safety**:
   - Migrations must be non-destructive. Never drop existing data columns without migration safeguards.
   - Always run a database backup (`./vendor/bin/sail artisan backup:run --only-db`) prior to running migrations.
