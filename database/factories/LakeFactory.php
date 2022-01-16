<?php

namespace Database\Factories;

use Fishinglog\Lake;
use Illuminate\Database\Eloquent\Factories\Factory;

class LakeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lake::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'fake-Lake '. $this->faker->lastName,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}
