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
        Schema::table('fish_breeds', function (Blueprint $table) {
            $table->unsignedInteger('fish_families_id')->change();
            $table->foreign('fish_families_id')->references('id')->on('fish_families')->onDelete('cascade');
        });

        Schema::table('anglers', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('records', function (Blueprint $table) {
            $table->unsignedInteger('anglers_id')->change();
            $table->unsignedInteger('lakes_id')->change();
            $table->unsignedInteger('fish_breeds_id')->change();
            $table->unsignedInteger('lures_id')->nullable()->change();

            $table->foreign('anglers_id')->references('id')->on('anglers')->onDelete('cascade');
            $table->foreign('lakes_id')->references('id')->on('lakes')->onDelete('cascade');
            $table->foreign('fish_breeds_id')->references('id')->on('fish_breeds')->onDelete('cascade');
            $table->foreign('lures_id')->references('id')->on('lures')->onDelete('set null');

            $table->index('caught');
        });

        Schema::table('crews', function (Blueprint $table) {
            $table->unsignedInteger('expeditions_id')->change();
            $table->unsignedInteger('anglers_id')->change();

            $table->foreign('expeditions_id')->references('id')->on('expeditions')->onDelete('cascade');
            $table->foreign('anglers_id')->references('id')->on('anglers')->onDelete('cascade');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('expeditions_id')->change();
            $table->unsignedInteger('anglers_id')->nullable()->change();

            $table->foreign('expeditions_id')->references('id')->on('expeditions')->onDelete('cascade');
            $table->foreign('anglers_id')->references('id')->on('anglers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['expeditions_id']);
            $table->dropForeign(['anglers_id']);
        });

        Schema::table('crews', function (Blueprint $table) {
            $table->dropForeign(['expeditions_id']);
            $table->dropForeign(['anglers_id']);
        });

        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign(['anglers_id']);
            $table->dropForeign(['lakes_id']);
            $table->dropForeign(['fish_breeds_id']);
            $table->dropForeign(['lures_id']);
            $table->dropIndex(['caught']);
        });

        Schema::table('anglers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('fish_breeds', function (Blueprint $table) {
            $table->dropForeign(['fish_families_id']);
        });
    }
};
