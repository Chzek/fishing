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
        Schema::table('lakes', function (Blueprint $table) {
            $table->foreignId('fishing_zone_id')
                ->nullable()
                ->after('max_depth')
                ->constrained('fishing_zones')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lakes', function (Blueprint $table) {
            $table->dropForeign(['fishing_zone_id']);
            $table->dropColumn('fishing_zone_id');
        });
    }
};
