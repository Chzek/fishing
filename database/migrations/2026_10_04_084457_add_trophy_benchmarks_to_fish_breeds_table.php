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
            $table->decimal('trophy_length_bench', 5, 2)->nullable()->after('image');
            $table->decimal('trophy_weight_bench', 5, 2)->nullable()->after('trophy_length_bench');
        });

        // Seed / backfill standard benchmark lengths and weights
        $benchmarks = [
            'rock bass' => ['length' => 10.0, 'weight' => 0.75],
            'largemouth bass' => ['length' => 20.0, 'weight' => 5.00],
            'smallmouth bass' => ['length' => 20.0, 'weight' => 4.50],
            'northern pike' => ['length' => 36.0, 'weight' => 15.00],
            'muskellunge' => ['length' => 48.0, 'weight' => 30.00],
            'lake trout' => ['length' => 32.0, 'weight' => 15.00],
            'brook trout' => ['length' => 18.0, 'weight' => 3.50],
            'splake' => ['length' => 22.0, 'weight' => 5.00],
            'brown trout' => ['length' => 24.0, 'weight' => 6.00],
            'bluegill' => ['length' => 9.5, 'weight' => 0.85],
            'walleye' => ['length' => 28.0, 'weight' => 8.00],
            'pink salmon' => ['length' => 20.0, 'weight' => 3.50],
            'chinook' => ['length' => 34.0, 'weight' => 18.00],
            'king' => ['length' => 34.0, 'weight' => 18.00],
            'atlantic' => ['length' => 30.0, 'weight' => 10.00],
            'yellow perch' => ['length' => 12.0, 'weight' => 1.25],
            'perch' => ['length' => 12.0, 'weight' => 1.25],
            'crappie' => ['length' => 13.0, 'weight' => 1.50],
            'rainbow' => ['length' => 26.0, 'weight' => 8.00],
            'steelhead' => ['length' => 26.0, 'weight' => 8.00],
            'choho' => ['length' => 28.0, 'weight' => 10.00],
            'coho' => ['length' => 28.0, 'weight' => 10.00],
            'silver' => ['length' => 28.0, 'weight' => 10.00],
        ];

        $breeds = \Illuminate\Support\Facades\DB::table('fish_breeds')->get();
        foreach ($breeds as $breed) {
            $nameLower = strtolower(trim($breed->name));
            $length = 20.0;
            $weight = 5.0;

            foreach ($benchmarks as $key => $values) {
                if (str_contains($nameLower, $key)) {
                    $length = $values['length'];
                    $weight = $values['weight'];
                    break;
                }
            }

            \Illuminate\Support\Facades\DB::table('fish_breeds')
                ->where('id', $breed->id)
                ->update([
                    'trophy_length_bench' => $length,
                    'trophy_weight_bench' => $weight,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fish_breeds', function (Blueprint $table) {
            $table->dropColumn(['trophy_length_bench', 'trophy_weight_bench']);
        });
    }
};
