<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * List of core tables requiring UUID and sync status tracking.
     */
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
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'uuid')) {
                        $table->uuid('uuid')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'sync_status')) {
                        $table->string('sync_status')->default('pending_upstream');
                    }
                    if (!Schema::hasColumn($tableName, 'synced_at')) {
                        $table->timestamp('synced_at')->nullable();
                    }
                });

                // Populate UUIDs for existing rows without UUIDs
                if (Schema::hasColumn($tableName, 'id')) {
                    $rows = DB::table($tableName)->whereNull('uuid')->orWhere('uuid', '')->get(['id']);
                    foreach ($rows as $row) {
                        DB::table($tableName)->where('id', $row->id)->update([
                            'uuid' => (string) Str::uuid(),
                            'sync_status' => 'pending_upstream',
                        ]);
                    }
                } else {
                    $rows = DB::table($tableName)->whereNull('uuid')->orWhere('uuid', '')->get();
                    foreach ($rows as $row) {
                        DB::table($tableName)->whereNull('uuid')->limit(1)->update([
                            'uuid' => (string) Str::uuid(),
                            'sync_status' => 'pending_upstream',
                        ]);
                    }
                }

                // Add unique index on uuid column if not already present
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->unique('uuid');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'uuid')) {
                        $table->dropUnique([$tableName . '_uuid_unique']);
                        $table->dropColumn('uuid');
                    }
                    if (Schema::hasColumn($tableName, 'sync_status')) {
                        $table->dropColumn('sync_status');
                    }
                    if (Schema::hasColumn($tableName, 'synced_at')) {
                        $table->dropColumn('synced_at');
                    }
                });
            }
        }
    }
};
