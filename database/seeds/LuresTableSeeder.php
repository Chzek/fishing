<?php

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
        factory(Fishinglog\Lure::class, 20)->create();
    }
}
