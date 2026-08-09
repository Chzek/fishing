<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    protected array $tables = [
        'users',
        'anglers',
        'lakes',
        'fish_families',
        'fish_breeds',
        'lures',
        'records',
        'expeditions',
        'crews',
        'posts',
        'fishing_zones',
        'fishing_rules',
        'lake_daily_weather',
    ];

    /**
     * Foreign key definitions: [table, col, targetTable, targetCol].
     */
    protected array $foreignKeys = [
        ['anglers', 'user_id', 'users', 'id'],
        ['lakes', 'fishing_zone_id', 'fishing_zones', 'id'],
        ['fish_breeds', 'fish_families_id', 'fish_families', 'id'],
        ['records', 'anglers_id', 'anglers', 'id'],
        ['records', 'lakes_id', 'lakes', 'id'],
        ['records', 'fish_breeds_id', 'fish_breeds', 'id'],
        ['records', 'lures_id', 'lures', 'id'],
        ['crews', 'expeditions_id', 'expeditions', 'id'],
        ['crews', 'anglers_id', 'anglers', 'id'],
        ['posts', 'expeditions_id', 'expeditions', 'id'],
        ['posts', 'anglers_id', 'anglers', 'id'],
        ['fishing_rules', 'fishing_zone_id', 'fishing_zones', 'id'],
        ['fishing_rules', 'lake_id', 'lakes', 'id'],
        ['fishing_rules', 'fish_breed_id', 'fish_breeds', 'id'],
        ['lake_daily_weather', 'lakes_id', 'lakes', 'id'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // 1. Gather existing integer IDs and generate new UUID strings for them
        $idMap = []; // [tableName => [oldIntId => newUuidStr]]

        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (Schema::hasColumn($tableName, 'id')) {
                $rows = DB::table($tableName)->get(['id']);
                foreach ($rows as $row) {
                    if (is_numeric($row->id)) {
                        $idMap[$tableName][$row->id] = (string) Str::uuid();
                    }
                }
            }
        }

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // 2. Safely drop existing foreign key constraints
        foreach ($this->foreignKeys as [$table, $col, $targetTable, $targetCol]) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $col)) {
                try {
                    Schema::table($table, function (Blueprint $t) use ($col) {
                        $t->dropForeign([$col]);
                    });
                } catch (\Throwable $e) {
                    // Foreign key constraint might not exist yet
                }
            }
        }

        // 3. Convert primary key columns to CHAR(36) string UUIDs
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'sync_status')) {
                    $table->string('sync_status')->default('pending_upstream');
                }
                if (!Schema::hasColumn($tableName, 'synced_at')) {
                    $table->timestamp('synced_at')->nullable();
                }
            });

            if (Schema::hasColumn($tableName, 'id')) {
                if ($driver === 'mysql') {
                    DB::statement("ALTER TABLE `{$tableName}` MODIFY `id` CHAR(36) NOT NULL;");
                } else {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->string('id', 36)->change();
                    });
                }

                // Update integer IDs to generated UUID strings
                if (isset($idMap[$tableName])) {
                    foreach ($idMap[$tableName] as $oldId => $newUuid) {
                        DB::table($tableName)->where('id', $oldId)->update(['id' => $newUuid]);
                    }
                }
            } else if ($tableName === 'crews') {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->uuid('id')->primary()->first();
                });
                $crews = DB::table('crews')->get();
                foreach ($crews as $crew) {
                    DB::table('crews')->whereNull('id')->limit(1)->update(['id' => (string) Str::uuid()]);
                }
            }
        }

        // 4. Convert foreign key columns to CHAR(36) and update mapped values
        foreach ($this->foreignKeys as [$table, $col, $targetTable, $targetCol]) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $col)) {
                continue;
            }

            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$col}` CHAR(36) NULL;");
            } else {
                Schema::table($table, function (Blueprint $t) use ($col) {
                    $t->string($col, 36)->nullable()->change();
                });
            }

            if (isset($idMap[$targetTable])) {
                foreach ($idMap[$targetTable] as $oldId => $newUuid) {
                    DB::table($table)->where($col, $oldId)->update([$col => $newUuid]);
                }
            }
        }

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
