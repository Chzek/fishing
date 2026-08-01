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
            $table->softDeletes();
        });

        Schema::table('lakes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('expeditions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('anglers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anglers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('expeditions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('lakes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('records', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
