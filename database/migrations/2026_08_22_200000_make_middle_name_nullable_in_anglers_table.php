<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('anglers', function (Blueprint $table) {
            $table->string('middleName')->nullable()->change();
        });

        // Clean up existing placeholder question marks & empty strings in anglers database
        DB::table('anglers')
            ->whereIn('middleName', ['?', 'N/A', '', ' '])
            ->update(['middleName' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anglers', function (Blueprint $table) {
            $table->string('middleName')->nullable(false)->change();
        });
    }
};
