<?php

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
        factory(Fishinglog\Lake::class, 10)->create();
    }
}
