<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(FishFamiliesTableSeeder::class);
        $this->call(FishBreedsTableSeeder::class);
        $this->call(AnglersTableSeeder::class);
        $this->call(LakesTableSeeder::class);
        $this->call(LuresTableSeeder::class);
        $this->call(RecordsTableSeeder::class);
    }
}
