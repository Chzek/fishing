<?php

use Fishinglog\Models\FishFamily;
use Illuminate\Database\Seeder;

class FishFamiliesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FishFamily::factory()->count(50)->create();
    }
}
