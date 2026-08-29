<?php

namespace Database\Factories;

use Fishinglog\Models\Lake;
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
            'name' => 'fake-Lake ' . $this->faker->lastName . ' ' . $this->faker->unique()->numberBetween(100, 999999),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}
