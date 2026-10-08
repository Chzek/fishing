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
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 64)->unique();
            $table->string('name', 100);
            $table->string('description', 255);
            $table->string('category', 50)->index();
            $table->string('tier', 30);
            $table->unsignedInteger('points')->default(10);
            $table->string('icon', 50);
            $table->string('image_path', 255)->nullable();
            $table->string('rule_type', 50)->index();
            $table->decimal('rule_threshold', 10, 2)->default(1.00);
            $table->string('accent_color', 30)->default('amber');
            $table->integer('sort_order')->default(0)->index();
            $table->string('sync_status')->default('pending_upstream');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('angler_badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('anglers_id', 36)->index();
            $table->char('badge_id', 36)->index();
            $table->char('record_id', 36)->nullable()->index();
            $table->char('expedition_id', 36)->nullable()->index();
            $table->timestamp('awarded_at')->index();
            $table->unsignedInteger('points')->default(0);
            $table->string('trigger_summary', 255)->nullable();
            $table->string('sync_status')->default('pending_upstream');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->foreign('anglers_id')->references('id')->on('anglers')->onDelete('cascade');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
            $table->foreign('record_id')->references('id')->on('records')->onDelete('set null');
            $table->foreign('expedition_id')->references('id')->on('expeditions')->onDelete('set null');
            $table->unique(['anglers_id', 'badge_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angler_badges');
        Schema::dropIfExists('badges');
    }
};
