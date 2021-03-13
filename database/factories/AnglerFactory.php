<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Angler;
use Faker\Generator as Faker;

$factory->define(Angler::class, function (Faker $faker) {
    return [
        'firstName' => $faker->firstName,
        'middleName' => 'fake-'.$faker->firstName,
        'lastName' => $faker->lastName,
        'user_id' => function(){
            return factory(\Fishinglog\User::class)->create()->id;
        },
    ];
});
