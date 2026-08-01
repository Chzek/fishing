<?php

use Fishinglog\Models\FishBreed;
use Illuminate\Database\Seeder;

class FishBreedsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FishBreed::factory()->count(50)->create();
    }
}
