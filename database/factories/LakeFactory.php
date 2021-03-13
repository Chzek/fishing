<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Lake;
use Faker\Generator as Faker;

$factory->define(Lake::class, function (Faker $faker) {
    return [
        'name' => 'fake-Lake '. $faker->lastName,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
