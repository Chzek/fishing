<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Model;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    $fish_families = Fishinglog\FishFamily::pluck('id')->toArray();

    return [
        'name' => 'fake-'.$faker->name,
        'fish_families_id' => $faker->randomElement($fish_families),
    ];
});
