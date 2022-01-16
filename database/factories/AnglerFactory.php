<?php

namespace Database\Factories;

use Fishinglog\Angler;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnglerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Angler::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = \Fishinglog\User::factory()->create();
        $avatarName = 'avatar_'.$user->id.'_'.time().'.jpg';

        return [
            'firstName' => $this->faker->firstName,
            'middleName' => 'fake-'.$this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'user_id' => $user->id,
            'avatar' => $avatarName,
        ];
    }
}
