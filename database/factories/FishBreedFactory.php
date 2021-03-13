<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\FishBreed;
use Faker\Generator as Faker;

$factory->define(FishBreed::class, function (Faker $faker) {
    return [
        'name' => 'fake-'.$faker->name,
        'fish_families_id' => function(){
            return factory(\Fishinglog\FishFamily::class)->create()->id;
        },
    ];
});
