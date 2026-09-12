<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->index(['anglers_id', 'caught'], 'records_anglers_caught_idx');
            $table->index(['lakes_id', 'fish_breeds_id'], 'records_lakes_fish_breeds_idx');
            $table->index(['fish_breeds_id', 'length'], 'records_fish_breeds_length_idx');
            $table->index(['fish_breeds_id', 'weight'], 'records_fish_breeds_weight_idx');
            $table->index(['lakes_id', 'caught'], 'records_lakes_caught_idx');
            $table->index(['anglers_id', 'fish_breeds_id'], 'records_anglers_fish_breeds_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropIndex('records_anglers_caught_idx');
            $table->dropIndex('records_lakes_fish_breeds_idx');
            $table->dropIndex('records_fish_breeds_length_idx');
            $table->dropIndex('records_fish_breeds_weight_idx');
            $table->dropIndex('records_lakes_caught_idx');
            $table->dropIndex('records_anglers_fish_breeds_idx');
        });
    }
};
