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
        Schema::create('fishing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fishing_zone_id')->nullable()->constrained('fishing_zones')->cascadeOnDelete();
            $table->unsignedInteger('lake_id')->nullable();
            $table->foreign('lake_id')->references('id')->on('lakes')->cascadeOnDelete();
            $table->unsignedInteger('fish_breed_id')->nullable();
            $table->foreign('fish_breed_id')->references('id')->on('fish_breeds')->cascadeOnDelete();
            $table->string('species_name')->nullable();
            $table->string('season')->nullable(); // e.g. "3rd Sat. in May to Dec 31"
            $table->string('sport_limit')->nullable(); // e.g. "S - 4"
            $table->string('conservation_limit')->nullable(); // e.g. "C - 2"
            $table->string('size_restriction')->nullable(); // e.g. "None over 46 cm (18.1 in)"
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishing_rules');
    }
};
