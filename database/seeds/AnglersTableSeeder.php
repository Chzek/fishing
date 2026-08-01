<?php

use Fishinglog\Models\Angler;
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
        Angler::factory()->count(50)->create();
    }
}
