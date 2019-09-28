<?php

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
        factory(Fishinglog\FishBreed::class, 12)->create();
    }
}
