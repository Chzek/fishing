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
        Schema::create('fishing_zones', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. "FMZ 7", "FMZ 9"
            $table->string('name'); // e.g. "FMZ 7 - Wawa & Algoma District"
            $table->string('province_state')->default('Ontario');
            $table->string('country')->default('Canada');
            $table->text('description')->nullable();
            $table->string('regulations_url')->nullable();
            $table->json('bounds')->nullable(); // Bounding box or polygon JSON
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishing_zones');
    }
};
