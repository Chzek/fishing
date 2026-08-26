---
name: query-profiler-optimizer
description: Specialized Database Performance Agent for auditing SQL queries, analyzing EXPLAIN execution plans in Laravel Sail, eliminating N+1 / cloned query bottlenecks, and optimizing Eloquent & raw MySQL index performance across the Fishing Logbook application.
---

# Query Profiler & Database Optimizer Skill

This skill provides step-by-step procedures for auditing database performance, running EXPLAIN query plans, and refactoring slow Eloquent queries in the **Fishing Logbook** codebase.

---

## Key Workflows

### 1. Profile Controller Query Latency & Query Counts
Run an empirical benchmark via Laravel Sail:
```bash
./vendor/bin/sail exec -T laravel.test php -r '
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::enableQueryLog();
$start = microtime(true);

// Execute target controller action
$response = app(Fishinglog\Http\Controllers\RecordController::class)->index(app(Illuminate\Pipeline\Pipeline::class), Illuminate\Http\Request::create("/record", "GET"));

$queries = DB::getQueryLog();
echo "Queries Count: " . count($queries) . "\n";
echo "Total SQL Time: " . round(array_sum(array_column($queries, "time")), 2) . " ms\n";
'
```

### 2. Run EXPLAIN Execution Plans
Inspect MySQL execution plans to detect `type: ALL` (Full Table Scans) and `Extra: Using filesort` / `Using temporary`:
```sql
EXPLAIN SELECT * FROM records WHERE deleted_at IS NULL ORDER BY caught DESC, lakes_id ASC, anglers_id ASC LIMIT 10;
```

### 3. Optimization Rules & Refactoring Patterns

- **Never wrap indexed columns in SQL functions inside `JOIN` or `WHERE` clauses**:
  * *Bad*: `ON DATE(records.caught) = lake_daily_weather.date` (Forces Full Table Scan)
  * *Good*: Add an indexed `caught_date` column or store `caught` as indexed `DATE`.
- **Consolidate multi-clone aggregate queries into `selectRaw()`**:
  * Combine `count()`, `sum()`, `avg()`, and conditional status counts into a single query builder call.
- **Cache High-Density Telemetry**:
  * Wrap overview statistics in `Cache::remember('catch_directory_stats', 300, fn() => ...)` or per-angler cache keys.
