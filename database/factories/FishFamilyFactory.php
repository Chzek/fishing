<?php

namespace Database\Factories;

use Fishinglog\FishFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

class FishFamilyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FishFamily::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'fake-'.$this->faker->name,
        ];
    }
}
