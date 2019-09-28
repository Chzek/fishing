<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Model;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'name' => 'fake-Lake '. $faker->lastName,
        'latitude' => $faker->latitude,
        'longiftude' => $faker->longitude,
    ];
});
