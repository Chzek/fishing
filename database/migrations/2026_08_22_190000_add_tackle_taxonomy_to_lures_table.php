<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lures', function (Blueprint $table) {
            $table->string('size')->nullable()->change();
            $table->string('category')->nullable()->after('name');
            $table->string('brand')->nullable()->after('category');
            $table->string('weight')->nullable()->after('size');
            $table->string('depth_range')->nullable()->after('weight');
        });


        // Auto-classify existing entries
        DB::table('lures')->get()->each(function ($lure) {
            $name = strtolower((string) $lure->name);

            $category = 'Other';
            $brand = null;

            if (str_contains($name, 'daredevil') || str_contains($name, 'spoon')) {
                $category = 'Spoon';
                $brand = 'Eppinger';
            } elseif (str_contains($name, 'ned') || str_contains($name, 'senko') || str_contains($name, 'worm')) {
                $category = 'Soft Plastic';
                $brand = str_contains($name, 'ned') ? 'Z-Man' : 'Yamamoto';
            } elseif (str_contains($name, 'shad rap') || str_contains($name, 'crank') || str_contains($name, 'rapala')) {
                $category = 'Crankbait';
                $brand = 'Rapala';
            } elseif (str_contains($name, 'spinner') || str_contains($name, 'mepps')) {
                $category = 'Inline Spinner';
                $brand = str_contains($name, 'mepps') ? 'Mepps' : null;
            }

            DB::table('lures')->where('id', $lure->id)->update([
                'category' => $category,
                'brand' => $brand,
                'weight' => $lure->size,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lures', function (Blueprint $table) {
            $table->dropColumn(['category', 'brand', 'weight', 'depth_range']);
        });
    }
};
