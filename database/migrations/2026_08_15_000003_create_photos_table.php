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
        Schema::create('photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sync_status')->default('synced');
            $table->timestamp('synced_at')->nullable();
            $table->uuidMorphs('photoable');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->text('caption')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->char('user_id', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
