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
            if (!Schema::hasColumn('lakes', 'structure')) {
                $table->string('structure')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('lakes', 'max_depth')) {
                $table->integer('max_depth')->unsigned()->nullable()->after('structure');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lakes', function (Blueprint $table) {
            $table->dropColumn(['structure', 'max_depth']);
        });
    }
};
