<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lakes', function (Blueprint $table) {
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable()->after('longitude');
        });

        Schema::table('records', function (Blueprint $table) {
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable()->after('longitude');
        });

        // Backfill existing lakes with valid coordinates
        DB::statement("
            UPDATE lakes 
            SET location = ST_SRID(Point(longitude, latitude), 4326) 
            WHERE latitude IS NOT NULL 
              AND longitude IS NOT NULL 
              AND latitude != 0 
              AND longitude != 0
        ");

        // Backfill existing records with valid coordinates
        DB::statement("
            UPDATE records 
            SET location = ST_SRID(Point(longitude, latitude), 4326) 
            WHERE latitude IS NOT NULL 
              AND longitude IS NOT NULL 
              AND latitude != 0 
              AND longitude != 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn('location');
        });

        Schema::table('lakes', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
