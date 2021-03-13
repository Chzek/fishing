<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\FishFamily;
use Faker\Generator as Faker;

$factory->define(FishFamily::class, function (Faker $faker) {
    return [
        'name' => 'fake-'.$faker->name,
    ];
});
