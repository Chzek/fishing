<?php

use Fishinglog\Models\Lake;
use Illuminate\Database\Seeder;

class LakesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Lake::factory()->count(50)->create();
    }
}
