<?php

use Fishinglog\Models\Lure;
use Illuminate\Database\Seeder;

class LuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Lure::factory()->count(50)->create();
    }
}
