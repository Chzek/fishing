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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sync_status')->default('pending_upstream');
            $table->timestamp('synced_at')->nullable();
            $table->char('expeditions_id', 36)->nullable()->index();
            $table->date('entry_date')->nullable()->index();
            $table->date('start_date')->nullable()->index();
            $table->date('end_date')->nullable()->index();
            $table->string('title');
            $table->longText('body_markdown');
            $table->text('highlights')->nullable();
            $table->string('weather_summary')->nullable();
            $table->string('location_summary')->nullable();
            $table->string('template_type')->default('cabin_journal');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('expeditions_id')->references('id')->on('expeditions')->onDelete('set null');
        });

        Schema::create('journal_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sync_status')->default('pending_upstream');
            $table->timestamp('synced_at')->nullable();
            $table->char('journal_entry_id', 36)->nullable()->index();
            $table->unsignedInteger('page_number')->nullable();
            $table->unsignedInteger('sequence_order')->default(1)->index();
            $table->string('filename')->index();
            $table->string('photo_path');
            $table->longText('raw_ocr_text')->nullable();
            $table->json('structured_metadata')->nullable();
            $table->boolean('is_processed')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        Schema::create('journal_entry_anglers', function (Blueprint $table) {
            $table->char('journal_entry_id', 36)->index();
            $table->char('angler_id', 36)->index();
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('angler_id')->references('id')->on('anglers')->onDelete('cascade');
            $table->primary(['journal_entry_id', 'angler_id']);
        });

        Schema::create('journal_entry_lakes', function (Blueprint $table) {
            $table->char('journal_entry_id', 36)->index();
            $table->char('lake_id', 36)->index();
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('lake_id')->references('id')->on('lakes')->onDelete('cascade');
            $table->primary(['journal_entry_id', 'lake_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lakes');
        Schema::dropIfExists('journal_entry_anglers');
        Schema::dropIfExists('journal_pages');
        Schema::dropIfExists('journal_entries');
    }
};
