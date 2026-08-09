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
        Schema::table('fishing_rules', function (Blueprint $table) {
            $table->boolean('is_aggregate')->default(false)->after('species_name');
            $table->string('aggregate_group')->nullable()->after('is_aggregate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fishing_rules', function (Blueprint $table) {
            $table->dropColumn(['is_aggregate', 'aggregate_group']);
        });
    }
};
