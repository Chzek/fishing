<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Fishinglog\Angler;
use Faker\Generator as Faker;

$factory->define(Angler::class, function (Faker $faker) {
    $users = Fishinglog\User::pluck('id')->toArray();

    return [
        'firstName' => $faker->firstName,
        'middleName' => 'fake-'.$faker->firstName,
        'lastName' => $faker->lastName,
        'user_id' => $faker->randomElement($users),
    ];
});
