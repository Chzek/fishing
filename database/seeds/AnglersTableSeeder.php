<?php

use Illuminate\Database\Seeder;

class AnglersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Fishinglog\Angler::class, 50)->create();
    }
}
