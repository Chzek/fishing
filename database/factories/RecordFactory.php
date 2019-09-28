<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Record;
use Faker\Generator as Faker;

$factory->define(Record::class, function (Faker $faker) {
    $anglers = Fishinglog\Angler::pluck('id')->toArray();
    $lakes = Fishinglog\Lake::pluck('id')->toArray();
    $breeds = Fishinglog\FishBreed::pluck('id')->toArray();
    $lures = Fishinglog\Lure::pluck('id')->toArray();

    return [
        'anglers_id' => $faker->randomElement($anglers),
        'lakes_id' => $faker->randomElement($lakes),
        'fish_breeds_id' => $faker->randomElement($breeds),
        'lures_id' => $faker->randomElement($lures),
        'weight' => $faker->randomFloat(2, 1, 100),
        'length' => $faker->randomFloat(2, 1, 100),
        'temperature' => $faker->randomFloat(0, 1, 150),
        'released' => $faker->boolean,
        'caught' => $faker->date('Y-m-d', 'now'),
    ];
});
