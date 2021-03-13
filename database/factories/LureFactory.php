<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Lure;
use Faker\Generator as Faker;

$factory->define(Lure::class, function (Faker $faker) {
    return [
        'name' => 'fake-'. $faker->company,
        'color' => $faker->colorName,
        'size' => $faker->numerify('##.## oz.'),
    ];
});
