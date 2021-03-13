<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Record;
use Faker\Generator as Faker;

$factory->define(Record::class, function (Faker $faker) {
    return [
        'anglers_id' => function(){
            return factory(\Fishinglog\Angler::class)->create()->id;
        },
        'lakes_id' => function(){
            return factory(\Fishinglog\Lake::class)->create()->id;
        },
        'fish_breeds_id' => function(){
            return factory(\Fishinglog\FishBreed::class)->create()->id;
        },
        'lures_id' => function(){
            return factory(\Fishinglog\Lure::class)->create()->id;
        },
        'weight' => $faker->randomFloat(2, 1, 100),
        'length' => $faker->randomFloat(2, 1, 100),
        'temperature' => $faker->randomFloat(0, 1, 150),
        'released' => $faker->boolean,
        'caught' => $faker->date('Y-m-d', 'now'),
    ];
});
