<?php

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
        factory(Fishinglog\FishFamily::class, 5)->create();
    }
}
