<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('fish_breeds')
            ->where('name', 'Pink Salmon')
            ->whereNull('image')
            ->update(['image' => 'pink_salmon']);

        DB::table('fish_breeds')
            ->where('name', 'Perch')
            ->whereNull('image')
            ->update(['image' => 'perch']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('fish_breeds')
            ->where('name', 'Pink Salmon')
            ->where('image', 'pink_salmon')
            ->update(['image' => null]);

        DB::table('fish_breeds')
            ->where('name', 'Perch')
            ->where('image', 'perch')
            ->update(['image' => null]);
    }
};
